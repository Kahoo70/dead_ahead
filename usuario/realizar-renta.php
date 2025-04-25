<?php
session_start();
require_once('../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $cedula = $_POST['cedula'];
    $direccion = $_POST['direccion'];
    $fecha_renta = $_POST['fecha_renta'];
    $id_personaje = $_POST['id_personaje'];

    try {
        // Insertar la renta en la base de datos
        $sql_insert = $con->prepare("
            INSERT INTO renta (cedula, direccion, fecha_renta, id_personaje) 
            VALUES (:cedula, :direccion, :fecha_renta, :id_personaje)
        ");
        $sql_insert->bindParam(':cedula', $cedula);
        $sql_insert->bindParam(':direccion', $direccion);
        $sql_insert->bindParam(':fecha_renta', $fecha_renta);
        $sql_insert->bindParam(':id_personaje', $id_personaje);
        $sql_insert->execute();

        // Redirigir de vuelta a index-usuario.php con un mensaje de éxito
        header("Location: index-usuario.php?success=renta_realizada");
        exit();
    } catch (PDOException $e) {
        // Redirigir de vuelta a index-usuario.php con un mensaje de error
        header("Location: index-usuario.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    // Si no se accede mediante POST, redirigir al panel de usuario
    header("Location: index-usuario.php");
    exit();
}
?>