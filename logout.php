<?php
session_start();
session_unset();
session_destroy();
header("Location: inicio_sesion.html");
exit();
?>