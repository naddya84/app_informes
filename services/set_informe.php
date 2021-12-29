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
  $informe = ORM::for_table('informe')          
    ->where(array(
      'id' => $informe_json->id_informe
  ))
    ->find_one();
} else {
  $informe = ORM::for_table('informe')->create();
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
if( isset($informe_json->fecha_limite) ){
  $informe->fecha_limite = $informe_json->fecha_limite;
}
if( isset($informe_json->email) ){
  $informe->complemetacion = $informe_json->email;
}
if( isset($informe_json->multa) ){
  $informe->multa = $informe_json->multa;
}
if( isset($informe_json->id_institucion) ){
  $informe->id_institucion = $informe_json->id_institucion;
}
$informe->estado = $informe_json->estado;
$informe->id_usuario = $usuario->id; 

ORM::get_db()->beginTransaction();

if( $informe->save() ){ 
     
  $ds = "/";  
  $tempStoreFolder = '../uploads'.$ds.session_id().$ds;
  $storeFolder = '../uploads'.$ds.$informe->id().$ds;
    
  if (!file_exists( $storeFolder )) {        
      if ( !mkdir( $storeFolder, 0777, true) ){
        ORM::get_db()->rollBack();    
        die ( json_encode(array(
          "success" => false,
          "reason" => "No se pudo crear el directorio para guardar los archivos"
        )));
      }
  }
    
  foreach ( $informe_json->fotos_informe as $foto_j ){    
      
    if( !rename( $tempStoreFolder.$foto_j, $storeFolder.$foto_j) ){
      ORM::get_db()->rollBack();    
      echo json_encode(array(
          "success" => false,      
          "reason" => "No se puede copiar las fotos del informe"
      ));  
      die();
    }
      
    $documentos = ORM::for_table('documentos_informe')->create();
    $documentos->url = $foto_j;
    $documentos->descripcion = $storeFolder.$foto_j;
    $documentos->id_informe = $informe->id();
    $documentos->id_usuario = $usuario->id;

    if( !$documentos->save() ){  
      ORM::get_db()->rollBack();    
      echo json_encode(array(
          "success" => false,      
          "reason" => "No se puede guardar los documentos del informe"
      ));  
      die();
    }
  }
  if( isset($informe_json->documento_eliminar) ){                
    if( count($informe_json->documento_eliminar) > 0 ){
      $delete_documento = ORM::for_table('documentos_informe')
      ->where_id_in($informe_json->documento_eliminar)
      ->find_many();

      foreach ( $delete_documento as $documento ){
        if( file_exists($documento->descripcion) ){
          unlink($documento->descripcion);
        }
        $documento->delete();      
      }
    }    
  }
  
  $result = array(
      "success" => true,
      "id" => $informe->id()
  );
  echo json_encode($result);
  ORM::get_db()->commit();   
}