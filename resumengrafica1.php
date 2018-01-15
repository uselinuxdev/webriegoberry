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
        
        // Categoría de todas las series
        $arrCat = categorychart1($arraya,$arrayb,$arrayc);
        
        // Con las categorías completas pintar 0 si en el array no existe la categoría.
        
        // Controlar los parámetros a tratar
        if(!empty($arraya)) {
            $subtext .= $arraya[0]['NOMBREP'];
            $dataseriesa = datachart1($arraya,$arrCat);
        }
        if(!empty($arrayb)) {
            $subtext .= " vs ".$arrayb[0]['NOMBREP'];
            $dataseriesb = datachart1($arrayb,$arrCat);
        }
        if(!empty($arrayc)) {
            $subtext .= " vs ".$arrayc[0]['NOMBREP'];
            // Cargar categorias del primer array
            $dataseriesc = datachart1($arrayc,$arrCat);
        }
        
        // Configuración chart
        $arrData = array(
                    "chart" => array(
                            "caption" => "Hoy ".$arraya[0]['PREFIJO']."",
                            "exportEnabled" => 1,
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
    function categorychart1($arraya,$arrayb,$arrayc) {
        $ameses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio', 'Agosto','Septiembre','Octubre','Noviembre','Diciembre');
        // Categorias. Valores X de la gráfica
        $acat = array();
        // Recorrer todas la filas
        foreach ($arraya as $afila) {
            $acat[$afila["HORA"]]=array("HORA"=>$afila["HORA"],"COLOR"=>"".$afila["COLOR"]."");
        }
        foreach ($arrayb as $afila) {
            $acat[$afila["HORA"]]=array("HORA"=>$afila["HORA"],"COLOR"=>"".$afila["COLOR"]."");
        }
        foreach ($arrayc as $afila) {
            $acat[$afila["HORA"]]=array("HORA"=>$afila["HORA"],"COLOR"=>"".$afila["COLOR"]."");
        }
        array_multisort($acat);
        // Recorrer el array y pintar el final
        $acatfin = array();
        foreach ($acat as $aval)
        {
            array_push($acatfin, array("label" => $aval["HORA"],"COLOR" =>$aval["COLOR"]));
        }
        //var_dump($acatfin);
       // var_dump($acatfin);
        return $acatfin;
    }
    
    function datachart1($array,$arrCat)
    {
        // Categorias. Valores Y de la gráfica
        $myCalc = new riegoresumenClass();
        $adat = array();
        // Control de color.
        $scolor = "";
        $scolor = "color => '".$array[0]["COLOR"]."'";  
        // Recoger la categoría, si no existe pintar 0
        foreach($arrCat as $fcreate) {
            $ipos = array_search($fcreate["label"], array_column($array,"HORA"));
            if (false !== $ipos) {
                // Calculo valor
                $vvalor = $myCalc->posdecimal($array[$ipos]["VALOR"],$array[$ipos]["POSDECIMAL"]);
                array_push($adat, array("value" => $vvalor,$scolor));
            }else {
                array_push($adat, array("value" => 0,$scolor));
            }
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