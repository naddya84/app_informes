 <?php
    /**** Paginacion ***/
 
 $limite_paginacion = 10;
 $margen = 5;
 
echo "<div class='contenedor_paginacion'>";
if( $pagina_actual > 0 ){
  echo "<div class='btn_paginacion css_anterior left' data-pagina='".($pagina_actual-1)."'> Anterior</div>"
          . "<div class= 'left css_paginador_m'> </div>";
}

if( $total_items/$items_x_pagina < $limite_paginacion ){
  for( $i=0; $i<$total_items/$items_x_pagina; $i++ ){
    if( $i ==  $pagina_actual ){
      echo "<div class='paginacion_actual css_paginacion left' >".($i+1)."</div>";
    } else {
      echo "<div class='btn_paginacion css_paginacion left' data-pagina='".$i."'>".($i+1)."</div>";
    }
  }
} else {
  $inicio = $inicio_izq = $pagina_actual - $margen;
  if( $pagina_actual > $margen ){
    echo "<div class='css_paginacion left' >...</div>";
  }    
  
  if( $inicio_izq < 0 ){
    $inicio = 0;
  }  
  
  
  if( $inicio_izq < 0 ){
    $fin = $fin_der = abs($inicio_izq) + $margen + $pagina_actual;
  } else {
    $fin = $fin_der = $pagina_actual + $margen;
  }
    
  if( $fin_der >= $total_items/$items_x_pagina ){   
    $fin = $total_items/$items_x_pagina; 
  }    
  
  for( $i=$inicio; $i<$fin; $i++ ){
    if( $i ==  $pagina_actual ){
      echo "<div class='paginacion_actual css_paginacion left' >".($i+1)."</div>";
    } else {
      echo "<div class='btn_paginacion css_paginacion left' data-pagina='".$i."'>".($i+1)."</div>";
    }
  }
  
  if( $fin_der < $total_items/$items_x_pagina ){
    echo "<div class='css_paginacion left' >...</div>";
  }
}



if( $pagina_actual +1 < $total_items/$items_x_pagina ){
  echo "<div class='btn_paginacion css_sig left' data-pagina='".($pagina_actual+1)."'>Siguiente </div>";
}
echo "</div>";
?>