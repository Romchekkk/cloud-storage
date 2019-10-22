<?php

require_once("checkAccessRights.php");

session_start();

// Авторизация с использованием механизма coockie
// В массиве $SESSION будет храниться имя авторизованного пользователя,
// а также путь, по которому он сейчас находится

$path = $_SESSION['path'];
checkAccessRights($path, $user);