<?php
require_once '../config/database.php';

session_name("LoyolaReportes");
session_start();


if( !isset($_SESSION['usuario']) ){
  die ( json_encode(array(
    "success" => false,
    "reason" => "sin_sesion"
  )));
} 

$usuario_session = $_SESSION['usuario'];

$request_body = file_get_contents('php://input');
$data_json = json_decode($request_body);


$alerta = ORM::for_table('alerta')->find_one($data_json->id);

if( $alerta == null ){
  die ( json_encode(array(
    "success" => false,
    "reason" => "No se encontro la alerta"
  )));
}

$alerta->visto = $data_json->visto;

if( $alerta->save() ){    
  echo json_encode(array(
      "success" => true      
  ));
} else { 
  echo json_encode(array(
      "success" => false      
  ));
}
