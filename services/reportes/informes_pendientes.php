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
  $where_texto = " AND ( LOWER(inf_m.codigo) LIKE LOWER('%$texto%') OR LOWER(inf_m.detalle) LIKE LOWER('%$texto%') ) ";
}
$where_fecha = "";
$fecha_inicial = $data->fecha_inicial;
$fecha_final = $data->fecha_final;
if( $fecha_inicial != "" && $fecha_final != "" ){    
  $where_fecha = " AND inf.created_at between '$fecha_inicial' AND '$fecha_final 23:59:59' ";
}
$informes = ORM::for_table('informe')
        ->raw_query(
        " SELECT inf_m.codigo, inf_m.detalle,inf_m.tipo_envio, inf_m.sistema_modulo, inf.id, inf.created_at,inf.fecha_limite, inf.avance, inf.id_usuario from informe inf".
        " LEFT JOIN informe_maestro inf_m ON ( inf_m.id = inf.id_informe_padre )".
        " WHERE inf.estado = 'pendiente' AND inf.deleted_at IS NULL".
        $where_texto.
        $where_fecha.        
        " ORDER BY  inf.created_at asc ")
        ->find_many();


if( count($informes) > 0 ) { 
  $objPHPExcel = new PHPExcel();
  // Se asignan las propiedades del libro
  $objPHPExcel->getProperties()->setCreator("Cooperativa Loyola") // Nombre del autor
          ->setLastModifiedBy("Cooperativa Loyola") //Ultimo usuario que lo modific??
          ->setTitle("REPORTE DE INFORMES PENDIENTES  ") // Titulo
          ->setSubject("REPORTE DE INFORMES PENDIENTES ") //Asunto
          ->setDescription("REPORTE DE INFORMES PENDIENTES ") //Descripci??n
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
          ->setCellValue('C5', "C??digo/Detalle:");
    $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue('D5',$texto );   
  } 
  $titulosColumnas = array('N??', 'CODIGO', 'DETALLE', 'TIPO DE ENVIO', 'SISTEMA - MODULO', 'RESPONSABLE', 'FECHA LIMITE', 'AVANCE');
 
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
      ->setCellValue('H6',  $titulosColumnas[7]);

  $i=7;
  $numeral= 0; $responsable="";
  foreach ($informes as $informe) { 
    $responsable = ORM::for_table("usuario")->select("fullname")->find_one( $informe->id_usuario );
   
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A'.$i, $numeral = $numeral + 1)
        ->setCellValue('B'.$i, $informe->codigo)   
        ->setCellValue('C'.$i, $informe->detalle)    
        ->setCellValue('D'.$i, $informe->tipo_envio)      
        ->setCellValue('E'.$i, $informe->sistema_modulo)    
        ->setCellValue('F'.$i, $responsable->fullname)
        ->setCellValue('G'.$i, (new DateTime($informe->fecha_limite ))->format("d-m-Y"))
        ->setCellValue('H'.$i, $informe->avance>0?$informe->avance." %":"-");
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i.'')->applyFromArray($css_fila);
    $i++;         
  }
  
  $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($estiloTitulo);
  $objPHPExcel->getActiveSheet()->getStyle('A2:H2')->applyFromArray($estiloTitulo);
  $objPHPExcel->getActiveSheet()->getStyle('A6:H6')->applyFromArray($estiloTituloReporte);
  
  $objPHPExcel->getActiveSheet()->setTitle('Reporte informes pendientes');

  for($i = 'A'; $i <= 'H'; $i++){
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
