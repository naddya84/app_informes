<?php

require_once('../libs/fpdf.php');
require_once '../config/database.php';


if( isset($_GET["id_usuario"]) ){
  $id_usuario = $_GET["id_usuario"];
}else{
  die("No se enviaron los datos");
}
$final = new DateTime();  
$final->modify("+ 20 day");  
$fecha_final = $final->format("Y-m-d");  
$fecha_inicial = date('Y-m-d', time()); 

$informes = ORM::for_table('informe')
        ->raw_query(
        " SELECT inf_m.codigo, inf_m.detalle, inf.id, inf.fecha_limite ".
        " FROM informe inf".
        " LEFT JOIN informe_maestro inf_m ON ( inf_m.id = inf.id_informe_padre )".
        " WHERE inf.deleted_at IS NULL AND inf.estado='en_proceso'".
        " AND inf.id_usuario= $id_usuario ".
        " AND inf.fecha_limite between '$fecha_inicial 00:00:00' AND '$fecha_final 23:59:59' ")
        ->find_many();

if(count($informes) > 0){ 

   
class PDF_alertas extends FPDF {
  // Cabecera de página
  function Header(){
    // Logo
    $this->Image('../img/logo.png',165,10,25);
    $this->SetFont('Arial','B',12);
    // Título
    $this->Cell(70);
    $this->Cell(30,10,'COOPERATIVA DE AHORRO Y CREDITO ABIERTA LOYOLA R.L.',0,0,'C');
    $this->Ln(9);
    $this->Cell(70);
    $this->SetFont('Arial','B',10);
    $this->Cell(30,10,utf8_decode('ALERTAS DE INFORMES'),0,0,'C');
    $this->Ln(10);
  }

  // Pie de página
  function Footer() {
    $this->SetY(-15);
    $this->SetFont('Arial','I',8);
    $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
  }
}

$pdf = new PDF_alertas('P','mm','Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Ln(18);

$header = array(utf8_decode('Nro'), utf8_decode('Código'), utf8_decode('Detalle'), utf8_decode('Tiempo Restante'));
        $pdf->SetFont('Arial','',8);
        $pdf->SetFillColor(250,250,250);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('','B');
        // Cabecera
        $w = array( 7,20,123,45);
        for($i=0;$i<count($header);$i++){
          $pdf->Cell($w[$i],7,$header[$i],1,0,'C',true);
        }
        $pdf->Ln();
        // Restauración de colores y fuentes
        $pdf->SetFillColor(224,235,255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');
        $fill = false;
      $index = 1;
  foreach ($informes as $informe) { 
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
      $tiempo_restante = "Tiene $dias $horas $minutos";
    } else {
      $tiempo_restante = "Fuera de Tiempo";
    }
    if( $dias == 0 &&(($horas > 1 && $horas < 24) || ($horas >= 1 && ($minutos >10 && $minutos <=60 ))) ){ 
      $pdf->SetFillColor(146,77,199); //alerta lila
      $pdf->SetTextColor(0);
      $pdf->SetDrawColor(0,0,0);
      $pdf->SetFont('');
    }
    elseif( $dias >= 1 && $dias <= 2 ){ 
      $pdf->SetFillColor(235,165,79);//alerta naranja
      $pdf->SetTextColor(0);
      $pdf->SetDrawColor(0,0,0);
      $pdf->SetFont('');
    }
    elseif( $dias >= 3 && $dias < 6 ){ 
      $pdf->SetFillColor(249,212,1);//alerta amarilla
      $pdf->SetTextColor(0);
      $pdf->SetDrawColor(0,0,0);
      $pdf->SetFont('');
    }
    elseif($tiempo_restante == "Fuera de Tiempo"){
      $pdf->SetFillColor(237,98,111);
      $pdf->SetTextColor(0);
      $pdf->SetDrawColor(0,0,0);
      $pdf->SetFont('');   
    } else {
      $pdf->SetFillColor(250,250,250);
      $pdf->SetTextColor(0);
      $pdf->SetDrawColor(0,0,0);
    }
    //filas
    $pdf->Cell($w[0],6,$index,'LR',0,'L',true);
    $pdf->Cell($w[1],6,$informe->codigo,'LR',0,'C',true);
    $pdf->Cell($w[2],6,$informe->detalle,'LR',0,'C',true);
    $pdf->Cell($w[3],6,$tiempo_restante,'LR',0,'C',true);
    $pdf->Ln();
    $pdf->Cell(array_sum($w),0,'','T');
    $pdf->Ln();
    $index ++;
  }
}

$pdf->Output("D", "alertas_informes.pdf");  
?>