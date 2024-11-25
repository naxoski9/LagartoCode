<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
        }
        form, table {
            width: 100%;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        table {
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Gestión de Usuarios</h2>
        <form id="usuarioForm">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="contraseña">Contraseña:</label>
            <input type="password" id="contraseña" name="contraseña" required>

            <button type="submit">Agregar Usuario</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Fecha de Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaUsuarios">
            </tbody>
        </table>
    </div>

    <script>
        function cargarUsuarios() {
            fetch('gestionar_usuarios.php')
                .then(response => response.json())
                .then(data => {
                    const tablaUsuarios = document.getElementById('tablaUsuarios');
                    tablaUsuarios.innerHTML = '';

                    data.forEach(usuario => {
                        const fila = document.createElement('tr');
                        fila.innerHTML = `
                            <td>${usuario.id}</td>
                            <td>${usuario.nombre}</td>
                            <td>${usuario.email}</td>
                            <td>${usuario.fecha_creacion}</td>
                            <td><button onclick="eliminarUsuario(${usuario.id})">Eliminar</button></td>
                        `;
                        tablaUsuarios.appendChild(fila);
                    });
                })
                .catch(error => console.error('Error al cargar usuarios:', error));
        }

        document.getElementById('usuarioForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const nombre = document.getElementById('nombre').value;
            const email = document.getElementById('email').value;
            const contraseña = document.getElementById('contraseña').value;

            fetch('gestionar_usuarios.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ nombre, email, contraseña })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    cargarUsuarios();
                    document.getElementById('usuarioForm').reset();
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error al agregar usuario:', error));
        });

        function eliminarUsuario(id) {
            fetch('gestionar_usuarios.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ eliminar_id: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    cargarUsuarios();
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error al eliminar usuario:', error));
        }

        document.addEventListener('DOMContentLoaded', cargarUsuarios);
    </script>
</body>
</html>
