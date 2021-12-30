<?php
require_once '../config/database.php';

session_name("BisaNitro");
session_start();

if (!isset($_SESSION['usuario'])) {
  die("sin_sesion");
}

$usuario = $_SESSION['usuario'];

$tabla = "reclamo";
$pagina_actual = 0;
if( isset($_GET["pagina_actual"]) ){
  $pagina_actual = $_GET["pagina_actual"];
}
$items_x_pagina = 5;

$where_like = "true";
$texto = "";
$columna_orden = "fecha_reclamo";
$orden = "desc";


if( isset( $_GET['texto_buscar'] ) ){
  $texto = $_GET['texto_buscar'];
  $prohibidas = array("<",">", "\'", "\"");
  $texto = str_replace($prohibidas, "", $texto);
  $where_like = " ( numero_placa like '%$texto%' or nombre_asegurado like '%$texto%' or numero_reclamo like '%$texto%' ) ";
}

if( isset( $_GET['columna_orden'] ) ){
  $columna_orden = $_GET['columna_orden'];
}

if( isset( $_GET['orden'] ) ){
  $orden = $_GET['orden'];
}

$total_items = ORM::for_table($tabla)
        ->where(array(
            'estado' => "finalizado",
            'ciudad' => $usuario->ciudad        
        ))->where_raw( $where_like )
          ->where_null('deleted_at')->count();

/*if(strlen($where_like) > 0 ){
  $total_items = $total_items
          ->where_raw( $where_like )
          ->where_null('deleted_at')->count();
} else {
  $total_items = $total_items          
          ->where_null('deleted_at')->count();
}*/

$items = ORM::for_table($tabla)
        ->where(array(
            'estado' => "finalizado",
            'ciudad' => $usuario->ciudad        
        ))                   
        ->where_null('deleted_at')
        ->where_raw( $where_like )
        ->limit( $items_x_pagina )
        ->offset( $pagina_actual*$items_x_pagina );

/*if(strlen($where_like) > 0 ){
  $items = $items->where_raw( $where_like )
          ->limit( $items_x_pagina )
        ->offset( $pagina_actual*$items_x_pagina );
} else {
  $items = $items->limit( $items_x_pagina )
        ->offset( $pagina_actual*$items_x_pagina );
}*/

if( $orden == "desc"){
  $items = $items->order_by_desc($columna_orden)->find_many();   
} else {
  $items = $items->order_by_asc($columna_orden)->find_many();   
}
?>
<div style="padding-top: 3%"></div>
<div class="css_listado">
   <div class="buscador">
    <label class="left font_amarillo">Buscar: </label>
    <div class="left margen_img_usr"><input id="texto_buscar_r" type="text" value='<?=$texto?>'/></div>
    <img id='btn_buscar' src="img/ico_buscarP.png" class="left margen_img_usr"  title="Busqueda por Asegurado, Placa y Nro de Reclamo" style="cursor:pointer"/>
  </div>
  <div class="margen"></div>
