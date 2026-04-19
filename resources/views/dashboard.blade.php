<x-layouts::app :title="__('Dashboard')">
    @php
        $user = auth()->user();
        $name = $user?->name ?: 'Eco Track User';
        $profileImage = $user?->profileImage();
        $initials = collect(explode(' ', trim($name)))
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('');
    @endphp

    @once
        <style>
            .eco-dashboard {
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
            }

            .eco-dashboard-hero {
                position: relative;
                overflow: hidden;
                border: 1px solid rgba(111, 149, 95, 0.24);
                border-radius: 0.35rem;
                background: #dff5df;
                padding: 1.5rem;
                color: #1f2f20;
                isolation: isolate;
            }

            .eco-dashboard-hero::before,
            .eco-dashboard-hero::after {
                content: '';
                position: absolute;
                pointer-events: none;
                opacity: 0.15;
                z-index: -1;
            }

            .eco-dashboard-hero::before {
                inset: auto 2rem -3rem auto;
                width: 14rem;
                height: 14rem;
                border-radius: 45% 55% 60% 40%;
                background: radial-gradient(circle, rgba(230, 244, 225, 0.95) 0%, rgba(230, 244, 225, 0) 70%);
            }

            .eco-dashboard-hero::after {
                inset: -2rem 4rem auto auto;
                width: 9rem;
                height: 9rem;
                border-radius: 60% 40% 50% 50%;
                background: radial-gradient(circle, rgba(240, 248, 236, 0.85) 0%, rgba(240, 248, 236, 0) 72%);
            }

            .eco-dashboard-hero__inner {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1.5rem;
            }

            .eco-dashboard-profile {
                display: flex;
                align-items: center;
                gap: 1rem;
                min-width: 0;
            }

            .eco-dashboard-avatar {
                position: relative;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                flex-shrink: 0;
                width: 4.5rem;
                height: 4.5rem;
                border: 3px solid rgba(255, 255, 255, 0.72);
                border-radius: 999px;
                background:
                    radial-gradient(circle at 35% 35%, #f9fff4 0%, #e1f0d8 52%, #a8c39a 100%);
                color: #355338;
                font-size: 1.35rem;
                font-weight: 800;
                letter-spacing: 0.04em;
                box-shadow: 0 12px 22px rgba(34, 63, 39, 0.14);
            }

            .eco-dashboard-avatar img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .eco-dashboard-hero__kicker {
                margin: 0;
                color: #6b7f6c;
                font-size: 0.74rem;
                font-weight: 700;
                letter-spacing: 0.18em;
                text-transform: uppercase;
            }

            .eco-dashboard-hero__title {
                margin: 0.28rem 0 0;
                color: #1f2f20;
                font-size: clamp(1.8rem, 2.8vw, 2.7rem);
                font-weight: 800;
                line-height: 1.02;
                letter-spacing: -0.045em;
            }

            .eco-dashboard-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.45rem;
                margin-top: 0.8rem;
                padding: 0.42rem 0.78rem;
                border: 1px solid rgba(255, 255, 255, 0.32);
                border-radius: 999px;
                background: #eef4e7;
                color: #486247;
                font-size: 0.92rem;
                font-weight: 700;
                line-height: 1;
            }

            .eco-dashboard-badge svg {
                width: 0.95rem;
                height: 0.95rem;
            }

            .eco-dashboard-hero__motion {
                position: relative;
                width: min(100%, 19rem);
                min-width: 15rem;
                height: 7.5rem;
                border-radius: 1.25rem;
                background: rgba(244, 255, 244, 0.55);
                box-shadow: inset 0 0 0 1px rgba(115, 154, 120, 0.18);
                overflow: hidden;
            }

            .eco-dashboard-hero__motion::before {
                content: '';
                position: absolute;
                inset: 0;
                background:
                    linear-gradient(135deg, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0)),
                    radial-gradient(circle at 20% 50%, rgba(181, 221, 183, 0.34), rgba(181, 221, 183, 0) 54%);
            }

            .eco-dashboard-footprint {
                position: absolute;
                top: 50%;
                left: -18%;
                width: 3rem;
                height: 3rem;
                color: rgba(61, 118, 73, 0.78);
                transform: translateY(-50%);
                animation: eco-footprints 6.4s linear infinite;
            }

            .eco-dashboard-footprint svg {
                width: 100%;
                height: 100%;
                display: block;
            }

            .eco-dashboard-footprint--two {
                animation-delay: 1.6s;
            }

            .eco-dashboard-footprint--three {
                animation-delay: 3.2s;
            }

            .eco-dashboard-footprint--four {
                animation-delay: 4.8s;
            }

            @keyframes eco-footprints {
                0% {
                    left: -18%;
                    opacity: 0;
                    transform: translateY(-50%) rotate(-14deg) scale(0.72);
                }

                15% {
                    opacity: 0.9;
                }

                35% {
                    transform: translateY(-78%) rotate(-8deg) scale(0.9);
                }

                50% {
                    transform: translateY(-24%) rotate(7deg) scale(1);
                }

                70% {
                    transform: translateY(-76%) rotate(-6deg) scale(0.92);
                }

                85% {
                    opacity: 0.9;
                }

                100% {
                    left: 108%;
                    opacity: 0;
                    transform: translateY(-34%) rotate(10deg) scale(0.76);
                }
            }

            .eco-dashboard-main {
                display: grid;
                gap: 1.5rem;
                align-items: start;
            }

            .eco-dashboard-panel {
                min-width: 0;
            }

            .eco-dashboard-secondary {
                display: grid;
                gap: 1.5rem;
            }

            .eco-dashboard-section {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }

            .eco-dashboard-section__header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
            }

            .eco-dashboard-section__eyebrow {
                margin: 0;
                color: #5c6f60;
                font-size: 0.73rem;
                font-weight: 700;
                letter-spacing: 0.16em;
                text-transform: uppercase;
            }

            .eco-dashboard-section__title {
                margin: 0.22rem 0 0;
                color: #203325;
                font-size: 1.35rem;
                font-weight: 800;
                letter-spacing: -0.03em;
            }

            .eco-dashboard-section__link {
                color: #4f7b56;
                font-size: 0.9rem;
                font-weight: 700;
                text-decoration: none;
            }

            .eco-dashboard-section__link:hover {
                color: #315e39;
            }

            .dark .eco-dashboard-hero {
                border-color: rgba(148, 190, 150, 0.16);
                background: #183220;
            }

            .dark .eco-dashboard-avatar {
                border-color: rgba(216, 240, 216, 0.4);
                background:
                    radial-gradient(circle at 35% 35%, #f0f7eb 0%, #bad6b0 48%, #587955 100%);
                color: #244128;
            }

            .dark .eco-dashboard-badge {
                border-color: rgba(148, 190, 150, 0.24);
                background: rgba(231, 243, 227, 0.9);
                color: #38553a;
            }

            .dark .eco-dashboard-hero__motion {
                background: rgba(10, 24, 13, 0.22);
                box-shadow: inset 0 0 0 1px rgba(148, 190, 150, 0.16);
            }

            .dark .eco-dashboard-footprint {
                color: rgba(204, 238, 205, 0.72);
            }

            .dark .eco-dashboard-section__eyebrow {
                color: #91a08f;
            }

            .dark .eco-dashboard-section__title {
                color: #f2f5ef;
            }

            .dark .eco-dashboard-section__link {
                color: #a6d3ab;
            }

            @media (min-width: 1024px) {
                .eco-dashboard-main {
                    grid-template-columns: minmax(0, 1.08fr) minmax(22rem, 0.92fr);
                }
            }

            @media (max-width: 767px) {
                .eco-dashboard-hero {
                    padding: 1.2rem;
                }

                .eco-dashboard-hero__inner {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .eco-dashboard-profile {
                    width: 100%;
                }

                .eco-dashboard-hero__motion {
                    width: 100%;
                    min-width: 0;
                }
            }
        </style>
    @endonce

    <div class="eco-dashboard">
        <section class="eco-dashboard-hero">
            <div class="eco-dashboard-hero__inner">
                <div class="eco-dashboard-profile">
                    <div class="eco-dashboard-avatar">
                        @if ($profileImage)
                            <img src="{{ $profileImage }}" alt="{{ $name }} profile picture">
                        @else
                            {{ $initials }}
                        @endif
                    </div>

                    <div class="min-w-0">
                        <p class="eco-dashboard-hero__kicker">Dashboard</p>
                        <h1 class="eco-dashboard-hero__title">{{ $name }}</h1>

                        <div class="eco-dashboard-badge">
                            <span>Eco-Warrior</span>
                            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M19.2 4.8c-4.46 0-8.09 1.72-10.52 4.98-1.72 2.31-2.57 5.03-2.68 7.25a.75.75 0 0 0 .98.77c1.94-.58 4.34-1.68 6.42-3.76 2.9-2.9 4.4-6.75 4.4-11.44 0-.41-.33-.75-.75-.75Z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="eco-dashboard-hero__motion" aria-hidden="true">
                    <span class="eco-dashboard-footprint eco-dashboard-footprint--one">
                        <svg viewBox="0 0 64 64" fill="currentColor">
                            <path d="M24 11c3.4 0 6 4.1 6 8.9s-2.6 8.9-6 8.9-6-4.1-6-8.9S20.6 11 24 11Zm14.3 4.8c2.8 0 4.9 3.3 4.9 7.2s-2.1 7.2-4.9 7.2-4.9-3.3-4.9-7.2 2.1-7.2 4.9-7.2Zm-26.2 5C14.2 20.8 16 23.7 16 27s-1.8 6.2-3.9 6.2S8.2 30.3 8.2 27s1.8-6.2 3.9-6.2Zm36.1 2.2c2.1 0 3.8 2.7 3.8 6.1s-1.7 6.1-3.8 6.1-3.8-2.7-3.8-6.1 1.7-6.1 3.8-6.1ZM28.8 27c7.7 0 14.1 7.3 14.1 16.4 0 6.4-4.7 9.6-11.2 9.6-10 0-18.6-6.7-18.6-14.6 0-5.7 6.9-11.4 15.7-11.4Z"/>
                        </svg>
                    </span>
                    <span class="eco-dashboard-footprint eco-dashboard-footprint--two">
                        <svg viewBox="0 0 64 64" fill="currentColor">
                            <path d="M24 11c3.4 0 6 4.1 6 8.9s-2.6 8.9-6 8.9-6-4.1-6-8.9S20.6 11 24 11Zm14.3 4.8c2.8 0 4.9 3.3 4.9 7.2s-2.1 7.2-4.9 7.2-4.9-3.3-4.9-7.2 2.1-7.2 4.9-7.2Zm-26.2 5C14.2 20.8 16 23.7 16 27s-1.8 6.2-3.9 6.2S8.2 30.3 8.2 27s1.8-6.2 3.9-6.2Zm36.1 2.2c2.1 0 3.8 2.7 3.8 6.1s-1.7 6.1-3.8 6.1-3.8-2.7-3.8-6.1 1.7-6.1 3.8-6.1ZM28.8 27c7.7 0 14.1 7.3 14.1 16.4 0 6.4-4.7 9.6-11.2 9.6-10 0-18.6-6.7-18.6-14.6 0-5.7 6.9-11.4 15.7-11.4Z"/>
                        </svg>
                    </span>
                    <span class="eco-dashboard-footprint eco-dashboard-footprint--three">
                        <svg viewBox="0 0 64 64" fill="currentColor">
                            <path d="M24 11c3.4 0 6 4.1 6 8.9s-2.6 8.9-6 8.9-6-4.1-6-8.9S20.6 11 24 11Zm14.3 4.8c2.8 0 4.9 3.3 4.9 7.2s-2.1 7.2-4.9 7.2-4.9-3.3-4.9-7.2 2.1-7.2 4.9-7.2Zm-26.2 5C14.2 20.8 16 23.7 16 27s-1.8 6.2-3.9 6.2S8.2 30.3 8.2 27s1.8-6.2 3.9-6.2Zm36.1 2.2c2.1 0 3.8 2.7 3.8 6.1s-1.7 6.1-3.8 6.1-3.8-2.7-3.8-6.1 1.7-6.1 3.8-6.1ZM28.8 27c7.7 0 14.1 7.3 14.1 16.4 0 6.4-4.7 9.6-11.2 9.6-10 0-18.6-6.7-18.6-14.6 0-5.7 6.9-11.4 15.7-11.4Z"/>
                        </svg>
                    </span>
                    <span class="eco-dashboard-footprint eco-dashboard-footprint--four">
                        <svg viewBox="0 0 64 64" fill="currentColor">
                            <path d="M24 11c3.4 0 6 4.1 6 8.9s-2.6 8.9-6 8.9-6-4.1-6-8.9S20.6 11 24 11Zm14.3 4.8c2.8 0 4.9 3.3 4.9 7.2s-2.1 7.2-4.9 7.2-4.9-3.3-4.9-7.2 2.1-7.2 4.9-7.2Zm-26.2 5C14.2 20.8 16 23.7 16 27s-1.8 6.2-3.9 6.2S8.2 30.3 8.2 27s1.8-6.2 3.9-6.2Zm36.1 2.2c2.1 0 3.8 2.7 3.8 6.1s-1.7 6.1-3.8 6.1-3.8-2.7-3.8-6.1 1.7-6.1 3.8-6.1ZM28.8 27c7.7 0 14.1 7.3 14.1 16.4 0 6.4-4.7 9.6-11.2 9.6-10 0-18.6-6.7-18.6-14.6 0-5.7 6.9-11.4 15.7-11.4Z"/>
                        </svg>
                    </span>
                </div>
            </div>
        </section>

        <section class="eco-dashboard-main">
            <div class="eco-dashboard-panel">
                <div class="eco-dashboard-section">
                    <livewire:carbon-calculator />
                </div>
            </div>

            <div class="eco-dashboard-panel eco-dashboard-secondary">
                <livewire:dashboard-stats />
            </div>
        </section>

    </div>
</x-layouts::app>
