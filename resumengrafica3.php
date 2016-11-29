<?php

/* Include the `fusioncharts.php` file that contains functions	to embed the charts. */

// Incluir php de gráficas. Se incluye en contenedor
 // Pinar bonito
 function datachart3($row) {
     $ameses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio', 'Agosto','Septiembre','Octubre','Noviembre','Diciembre');
     $adata = array(
        "label" => $row["dia"],
        "value" => $row["intvalor"]
     );
     return $adata;
 }
?>
<html>
   <head>
  	<title></title>
        <style>
            div.chart-grafica3 {
                float:right;
                margin:auto;
                margin-bottom: 10px;
                margin-left: 10px;
                background: rgba(0, 97, 255, 0.5);
                with: 430x;
                -moz-border-radius:10px;
                -webkit-border-radius:10px;
            }
        </style>
  </head>

   <body>
  	<?php

     	$strQuery = "SELECT dia,intvalor FROM grafica_dias where intvalor > 0 and idparametro=140 limit 10";
             	// Execute the query, or else return the error message.
     	$result3 = $dbhandle->query($strQuery) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

     	// If the query returns a valid response, prepare the JSON string
        if ($result3) {
            $fila1 = mysqli_fetch_array($result3);
            $vvalor = "Titulo3";
            //$vprefijo = $fila1["PREFIJO"];
            //$ilink = $fila1["ESTLINK"];
            $vtxtpie= "Descargar.";
            //$vvalor.=" / ".$vprefijo;
            // The `$arrData` array holds the chart attributes and data
            $arrData3 = array(
                "chart" => array(
                  "caption" => "".$vvalor."",
                  //"paletteColors" => "#0075c2",
                  //"numberprefix" => "".$vprefijo."",
                  "bgColor" => "#ffffff",
                  "borderAlpha"=> "20",
                  "canvasBorderAlpha"=> "0",
                  "usePlotGradientColor"=> "0",
                  "plotBorderAlpha"=> "10",
                  "showXAxisLine"=> "1",
                  "xAxisLineColor" => "#999999",
                  "showValues" => "0",
                  "divlineColor" => "#999999",
                  "divLineIsDashed" => "1",
                  "showAlternateHGridColor" => "0",
                  //"showexportdatamenuitem" => "1"
                  /*  "caption" => "".$vvalor."",
                    "subcaption"=> "",
                    "yaxisname" => "",
                    "numberprefix" => "".$vprefijo."",
                    "bgcolor"=> "FFFFFF",
                    "useroundedges" => "1",
                    "showborder"=> "0" */
                  )
               );
            $arrData3["data"] = array();
            // Valores de primera fila.
            $adet3 = datachart3($fila1);
            array_push($arrData3["data"], $adet3);
            // Resto de filas en array
            while($row = mysqli_fetch_array($result3)) {
                $adet3 = datachart3($row);
                array_push($arrData3["data"], $adet3);
            }
        }
        /*--------------------------------------------------------------------------------------------------------------*/
        /*--------------------------------------------------------------------------------------------------------------*/

        /*JSON Encode the data to retrieve the string containing the JSON representation of the data in the array. */
        $jsonEncodedData3 = json_encode($arrData3);
        /*Create an object for the column chart using the FusionCharts PHP class constructor. Syntax for the constructor is ` FusionCharts("type of chart", "unique chart id", width of the chart, height of the chart, "div id to render the chart", "data format", "data source")`. Because we are using JSON data to render the chart, the data format will be `json`. The variable `$jsonEncodeData` holds all the JSON data for the chart, and will be passed as the value for the data source parameter of the constructor.*/

        //$columnChart = new FusionCharts("column2D", "Grafica / Hora" , 600, 300, "graf_hora", "json", $jsonEncodedData);
        $columnChart3 = new FusionCharts("column2d", "Grafica3" , 430, 200, "chart-grafica3", "json", $jsonEncodedData3);
        // Render the chart
        $columnChart3->render();
        ?>
  	<div id="chart-grafica3"><!-- Grafica Nº3 --></div>
   </body>

</html>