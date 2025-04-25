<?php
session_start();
require_once('../conex/conex.php');
include ('../includes/validarsesion.php');
include ('../includes/validarlicencia.php');
$conex = new Database;
$con = $conex->conectar();

// Verificar si el administrador está logueado y obtener su id_empresa
if (!isset($_SESSION['id_empresa'])) {
    header("Location: ../index.php"); // Redirige a la página de inicio de sesión
    exit();
}
$id_empresa_admin = $_SESSION['id_empresa']; // id_empresa del administrador logueado
$nombre_admin = $_SESSION['nombre']; // Nombre del administrador logueado

// Obtener el nombre de la empresa del administrador logueado
$sql_empresa = $con->prepare("SELECT nom_empresa FROM empresa WHERE id_empresa = :id_empresa");
$sql_empresa->bindParam(':id_empresa', $id_empresa_admin);
$sql_empresa->execute();
$empresa_admin = $sql_empresa->fetch(PDO::FETCH_ASSOC)['nom_empresa'];

// Consulta para obtener los roles disponibles para los usuarios
$sql_roles = $con->prepare("SELECT id_roles, nom_roles FROM roles WHERE id_roles = 3"); // Solo mostrar el rol de usuario
$sql_roles->execute();
$roles = $sql_roles->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener los usuarios con rol id_roles = 3 creados por el administrador logueado
$sql_users = $con->prepare("SELECT u.documento, u.nombre, u.correo, r.nom_roles, u.fecha_registro 
                            FROM usuarios u 
                            INNER JOIN roles r ON u.id_roles = r.id_roles 
                            WHERE u.id_empresa = :id_empresa AND u.id_roles = 3"); // Filtrar por id_roles = 3
$sql_users->bindParam(':id_empresa', $id_empresa_admin);
$sql_users->execute();
$users = $sql_users->fetchAll(PDO::FETCH_ASSOC);

// Procesar el formulario de creación de usuarios
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_usuario'])) {
    $documento = $_POST['documento'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_BCRYPT); // Hashear la contraseña
    $id_roles = $_POST['id_roles'];
    $fecha_registro = date('Y-m-d H:i:s'); // Fecha y hora actual

    // Insertar el nuevo usuario en la base de datos
    $sql_insert = $con->prepare("INSERT INTO usuarios (documento, nombre, correo, contrasena, id_roles, id_empresa, fecha_registro) 
                                 VALUES (:documento, :nombre, :correo, :contrasena, :id_roles, :id_empresa, :fecha_registro)");
    $sql_insert->bindParam(':documento', $documento);
    $sql_insert->bindParam(':nombre', $nombre);
    $sql_insert->bindParam(':correo', $correo);
    $sql_insert->bindParam(':contrasena', $contrasena);
    $sql_insert->bindParam(':id_roles', $id_roles);
    $sql_insert->bindParam(':id_empresa', $id_empresa_admin); // Usar el id_empresa del administrador logueado
    $sql_insert->bindParam(':fecha_registro', $fecha_registro);
    $sql_insert->execute();

    // Redirigir para evitar reenvío del formulario
    header("Location: index-admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Crear Usuarios</title>
    <link rel="stylesheet" href="../css/login.css">
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            background: url('../img/fondo.png') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(0, 0, 0, 0.8);
            padding: 10px 20px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }

        .header button {
            background: rgba(107, 255, 3, 0.2);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .header button:hover {
            background: rgba(107, 255, 3, 0.5);
        }

        .logout-btn {
            background: rgba(255, 0, 0, 0.7);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .logout-btn:hover {
            background: rgba(255, 0, 0, 0.9);
        }

        .saludo {
            margin: 20px 0;
            background: rgba(0, 0, 0, 0.8);
            padding: 10px;
            border-radius: 5px;
            color: #fff;
            font-size: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: rgba(0, 0, 0, 0.8);
            color: #fff;
        }

        table th, table td {
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 10px;
            text-align: center;
        }

        table th {
            background: rgba(107, 255, 3, 0.3);
        }

        /* Modal styles */
        #crear-usuario-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.9);
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            z-index: 1000;
        }

        #crear-usuario-modal input, #crear-usuario-modal select, #crear-usuario-modal button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
        }

        #crear-usuario-modal button {
            background: rgba(107, 255, 3, 0.2);
            color: #fff;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        #crear-usuario-modal button:hover {
            background: rgba(107, 255, 3, 0.5);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin - Crear Usuarios</h1>
        <button id="open-modal">Crear Usuario</button>
        <form action="../includes/salir.php" method="POST" style="display: inline;">
            <button type="submit" class="logout-btn">Cerrar Sesión</button>
        </form>
    </div>

    <!-- Saludo al administrador -->
    <div class="saludo">
        <p>Hola, <strong><?= htmlspecialchars($nombre_admin) ?></strong>. Estás gestionando usuarios para la empresa <strong><?= htmlspecialchars($empresa_admin) ?></strong>.</p>
    </div>

    <!-- Modal para crear usuario -->
    <div id="crear-usuario-modal">
        <h2>Crear Usuario</h2>
        <form action="index-admin.php" method="POST">
            <label for="documento">Documento:</label>
            <input type="text" name="documento" id="documento" placeholder="Documento del Usuario" required>

            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" placeholder="Nombre del Usuario" required>

            <label for="correo">Correo:</label>
            <input type="email" name="correo" id="correo" placeholder="Correo del Usuario" required>

            <label for="contrasena">Contraseña:</label>
            <input type="password" name="contrasena" id="contrasena" placeholder="Contraseña del Usuario" required>

            <label for="id_roles">Rol:</label>
            <select name="id_roles" id="id_roles" required>
                <option value="3" selected>Usuario</option>
            </select>

            <button type="submit" name="crear_usuario">Registrar Usuario</button>
            <button type="button" id="close-modal">Cancelar</button>
        </form>
    </div>

    <div class="container">
        <h2>Lista de Usuarios</h2>
        <table>
            <thead>
                <tr>
                    <th>Documento</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Fecha de Registro</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['documento']) ?></td>
                        <td><?= htmlspecialchars($user['nombre']) ?></td>
                        <td><?= htmlspecialchars($user['correo']) ?></td>
                        <td><?= htmlspecialchars($user['nom_roles']) ?></td>
                        <td><?= htmlspecialchars($user['fecha_registro']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Mostrar el modal
        document.getElementById('open-modal').addEventListener('click', function () {
            document.getElementById('crear-usuario-modal').style.display = 'block';
        });

        // Ocultar el modal
        document.getElementById('close-modal').addEventListener('click', function () {
            document.getElementById('crear-usuario-modal').style.display = 'none';
        });
    </script>
</body>
</html>