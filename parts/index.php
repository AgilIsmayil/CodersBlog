<?php

session_start();

date_default_timezone_set("Asia/Baku");
include "connect.php";
include "header.php";
include "helper.php";
include "nav.php";

$pageWithoutUri = [
    '/Coders/project/auth/login.php',
    '/Coders/project/auth/register.php'
];

if (isset($_SESSION['id']) && in_array($_SERVER['REQUEST_URI'], $pageWithoutUri)) {
    header("location: ../client/blog.php");
}

?>

</body>

</html>