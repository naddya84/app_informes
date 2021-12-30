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
  $informe_edit = ORM::for_table('informe')->where('id', $_GET['id_informe'])->find_one();
  if($informe_edit != null){
    $periodo = $informe_edit->tipo_periodo;
  }
}
$instituciones = ORM::for_table('institucion')->find_many();
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
    <script src="js/registro_informe.js?v=1"></script>
    
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
      <input type="hidden" id="id_informe" value="<?=$informe_edit->id?>">
      <div class="fondo_paginas">
        <div class="espacio"></div>
          <div class="container">
          <div class="card bg_card">
            <div class="card-body">
              <div class="espacio"></div>
              <div class="center"><h3 class="font_titulo">Nuevo Informe</h3></div>
              <div class="espacio"></div>
              <div class="form-group">
                <label class="font_dato">Detalle:</label>
                <input type="text" id="nombre" class="form-control input_form_detalle" value="<?= isset($informe_edit) ? $informe_edit->detalle : "" ?>">
              </div>
              <div class="espacio"></div>
              <div class="row">
                <div class="col-6">
                  <label class="font_dato">Código:</label>
                  <input type="text" id="codigo" class="form-control input_form" value="<?= isset($informe_edit) ? $informe_edit->codigo : "" ?>">
                </div>
                <div class="col-6">
                  <label class="font_dato">Periodo:</label>
                  <select id="periodo" class="form-control input_form">
                    <option value="diario" <?="diario"==$periodo?"selected":""?>>Diario</option>
                    <option value="semanal" <?="semanal"==$periodo?"selected":""?>>Semanal</option>
                    <option value="mensual" <?="mensual"==$periodo?"selected":""?>>Mensual</option>
                    <option value="trimestral" <?="trimestral"==$periodo?"selected":""?>>Trimestral</option>
                    <option value="semestral" <?="semestral"==$periodo?"selected":""?>>Semestral</option>
                    <option value="anual" <?="anual"==$periodo?"selected":""?>>Anual</option>
                    <option value="requerimiento_ASFI" <?="requerimiento_ASFI"==$periodo?"selected":""?>>A Requerimiento ASFI</option>
                  </select>
                </div>
              </div>
              <div class="espacio"></div>
              <div class="form-group row">
                <div class="col">
                  <label class="font_dato">Sistema:</label>
                  <input type="text" id="sistema_modulo" class="form-control input_form" <?= isset($informe_edit) ? "value='" .$informe_edit->sistema_modulo. "'" : "" ?>>
                </div>
                <div class="col">
                  <label class="font_dato">Avance(%):</label>
                  <input type="text" id="avance_informe" class="form-control input_form" <?= isset($informe_edit) ? "value='" .$informe_edit->avance. "'" : "" ?>>
                </div>
              </div>
              <div class="espacio"></div>
              <div class="form-group row">
                <div class="col-6">
                  <label class="font_dato">Institucion:</label>
                  <select id="institucion" class="form-control input_form">
                    <option value="">Sin Asignar</option>                                                                                  
                      <?php foreach ( $instituciones as $institucion ){ ?>
                      <option value="<?=$institucion->id?>"
                              <?=isset($informe_edit)?($informe_edit->id==$informe_edit->id_institucion?'selected':''):''?>><?=$institucion->nombre?></option>
                      <?php } ?>
                    </select>
                </div>
                <div class="col">
                  <label class="font_dato">E-mail:</label>
                  <input type="text" id="email" class="form-control input_form" <?= isset($informe_edit) ? "value='" .$informe_edit->complemetacion. "'" : "" ?>>
                </div>
              </div>
              <div class="espacio"></div>
              <div class="form-group row">
                <div class="col">
                  <label class="font_datos">Tipo de Envio:</label>
                  <strong>Digital </strong><input id="radio_tipo" type="radio" name="envio" value="e" class="radio" <?=isset($informe_edit)?($informe_edit->tipo_envio == "e")?'checked="checked"':'':''?>/>
                  <strong class="margen_left">Impreso </strong><input id="radio_tipo" type="radio" name="envio" value="i" class="radio" <?=isset($informe_edit)?($informe_edit->tipo_envio == "i")?'checked="checked"':'':''?>/>
                  <strong class="margen_left">Impreso - Correo </strong><input id="radio_tipo" type="radio" name="envio" value="i-c" class="radio" <?=isset($informe_edit)?($informe_edit->tipo_envio == "i-c")?'checked="checked"':'':''?>/>
                </div>
                <div class="col">
                  <label class="font_dato">Multa:</label>
                  <strong> Si </strong>
                  <input id="radio_a" type="radio" name="multa" value="1" class="radio" <?=isset($informe_edit)?($informe_edit->multa == "1")?'checked="checked"':'':''?>/>
                  <strong class="margen_left">No </strong>
                  <input id="radio_a" type="radio" name="multa" value="0" class="radio" <?=isset($informe_edit)?($informe_edit->multa == "0")?'checked="checked"':'':''?>/>
                </div>
              </div>
              <div class="espacio"></div>
              <div class="form-group row">
                <div class="col-6">
                  <label class="font_datos">Fecha limite: </label>
                  <div class="input-group date form_datetime col-lg-3 col-md-5 margen_fecha"  data-date-format="yyyy-mm-dd hh:ii" >
                    <input id="fecha_limite" class="form-control input_form" size="16" type="text" readonly  <?= isset($informe_edit) ? "value='" .$informe_edit->fecha_limite. "'" : "" ?>/>
                    <span class="input-group-addon"><span class="css_remove glyphicon-remove"><img src="img/ico_remove_datatime.png"></span></span>
                    <span class="input-group-addon"><span class="glyphicon-th"><img src="img/ico_datetime.png"></span></span>
                  </div>
                  <input type="hidden" id="dtp_input1" value="" />  
                </div>  
              </div>
              <div class="espacio"></div>
              <?php 
              $fotos_informe = ORM::for_table('documentos_informe')->find_many();
              if(isset($informe_edit) && $fotos_informe != null){   ?>
                  <div class="contenedor_doc">
                    <?php foreach($fotos_informe as $fotos){ ?>
                    <input type="hidden" id="documento" value="<?=$fotos->url?>">
                    <img src="img/ico_eliminar.png" class="cursor" id="btn_eliminar_documento">
                    <a href="<?='uploads/'.$informe_edit->id.'/'.$fotos->url?>" target="_blank"><img src="<?='uploads/'.$informe_edit->id.'/'.$fotos->url?>" class="fotos_informe"></a>
                    <?php } ?>
                  </div>
                  <?php } ?>
              <div>
                <label class="color_plomo">Fotos del Informe</label>
                <form action="services/photoupload.php" class="dropzone" id="my-dropzone" method="POST"></form>
              </div>
              <div class="espacio"></div>
              <div class="center">
                <div id="btn_guardar" class="css_btn">GUARDAR</div>
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
