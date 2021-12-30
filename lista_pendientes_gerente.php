<?php
require_once 'config/database.php';

session_name("LoyolaReportes");
session_start();

if (!isset($_SESSION['usuario'])) {  
  die("sin_sesion");
}

$usuario = $_SESSION['usuario'];

if ($usuario->rol != "gerente") {  
  die("sin_sesion");
}

$pagina_actual = 0;
if( isset($_GET["pagina_actual"]) ){
  $pagina_actual = $_GET["pagina_actual"];
}
$items_x_pagina = 2;

$total_items = ORM::for_table('informe')
        ->raw_query(
        " SELECT count(id) total ".
        " FROM informe ".
        " WHERE deleted_at IS NULL AND estado='pendiente'")
         ->find_one();

$total_items = $total_items->total;

$informes = ORM::for_table('informe')
        ->raw_query(
        " SELECT * ".
        " FROM informe ".
        " WHERE deleted_at IS NULL AND estado='pendiente' ".
        " ORDER BY created_at desc ".
        " LIMIT ".($pagina_actual*$items_x_pagina).", $items_x_pagina")
        ->find_many();

?>
<div style="padding-top: 3%"></div>
<div class="buscador">
  <label class="left">Buscar: </label>
  <div class="left"><input id="texto_buscar_r" type="text" value=''/></div>
  <img id='btn_buscar' src="img/ico_buscar.png" class="left" title="Busqueda por código, periodo"/>
</div>
<div class="espacio"></div>
<?php
if (count($informes) <= 0) {
  echo "No hay reclamos";
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
    ?>
    <div class="row css_row">
      <div class="col-lg"><?= $index ++ ?></div>
      <div class="col-lg"><?= $informe->codigo ?></div>      
      <div class="col-lg"><?= $informe->detalle ?></div> 
      <div class="col-lg"><?= $responsable ?></div> 
      <div class="col-lg"></div>   
      <div class="col-lg"><?= (new DateTime($informe->fecha_limite))->format("d-m-Y") ?></div>    
      <div class="col-lg"></div>
    </div>  
  <?php } ?>                              

<div class="espacio"></div>
<?php } ?>
</div>
<?php include("paginacion.php"); ?>          

 