<?php

require_once '../../config/database.php';
require_once '../../excel/PHPExcel.php';


session_name("LoyolaReportes");
session_start();

if (!isset($_SESSION['usuario'])) {
  die("Sin sesion ");
}

/*css */
$estiloTitulo = array(
    'font' => array(
        'name'      => 'Calibri',
        'bold'      => true,
        'italic'    => false,
        'strike'    => false,
        'size'      => 12,
        'color'     => array(
            'rgb' => '000000'
        )
    ),
    'borders' => array(
        'left' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN ,
                ) 
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        'rotation' => 0,
        'wrap' => TRUE
    )
);

$estiloTituloReporte = array(
    'font' => array(
        'name'      => 'Calibri',
        'bold'      => true,
        'italic'    => false,
        'strike'    => false,
        'size'      => 12,
        'color'     => array(
            'rgb' => '000000'
        )
    ),
    'fill' => array(
      'type'  => PHPExcel_Style_Fill::FILL_SOLID,
      'color' => array(
            'argb' => '99cccc')
  ),
    'borders' => array(
        'left' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN ,
                ) 
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        'rotation' => 0,
        'wrap' => TRUE
    )
);

$css_fila = array(
      'font' => array(
          'name' => 'Calibri',
          'bold' => false,
          'italic' => false,
          'strike' => false,
          'size' => 11,
          'color' => array(
              'rgb' => '000000'
          )
      ),
      'borders' => array(
          'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
          )
      ),
      'alignment' => array(
          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
          'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          'rotation' => 0,
          'wrap' => TRUE
      )
);

$data = json_decode($_GET['data']);

$where_institucion = " AND id_institucion = $data->id_institucion";
$institucion = ORM::for_table('institucion')->where('id',$data->id_institucion)->find_one();
 
$where_fecha = "";
$fecha_inicial = $data->fecha_inicial;
$fecha_final = $data->fecha_final;
if( $fecha_inicial != "" && $fecha_final != "" ){    
  $where_fecha = " AND created_at between '$fecha_inicial' AND '$fecha_final 23:59:59' ";
}
$informes = ORM::for_table('informe')
        ->raw_query(
        " SELECT * from informe ".
        " WHERE deleted_at IS NULL".
        $where_institucion.
        $where_fecha.          
        " ORDER BY  created_at asc ")
        ->find_many();

if( count($informes) > 0 ) { 
  $objPHPExcel = new PHPExcel();
  // Se asignan las propiedades del libro
  $objPHPExcel->getProperties()->setCreator("Cooperativa Loyola") // Nombre del autor
          ->setLastModifiedBy("Cooperativa Loyola") //Ultimo usuario que lo modificó
          ->setTitle("REPORTE X INSTITUCION ") // Titulo
          ->setSubject("REPORTE X INSTITUCION ") //Asunto
          ->setDescription("REPORTE X INSTITUCION ") //Descripción
          ->setKeywords("REPORTE X INSTITUCION ") //Etiquetas
          ->setCategory("REPORTE X INSTITUCION");


  $tituloReporte = "REPORTE X ESTADO";
  $fila_1 = "REPORTE X ESTADO   ";
  $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue('D1', $fila_1);

  $fila_2 = "COOPERATIVA LOYOLA R.L.";
  $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue('D2', $fila_2);
  
  if($fecha_inicial !== "" && $fecha_final !== ""){
    $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue('C4', "Fechas Seleccionadas: ");
    $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue('D4', (new DateTime($data->fecha_inicial ))->format("d-m-Y")." - ".(new DateTime($data->fecha_final ))->format("d-m-Y"));   
  } 
 
  $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue('C5', "Institución:");
  $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue('D5',$institucion->nombre );   
  
  $titulosColumnas = array('Nº', 'CODIGO', 'DETALLE', 'TIPO DE ENVIO', 'PERIODO','SISTEMA - MODULO', 'RESPONSABLE', 'FECHA LIMITE','TIEMPO RESTANTE', 'AVANCE','ESTADO');
 
  // Se agregan los titulos del reporte
  //Titulo de las columnas
  $objPHPExcel->setActiveSheetIndex(0)    
      ->setCellValue('A6',  $titulosColumnas[0])  
      ->setCellValue('B6',  $titulosColumnas[1])
      ->setCellValue('C6',  $titulosColumnas[2])
      ->setCellValue('D6',  $titulosColumnas[3])
      ->setCellValue('E6',  $titulosColumnas[4])
      ->setCellValue('F6',  $titulosColumnas[5])
      ->setCellValue('G6',  $titulosColumnas[6])
      ->setCellValue('H6',  $titulosColumnas[7])
      ->setCellValue('I6',  $titulosColumnas[8])
      ->setCellValue('J6',  $titulosColumnas[9])
      ->setCellValue('K6',  $titulosColumnas[10]);

  $i=7;
  $numeral= 0; $responsable="";
  foreach ($informes as $informe) { 
    $responsable = ORM::for_table("usuario")->select("fullname")->find_one( $informe->id_usuario );
    $tiempo_restante = "";
            $responsable = ORM::for_table("usuario")->select("fullname")->find_one( $informe->id_usuario );
            
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
            $restante = "Tiene $dias $horas $minutos para enviar";
            } else {
              $restante = "Fuera de Tiempo";
            }
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A'.$i, $numeral = $numeral + 1)
        ->setCellValue('B'.$i, $informe->codigo)    
        ->setCellValue('C'.$i, $informe->detalle)    
        ->setCellValue('D'.$i, $informe->tipo_envio)      
        ->setCellValue('E'.$i, $informe->tipo_periodo)    
        ->setCellValue('F'.$i, $informe->sistema_modulo)
        ->setCellValue('G'.$i, $responsable->fullname)
        ->setCellValue('H'.$i, (new DateTime($informe->created_at ))->format("d-m-Y"))
        ->setCellValue('I'.$i, $restante)
        ->setCellValue('J'.$i, $informe->avance)
        ->setCellValue('K'.$i, $informe->estado);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i.'')->applyFromArray($css_fila);
    $i++;         
  }
  
  $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray($estiloTitulo);
  $objPHPExcel->getActiveSheet()->getStyle('A2:K2')->applyFromArray($estiloTitulo);
  $objPHPExcel->getActiveSheet()->getStyle('A6:K6')->applyFromArray($estiloTituloReporte);
  
  $objPHPExcel->getActiveSheet()->setTitle('Reporte x Institucion');

  for($i = 'A'; $i <= 'K'; $i++){
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setAutoSize(TRUE);
  }
  // Se activa la hoja para que sea la que se muestre cuando el archivo se abre
  $objPHPExcel->setActiveSheetIndex(0);

  // Se manda el archivo al navegador web, con el nombre que se indica, en formato 2007
  header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  header('Content-Disposition: attachment;filename="informes_x_institucion.xlsx"');
  header('Cache-Control: max-age=0');
  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
  $objWriter->save('php://output');
  exit;
    }  else {
      die (json_encode(array(
        "success" => false,
        "reason" => "sin_sesion"    
      )));
    }