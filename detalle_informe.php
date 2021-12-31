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
}
$institucion = ORM::for_table('institucion')->where("id","id_institucion")->find_one();
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">  
    <link href="css/jquery-ui.css" media="screen" rel="stylesheet" type="text/css" >
    <link href="css/style.css?v=1" media="screen" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="css/jquery.mCustomScrollbar.css" />
    
    <script src="js/libs/jquery-3.3.1.min.js"></script>
    <script src="js/libs/jquery-ui.js"></script>        
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="js/libs/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="js/detalle_informe.js"></script>
    
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
      <div class="fondo_paginas">
        <div class="espacio"></div>
          <div class="container">
          <div class="card bg_card">
            <div class="card-body">
              <div class="espacio"></div>
              <div class="center"><h3 class="font_titulo">Detalle Informe</h3></div>
              <div class="espacio"></div>
              <div class="form-group">
                <label class="font_dato">Detalle: </label><span><?=$informe->detalle?></span>
              </div>
              <div class="espacio"></div>
              <div class="row">
                <div class="col-6">
                  <label class="font_dato">Código:</label></label><span><?=$informe->codigo?></span>
                </div>
                <div class="col-6">
                  <label class="font_dato">Periodo:</label></label><span><?=$informe->	tipo_periodo?></span>
                </div>
              </div>
              <div class="espacio"></div>
              <div class="form-group row">
                <div class="col">
                  <label class="font_dato">Sistema:</label></label><span><?=$informe->sistema_modulo?></span>
                </div>
                <div class="col">
                  <label class="font_dato">Avance(%):</label></label><span><?=$informe->avance?></span>
                </div>
              </div>
              <div class="espacio"></div>
              <div class="form-group row">
                <div class="col-6">
                  <label class="font_dato">Institucion:</label></label><span><?=$institucion?></span>
                </div>
                <div class="col">
                  <label class="font_dato">E-mail:</label></label><span><?=$informe->complemetacion?></span>
                </div>
              </div>
              <div class="espacio"></div>
              <div class="form-group row">
                <div class="col">
                  <label class="font_datos">Tipo de Envio:</label></label><span><?=$informe->tipo_envio?></span>
                </div>
                <div class="col">
                  <label class="font_dato">Multa:</label></label><span><?=$informe->multa==1?"Si":"No"?></span>
                </div>
              </div>
              <div class="espacio"></div>
              <div class="form-group row">
                <div class="col-6">
                  <label class="font_datos">Fecha limite: </label></label><span><?=$informe->fecha_limite?></span>
              </div>
              <div class="espacio"></div>
              <?php 
              $fotos_informe = ORM::for_table('documentos_informe')->where("id_informe",$informe->id)->find_many();
              if($fotos_informe != null){   ?>
              <div class="espacio"></div>
                <div class="color_plomo">Fotos del Informe</div>
                <div class="contenedor_doc">
                  <div id="scroll_fotos">
                    <?php foreach ($fotos_informe as $foto){ ?>
                      <a href="<?= $foto->url?>" target="_blank"><img src = "<?='uploads/'.$informe->id.'/'.$foto->url?>" class="fotos"></a>
                    <?php } ?>
                  </div>
                </div>
                <?php } ?>
              <div class="espacio"></div>
              <div class="center">
              <a " href="javascript:history.back()" id="btn_volver" class="css_btn">Volver</a>
              </div>
            </div>
          </div>
        </div>
        <br>
      </div>
    </div>
    <?php include 'pie_pagina.php'; ?>
    <!--Ventana emergente contacto -->
    <div id="div_cargando" class="fondo_block">
      <img src="img/cargando.gif" class="img_cargando">
    </div>
  </body>
</html>
