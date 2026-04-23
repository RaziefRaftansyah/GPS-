(() => {
            const owl = document.getElementById('coffee-owl');
            const email = document.getElementById('email');
            const password = document.getElementById('password');

            if (!owl || !email || !password) {
                return;
            }

            const clearState = () => {
                owl.classList.remove('is-email', 'is-password');
            };

            email.addEventListener('focus', () => {
                clearState();
                owl.classList.add('is-email');
            });

            password.addEventListener('focus', () => {
                clearState();
                owl.classList.add('is-password');
            });

            email.addEventListener('blur', clearState);
            password.addEventListener('blur', clearState);
        })();
