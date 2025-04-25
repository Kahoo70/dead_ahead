<?php
session_start();
require_once('../conex/conex.php');
include ('../includes/validarsesion.php');
$conex = new Database;
$con = $conex->conectar();

// Consulta para obtener los personajes disponibles
$sql_personajes = $con->prepare("SELECT id_personaje, nom_personaje FROM personajes");
$sql_personajes->execute();
$personajes = $sql_personajes->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener las rentas junto con la foto del personaje
$sql_rentas = $con->prepare("
    SELECT r.id_renta, r.cedula, r.direccion, r.fecha_renta, r.id_personaje, 
           p.nom_personaje, p.foto 
    FROM renta r
    INNER JOIN personajes p ON r.id_personaje = p.id_personaje
");
$sql_rentas->execute();
$rentas = $sql_rentas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuario - Dead Ahead</title>
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

        .form-container input, .form-container select, .form-container button {
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

        .image-container img {
            width: 100px;
            height: auto;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Usuario Panel</h1>
        <button id="show-rentas">Mostrar Rentas</button>
        <form action="../includes/salir.php" method="POST" style="display: inline;">
            <button type="submit" class="logout-btn">Cerrar Sesión</button>
        </form>
    </div>

    <div class="container">
        <!-- Formulario para realizar rentas -->
        <div class="form-container">
            <h2>Realizar Renta</h2>
            <form action="realizar-renta.php" method="POST">
                <input type="text" name="cedula" placeholder="Cédula del Cliente" required>
                <input type="text" name="direccion" placeholder="Dirección" required>
                <input type="datetime-local" name="fecha_renta" placeholder="Fecha de Renta" required>
                <select name="id_personaje" required>
                    <option value="" disabled selected>Seleccione un Personaje</option>
                    <?php foreach ($personajes as $personaje): ?>
                        <option value="<?= htmlspecialchars($personaje['id_personaje']) ?>">
                            <?= htmlspecialchars($personaje['nom_personaje']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Realizar Renta</button>
            </form>
        </div>

        <!-- Tabla de rentas -->
        <div id="rentas-container" style="display: none;">
            <h2>Rentas</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Renta</th>
                        <th>Cédula</th>
                        <th>Dirección</th>
                        <th>ID Personaje</th>
                        <th>Nombre del Personaje</th>
                        <th>Foto del Personaje</th>
                        <th>Fecha de Renta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rentas as $renta): ?>
                        <tr>
                            <td><?= htmlspecialchars($renta['id_renta']) ?></td>
                            <td><?= htmlspecialchars($renta['cedula']) ?></td>
                            <td><?= htmlspecialchars($renta['direccion']) ?></td>
                            <td><?= htmlspecialchars($renta['id_personaje']) ?></td>
                            <td><?= htmlspecialchars($renta['nom_personaje']) ?></td>
                            <td>
                                <div class="image-container">
                                    <img src="../<?= htmlspecialchars($renta['foto']) ?>" alt="Foto del Personaje">
                                </div>
                            </td>
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
    </script>
</body>
</html>