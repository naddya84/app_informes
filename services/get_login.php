<?php
session_name("LoyolaReportes");
session_start();

header('X-Frame-Options: SAMEORIGIN');

require_once '../config/database.php';


$request_body = file_get_contents('php://input');
$data = json_decode($request_body);


$usuario = ORM::for_table('usuario')
        ->where(array(
            'usuario' => $data->usuario,   
            'clave'=> $data->clave
          ))
        ->find_one();

if( $usuario == null ){
  $_SESSION["logout"] = "log_out";
  die( json_encode(array(
      "success" => false      
  )));
}

if($usuario != null){
  if( $usuario->deleted_at != null ){
    echo json_encode(array(
        "success" => false,
        "reason" => "Esta cuenta fue suspendida, contactarse con el administrador "
        . "del sistema para más información"    
    ));
    die();
  }
  
  $_SESSION["usuario"] = $usuario;  
  
  echo json_encode(array(
      "success" => true,
      "rol" => $usuario->rol    
  ));
} else {
  $_SESSION["logout"] = "log_out";
  echo json_encode(array(
      "success" => false      
  ));
}