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

$usuario = $_SESSION['usuario'];

$request_body = file_get_contents('php://input');

if( !$request_body ){
  die ( json_encode(array("success" => false,"reason" => "sin_datos")));
} else {
  $informe_json = json_decode($request_body);
}
if( isset( $informe_json->id ) ){
  $informe = ORM::for_table('informe_maestro')          
    ->where(array(
      'id' => $informe_json->id
  ))
    ->find_one();
} else {
  $informe = ORM::for_table('informe_maestro')->create();
}
 
if( isset($informe_json->nombre) ){
  $informe->detalle = $informe_json->nombre;
}
if( isset($informe_json->codigo) ){
  $informe->codigo = $informe_json->codigo;
}
if( isset($informe_json->periodo) ){
  $informe->tipo_periodo = $informe_json->periodo;
}
if( isset($informe_json->tipo) ){
  $informe->tipo_envio = $informe_json->tipo;
}
if( isset($informe_json->avance_informe) ){
  $informe->avance = $informe_json->avance_informe;
}
if( isset($informe_json->sistema_modulo) ){
  $informe->sistema_modulo = $informe_json->sistema_modulo;
}
if( isset($informe_json->plazo_envio) ){
  $informe->plazo_envio = $informe_json->plazo_envio;
}
if( isset($informe_json->email) ){
  $informe->complementacion = $informe_json->email;
}
if( isset($informe_json->multa) ){
  $informe->multa = $informe_json->multa;
}
if( isset($informe_json->id_institucion) ){
  $informe->id_institucion = $informe_json->id_institucion;
}

$informe->estado = "pendiente";

if( isset($informe_json->archivos_electronicos)){
  $informe->archivos_electronicos = $informe_json->archivos_electronicos;
}
if( isset($informe_json->informacion_remitida)){
  $informe->informacion_remitida = $informe_json->informacion_remitida;
}
if( isset($informe_json->seccion)){
  $informe->seccion = $informe_json->seccion;
}
if( isset($informe_json->normativa)){
  $informe->normativa = $informe_json->normativa;
}
if( isset($informe_json->articulo)){
  $informe->articulo = $informe_json->articulo;
}
$informe->id_usuario = $usuario->id; 

ORM::get_db()->beginTransaction();

if( $informe->save() ){     
  $result = array(
      "success" => true,
      "id" => $informe->id()
  );
  echo json_encode($result);
  ORM::get_db()->commit();   
}