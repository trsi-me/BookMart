<?php
session_start();
unset($_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['admin_role']);
header('Location: login.php');
exit;
