<?php
/* Include the `fusioncharts.php` file that contains functions	to embed the charts. */
    // Pinar bonito
    function configchar2($arraya,$arrayb,$arrayc) {
        // Pitar x en valores instantaneos.
        $subtext = "";
        $arrCat = array();
        $dataseriesa = array();
        $dataseriesb = array();
        $dataseriesc = array();
        
        // Categorías
        $arrCat = categorychart2($arraya,$arrayb,$arrayc);
        
        // Controlar los parámetros a tratar
        if(!empty($arraya)) {
            $subtext .= $arraya[0]['NOMBREP'];
            $dataseriesa = datachart2($arraya,$arrCat);
        }     
        if(!empty($arrayb)) {
            $subtext .= " vs ".$arrayb[0]['NOMBREP'];
            $dataseriesb = datachart2($arrayb,$arrCat);
        }
        if(!empty($arrayc)) {
            $subtext .= " vs ".$arrayc[0]['NOMBREP'];
            // Cargar categorias del primer array
            $dataseriesc = datachart2($arrayc,$arrCat);
        }
        // Configuración chart
        $arrData = array(
                    "chart" => array(
                                "caption"=> "".$subtext." mes actual",
                                "yaxisname"=> "".$arraya[0]['PREFIJO']."",
                                "exportEnabled" => 1,
                                "rotatevalues"=> "0",
                                "showvalues"=> "0",
                                "valuefontcolor"=> "074868",
                                "plotgradientcolor"=> "",
                                "showcanvasborder"=> "0",
                                "numdivlines"=> "5",
                                "showyaxisvalues"=> "1",
                                "canvasborderthickness"=> "1",
                                "canvasbordercolor"=> "#074868",
                                "canvasborderalpha"=> "30",
                                "basefontcolor"=> "#074868",
                                "divlinecolor"=> "#074868",
                                "divlinealpha"=> "10",
                                "divlinedashed"=> "0"
                            )
	);
        // añadir las series
        $arrData["categories"]=array(array("category"=>$arrCat));
        // añadir valores
        $arrData["dataset"] = array(array("seriesName"=> $arraya[0]['NOMBREP'], "data"=>$dataseriesa),array("seriesName"=> $arrayb[0]['NOMBREP'], "data"=>$dataseriesb),array("seriesName"=> $arrayc[0]['NOMBREP'], "data"=>$dataseriesc));

        return $arrData;
    }
  
    function categorychart2($arraya,$arrayb,$arrayc) {
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
    
    function mediachart2($array) {
        // Media de la grafica

        // Recorrer todas las filas del arraya
        $longitud = count($array);
        $tvalores = 0;
        for($i=0; $i<$longitud; $i++)
	{
            $tvalores = $tvalores + $array[$i]["VALOR"];
        }
        $media = $tvalores/$longitud;
        $arrMedia = array(
                        "startvalue"=> "".$media."",
                        "endvalue"=> "",
                        "istrendzone"=> "",
                        "valueonright"=> "1",
                        "color"=> "fda813",
                        "displayvalue"=> "Media ".$media,
                        "showontop"=> "1",
                        "thickness"=> "2"
                      );
        return $arrMedia;
    }
    
    function datachart2($array,$arrCat)
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
            $myClass = new riegoresumenClass();
            $myClass->cargarClase('resumengrafica2'); 
            $aparam = $myClass->verParam();
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
                    $arraya = $myClass->loadarrayparam($param); 
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
                    $arrayb = $myClass->loadarrayparam($param);
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
                    $arrayc = $myClass->loadarrayparam($param);
            }       
        ?>
        
  </head>

   <body>
  	<?php
        // The `$arrData` array holds the chart attributes and data
        $arrChart2 = configchar2($arraya,$arrayb,$arrayc);
       //print_r($arrChart2);
        /*JSON Encode the data to retrieve the string containing the JSON representation of the data in the array. */
        $valoresjson2 = json_encode($arrChart2);
        //$columnChart = new FusionCharts(Tipo Chart,Ide java chart,width, heigth, div, "tipo", datos)
        $columnChart2 = new FusionCharts("mscolumn2d", "Grafica2" , 430, 200, "chart-grafica2", "json", $valoresjson2);
        // Render the chart
        $columnChart2->render();
        
  	?>

  	<div id="chart-grafica2"><!-- Grafica Nº2 --></div>
   </body>

</html>