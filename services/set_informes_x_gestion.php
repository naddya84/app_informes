<?php
require_once '../config/database.php';

session_name("LoyolaReportes");
session_start();

require_once 'send_notification_disp.php';

if( !isset($_SESSION['usuario']) ){
  die ( json_encode(array(
    "success" => false,
    "reason" => "sin_sesion"
  )));
} 

$usuario = $_SESSION['usuario'];

$request_body = file_get_contents('php://input');

if( !$request_body ){
  die ( json_encode(array("success" => false,"reason" => "sin_datos")));
} else {
  $items_json = json_decode($request_body);
}
$gestion= (new DateTime())->format("Y");
$informe_gestion = null;
$informe_gestion = ORM::for_table('informe')->where('gestion', $gestion)->find_one();

if($informe_gestion == null){
  foreach ( $items_json->ids as $id_item ){    
    $informe_maestro = ORM::for_table('informe_maestro')->where('id', $id_item)->find_one();
    $informe = ORM::for_table('informe')->create();
    $informe->id_informe_padre = $id_item;
    $informe->id_usuario = $informe_maestro->id_usuario;
    if($informe_maestro->id_usuario_2 != null){
      $informe->id_usuario_2 = $informe_maestro->id_usuario_2;
    }
    $informe->gestion = $gestion;
    $informe->estado = "pendifente";
    
    if( !$informe->save() ){  
      ORM::get_db()->rollBack();    
      echo json_encode(array(
          "success" => false,      
          "reason" => "No se puedo crear el informe"
      ));  
      die();
    }
  }
  echo json_encode(array(
    "success" => true
  ));
} else {
  echo json_encode(array(
    "success" => false,      
    "reason" => "Los informes Semestrales, trimestrales y anulales ya se crearon para la presente gestión"
  ));  
  die();
}
