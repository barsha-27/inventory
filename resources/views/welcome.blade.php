<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Include Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Styles -->
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
        .login-container {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-container h2 {
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
    <div class="login-container">
        <h2>Log In</h2>
        <form id="loginForm">
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                <div class="error" id="emailError"></div>
            </div>
            <div class="input-group">
                <label for="password">
                    Password
                    <a href="#" style="float: right; font-size: 0.8rem;">Forgot?</a>
                </label>
                <input type="password" id="password" name="password" required>
                <div class="error" id="passwordError"></div>
            </div>
            <button type="submit" id="loginBtn">Log In</button>
            <div class="error" id="loginError" style="text-align:center; margin-top: 1rem;"></div>
        </form>
        <div class="extra-links">
            Don’t have an account? <a href="/register">Sign up</a>
        </div>
    </div>

    <script>
        const form = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            loginBtn.disabled = true;
            loginBtn.textContent = "Logging in...";

            // Clear errors
            document.getElementById('emailError').textContent = '';
            document.getElementById('passwordError').textContent = '';
            document.getElementById('loginError').textContent = '';

            const data = {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
            };

            axios.post('/api/login', data)
                .then(res => {
                    const token = res.data.token;
                    localStorage.setItem('auth_token', token);
                    alert('Login successful!');
                    window.location.href = "/dashboard"; // redirect to dashboard or home
                })
                .catch(err => {
                    if (err.response.status === 422) {
                        const errors = err.response.data.errors;
                        if (errors.email) {
                            document.getElementById('emailError').textContent = errors.email[0];
                        }
                        if (errors.password) {
                            document.getElementById('passwordError').textContent = errors.password[0];
                        }
                    } else if (err.response.status === 401) {
                        document.getElementById('loginError').textContent = err.response.data.message;
                    } else {
                        document.getElementById('loginError').textContent = "Something went wrong. Try again.";
                    }
                })
                .finally(() => {
                    loginBtn.disabled = false;
                    loginBtn.textContent = "Log In";
                });
        });
    </script>
</body>
</html>
