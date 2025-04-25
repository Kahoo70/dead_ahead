<?php
session_start();
require_once('../conex/conex.php');
include ('../includes/validarsesion.php');
$conex = new Database;
$con = $conex->conectar();

// Consulta para obtener los tipos de licencia
$sql_tipos_licencia = $con->prepare("SELECT * FROM tipo_licencia");
$sql_tipos_licencia->execute();
$tipos_licencia = $sql_tipos_licencia->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener los estados de licencia
$sql_estados_licencia = $con->prepare("SELECT * FROM estado_licencia");
$sql_estados_licencia->execute();
$estados_licencia = $sql_estados_licencia->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener las empresas
$sql_empresas = $con->prepare("SELECT id_empresa, nom_empresa FROM empresa");
$sql_empresas->execute();
$empresas = $sql_empresas->fetchAll(PDO::FETCH_ASSOC);

// Procesar el formulario de creación de adquisiciones
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_adquisicion'])) {
    $id_licencia = bin2hex(random_bytes(10)); // Generar un id_licencia alfanumérico de 20 caracteres
    $fecha_adquisicion = $_POST['fecha_adquisicion'];
    $fecha_fin = $_POST['fecha_fin'];
    $id_tipo_licencia = $_POST['id_tipo_licencia'];
    $id_estado_licencia = $_POST['id_estado_licencia'];
    $id_empresa = $_POST['id_empresa'];

    $sql_insert = $con->prepare("INSERT INTO adquisicion (id_licencia, fecha_adquisicion, fecha_fin, id_tipo_licencia, id_estado_licencia, id_empresa) 
                                 VALUES (:id_licencia, :fecha_adquisicion, :fecha_fin, :id_tipo_licencia, :id_estado_licencia, :id_empresa)");
    $sql_insert->bindParam(':id_licencia', $id_licencia);
    $sql_insert->bindParam(':fecha_adquisicion', $fecha_adquisicion);
    $sql_insert->bindParam(':fecha_fin', $fecha_fin);
    $sql_insert->bindParam(':id_tipo_licencia', $id_tipo_licencia);
    $sql_insert->bindParam(':id_estado_licencia', $id_estado_licencia);
    $sql_insert->bindParam(':id_empresa', $id_empresa);
    $sql_insert->execute();

    header("Location: crear-adquisicion.php");
    exit();
}

// Procesar la eliminación de una adquisición
if (isset($_GET['eliminar'])) {
    $id_licencia = $_GET['eliminar'];
    $sql_delete = $con->prepare("DELETE FROM adquisicion WHERE id_licencia = :id_licencia");
    $sql_delete->bindParam(':id_licencia', $id_licencia);
    $sql_delete->execute();

    header("Location: crear-adquisicion.php");
    exit();
}

// Procesar la edición de una adquisición
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_adquisicion'])) {
    $id_licencia = $_POST['id_licencia'];
    $fecha_adquisicion = $_POST['fecha_adquisicion'];
    $fecha_fin = $_POST['fecha_fin'];
    $id_tipo_licencia = $_POST['id_tipo_licencia'];
    $id_estado_licencia = $_POST['id_estado_licencia'];
    $id_empresa = $_POST['id_empresa'];

    $sql_update = $con->prepare("UPDATE adquisicion 
                                 SET fecha_adquisicion = :fecha_adquisicion, 
                                     fecha_fin = :fecha_fin, 
                                     id_tipo_licencia = :id_tipo_licencia, 
                                     id_estado_licencia = :id_estado_licencia, 
                                     id_empresa = :id_empresa 
                                 WHERE id_licencia = :id_licencia");
    $sql_update->bindParam(':id_licencia', $id_licencia);
    $sql_update->bindParam(':fecha_adquisicion', $fecha_adquisicion);
    $sql_update->bindParam(':fecha_fin', $fecha_fin);
    $sql_update->bindParam(':id_tipo_licencia', $id_tipo_licencia);
    $sql_update->bindParam(':id_estado_licencia', $id_estado_licencia);
    $sql_update->bindParam(':id_empresa', $id_empresa);
    $sql_update->execute();

    header("Location: crear-adquisicion.php");
    exit();
}

