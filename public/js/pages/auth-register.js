(() => {
            const owl = document.getElementById('coffee-owl');
            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const passwordConfirmation = document.getElementById('password_confirmation');

            if (!owl || !name || !email || !password || !passwordConfirmation) {
                return;
            }

            const clearState = () => {
                owl.classList.remove('is-name', 'is-email', 'is-password', 'is-password-confirmation');
            };

            name.addEventListener('focus', () => {
                clearState();
                owl.classList.add('is-name');
            });

            email.addEventListener('focus', () => {
                clearState();
                owl.classList.add('is-email');
            });

            password.addEventListener('focus', () => {
                clearState();
                owl.classList.add('is-password');
            });

            passwordConfirmation.addEventListener('focus', () => {
                clearState();
                owl.classList.add('is-password-confirmation');
            });

            [name, email, password, passwordConfirmation].forEach((element) => {
                element.addEventListener('blur', clearState);
            });
        })();
