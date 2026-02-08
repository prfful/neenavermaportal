<?php
session_start();
session_destroy();
header('Location: photo-admin-login.php');
exit;
?>
