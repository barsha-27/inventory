<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Category Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 2rem;
        }
        h1 {
            text-align: center;
        }
        .form-container, .table-container {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin: 1rem auto;
            max-width: 600px;
        }
        input[type="text"] {
            width: 100%;
            padding: 0.6rem;
            margin-bottom: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            padding: 0.6rem 1.2rem;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        table th, table td {
            padding: 0.75rem;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        .actions button {
            margin-right: 0.5rem;
            background-color: #2196F3;
        }
        .actions .delete-btn {
            background-color: #f44336;
        }
        .error {
            color: red;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <h1>Manage Categories</h1>

    <div class="form-container">
        <h3 id="formTitle">Add Category</h3>
        <input type="text" id="categoryName" placeholder="Category name">
        <div class="error" id="nameError"></div>
        <button onclick="submitCategory()" id="submitBtn">Add</button>
    </div>

    <div class="table-container">
        <h3>Category List</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th style="width: 160px;">Actions</th>
                </tr>
            </thead>
            <tbody id="categoryTableBody">
                <!-- dynamic rows -->
            </tbody>
        </table>
    </div>

    <script>
        let editMode = false;
        let editId = null;

        const token = localStorage.getItem('auth_token');
        if (!token) {
            window.location.href = '/login';
        }

        function fetchCategories() {
            axios.get('/api/category', {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }).then(res => {
                const categories = res.data;
                const tbody = document.getElementById('categoryTableBody');
                tbody.innerHTML = '';
                categories.forEach(cat => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${cat.name}</td>
                            <td class="actions">
                                <button onclick="editCategory(${cat.id}, '${cat.name}')">Edit</button>
                                <button class="delete-btn" onclick="deleteCategory(${cat.id})">Delete</button>
                            </td>
                        </tr>
                    `;
                });
            });
        }

        function submitCategory() {
            const name = document.getElementById('categoryName').value;
            document.getElementById('nameError').textContent = '';

            const url = editMode ? `/api/category/${editId}` : '/api/category';
            const method = editMode ? 'put' : 'post';

            axios({
                method: method,
                url: url,
                headers: {
                    Authorization: `Bearer ${token}`
                },
                data: { name }
            }).then(res => {
                document.getElementById('categoryName').value = '';
                document.getElementById('formTitle').textContent = 'Add Category';
                document.getElementById('submitBtn').textContent = 'Add';
                editMode = false;
                editId = null;
                fetchCategories();
            }).catch(err => {
                if (err.response?.status === 422) {
                    const errors = err.response.data.errors;
                    if (errors.name) {
                        document.getElementById('nameError').textContent = errors.name[0];
                    }
                }
            });
        }

        function editCategory(id, name) {
            editMode = true;
            editId = id;
            document.getElementById('categoryName').value = name;
            document.getElementById('formTitle').textContent = 'Edit Category';
            document.getElementById('submitBtn').textContent = 'Update';
        }

        function deleteCategory(id) {
            if (!confirm("Are you sure you want to delete this category?")) return;

            axios.delete(`/api/category/${id}`, {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }).then(res => {
                fetchCategories();
            });
        }

        window.onload = fetchCategories;
    </script>
</body>
</html>
