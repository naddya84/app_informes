<?php
require_once 'config/database.php';
session_name("LoyolaReportes");
session_start();

if (!isset($_SESSION['usuario'])) {
  header('Location: index.php');
  die();
}
$usuario = $_SESSION['usuario'];

$periodo = "";
if (isset($_GET['id_informe'])) {
  $informe = ORM::for_table('informe')->where('id', $_GET['id_informe'])->find_one();
  $informe_maestro = ORM::for_table('informe_maestro')->where('id', $informe->id_informe_padre)->find_one();
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">  
    <link href="css/jquery-ui.css" media="screen" rel="stylesheet" type="text/css" >
    <link href="css/dropzone.css" rel="stylesheet">
    <link href="css/style.css?v=1" media="screen" rel="stylesheet" type="text/css" />
    <link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">

    <script src="js/libs/jquery-3.3.1.min.js"></script>
    <script src="js/libs/jquery-ui.js"></script>        
    <script src="bootstrap/js/bootstrap.min.js"></script>     
    <script src="js/libs/dropzone.js"></script>  
    <script type="text/javascript" src="js/libs/bootstrap-datetimepicker.js" charset="UTF-8"></script>
    <script type="text/javascript" src="js/libs/locales/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script>
    <script src="js/informe.js?v=1.1"></script>
    
    <script type="text/javascript">
      $(document).ready(function () {        
        iniciar();                                    
      });            
    </script>
    <title>Loyola Informes</title>
  </head>
  <body>
    <div class="container-fluid">
      <?php include 'cabecera.php'; ?>
      <input type="hidden" id="id_informe" value="<?=$informe->id?>">
      <input type="hidden" id="rol_usuario" value="<?=$usuario->rol?>">
      <div class="fondo_paginas">
        <div class="espacio"></div>
          <div class="container">
          <div class="card bg_card_green">
            <div class="card-body">
              <div class="espacio"></div>
              <div class="center"><h3 class="font_titulo">Datos Informe</h3></div>
              <div class="espacio"></div>
              <div class="form-group">
                <label class="font_dato">Detalle:</label><span> <?= $informe_maestro->detalle ?></span>
              </div>
              <div class="espacio"></div>
              <div class="row">
                <div class="col-6">
                  <label class="font_dato">Código: </label><span><?=$informe_maestro->codigo?></span>
                  
                </div>
                <div class="col-6">
                  <label class="font_dato">Periodo:</label><span> <?=$informe_maestro->tipo_periodo?></span>
                </div>
              </div>
              <div class="espacio"></div>
              <div class="form-group row">
                <div class="col">
                  <label class="font_dato">Sistema:</label><span> <?=$informe_maestro->sistema_modulo?></span>
                </div>
                <div class="col-6">
                  <label class="font_dato">Institucion:</label>
                </div>
              </div>
              <div class="espacio"></div>
              <div class="form-group row">
                
                <div class="col">
                  <label class="color_plomo">Complementación:</label><span> <?=$informe->complemetacion?></span>
                  
                </div>
              </div>
              <div class="espacio"></div>
              <div class="form-group row">
                <div class="col">
                  <label class="color_plomo">Tipo de Envio: </label><span>  <?=$informe_maestro->tipo_envio?></span>
                </div>
                <div class="col">
                  <label class="font_dato">Multa:</label><?=($informe_maestro->multa == "1")?$informe_maestro->multa:' No'?>
                </div>
              </div>
              <div class="espacio"></div>
              <?php
              if($informe_maestro->tiempo_realizar != null){
              $tiempo_realizar_informe = json_decode($informe->tiempo_realizar);
              }else{
                $tiempo_realizar_informe = "";
              }
              ?>
              <label class="color_plomo">Tiempo para realizar el informe:</label>
              <?=($tiempo_realizar_informe!="" && $tiempo_realizar_informe->dias != "")?"value='".$tiempo_realizar_informe->dias."'":" 0 "?><span> Días</span>
              <?=($tiempo_realizar_informe!="" && $tiempo_realizar_informe->horas != "")?"value='".$tiempo_realizar_informe->horas."'":" 0 "?><span> Horas</span>
             
            </div>
          </div>
          <div class="margen"></div>
          <div class="card">
            <div class="card-body">
              <div class="espacio"></div>
              <h4 class="font_titulo">Completar Datos Informe</h4>
              <div class="espacio"></div>
              <div class="form-group row">
                <div class="col">
                  <label class="font_datos">Tipo de Envio:</label>
                  <strong>Digital </strong><input id="radio_tipo" type="radio" name="envio" value="e" class="radio" <?=($informe->tipo_envio == "e")?'checked="checked"':''?>/>
                  <strong class="margen_left">Impreso </strong><input id="radio_tipo" type="radio" name="envio" value="i" class="radio" <?=($informe->tipo_envio == "i")?'checked="checked"':''?>/>
                  <strong class="margen_left">Impreso - Correo </strong><input id="radio_tipo" type="radio" name="envio" value="i-c" class="radio" <?=($informe->tipo_envio == "i-c")?'checked="checked"':''?>/>
                </div>
                <div class="col">
                  <label class="font_dato">Avance(%):</label>
                  <input type="text" id="avance_informe" class="form-control input_form" value="<?=$informe->avance?>">
                </div>
              </div>
              <div class="margen"></div>
              <div class="form-group row">
                <div class="col-6">
                  <label class="font_datos">Fecha limite: </label>
                  <div class="input-group date form_datetime col-lg-2 col-md-5 margen_fecha"  data-date-format="yyyy-mm-dd hh:ii" >
                    <input id="fecha_limite" class="form-control input_form" size="16" type="text" readonly  <?= $informe->fecha_limite !="" ? "value='" .$informe->fecha_limite. "'" : "" ?>/>
                    <span class="input-group-addon"><span class="css_remove glyphicon-remove"><img src="img/ico_remove_datatime.png"></span></span>
                    <span class="input-group-addon"><span class="glyphicon-th"><img src="img/ico_datetime.png"></span></span>
                  </div>
                  <input type="hidden" id="dtp_input1" value="" />  
                </div>  
                <div class="col-6">
                  <label class="font_dato">E-mail:</label>
                  <input type="text" id="email" class="form-control input_form" <?= ($informe->email != null) ? "value='" .$informe->email. "'" : "" ?>>
                </div>
              </div>
              <div class="espacio"></div>
              <?php 
                $fotos_informe = ORM::for_table('documentos_informe')->where('id_informe',$informe->id)->find_many();
                if($fotos_informe != null){?>
                <div class="contenedor_fotos">
                <?php foreach($fotos_informe as $fotos){ ?>
                  <div data-id_foto="<?=$fotos->id?>" class="left">
                    <input type="hidden" id="documento" value="<?=$fotos->url?>">
                    <img src="img/ico_eliminar.png" class="cursor btn_eliminar_documento">
                    <a href="<?='uploads/'.$informe->id.'/'.$fotos->url?>" target="_blank"><img src="<?='uploads/'.$informe->id.'/'.$fotos->url?>" class="fotos_informe"></a>
                  </div>
                <?php } ?>
                </div>
                <?php } ?>
              <div class="espacio"></div>
              
              <div>
                <label class="color_plomo">Fotos del Informe</label>
                <form action="services/photoupload.php" class="dropzone" id="my-dropzone" method="POST"></form>
              </div>
              <div class="espacio"></div>
              <label class="color_plomo">Observaciones:</label>
              <textarea type="text" class="css_textarea" id="observacion" ><?=($informe->observaciones != null) ?$informe->observaciones: "" ?></textarea>
              <div class="espacio"></div>
              <div class="center">
                <div id="btn_guardar" class="btn css_btn">GUARDAR</div>
                <div id="btn_finalizar" class="btn css_btn" style="margin-left: 3%;margin-right: 3%">FINALIZAR</div>
                <a " href="javascript:history.back()" class="btn css_btn">VOLVER</a>
              </div>
            </div>
          </div>
        </div>
        <br>
      </div>
    </div>
    <?php include 'pie_pagina.php'; ?>
    <!--Ventana emergente contacto -->
    <div id="fondo_pop" class="popup-overlay"></div>
    <div id="mensaje_form" class="popup" >
      <div class="content-popup">
        <div id="btn_cerrar" class="btn_cerrar_pop"></div>
        <div>
          <div id="texto_mensaje"> </div>
        </div>
      </div>
    </div>
    <div id="div_cargando" class="fondo_block">
      <img src="img/cargando.gif" class="img_cargando">
    </div>
  </body>
</html>
