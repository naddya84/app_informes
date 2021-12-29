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
$storeFolder = '..'.$ds.'uploads'.$ds.session_id();
 
if (!empty($_FILES)) {
  
    if (!file_exists( $storeFolder )) {
        if ( !mkdir( $storeFolder, 0777, true) ){
          die ( json_encode(array(
            "success" => false,
            "reason" => "No se pudo crear el directorio para subir los archivos"
          )));
        }
    }
     
    $tempFile = $_FILES['file']['tmp_name'];          
    $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;  
    $targetFile =  $targetPath. $_FILES['file']['name']; 
    /*$ext = pathinfo($_FILES["file"]["name"])['extension'];
    $targetFile =  $targetPath. microtime(true).".".$ext; */
 
    if( move_uploaded_file($tempFile,$targetFile) ) {
      die ( json_encode(array(
        "success" => true        
      )));
    } else {
      die ( json_encode(array(
        "success" => false,
        "reason" => "No se pudo guardar el archivo"
      )));
    } 
     
} else {
  die ( json_encode(array(
    "success" => false,
    "reason" => "No se mandaron archivos"
  )));
}
?> 