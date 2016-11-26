<?php

/* Include the `fusioncharts.php` file that contains functions	to embed the charts. */

   // Incluir php de gráficas.
include("fusioncharts/fusioncharts.php");

/* The following 4 code lines contain the database connection information. Alternatively, you can move these code lines to a separate file and include the file here. You can also modify this code based on your database connection. */
    $hostdb = $_SESSION['serverdb'];  // MySQl host
    $userdb = $_SESSION['dbuser'];  // MySQL username
    $passdb = $_SESSION['dbpass'];  // MySQL password
    $namedb = $_SESSION['dbname'];  // MySQL database name

   // Establish a connection to the database
   $dbhandle = new mysqli($hostdb, $userdb, $passdb, $namedb);

    if ($dbhandle->connect_error) {
       exit("No se ha podido conectar a la Base de Datos: ".$dbhandle->connect_error);
    }
    // Pinar bonito
    function datachart($row) {
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
  </head>

   <body>
  	<?php

     	$strQuery = "SELECT dia,intvalor FROM grafica_dias where intvalor > 0 and idparametro=140 limit 10";

     	// Execute the query, or else return the error message.
     	$result = $dbhandle->query($strQuery) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

     	// If the query returns a valid response, prepare the JSON string
        if ($result) {
            $fila1 = mysqli_fetch_array($result);
            $vvalor = "Titulo";
            //$vprefijo = $fila1["PREFIJO"];
            //$ilink = $fila1["ESTLINK"];
            $vtxtpie= "Descargar.";
            //$vvalor.=" / ".$vprefijo;
            // The `$arrData` array holds the chart attributes and data
            $arrData = array(
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
            $arrData["data"] = array();
            // Valores de primera fila.
            $adet = datachart($fila1);
            array_push($arrData["data"], $adet);
            // Resto de filas en array
            while($row = mysqli_fetch_array($result)) {
                $adet = datachart($row);
                array_push($arrData["data"], $adet);
            }
            /*--------------------------------------------------------------------------------------------------------------*/
            /*--------------------------------------------------------------------------------------------------------------*/
            
            /*JSON Encode the data to retrieve the string containing the JSON representation of the data in the array. */
            $jsonEncodedData = json_encode($arrData);

            /*Create an object for the column chart using the FusionCharts PHP class constructor. Syntax for the constructor is ` FusionCharts("type of chart", "unique chart id", width of the chart, height of the chart, "div id to render the chart", "data format", "data source")`. Because we are using JSON data to render the chart, the data format will be `json`. The variable `$jsonEncodeData` holds all the JSON data for the chart, and will be passed as the value for the data source parameter of the constructor.*/

            //$columnChart = new FusionCharts("column2D", "Grafica / Hora" , 600, 300, "graf_hora", "json", $jsonEncodedData);
            $columnChart = new FusionCharts("column3d", "Grafica / Hora" , 430, 200, "chart-grafina1", "json", $jsonEncodedData);

            // Render the chart
            $columnChart->render();

            // Close the database connection
            $dbhandle->close();
        }
  	?>

  	<div id="chart-grafina1"><!-- Grafica Nº1 --></div>
        
   </body>

</html>