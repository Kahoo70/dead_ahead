<?php
session_start();
require_once('../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

// Consulta para obtener los datos de la tabla empresas
$sql_empresas = $con->prepare("SELECT id_empresa, nom_empresa FROM empresa");
$sql_empresas->execute();
$empresas = $sql_empresas->fetchAll(PDO::FETCH_ASSOC);

// Procesar el formulario de creación de empresas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_empresa'])) {
    $nombre_empresa = $_POST['nombre_empresa'];

    // Insertar la nueva empresa en la base de datos
    $sql_insert = $con->prepare("INSERT INTO empresa (nom_empresa) VALUES (:nombre_empresa)");
    $sql_insert->bindParam(':nombre_empresa', $nombre_empresa);
    $sql_insert->execute();

    // Redirigir para evitar reenvío del formulario
    header("Location: crear-empresas.php");
    exit();
}

// Procesar el formulario de edición de empresas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_empresa'])) {
    $id_empresa = $_POST['id_empresa'];
    $nombre_empresa = $_POST['editar_nombre_empresa'];

    // Actualizar la empresa en la base de datos
    $sql_update = $con->prepare("UPDATE empresa SET nom_empresa = :nombre_empresa WHERE id_empresa = :id_empresa");
    $sql_update->bindParam(':nombre_empresa', $nombre_empresa);
    $sql_update->bindParam(':id_empresa', $id_empresa);
    $sql_update->execute();

    // Redirigir para evitar reenvío del formulario
    header("Location: crear-empresas.php");
    exit();
}

// Procesar la eliminación de una empresa
if (isset($_GET['eliminar'])) {
    $id_empresa = $_GET['eliminar'];

    // Eliminar la empresa de la base de datos
    $sql_delete = $con->prepare("DELETE FROM empresa WHERE id_empresa = :id_empresa");
    $sql_delete->bindParam(':id_empresa', $id_empresa);
    $sql_delete->execute();

    // Redirigir para evitar problemas de recarga
    header("Location: crear-empresas.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin - Empresas</title>
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

        .action-buttons button {
            margin-right: 5px;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .action-buttons .edit {
            background: rgba(0, 123, 255, 0.8);
            color: #fff;
        }

        .action-buttons .delete {
            background: rgba(255, 0, 0, 0.8);
            color: #fff;
        }

        .action-buttons .edit:hover {
            background: rgba(0, 123, 255, 1);
        }

        .action-buttons .delete:hover {
            background: rgba(255, 0, 0, 1);
        }

        .form-container {
            margin-top: 20px;
            background: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 10px;
        }

        .form-container input, .form-container button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
        }

        .form-container button {
            background: rgba(107, 255, 3, 0.2);
            color: #fff;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .form-container button:hover {
            background: rgba(107, 255, 3, 0.5);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Super Admin - Empresas</h1>
        <div>
            <button id="create-empresa">Crear Empresa</button>
        </div>
    </div>

    <!-- Formulario para crear empresas (modal) -->
    <div id="crear-empresa-modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0, 0, 0, 0.9); padding: 20px; border-radius: 10px; width: 400px; z-index: 1000;">
        <h2>Crear Empresa</h2>
        <form action="crear-empresas.php" method="POST">
            <label for="nombre_empresa">Nombre de la Empresa:</label>
            <input type="text" name="nombre_empresa" id="nombre_empresa" placeholder="Nombre de la Empresa" required>
            <button type="submit" name="crear_empresa">Registrar Empresa</button>
            <button type="button" id="close-modal">Cancelar</button>
        </form>
    </div>

    <!-- Formulario para editar empresas (modal) -->
    <div id="editar-empresa-modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0, 0, 0, 0.9); padding: 20px; border-radius: 10px; width: 400px; z-index: 1000;">
        <h2>Editar Empresa</h2>
        <form action="crear-empresas.php" method="POST">
            <input type="hidden" name="id_empresa" id="editar-id-empresa">
            <label for="editar_nombre_empresa">Nuevo Nombre:</label>
            <input type="text" name="editar_nombre_empresa" id="editar-nombre-empresa" placeholder="Nuevo Nombre de la Empresa" required>
            <button type="submit" name="editar_empresa">Actualizar Empresa</button>
            <button type="button" id="close-edit-modal">Cancelar</button>
        </form>
    </div>

    <div class="container">
        <!-- Tabla de empresas -->
        <h2>Empresas</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($empresas as $empresa): ?>
                    <tr>
                        <td><?= htmlspecialchars($empresa['id_empresa']) ?></td>
                        <td><?= htmlspecialchars($empresa['nom_empresa']) ?></td>
                        <td class="action-buttons">
                            <button class="edit" onclick="abrirEditarModal(<?= $empresa['id_empresa'] ?>, '<?= htmlspecialchars($empresa['nom_empresa']) ?>')">Editar</button>
                            <button class="delete" onclick="eliminarEmpresa(<?= $empresa['id_empresa'] ?>)">Eliminar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Mostrar/ocultar el formulario de creación de empresas
        document.getElementById('create-empresa').addEventListener('click', function () {
            document.getElementById('crear-empresa-modal').style.display = 'block';
        });

        document.getElementById('close-modal').addEventListener('click', function () {
            document.getElementById('crear-empresa-modal').style.display = 'none';
        });

        // Mostrar/ocultar el formulario de edición de empresas
        function abrirEditarModal(id, nombre) {
            document.getElementById('editar-id-empresa').value = id;
            document.getElementById('editar-nombre-empresa').value = nombre;
            document.getElementById('editar-empresa-modal').style.display = 'block';
        }

        document.getElementById('close-edit-modal').addEventListener('click', function () {
            document.getElementById('editar-empresa-modal').style.display = 'none';
        });

        // Función para eliminar una empresa
        function eliminarEmpresa(id) {
            if (confirm('¿Estás seguro de que deseas eliminar esta empresa?')) {
                window.location.href = 'crear-empresas.php?eliminar=' + id;
            }
        }
    </script>
</body>
</html>