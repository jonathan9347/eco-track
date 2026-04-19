<?php

namespace App\Livewire;

use App\Models\User;
use App\Services\EmissionFactorService;
use App\Services\UserProfileService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Contract\Firestore;
use Livewire\Component;

class AdminPanel extends Component
{
    protected UserProfileService $userProfileService;

    public string $userSearch = '';
    public string $userClassroomFilter = '';
    public ?string $statusMessage = null;
    public ?string $editingClassroomId = null;

    public array $factorForm = [];
    public array $classroomForm = [];
    public array $announcementForm = [];
    public array $challengeForm = [];

    public array $classrooms = [];
    public array $announcements = [];
    public array $challenges = [];
    public array $apiFactorStatus = [];

    public function mount(): void
    {
        abort_unless(auth()->check() && auth()->user()->is_admin, 403);

        $this->userProfileService = app(UserProfileService::class);

        $this->factorForm = [
            'transport' => ['jeepney' => 0.15, 'bus' => 0.12, 'tricycle' => 0.10, 'car' => 0.20, 'walking' => 0.00],
            'diet' => ['meat' => 5.0, 'average' => 3.5, 'vegetarian' => 2.0, 'vegan' => 1.5],
            'gadgets' => ['per_hour' => 0.05],
        ];

        $this->resetClassroomForm();
        $this->resetAnnouncementForm();
        $this->resetChallengeForm();
        $this->loadAdminData();
    }

    public function render(): View
    {
        $users = User::query()
            ->when($this->userSearch !== '', fn ($q) => $q->where(fn ($nested) => $nested
                ->where('name', 'like', '%'.$this->userSearch.'%')
                ->orWhere('email', 'like', '%'.$this->userSearch.'%')))
            ->when($this->userClassroomFilter !== '', fn ($q) => $q->where('classroom', $this->userClassroomFilter))
            ->orderBy('name')
            ->get();

        return view('components.admin-panel', ['users' => $users])
            ->layout('layouts.app', ['title' => 'Admin Panel']);
    }

    public function saveFactors(): void
    {
        $validated = Validator::make($this->factorForm, [
            'transport.jeepney' => ['required', 'numeric', 'min:0'],
            'transport.bus' => ['required', 'numeric', 'min:0'],
            'transport.tricycle' => ['required', 'numeric', 'min:0'],
            'transport.car' => ['required', 'numeric', 'min:0'],
            'transport.walking' => ['required', 'numeric', 'min:0'],
            'diet.meat' => ['required', 'numeric', 'min:0'],
            'diet.average' => ['required', 'numeric', 'min:0'],
            'diet.vegetarian' => ['required', 'numeric', 'min:0'],
            'diet.vegan' => ['required', 'numeric', 'min:0'],
            'gadgets.per_hour' => ['required', 'numeric', 'min:0'],
        ])->validate();

        $this->database()->collection('settings')->document('emission_factors')->set([
            ...$validated,
            'updated_at' => now()->toISOString(),
            'updated_by' => auth()->id(),
        ]);

        Cache::forget('emissions:fallback:firebase');
        $this->loadApiFactorStatus();

        $this->statusMessage = 'Emission fallback factors saved.';
    }

    public function saveClassroom(): void
    {
        $validated = Validator::make($this->classroomForm, [
            'name' => ['required', 'string', 'max:255'],
            'section' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
        ])->validate();

        if ($this->editingClassroomId) {
            $this->database()->collection('classrooms')->document($this->editingClassroomId)->set([
                ...$validated,
                'updated_at' => now()->toISOString(),
            ], ['merge' => true]);
            $this->statusMessage = 'Classroom updated.';
        } else {
            $this->database()->collection('classrooms')->add([
                ...$validated,
                'created_at' => now()->toISOString(),
            ]);
            $this->statusMessage = 'Classroom created.';
        }

        $this->resetClassroomForm();
        $this->loadClassrooms();
    }

