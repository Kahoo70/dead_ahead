<?php
session_start();
require_once('../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

// Consulta para obtener los datos de la tabla personajes
$sql_personajes = $con->prepare("SELECT * FROM personajes");
$sql_personajes->execute();
$personajes = $sql_personajes->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener las rentas (se usará al hacer clic en el botón)
$sql_rentas = $con->prepare("SELECT * FROM renta");
$sql_rentas->execute();
$rentas = $sql_rentas->fetchAll(PDO::FETCH_ASSOC);
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
            text-align: left;
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

    </style>
</head>
<body>
    <div class="header">
        <h1>Super Admin Panel</h1>
        <div>
            <button id="show-rentas">Mostrar Rentas</button>
            <button id="create-personaje">Crear Personaje</button>
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
    </script>
</body>
</html>