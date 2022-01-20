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
$responsables = ORM::for_table('usuario')->find_many();
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">  
    <link href="css/jquery-ui.css" media="screen" rel="stylesheet" type="text/css" >
    <link href="css/style.css?v=1" media="screen" rel="stylesheet" type="text/css" />
    <link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">

    <script src="js/libs/jquery-3.3.1.min.js"></script>
    <script src="js/libs/jquery-ui.js"></script>        
    <script src="bootstrap/js/bootstrap.min.js"></script>    
    <script type="text/javascript" src="js/libs/bootstrap-datetimepicker.js" charset="UTF-8"></script>
    <script type="text/javascript" src="js/libs/locales/bootstrap-datetimepicker.fr.js" charset="UTF-8"></script>
    <script src="js/registro_informe.js?v=1.0"></script>
    
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
      <input type="hidden" id="rol_usuario" value="<?=$usuario->rol?>">
      <div class="fondo_paginas">
        <div class="espacio"></div>
          <div class="container">
          <div class="card bg_card">
            <div class="card-body">
              <div class="espacio"></div>
              <div class="center"><h3 class="font_titulo"><?= isset($informe_edit)?"Datos Informe":"Nuevo Informe"?></h3></div>
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
                  <label class="font_dato">	Archivos Electronicos:</label>
                  <input type="text" id="archivos_electronicos" class="form-control input_form" <?= isset($informe_edit) ? "value='" .$informe_edit->archivos_electronicos. "'" : "" ?>>
                </div>
              </div>
              <div class="form-group row">
                <div class="col">
                  <label class="font_dato">Información Remitida:</label>
                  <input type="text" id="informacion_remitida" class="form-control input_form" <?= isset($informe_edit) ? "value='" .$informe_edit->informacion_remitida. "'" : "" ?>>
                </div>
                <div class="col">
                  <label class="font_dato">Sección:</label>
                  <input type="text" id="seccion" class="form-control input_form" <?= isset($informe_edit) ? "value='" .$informe_edit->seccion. "'" : "" ?>>
                </div>
              </div>
              <div class="form-group row">
                <div class="col">
                  <label class="font_dato">Normativa:</label>
                  <input type="text" id="normativa" class="form-control input_form" <?= isset($informe_edit) ? "value='" .$informe_edit->normativa. "'" : "" ?>>
                </div>
                <div class="col">
                  <label class="font_dato">Articulo:</label>
                  <input type="text" id="articulo" class="form-control input_form" <?= isset($informe_edit) ? "value='" .$informe_edit->articulo. "'" : "" ?>>
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
                      <?=isset($informe_edit)?($informe_edit->id_institucion==$institucion->id?'selected':''):''?>><?=$institucion->nombre?></option>
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
                  <label class="color_plomo">Plazo de Envio: </label>
                  <div class="input-group date form_datetime col-lg-3 col-md-5 margen_fecha"  data-date-format="yyyy-mm-dd hh:ii" >
                    <input id="plazo_envio" class="form-control input_form" size="16" type="text" readonly  <?= isset($informe_edit) ? "value='" .$informe_edit->plazo_envio. "'" : "" ?>/>
                    <span class="input-group-addon"><span class="css_remove glyphicon-remove"><img src="img/ico_remove_datatime.png"></span></span>
                    <span class="input-group-addon"><span class="glyphicon-th"><img src="img/ico_datetime.png"></span></span>
                  </div>
                  <input type="hidden" id="dtp_input1" value="" />  
                </div>  
              </div>
              <div>
              <?php $tiempo_realizar_informe = "";
              if(isset($informe_edit)){
                if($informe_edit->tipo_periodo == "diario") $max = 1;
                if($informe_edit->tipo_periodo == "semanal") $max = 5;
                if($informe_edit->tipo_periodo == "mensual") $max = 20;
                if($informe_edit->tipo_periodo == "semestral") $max = 150;
                if($informe_edit->tipo_periodo == "trimestral") $max = 90;
                if($informe_edit->tipo_periodo == "anual") $max = 50;
                if($informe->tiempo_realizar != null){
                $tiempo_realizar_informe = json_decode($informe->tiempo_realizar);
                }
              }
              ?>
              <label class="color_plomo">Tiempo para realizar el informe:</label>
              <div>
                <span>Días:</span><input type="number" id="dias" <?=($tiempo_realizar_informe!="" && $tiempo_realizar_informe->dias != "")?"value='".$tiempo_realizar_informe->dias."'":""?>" min="0" max="<?=$max?>">
                <span>Horas:</span><input type="number" id="horas" <?=($tiempo_realizar_informe!="" && $tiempo_realizar_informe->horas != "")?"value='".$tiempo_realizar_informe->horas."'":""?>" min="0" max="24">
              </div>
              </div>
              <div class="espacio"></div>
              <div class="row">
                <div class="col-6">
                  <label class="color_plomo">Responsable1:</label>
                  <select id="id_usuario" class="form-control input_form">
                    <option value="">Sin Asignar</option>                                                                                  
                    <?php foreach ( $responsables as $responsable ){ ?>
                    <option value="<?=$responsable->id?>"
                      <?=isset($informe_edit)?($informe_edit->id_usuario==$responsable->id?'selected':''):''?>><?=$responsable->fullname?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-6">
                  <label class="color_plomo">Responsable2:</label>
                  <select id="id_usuario_2" class="form-control input_form">
                    <option value="">Sin Asignar</option>                                                                                  
                    <?php foreach ( $responsables as $responsable ){ ?>
                    <option value="<?=$responsable->id?>"
                      <?=isset($informe_edit)?($informe_edit->id_usuario==$responsable->id?'selected':''):''?>><?=$responsable->fullname?></option>
                    <?php } ?>
                  </select>
                </div>              </div>
              <div class="espacio"></div>
              <div class="center">
                <div id="btn_guardar" class="btn css_btn">GUARDAR</div>
                <div class="margen_left"></div>
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
