<div class="cabecera">
  <img src="img/logo.png" class="logo_home">
  <div class="css_usuario">
    <div class="row">
      <div class="col-2">
        <?php if($usuario->foto){  ?>
          <img src="uploads/foto_perfil/<?= $usuario->id ?>/<?= $usuario->foto ?>" class="perfil">
        <?php }else{ ?>
          <img src='img/perfil.png' class="perfil"> 
        <?php } ?>
      </div>
      <div class="col css_datos_usuario">
        <div class="texto_usuario"><a href="lista_notificaciones.php" class="texto_rol"><?= $usuario->fullname ?></a></div>
        <span class="texto_rol"><?=nombre_rol($usuario->rol)?></span>
      </div>  
    </div>
  </div>

<input id="rol" type="hidden" value="<?= $usuario->rol ?>" />

<?php if (strcmp($usuario->rol, "jefe") == 0 ) { ?>

  <div class="contenedor_menu" id="menu_jefe">
    <a class="css_inicio seleccionado left" href="home_<?= $usuario->rol ?>.php" >HOME</a>  
    <a class="css_inicio left" id ="reportes" href="reportes.php">REPORTES</a>
    <a class="css_inicio left" id ="notificaciones" href="notificaciones.php">NOTIFICACIÓN</a>
    <a class="css_inicio left" href="cerrar_sesion.php">SALIR</a>
  </div>  
<?php } ?>
 
<?php if (strcmp($usuario->rol, "gerente") == 0) { ?>
  <div class="contenedor_menu" id="menu_gerente">
    <a class="css_inicio seleccionado left" href="home_<?= $usuario->rol ?>.php" >HOME</a>  
    <a class="css_inicio left" id ="reportes" href="reportes.php">REPORTES</a>
    <a class="css_inicio left" id ="notificaciones" href="notificaciones.php">NOTIFICACIÓN</a>
    <a class="css_inicio left" href="cerrar_sesion.php">SALIR</a>
  </div>      
<?php } ?> 
<?php if (strcmp($usuario->rol, "administrador") == 0) { ?>
  <div class="contenedor_menu" id="menu_gerente">
    <a class="css_inicio seleccionado left" href="home_administrador.php" >HOME</a>  
    <a class="css_inicio left" id ="n_informes" href="registro_informe.php">NUEVO INFORME</a>
    <a class="css_inicio left" id ="n_gestion" href="administrar_informes.php">ADMIN. GESTIÓN</a>
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