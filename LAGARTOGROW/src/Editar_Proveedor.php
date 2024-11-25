<?php

$host = 'localhost';
$db = 'lagartogrow_db'; 
$user = 'root';
$password = ''; 

$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$proveedor = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id']; 

    $stmt = $conn->prepare("SELECT * FROM proveedores WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $proveedor = $result->fetch_assoc();
    } else {
        die("Proveedor no encontrado.");
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre_proveedor']) && $proveedor !== null) {

    $id = $proveedor['id'];
    $nombre_proveedor = $_POST['nombre_proveedor'];
    $email = $_POST['email'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];

    $stmt = $conn->prepare("UPDATE proveedores SET nombre=?, email=?, direccion=?, telefono=? WHERE id=?");
    $stmt->bind_param("ssssi", $nombre_proveedor, $email, $direccion, $telefono, $id);
    if ($stmt->execute()) {
        echo "<p>Proveedor actualizado correctamente.</p>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/editar.css">
    <header class="header">
        <img src="../img/lagarto.jpg" alt="Lagarto Grow Logo" class="logo">
        <title>Editar Proveedor</title>
        <style>
            .button-s {
                background-color: #ddd;
                color: black;
                border: none;
                padding: 10px 20px;
                font-size: 16px;
                cursor: pointer;
                margin-top: 20px;
                display: block;
                width: 218px;
                margin-left: auto;
                margin-right: auto;
            }

            .button-s:hover {
                background-color: #bbb;
            }

            .container {
                display: flex;
                justify-content: center;
                align-items: flex-start;
                min-height: 100vh;
                margin-top: 100px;
            }

            .logo {
                width: 100px;
                height: auto;
                position: absolute;
                top: 10px;
                right: 10px;
            }
        </style>
</head>

<body>

    <div class="container">
        <div class="main-content">
            <div class="search-bar-container">
                <h1>Editar Proveedor</h1>
                <div class="search-bar">
                    <form action="" method="POST">
                        <input type="number" id="id" name="id" placeholder="Buscar proveedor por ID..." required>
                        <button type="submit">Buscar</button>
                    </form>
                </div>
            </div>

            <?php if ($proveedor !== null): ?>
            <form action="" method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($proveedor['id']); ?>">

                <label for="nombre">Nombre del proveedor:</label>
                <input type="text" id="nombre" name="nombre_proveedor"
                    value="<?php echo htmlspecialchars($proveedor['nombre']); ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" 
                    value="<?php echo htmlspecialchars($proveedor['email']); ?>" required>

                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" 
                    value="<?php echo htmlspecialchars($proveedor['telefono']); ?>" required>

                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion" 
                    value="<?php echo htmlspecialchars($proveedor['direccion']); ?>" required>

                <button type="submit">Actualizar Proveedor</button>
            </form>
            <?php endif; ?>

            <div>
                <button class="button-s" onclick="location.href='Proveedores.php'">Volver a Proveedores</button>
            </div>

        </div>
    </div>
</body>

</html>
