<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Lagarto Grow</title>
    <img src="../img/lagarto.jpg" alt="Lagarto Grow Logo" class="logo">
    <link rel="stylesheet" href="../css/seguimientopedido.css">
</head>
<style>
    .botongestionar {
        padding: 10px;
        background-color: #4caf50;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .botongestionar:hover {
        background-color: #45a049;
    }
</style>


<body>
    <div class="container">
        <aside class="sidebar">
            <div class="user-section">
                <span class="user-icon">👤</span>
                <p>Admin</p>
            </div>
            <nav class="menu">
                <ul>
                    <li><button onclick="location.href='Inventario.php'">📦 Inventario</button></li>
                    <li><button onclick="location.href='Proveedores.php'">🛒 Proveedores</button></li>
                    <li><button onclick="location.href='seguimiento.php'">📊 Seguimiento</button></li>
                    <li><button onclick="location.href='Boleta.php'">🧾 Historial ventas</button></li>
                </ul>
            </nav>
            <button class="logout-button" onclick="location.href='../login.php'">Cerrar Sesión</button>
            <button class="botongestionar" onclick="location.href='usuarios.php'">Gestionar Usuarios</button>
        </aside>


        <main class="main-content">
            <header class="header">
        
                <h1>Sistema Lagarto Grow</h1>
            </header>

            <div class="main-content">
                <div class="header">
                    <h1>Agregar Pedido</h1>
                </div>

                <form id="pedidoForm">
                    <label for="producto">Producto:</label>
                    <input type="text" id="producto" name="producto" required>

                    <label for="codigo_seguimiento">Código de Seguimiento:</label>
                    <input type="text" id="codigo_seguimiento" name="codigo_seguimiento" readonly>
                    <button type="button" id="generarCodigo">Generar Código</button>

                    <label for="estado">Estado del Pedido:</label>
                    <select id="estado" name="estado">
                        <option value="Pedido Confirmado">Pedido Confirmado</option>
                        <option value="Pedido en transito">Pedido en transito</option>
                        <option value="Entregado">Entregado</option>
                        <option value="Cancelado">Cancelado</option>
                    </select>

                    <button type="submit">Agregar Pedido</button>
                </form>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Código de Seguimiento</th>
                            <th>Fecha del Último Pedido</th>
                            <th>Producto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaPedidos">
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        function generarCodigoSeguimiento() {
            const codigo = 'PED-' + Math.random().toString(36).substr(2, 9).toUpperCase();
            document.getElementById('codigo_seguimiento').value = codigo;
        }

        document.getElementById('generarCodigo').addEventListener('click', generarCodigoSeguimiento);

        function cargarPedidos() {
            fetch('pedidos_seg.php')
                .then(response => response.json())
                .then(data => {
                    const tablaPedidos = document.getElementById('tablaPedidos');
                    tablaPedidos.innerHTML = '';

                    if (data.length === 0) {
                        tablaPedidos.innerHTML = '<tr><td colspan="5">No hay pedidos registrados.</td></tr>';
                    } else {
                        data.forEach(pedido => {
                            const nuevaFila = document.createElement('tr');
                            nuevaFila.innerHTML = `
                                <td>${pedido.codigo_seguimiento}</td>
                                <td>${pedido.fecha}</td>
                                <td>${pedido.nombre_producto}</td>
                                <td>${pedido.estado}</td>
                                <td>
                                    <button onclick="editarPedido(this)">Editar</button>
                                    <button onclick="eliminarPedido(this)">Eliminar</button>
                                </td>
                            `;
                            tablaPedidos.appendChild(nuevaFila);
                        });
                    }
                })
                .catch(error => console.error('Error al cargar los pedidos:', error));
        }

        document.addEventListener('DOMContentLoaded', cargarPedidos);

        let pedidoEditado = null;
        function editarPedido(button) {
            const fila = button.parentElement.parentElement;
            const codigo = fila.cells[0].textContent;
            const producto = fila.cells[2].textContent;
            const estado = fila.cells[3].textContent;

            document.getElementById('codigo_seguimiento').value = codigo;
            document.getElementById('producto').value = producto;
            document.getElementById('estado').value = estado;

            pedidoEditado = codigo;
            fila.remove();
        }

        // Eliminar pedido
        function eliminarPedido(button) {
            const fila = button.parentElement.parentElement;
            const codigo = fila.cells[0].textContent;

            fetch('pedidos_seg.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    'eliminar_codigo': codigo
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    fila.remove();
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error al eliminar el pedido:', error);
                alert('Hubo un error al eliminar el pedido.');
            });
        }
        document.getElementById('pedidoForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const codigo = document.getElementById('codigo_seguimiento').value;
            const estado = document.getElementById('estado').value;
            const producto = document.getElementById('producto').value;

            if (!codigo || !producto) {
                alert('Debe generar un código de seguimiento y escribir el nombre del producto antes de agregar el pedido.');
                return;
            }

            fetch('pedidos_seg.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    'codigo_seguimiento': codigo,
                    'estado': estado,
                    'producto': producto
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    cargarPedidos();
                    document.getElementById('pedidoForm').reset();
                    alert(data.message);
                    pedidoEditado = null;
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>

