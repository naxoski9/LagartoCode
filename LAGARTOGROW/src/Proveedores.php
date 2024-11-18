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
    <title>Sistema Lagarto Grow</title>
    <link rel="stylesheet" href="../css/seguimiento.css">
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
</head>

<body>
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
            <button class="botongestionar" onclick="location.href='usuarios.php'">Gestionar Usuarios</button>

        </aside>

        <main class="main-content">
            <header class="header"><img src="../img/lagarto.jpg" alt="Lagarto Grow Logo" class="logo">

                <h1>Sistema Lagarto Grow</h1>
            </header>
            <div class="search-container">
                <input type="text" placeholder="Buscar">
                <button>Buscar</button>
            </div>
            <div class="button-group">
                <button onclick="location.href='Agregar_Proveedor.php'">Agregar Proveedor</button>
                <button onclick="location.href='Editar_Proveedor.php'">Editar</button>
                <button onclick="location.href='Eliminar_Proveedor.php'">Eliminar</button>
            </div>

            <div class="product-list">
                <?php
                $host = 'localhost';
                $db = 'lagartogrow_db'; 
                $user = 'root';
                $password = ''; 
                $conn = new mysqli($host, $user, $password, $db);
                
                
                if ($conn->connect_error) {
                    die("Error de conexión: " . $conn->connect_error);
                }

                $sql = "SELECT id, nombre, email, telefono, direccion FROM proveedores";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="card">';
                        echo '<h2>' . $row["nombre"] . '</h2>';
                        echo '<p><strong>Id:</strong> ' . $row["id"] . '</p>';
                        echo '<p><strong>Email:</strong> ' . $row["email"] . '</p>';
                        echo '<p><strong>Direccion:</strong> ' . $row["direccion"] . '</p>';
                        echo '<p><strong>Telefono:</strong> ' . $row["telefono"] . '</p>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No hay proveedores registrados.</p>';
                }

                $conn->close();
                ?>
            </div>
        </main>
    </div>
</body>

</html>