<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lagartogrow_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cliente'])) {
    $cliente = $_POST['cliente'];
    $direccion = $_POST['direccion'];
    $metodo_pago = $_POST['metodo_pago'];
    $fecha_emision = $_POST['fecha_emision'];

    $productos = $_POST['codigo_producto'];
    $cantidades = $_POST['cantidad'];
    $precios_unitarios = $_POST['precio_unitario'];

    $numero_documento = "DOC-" . rand(100000, 999999);
    $precio_total = 0;

    foreach ($productos as $index => $codigo) {
        $subtotal = $cantidades[$index] * $precios_unitarios[$index];
        $precio_total += $subtotal;
    }

    $sql_boleta = "INSERT INTO boletas (cliente, numero_documento, direccion, metodo_pago, fecha_emision, precio_total)
                   VALUES ('$cliente', '$numero_documento', '$direccion', '$metodo_pago', '$fecha_emision', '$precio_total')";

    if ($conn->query($sql_boleta) === TRUE) {
        $boleta_id = $conn->insert_id;

        foreach ($productos as $index => $codigo) {
            $cantidad = $cantidades[$index];
            $precio = $precios_unitarios[$index];
            $subtotal = $cantidad * $precio;

            $sql_pedido = "INSERT INTO boleta_producto (boleta_id, producto_id, cantidad, precio, subtotal)
                           VALUES ('$boleta_id', 
                                   (SELECT id FROM producto WHERE codigo = '$codigo'),
                                   '$cantidad', '$precio', '$subtotal')";
            $conn->query($sql_pedido);

            $sql_stock = "UPDATE producto SET stock = stock - {$cantidad} WHERE codigo = '$codigo'";
            $conn->query($sql_stock);
        }

        $mensaje = "Boleta registrada correctamente.";
        header("Location: boleta.php");
        exit();
    } else {
        $mensaje = "Error al registrar la boleta: " . $conn->error;
    }
}

$registros_por_pagina = 10;

$pagina_actual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;

$offset = ($pagina_actual - 1) * $registros_por_pagina;

$where = "";

if (isset($_GET['buscar_cliente']) && $_GET['buscar_cliente'] !== '') {
    $cliente = $conn->real_escape_string($_GET['buscar_cliente']);
    $where .= " WHERE cliente LIKE '%$cliente%'";

    if (isset($_GET['buscar_documento']) && $_GET['buscar_documento'] !== '') {
        $documento = $conn->real_escape_string($_GET['buscar_documento']);
        $where .= " AND numero_documento LIKE '%$documento%'";
    }
}

$sql_total = "SELECT COUNT(*) as total FROM boletas $where";
$result_total = $conn->query($sql_total);
$total_registros = $result_total->fetch_assoc()['total'];

$total_paginas = ceil($total_registros / $registros_por_pagina);

$sql_boletas = "SELECT * FROM boletas $where ORDER BY fecha_emision DESC LIMIT $registros_por_pagina OFFSET $offset";
$result_boletas = $conn->query($sql_boletas);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Lagarto Grow - Historial ventas</title>
    <link rel="stylesheet" href="../css/boleta.css">
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
    <?php if (!empty($mensaje)): ?>
        <script>
            alert("<?php echo $mensaje; ?>");
        </script>
    <?php endif; ?>

    <div class="container">
        <aside class="sidebar">
            <div class="user-section">
                <span class="user-icon">👤</span>
                <p>Admin</p>
            </div>
            <nav class="menu">
                <ul>
                    <li><button onclick="location.href='inventario.php'">📦 Inventario</button></li>
                    <li><button onclick="location.href='proveedores.php'">🛒 Proveedores</button></li>
                    <li><button onclick="location.href='seguimiento.php'">📊 Seguimiento</button></li>
                    <li><button onclick="location.href='boleta.php'">🧾 Historial ventas</button></li>
                </ul>
            </nav>
            <button class="logout-button" onclick="location.href='../login.php'">Cerrar Sesión</button>
            <button class="botongestionar" onclick="location.href='usuarios.php'">Gestionar Usuarios</button>
        </aside>

        <main class="main-content">
            <header class="header">
                <img src="../img/lagarto.jpg" alt="Lagarto Grow Logo" class="logo">
                <h1>Sistema Lagarto Grow</h1>
            </header>
            <form action="boleta.php" method="POST">
                <h3>Datos del Cliente</h3>
                <input type="text" name="cliente" placeholder="Cliente" required>
                <input type="text" name="direccion" placeholder="Dirección" required>
                <input type="text" name="metodo_pago" placeholder="Método de pago" required>
                <input type="date" name="fecha_emision" required>

                <h3>Productos</h3>
                <div id="productos-container"></div>
                <button type="button" onclick="agregarProducto()">Agregar Producto</button>

                <p>Total Final: $<span id="precio_total">0.00</span></p>
                <button type="submit">Emitir</button>
            </form>

            <hr>

            <form method="GET" action="boleta.php">
                <input type="text" name="buscar_cliente" placeholder="Buscar por cliente" value="<?php echo isset($_GET['buscar_cliente']) ? $_GET['buscar_cliente'] : ''; ?>">
                <button type="submit">Buscar</button>
            </form>
            <?php if (isset($_GET['buscar_cliente']) && $_GET['buscar_cliente'] !== ''): ?>
                <form method="GET" action="boleta.php">
                    <input type="hidden" name="buscar_cliente" value="<?php echo $_GET['buscar_cliente']; ?>">
                    <input type="text" name="buscar_documento" placeholder="Filtrar por número de documento" value="<?php echo isset($_GET['buscar_documento']) ? $_GET['buscar_documento'] : ''; ?>">
                    <button type="submit">Filtrar</button>
                </form>
            <?php endif; ?>
            <h3>Historial de Ventas</h3>
            <?php if ($result_boletas->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Documento</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($boleta = $result_boletas->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $boleta['cliente']; ?></td>
                                <td><?php echo $boleta['numero_documento']; ?></td>
                                <td><?php echo $boleta['fecha_emision']; ?></td>
                                <td>$<?php echo $boleta['precio_total']; ?></td>
                                <td>
                                    <a href="detalle_boleta.php?numero_documento=<?php echo $boleta['numero_documento']; ?>">Ver Detalles</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay boletas registradas.</p>
            <?php endif; ?>

            <div class="pagination">
                <?php if ($pagina_actual > 1): ?>
                    <a href="boleta.php?page=<?php echo $pagina_actual - 1; ?>&buscar_cliente=<?php echo urlencode($_GET['buscar_cliente'] ?? ''); ?>">Anterior</a>
                <?php endif; ?>

                <span>Página <?php echo $pagina_actual; ?> de <?php echo $total_paginas; ?></span>

                <?php if ($pagina_actual < $total_paginas): ?>
                    <a href="boleta.php?page=<?php echo $pagina_actual + 1; ?>&buscar_cliente=<?php echo urlencode($_GET['buscar_cliente'] ?? ''); ?>">Siguiente</a>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        function agregarProducto() {
            const container = document.getElementById('productos-container');
            const div = document.createElement('div');
            div.innerHTML = `
                <input type="text" name="codigo_producto[]" placeholder="Código del producto" required>
                <input type="number" name="cantidad[]" placeholder="Cantidad" min="1" required>
                <input type="number" step="0.01" name="precio_unitario[]" placeholder="Precio Unitario" required>
            `;
            container.appendChild(div);
        }
    </script>
</body>

</html>
