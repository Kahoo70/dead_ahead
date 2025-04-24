<!-- filepath: c:\xampp\htdocs\deadahead\super-admin\crear-personaje.php -->
<?php
session_start();
require_once('../conex/conex.php');
$conex = new Database;
$con = $conex->conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_personaje = trim($_POST['nom_personaje']);
    $fuerza = intval($_POST['fuerza']);
    $creado_por = $_SESSION['documento']; // Documento del usuario en sesión
    $fecha_creacion = date('Y-m-d H:i:s'); // Fecha actual

    // Manejo de la foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $foto_nombre = basename($_FILES['foto']['name']);
        $foto_extension = strtolower(pathinfo($foto_nombre, PATHINFO_EXTENSION));
        $foto_destino = "../img/personajes/" . $foto_nombre;

        // Validar tipo de archivo (solo PNG) y tamaño (máximo 2MB)
        if ($foto_extension !== 'png') {
            echo '<script>alert("Solo se permiten imágenes en formato PNG."); window.location = "index-super-admin.php";</script>';
            exit;
        }

        if ($_FILES['foto']['size'] > 2 * 1024 * 1024) { // 2MB
            echo '<script>alert("El tamaño máximo permitido para la imagen es de 2MB."); window.location = "index-super-admin.php";</script>';
            exit;
        }

        // Mover la foto al directorio de imágenes
        if (move_uploaded_file($foto_tmp, $foto_destino)) {
            try {
                // Guardar la ruta completa en la base de datos
                $foto_ruta = "img/personajes/" . $foto_nombre;

                // Insertar el personaje en la base de datos
                $sql = $con->prepare("INSERT INTO personajes (nom_personaje, fuerza, foto, creado_por, fecha_creado) VALUES (:nom_personaje, :fuerza, :foto, :creado_por, :fecha_creado)");
                $sql->bindParam(':nom_personaje', $nom_personaje, PDO::PARAM_STR);
                $sql->bindParam(':fuerza', $fuerza, PDO::PARAM_INT);
                $sql->bindParam(':foto', $foto_ruta, PDO::PARAM_STR);
                $sql->bindParam(':creado_por', $creado_por, PDO::PARAM_STR);
                $sql->bindParam(':fecha_creado', $fecha_creacion, PDO::PARAM_STR);
                $sql->execute();

                echo '<script>alert("Personaje creado exitosamente."); window.location = "index-super-admin.php";</script>';
            } catch (PDOException $e) {
                die("Error al registrar el personaje: " . $e->getMessage());
            }
        } else {
            echo '<script>alert("Error al subir la foto."); window.location = "index-super-admin.php";</script>';
        }
    } else {
        echo '<script>alert("Por favor, selecciona una foto válida."); window.location = "index-super-admin.php";</script>';
    }
}
?>