@php($pageTitle = filled($title ?? null) ? $title : config('app.name', 'Eco Track'))
@php($pageDescription = 'Eco Track helps students log carbon footprint activity, view insights, and build greener habits.')
@php($pageImage = asset('assets/logo-metadata.png'))

<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $pageTitle }}</title>
<meta name="title" content="{{ $pageTitle }}" />
<meta name="description" content="{{ $pageDescription }}" />
<meta property="og:title" content="{{ $pageTitle }}" />
<meta property="og:description" content="{{ $pageDescription }}" />
<meta property="og:image" content="{{ $pageImage }}" />
<meta property="og:image:width" content="512" />
<meta property="og:image:height" content="512" />
<meta property="og:type" content="website" />
<meta name="twitter:card" content="summary" />
<meta name="twitter:title" content="{{ $pageTitle }}" />
<meta name="twitter:description" content="{{ $pageDescription }}" />
<meta name="twitter:image" content="{{ $pageImage }}" />

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
<link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700" rel="stylesheet" />

<script>
    (() => {
        const savedTheme = window.localStorage.getItem('flux.appearance');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const shouldUseDark = savedTheme === 'dark' || (!savedTheme && prefersDark);

        document.documentElement.classList.toggle('dark', shouldUseDark);
    })();
</script>
@fluxAppearance
@vite(['resources/css/app.css', 'resources/js/app.js'])
