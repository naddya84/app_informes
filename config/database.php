<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'idiorm.php';

define ('HOST', "localhost");
define ('NAME_DB', "loyola_reportes");
define ('USER_DB', "root");
define ('PASSWD_DB', "");
define ('ROUTE_SERVER', "/loyola_reportes");
define ('NAME_SERVER', "http://localhost");

define("EMAIL_FROM", "no-reply@loyola.com");
define("EMAIL_FROM_NAME", "Administracion Reportes");
define("EMAIL_SUBJECT", "Restaurar clave");
define("EMAIL_CC", "naddya.villarroel@wiserkronox.com");//Si EMAIL_CC se deja en blanco no manda copias

//**   configuraciones para el uso de servidor SMTP_SERVER 
define("SMTP_SERVER", true);
//Solo configurar los siguientes campos solo si el servidor es SMTP
define("SMTP_DEBUG", false); // solo usar true para pruebas de diagnstico 
define("SMTP_HOST", "mail.wiserkronox.com");
define("SMTP_PORT", "26");
define("SMTP_USER_NAME", "naddya.villarroel@wiserkronox.com");
define("SMTP_PASSWORD", "wkx");
define("SMTP_SECURE", "");//Valores posibles ssl y tls

date_default_timezone_set("America/La_Paz");



ORM::configure('mysql:host='.HOST.';dbname='.NAME_DB.';charset=utf8');
ORM::configure('username', USER_DB);
ORM::configure('password', PASSWD_DB);
