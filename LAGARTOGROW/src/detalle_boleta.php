<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lagartogrow_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el número de documento de la URL
if (isset($_GET['numero_documento'])) {
    $numero_documento = $_GET['numero_documento'];

    // Obtener los detalles de la boleta
    $sql_boleta = "SELECT * FROM boletas WHERE numero_documento = ?";
    $stmt_boleta = $conn->prepare($sql_boleta);
    $stmt_boleta->bind_param("s", $numero_documento);
    $stmt_boleta->execute();
    $result_boleta = $stmt_boleta->get_result();

    if ($result_boleta->num_rows > 0) {
        $boleta = $result_boleta->fetch_assoc();
        $boleta_id = $boleta['id']; // Asegúrate de tener el campo 'id' en la tabla 'boletas'

        // Obtener los productos vendidos
        $sql_productos = "
        SELECT p.codigo, bp.cantidad, bp.precio, bp.subtotal
        FROM boleta_producto bp
        JOIN producto p ON bp.producto_id = p.id
        WHERE bp.boleta_id = ?";
        $stmt_productos = $conn->prepare($sql_productos);
        $stmt_productos->bind_param("i", $boleta_id);
        $stmt_productos->execute();
        $result_productos = $stmt_productos->get_result();
    } else {
        echo "No se encontró ninguna boleta con el número de documento: " . $numero_documento . "<br>";
    }
} else {
    echo "Número de documento no proporcionado.<br>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle Boleta - Sistema Lagarto Grow</title>
    <link rel="stylesheet" href="../css/detalleboleta.css">
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
                <p>Sebastian Admin</p>
            </div>
            <nav class="menu">
                <ul>
                    <li><button onclick="location.href='inventario.php'">📦 Inventario</button></li>
                    <li><button onclick="location.href='proveedores.php'">🛒 Proveedores</button></li>
                    <li><button onclick="location.href='seguimiento.php'">📊 Seguimiento</button></li>
                    <li><button onclick="location.href='boleta.php'">🧾 Historial ventas</button></li>
                </ul>
            </nav>
            <button class="logout-button" onclick="location.href='../login.html'">Cerrar Sesión</button>
            <button class="botongestionar" onclick="location.href='usuarios.php'">Gestionar Usuarios</button>

        </aside>

        <main class="main-content">
            <header class="header">
                <img src="../img/lagarto.jpg" alt="Lagarto Grow Logo" class="logo">
                <h1>Detalle de Boleta</h1>
            </header>

            <?php if (isset($boleta)): ?>
                <h3>Información de la Boleta</h3>
                <table>
                    <tr>
                        <th>Cliente</th>
                        <td><?php echo $boleta['cliente']; ?></td>
                    </tr>
                    <tr>
                        <th>Documento</th>
                        <td><?php echo $boleta['numero_documento']; ?></td>
                    </tr>
                    <tr>
                        <th>Dirección</th>
                        <td><?php echo $boleta['direccion']; ?></td>
                    </tr>
                    <tr>
                        <th>Método de Pago</th>
                        <td><?php echo $boleta['metodo_pago']; ?></td>
                    </tr>
                    <tr>
                        <th>Fecha de Emisión</th>
                        <td><?php echo $boleta['fecha_emision']; ?></td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <td>$<?php echo $boleta['precio_total']; ?></td>
                    </tr>
                </table>

                <h3>Productos Vendidos</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Código Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($result_productos) && $result_productos->num_rows > 0): ?>
                            <?php while ($producto = $result_productos->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $producto['codigo']; ?></td>
                                    <td><?php echo $producto['cantidad']; ?></td>
                                    <td>$<?php echo $producto['precio']; ?></td>
                                    <td>$<?php echo $producto['subtotal']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No se encontraron productos vendidos para esta boleta.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No se encontró la boleta con el número de documento: <?php echo $numero_documento; ?></p>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>
