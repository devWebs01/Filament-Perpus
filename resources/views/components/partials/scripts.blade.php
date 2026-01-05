<script>
    (function() {
        'use strict';

        // Store event listeners for cleanup
        const eventListeners = [];

        function addEventListenerSafe(element, event, handler) {
            if (!element) return;
            element.addEventListener(event, handler);
            eventListeners.push({ element, event, handler });
        }

        function cleanupEventListeners() {
            eventListeners.forEach(({ element, event, handler }) => {
                element.removeEventListener(event, handler);
            });
            eventListeners.length = 0;
        }

        // Initialize menu functionality
        function initMenu() {
            try {
                const openMenu = document.getElementById('openMenu');
                const closeMenu = document.getElementById('closeMenu');
                const menu = document.getElementById('menu');
                const section = document.getElementById('section');

                if (openMenu && menu && section) {
                    addEventListenerSafe(openMenu, 'click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        menu.classList.remove('max-md:w-0');
                        menu.classList.add('max-md:w-full');
                        section.classList.add('overflow-hidden');
                    });
                }

                if (closeMenu && menu && section) {
                    addEventListenerSafe(closeMenu, 'click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        menu.classList.remove('max-md:w-full');
                        menu.classList.add('max-md:w-0');
                        section.classList.remove('overflow-hidden');
                    });
                }
            } catch (error) {
                console.error('Error initializing menu:', error);
            }
        }

        // Initialize on DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMenu);
        } else {
            initMenu();
        }

        // Cleanup on page unload
        window.addEventListener('beforeunload', cleanupEventListeners);

        // Expose cleanup function globally for manual cleanup if needed
        window.cleanupMenuEventListeners = cleanupEventListeners;
    })();
</script>

<!-- Livewire Alert Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Global error handler for unhandled errors
    window.addEventListener('error', function(event) {
        console.error('Global error:', event.error);
    });

    // Handle unhandled promise rejections
    window.addEventListener('unhandledrejection', function(event) {
        console.error('Unhandled promise rejection:', event.reason);
    });
</script>
