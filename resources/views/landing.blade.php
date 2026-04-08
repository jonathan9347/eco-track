<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eco-Track - Track Your Carbon Footprint</title>
    <meta
        name="description"
        content="Eco-Track helps students in the Philippines measure daily habits, compete with classmates, and build greener routines."
    >

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        eco: {
                            50: '#effef6',
                            100: '#d9fbe8',
                            200: '#b5f4d0',
                            300: '#84e9af',
                            400: '#4fd386',
                            500: '#28b463',
                            600: '#1f8f4f',
                            700: '#1b7141',
                            800: '#195a37',
                            900: '#17492f',
                            950: '#0b2418'
                        }
                    }
                }
            }
        };
    </script>
    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            background:
                radial-gradient(circle at top left, rgba(132, 233, 175, 0.22), transparent 34%),
                radial-gradient(circle at bottom right, rgba(250, 204, 21, 0.14), transparent 30%),
                linear-gradient(135deg, #0f3b2a 0%, #081b13 100%);
            color: #f4fff8;
        }

        .hero-type-cursor::after {
            content: '';
            display: inline-block;
            width: 0.08em;
            height: 0.95em;
            margin-left: 0.12em;
            border-radius: 999px;
            background: rgba(167, 243, 208, 0.95);
            vertical-align: -0.08em;
            animation: hero-cursor-blink 1s steps(1) infinite;
        }

        @keyframes hero-cursor-blink {
            0%, 49% {
                opacity: 1;
            }

            50%, 100% {
                opacity: 0;
            }
        }

        .hero-type-shell {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .hero-type-shell::before {
            content: attr(data-hero-text);
            display: block;
            visibility: hidden;
            white-space: normal;
        }

        .hero-type-live {
            position: absolute;
            inset: 0;
        }

        .feature-copy-panel {
            transition:
                opacity 240ms ease,
                transform 240ms ease;
        }

        .feature-copy-panel.is-switching {
            opacity: 0;
            transform: translateY(10px);
        }
    </style>
</head>
<body class="min-h-screen text-white antialiased">
    @php
        $registerUrl = Route::has('register') ? route('register') : url('/register');
        $loginUrl = Route::has('login') ? route('login') : url('/login');
        $stats = [
            ['value' => '12,480 kg', 'label' => 'Total CO2 saved', 'image' => 'assets/total-saved.jpg', 'description' => 'Carbon emissions reduced through daily eco-friendly choices', 'back_detail' => 'This total shows the collective impact of classroom actions, turning individual habits into measurable climate progress. Every kilogram recorded reflects a small step toward greener daily routines.'],
            ['value' => '1,240', 'label' => 'Active students', 'image' => 'assets/active-students.jpg', 'description' => 'Students actively tracking and improving their habits', 'back_detail' => 'These learners are engaging every day, logging routines and sharing progress to build momentum together. Their consistent participation helps classrooms compare performance and stay accountable.'],
            ['value' => '58', 'label' => 'Classrooms participating', 'image' => 'assets/classroom.jpg', 'description' => 'Educational communities building sustainable futures', 'back_detail' => 'Every participating classroom contributes to a larger school-wide effort toward greener living. The growing number helps schools track adoption and celebrate community-level climate action.'],
        ];
        $features = [
            [
                'title' => 'Carbon Calculator',
                'description' => 'Turn daily habits into CO2 data with fast inputs for transport, food, and electricity use.',
                'card_summary' => 'Quickly log daily transport, meals, and energy use to see your carbon footprint improve.',
                'detail_title' => 'Carbon Calculator, made for everyday logging.',
                'detail_body' => 'The Carbon Calculator helps students quickly record transport, food, and energy habits without dealing with confusing numbers. Eco-Track translates those choices into clear carbon estimates, so users can immediately understand what parts of their day create the biggest footprint.',
                'detail_note' => 'Best for turning daily routines into practical climate awareness.',
                'icon' => 'M12 6v6l4 2m-4-2-4 2m4-2v12m6 2v10a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V8m10 0 4 2m-4-2-4 2',
                'image' => 'assets/carbon-calculator.jpg',
            ],
            [
                'title' => 'Classroom Leaderboard',
                'description' => 'Compete with classmates through friendly rankings that make greener decisions feel social and motivating.',
                'card_summary' => 'See your class rankings at a glance and stay motivated with friendly eco challenges.',
                'detail_title' => 'A leaderboard that makes sustainability social.',
                'detail_body' => 'The Classroom Leaderboard transforms personal eco habits into a shared class experience. Instead of tracking progress alone, students can compare results, celebrate improvement, and stay motivated by seeing how their choices contribute to a bigger community effort.',
                'detail_note' => 'Best for increasing participation through healthy competition.',
                'icon' => 'M7 20h10M8 16h1M12 12h1m3 4h1M6 8h12l-1 10H7L6 8Zm3-4h6l1 4H8l1-4Z',
                'image' => 'assets/classroom-leaderboard.jpg',
            ],
            [
                'title' => 'Eco Badges',
                'description' => 'Earn achievements for green habits so progress feels visible, rewarding, and worth repeating every day.',
                'card_summary' => 'Collect badges for consistent eco-friendly choices and celebrate every habit change.',
                'detail_title' => 'Eco Badges that reward real progress.',
                'detail_body' => 'Eco Badges give students a sense of momentum by recognizing consistent green behavior over time. Small actions feel more meaningful when they unlock visible milestones, helping users stay engaged and build sustainable habits that last beyond a single session.',
                'detail_note' => 'Best for making progress feel visible, rewarding, and repeatable.',
                'icon' => 'M12 3l2.5 5.2 5.7.8-4.1 4 1 5.7L12 16.8 6.9 19.7l1-5.7-4.1-4 5.7-.8L12 3Z',
                'image' => 'assets/eco-badge.jpg',
            ],
        ];
        $steps = [
            ['title' => 'Create Account', 'description' => 'Register with your class and start tracking progress together in a safe, school-friendly space designed for collaborative learning.'],
            ['title' => 'Log Daily Activities', 'description' => 'Quickly record transport, meals, and energy use so every habit becomes a measurable eco action that contributes to your class goals.'],
            ['title' => 'Compare Your Impact', 'description' => 'Review your carbon score alongside classmates to see where you can improve and celebrate wins that make a real difference.'],
            ['title' => 'Earn Achievements', 'description' => 'Collect badges and milestones for consistent green habits that feel rewarding every day and motivate continued participation.'],
        ];
    @endphp

    <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(110,231,183,0.22),_transparent_35%),radial-gradient(circle_at_bottom_right,_rgba(251,191,36,0.16),_transparent_28%),linear-gradient(135deg,_rgba(6,78,59,0.92),_rgba(2,44,34,1))]"></div>
        <div class="absolute inset-x-0 top-0 h-32 bg-[linear-gradient(to_bottom,_rgba(167,243,208,0.18),_transparent)]"></div>

        <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
            <header class="flex items-center justify-between py-6">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <img src="{{ asset('assets/logo.png') }}" alt="Eco-Track Logo" class="h-11 w-11 rounded-2xl ring-1 ring-white/15">
                    <div>
                        <p class="text-lg font-semibold text-white">Eco-Track</p>
                        <p class="text-xs uppercase tracking-[0.35em] text-emerald-200/80">Philippines</p>
                    </div>
                </a>

                <nav class="hidden items-center gap-8 text-sm text-emerald-50/80 md:flex">
                    <a href="#features" class="transition hover:text-white">Features</a>
                    <a href="#how-it-works" class="transition hover:text-white">How It Works</a>
                    <a href="#stats" class="transition hover:text-white">Impact</a>
                    <a href="{{ $loginUrl }}" class="rounded-full border border-white/15 px-4 py-2 font-medium text-white transition hover:border-white/30 hover:bg-white/10">Log In</a>
                </nav>
            </header>

            <main>
                <section class="grid items-center gap-16 py-16 lg:grid-cols-[1.1fr_0.9fr] lg:py-24">
                    <div class="max-w-2xl">
                        <span class="inline-flex items-center rounded-full border border-emerald-200/20 bg-emerald-300/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.32em] text-emerald-100">
                            Climate action for every student
                        </span>
                        <h1
                            class="mt-8 text-5xl font-black leading-tight text-white sm:text-6xl"
                        >
                            <span
                                class="hero-type-shell"
                                data-hero-typing
                                data-hero-text="Eco-Track - Track Your Carbon Footprint"
                            >
                                <span class="hero-type-live">
                                    <span data-hero-typing-text></span>
                                    <span class="hero-type-cursor"></span>
                                </span>
                            </span>
                        </h1>
                        <p class="mt-6 max-w-xl text-lg leading-8 text-emerald-50/82 sm:text-xl">
                            Help save the environment, one day at a time
                        </p>
                        <p class="mt-6 max-w-2xl text-base leading-8 text-emerald-100/72">
                            Built for students in the Philippines, Eco-Track transforms everyday choices like commuting, meals, and electricity use into simple climate insights that make sustainability feel motivating, social, and real.
                        </p>

                        <div class="mt-10 flex flex-col gap-4 sm:flex-row">
                            <a
                                href="{{ $registerUrl }}"
                                class="inline-flex items-center justify-center rounded-full bg-emerald-300 px-7 py-4 text-sm font-bold text-emerald-950 shadow-[0_20px_60px_-20px_rgba(110,231,183,0.85)] transition hover:-translate-y-0.5 hover:bg-emerald-200"
                            >
                                Get Started
                            </a>
                            <a
                                href="#features"
                                class="inline-flex items-center justify-center rounded-full border border-white/15 bg-white/5 px-7 py-4 text-sm font-semibold text-white backdrop-blur transition hover:border-white/30 hover:bg-white/10"
                            >
                                Learn More
                            </a>
                        </div>
                    </div>

                    <div class="relative [perspective:1800px]">
                        <div class="absolute -left-6 top-10 h-24 w-24 rounded-full bg-lime-300/20 blur-3xl"></div>
                        <div class="absolute -right-4 bottom-8 h-32 w-32 rounded-full bg-amber-300/20 blur-3xl"></div>
                        <div class="absolute left-8 top-16 h-[28rem] w-[88%] rounded-[2.4rem] bg-black/30 blur-3xl"></div>

                        <div class="relative mx-auto w-full max-w-[34rem] [transform:rotateX(16deg)_rotateY(-18deg)_rotateZ(8deg)] transition duration-700 hover:[transform:rotateX(10deg)_rotateY(-10deg)_rotateZ(4deg)_translateY(-10px)]">
                            <div class="absolute inset-x-[8%] -bottom-8 h-12 rounded-full bg-black/45 blur-2xl"></div>
                            <div class="absolute inset-0 translate-x-5 translate-y-5 rounded-[2.6rem] border border-white/6 bg-emerald-950/45"></div>
                            <div class="absolute inset-0 translate-x-2.5 translate-y-2.5 rounded-[2.6rem] border border-white/8 bg-white/6 backdrop-blur-sm"></div>

                            <div class="relative rounded-[2.6rem] border border-white/14 bg-[linear-gradient(155deg,rgba(255,255,255,0.18),rgba(255,255,255,0.05)_22%,rgba(4,47,36,0.96)_58%,rgba(2,24,18,1)_100%)] p-5 shadow-[0_38px_100px_-35px_rgba(0,0,0,0.8)] backdrop-blur-xl">
                                <div class="absolute inset-x-10 top-0 h-px bg-gradient-to-r from-transparent via-white/70 to-transparent"></div>
                                <div class="absolute right-8 top-8 h-28 w-28 rounded-full bg-emerald-300/14 blur-3xl"></div>
                                <div class="absolute left-10 top-10 h-20 w-20 rounded-full bg-amber-200/12 blur-3xl"></div>

                                <div class="relative rounded-[2rem] border border-white/10 bg-[linear-gradient(180deg,rgba(6,95,70,0.22),rgba(2,44,34,0.94))] p-6 shadow-[inset_0_1px_0_rgba(255,255,255,0.16)]">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm uppercase tracking-[0.24em] text-emerald-100/72">Today's footprint</p>
                                            <p class="mt-3 text-4xl font-black tracking-tight text-white sm:text-5xl">3.2 kg CO2</p>
                                        </div>
                                        <div class="rounded-[1.4rem] border border-emerald-200/10 bg-emerald-300/14 px-4 py-3 text-right shadow-[inset_0_1px_0_rgba(255,255,255,0.18)]">
                                            <p class="text-[0.65rem] uppercase tracking-[0.34em] text-emerald-200">Status</p>
                                            <p class="mt-1 text-sm font-semibold text-emerald-100">Improving</p>
                                        </div>
                                    </div>

                                    <div class="mt-8 space-y-4">
                                        <div class="rounded-[1.4rem] border border-white/8 bg-white/6 p-4 shadow-[inset_0_1px_0_rgba(255,255,255,0.08)]">
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="text-emerald-100/70">Transport</span>
                                                <span class="font-semibold text-white">1.1 kg</span>
                                            </div>
                                            <div class="mt-3 h-2 rounded-full bg-white/10">
                                                <div class="h-2 w-2/3 rounded-full bg-emerald-300 shadow-[0_0_20px_rgba(110,231,183,0.55)]"></div>
                                            </div>
                                        </div>
                                        <div class="rounded-[1.4rem] border border-white/8 bg-white/6 p-4 shadow-[inset_0_1px_0_rgba(255,255,255,0.08)]">
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="text-emerald-100/70">Food</span>
                                                <span class="font-semibold text-white">0.9 kg</span>
                                            </div>
                                            <div class="mt-3 h-2 rounded-full bg-white/10">
                                                <div class="h-2 w-1/2 rounded-full bg-lime-300 shadow-[0_0_20px_rgba(190,242,100,0.45)]"></div>
                                            </div>
                                        </div>
                                        <div class="rounded-[1.4rem] border border-white/8 bg-white/6 p-4 shadow-[inset_0_1px_0_rgba(255,255,255,0.08)]">
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="text-emerald-100/70">Energy</span>
                                                <span class="font-semibold text-white">1.2 kg</span>
                                            </div>
                                            <div class="mt-3 h-2 rounded-full bg-white/10">
                                                <div class="h-2 w-3/4 rounded-full bg-amber-300 shadow-[0_0_20px_rgba(252,211,77,0.45)]"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-8 grid gap-4 sm:grid-cols-3">
                                        @foreach ($stats as $stat)
                                            <div class="rounded-[1.35rem] border border-white/8 bg-black/10 px-4 py-5 shadow-[inset_0_1px_0_rgba(255,255,255,0.08)]">
                                                <p class="text-2xl font-black text-white">{{ $stat['value'] }}</p>
                                                <p class="mt-2 text-sm text-emerald-100/65">{{ $stat['label'] }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="features" class="relative py-10 sm:py-14">
                    <div class="absolute inset-y-0 left-1/2 w-screen -translate-x-1/2 bg-white"></div>

                    <div class="relative px-8 py-10 text-zinc-900 sm:px-10 sm:py-14">
                        <div class="grid gap-10 lg:grid-cols-2 lg:items-stretch lg:gap-12">
                            <div class="flex min-h-[36rem] flex-col justify-center p-2 sm:p-4 lg:pr-10 xl:pr-16">
                                <div
                                    class="feature-copy-panel"
                                    data-feature-copy
                                >
                                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-700/80">Features</p>
                                    <h2 class="mt-4 text-3xl font-black text-zinc-900 sm:text-4xl" data-feature-copy-title>
                                        {{ $features[0]['detail_title'] }}
                                    </h2>
                                    <p class="mt-6 text-base leading-8 text-zinc-600 sm:text-lg" data-feature-copy-body>
                                        {{ $features[0]['detail_body'] }}
                                    </p>
                                    <p class="mt-6 text-base font-semibold leading-8 text-emerald-800/85 sm:text-lg" data-feature-copy-note>
                                        {{ $features[0]['detail_note'] }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex min-h-[36rem] flex-col justify-between px-4 py-3 sm:px-8 sm:py-6">
                                <div
                                    class="relative flex-1 overflow-visible"
                                    data-feature-carousel
                                >
                                    <div class="pointer-events-none absolute inset-x-8 top-8 bottom-20 rounded-[2.5rem] bg-gradient-to-b from-emerald-50 to-transparent"></div>
                                    <div
                                        class="relative min-h-[32rem] overflow-visible sm:min-h-[34rem]"
                                        data-feature-track
                                    >
                                        @foreach ($features as $index => $feature)
                                            <article
                                                class="absolute left-1/2 top-1/2 flex h-[35rem] w-[88%] -translate-x-1/2 -translate-y-1/2 flex-col overflow-hidden rounded-[0.35rem] border-2 border-emerald-300 bg-[linear-gradient(180deg,#f7fff9_0%,#e3f7eb_100%)] p-5 transition duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] sm:h-[36rem] sm:w-[82%]"
                                                data-feature-slide
                                                data-slide-index="{{ $index }}"
                                                data-feature-title="{{ $feature['detail_title'] }}"
                                                data-feature-body="{{ $feature['detail_body'] }}"
                                                data-feature-note="{{ $feature['detail_note'] }}"
                                            >
                                                <div class="@class([
                                                    'relative h-[18.5rem] overflow-hidden rounded-[1.5rem] border border-emerald-200/70 p-5 sm:h-[19rem]',
                                                    'bg-gradient-to-br from-emerald-200 via-emerald-50 to-lime-100' => $index === 0,
                                                    'bg-gradient-to-br from-emerald-300 via-emerald-100 to-teal-100' => $index === 1,
                                                    'bg-gradient-to-br from-lime-200 via-emerald-50 to-emerald-100' => $index === 2,
                                                ])">
                                                    <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-white/70 blur-2xl"></div>
                                                    <div class="relative">
                                                        <div class="flex items-center justify-between">
                                                            <span class="rounded-full bg-white/80 px-3 py-1 text-[0.65rem] font-bold uppercase tracking-[0.28em] text-emerald-800">
                                                                Preview
                                                            </span>
                                                            <div class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-white/85 shadow-sm">
                                                                <svg class="h-5 w-5 text-emerald-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                                    <path d="{{ $feature['icon'] }}" />
                                                                </svg>
                                                            </div>
                                                        </div>

                                                        <div class="mt-6 rounded-[1.35rem] border border-white/80 bg-white/85 p-4 shadow-[inset_0_1px_0_rgba(255,255,255,0.85)]">
                                                            <div class="flex items-center justify-between">
                                                                <div>
                                                                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-700/60">Feature</p>
                                                                    <p class="mt-2 text-lg font-black text-emerald-950">{{ $feature['title'] }}</p>
                                                                    <p class="mt-2 text-sm leading-6 text-emerald-700/80">{{ $feature['card_summary'] }}</p>
                                                                </div>
                                                                <div class="rounded-2xl bg-emerald-900 px-3 py-2 text-xs font-bold uppercase tracking-[0.22em] text-white">
                                                                    0{{ $index + 1 }}
                                                                </div>
                                                            </div>

                                                            <div class="mt-5">
                                                                <div class="overflow-hidden rounded-[1.35rem] border border-white/20 bg-white/80">
                                                                    <img
                                                                        src="{{ asset($feature['image']) }}"
                                                                        alt="{{ $feature['title'] }} preview"
                                                                        class="h-56 w-full object-cover sm:h-64"
                                                                    />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mt-6 min-h-[6.5rem]"></div>
                                            </article>
                                        @endforeach
                                    </div>

                                    <div class="mt-6 flex items-center justify-center gap-3" data-feature-pagination>
                                        @foreach ($features as $index => $feature)
                                            <button
                                                type="button"
                                                class="h-3 w-3 rounded-full bg-zinc-300 transition-all duration-300 hover:bg-zinc-500"
                                                data-feature-dot
                                                data-slide-index="{{ $index }}"
                                                aria-label="Go to {{ $feature['title'] }}"
                                                aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                                            ></button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="how-it-works" class="relative py-16 sm:py-20">
                    <div class="absolute inset-y-0 left-1/2 w-screen -translate-x-1/2 bg-white"></div>

                    <div class="relative px-8 py-10 text-zinc-900 sm:px-10 sm:py-14 xl:px-12">
                        <div class="mx-auto max-w-3xl text-center">
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-700/80">How It Works</p>
                            <h2 class="mt-4 text-3xl font-black text-zinc-900 sm:text-4xl">Everything students need to log, learn, and lower their footprint.</h2>
                            <p class="mt-4 text-base leading-7 text-zinc-600">From signing in to comparing progress, Eco-Track makes it easy to turn everyday choices into better habits.</p>
                        </div>

                        <div class="mt-10 grid gap-6 md:grid-cols-2">
                            @foreach ($steps as $index => $step)
                                <div class="p-7">
                                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-[0.35rem] bg-emerald-600 text-base font-black text-white shadow-sm">
                                        {{ $index + 1 }}
                                    </div>
                                    <h3 class="mt-6 text-2xl font-bold text-zinc-900">{{ $step['title'] }}</h3>
                                    <p class="mt-3 text-base leading-7 text-zinc-600">{{ $step['description'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                <section id="stats" class="relative py-10 sm:py-14">
                    <div class="mx-auto max-w-7xl px-6 lg:px-8">
                        <div class="flex flex-col gap-6">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                                <div class="max-w-2xl">
                                    <p class="text-sm font-semibold uppercase tracking-[0.32em] text-emerald-300/80">Community Impact</p>
                                    <h2 class="mt-4 text-3xl font-black text-white sm:text-4xl">Sample stats for now, ready to connect to live API data later.</h2>
                                </div>
                                <a
                                    href="{{ $registerUrl }}"
                                    class="inline-flex items-center justify-center rounded-full border border-emerald-300/20 bg-emerald-600/15 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-600/25"
                                >
                                    Join Your Classroom
                                </a>
                            </div>

                            <div class="mt-10 grid gap-6 md:grid-cols-3">
                                @foreach ($stats as $stat)
                                    <div class="group h-[30rem] [perspective:1000px]">
                                        <div class="relative h-full w-full transition-transform duration-700 [transform-style:preserve-3d] group-hover:[transform:rotateY(180deg)]">
                                            <!-- Front of card -->
                                            <div class="absolute inset-0 h-full w-full [backface-visibility:hidden]">
                                                <div class="rounded-[0.35rem] border border-white/15 bg-white/5 p-8 backdrop-blur-xl h-full flex flex-col">
                                                    <div class="mb-6 h-80 w-full overflow-hidden rounded-[0.35rem]">
                                                        <img
                                                            src="{{ asset($stat['image']) }}"
                                                            alt="{{ $stat['label'] }} illustration"
                                                            class="h-full w-full object-cover"
                                                        />
                                                    </div>
                                                    <p class="text-sm uppercase tracking-[0.28em] text-emerald-200/75">{{ $stat['label'] }}</p>
                                                    <p class="mt-4 text-4xl font-black text-white sm:text-5xl">{{ $stat['value'] }}</p>
                                                </div>
                                            </div>
                                            <!-- Back of card -->
                                            <div class="absolute inset-0 h-full w-full [backface-visibility:hidden] [transform:rotateY(180deg)]">
                                                <div class="rounded-[0.35rem] border border-white/15 bg-emerald-600/20 p-8 backdrop-blur-xl h-full flex flex-col justify-center items-center text-center">
                                                    <div class="mb-6">
                                                        <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500/30 mb-4">
                                                            <svg class="h-8 w-8 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <p class="text-lg font-bold text-white mb-4">{{ $stat['label'] }}</p>
                                                    <p class="text-base leading-7 text-emerald-100">{{ $stat['description'] }}</p>
                                                    <p class="mt-4 text-sm leading-6 text-emerald-100/80">{{ $stat['back_detail'] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="border-t border-white/10 py-8">
                <div class="flex flex-col gap-6 text-sm text-emerald-100/70 md:flex-row md:items-center md:justify-between">
                    <p>(c) {{ now()->year }} Eco-Track. Helping students in the Philippines build greener habits.</p>
                    <div class="flex flex-wrap items-center gap-5">
                        <a href="#features" class="transition hover:text-white">Features</a>
                        <a href="#how-it-works" class="transition hover:text-white">How It Works</a>
                        <a href="#stats" class="transition hover:text-white">Stats</a>
                        <a href="{{ $registerUrl }}" class="transition hover:text-white">Register</a>
                        <a href="{{ $loginUrl }}" class="transition hover:text-white">Log In</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script>
        document.querySelectorAll('[data-hero-typing]').forEach((element) => {
            const text = element.dataset.heroText || '';
            const textTarget = element.querySelector('[data-hero-typing-text]');
            const cursor = element.querySelector('.hero-type-cursor');

            if (! text || ! textTarget || ! cursor) {
                return;
            }

            let index = 0;
            let isDeleting = false;

            const tick = () => {
                textTarget.textContent = text.slice(0, index);

                if (! isDeleting && index < text.length) {
                    index += 1;
                    window.setTimeout(tick, index < 10 ? 125 : 82);
                    return;
                }

                if (! isDeleting && index === text.length) {
                    isDeleting = true;
                    window.setTimeout(tick, 1400);
                    return;
                }

                if (isDeleting && index > 0) {
                    index -= 1;
                    window.setTimeout(tick, 45);
                    return;
                }

                isDeleting = false;
                window.setTimeout(tick, 450);
            };

            textTarget.textContent = '';
            tick();
        });

        document.querySelectorAll('[data-feature-carousel]').forEach((carousel) => {
            const track = carousel.querySelector('[data-feature-track]');
            const slides = Array.from(carousel.querySelectorAll('[data-feature-slide]'));
            const dots = Array.from(carousel.querySelectorAll('[data-feature-dot]'));
            const copyPanel = document.querySelector('[data-feature-copy]');
            const copyTitle = document.querySelector('[data-feature-copy-title]');
            const copyBody = document.querySelector('[data-feature-copy-body]');
            const copyNote = document.querySelector('[data-feature-copy-note]');
            let activeIndex = 0;
            let wheelLocked = false;

            if (! track || ! slides.length || ! dots.length) {
                return;
            }

            const setActiveDot = (nextIndex) => {
                dots.forEach((dot, index) => {
                    const isActive = index === nextIndex;

                    dot.setAttribute('aria-current', isActive ? 'true' : 'false');
                    dot.classList.toggle('bg-emerald-600', isActive);
                    dot.classList.toggle('w-10', isActive);
                    dot.classList.toggle('shadow-[0_10px_25px_-8px_rgba(5,150,105,0.65)]', isActive);
                    dot.classList.toggle('bg-zinc-300', ! isActive);
                });
            };

            const renderSlides = (nextIndex) => {
                slides.forEach((slide, index) => {
                    const total = slides.length;
                    const normalized = (index - nextIndex + total) % total;
                    const isActive = normalized === 0;
                    const isSecond = normalized === 1;
                    const isLast = normalized === total - 1;
                    const isHidden = ! isActive && ! isSecond && ! isLast;

                    slide.style.zIndex = isActive ? '40' : isSecond ? '25' : isLast ? '24' : '10';
                    slide.style.opacity = isHidden ? '0' : '1';
                    slide.style.pointerEvents = isActive ? 'auto' : 'none';
                    slide.style.filter = 'blur(0px)';
                    slide.style.transform = isActive
                        ? 'translate3d(-50%, -50%, 0) scale(1) rotate(0deg)'
                        : isSecond
                            ? 'translate3d(-24%, -47%, 0) scale(0.92) rotate(0deg)'
                            : isLast
                                ? 'translate3d(-76%, -47%, 0) scale(0.92) rotate(0deg)'
                                : 'translate3d(-50%, -44%, 0) scale(0.88) rotate(0deg)';
                    slide.style.boxShadow = 'none';
                    slide.style.borderColor = isActive ? 'rgba(110, 231, 183, 0.95)' : 'rgba(16, 185, 129, 0.45)';
                    slide.style.background = isActive
                        ? 'linear-gradient(180deg, #f7fff9 0%, #e3f7eb 100%)'
                        : normalized === 1
                            ? 'linear-gradient(180deg, #dcfce7 0%, #c7f9d8 100%)'
                            : normalized === total - 1
                                ? 'linear-gradient(180deg, #d1fae5 0%, #bbf7d0 100%)'
                                : 'linear-gradient(180deg, #ecfdf5 0%, #d1fae5 100%)';
                });
            };

            const goToSlide = (nextIndex) => {
                activeIndex = ((nextIndex % slides.length) + slides.length) % slides.length;
                renderSlides(activeIndex);
                setActiveDot(activeIndex);

                 const activeSlide = slides[activeIndex];

                if (copyPanel && copyTitle && copyBody && copyNote && activeSlide) {
                    copyPanel.classList.add('is-switching');

                    window.setTimeout(() => {
                        copyTitle.textContent = activeSlide.dataset.featureTitle || '';
                        copyBody.textContent = activeSlide.dataset.featureBody || '';
                        copyNote.textContent = activeSlide.dataset.featureNote || '';
                        copyPanel.classList.remove('is-switching');
                    }, 180);
                }
            };

            dots.forEach((dot) => {
                dot.addEventListener('click', () => {
                    goToSlide(Number(dot.dataset.slideIndex || 0));
                });
            });

            let touchStartX = 0;
            let touchDeltaX = 0;

            track.addEventListener('pointerdown', (event) => {
                touchStartX = event.clientX;
                touchDeltaX = 0;
            });

            track.addEventListener('pointermove', (event) => {
                if (touchStartX === 0) {
                    return;
                }

                touchDeltaX = event.clientX - touchStartX;
            });

            const finishGesture = () => {
                if (Math.abs(touchDeltaX) > 50) {
                    goToSlide(activeIndex + (touchDeltaX < 0 ? 1 : -1));
                }

                touchStartX = 0;
                touchDeltaX = 0;
            };

            track.addEventListener('pointerup', finishGesture);
            track.addEventListener('pointerleave', finishGesture);

            track.addEventListener('wheel', (event) => {
                const dominantDelta = Math.abs(event.deltaX) > Math.abs(event.deltaY) ? event.deltaX : event.deltaY;

                if (Math.abs(dominantDelta) < 24 || wheelLocked) {
                    return;
                }

                event.preventDefault();
                wheelLocked = true;
                goToSlide(activeIndex + (dominantDelta > 0 ? 1 : -1));

                window.setTimeout(() => {
                    wheelLocked = false;
                }, 520);
            }, { passive: false });

            track.addEventListener('keydown', (event) => {
                if (event.key === 'ArrowRight') {
                    goToSlide(activeIndex + 1);
                }

                if (event.key === 'ArrowLeft') {
                    goToSlide(activeIndex - 1);
                }
            });

            track.setAttribute('tabindex', '0');
            goToSlide(0);
        });
    </script>
</body>
</html>
