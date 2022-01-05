<?php
require_once 'config/database.php';

$informes = ORM::for_table('informe')
        ->raw_query(
        " SELECT * ".
        " FROM informe ".
        " WHERE deleted_at IS NULL AND estado='pendiente'")
        ->find_many();
?>
<!--pop up alertas -->
<div id="fondo_pop" class="overlay_alerta"></div>
<div class="center">
  <div id="mensaje" class="popup_alerta">
    <div class="center"><img src="img/ico_alerta.png" class="img_alerta"></div>
    <div class="content-popup_alerta">
    <?php foreach ($informes as $informe) { 
      $usuario = ORM::for_table('usuario')->where("id", $informe->id_usuario)->find_one();
      $responsable = $usuario->fullname;
         
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
      if($dias > 0 && $dias < 5){ ?>
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
    }?>
    </div>
  </div>
</div>