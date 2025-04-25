<?php
session_start();
unset($_SESSION['documento']);
unset($_SESSION['id_roles']);
unset($_SESSION['id_empresa']);
session_destroy();
session_write_close();

header("Location: ../index.php");
?>  