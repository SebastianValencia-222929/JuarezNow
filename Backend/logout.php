<?php
session_start();
session_unset();
session_destroy();
header("Location: ../Frontend/resources/iniciar_sesion.php");
exit();
?>
