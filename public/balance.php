<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Месячный баланс пользователя</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            max-width: 600px;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .negative {
            color: red;
        }
    </style>
</head>
<body>
<h1>Месячный баланс пользователя</h1>

<select id="userSelect">
    <option value="">Выберите пользователя</option>
</select>

<table id="balanceTable">
    <thead>
    <tr>
        <th>Месяц</th>
        <th>Баланс</th>
    </tr>
    </thead>
    <tbody></tbody>
</table>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        loadUsers();

        document.getElementById('userSelect').addEventListener('change', function () {
            const userId = this.value;
            if (userId) {
                loadBalance(userId);
            } else {
                clearTable();
            }
        });
    });

    function loadUsers() {
        fetch('/api/users')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const select = document.getElementById('userSelect');
                    data.data.forEach(user => {
                        const option = document.createElement('option');
                        option.value = user.id;
                        option.textContent = user.name;
                        select.appendChild(option);
                    });
                }
            })
            .catch(error => console.error('Ошибка:', error));
    }

    function loadBalance(userId) {
        fetch(`/api/balance/${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateTable(data.data);
                }
            })
            .catch(error => console.error('Ошибка:', error));
    }

    function updateTable(data) {
        const tbody = document.querySelector('#balanceTable tbody');
        tbody.innerHTML = '';

        data.forEach(row => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                    <td>${formatMonth(row.month)}</td>
                    <td class="${row.balance < 0 ? 'negative' : ''}">${formatMoney(row.balance)}</td>
                `;
            tbody.appendChild(tr);
        });
    }

    function clearTable() {
        document.querySelector('#balanceTable tbody').innerHTML = '';
    }

    function formatMonth(monthStr) {
        const [year, month] = monthStr.split('-');
        return new Date(year, month - 1).toLocaleString('ru', {year: 'numeric', month: 'long'});
    }

    function formatMoney(amount) {
        return new Intl.NumberFormat('us-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    }
</script>
</body>
</html>