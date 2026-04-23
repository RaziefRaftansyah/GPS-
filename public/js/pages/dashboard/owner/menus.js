const menuEditToggles = document.querySelectorAll('[data-edit-toggle]');

        function setMenuEditPanelState(button, isOpen) {
            const panel = document.getElementById(button.dataset.targetId);

            if (!panel) {
                return;
            }

            panel.hidden = !isOpen;
            button.setAttribute('aria-expanded', String(isOpen));
            button.textContent = isOpen ? button.dataset.labelOpen : button.dataset.labelClose;
        }

        menuEditToggles.forEach((button) => {
            setMenuEditPanelState(button, false);

            button.addEventListener('click', () => {
                const panel = document.getElementById(button.dataset.targetId);

                if (!panel) {
                    return;
                }

                const shouldOpen = panel.hidden;

                menuEditToggles.forEach((otherButton) => {
                    if (otherButton !== button) {
                        setMenuEditPanelState(otherButton, false);
                    }
                });

                setMenuEditPanelState(button, shouldOpen);
            });
        });
