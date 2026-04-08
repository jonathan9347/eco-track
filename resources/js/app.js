const applyStoredTheme = () => {
    const savedTheme = window.localStorage.getItem('flux.appearance');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const shouldUseDark = savedTheme === 'dark' || (! savedTheme && prefersDark);

    document.documentElement.classList.toggle('dark', shouldUseDark);

    if (window.Flux && typeof window.Flux.applyAppearance === 'function') {
        window.Flux.applyAppearance(savedTheme || 'system');
    }
};

document.addEventListener('DOMContentLoaded', applyStoredTheme);
document.addEventListener('livewire:navigated', applyStoredTheme);
window.addEventListener('storage', (event) => {
    if (event.key === 'flux.appearance') {
        applyStoredTheme();
    }
});
