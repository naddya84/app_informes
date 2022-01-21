<?php
require_once '../config/database.php';
require_once 'enviar_email.php';

session_name("LoyolaReportes");
session_start();

$request_body = file_get_contents('php://input');
$data_json = json_decode($request_body);

 $url = NAME_SERVER.ROUTE_SERVER.'/services/get_reporte_alertas_pdf?id_usuario='.$data_json->id_usuario ;
  if( $data_json->email != null ){
    if( filter_var($data_json->email, FILTER_VALIDATE_EMAIL)  ){
      send_mail($data_json->email,$data_json->id_usuario, $url);             
    } 
    echo json_encode(array(
        "success" => true
    ));
  } else {
  echo json_encode(array(
      "success" => false      
  ));
}

  