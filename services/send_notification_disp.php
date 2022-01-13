<?php

define( 'PROJECT_ID', 'loyola-reportes' );
define ('ACCESS_TOKEN', 'AAAA7rGiPQU:APA91bFrWRf7np2QoRCsP_Qc-OkPqOZAq5IJpKu7yvR7nOfauq6-brPunDc-hFA2kz4OXvzV523uTON5wb9VWmfUiRSlYpPA757dbZtAeCyoPRYTex2Aor_Q5xQfshACDZKivH5fufKK');

function send_notificacion_disps( $id_usuario, $titulo, $cuerpo, $tipo = "normal"){
  
  if( $tipo == "normal" ){
    $notifs = ORM::for_table("notificacion_dispositivo")
        ->where("id_usuario", $id_usuario)
        ->where("habilitado", "1")
        ->find_many();
    $url_land = "/notificacion.php";
  }
  
  if( count($notifs) > 0 ){
    $ids = "";
    if( count($notifs) > 1 ){
      $ids = array();
      foreach ( $notifs as $notif ) {
        array_push($ids, $notif->token);
      }
    } else {
      $ids = $notifs[0]->token;
    }


    $url = "https://fcm.googleapis.com/fcm/send";
    $error_msg = "";    

    $data = new stdClass();

    if( gettype($ids) == "array" ){
      $data->registration_ids = $ids;
    } else {
      $data->to = $ids;
    }  

    $data->data = new stdClass();
    $data->data->notification = new stdClass();
    $data->data->notification->title = $titulo;
    $data->data->notification->body = $cuerpo; 
    $data->data->notification->url = "https://".$_SERVER['SERVER_NAME'].ROUTE_SERVER.$url_land;
    $data->data->notification->url_ico = ROUTE_SERVER."/img/logo_2.png"; 
    $data->data->notification->url_badge = ROUTE_SERVER."/img/badge.png";   


    $headers = array
    (
        'Authorization:key=' . ACCESS_TOKEN,
        'Content-Type: application/json'
    );

    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, $url );
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, true );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $data ) );
    $result = curl_exec($ch );
    
    if (curl_error($ch)) {
      $error_msg = curl_error($ch);
    }

    curl_close( $ch );

    if($error_msg == ""){    
      return $result;
    } else {    
      return $error_msg;
    }
  } else {
    return "No hay nada que notificar id_usuario: ".$id_usuario;
  }
}
