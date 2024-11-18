<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rango'] === 'proveedor') {
    echo '<script type="text/javascript">
            alert("Acceso denegado. No tienes permisos para acceder a esta página.");
            window.history.back(); // Esto hace que el usuario vuelva a la página anterior.
          </script>';
    exit(); 
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historia de compra</title>
    <link rel="stylesheet" href="../css/seguimiento.css">
    <script>
    function mostrarMensaje(mensaje) {
        alert(mensaje);
    }
    </script>
</head>

<body>

    <?php
$mensaje = "";

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lagartogrow_db";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cliente'])) {
    $cliente = $_POST['cliente'];
    $numero_documento = $_POST['nmro_documento'];
    $direccion = $_POST['direccion'];
    $metodo_pago = $_POST['metodo_pago'];
    $fecha_emision = $_POST['fecha_emision'];
    $precio_total = $_POST['precio_total'];

    $sql = "INSERT INTO boletas (cliente, numero_documento, direccion, metodo_pago, fecha_emision, precio_total) 
            VALUES ('$cliente', '$numero_documento', '$direccion', '$metodo_pago', '$fecha_emision', '$precio_total')";

    if ($conn->query($sql) === TRUE) {
        $mensaje = "Boleta registrada con éxito.";
    } else {
        $mensaje = "Error al registrar la boleta: " . $conn->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['numero_documento_eliminar'])) {
    $numero_documento = $_POST['numero_documento_eliminar'];

    $sql = "DELETE FROM boletas WHERE numero_documento = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $numero_documento);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $mensaje = "Boleta eliminada con éxito.";
            } else {
                $mensaje = "No se encontró ninguna boleta con ese número de documento.";
            }
        } else {
            $mensaje = "Error al eliminar la boleta: " . $stmt->error;
        }

        
        $stmt->close();
    } else {
        $mensaje = "Error al preparar la consulta: " . $conn->error;
    }
}


$conn->close();
?>


    <?php if (!empty($mensaje)): ?>
    <script>
    mostrarMensaje("<?php echo $mensaje; ?>");
    </script>
    <?php endif; ?>

    <div class="container">
        <aside class="sidebar">
            <div class="user-section">
                <span class="user-icon">👤</span>
                <p><?php echo $_SESSION['usuario_nombre'];?></p>
            </div>

            <nav class="menu">
                <ul>
                    <li><button onclick="location.href='inventario.php'">📦 Inventario</button></li>
                    <li><button onclick="location.href='proveedores.php'">🛒 Proveedores</button></li>
                    <li><button onclick="location.href='seguimiento.php'">📊 Seguimiento</button></li>
                    <li><button onclick="location.href='boleta.php'">🧾 Historial de compra</button></li>
                </ul>
            </nav>
            <button class="logout-button" onclick="location.href='../login.php'">Cerrar Sesión</button>
            <button class="manage-users-button" onclick="location.href='usuarios.php'">Gestionar Usuarios</button>
        </aside>

        <main class="main-content">
            <header class="header">
                <img src="../img/lagarto.jpg" alt="Lagarto Grow Logo" class="logo">
                <h1>Historial De Compra</h1>
            </header>

            <div class="search-container">
                <input type="text" placeholder="Buscar">
                <button>Buscar</button>
            </div>
            <style>
            .botongestionar {
                width: 100%;
                padding: 12px;
                background-color: #4caf50;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-weight: bold;
                text-align: center;
                font-size: 14px;
                transition: background-color 0.3s ease;
            }

            .botongestionar:hover {
                background-color: #45a049;
            }
            </style>
            <form action="boleta.php" method="POST">
                <div class="button-group">
                    <h3>Datos del Cliente</h3>
                    <input type="text" name="cliente" placeholder="Cliente" required>
                    <input type="text" name="nmro_documento" placeholder="Nmr. documento" required>
                    <input type="text" name="direccion" placeholder="Dirección" required>
                    <input type="text" name="metodo_pago" placeholder="Método de pago" required>
                    <input type="date" name="fecha_emision" required>

                    <h3>Datos del Producto</h3>
                    <input type="text" name="codigo_producto" placeholder="Código del Producto" required>
                    <input type="number" name="cantidad" placeholder="Cantidad" required>
                    <input type="text" name="precio_total" placeholder="Precio Total" required>

                    <button type="submit" class="emitir-button">Emitir</button>
                    <button type="button" class="cancel-button" onclick="location.href='boleta.html'">Cancelar</button>
                </div>
            </form>

            <div class="button-Borrar">
                <form action="boleta.php" method="POST">
                    <input type="text" name="numero_documento_eliminar" placeholder="Número de documento a borrar"
                        required>
                    <button type="submit">Borrar</button>
                </form>
            </div>

        </main>
    </div>

</body>

</html>