    public function editClassroom(string $id): void
    {
        $classroom = collect($this->classrooms)->firstWhere('id', $id);
        if (! $classroom) {
            return;
        }

        $this->editingClassroomId = $id;
        $this->classroomForm = [
            'name' => $classroom['name'] ?? '',
            'section' => $classroom['section'] ?? '',
            'description' => $classroom['description'] ?? '',
        ];
    }

    public function deleteClassroom(string $id): void
    {
        $this->database()->collection('classrooms')->document($id)->delete();
        $this->statusMessage = 'Classroom deleted.';
        $this->resetClassroomForm();
        $this->loadClassrooms();
    }

    public function saveAnnouncement(): void
    {
        $validated = Validator::make($this->announcementForm, [
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:1500'],
            'target_audience' => ['required', 'string', 'max:255'],
            'scheduled_for' => ['nullable', 'date'],
        ])->validate();

        $this->database()->collection('announcements')->add([
            ...$validated,
            'created_at' => now()->toISOString(),
            'created_by' => auth()->id(),
        ]);

        $this->statusMessage = 'Announcement created.';
        $this->resetAnnouncementForm();
        $this->loadAnnouncements();
    }

    public function saveChallenge(): void
    {
        $validated = Validator::make($this->challengeForm, [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'metric' => ['required', 'string', 'max:255'],
            'target' => ['required', 'numeric', 'min:1'],
            'points' => ['required', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['boolean'],
        ])->validate();

        if ($validated['is_active'] ?? false) {
            $this->deactivateExistingChallenges();
        }

        $this->database()->collection('weekly_challenges')->add([
            ...$validated,
            'created_at' => now()->toISOString(),
            'created_by' => auth()->id(),
        ]);

        $this->statusMessage = 'Challenge created.';
        $this->resetChallengeForm();
        $this->loadChallenges();
    }

    public function toggleAdmin(int $userId): void
    {
        $user = User::findOrFail($userId);
        if (auth()->id() === $userId) {
            $this->statusMessage = 'You cannot change your own admin status here.';
            return;
        }

        $user->is_admin = ! $user->is_admin;
        $user->save();
        $this->syncUserToFirebase($user);
        $this->statusMessage = 'Admin status updated.';
    }

    public function saveUserClassroom(int $userId, string $classroom): void
    {
        $user = User::findOrFail($userId);
        $user->classroom = $classroom;
        $user->save();
        $this->syncUserToFirebase($user);
        $this->statusMessage = 'User classroom updated.';
    }

    public function resetLogs(int $userId): void
    {
        $documents = $this->database()->collection('carbon_logs')->where('user_id', '=', $userId)->documents();

        foreach ($documents as $document) {
            if ($document->exists()) {
                $this->database()->collection('carbon_logs')->document($document->id())->delete();
            }
        }

        $this->statusMessage = 'User logs reset.';
        $this->loadClassrooms();
    }

    protected function loadAdminData(): void
    {
        $this->loadFactors();
        $this->loadApiFactorStatus();
        $this->loadClassrooms();
        $this->loadAnnouncements();
        $this->loadChallenges();
    }

    protected function loadFactors(): void
    {
        $document = $this->database()->collection('settings')->document('emission_factors')->snapshot();
        if ($document->exists()) {
            $this->factorForm = array_replace_recursive($this->factorForm, $document->data());
        }
    }

    protected function loadApiFactorStatus(): void
    {
        $service = app(EmissionFactorService::class);

        foreach (array_keys($this->factorForm['transport']) as $transportType) {
            $service->getTransportFactor($transportType);
        }

        foreach (array_keys($this->factorForm['diet']) as $dietType) {
            $service->getDietFactor($dietType);
        }

        $service->getElectricityFactor();

        $this->apiFactorStatus = $service->getApiStatusSummary();
    }

    protected function loadClassrooms(): void
    {
        $stats = $this->classroomStats();
        $items = [];
        $documents = $this->database()->collection('classrooms')->documents();

        foreach ($documents as $document) {
            if (! $document->exists()) {
                continue;
            }

            $data = $document->data();
            $name = $data['name'] ?? 'Unnamed classroom';
            $items[] = [
                'id' => $document->id(),
                ...$data,
                'user_count' => $stats[$name]['user_count'] ?? 0,
                'log_count' => $stats[$name]['log_count'] ?? 0,
                'average_emission' => $stats[$name]['average_emission'] ?? 0,
            ];
        }

        $this->classrooms = collect($items)->sortBy('name')->values()->all();
    }

    protected function loadAnnouncements(): void
    {
        $items = [];
        $documents = $this->database()->collection('announcements')->documents();

        foreach ($documents as $document) {
            if ($document->exists()) {
                $items[] = ['id' => $document->id(), ...$document->data()];
            }
        }

        $this->announcements = collect($items)->sortByDesc('created_at')->values()->all();
    }

    protected function loadChallenges(): void
    {
        $items = [];
        $documents = $this->database()->collection('weekly_challenges')->documents();

        foreach ($documents as $document) {
            if ($document->exists()) {
                $items[] = ['id' => $document->id(), ...$document->data()];
            }
        }

        $this->challenges = collect($items)->sortByDesc('created_at')->values()->all();
    }

    protected function classroomStats(): array
    {
        $stats = User::query()
            ->select('classroom')
            ->selectRaw('count(*) as user_count')
            ->whereNotNull('classroom')
            ->groupBy('classroom')
            ->get()
            ->keyBy('classroom')
            ->map(fn ($row) => ['user_count' => (int) $row->user_count, 'log_count' => 0, 'total_emission' => 0.0, 'average_emission' => 0.0])
            ->all();

        $documents = $this->database()->collection('carbon_logs')->documents();
        foreach ($documents as $document) {
            if (! $document->exists()) {
                continue;
            }
            $data = $document->data();
            $classroom = $data['classroom'] ?? 'Unassigned';
            $stats[$classroom] ??= ['user_count' => 0, 'log_count' => 0, 'total_emission' => 0.0, 'average_emission' => 0.0];
            $stats[$classroom]['log_count']++;
            $stats[$classroom]['total_emission'] += (float) ($data['total_emission'] ?? 0);
        }

        foreach ($stats as &$stat) {
            $stat['average_emission'] = $stat['log_count'] > 0 ? round($stat['total_emission'] / $stat['log_count'], 2) : 0;
        }
        unset($stat);

        return $stats;
    }

    protected function syncUserToFirebase(User $user): void
    {
        $this->userProfileService->syncUser($user);
    }

    protected function deactivateExistingChallenges(): void
    {
        $documents = $this->database()->collection('weekly_challenges')->where('is_active', '=', true)->documents();
        foreach ($documents as $document) {
            if ($document->exists()) {
                $this->database()->collection('weekly_challenges')->document($document->id())->set([
                    'is_active' => false,
                    'updated_at' => now()->toISOString(),
                ], ['merge' => true]);
            }
        }
    }

    protected function resetClassroomForm(): void
    {
        $this->editingClassroomId = null;
        $this->classroomForm = ['name' => '', 'section' => '', 'description' => ''];
    }

    protected function resetAnnouncementForm(): void
    {
        $this->announcementForm = ['title' => '', 'message' => '', 'target_audience' => 'all', 'scheduled_for' => ''];
    }

    protected function resetChallengeForm(): void
    {
        $this->challengeForm = [
            'title' => '',
            'description' => '',
            'metric' => 'walking_days',
            'target' => 5,
            'points' => 100,
            'starts_at' => '',
            'ends_at' => '',
            'is_active' => true,
        ];
    }

    protected function database()
    {
        return app(Firestore::class)->database();
    }
}