// Consulta para obtener todos los registros de la tabla adquisicion
$sql_adquisiciones = $con->prepare("SELECT a.id_licencia, a.fecha_adquisicion, a.fecha_fin, 
                                    t.nom_licencia AS tipo_licencia, e.nom_estado_licencia AS estado_licencia, 
                                    em.nom_empresa AS empresa
                                    FROM adquisicion a
                                    INNER JOIN tipo_licencia t ON a.id_tipo_licencia = t.id_tipo_licencia
                                    INNER JOIN estado_licencia e ON a.id_estado_licencia = e.id_estado_licencia
                                    INNER JOIN empresa em ON a.id_empresa = em.id_empresa");
$sql_adquisiciones->execute();
$adquisiciones = $sql_adquisiciones->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin - Crear Adquisición</title>
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

        #crear-adquisicion-modal {
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

        #crear-adquisicion-modal input, #crear-adquisicion-modal select, #crear-adquisicion-modal button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
        }

        #crear-adquisicion-modal button {
            background: rgba(107, 255, 3, 0.2);
            color: #fff;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        #crear-adquisicion-modal button:hover {
            background: rgba(107, 255, 3, 0.5);
        }

        #close-modal {
            background: rgba(255, 0, 0, 0.7);
        }

        #close-modal:hover {
            background: rgba(255, 0, 0, 0.9);
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Super Admin - Crear Adquisición</h1>
        <button id="open-modal">Nueva Adquisición</button>
    </div>

    <!-- Modal para crear adquisición -->
    <div id="crear-adquisicion-modal">
        <h2>Crear Adquisición</h2>
        <form action="crear-adquisicion.php" method="POST">
            <label for="fecha_adquisicion">Fecha de Adquisición:</label>
            <input type="datetime-local" name="fecha_adquisicion" id="fecha_adquisicion" required>

            <label for="fecha_fin">Fecha de Fin:</label>
            <input type="datetime-local" name="fecha_fin" id="fecha_fin" required>

            <label for="id_tipo_licencia">Tipo de Licencia:</label>
            <select name="id_tipo_licencia" id="id_tipo_licencia" required>
                <option value="" disabled selected>Seleccione un Tipo de Licencia</option>
                <?php foreach ($tipos_licencia as $tipo): ?>
                    <option value="<?= htmlspecialchars($tipo['id_tipo_licencia']) ?>"><?= htmlspecialchars($tipo['nombre_tipo']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="id_estado_licencia">Estado de Licencia:</label>
            <select name="id_estado_licencia" id="id_estado_licencia" required>
                <option value="" disabled selected>Seleccione un Estado de Licencia</option>
                <?php foreach ($estados_licencia as $estado): ?>
                    <option value="<?= htmlspecialchars($estado['id_estado_licencia']) ?>"><?= htmlspecialchars($estado['nombre_estado']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="id_empresa">Empresa:</label>
            <select name="id_empresa" id="id_empresa" required>
                <option value="" disabled selected>Seleccione una Empresa</option>
                <?php foreach ($empresas as $empresa): ?>
                    <option value="<?= htmlspecialchars($empresa['id_empresa']) ?>"><?= htmlspecialchars($empresa['nom_empresa']) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" name="crear_adquisicion">Registrar Adquisición</button>
            <button type="button" id="close-modal">Cancelar</button>
        </form>
    </div>

    <!-- Tabla de adquisiciones -->
    <div class="container">
        <h2>Lista de Adquisiciones</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Licencia</th>
                    <th>Fecha de Adquisición</th>
                    <th>Fecha de Fin</th>
                    <th>Tipo de Licencia</th>
                    <th>Estado de Licencia</th>
                    <th>Empresa</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($adquisiciones as $adquisicion): ?>
                    <tr>
                        <td><?= htmlspecialchars($adquisicion['id_licencia']) ?></td>
                        <td><?= htmlspecialchars($adquisicion['fecha_adquisicion']) ?></td>
                        <td><?= htmlspecialchars($adquisicion['fecha_fin']) ?></td>
                        <td><?= htmlspecialchars($adquisicion['tipo_licencia']) ?></td>
                        <td><?= htmlspecialchars($adquisicion['estado_licencia']) ?></td>
                        <td><?= htmlspecialchars($adquisicion['empresa']) ?></td>
                        <td>
                            <button class="action-btn edit-btn" onclick="abrirModalEditar('<?= htmlspecialchars($adquisicion['id_licencia']) ?>')">Editar</button>
                            <button class="action-btn delete-btn" onclick="abrirModalEliminar('<?= htmlspecialchars($adquisicion['id_licencia']) ?>')">Eliminar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal para editar adquisición -->
    <div id="modal-editar" style="display: none;">
        <h2>Editar Adquisición</h2>
        <form action="crear-adquisicion.php" method="POST">
            <input type="hidden" name="id_licencia" id="edit-id-licencia">
            <label for="edit-fecha-adquisicion">Fecha de Adquisición:</label>
            <input type="datetime-local" name="fecha_adquisicion" id="edit-fecha-adquisicion" required>

            <label for="edit-fecha-fin">Fecha de Fin:</label>
            <input type="datetime-local" name="fecha_fin" id="edit-fecha-fin" required>

            <label for="edit-id-tipo-licencia">Tipo de Licencia:</label>
            <select name="id_tipo_licencia" id="edit-id-tipo-licencia" required>
                <?php foreach ($tipos_licencia as $tipo): ?>
                    <option value="<?= htmlspecialchars($tipo['id_tipo_licencia']) ?>"><?= htmlspecialchars($tipo['nom_licencia']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="edit-id-estado-licencia">Estado de Licencia:</label>
            <select name="id_estado_licencia" id="edit-id-estado-licencia" required>
                <?php foreach ($estados_licencia as $estado): ?>
                    <option value="<?= htmlspecialchars($estado['id_estado_licencia']) ?>"><?= htmlspecialchars($estado['nom_estado_licencia']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="edit-id-empresa">Empresa:</label>
            <select name="id_empresa" id="edit-id-empresa" required>
                <?php foreach ($empresas as $empresa): ?>
                    <option value="<?= htmlspecialchars($empresa['id_empresa']) ?>"><?= htmlspecialchars($empresa['nom_empresa']) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" name="editar_adquisicion">Guardar Cambios</button>
            <button type="button" onclick="cerrarModal('modal-editar')">Cancelar</button>
        </form>
    </div>

    <!-- Modal para confirmar eliminación -->
    <div id="modal-eliminar" style="display: none;">
        <h2>Confirmar Eliminación</h2>
        <p>¿Estás seguro de que deseas eliminar esta adquisición?</p>
        <form action="crear-adquisicion.php" method="GET">
            <input type="hidden" name="eliminar" id="delete-id-licencia">
            <button type="submit">Eliminar</button>
            <button type="button" onclick="cerrarModal('modal-eliminar')">Cancelar</button>
        </form>
    </div>

    <script>
        function abrirModalEditar(id) {
            document.getElementById('modal-editar').style.display = 'block';
            document.getElementById('edit-id-licencia').value = id;
        }

        function abrirModalEliminar(id) {
            document.getElementById('modal-eliminar').style.display = 'block';
            document.getElementById('delete-id-licencia').value = id;
        }

        function cerrarModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Mostrar el modal
        document.getElementById('open-modal').addEventListener('click', function () {
            document.getElementById('crear-adquisicion-modal').style.display = 'block';
        });

        // Ocultar el modal
        document.getElementById('close-modal').addEventListener('click', function () {
            document.getElementById('crear-adquisicion-modal').style.display = 'none';
        });
    </script>
</body>
</html>