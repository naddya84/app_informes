<?php

require_once '../config/database.php';

session_name("LoyolaReportes");
session_start();

if (!isset($_SESSION['usuario'])) {
  die ( json_encode(array(
    "success" => false,
    "reason" => "sin_sesion"
  )));
}

$ds = "/";  
$storeFolder = '..'.$ds.'uploads'.$ds.session_id().$ds;

$request_body = file_get_contents('php://input');
$data = json_decode($request_body);

//print_r($data);

if (!isset( $data->name_delete) ){
  die(json_encode(array(
    "success" => false,
    "reason" => "No se mando el nombre archivo"    
  )));
}

if (!file_exists ( $storeFolder.$data->name_delete ) ){
  die(json_encode(array(
    "success" => true,
    "data" => "No existe :".$storeFolder.$data->name_delete
  )));
}

if( unlink( $storeFolder.$data->name_delete ) ){
  echo json_encode(array(
    "success" => true    
  ));
} else {
  echo json_encode(array(
    "success" => false,
    "reason" => "No se pudo eliminar el archivo"
  ));
}


?> 