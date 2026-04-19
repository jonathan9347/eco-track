<x-layouts::app :title="__('Eco Chat')">
    <style>
        .eco-chat-page [data-chat-guide-button] {
            display: inline-flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
    </style>

    <div class="eco-chat-page">
        <livewire:eco-chat />
    </div>

    <script>
        (() => {
            const markGuideButtons = () => {
                const nodes = document.querySelectorAll('.eco-chat-page button, .eco-chat-page a, .eco-chat-page summary');

                nodes.forEach((node) => {
                    const text = (node.textContent || '').trim().toLowerCase();

                    if (! text.includes('guide')) {
                        return;
                    }

                    node.setAttribute('data-chat-guide-button', 'true');
                    node.style.display = 'inline-flex';
                    node.style.visibility = 'visible';
                    node.style.opacity = '1';
                });
            };

            document.addEventListener('DOMContentLoaded', markGuideButtons);
            document.addEventListener('livewire:navigated', markGuideButtons);
            document.addEventListener('livewire:initialized', markGuideButtons);

            const observer = new MutationObserver(() => markGuideButtons());
            observer.observe(document.body, { childList: true, subtree: true });
        })();
    </script>
</x-layouts::app>
