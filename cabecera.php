<div class="cabecera">
  <img src="img/logo.png" class="logo_home">
  <div class="css_usuario">
    <div class="row tm_usuario">
      <div class="col-2">
        <?php if($usuario->foto){  ?>
          <img src="uploads/foto_perfil/<?= $usuario->id ?>/<?= $usuario->foto ?>" class="perfil">
        <?php }else{ ?>
          <img src='img/perfil.png'> 
        <?php } ?>
      </div>
      <div class="col css_datos_usuario">
        <div class="texto_usuario"><?= $usuario->fullname ?></div>
        <span class="texto_rol"><?=nombre_rol($usuario->rol)?></span>
      </div>  
    </div>
  </div>

<input id="rol" type="hidden" value="<?= $usuario->rol ?>" />

<?php if (strcmp($usuario->rol, "jefe") == 0) { ?>

  <div class="contenedor_menu" id="menu_jefe">
    <a class="css_inicio left" href="home_<?= $usuario->rol ?>.php" >HOME</a>  
    <a class="css_inicio left" href="registro_informe.php">NUEVO INFORME</a>
    <a class="css_inicio left" href="reportes.php">REPORTES</a>
    <a class="css_inicio left" href="reporte_alertas.php">NOTIFICACIÓN</a>
    <a class="css_inicio left" href="cerrar_sesion.php">SALIR</a>
  </div>  
<?php } ?>
 
<?php if (strcmp($usuario->rol, "gerente") == 0) { ?>
  <div class="contenedor_menu" id="menu_gerente">
    <a class="css_inicio left" href="home_<?= $usuario->rol ?>.php" >HOME</a>  
    <a class="css_inicio left" href="registro_informe.php">NUEVO INFORME</a>
    <a class="css_inicio left" href="reportes.php">REPORTES</a>
    <a class="css_inicio left" href="reporte_alertas.php">NOTIFICACIÓN</a>
    <a class="css_inicio left" href="cerrar_sesion.php">SALIR</a>
  </div>      
<?php } ?> 
</div>
<?php
function nombre_rol($rol){
  switch ( $rol ){
    case 'administrador': return "Administrador";
    case 'jefe': return "Jefe";
    case 'gerente': return "Gerente";
    default: return $rol;  
  }
}
?>