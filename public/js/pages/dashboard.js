const modalOverlays = document.querySelectorAll('.modal-overlay');

        function setModalState(modal, isOpen) {
            if (!modal) {
                return;
            }

            modal.classList.toggle('is-open', isOpen);
            modal.setAttribute('aria-hidden', String(!isOpen));
            document.body.style.overflow = document.querySelector('.modal-overlay.is-open') ? 'hidden' : '';
        }

        document.querySelectorAll('[data-open-modal]').forEach((button) => {
            button.addEventListener('click', () => {
                const modal = document.getElementById(button.dataset.openModal);
                setModalState(modal, true);
                button.setAttribute('aria-expanded', 'true');
            });
        });

        document.querySelectorAll('[data-close-modal]').forEach((button) => {
            button.addEventListener('click', () => {
                const modal = document.getElementById(button.dataset.closeModal);
                setModalState(modal, false);
            });
        });

        modalOverlays.forEach((modal) => {
            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    setModalState(modal, false);
                }
            });
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                modalOverlays.forEach((modal) => setModalState(modal, false));
            }
        });

        if (document.querySelector('.modal-overlay.is-open')) {
            document.body.style.overflow = 'hidden';
        }
