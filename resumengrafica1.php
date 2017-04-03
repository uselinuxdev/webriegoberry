<?php
/* Include the `fusioncharts.php` file that contains functions	to embed the charts. */
    // Pinar bonito
    function configchar1($arraya,$arrayb,$arrayc) {
        // Pitar x en valores instantaneos.
        $subtext = "";
        $arrCat = array();
        $dataseriesa = array();
        $dataseriesb = array();
        $dataseriesc = array();
        // Controlar los parámetros a tratar
        if(!empty($arraya)) {
            $subtext .= $arraya[0]['NOMBREP'];
            $dataseriesa = datachart1($arraya);
            if(empty($arrCat))
            {
                $arrCat = categorychart1($arraya);
            }
        }
        if(!empty($arrayb)) {
            $subtext .= " vs ".$arrayb[0]['NOMBREP'];
            $dataseriesb = datachart1($arrayb);
            // Cargar categorias del primer array
            if(empty($arrCat))
            {
                $arrCat = categorychart1($arrayb);
            }
        }
        if(!empty($arrayc)) {
            $subtext .= " vs ".$arrayc[0]['NOMBREP'];
            // Cargar categorias del primer array
            $dataseriesc = datachart1($arrayc);
            if(empty($arrCat))
            {
                $arrCat = categorychart1($arrayc);
            }
        }
        // Configuración chart
        $arrData = array(
                    "chart" => array(
                            "caption" => "Hoy ".$arraya[0]['PREFIJO']."",
                            "subCaption"  => "".$subtext."",
                            "captionFontSize"  => "14",
                            "subcaptionFontSize"  => "14",		
                            "subcaptionFontBold"  => "0",
                            "bgcolor"  => "#ffffff",
                            "showBorder"  => "0",
                            "showShadow"  => "0",
                            "showCanvasBorder"  => "0",
                            "usePlotGradientColor"  => "0",
                            "legendBorderAlpha"  => "0",
                            "legendShadow"  => "0",
                            "showAxisLines"=> "0",
                            "showAlternateHGridColor"  => "0",
                            "divlineThickness"  => "1",
                            "divLineIsDashed" => "1",
                            "divLineDashLen"  => "1",
                            "xAxisName"  => "Hora",
                            "showValues"  => "0",
                            "linePosition" => "0",
                            )
	);
        // Añadir categorias
        $arrData["categories"]=array(array("category"=>$arrCat));
        // añadir las series
        $arrData["dataset"] = array(array("seriesName"=> $arraya[0]['NOMBREP'], "data"=>$dataseriesa),array("seriesName"=> $arrayb[0]['NOMBREP'], "data"=>$dataseriesb),array("seriesName"=> $arrayc[0]['NOMBREP'], "data"=>$dataseriesc));
        // Retornar variable JSON
        //print_r($arrCat);
        return $arrData;
    }
    function categorychart1($array) {
        $ameses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio', 'Agosto','Septiembre','Octubre','Noviembre','Diciembre');
        // Categorias. Valores X de la gráfica
        $arrCat = array();
        // Recorrer todas las filas del arraya
        $longitud = count($array);
        for($i=0; $i<$longitud; $i++)
	{
            // Control de color.
            $scolor = "";
            if(isset($array[$i]["COLOR"])) 
            {
              $scolor = "color => '".$array[$i]["COLOR"]."'";         
            }
            array_push($arrCat, array(
                    "label" => $array[$i]["HORA"],$scolor
                    )
            );
        }
        return $arrCat;
    }
    
    function datachart1($array)
    {
        // Categorias. Valores Y de la gráfica
        $myCalc = new riegoresumenClass();
        $adat = array();
        // Recorrer todas las filas del arraya
        $longitud = count($array);
        for($i=0; $i<$longitud; $i++)
	{
          // Calculo valor
            $vvalor = $myCalc->posdecimal($array[$i]["VALOR"],$array[$i]["POSDECIMAL"]);
            // Control de color.
            $scolor = "";
            if(isset($array[$i]["COLOR"])) 
            {
              $scolor = "color => '".$array[$i]["COLOR"]."'";         
            }
            array_push($adat, array(
                    "value" => $vvalor,$scolor)
            );     
        }
        return $adat;
    }
?>

<html>
   <head>
  	<title></title>
        <?php
            $myClass1 = new riegoresumenClass();
            $myClass1->cargarClase('resumengrafica1'); 
            $aparam = $myClass1->verParam();
            // Cargar los difrirentes Arrays A,B y C.
            $param = $aparam[0]['idparametroa'];
            switch ($param) {
                case -1:
                    // Parametro no definido
                    break;
                case 0:
                    // Cargar datos estimados
                    $arraya = $myClass->loadstimate($param); 
                    break;
                default:
                    // Cargar los datos del parametro
                    $arraya1 = $myClass1->loadarrayparam($param); 
            }
            $param = $aparam[0]['idparametrob'];
            switch ($param) {
                case -1:
                    // Parametro no definido
                    break;
                case 0:
                    // Cargar datos estimados
                    $arraya = $myClass->loadstimate($param); 
                    break;
                default:
                    // Cargar los datos del parametro
                    $arrayb1 = $myClass1->loadarrayparam($param);
            }
            $param = $aparam[0]['idparametroc'];
            switch ($param) {
                case -1:
                    // Parametro no definido
                    break;
                case 0:
                    // Cargar datos estimados
                    $arraya = $myClass->loadstimate($param); 
                    break;
                default:
                    // Cargar los datos del parametro
                    $arrayc1 = $myClass1->loadarrayparam($param);
            }
        
        
        ?>
        
  </head>

   <body>
  	<?php
        // The `$arrData` array holds the chart attributes and data
        $arrChart1 = configchar1($arraya1,$arrayb1,$arrayc1);
       //print_r($arrChart1);
        /*JSON Encode the data to retrieve the string containing the JSON representation of the data in the array. */
        $valoresjson1 = json_encode($arrChart1);
        //$columnChart = new FusionCharts(Tipo Chart,Ide java chart,width, heigth, div, "tipo", datos)
        $columnChart1 = new FusionCharts("msspline", "Grafica1" , 430, 200, "chart-grafica1", "json", $valoresjson1);
        // Render the chart
        $columnChart1->render();
        
  	?>

  	<div id="chart-grafica1"><!-- Grafica Nº1 --></div>
   </body>

</html>