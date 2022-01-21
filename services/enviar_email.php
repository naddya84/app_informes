<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../libs/phpmailer/phpmailer/src/Exception.php';
require_once '../libs/phpmailer/phpmailer/src/PHPMailer.php';    
require_once '../libs/phpmailer/phpmailer/src/SMTP.php';
  
require_once '../config/database.php';


function send_mail($to_email, $id_usuario, $url){  
   $body = 
    '<div style="padding: 15px; font-size: 14px; width=80%;">
      <br>
      <div>Alerta de Informes
      <b> Cooperativa Loyola</b>, por 
      favor siga el siguiente enlace:'
          . '<a href="'.$url .'" style="color:#b40837; font-weight: bold;">Aquí</a> 
      </div>      
      <br><br>      
      <div>
        Si no puedes ingresar con el enlace, copia y pega esta direccion en tu navegador:
        <code>'.$url.'</code>
      </div>
      <br>
      <br>
      <div>
      Saludos.<br>
      <b>Coopertativa Abierta de Ahorro y Credito Loyola</b>
      </div>
      <br>  
      <b>Nota.-</b><br>
      Este es un mensaje generado automáticamente y no requiere respuesta. El contenido de este correo 
      es confidencial e interesan exclusivamente al destinatario. Si recibió este mensaje por error 
      le pedimos que lo elimine.
    </div>';    
  
  
   try {
        $email = new PHPMailer();        
        
        if( SMTP_DEBUG ){
          $email->SMTPDebug = 3;          
        }
        
        if( SMTP_SERVER ){
          $email->isSMTP();            
          $email->Host = SMTP_HOST;
          $email->Port = SMTP_PORT;                 
          $email->SMTPAuth = true;                                    
          $email->Username = SMTP_USER_NAME;                
          $email->Password = SMTP_PASSWORD;
          
          if( SMTP_SECURE != ""){
            $email->SMTPSecure = SMTP_SECURE;
          } else  {          
            $email->SMTPOptions = array(
              'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
              )
            );
          }          
        }                                
        
        $email->From      = "cooperativaloyola@gmail.com";
        $email->FromName  = "Alerta de Informes";
        $email->Subject   = "Alerta de Informes";
        $email->Body      = $body;                
        
        $email->AddAddress( $to_email );
        
        if( EMAIL_CC != "" ){
          $email->AddCC( EMAIL_CC );
        }
                        
        $email->IsHTML(true);  
        $email->CharSet = 'UTF-8';

        if( $email->Send() ){
            return true;            
        } else {
          return false;          
        }
    } catch (Exception $e) {        
      return false;        
    }
  
}