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

$where_texto = "";
if( isset( $data->texto ) ){
  $texto = $data->texto; 
  $where_texto = " AND ( LOWER(codigo) LIKE LOWER('%$texto%') OR LOWER(detalle) LIKE LOWER('%$texto%') ) ";
}
$where_fecha = "";
$fecha_inicial = $data->fecha_inicial;
$fecha_final = $data->fecha_final;
if( $fecha_inicial != "" && $fecha_final != "" ){    
  $where_fecha = " AND created_at between '$fecha_inicial' AND '$fecha_final 23:59:59' ";
}
$informes = ORM::for_table('informe')
        ->raw_query(
        " SELECT * from informe ".
        " WHERE estado = 'pendiente' AND deleted_at IS NULL".
        $where_texto.
        $where_fecha.        
        " ORDER BY  created_at asc ")
        ->find_many();


if( count($informes) > 0 ) { 
  $objPHPExcel = new PHPExcel();
  // Se asignan las propiedades del libro
  $objPHPExcel->getProperties()->setCreator("Cooperativa Loyola") // Nombre del autor
          ->setLastModifiedBy("Cooperativa Loyola") //Ultimo usuario que lo modificó
          ->setTitle("REPORTE DE INFORMES PENDIENTES  ") // Titulo
          ->setSubject("REPORTE DE INFORMES PENDIENTES ") //Asunto
          ->setDescription("REPORTE DE INFORMES PENDIENTES ") //Descripción
          ->setKeywords("REPORTE DE INFORMES PENDIENTES ") //Etiquetas
          ->setCategory("REPORTE DE INFORMES PENDIENTES");


  $tituloReporte = "REPORTE DE INFORMES PENDIENTES";
  $fila_1 = "REPORTE DE INFORMES PENDIENTES    ";
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
  if($texto !== ""){
    $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue('C5', "Código/Detalle:");
    $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue('D5',$texto );   
  } 
  $titulosColumnas = array('Nº', 'CODIGO', 'DETALLE', 'TIPO DE ENVIO', 'SISTEMA - MODULO', 'RESPONSABLE', 'FECHA LIMITE', 'AVANCE');
 
  // Se agregan los titulos del reporte
  //Titulo de las columnas
  $objPHPExcel->setActiveSheetIndex(0)    
      ->setCellValue('A6',  $titulosColumnas[0])  
      ->setCellValue('B6',  $titulosColumnas[1])
      ->setCellValue('C6',  $titulosColumnas[2])
      ->setCellValue('D6',  $titulosColumnas[3])
      ->setCellValue('E6',  $titulosColumnas[4])
      ->setCellValue('F6',  $titulosColumnas[5])
      ->setCellValue('G6',  $titulosColumnas[6]);

  $i=7;
  $numeral= 0; $responsable="";
  foreach ($informes as $informe) { 
    $responsable = ORM::for_table("usuario")->select("fullname")->find_one( $informe->id_usuario );
   
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A'.$i, $numeral = $numeral + 1)
        ->setCellValue('B'.$i, $informe->detalle)    
        ->setCellValue('C'.$i, $informe->tipo_envio)      
        ->setCellValue('D'.$i, $informe->sistema_modulo)    
        ->setCellValue('E'.$i, $responsable->fullname)
        ->setCellValue('F'.$i, (new DateTime($informe->created_at ))->format("d-m-Y"))
        ->setCellValue('G'.$i, $informe->avance);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i.'')->applyFromArray($css_fila);
    $i++;         
  }
  
  $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray($estiloTitulo);
  $objPHPExcel->getActiveSheet()->getStyle('A2:G2')->applyFromArray($estiloTitulo);
  $objPHPExcel->getActiveSheet()->getStyle('A6:G6')->applyFromArray($estiloTituloReporte);
  
  $objPHPExcel->getActiveSheet()->setTitle('Reporte informes pendientes');

  for($i = 'A'; $i <= 'G'; $i++){
      $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setAutoSize(TRUE);
  }
  // Se activa la hoja para que sea la que se muestre cuando el archivo se abre
  $objPHPExcel->setActiveSheetIndex(0);

  // Se manda el archivo al navegador web, con el nombre que se indica, en formato 2007
  header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  header('Content-Disposition: attachment;filename="informes_pendientes.xlsx"');
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
