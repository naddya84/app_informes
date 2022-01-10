<?php
require_once 'config/database.php';

if(isset($_GET['session'])){
session_name("LoyolaReportes");
session_start();

if (!isset($_SESSION['usuario'])) {  
  die("sin_sesion");
}

$usuario = $_SESSION['usuario'];
}
$final = new DateTime();  
$final->modify("+ 25 day");  
$fecha_final = $final->format("Y-m-d");  
$fecha_inicial = date('Y-m-d', time()); 

$informes = ORM::for_table('informe')
        ->raw_query(
        " SELECT * ".
        " FROM informe ".
        " WHERE deleted_at IS NULL AND estado='pendiente' AND id_usuario= $usuario->id".
        " AND fecha_limite between '$fecha_inicial' AND '$fecha_final 23:59:59' ")
        ->find_many();
?>
<!--pop up alertas -->
<div id="fondo_pop" class="overlay_alerta"></div>
<div class="center">
  <div id="mensaje" class="popup_alerta">
    <div class="center"><img src="img/ico_alerta.png" class="img_alerta"></div>
    <div class="content-popup_alerta">
    <?php 
    if(count($informes) > 0){ 
      foreach ($informes as $informe) { 
        $limite = new DateTime($informe->fecha_limite);
        $d1= new DateTime(); 
        $d2= $limite;

        $dias = $horas = $minutos = "";
      if( $d1 < $d2 ){ 
        $interval= $d1->diff($d2);    
        if( $interval->days > 0 ){
          $dias = $interval->days." dias ";
        }            
        if( $interval->h > 0 ){
          $horas = $interval->h." hrs.";      
        }    
        if( $interval->i > 0 ){
          $minutos = $interval->i." min.";      
        }    
        $tiempo_restante = "Tiene $dias $horas $minutos para enviar";
      } else {
        $tiempo_restante = "Fuera de Tiempo";
      }
      if($dias > 0 && $dias < 26){ ?>
      <div class="alerta_lila">
        <div class="texto_alerta"><?=$informe->detalle?></div>
        <div class="row">
          <div class="col-8 texto_tiempo"><?=$tiempo_restante?></div>
          <div class="col"><a href="detalle_informe.php?id_informe=<?=$informe->id?>" class="btn_detalle">Ver Detalle</a> </div>
        </div>
      </div>
      <?php }
      if($dias < 0){ ?>
      <div class="alerta_naranja">
        <div class="texto_alerta"><?=$informe->detalle?></div>
        <div class="row">
          <div class="col-8 texto_tiempo"><?=$tiempo_restante?></div>
          <div class="col"><a href="detalle_informe.php?id_informe=<?=$informe->id?>" class="btn_detalle">Ver Detalle</a> </div>
        </div>
      </div>
      <?php } 
      if($tiempo_restante == "Fuera de Tiempo"){?>
      <div class="alerta_roja">
        <div class="texto_alerta"><?=$informe->detalle?></div>
        <div class="row">
          <div class="col-8 texto_tiempo"><?=$tiempo_restante?></div>
          <div class="col"><a href="detalle_informe.php?id_informe=<?=$informe->id?>" class="btn_detalle">Ver Detalle</a> </div>
        </div>
      </div>
      <?php } 
      }
    } else{ ?>
      <div id="texto_mensaje">En este momento no existen alertas de informes pendientes</div>
    <?php }?>
    </div>
  </div>
</div>
