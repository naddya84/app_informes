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
$final->modify("+ 20 day");  
$fecha_final = $final->format("Y-m-d");  
$fecha_inicial = date('Y-m-d', time()); 

$informes = ORM::for_table('informe')
        ->raw_query(
        " SELECT inf_m.codigo, inf_m.detalle, inf.id, inf.fecha_limite ".
        " FROM informe inf".
        " LEFT JOIN informe_maestro inf_m ON ( inf_m.id = inf.id_informe_padre )".
        " WHERE inf.deleted_at IS NULL AND inf.estado='en_proceso'".
        " AND inf.id_usuario= $usuario->id ".
        " AND inf.fecha_limite between '$fecha_inicial 00:00:00' AND '$fecha_final 23:59:59' ")
        ->find_many();

?>
<!--pop up alertas -->
<input type="hidden" id="id_usuario" value="<?=$usuario->id?>">
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
      
      if( $dias == 0 &&(($horas > 1 && $horas < 24) || ($horas >= 1 && ($minutos >10 && $minutos <=60 ))) ){ ?>
      <div class="alerta_lila">
        <div class="texto_alerta"><?=$informe->detalle?></div>
        <div class="row">
          <div class="col-8 texto_tiempo"><?=$tiempo_restante?></div>
          <div class="col"><a href="detalle_informe.php?id_informe=<?=$informe->id?>" class="btn_detalle">Ver Detalle</a> </div>
        </div>
      </div>
      <?php }
      if( $dias >= 1 && $dias <= 2 ){  ?>
      <div class="alerta_naranja">
        <div class="texto_alerta"><?=$informe->detalle?></div>
        <div class="row">
          <div class="col-8 texto_tiempo"><?=$tiempo_restante?></div>
          <div class="col"><a href="detalle_informe.php?id_informe=<?=$informe->id?>" class="btn_detalle">Ver Detalle</a> </div>
        </div>
      </div>
      <?php }
      if( $dias >= 3 && $dias < 6 ){  ?>
      <div class="alerta_amarillo">
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
      } ?>
      <div class="espacio"></div>
      <div class="center"><div class="btn btn-dark" id="btn_compartir">Compartir Alertas</div></div>
      <div id="div_email" style="display:none">
        <label>Ingresa el Email del destinatario:</label>
        <input type="text" id="email" class="form-control">
        <div class="espacio"></div>
        <div class="center"><div class="btn btn-dark" id="btn_enviar_email">Enviar Email</div></div>
      </div>
    <?php } else { ?>
      <div id="texto_mensaje">En este momento no existen alertas de informes pendientes</div>
    <?php }?>
    </div>
  </div>
</div>
