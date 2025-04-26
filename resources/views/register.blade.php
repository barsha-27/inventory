<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Include Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f7fa;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .register-container {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .register-container h2 {
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .input-group {
            margin-bottom: 1rem;
        }
        .input-group label {
            display: block;
            margin-bottom: 0.3rem;
        }
        .input-group input {
            width: 100%;
            padding: 0.6rem;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .error {
            color: red;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }
        button {
            width: 100%;
            background-color: #4c6ef5;
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
        }
        button:disabled {
            background-color: #a5b4fc;
            cursor: not-allowed;
        }
        .extra-links {
            margin-top: 1rem;
            text-align: center;
            font-size: 0.9rem;
        }
        .extra-links a {
            color: #4c6ef5;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Sign Up</h2>
        <form id="registerForm">
            <div class="input-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
                <div class="error" id="nameError"></div>
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                <div class="error" id="emailError"></div>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <div class="error" id="passwordError"></div>
            </div>
            <div class="input-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
                <div class="error" id="confirmError"></div>
            </div>
            <button type="submit" id="registerBtn">Register</button>
            <div class="error" id="registerError" style="text-align:center; margin-top: 1rem;"></div>
        </form>
        <div class="extra-links">
            Already have an account? <a href="/">Log in</a>
        </div>
    </div>

    <script>
        const form = document.getElementById('registerForm');
        const registerBtn = document.getElementById('registerBtn');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            registerBtn.disabled = true;
            registerBtn.textContent = "Registering...";

            // Clear previous errors
            ['nameError', 'emailError', 'passwordError', 'confirmError', 'registerError'].forEach(id => {
                document.getElementById(id).textContent = '';
            });

            const data = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                password_confirmation: document.getElementById('password_confirmation').value
            };

            axios.post('/api/register', data)
                .then(res => {
                    const token = res.data.token;
                    localStorage.setItem('auth_token', token);
                    alert('Registration successful!');
                    window.location.href = "/";
                })
                .catch(err => {
                    if (err.response.status === 422) {
                        const errors = err.response.data.errors;
                        if (errors.name) {
                            document.getElementById('nameError').textContent = errors.name[0];
                        }
                        if (errors.email) {
                            document.getElementById('emailError').textContent = errors.email[0];
                        }
                        if (errors.password) {
                            document.getElementById('passwordError').textContent = errors.password[0];
                        }
                        if (errors.password_confirmation) {
                            document.getElementById('confirmError').textContent = errors.password_confirmation[0];
                        }
                    } else {
                        document.getElementById('registerError').textContent = "Something went wrong. Try again.";
                    }
                })
                .finally(() => {
                    registerBtn.disabled = false;
                    registerBtn.textContent = "Register";
                });
        });
    </script>
</body>
</html>
