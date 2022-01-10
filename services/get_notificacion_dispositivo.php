<?php
header('X-Frame-Options: SAMEORIGIN');
require_once '../config/database.php';

session_name("BisaNitro");
session_start();


if( !isset($_SESSION['usuario']) ){
  die ( "Sin sesion ");
} 

$usuario = $_SESSION['usuario'];

$request_body = file_get_contents('php://input');
$data = json_decode($request_body);


$notif = ORM::for_table('notificacion_dispositivo')
        ->where(array(
            'id_usuario' => $usuario->id,
            'so' => $data->so,
            'movil' => $data->movil            
          ))        
        ->find_one();

if( $notif == null ){
  $notif = ORM::for_table("notificacion_dispositivo")->create();
  $notif->id_usuario = $usuario->id;
  $notif->so = $data->so;
  $notif->movil = $data->movil;
  $notif->navegador = $data->navegador;
  $notif->habilitado = -1;
  
  if( !$notif->save() ){
    echo json_encode(array(
      "success" => false,
      "reasion" => "No se pudo crear la notificacion para el usuario"
    ));
    die();
  }
}

 
echo json_encode(array(
    "success" => true,
    "notificacion" => $notif->as_array()    
));

