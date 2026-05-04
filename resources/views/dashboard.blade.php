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
                border: 1px solid rgba(93, 133, 88, 0.2);
                border-radius: 0.55rem;
                background:
                    linear-gradient(135deg, rgba(246, 255, 242, 0.92) 0%, rgba(220, 243, 218, 0.86) 48%, rgba(231, 244, 235, 0.9) 100%),
                    radial-gradient(circle at 88% 12%, rgba(98, 161, 139, 0.26), transparent 30%);
                padding: 1.5rem;
                color: #1f2f20;
                isolation: isolate;
                box-shadow: 0 18px 50px rgba(47, 78, 48, 0.1);
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
                inset: auto 1rem -5rem auto;
                width: 18rem;
                height: 18rem;
                border-radius: 999px;
                background: radial-gradient(circle, rgba(255, 255, 255, 0.85) 0%, rgba(255, 255, 255, 0) 68%);
            }

            .eco-dashboard-hero::after {
                inset: -2rem 4rem auto auto;
                width: 11rem;
                height: 11rem;
                border-radius: 999px;
                background: radial-gradient(circle, rgba(255, 197, 92, 0.28) 0%, rgba(255, 197, 92, 0) 70%);
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
                height: 8.5rem;
                border-radius: 0.55rem;
                background:
                    linear-gradient(145deg, rgba(255, 255, 255, 0.72), rgba(232, 246, 229, 0.5)),
                    radial-gradient(circle at 50% 40%, rgba(118, 178, 121, 0.2), transparent 54%);
                box-shadow:
                    inset 0 0 0 1px rgba(115, 154, 120, 0.16),
                    0 16px 34px rgba(56, 88, 58, 0.12);
                overflow: hidden;
                perspective: 620px;
            }

            .eco-dashboard-hero__motion::before {
                content: '';
                position: absolute;
                inset: 0;
                background:
                    linear-gradient(90deg, rgba(255, 255, 255, 0.42), rgba(255, 255, 255, 0)),
                    repeating-linear-gradient(90deg, rgba(53, 91, 58, 0.08) 0 1px, transparent 1px 2rem);
                mask-image: linear-gradient(90deg, transparent, #000 24%, #000 76%, transparent);
            }

            .eco-dashboard-hero__motion::after {
                content: '';
                position: absolute;
                left: 12%;
                right: 12%;
                bottom: 1rem;
                height: 0.55rem;
                border-radius: 999px;
                background: rgba(43, 74, 45, 0.16);
                filter: blur(7px);
            }

            .eco-carbon-scene {
                position: absolute;
                inset: 0;
                transform-style: preserve-3d;
                animation: eco-scene-float 7s ease-in-out infinite;
            }

            .eco-carbon-core {
                position: absolute;
                top: 50%;
                left: 50%;
                width: 4.8rem;
                height: 4.8rem;
                border-radius: 999px;
                background:
                    radial-gradient(circle at 34% 28%, #ffffff 0 10%, #bde5a8 11% 27%, #5f9f62 50%, #2f6b47 78%, #204934 100%);
                box-shadow:
                    inset -0.7rem -0.65rem 1.1rem rgba(24, 65, 43, 0.42),
                    inset 0.4rem 0.35rem 0.75rem rgba(255, 255, 255, 0.72),
                    0 1.2rem 1.8rem rgba(35, 77, 48, 0.22);
                transform: translate(-50%, -50%) rotateX(58deg) rotateZ(-18deg);
                transform-style: preserve-3d;
            }

            .eco-carbon-core::before,
            .eco-carbon-core::after {
                content: '';
                position: absolute;
                border-radius: 58% 42% 52% 48%;
                background: rgba(230, 255, 220, 0.56);
                box-shadow: inset -0.2rem -0.25rem 0.4rem rgba(55, 113, 68, 0.18);
            }

            .eco-carbon-core::before {
                width: 1.35rem;
                height: 0.95rem;
                top: 1.1rem;
                left: 1rem;
                transform: rotate(-28deg);
            }

            .eco-carbon-core::after {
                width: 1.6rem;
                height: 1.05rem;
                right: 0.78rem;
                bottom: 1.05rem;
                transform: rotate(24deg);
            }

            .eco-carbon-ring {
                position: absolute;
                top: 50%;
                left: 50%;
                border: 1px solid rgba(49, 104, 70, 0.24);
                border-radius: 999px;
                transform-style: preserve-3d;
            }

            .eco-carbon-ring--wide {
                width: 13.5rem;
                height: 4.4rem;
                transform: translate(-50%, -50%) rotateX(64deg) rotateZ(-9deg);
                animation: eco-orbit 9s linear infinite;
            }

            .eco-carbon-ring--tilt {
                width: 10.5rem;
                height: 3.4rem;
                transform: translate(-50%, -50%) rotateX(64deg) rotateZ(52deg);
                animation: eco-orbit-reverse 7s linear infinite;
            }

            .eco-carbon-node {
                position: absolute;
                display: inline-grid;
                place-items: center;
                width: 2.05rem;
                height: 2.05rem;
                border: 1px solid rgba(255, 255, 255, 0.62);
                border-radius: 999px;
                background: linear-gradient(145deg, #fff9e8, #f4c865);
                color: #5d4a12;
                box-shadow: 0 0.65rem 1rem rgba(72, 83, 39, 0.18);
                transform: translateZ(1.3rem) rotateX(-64deg);
            }

            .eco-carbon-node svg {
                width: 1.08rem;
                height: 1.08rem;
            }

            .eco-carbon-node--transport {
                left: -0.95rem;
                top: 50%;
                transform: translateY(-50%) translateZ(1.3rem) rotateX(-64deg);
            }

            .eco-carbon-node--food {
                right: 0.2rem;
                top: -0.85rem;
                background: linear-gradient(145deg, #eefbea, #82bf72);
                color: #245d33;
            }

            .eco-carbon-node--energy {
                left: 46%;
                bottom: -0.98rem;
                background: linear-gradient(145deg, #e9fbff, #77c7c5);
                color: #155e63;
            }

            .eco-carbon-spark {
                position: absolute;
                width: 0.45rem;
                height: 0.45rem;
                border-radius: 999px;
                background: #f2c866;
                box-shadow: 0 0 0.9rem rgba(242, 200, 102, 0.75);
                animation: eco-spark 3.6s ease-in-out infinite;
            }

            .eco-carbon-spark--one {
                top: 1.4rem;
                left: 3rem;
            }

            .eco-carbon-spark--two {
                right: 3.7rem;
                bottom: 1.6rem;
                animation-delay: 1.3s;
            }

            @keyframes eco-scene-float {
                0%, 100% {
                    transform: translateY(0) rotateX(0);
                }

                50% {
                    transform: translateY(-0.35rem) rotateX(3deg);
                }
            }

            @keyframes eco-orbit {
                from {
                    transform: translate(-50%, -50%) rotateX(64deg) rotateZ(-9deg);
                }

                to {
                    transform: translate(-50%, -50%) rotateX(64deg) rotateZ(351deg);
                }
            }

            @keyframes eco-orbit-reverse {
                from {
                    transform: translate(-50%, -50%) rotateX(64deg) rotateZ(52deg);
                }

                to {
                    transform: translate(-50%, -50%) rotateX(64deg) rotateZ(-308deg);
                }
            }

            @keyframes eco-spark {
                0%, 100% {
                    opacity: 0.35;
                    transform: translate3d(0, 0, 0) scale(0.72);
                }

                50% {
                    opacity: 1;
                    transform: translate3d(0.25rem, -0.35rem, 1rem) scale(1);
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
                background:
                    linear-gradient(135deg, #122318 0%, #183220 58%, #14262a 100%),
                    radial-gradient(circle at 88% 12%, rgba(87, 180, 159, 0.18), transparent 30%);
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

            .dark .eco-carbon-ring {
                border-color: rgba(199, 235, 199, 0.22);
            }

            .dark .eco-carbon-core {
                box-shadow:
                    inset -0.7rem -0.65rem 1.1rem rgba(8, 24, 14, 0.56),
                    inset 0.4rem 0.35rem 0.75rem rgba(255, 255, 255, 0.36),
                    0 1.2rem 1.8rem rgba(0, 0, 0, 0.24);
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

    <div class="eco-dashboard eco-page-palette">
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
                    <div class="eco-carbon-scene">
                        <span class="eco-carbon-spark eco-carbon-spark--one"></span>
                        <span class="eco-carbon-spark eco-carbon-spark--two"></span>
                        <span class="eco-carbon-ring eco-carbon-ring--wide">
                            <span class="eco-carbon-node eco-carbon-node--transport">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 17h10M6 17l1.2-5.4A3 3 0 0 1 10.1 9h3.8a3 3 0 0 1 2.9 2.6L18 17" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 17v1M16 17v1" />
                                </svg>
                            </span>
                            <span class="eco-carbon-node eco-carbon-node--food">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M18.8 4.3c-3.1.2-5.4 1.4-6.9 3.6-1.1 1.6-1.6 3.5-1.5 5.1a.7.7 0 0 0 .9.6c1.5-.5 3.2-1.4 4.7-3 1.9-2 2.9-4.6 2.8-6.3Z" />
                                    <path d="M6.3 5.4c2.2.2 3.8 1 4.8 2.4.7 1 .9 2.2.8 3.2a.55.55 0 0 1-.7.45c-1-.36-2.2-.98-3.2-2.05-1.3-1.4-1.9-3.1-1.7-4Z" />
                                </svg>
                            </span>
                        </span>
                        <span class="eco-carbon-ring eco-carbon-ring--tilt">
                            <span class="eco-carbon-node eco-carbon-node--energy">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M13.5 2.8 6.4 13.1a.75.75 0 0 0 .62 1.17h4.14l-.64 6.78c-.08.82.99 1.2 1.45.52l7.05-10.34a.75.75 0 0 0-.62-1.17h-4.08l.6-6.72c.08-.82-.96-1.2-1.42-.55Z" />
                                </svg>
                            </span>
                        </span>
                        <span class="eco-carbon-core"></span>
                    </div>
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
