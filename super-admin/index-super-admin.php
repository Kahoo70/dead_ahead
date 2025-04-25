<?php
session_start();
require_once('../conex/conex.php');
include ('../includes/validarsesion.php');
$conex = new Database;
$con = $conex->conectar();

// Consulta para obtener los datos de la tabla personajes
$sql_personajes = $con->prepare("SELECT * FROM personajes");
$sql_personajes->execute();
$personajes = $sql_personajes->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener las rentas
$sql_rentas = $con->prepare("SELECT * FROM renta");
$sql_rentas->execute();
$rentas = $sql_rentas->fetchAll(PDO::FETCH_ASSOC);

// Procesar la eliminación de un personaje
if (isset($_GET['eliminar'])) {
    $id_personaje = $_GET['eliminar'];

    $sql_delete = $con->prepare("DELETE FROM personajes WHERE id_personaje = :id_personaje");
    $sql_delete->bindParam(':id_personaje', $id_personaje, PDO::PARAM_INT);
    $sql_delete->execute();

    echo '<script>alert("Personaje eliminado exitosamente."); window.location = "index-super-admin.php";</script>';
    exit();
}

// Procesar el formulario de edición de personajes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_personaje'])) {
    $id_personaje = $_POST['id_personaje'];
    $nom_personaje = trim($_POST['editar_nom_personaje']);
    $fuerza = intval($_POST['editar_fuerza']);
    $foto_ruta = null;

    // Manejo de la foto si se sube una nueva
    if (isset($_FILES['editar_foto']) && $_FILES['editar_foto']['error'] === UPLOAD_ERR_OK) {
        $foto_tmp = $_FILES['editar_foto']['tmp_name'];
        $foto_nombre = basename($_FILES['editar_foto']['name']);
        $foto_extension = strtolower(pathinfo($foto_nombre, PATHINFO_EXTENSION));
        $foto_destino = "../img/personajes/" . $foto_nombre;

        // Validar tipo de archivo (solo PNG) y tamaño (máximo 2MB)
        if ($foto_extension !== 'png') {
            echo '<script>alert("Solo se permiten imágenes en formato PNG."); window.location = "index-super-admin.php";</script>';
            exit;
        }

        if ($_FILES['editar_foto']['size'] > 2 * 1024 * 1024) { // 2MB
            echo '<script>alert("El tamaño máximo permitido para la imagen es de 2MB."); window.location = "index-super-admin.php";</script>';
            exit;
        }

        // Mover la foto al directorio de imágenes
        if (move_uploaded_file($foto_tmp, $foto_destino)) {
            $foto_ruta = "img/personajes/" . $foto_nombre;
        } else {
            echo '<script>alert("Error al subir la foto."); window.location = "index-super-admin.php";</script>';
            exit;
        }
    }

    // Actualizar el personaje en la base de datos
    if ($foto_ruta) {
        $sql_update = $con->prepare("UPDATE personajes SET nom_personaje = :nom_personaje, fuerza = :fuerza, foto = :foto WHERE id_personaje = :id_personaje");
        $sql_update->bindParam(':foto', $foto_ruta, PDO::PARAM_STR);
    } else {
        $sql_update = $con->prepare("UPDATE personajes SET nom_personaje = :nom_personaje, fuerza = :fuerza WHERE id_personaje = :id_personaje");
    }

    $sql_update->bindParam(':nom_personaje', $nom_personaje, PDO::PARAM_STR);
    $sql_update->bindParam(':fuerza', $fuerza, PDO::PARAM_INT);
    $sql_update->bindParam(':id_personaje', $id_personaje, PDO::PARAM_INT);
    $sql_update->execute();

    echo '<script>alert("Personaje actualizado exitosamente."); window.location = "index-super-admin.php";</script>';
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin - Dead Ahead</title>
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
        <h1>Super Admin Panel</h1>
        <div>
            <button id="show-rentas">Mostrar Rentas</button>
            <button id="create-personaje">Crear Personaje</button>
            <form action="../includes/salir.php" method="POST" style="display: inline;">
                <button type="submit" class="logout-btn">Cerrar Sesión</button>
            </form>
        </div>
    </div>

    <!-- Formulario para crear personajes (modal) -->
    <div id="crear-personaje-modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0, 0, 0, 0.9); padding: 20px; border-radius: 10px; width: 400px; z-index: 1000;">
        <h2>Crear Personaje</h2>
        <form action="crear-personaje.php" method="POST" enctype="multipart/form-data">
            <label for="nom_personaje">Nombre del Personaje:</label>
            <input type="text" name="nom_personaje" id="nom_personaje" placeholder="Nombre del Personaje" required>

            <label for="fuerza">Fuerza:</label>
            <input type="number" name="fuerza" id="fuerza" placeholder="Fuerza" required>

            <label for="foto">Foto del Personaje:</label>
            <input type="file" name="foto" id="foto" accept="image/*" required>

            <button type="submit">Registrar Personaje</button>
            <button type="button" id="close-modal">Cancelar</button>
        </form>
    </div>

    <div class="container">
        <!-- Tabla de personajes -->
        <h2>Personajes</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Fuerza</th>
                    <th>Foto</th>
                    <th>Creado Por</th>
                    <th>Fecha de Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($personajes as $personaje): ?>
                    <tr>
                        <td><?= htmlspecialchars($personaje['id_personaje']) ?></td>
                        <td><?= htmlspecialchars($personaje['nom_personaje']) ?></td>
                        <td><?= htmlspecialchars($personaje['fuerza']) ?></td>
                        <td>
                            <div class="image-container">
                                <img src="../<?= htmlspecialchars($personaje['foto']) ?>" 
                                     alt="Foto de <?= htmlspecialchars($personaje['nom_personaje']) ?>">
                            </div>
                        </td>
                        <td><?= htmlspecialchars($personaje['creado_por']) ?></td>
                        <td><?= htmlspecialchars($personaje['fecha_creado']) ?></td>
                        <td class="action-buttons">
                            <button class="edit" onclick="abrirEditarModal(<?= $personaje['id_personaje'] ?>, '<?= htmlspecialchars($personaje['nom_personaje']) ?>', <?= $personaje['fuerza'] ?>)">Editar</button>
                            <button class="delete" onclick="eliminarPersonaje(<?= $personaje['id_personaje'] ?>)">Eliminar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Formulario para realizar rentas -->
        <div class="form-container">
            <h2>Realizar Renta</h2>
            <form action="realizar_renta.php" method="POST">
                <input type="text" name="id_personaje" placeholder="ID del Personaje" required>
                <input type="text" name="id_usuario" placeholder="ID del Usuario" required>
                <input type="date" name="fecha_renta" placeholder="Fecha de Renta" required>
                <button type="submit">Realizar Renta</button>
            </form>
        </div>

        <!-- Tabla de rentas (oculta inicialmente) -->
        <div id="rentas-container" style="display: none;">
            <h2>Rentas</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Renta</th>
                        <th>ID Usuario</th>
                        <th>ID Personaje</th>
                        <th>Fecha de Renta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rentas as $renta): ?>
                        <tr>
                            <td><?= htmlspecialchars($renta['id_renta']) ?></td>
                            <td><?= htmlspecialchars($renta['id_usuario']) ?></td>
                            <td><?= htmlspecialchars($renta['id_personaje']) ?></td>
                            <td><?= htmlspecialchars($renta['fecha_renta']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para editar personaje -->
    <div id="editar-personaje-modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0, 0, 0, 0.9); padding: 20px; border-radius: 10px; width: 400px; z-index: 1000;">
        <h2>Editar Personaje</h2>
        <form action="index-super-admin.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_personaje" id="editar-id-personaje">
            <label for="editar_nom_personaje">Nombre:</label>
            <input type="text" name="editar_nom_personaje" id="editar-nom-personaje" required>
            <label for="editar_fuerza">Fuerza:</label>
            <input type="number" name="editar_fuerza" id="editar-fuerza" required>
            <label for="editar_foto">Actualizar Foto:</label>
            <input type="file" name="editar_foto" id="editar-foto" accept="image/*">
            <button type="submit" name="editar_personaje">Actualizar</button>
            <button type="button" onclick="cerrarEditarModal()">Cancelar</button>
        </form>
    </div>

    <script>
        // Mostrar/ocultar la tabla de rentas
        document.getElementById('show-rentas').addEventListener('click', function () {
            const rentasContainer = document.getElementById('rentas-container');
            if (rentasContainer.style.display === 'none') {
                rentasContainer.style.display = 'block';
            } else {
                rentasContainer.style.display = 'none';
            }
        });

        // Mostrar/ocultar el formulario de creación de personajes
        document.getElementById('create-personaje').addEventListener('click', function () {
            document.getElementById('crear-personaje-modal').style.display = 'block';
        });

        document.getElementById('close-modal').addEventListener('click', function () {
            document.getElementById('crear-personaje-modal').style.display = 'none';
        });

        function abrirEditarModal(id, nombre, fuerza) {
            document.getElementById('editar-id-personaje').value = id;
            document.getElementById('editar-nom-personaje').value = nombre;
            document.getElementById('editar-fuerza').value = fuerza;
            document.getElementById('editar-personaje-modal').style.display = 'block';
        }

        function cerrarEditarModal() {
            document.getElementById('editar-personaje-modal').style.display = 'none';
        }

        function eliminarPersonaje(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este personaje?')) {
                window.location.href = 'index-super-admin.php?eliminar=' + id;
            }
        }
    </script>
</body>
</html>