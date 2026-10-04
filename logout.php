<?php
/**
 * تسجيل الخروج - BookMart
 */
session_start();
session_destroy();
header('Location: index.php');
exit;
