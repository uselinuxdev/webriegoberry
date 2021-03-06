<?php
/* Include the `fusioncharts.php` file that contains functions	to embed the charts. */
    // Pinar bonito
    function configchar3($arraya,$arrayb,$arrayc) {
        // Pitar x en valores instantaneos.
        $subtext = "";
        $arrCat = array();
        $dataseriesa = array();
        $dataseriesb = array();
        $dataseriesc = array();
        // Controlar los parÃ¡metros a tratar
        if(!empty($arraya)) {
            $subtext .= $arraya[0]['NOMBREP'];
            $dataseriesa = datachart3($arraya);
            if(empty($arrCat))
            {
                $arrCat = categorychart3($arraya);
            }
        }
        if(!empty($arrayb)) {
            $subtext .= " vs ".$arrayb[0]['NOMBREP'];
            $dataseriesb = datachart3($arrayb);
            // Cargar categorias del primer array
            if(empty($arrCat))
            {
                $arrCat = categorychart3($arrayb);
            }
        }
        if(!empty($arrayc)) {
            $subtext .= " vs ".$arrayc[0]['NOMBREP'];
            // Cargar categorias del primer array
            $dataseriesc = datachart3($arrayc);
            if(empty($arrCat))
            {
                $arrCat = categorychart3($arrayc);
            }
        }
        // ConfiguraciÃ³n chart
        $arrData = array(
                    "chart" => array(
                                "caption"=>  "".$subtext." aÃ±o actual",
                                "exportEnabled" => 1,
                                "captionFontSize"=> "12",
                                "subcaptionFontSize"=> "12",
                                "baseFontColor"=> "#333333",
                                "baseFont"=> "Helvetica Neue,Arial",
                                "subcaptionFontBold"=> "0",
                                "yAxisName"=> "".$arraya[0]['PREFIJO']."",
                                "showValues"=> "0",
                                "bgColor"=> "#ffffff",
                                "showBorder"=> "0",
                                "showShadow"=> "0",
                                "showAlternateHGridColor"=> "0",
                                "showCanvasBorder"=> "0",
                                "showXAxisLine"=> "1",
                                "xAxisLineThickness"=> "1",
                                "xAxisLineColor"=> "#999999",
                                "legendBorderAlpha"=> "0",
                                "legendShadow"=> "0",
                                "divlineAlpha"=>"100",
                                "divlineColor"=> "#999999",
                                "divlineThickness"=> "1",
                                "divLineDashed"=> "1",
                                "divLineDashLen"=> "1"
                            )
	);
        // aÃ±adir las series
        $arrData["categories"]=array(array("category"=>$arrCat));
        // aÃ±adir valores
        $arrData["dataset"] = array(array("seriesName"=> $arraya[0]['NOMBREP'], "data"=>$dataseriesa),array("seriesName"=> $arrayb[0]['NOMBREP'], "data"=>$dataseriesb),array("seriesName"=> $arrayc[0]['NOMBREP'], "data"=>$dataseriesc));

        return $arrData;
    }
    
    function categorychart3($array) {
        $ameses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio', 'Agosto','Septiembre','Octubre','Noviembre','Diciembre');
        $arrCat = array();
        // Recorrer todas las filas del arraya
        // Categorias. Valores X de la grÃ¡fica. Pintar siempre los 12 meses.
        for($i=0; $i<12; $i++)
        {
            $vlabel = $ameses[$i];
            // Control de color.
            $scolor = "";
            if(isset($array[$i]["COLOR"])) 
            {
              $scolor = "color => '".$array[$i]["COLOR"]."'";         
            }
            array_push($arrCat, array("label" => $vlabel,$scolor)); 
        }
        //print_r($arrCat);
        return $arrCat;
    }
    
    function datachart3($array)
    {
        // Categorias. Valores Y de la grÃ¡fica. Pintar los 12 meses
        $myCalc3 = new riegoresumenClass();
        $adat = array();
        $valor = 0;
        // Recorrer los 12 meses. Poner a 0. Luego localizar y actualizar si existe valor
        for ($i=0; $i<12; $i++)
        {
            // Control de color.
            $scolor = "";
            if(isset($array[$i]["COLOR"])) 
            {
              $scolor = "color => '".$array[$i]["COLOR"]."'";         
            }
            array_push($adat, array(
                "value" => $valor,$scolor)
            ); 
        }
        // Con los 12 valores pintar los que correspondan. Recorrer el array de encontrados
        $longitud = count($array);
        for($i=0; $i<$longitud; $i++)
        {
            $valor = $myCalc3->posdecimal($array[$i]["VALOR"],$array[$i]["POSDECIMAL"]);
            $adat[$array[$i]["HORA"]-1]["value"] = $valor;
        }
        return $adat;
    }
?>

<html>
   <head>
  	<title></title>
        <?php
            $myClass = new riegoresumenClass();
            $myClass->cargarClase('resumengrafica3'); 
            $aparam = $myClass->verParam();
            // Cargar los difrirentes Arrays A,B y C.
            $param = $aparam[0]['idparametroa'];
            switch ($param) {
                case -1:
                    // Parametro no definido. No aplica almenos tiene q haber a.
                    break;
                case 0:
                    // Cargar datos estimados. No aplica almenos tiene q haber a.
                default:
                    // Cargar los datos del parametro
                    $arraya = $myClass->loadarrayparam($param); 
            }
            $param = $aparam[0]['idparametrob'];
            switch ($param) {
                case -1:
                    // Parametro no definido
                    break;
                case 0:
                    // Cargar datos estimados
                    $arrayb = $myClass->loadstimate($aparam[0]['idparametroa']); 
                    break;
                default:
                    // Cargar los datos del parametro
                    $arrayb = $myClass->loadarrayparam($param);
            }
            $param = $aparam[0]['idparametroc'];
            switch ($param) {
                case -1:
                    // Parametro no definido
                    break;
                case 0:
                    // Cargar datos estimados
                    $arrayc = $myClass->loadstimate($aparam[0]['idparametroa']); 
                    break;
                default:
                    // Cargar los datos del parametro
                    $arrayc = $myClass->loadarrayparam($param);
            }
        
        
        ?>
        
  </head>

   <body>
  	<?php
        // The `$arrData` array holds the chart attributes and data
        $arrChart3 = configchar3($arraya,$arrayb,$arrayc);
       //print_r($arrChart1);
        /*JSON Encode the data to retrieve the string containing the JSON representation of the data in the array. */
        $valoresjson3 = json_encode($arrChart3);
        //$columnChart = new FusionCharts(Tipo Chart,Ide java chart,width, heigth, div, "tipo", datos)
        $columnChart3 = new FusionCharts("mscolumn2d", "Grafica3" , 430, 200, "chart-grafica3", "json", $valoresjson3);
        // Render the chart
        $columnChart3->render();
        
  	?>

  	<div id="chart-grafica3"><!-- Grafica NÂº3 --></div>
   </body>

</html>