const applyStoredTheme = () => {
    const savedTheme = window.localStorage.getItem('flux.appearance');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const shouldUseDark = savedTheme === 'dark' || (! savedTheme && prefersDark);

    document.documentElement.classList.toggle('dark', shouldUseDark);

    if (window.Flux && typeof window.Flux.applyAppearance === 'function') {
        window.Flux.applyAppearance(savedTheme || 'system');
    }
};

const syncPageTitle = () => {
    const titleSource = document.querySelector('[data-page-title]');

    if (! titleSource) {
        return;
    }

    const nextTitle = titleSource.getAttribute('data-page-title');

    if (! nextTitle) {
        return;
    }

    document.title = nextTitle;

    const titleMeta = document.head.querySelector('meta[name="title"]');
    const ogTitleMeta = document.head.querySelector('meta[property="og:title"]');

    if (titleMeta) {
        titleMeta.setAttribute('content', nextTitle);
    }

    if (ogTitleMeta) {
        ogTitleMeta.setAttribute('content', nextTitle);
    }
};

const syncAppChrome = () => {
    applyStoredTheme();
    syncPageTitle();
};

document.addEventListener('DOMContentLoaded', syncAppChrome);
document.addEventListener('livewire:navigated', syncAppChrome);
window.addEventListener('storage', (event) => {
    if (event.key === 'flux.appearance') {
        applyStoredTheme();
    }
});
