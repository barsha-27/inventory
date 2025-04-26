<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #eef1f7;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .dashboard-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        .dashboard-container h1 {
            margin-bottom: 1rem;
        }
        .logout-btn {
            margin-top: 1rem;
            padding: .7rem 1rem;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn {
            margin-top: 1rem;
            padding: 0.5rem 1rem;
            background-color:rgb(13, 133, 111);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Welcome to the Dashboard</h1>
        <p>You are logged in!</p>

        <a class="btn"  href="/category">Category</a>
        <a class="btn"  href="/product">Product</a>
        <button class="logout-btn" onclick="logout()">Logout</button>
    </div>

    <script>
        function logout() {
            localStorage.removeItem('auth_token');
            alert("Logged out!");
            window.location.href = "/";
        }

        // Redirect if not logged in
        if (!localStorage.getItem('auth_token')) {
            window.location.href = "/";
        }
    </script>
</body>
</html>
