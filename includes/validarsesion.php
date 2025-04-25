<?php
if (!isset($_SESSION['documento'])){ // se verifica la session activada

    unset($_SESSION['documento']);
    unset($_SESSION['id_roles']);
    unset($_SESSION['id_empresa']);
    $_SESSION=array();
    session_destroy();
    session_write_close();

    echo "<script>alert ('Please login') </script>";
    echo '<script>window.location="../index.php"</script>';
    exit();
}
?>
    
<?php //Verificacion del tiempo por inactividad por parte del usu
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 500)) { // 120 seg de tiempo
    unset($_SESSION['id_roles']);
    unset($_SESSION['id_empresa']);
    unset($_SESSION['documento']);
    $_SESSION = array();
    session_destroy();
    session_write_close();
    echo "<script>alert('Your session is expired for inactivity. Please login again.');</script>";
    echo '<script>window.location="../login.php";</script>';
    exit();
}
?>

<?php 
// Actualizamos cada vez que haya una actividad hecha por el usu
$_SESSION['last_activity'] = time();
?>

