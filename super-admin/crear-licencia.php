<?php
session_start();
require_once('../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

// Consulta para obtener los datos de la tabla estados de licencia
$sql_estados_licencia = $con->prepare("SELECT id_estado_licencia, nom_estado_licencia FROM estado_licencia");
$sql_estados_licencia->execute();
$estados_licencia = $sql_estados_licencia->fetchAll(PDO::FETCH_ASSOC);

// Procesar el formulario de creación de estados de licencia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_estado_licencia'])) {
    $nombre_estado_licencia = $_POST['nombre_estado_licencia'];

    // Insertar el nuevo estado de licencia en la base de datos
    $sql_insert = $con->prepare("INSERT INTO estado_licencia (nom_estado_licencia) VALUES (:nombre_estado_licencia)");
    $sql_insert->bindParam(':nombre_estado_licencia', $nombre_estado_licencia);
    $sql_insert->execute();

    // Redirigir para evitar reenvío del formulario
    header("Location: crear-licencia.php");
    exit();
}

// Procesar el formulario de edición de estados de licencia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_estado_licencia'])) {
    $id_estado_licencia = $_POST['id_estado_licencia'];
    $nombre_estado_licencia = $_POST['editar_estado_licencia'];

    // Actualizar el estado de licencia en la base de datos
    $sql_update = $con->prepare("UPDATE estado_licencia SET nom_estado_licencia = :nombre_estado_licencia WHERE id_estado_licencia = :id_estado_licencia");
    $sql_update->bindParam(':nombre_estado_licencia', $nombre_estado_licencia);
    $sql_update->bindParam(':id_estado_licencia', $id_estado_licencia);
    $sql_update->execute();

    // Redirigir para evitar reenvío del formulario
    header("Location: crear-licencia.php");
    exit();
}

// Procesar la eliminación de un estado de licencia
if (isset($_GET['eliminar'])) {
    $id_estado_licencia = $_GET['eliminar'];

    // Eliminar el estado de licencia de la base de datos
    $sql_delete = $con->prepare("DELETE FROM estado_licencia WHERE id_estado_licencia = :id_estado_licencia");
    $sql_delete->bindParam(':id_estado_licencia', $id_estado_licencia);
    $sql_delete->execute();

    // Redirigir para evitar problemas de recarga
    header("Location: crear-licencia.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin - Estados de Licencia</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Super Admin - Estados de Licencia</h1>
        <div>
            <button id="create-estado-licencia">Crear Estado de Licencia</button>
        </div>
    </div>

    <!-- Formulario para crear estados de licencia (modal) -->
    <div id="crear-estado-licencia-modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0, 0, 0, 0.9); padding: 20px; border-radius: 10px; width: 400px; z-index: 1000;">
        <h2>Crear Estado de Licencia</h2>
        <form action="crear-licencia.php" method="POST">
            <label for="nombre_estado_licencia">Nombre del Estado de Licencia:</label>
            <input type="text" name="nombre_estado_licencia" id="nombre_estado_licencia" placeholder="Nombre del Estado de Licencia" required>

            <button type="submit">Registrar Estado de Licencia</button>
            <button type="button" id="close-modal">Cancelar</button>
        </form>
    </div>

    <!-- Formulario para editar estados de licencia (modal) -->
    <div id="editar-estado-licencia-modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0, 0, 0, 0.9); padding: 20px; border-radius: 10px; width: 400px; z-index: 1000;">
        <h2>Editar Estado de Licencia</h2>
        <form action="crear-licencia.php" method="POST">
            <input type="hidden" name="id_estado_licencia" id="editar-id-estado-licencia">
            <label for="editar_estado_licencia">Nuevo Nombre:</label>
            <input type="text" name="editar_estado_licencia" id="editar-nombre-estado-licencia" placeholder="Nuevo Nombre del Estado de Licencia" required>

            <button type="submit">Actualizar Estado de Licencia</button>
            <button type="button" id="close-edit-modal">Cancelar</button>
        </form>
    </div>

    <div class="container">
        <!-- Tabla de estados de licencia -->
        <h2>Estados de Licencia</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($estados_licencia as $estado_licencia): ?>
                    <tr>
                        <td><?= htmlspecialchars($estado_licencia['id_estado_licencia']) ?></td>
                        <td><?= htmlspecialchars($estado_licencia['nom_estado_licencia']) ?></td>
                        <td class="action-buttons">
                            <button class="edit" onclick="abrirEditarModal(<?= $estado_licencia['id_estado_licencia'] ?>, '<?= htmlspecialchars($estado_licencia['nom_estado_licencia']) ?>')">Editar</button>
                            <button class="delete" onclick="eliminarEstado(<?= $estado_licencia['id_estado_licencia'] ?>)">Eliminar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Mostrar/ocultar el formulario de creación de estados de licencia
        document.getElementById('create-estado-licencia').addEventListener('click', function () {
            document.getElementById('crear-estado-licencia-modal').style.display = 'block';
        });

        document.getElementById('close-modal').addEventListener('click', function () {
            document.getElementById('crear-estado-licencia-modal').style.display = 'none';
        });

        // Mostrar/ocultar el formulario de edición de estados de licencia
        function abrirEditarModal(id, nombre) {
            document.getElementById('editar-id-estado-licencia').value = id;
            document.getElementById('editar-nombre-estado-licencia').value = nombre;
            document.getElementById('editar-estado-licencia-modal').style.display = 'block';
        }

        document.getElementById('close-edit-modal').addEventListener('click', function () {
            document.getElementById('editar-estado-licencia-modal').style.display = 'none';
        });

        // Función para eliminar un estado de licencia
        function eliminarEstado(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este estado de licencia?')) {
                window.location.href = 'crear-licencia.php?eliminar=' + id;
            }
        }
    </script>
</body>
</html>