<?php
require_once '../config/database.php';

session_name("LoyolaReportes");
session_start();

if (!isset($_SESSION['usuario'])) {  
  die("sin_sesion");
}

$usuario = $_SESSION['usuario'];

if ($usuario->rol != "gerente") {  
  die("sin_sesion");
}
$instituciones = ORM::for_table('institucion')->find_many();

$id_institucion = 1;
if( isset($_GET["id_institucion"]) ){ 
  $id_institucion = $_GET["id_institucion"];
}
$texto="";
$where_texto = "";
if( isset( $_GET['buscar_texto'] ) ){
  $texto = $_GET['buscar_texto']; 
  $where_texto = " AND ( LOWER(codigo) LIKE LOWER('%$texto%') OR LOWER(detalle) LIKE LOWER('%$texto%') ) ";
}
$pagina_actual = 0;
if( isset($_GET["pagina_actual"]) ){
  $pagina_actual = $_GET["pagina_actual"];
}
$items_x_pagina = 5;

$total_items = ORM::for_table('informe')
        ->raw_query(
        " SELECT count(id) total ".
        " FROM informe ".
        " WHERE deleted_at IS NULL AND estado='pendiente' AND id_institucion= $id_institucion 
        $where_texto")
         ->find_one();

$total_items = $total_items->total;

$informes = ORM::for_table('informe')
        ->raw_query(
        " SELECT * ".
        " FROM informe ".
        " WHERE deleted_at IS NULL AND estado='pendiente' AND id_institucion= $id_institucion ".
        $where_texto.
        " ORDER BY created_at desc ".
        " LIMIT ".($pagina_actual*$items_x_pagina).", $items_x_pagina")
        ->find_many();

?>
<div class="buscador row">
  <div class="col-4">
    <label>Institucion:</label>
    <select id="institucion">                                                                                 
      <?php foreach ( $instituciones as $institucion ){ ?>
        <option value="<?=$institucion->id?>"
          <?=isset($_GET["id_institucion"])?($institucion->id==$id_institucion?'selected':''):''?>><?=$institucion->nombre?></option>
          <?php } ?>
    </select>
  </div>
  <div class="col">
    <label class="left">Buscar: </label>
    <div class="left"><input id="buscar_texto" type="text" value='<?=isset( $_GET['buscar_texto'] )?$_GET['buscar_texto']:''?>'/></div>
    <div id="btn_buscar" class="left cursor"><img src="img/ico_buscar.png" title="Busqueda por código, periodo"/></div>
  </div>
</div>
<?php
if (count($informes) <= 0) {
  echo "No hay informes pendientes";
} else {
?>
<div class="css_listado">
  <div class="row css_tabla">           
    <div class="col-lg">Nro.</div>     
    <div class="col-lg">Codigo</div>     
    <div class="col-lg">Detalle</div>
    <div class="col-lg">Responsable</div>
    <div class="col-lg">Tiempo Restante</div>   
    <div class="col-lg">Fecha Límite</div>
    <div class="col-lg">Acciones</div>
  </div>

  <?php
  $index = 1;
  foreach ($informes as $informe) { 
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
    ?>
    <div class="row css_row">
      <div class="col-lg"><?= $index ++ ?></div>
      <div class="col-lg"><?= $informe->codigo ?></div>      
      <div class="col-lg"><?= $informe->detalle ?></div> 
      <div class="col-lg"><?= $responsable ?></div> 
      <div class="col-lg"><?=$tiempo_restante?></div>   
      <div class="col-lg"><?= (new DateTime($informe->fecha_limite))->format("d-m-Y") ?></div>    
      <div class="col-lg"><a href="registro_informe.php?id_informe=<?=$informe->id?>" class="btn_opciones">Editar informe</a></div>
    </div>  
  <?php } ?>                              

<div class="espacio"></div>
<?php } ?>
</div>
<?php include("../paginacion.php"); ?>          

 