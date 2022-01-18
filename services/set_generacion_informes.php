<?php
/*creacion de informes  mensuales*/
  $gestion= (new DateTime())->format("Y");
  $mes= (new DateTime())->format("m");
  $dia= (new DateTime())->format("d");
  
  $informes_mensuales = ORM::for_table('control_informes_generados')
        ->raw_query(
        " SELECT * ".
        " FROM control_informes_generados ".
        " WHERE gestion=$gestion AND nro_mes=$mes AND tipo= 'mensual'")
        ->find_one();
  
  if($informes_mensuales == null){
    $informes_mensuales = ORM::for_table('informe_maestro')->where('tipo_periodo', 'mensual')->find_many();
    foreach ( $informes_mensuales as $informe_mensual ){    
      $informe_maestro = ORM::for_table('informe_maestro')->where('id', $informe_mensual->id)->find_one();
      $informe = ORM::for_table('informe')->create();
      $informe->id_informe_padre = $informe_mensual->id;
      $informe->id_usuario = $informe_maestro->id_usuario;
      if($informe_maestro->id_usuario_2 != null){
        $informe->id_usuario_2 = $informe_maestro->id_usuario_2;
      }
      $informe->gestion = $gestion;
      $informe->estado = "pendiente";

      if( !$informe->save() ){  
        ORM::get_db()->rollBack();    
        echo json_encode(array(
            "success" => false,      
            "reason" => "No se puedo crear el informe"
        ));  
        die();
      }
    }
    $indicador_generados = ORM::for_table('control_informes_generados')->create();
    $indicador_generados->nro_mes = $mes;
    $indicador_generados->gestion = $gestion;
    $indicador_generados->tipo = 'mensual';
    if( !$indicador_generados->save() ){  
      ORM::get_db()->rollBack();    
      echo json_encode(array(
          "success" => false,      
          "reason" => "No se puedo crear el control"
      ));  
      die();
    }
  }
  /*generar informes semanales*/
  $fecha = new DateTime();
  $numero_semana = $fecha->format('W'); //Número de la semana del año, la semanas comienzan los lunes 
  $informes_semanales = ORM::for_table('control_informes_generados')
        ->raw_query(
        " SELECT * ".
        " FROM control_informes_generados ".
        " WHERE gestion=$gestion AND nro_mes=$mes AND tipo= 'semanal' AND nro_semana=$numero_semana")
        ->find_one();
  if($informes_semanales == null){
    $informes_semanales = ORM::for_table('informe_maestro')->where('tipo_periodo', 'semanal')->find_many();
    foreach ( $informes_semanales as $informe_semanal ){    
      $informe_maestro = ORM::for_table('informe_maestro')->where('id', $informe_semanal->id)->find_one();
      $informe = ORM::for_table('informe')->create();
      $informe->id_informe_padre = $informe_maestro->id;
      $informe->id_usuario = $informe_maestro->id_usuario;
      if($informe_maestro->id_usuario_2 != null){
        $informe->id_usuario_2 = $informe_maestro->id_usuario_2;
      }
      $informe->gestion = $gestion;
      $informe->estado = "pendiente";

      if( !$informe->save() ){  
        ORM::get_db()->rollBack();    
        echo json_encode(array(
            "success" => false,      
            "reason" => "No se puedo crear el informe"
        ));  
        die();
      }
    }
    $control_generados = ORM::for_table('control_informes_generados')->create();
    $control_generados->nro_semana = $numero_semana;
    $control_generados->gestion = $gestion;
    $control_generados->tipo = 'semanal';
    $control_generados->nro_mes = $mes;
    if( !$control_generados->save() ){  
      ORM::get_db()->rollBack();    
      echo json_encode(array(
          "success" => false,      
          "reason" => "No se puedo crear el informe"
      ));  
      die();
    }
  } 