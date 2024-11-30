<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rango'] === 'proveedor') {
    echo '<script type="text/javascript">
            alert("Acceso denegado. No tienes permisos para acceder a esta página.");
            window.history.back();
          </script>';
    exit(); 
}

$host = 'localhost';
$user = 'feriasof1_grupo3';
$password = 'RY9jaepMgPmTP6gEQKbM';
$dbname = 'feriasof1_grupo3';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$sql = "SELECT id, nombre, email, telefono, direccion FROM proveedores";
$result = $conn->query($sql);

$proveedores = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $proveedores[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Lagarto Grow</title>
    <link rel="stylesheet" href="../css/seguimiento.css">
    <script>
        function filtrarProveedores() {
            const searchTerm = document.getElementById('search').value.toLowerCase();
            const proveedores = document.querySelectorAll('.card');
            
            proveedores.forEach((proveedor) => {
                const nombre = proveedor.querySelector('.nombre').textContent.toLowerCase();
                const email = proveedor.querySelector('.email').textContent.toLowerCase();
                const direccion = proveedor.querySelector('.direccion').textContent.toLowerCase();
                
                if (nombre.includes(searchTerm) || email.includes(searchTerm) || direccion.includes(searchTerm)) {
                    proveedor.style.display = '';
                } else {
                    proveedor.style.display = 'none';
                }
            });
        }
    </script>
</head>

<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    display: flex;
    height: 100vh;
    overflow: hidden;
}

.container {
    display: flex;
    width: 100%;
}

.sidebar {
    background-color: #f4f4f4;
    width: 200px;
    padding: 15px;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    z-index: 1000;
}

.main-content {
    flex-grow: 1;
    background-color: #f9f9f9;
    padding: 20px;
    display: flex;
    flex-direction: column;
    margin-left: 200px;
    height: 100vh;
    overflow-y: auto;
    padding-top: 80px;
}

.header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    margin-left: 25px;
}

.search-container {
    display: flex;
    margin-bottom: 20px;
    margin-left: 25px;
}

.search-container input {
    flex-grow: 1;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-right: 10px;
}

.search-container button {
    padding: 10px;
    background-color: #4caf50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.search-container button:hover {
    background-color: #45a049;
}

.button-group {
    margin-bottom: 20px;
    display: flex;
    justify-content: flex-start;
    gap: 10px;
    margin-left: 25px;
}

.button-group button {
    padding: 10px;
    background-color: #2196F3;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.button-group button:hover {
    background-color: #1976D2;
}

.card-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-left: 25px;
    margin-bottom: 40px;
    padding-right: 25px;
    max-height: calc(100vh - 180px);
}

.card {
    padding: 8px;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    margin-bottom: 10px;
    height: 180px;
}

.card h2 {
    margin: 0;
}

.logo {
    width: 100px;
    height: auto;
    position: fixed;
    top: 10px;
    right: 10px;
    z-index: 1000;
}

.button-Cancelar, .button-Emitir, .button-Borrar {
    margin-bottom: 20px;
    text-align: left;
    margin-left: 25px;
}

.button-Cancelar button,
.button-Emitir button,
.button-Borrar button {
    padding: 10px;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-right: 10px;
}

.button-Cancelar button {
    background-color: red;
}

.button-Cancelar button:hover {
    background-color: #d21922;
}

.button-Emitir button {
    background-color: rgb(0, 255, 47);
}

.button-Emitir button:hover {
    background-color: #19d244;
}

.button-Borrar button {
    background-color: red;
}

.button-Borrar button:hover {
    background-color: #d21922;
}

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

.product-list .card {
    border: 1px solid #ccc;
    padding: 15px;
    margin: 10px 0;
    border-radius: 5px;
    background-color: #f9f9f9;
    margin-left: 25px;
}

</style>

<body>
<div class="container">
        <aside class="sidebar">
            <div class="user-section">
                <span class="user-icon">👤</span>
                <p><?php echo $_SESSION['usuario_rango'];?></p>
            </div>
            
            <nav class="menu">
                <ul>
                    <li><button onclick="location.href='Inventario.php'">📦 Inventario</button></li>
                    <li><button onclick="location.href='Proveedores.php'">🛒 Proveedores</button></li>
                    <li><button onclick="location.href='Seguimiento.php'">📊 Seguimiento</button></li>
                    <li><button onclick="location.href='Boleta.php'">🧾 Historial ventas</button></li>
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
                <input type="text" id="search" placeholder="Buscar por nombre, email o dirección..." oninput="filtrarProveedores()">
            </div>

            <div class="button-group">
                <button onclick="location.href='Agregar_Proveedor.php'">Agregar Proveedor</button>
                <button onclick="location.href='editar_proveedor.php'">Editar</button>
                <button onclick="location.href='Eliminar_Proveedor.php'">Eliminar</button>
            </div>

            <div class="product-list">
                <?php
                foreach ($proveedores as $proveedor) {
                    echo '<div class="card">';
                    echo '<h2 class="nombre">' . $proveedor["nombre"] . '</h2>';
                    echo '<p><strong>Id:</strong> ' . $proveedor["id"] . '</p>';
                    echo '<p class="email"><strong>Email:</strong> ' . $proveedor["email"] . '</p>';
                    echo '<p class="direccion"><strong>Direccion:</strong> ' . $proveedor["direccion"] . '</p>';
                    echo '<p><strong>Telefono:</strong> ' . $proveedor["telefono"] . '</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </main>
    </div>
</body>

</html>
