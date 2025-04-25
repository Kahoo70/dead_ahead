<?php
session_start();
require_once('../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

// Consulta para obtener el rol con id = 1
$sql_roles = $con->prepare("SELECT id_roles, nom_roles FROM roles WHERE id_roles = 1");
$sql_roles->execute();
$roles = $sql_roles->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener las empresas
$sql_empresas = $con->prepare("SELECT id_empresa, nom_empresa FROM empresa");
$sql_empresas->execute();
$empresas = $sql_empresas->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener los administradores (usuarios con rol id_roles = 1)
$sql_admins = $con->prepare("SELECT u.documento, u.nombre, u.correo, e.nom_empresa, u.fecha_registro 
                             FROM usuarios u 
                             INNER JOIN empresa e ON u.id_empresa = e.id_empresa 
                             WHERE u.id_roles = 1");
$sql_admins->execute();
$admins = $sql_admins->fetchAll(PDO::FETCH_ASSOC);

// Procesar el formulario de creación de administradores
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_admin'])) {
    $documento = $_POST['documento'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_BCRYPT); // Hashear la contraseña
    $id_roles = 1; // Valor fijo para el rol de administrador
    $id_empresa = $_POST['id_empresa'];
    $fecha_registro = date('Y-m-d H:i:s'); // Fecha y hora actual

    // Insertar el nuevo administrador en la base de datos
    $sql_insert = $con->prepare("INSERT INTO usuarios (documento, nombre, correo, contrasena, id_roles, id_empresa, fecha_registro) VALUES (:documento, :nombre, :correo, :contrasena, :id_roles, :id_empresa, :fecha_registro)");
    $sql_insert->bindParam(':documento', $documento);
    $sql_insert->bindParam(':nombre', $nombre);
    $sql_insert->bindParam(':correo', $correo);
    $sql_insert->bindParam(':contrasena', $contrasena);
    $sql_insert->bindParam(':id_roles', $id_roles); // Valor fijo para id_roles
    $sql_insert->bindParam(':id_empresa', $id_empresa);
    $sql_insert->bindParam(':fecha_registro', $fecha_registro);
    $sql_insert->execute();

    // Redirigir para evitar reenvío del formulario
    header("Location: crear-admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin - Crear Administrador</title>
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
        #crear-admin-modal {
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

        #crear-admin-modal input, #crear-admin-modal select, #crear-admin-modal button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
        }

        #crear-admin-modal button {
            background: rgba(107, 255, 3, 0.2);
            color: #fff;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        #crear-admin-modal button:hover {
            background: rgba(107, 255, 3, 0.5);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Super Admin - Crear Administrador</h1>
        <button id="open-modal">Crear Administrador</button>
    </div>

    <!-- Modal para crear administrador -->
    <div id="crear-admin-modal">
        <h2>Crear Administrador</h2>
        <form action="crear-admin.php" method="POST">
            <label for="documento">Documento:</label>
            <input type="text" name="documento" id="documento" placeholder="Documento del Administrador" required>

            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" placeholder="Nombre del Administrador" required>

            <label for="correo">Correo:</label>
            <input type="email" name="correo" id="correo" placeholder="Correo del Administrador" required>

            <label for="contrasena">Contraseña:</label>
            <input type="password" name="contrasena" id="contrasena" placeholder="Contraseña del Administrador" required>

            <label for="id_roles">Rol:</label>
            <select name="id_roles" id="id_roles" required>
                <option value="1" selected>Administrador</option>
            </select>

            <label for="id_empresa">Empresa:</label>
            <select name="id_empresa" id="id_empresa" required>
                <option value="" disabled selected>Seleccione una Empresa</option>
                <?php foreach ($empresas as $empresa): ?>
                    <option value="<?= htmlspecialchars($empresa['id_empresa']) ?>"><?= htmlspecialchars($empresa['nom_empresa']) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" name="crear_admin">Registrar Administrador</button>
            <button type="button" id="close-modal">Cancelar</button>
        </form>
    </div>

    <div class="container">
        <h2>Lista de Administradores</h2>
        <table>
            <thead>
                <tr>
                    <th>Documento</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Empresa</th>
                    <th>Fecha de Registro</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td><?= htmlspecialchars($admin['documento']) ?></td>
                        <td><?= htmlspecialchars($admin['nombre']) ?></td>
                        <td><?= htmlspecialchars($admin['correo']) ?></td>
                        <td><?= htmlspecialchars($admin['nom_empresa']) ?></td>
                        <td><?= htmlspecialchars($admin['fecha_registro']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Mostrar el modal
        document.getElementById('open-modal').addEventListener('click', function () {
            document.getElementById('crear-admin-modal').style.display = 'block';
        });

        // Ocultar el modal
        document.getElementById('close-modal').addEventListener('click', function () {
            document.getElementById('crear-admin-modal').style.display = 'none';
        });
    </script>
</body>
</html>