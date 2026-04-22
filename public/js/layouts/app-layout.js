const sidebarElement = document.querySelector('[data-mobile-sidebar]');
            const sidebarOverlay = document.querySelector('[data-sidebar-overlay]');
            const sidebarToggleButtons = document.querySelectorAll('[data-sidebar-toggle]');
            const sidebarCloseButtons = document.querySelectorAll('[data-sidebar-close]');

            function setSidebarState(isOpen) {
                if (!sidebarElement || !sidebarOverlay) {
                    return;
                }

                sidebarElement.classList.toggle('is-open', isOpen);
                sidebarOverlay.classList.toggle('is-open', isOpen);
                document.body.style.overflow = isOpen ? 'hidden' : '';

                sidebarToggleButtons.forEach((button) => {
                    button.setAttribute('aria-expanded', String(isOpen));
                });
            }

            sidebarToggleButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const shouldOpen = !sidebarElement?.classList.contains('is-open');
                    setSidebarState(shouldOpen);
                });
            });

            sidebarCloseButtons.forEach((button) => {
                button.addEventListener('click', () => setSidebarState(false));
            });

            sidebarOverlay?.addEventListener('click', () => setSidebarState(false));

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    setSidebarState(false);
                }
            });
