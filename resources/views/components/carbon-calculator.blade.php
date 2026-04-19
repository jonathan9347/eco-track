<section
    x-data="carbonCalculator({
        calculateUrl: '{{ url('/api/calculate-carbon') }}',
        saveUrl: '{{ url('/api/save-log') }}',
        csrfToken: '{{ csrf_token() }}',
        userId: @js($userId),
    })"
    class="w-full"
>
    @once
        <style>
            [x-cloak] {
                display: none !important;
            }

            .cc-card {
                overflow: hidden;
                border: 1px solid rgba(119, 155, 112, 0.2);
                border-radius: 0.35rem;
                background: linear-gradient(180deg, #ffffff 0%, #fbf8f0 100%);
            }

            .cc-card__inner {
                display: flex;
                flex-direction: column;
                gap: 1.4rem;
                padding: 1.35rem;
            }

            .cc-card__header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 1rem;
            }

            .cc-card__title {
                margin: 0;
                color: #1f2f1f;
                font-size: 1.55rem;
                font-weight: 800;
                letter-spacing: -0.04em;
            }

            .cc-card__subtitle {
                margin: 0.35rem 0 0;
                color: #6d7868;
                font-size: 0.92rem;
                line-height: 1.55;
            }

            .cc-card__pill {
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                padding: 0.5rem 0.75rem;
                border-radius: 999px;
                background: #eef4e7;
                color: #476447;
                font-size: 0.78rem;
                font-weight: 700;
                white-space: nowrap;
            }

            .cc-form {
                display: flex;
                flex-direction: column;
                gap: 1.1rem;
            }

            .cc-row {
                display: grid;
                gap: 0.75rem;
                padding: 1rem;
                border: 1px solid rgba(111, 149, 95, 0.12);
                border-radius: 0.35rem;
                background: rgba(250, 251, 246, 0.92);
            }

            .cc-row__top {
                display: grid;
                grid-template-columns: auto minmax(0, 1fr);
                gap: 0.8rem;
                align-items: start;
            }

            .cc-row__icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 2.7rem;
                height: 2.7rem;
                border-radius: 999px;
                background: #e2edd9;
                color: #486847;
            }

            .cc-row__icon svg {
                width: 1.2rem;
                height: 1.2rem;
            }

            .cc-row__label {
                margin: 0;
                color: #223523;
                font-size: 1rem;
                font-weight: 700;
                line-height: 1.2;
            }

            .cc-row__help {
                margin: 0.18rem 0 0;
                color: #6f7a6d;
                font-size: 0.8rem;
                line-height: 1.45;
            }

            .cc-row__controls {
                display: grid;
                gap: 0.7rem;
            }

            .cc-select,
            .cc-input {
                width: 100%;
                min-height: 2.85rem;
                border: 1px solid #d8dfd1;
                border-radius: 0.35rem;
                background: #ffffff;
                color: #223223;
                font-size: 0.92rem;
                padding: 0.7rem 0.85rem;
                outline: none;
                transition: border-color 140ms ease, box-shadow 140ms ease;
            }

            .cc-select:focus,
            .cc-input:focus {
                border-color: #73976a;
                box-shadow: 0 0 0 4px rgba(111, 149, 95, 0.14);
            }

            .cc-range-wrap {
                display: grid;
                gap: 0.5rem;
            }

            .cc-range-meta {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.75rem;
                color: #62705e;
                font-size: 0.78rem;
                font-weight: 700;
            }

            .cc-range {
                width: 100%;
                height: 0.42rem;
                appearance: none;
                border-radius: 999px;
                background: linear-gradient(90deg, #7fab77 0%, #d9e3ce 100%);
                outline: none;
            }

            .cc-range::-webkit-slider-thumb {
                appearance: none;
                width: 1.1rem;
                height: 1.1rem;
                border: 3px solid #6f955f;
                border-radius: 999px;
                background: #fffefb;
                box-shadow: 0 4px 10px rgba(67, 99, 61, 0.16);
                cursor: pointer;
            }

            .cc-range::-moz-range-thumb {
                width: 1.1rem;
                height: 1.1rem;
                border: 3px solid #6f955f;
                border-radius: 999px;
                background: #fffefb;
                box-shadow: 0 4px 10px rgba(67, 99, 61, 0.16);
                cursor: pointer;
            }

            .cc-actions {
                display: grid;
                gap: 0.75rem;
            }

            .cc-button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 3rem;
                border: 0;
                border-radius: 0.35rem;
                font-size: 0.95rem;
                font-weight: 800;
                transition: transform 140ms ease, background-color 140ms ease, opacity 140ms ease;
                cursor: pointer;
            }

            .cc-button:disabled {
                opacity: 0.55;
                cursor: not-allowed;
            }

            .cc-button:active:not(:disabled) {
                transform: translateY(1px);
            }

            .cc-button--primary {
                background: #295c36;
                color: #fffef8;
            }

            .cc-button--primary:hover:not(:disabled) {
                background: #214e2d;
            }

            .cc-button--secondary {
                background: #edf3e9;
                color: #34523a;
            }

            .cc-button--secondary:hover:not(:disabled) {
                background: #e2ecdd;
            }

            .cc-error {
                border: 1px solid #f0c8c8;
                border-radius: 0.35rem;
                background: #fff1f1;
                color: #b63a3a;
                font-size: 0.88rem;
                line-height: 1.45;
                padding: 0.85rem 0.95rem;
            }

            .cc-results {
                display: grid;
                gap: 0.8rem;
                padding: 1rem;
                border: 1px solid rgba(111, 149, 95, 0.14);
                border-radius: 0.35rem;
                background: linear-gradient(180deg, #f6fbf3 0%, #eff6ea 100%);
            }

            .cc-loading {
                display: grid;
                justify-items: center;
                gap: 0.15rem;
                padding: 0.2rem 0.75rem 0.45rem;
                border: 1px solid rgba(111, 149, 95, 0.14);
                border-radius: 0.35rem;
                background: #ffffff;
                text-align: center;
            }

            .cc-loading__title {
                color: #295c36;
                font-size: 0.95rem;
                font-weight: 800;
            }

            .cc-loading__copy {
                max-width: 22rem;
                color: #62705e;
                font-size: 0.8rem;
                line-height: 1.45;
            }

            .cc-loading dotlottie-wc {
                display: block;
                width: 300px;
                height: 220px;
                max-width: 100%;
            }

            .cc-results__eyebrow {
                margin: 0;
                color: #5c6d57;
                font-size: 0.72rem;
                font-weight: 700;
                letter-spacing: 0.16em;
                text-transform: uppercase;
            }

            .cc-results__total {
                display: flex;
                align-items: baseline;
                gap: 0.35rem;
                margin: 0;
            }

            .cc-results__value {
                color: #24402b;
                font-size: 2.4rem;
                font-weight: 800;
                line-height: 1;
                letter-spacing: -0.05em;
            }

            .cc-results__unit {
                color: #5d705f;
                font-size: 1rem;
                font-weight: 700;
            }

            .cc-breakdown {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 0.65rem;
            }

            .cc-breakdown__item {
                padding: 0.75rem;
                border-radius: 0.35rem;
                background: rgba(255, 255, 255, 0.8);
            }

            .cc-breakdown__label {
                margin: 0;
                color: #6d7868;
                font-size: 0.72rem;
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            .cc-breakdown__value {
                margin: 0.35rem 0 0;
                color: #203225;
                font-size: 1.05rem;
                font-weight: 800;
            }

            .cc-empty {
                display: grid;
                gap: 0.55rem;
                align-items: start;
                padding: 1rem;
                border: 1px dashed rgba(111, 149, 95, 0.28);
                border-radius: 0.35rem;
                background: rgba(255, 255, 255, 0.55);
            }

            .cc-empty__title {
                margin: 0;
                color: #243425;
                font-size: 1rem;
                font-weight: 800;
            }

            .cc-empty__copy {
                margin: 0;
                color: #6d7868;
                font-size: 0.86rem;
                line-height: 1.5;
            }

            .cc-toast {
                position: fixed;
                right: 1rem;
                bottom: 1rem;
                z-index: 50;
                border-radius: 0.35rem;
                background: #295c36;
                color: #fffef8;
                padding: 0.85rem 1rem;
            }

            .dark .cc-card {
                border-color: rgba(129, 164, 123, 0.12);
                background: linear-gradient(180deg, #111712 0%, #0d130f 100%);
            }

            .dark .cc-card__title,
            .dark .cc-row__label,
            .dark .cc-results__value,
            .dark .cc-breakdown__value,
            .dark .cc-empty__title {
                color: #eef5e8;
            }

            .dark .cc-card__subtitle,
            .dark .cc-row__help,
            .dark .cc-range-meta,
            .dark .cc-results__eyebrow,
            .dark .cc-results__unit,
            .dark .cc-breakdown__label,
            .dark .cc-empty__copy {
                color: #9daf9f;
            }

            .dark .cc-card__pill,
            .dark .cc-row {
                background: rgba(20, 28, 22, 0.92);
                border-color: rgba(129, 164, 123, 0.12);
            }

            .dark .cc-row__icon {
                background: #203126;
                color: #afd29e;
            }

            .dark .cc-select,
            .dark .cc-input {
                border-color: #2d3f31;
                background: #121914;
                color: #edf5ea;
            }

            .dark .cc-results {
                border-color: rgba(129, 164, 123, 0.14);
                background: linear-gradient(180deg, #111912 0%, #101611 100%);
            }

            .dark .cc-breakdown__item,
            .dark .cc-empty {
                background: rgba(17, 24, 18, 0.85);
            }

            .dark .cc-empty {
                border-color: rgba(129, 164, 123, 0.18);
            }

            @media (min-width: 640px) {
                .cc-actions {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }
        </style>
    @endonce

    <div class="cc-card">
        <div class="cc-card__inner">
            <div class="cc-card__header">
                <div>
                    <h3 class="cc-card__title">Carbon Calculator</h3>
                    <p class="cc-card__subtitle">Log a quick commute, device session, and meal choice without leaving the dashboard.</p>
                </div>

                <div class="cc-card__pill">Live estimate</div>
            </div>

            <form id="carbon-calculator-form" @submit.prevent="calculate" class="cc-form">
                <div class="cc-row">
                    <div class="cc-row__top">
                        <div class="cc-row__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13l1.5-4.5A3 3 0 0 1 7.35 6.5h9.3a3 3 0 0 1 2.85 2L21 13" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13h14v4a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1v-1H8v1a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-4Z" />
                                <circle cx="7.5" cy="14.5" r="1" fill="currentColor" stroke="none" />
                                <circle cx="16.5" cy="14.5" r="1" fill="currentColor" stroke="none" />
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <p class="cc-row__label">Commute</p>
                            <p class="cc-row__help">Choose your transport and adjust the round-trip distance.</p>
                        </div>
                    </div>

                    <div class="cc-row__controls">
                        <select x-model="form.transport_type" class="cc-select">
                            <option value="">Select transport</option>
                            @foreach ($transportOptions as $option)
                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>

                        <input
                            x-model="form.distance"
                            type="number"
                            min="0"
                            max="200"
                            step="0.1"
                            class="cc-input"
                            placeholder="Distance in km"
                        >

                        <div class="cc-range-wrap">
                            <div class="cc-range-meta">
                                <span>0 km</span>
                                <span x-text="`${Number(form.distance || 0).toFixed(1)} km`"></span>
                                <span>200 km</span>
                            </div>

                            <input x-model="form.distance" type="range" min="0" max="200" step="1" class="cc-range">
                        </div>
                    </div>
                </div>

                <div class="cc-row">
                    <div class="cc-row__top">
                        <div class="cc-row__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 18h6" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 22h4" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2a6 6 0 0 0-3.7 10.72c.55.44.96 1.05 1.14 1.73L9.5 15h5l.06-.55c.18-.68.59-1.29 1.14-1.73A6 6 0 0 0 12 2Z" />
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <p class="cc-row__label">Device Use</p>
                            <p class="cc-row__help">Estimate screen time or gadget usage for the day.</p>
                        </div>
                    </div>

                    <div class="cc-row__controls">
                        <select x-model="form.gadget_type" class="cc-select">
                            <option value="laptop">Laptop</option>
                            <option value="smartphone">Smartphone</option>
                            <option value="tablet">Tablet</option>
                            <option value="desktop_pc">Desktop PC</option>
                            <option value="monitor">Monitor</option>
                        </select>

                        <input
                            x-model="form.gadget_hours"
                            type="number"
                            min="0"
                            max="24"
                            step="0.1"
                            class="cc-input"
                            placeholder="Hours used"
                        >

                        <div class="cc-range-wrap">
                            <div class="cc-range-meta">
                                <span>0 hrs</span>
                                <span x-text="`${Number(form.gadget_hours || 0).toFixed(1)} hrs`"></span>
                                <span>24 hrs</span>
                            </div>

                            <input x-model="form.gadget_hours" type="range" min="0" max="24" step="0.5" class="cc-range">
                        </div>
                    </div>
                </div>

                <div class="cc-row">
                    <div class="cc-row__top">
                        <div class="cc-row__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12h12" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 8h8" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16h10" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 4h14l-1 16H6L5 4Z" />
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <p class="cc-row__label">Diet</p>
                            <p class="cc-row__help">Pick the meal pattern that best matches today.</p>
                        </div>
                    </div>

                    <div class="cc-row__controls">
                        <select x-model="form.diet_type" class="cc-select">
                            <option value="">Select diet</option>
                            @foreach ($dietOptions as $option)
                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <template x-if="error">
                    <div class="cc-error" x-text="error"></div>
                </template>

                <template x-if="loading">
                    <div class="cc-loading">
                        <dotlottie-wc
                            src="https://lottie.host/001cfd71-a974-4ad6-bbf7-eb5473be1654/uoBhWjhEv1.lottie"
                            style="width: 300px; height: 300px;"
                            autoplay
                            loop
                        ></dotlottie-wc>
                        <p class="cc-loading__title">Calculating your impact...</p>
                        <p class="cc-loading__copy">We are checking your transport, diet, and gadget activity before showing your updated footprint.</p>
                    </div>
                </template>

                <template x-if="!loading && result">
                    <div class="cc-results">
                        <p class="cc-results__eyebrow">Estimated footprint</p>

                        <p class="cc-results__total">
                            <span class="cc-results__value" x-text="result.total_emission"></span>
                            <span class="cc-results__unit">kg CO2e</span>
                        </p>

                        <div class="cc-breakdown">
                            <div class="cc-breakdown__item">
                                <p class="cc-breakdown__label">Transport</p>
                                <p class="cc-breakdown__value" x-text="`${result.breakdown.transport} kg`"></p>
                            </div>

                            <div class="cc-breakdown__item">
                                <p class="cc-breakdown__label">Diet</p>
                                <p class="cc-breakdown__value" x-text="`${result.breakdown.diet} kg`"></p>
                            </div>

                            <div class="cc-breakdown__item">
                                <p class="cc-breakdown__label">Gadgets</p>
                                <p class="cc-breakdown__value" x-text="`${result.breakdown.gadgets} kg`"></p>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="!loading && !result">
                    <div class="cc-empty">
                        <p class="cc-empty__title">Calculate your impact</p>
                        <p class="cc-empty__copy">Use the controls above to get a quick estimate, then save it directly to your carbon log.</p>
                    </div>
                </template>

                <div class="cc-actions">
                    <button type="submit" class="cc-button cc-button--primary" :disabled="loading">
                        <span x-show="!loading">Calculate</span>
                        <span x-show="loading">Calculating...</span>
                    </button>

                    <button type="button" @click="save" class="cc-button cc-button--secondary" :disabled="!result || saving">
                        <span x-show="!saving">Save Log</span>
                        <span x-show="saving">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="toast.show" x-transition.opacity.duration.250ms class="cc-toast" x-cloak>
        <p x-text="toast.message"></p>
    </div>
</section>

<script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.9.10/dist/dotlottie-wc.js" type="module"></script>
<script>
    function carbonCalculator({ calculateUrl, saveUrl, csrfToken, userId }) {
        return {
            form: {
                transport_type: '',
                distance: '',
                diet_type: '',
                gadget_type: 'laptop',
                gadget_hours: '',
            },
            result: null,
            loading: false,
            saving: false,
            error: '',
            toast: {
                show: false,
                message: '',
            },

            async calculate() {
                this.loading = true;
                this.error = '';
                this.result = null;
                const minimumAnimationTime = 3000;
                const animationDelay = new Promise((resolve) => window.setTimeout(resolve, minimumAnimationTime));

                try {
                    const responsePromise = fetch(calculateUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify(this.form),
                    });

                    const [response] = await Promise.all([responsePromise, animationDelay]);

                    const data = await response.json();

                    if (!response.ok) {
                        this.error = this.extractError(data, 'Unable to calculate emissions right now.');
                        return;
                    }

                    this.result = data;
                } catch (error) {
                    this.error = 'Something went wrong while contacting the calculator API.';
                } finally {
                    this.loading = false;
                }
            },

            async save() {
                if (!this.result) {
                    return;
                }

                if (!userId) {
                    this.error = 'You need to be logged in before saving to your log.';
                    return;
                }

                this.saving = true;
                this.error = '';

                try {
                    const response = await fetch(saveUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            ...this.form,
                            user_id: userId,
                        }),
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        this.error = this.extractError(data, 'Unable to save your carbon log right now.');
                        return;
                    }

                    window.dispatchEvent(new CustomEvent('carbon-log-saved', {
                        detail: data.log ?? null,
                    }));

                    this.showToast(data.message ?? 'Saved to your log.');
                } catch (error) {
                    this.error = 'Something went wrong while saving your carbon log.';
                } finally {
                    this.saving = false;
                }
            },

            extractError(data, fallback) {
                if (data?.message) {
                    return data.message;
                }

                const firstValidationError = Object.values(data?.errors ?? {})[0]?.[0];

                return firstValidationError ?? fallback;
            },

            showToast(message) {
                this.toast.message = message;
                this.toast.show = true;

                window.clearTimeout(this.toastTimeout);
                this.toastTimeout = window.setTimeout(() => {
                    this.toast.show = false;
                }, 2500);
            },
        };
    }
</script>
