<?php
session_start();
session_destroy();
header('Location: /sundarta/login');
exit();
?>
