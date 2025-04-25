<?php
session_start();
require_once('../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

// Consulta para obtener los datos de la tabla tipos de licencia
$sql_tipos_licencia = $con->prepare("SELECT * FROM tipo_licencia");
$sql_tipos_licencia->execute();
$tipos_licencia = $sql_tipos_licencia->fetchAll(PDO::FETCH_ASSOC);

// Procesar el formulario de creación de tipos de licencia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_tipo_licencia'])) {
    $nombre_tipo_licencia = $_POST['nombre_tipo_licencia'];

    // Insertar el nuevo tipo de licencia en la base de datos
    $sql_insert = $con->prepare("INSERT INTO tipo_licencia (nom_licencia) VALUES (:nombre_tipo_licencia)");
    $sql_insert->bindParam(':nombre_tipo_licencia', $nombre_tipo_licencia);
    $sql_insert->execute();

    // Redirigir para evitar reenvío del formulario
    header("Location: crear-tipo-licencia.php");
    exit();
}

// Procesar el formulario de edición de tipos de licencia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_tipo_licencia'])) {
    $id_tipo_licencia = $_POST['id_tipo_licencia'];
    $nombre_tipo_licencia = $_POST['editar_nombre_tipo_licencia'];

    // Actualizar el tipo de licencia en la base de datos
    $sql_update = $con->prepare("UPDATE tipo_licencia SET nom_licencia = :nombre_tipo_licencia WHERE id_tipo_licencia = :id_tipo_licencia");
    $sql_update->bindParam(':nombre_tipo_licencia', $nombre_tipo_licencia);
    $sql_update->bindParam(':id_tipo_licencia', $id_tipo_licencia);
    $sql_update->execute();

    // Redirigir para evitar reenvío del formulario
    header("Location: crear-tipo-licencia.php");
    exit();
}

// Procesar la eliminación de un tipo de licencia
if (isset($_GET['eliminar'])) {
    $id_tipo_licencia = $_GET['eliminar'];

    // Eliminar el tipo de licencia de la base de datos
    $sql_delete = $con->prepare("DELETE FROM tipo_licencia WHERE id_tipo_licencia = :id_tipo_licencia");
    $sql_delete->bindParam(':id_tipo_licencia', $id_tipo_licencia);
    $sql_delete->execute();

    // Redirigir para evitar problemas de recarga
    header("Location: crear-tipo-licencia.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin - Tipos de Licencia</title>
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
        <h1>Super Admin - Tipos de Licencia</h1>
        <div>
            <button id="create-tipo-licencia">Crear Tipo de Licencia</button>
        </div>
    </div>

    <!-- Formulario para crear tipos de licencia (modal) -->
    <div id="crear-tipo-licencia-modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0, 0, 0, 0.9); padding: 20px; border-radius: 10px; width: 400px; z-index: 1000;">
        <h2>Crear Tipo de Licencia</h2>
        <form action="crear-tipo-licencia.php" method="POST">
            <label for="nombre_tipo_licencia">Nombre del Tipo de Licencia:</label>
            <input type="text" name="nombre_tipo_licencia" id="nombre_tipo_licencia" placeholder="Nombre del Tipo de Licencia" required>
            <button type="submit" name="crear_tipo_licencia">Registrar Tipo de Licencia</button>
            <button type="button" id="close-modal">Cancelar</button>
        </form>
    </div>

    <!-- Formulario para editar tipos de licencia (modal) -->
    <div id="editar-tipo-licencia-modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0, 0, 0, 0.9); padding: 20px; border-radius: 10px; width: 400px; z-index: 1000;">
        <h2>Editar Tipo de Licencia</h2>
        <form action="crear-tipo-licencia.php" method="POST">
            <input type="hidden" name="id_tipo_licencia" id="editar-id-tipo-licencia">
            <label for="editar_nombre_tipo_licencia">Nuevo Nombre:</label>
            <input type="text" name="editar_nombre_tipo_licencia" id="editar-nombre-tipo-licencia" placeholder="Nuevo Nombre del Tipo de Licencia" required>
            <button type="submit" name="editar_tipo_licencia">Actualizar Tipo de Licencia</button>
            <button type="button" id="close-edit-modal">Cancelar</button>
        </form>
    </div>

    <div class="container">
        <!-- Tabla de tipos de licencia -->
        <h2>Tipos de Licencia</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tipos_licencia as $tipo_licencia): ?>
                    <tr>
                        <td><?= htmlspecialchars($tipo_licencia['id_tipo_licencia']) ?></td>
                        <td><?= htmlspecialchars($tipo_licencia['nom_licencia']) ?></td>
                        <td class="action-buttons">
                            <button class="edit" onclick="abrirEditarModal(<?= $tipo_licencia['id_tipo_licencia'] ?>, '<?= htmlspecialchars($tipo_licencia['nom_licencia']) ?>')">Editar</button>
                            <button class="delete" onclick="eliminarTipoLicencia(<?= $tipo_licencia['id_tipo_licencia'] ?>)">Eliminar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Mostrar/ocultar el formulario de creación de tipos de licencia
        document.getElementById('create-tipo-licencia').addEventListener('click', function () {
            document.getElementById('crear-tipo-licencia-modal').style.display = 'block';
        });

        document.getElementById('close-modal').addEventListener('click', function () {
            document.getElementById('crear-tipo-licencia-modal').style.display = 'none';
        });

        // Mostrar/ocultar el formulario de edición de tipos de licencia
        function abrirEditarModal(id, nombre) {
            document.getElementById('editar-id-tipo-licencia').value = id;
            document.getElementById('editar-nombre-tipo-licencia').value = nombre;
            document.getElementById('editar-tipo-licencia-modal').style.display = 'block';
        }

        document.getElementById('close-edit-modal').addEventListener('click', function () {
            document.getElementById('editar-tipo-licencia-modal').style.display = 'none';
        });

        // Función para eliminar un tipo de licencia
        function eliminarTipoLicencia(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este tipo de licencia?')) {
                window.location.href = 'crear-tipo-licencia.php?eliminar=' + id;
            }
        }
    </script>
</body>
</html>