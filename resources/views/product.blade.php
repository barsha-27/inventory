<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f9; padding: 2rem; }
        h1 { text-align: center; }
        .form-container, .table-container {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin: 1rem auto;
            max-width: 700px;
        }
        input, select { width: 100%; padding: 0.6rem; margin-bottom: 0.75rem; border: 1px solid #ccc; border-radius: 6px; }
        button {
            padding: 0.6rem 1.2rem;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-export {
            background-color: #2e86de;
            margin-right: 0.5rem;
        }
        .btn-pdf {
            background-color: #6c5ce7;
        }
        .export-buttons {
            max-width: 700px;
            margin: 1rem auto;
            text-align: right;
        }
        .filter-container {
            max-width: 700px;
            margin: 1rem auto;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 0.75rem; border-bottom: 1px solid #eee; text-align: left; }
        .actions button { margin-right: 0.5rem; background: #2196F3; }
        .delete-btn { background-color: #f44336; }
        .error { color: red; font-size: 0.9rem; }
    </style>
</head>
<body>
<h1>Manage Products</h1>

<div class="form-container">
    <h3 id="formTitle">Add Product</h3>
    <input type="text" id="productName" placeholder="Product name">
    <input type="number" id="productPrice" placeholder="Price">
    <input type="number" id="productQuantity" placeholder="Quantity">
    <select id="productCategory"></select>
    <div class="error" id="productError"></div>
    <button onclick="submitProduct()" id="submitBtn">Add</button>
</div>

<div class="filter-container">
    <label for="filterCategory">Filter by Category:</label>
    <select id="filterCategory" onchange="filterProductsByCategory()">
        <option value="">-- All Categories --</option>
    </select>
</div>

<div class="export-buttons">
    <button class="btn-export" onclick="exportCSV()">Export CSV</button>
    <button class="btn-pdf" onclick="exportPDF()">Export PDF</button>
</div>

<div class="table-container">
    <h3>Product List</h3>
    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Category</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody id="productTableBody"></tbody>
    </table>
</div>

<script>
    let editMode = false;
    let editId = null;
    const token = localStorage.getItem('auth_token');
    if (!token) window.location.href = '/login';

    function fetchCategories() {
        axios.get('/api/category', { headers: { Authorization: `Bearer ${token}` } })
            .then(res => {
                const categorySelect = document.getElementById('productCategory');
                const filterSelect = document.getElementById('filterCategory');
                categorySelect.innerHTML = '';
                filterSelect.innerHTML = '<option value="">-- All Categories --</option>';
                res.data.forEach(cat => {
                    categorySelect.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
                    filterSelect.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
                });
            });
    }

    function fetchProducts() {
        axios.get('/api/product', { headers: { Authorization: `Bearer ${token}` } })
            .then(res => renderProducts(res.data));
    }

    function filterProductsByCategory() {
        const categoryId = document.getElementById('filterCategory').value;
        const url = categoryId ? `/api/product/category/${categoryId}` : '/api/product';
        axios.get(url, { headers: { Authorization: `Bearer ${token}` } })
            .then(res => renderProducts(res.data));
    }

    function renderProducts(products) {
        const tbody = document.getElementById('productTableBody');
        tbody.innerHTML = '';
        products.forEach(p => {
            tbody.innerHTML += `
                <tr>
                    <td>${p.name}</td>
                    <td>${p.price}</td>
                    <td>${p.quantity}</td>
                    <td>${p.category ? p.category.name : 'N/A'}</td>
                    <td>
                        <button onclick="editProduct(${p.id}, '${p.name}', ${p.price}, ${p.quantity}, ${p.category_id})">Edit</button>
                        <button class="delete-btn" onclick="deleteProduct(${p.id})">Delete</button>
                    </td>
                </tr>
            `;
        });
    }

    function submitProduct() {
        const name = document.getElementById('productName').value;
        const price = document.getElementById('productPrice').value;
        const quantity = document.getElementById('productQuantity').value;
        const category_id = document.getElementById('productCategory').value;

        const url = editMode ? `/api/product/${editId}` : '/api/product';
        const method = editMode ? 'put' : 'post';

        axios({ method, url, headers: { Authorization: `Bearer ${token}` }, data: { name, price, quantity, category_id } })
            .then(() => {
                document.getElementById('productName').value = '';
                document.getElementById('productPrice').value = '';
                document.getElementById('productQuantity').value = '';
                editMode = false;
                editId = null;
                document.getElementById('submitBtn').textContent = 'Add';
                document.getElementById('formTitle').textContent = 'Add Product';
                fetchProducts();
            })
            .catch(err => {
                document.getElementById('productError').textContent = err.response?.data?.message || 'Error';
            });
    }

    function editProduct(id, name, price, quantity, category_id) {
        editMode = true;
        editId = id;
        document.getElementById('productName').value = name;
        document.getElementById('productPrice').value = price;
        document.getElementById('productQuantity').value = quantity;
        document.getElementById('productCategory').value = category_id;
        document.getElementById('submitBtn').textContent = 'Update';
        document.getElementById('formTitle').textContent = 'Edit Product';
    }

    function deleteProduct(id) {
        if (!confirm("Are you sure?")) return;
        axios.delete(`/api/product/${id}`, { headers: { Authorization: `Bearer ${token}` } })
            .then(() => fetchProducts());
    }

    function exportCSV() {
        axios.get('/api/product/export/csv', {
            headers: { Authorization: `Bearer ${token}` },
            responseType: 'blob'
        }).then(response => {
            const blob = new Blob([response.data], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'products.csv');
            document.body.appendChild(link);
            link.click();
            link.remove();
        }).catch(() => {
            alert('Failed to export CSV.');
        });
    }

    function exportPDF() {
        axios.get('/api/product/export/pdf', {
            headers: { Authorization: `Bearer ${token}` },
            responseType: 'blob'
        }).then(response => {
            const blob = new Blob([response.data], { type: 'application/pdf' });
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'products.pdf');
            document.body.appendChild(link);
            link.click();
            link.remove();
        }).catch(() => {
            alert('Failed to export PDF.');
        });
    }

    window.onload = () => {
        fetchCategories();
        fetchProducts();
    }
</script>
</body>
</html>