<?php
if (count($items) <= 0) {
  echo "No hay reclamos finalizados";
} else {
  ?>  
  <div class="row cabecera_tabla">  
    <div class="col-lg-1 ocultar_cabecera">Nº </div>
    <div class="col-lg ocultar_cabecera btn_columna_list <?=$columna_orden=="numero_placa"?'columna_sel_list':''?>" data-columna="numero_placa" data-orden="<?=($orden=="desc"?'asc':'desc')?>"><?=$columna_orden=="numero_placa"?($orden=="desc"?'&#9650;':'&#9660;'):''?> Placa</div> 
    <div class="col-lg ocultar_cabecera btn_columna_list <?=$columna_orden=="nombre_asegurado"?'columna_sel_list':''?>" data-columna="nombre_asegurado" data-orden="<?=($orden=="desc"?'asc':'desc')?>"><?=$columna_orden=="nombre_asegurado"?($orden=="desc"?'&#9650;':'&#9660;'):''?>Nombre Asegurado</div>
    <div class="col-lg ocultar_cabecera">Vehículo</div> 
    <div class="col-lg ocultar_cabecera btn_columna_list <?=$columna_orden=="numero_reclamo"?'columna_sel_list':''?>" data-columna="numero_reclamo" data-orden="<?=($orden=="desc"?'asc':'desc')?>"><?=$columna_orden=="numero_reclamo"?($orden=="desc"?'&#9650;':'&#9660;'):''?> Nº Reclamo</div>
    <div class="col-lg ocultar_cabecera">UTC</div> 
    <div class="col-lg ocultar_cabecera">Cotizador</div> 
    <div class="col-lg ocultar_cabecera btn_columna_list <?=$columna_orden=="fecha_siniestro"?'columna_sel_list':''?>" data-columna="fecha_siniestro" data-orden="<?=($orden=="desc"?'asc':'desc')?>"><?=$columna_orden=="fecha_siniestro"?($orden=="desc"?'&#9650;':'&#9660;'):''?> Fecha de Siniestro</div>
    <div class="col-lg ocultar_cabecera"></div>  
    <div class="col-lg ocultar_cabecera"></div>  
  </div>

  <?php
  $index=0 +($pagina_actual*$items_x_pagina);
  foreach ($items as $reclamo) {
    $reclamo_aprobado = ORM::for_table("reclamo_aprobado")
            ->where("id_reclamo", $reclamo->id)
            ->where_not_equal("estado", "anulado")
            ->where("rol_aprobacion", "supervisor")
            ->find_one();
    
    $obra_vendida_aprobada = ORM::for_table("obra_vendida_aprobada")
                ->where('id_reclamo', $reclamo->id)
                ->where_equal("estado", "aprobado")
                ->find_one(); 
        
    $proforma = ORM::for_table("proforma")->find_one($reclamo->id_proforma);
    if( $reclamo->id_utc > 0 ){
      $utc = ORM::for_table("usuario")->find_one( $reclamo->id_utc );
    } else {
      $utc = null;
    }
    if( $reclamo->id_cotizador > 0 ){
      $cotizador = ORM::for_table("usuario")->find_one( $reclamo->id_cotizador );
    } else {
      $cotizador = null;
    }
    $index++;
    ?>
    <div class="row css_row_azul">
      <div class="col-lg-1"><span class="desc_elemento_p left">Nº:&nbsp; </span><?=$index?></div>
      <div class="col-lg"><span class="desc_elemento_p left">Placa:&nbsp; </span><?= $reclamo->numero_placa ?></div>
      <div class="col-lg"><span class="desc_elemento_p left">Nombre Asegurado:&nbsp; </span><?= $reclamo->nombre_asegurado ?></div>
      <div class="col-lg"><span class="desc_elemento_p left">Vehículo:&nbsp; </span><?= $reclamo->marca." ".$reclamo->tipo ?></div> 
      <div class="col-lg"><span class="desc_elemento_p left">Nº de Reclamo:&nbsp; </span><?= $reclamo->numero_reclamo ?></div>
      <div class="col-lg"><span class="desc_elemento_p left">UTC:&nbsp; </span><?= $utc!=null?$utc->fullname:'-' ?></div>  
      <div class="col-lg"><span class="desc_elemento_p left">Cotizador:&nbsp; </span><?= $cotizador!=null?$cotizador->fullname:'-' ?></div>    
      <div class="col-lg"><span class="desc_elemento_p left">Fecha Siniestro:&nbsp; </span><?= (new DateTime($reclamo->fecha_siniestro))->format("d-m-Y") ?></div>   
      <div class="col-lg">
        <a href="ver_detalle_reclamo_inspector.php?id_reclamo=<?=$reclamo->id ?>"><img src="img/ico_ver_reclamo.png" class="ocultar_imagen_movil"><span class="text_plomo ajuste_texto">Ver Reclamo</span></a>
      </div>          
      <div class="col-lg">
        <?php if($reclamo_aprobado != null) { ?>  
          <a href="lista_recuperos.php?id_reclamo=<?= $reclamo->id ?>"><img src="img/ico_recuperos.png" class="ocultar_imagen_movil"><span class="text_plomo">Administrar Recuperos</span></a>
        <?php } ?> 
      </div> 
    </div>
  <?php } ?>                  
  <div class="margen"></div>
</div>
<?php }

include("paginacion.php");
?> 