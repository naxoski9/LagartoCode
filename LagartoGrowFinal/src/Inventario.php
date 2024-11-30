<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rango'] === 'proveedor') {
    echo '<script type="text/javascript">
            alert("Acceso denegado. No tienes permisos para acceder a esta página.");
            window.history.back();
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
    <link rel="stylesheet" href="../css/inventario.css">
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
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    z-index: 1000;
}

.sidebar-buttons {
    margin-top: auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.sidebar .user-section {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.sidebar .user-section .user-icon {
    font-size: 24px;
    margin-right: 10px;
}

.sidebar nav {
    margin-bottom: auto;
}

.sidebar nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar nav ul li {
    margin: 10px 0;
}

.sidebar nav ul li button {
    width: 100%;
    padding: 10px;
    border: none;
    background-color: #e0e0e0;
    cursor: pointer;
    text-align: left;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.sidebar nav ul li button:hover {
    background-color: #d0d0d0;
}

.logout-button {
    width: 100%;
    padding: 10px;
    background-color: #ff4f4f;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    text-align: center;
    font-size: 16px;
}

.manage-users-button {
    width: 100%;
    padding: 10px;
    background-color: #4caf50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-align: center;
    margin-top: 10px;
}

.manage-users-button:hover {
    background-color: #45a049;
}

.main-content {
    flex-grow: 1;
    background-color: #f9f9f9;
    padding: 20px;
    display: flex;
    flex-direction: column;
    margin-left: 200px;
    overflow-y: auto;
    height: calc(100vh - 60px);
    margin-top: 60px;
}

.header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    position: fixed;
    top: 0;
    left: 200px;
    right: 0;
    z-index: 999;
    background-color: white;
    padding: 10px;
    margin-left: 40px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    height: 60px;
}

.header h1 {
    margin: 0;
}

.search-container {
    display: flex;
    margin-bottom: 20px;
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
    display: flex;
    justify-content: flex;
    width: 100%;
    gap: 10px;
    margin-bottom: 20px;
    margin-left: 25px;
    margin-top: 10px;
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
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.card {
    flex: 1 0 200px;
    padding: 10px;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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
}

.button-Cancelar, .button-Emitir, .button-Borrar {
    margin-bottom: 20px;
    text-align: left;
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
.product-list {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
    justify-content: start;
    margin-left: 25px;
}

.card {
    border: 1px solid #ddd;
    padding: 20px;
    border-radius: 10px;
    width: 250px;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    background-color: white;
}

.card img {
    width: 150px;
    height: 150px;
    object-fit: cover;
    margin-bottom: 15px;
}

.card h2 {
    font-size: 22px;
    margin-bottom: 10px;
}

.card p {
    font-size: 14px;
    margin-bottom: 5px;
}

.button-group button {
    margin-right: 10px;
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
    </style>
    <script>
        function sortProductsByPrice() {
            const productList = document.getElementById("productList");
            const cards = Array.from(productList.getElementsByClassName("card"));

            cards.sort((a, b) => {
                const priceA = parseFloat(a.getAttribute("data-price"));
                const priceB = parseFloat(b.getAttribute("data-price"));
                return priceB - priceA;
            });

            cards.forEach(card => productList.appendChild(card));
        }
    </script>
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <div class="user-section">
                <span class="user-icon">👤</span>
                <p>jefe</p>
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
            <header class="header">
                <img src="../img/lagarto.jpg" alt="Lagarto Grow Logo" class="logo">
                <h1>Sistema Lagarto Grow</h1>
            </header>

            <div class="button-group">
                <button onclick="location.href='Agregar_Producto.php'">Agregar producto</button>
                <button onclick="location.href='Editar_Producto.php'">Editar</button>
                <button onclick="location.href='eliminar_producto.php'">Eliminar</button>
                <button onclick="location.href='Ofertas.php'">Ofertas</button>
                <button onclick="sortProductsByPrice()">Ordenar por precio (Mayor a menor)</button>
            </div>

            <div class="product-list" id="productList">
                <?php
                function formatPrice($price) {
                    return '$' . number_format((float)$price, 0, ',', '.');
                }

                function createCard($row) {
                    $card = '<div class="card" data-name="' . strtolower($row["nombre_producto"]) . '" data-price="' . $row["precio"] . '">';
                    $card .= '<h2>' . $row["nombre_producto"] . '</h2>';
                    $card .= '<p><strong>Código:</strong> ' . $row["codigo"] . '</p>';
                    $card .= '<p><strong>Stock:</strong> ' . $row["stock"] . '</p>';
                    $card .= '<p><strong>Precio de venta:</strong> ' . formatPrice($row["precio"]) . '</p>';
                    $card .= '<p><strong>Estado:</strong> ' . $row["estado"] . '</p>';
                    $card .= '<p><strong>Proveedor:</strong> ' . $row["nombre_proveedor"] . '</p>';
                    $card .= '<p><strong>Descripcion:</strong> ' . $row["descripcion"] . '</p>';

                    if (!empty($row["imagen_url"])) {
                        $card .= '<img src="' . $row["imagen_url"] . '" alt="' . $row["nombre_producto"] . '">';
                    } else {
                        $card .= '<p>Sin imagen disponible</p>';
                    }

                    $card .= '</div>';
                    return $card;
                }

                $host = 'localhost';
                $user = 'feriasof1_grupo3';
                $password = 'RY9jaepMgPmTP6gEQKbM';
                $dbname = 'feriasof1_grupo3';

                $conn = new mysqli($host, $user, $password, $dbname);

                if ($conn->connect_error) {
                    die("Error de conexión: " . $conn->connect_error);
                }

                $deleteZeroStockQuery = "DELETE FROM producto WHERE stock = 0";
                if (!$conn->query($deleteZeroStockQuery)) {
                    echo '<p>Error al eliminar productos sin stock: ' . $conn->error . '</p>';
                }

                $sql = "
                    SELECT p.stock, p.descripcion, p.codigo, p.precio, p.nombre_producto, p.estado, p.imagen_url, pr.nombre AS nombre_proveedor 
                    FROM producto p 
                    JOIN proveedores pr ON p.proveedor_id = pr.id
                ";
                $result = $conn->query($sql);
                $products = [];

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $products[] = $row;
                    }
                } else {
                    echo '<p>No hay productos disponibles.</p>';
                }

                foreach ($products as $row) {
                    echo createCard($row);
                }

                $conn->close();
                ?>
            </div>
        </main>
    </div>
</body>

</html>
