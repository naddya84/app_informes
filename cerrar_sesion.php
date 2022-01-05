<?php

session_name("LoyolaReportes");
session_start();

unset($_SESSION['usuario']);

header('Location: index.php');

?> 