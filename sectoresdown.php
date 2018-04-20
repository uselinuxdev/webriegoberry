<?php

/*Include the `fusioncharts.php` file that contains functions
	to embed the charts.
*/
include("fusioncharts/fusioncharts.php");
        // Controlar que exista sesion iniciada
require('adminsession.php');
if (CheckLogin() == false)
{
    header("Location: login.php");
}
  /* The following 4 code lines contain the database connection information. Alternatively, you can move these code lines to a separate file and include the file here. You can also modify this code based on your database connection.   */

//Primero hacemos las conexiones
$hostdb = $_SESSION['serverdb'];  // MySQl host
$userdb = $_SESSION['dbuser'];  // MySQL username
$passdb = $_SESSION['dbpass'];  // MySQL password
$namedb = $_SESSION['dbname'];  // MySQL database name

//$hostdb = 'localhost';  // MySQl host
//$userdb = 'riegosql';
//$passdb = 'riegoprod15';
//$namedb = 'riegosolar';
mysql_set_charset('utf8'); // Importante juego de caracteres a utilizar.
// Establish a connection to the database
$dbhandle = new mysqli($hostdb, $userdb, $passdb, $namedb);


/*Render an error message, to avoid abrupt failure, if the database connection parameters are incorrect */
if ($dbhandle->connect_error) {
   exit("No se ha podido conectar a la Base de Datos: ".$dbhandle->connect_error);
}

// Variables pasadas de php principal
$vtiposalida = $_GET['gtiposalida'];
$gcolumna = $_GET['gcolumna'];
// Funciones de diferentes select
function selectdia() {
    // Usar vista unión parametros_server y lectura_parametros
    $vdesde = $_GET['gdesde'];
    $vhasta = $_GET['ghasta'];
    $gcolumna = $_GET['gcolumna'];
    $sselect = "SELECT NOMBREP,PREFIJO,POSDECIMAL,VALOR,HORA FROM vgrafica_horas ";
    $sselect.=" WHERE flectura >= '".$vdesde."'";
    $sselect.=" AND flectura < '".$vhasta."'";
    $sselect.=" AND HORA = '".$gcolumna."'";
    $sselect.=" AND estlink = 2";
    $sselect.=" AND VALOR > 0";
    $sselect.=" order by NOMBREP";
    //echo $sselect;
    return $sselect;
}

function selectmes() {
    // Usar vista unión parametros_server y lectura_parametros
    $vdesde = $_GET['gdesde'];
    $vhasta = $_GET['ghasta'];
    $gcolumna = $_GET['gcolumna'];
    $sselect .=" SELECT NOMBREP,PREFIJO,POSDECIMAL,SUM(VALOR) AS VALOR,DIA AS HORA FROM vgrafica_dias "; 
    $sselect.="WHERE estlink = 2";
    $sselect.=" AND DIA = '".$gcolumna."'";
    $sselect .=" AND flectura >= '".$vdesde."'";
    $sselect .=" AND flectura < '".$vhasta."'";
    $sselect.=" AND VALOR > 0";
    $sselect .=" group by NOMBREP,PREFIJO,POSDECIMAL,DIA order by NOMBREP";
    //echo $sselect;
    return $sselect;
}

function selectyear() {
    // Array de meses.
    // Localizar Nº de mes base 0 a base 1 (Enero 1, Febrero 2).
    $vdesde = $_GET['gdesde'];
    $vyear=substr($vdesde, 0, 4);
    $gcolumna = $_GET['gcolumna'];
    $vfecha = "01-".$gcolumna."-".$vyear;
    $vdesde = date("Y-m-d H:i:s", strtotime('+0 hours', strtotime($vfecha)));
    $vhasta = date("Y-m-d H:i:s", strtotime('+1 month',strtotime($vfecha)));
    $sselect = "SELECT NOMBREP,PREFIJO,POSDECIMAL,SUM(VALOR) AS VALOR,MES AS HORA FROM vgrafica_dias ";
    $sselect.="WHERE estlink = 2";
    $sselect .=" AND flectura >= '".$vdesde."'";
    $sselect .=" AND flectura < '".$vhasta."'";
    $sselect.=" AND VALOR > 0";
    $sselect.=" GROUP BY NOMBREP,PREFIJO,POSDECIMAL,MES order by NOMBREP";
    return $sselect;
}

function selectall() {
    // Usar vista unión parametros_server y lectura_parametros
    $gcolumna = $_GET['gcolumna'];
    $sselect = "SELECT NOMBREP,PREFIJO,POSDECIMAL,SUM(VALOR) AS VALOR,YEAR AS HORA FROM vgrafica_dias ";
    $sselect.="WHERE estlink = 2";
    $sselect.=" AND YEAR = '".$gcolumna."'";
    $sselect.=" AND VALOR > 0";
    $sselect.=" GROUP BY NOMBREP,PREFIJO,POSDECIMAL,YEAR order by NOMBREP";
    return $sselect;
}

function posdecimal($valor,$posiciones) {
    if ($posiciones > 0) {
        $div = 1;
        for ($x = 0; $x < $posiciones; $x++) {
            $div = $div * 10;
        }
        // Resultado
        $valor = $valor/$div;
        $valor = round($valor, $posiciones);
        //$valor = $valor/$div;
    }
    // Retorna valor sin tocar o con la division
    return $valor;           
}

?>

<html>
   <head>
  	<title>Sectores de Riego</title>
	  <link  rel="stylesheet" type="text/css" href="css/style.css" />

	<!--  Include the `fusioncharts.js` file. This file is needed to render the chart. Ensure that the path to this JS file is correct. Otherwise, it may lead to JavaScript errors. -->

      <script src="fusioncharts/fusioncharts.js"></script>
   </head>
   <body>
  	<?php
        if ($vtiposalida == 1) {
            $sql = selectdia();
            //echo $sql;
        }
        if ($vtiposalida == 2) {
            $sql = selectmes();
        }
        if ($vtiposalida == 3) {
            $sql = selectyear();
        }
        if ($vtiposalida == 4) {
            $sql = selectall();
        }
        //echo $sql;
     	// Execute the query, or else return the error message.
     	$result = $dbhandle->query($sql) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

     	// If the query returns a valid response, prepare the JSON string
     	if ($result) {
        	// The `$arrData` array holds the chart attributes and data
        	$arrData = array(
                    "chart" => array(
                        "caption" =>  "Información sectores de riego",
                        "subCaption" => "Detalle: ".$gcolumna,
                        "paletteColors" => "#0075c2,#1aaf5d,#f2c500,#f45b00,#8e0000",
                        "bgColor" => "#ffffff",
                        "showBorder" => "0",
                        "use3DLighting" => "0",
                        "showShadow" => "0",
                        "enableSmartLabels" => "0",   
                        "startingAngle" => "0",
                        "showPercentValues" => "0", // <- Cambiado 1 -> 0
                        "showPercentInTooltip" => "0",
                        "decimals" => "2",
                        "captionFontSize" => "14",
                        "subcaptionFontSize" => "14",
                        "subcaptionFontBold" => "0",
                        "toolTipColor" => "#ffffff",
                        "toolTipBorderThickness" => "0",
                        "toolTipBgColor" => "#000000",
                        "toolTipBgAlpha" => "80",
                        "toolTipBorderRadius" => "2",
                        "toolTipPadding" => "5",
                        "showHoverEffect" => "1",
                        "showLegend" => "1",
                        "legendBgColor" => "#ffffff",
                        "legendBorderAlpha" => "0",
                        "legendShadow" => "0",
                        "legendItemFontSize" => "10",
                        "legendItemFontColor" => "#666666",
                        "useDataPlotColorForLabels" => "1"
                    )
           	);

        	$arrData["data"] = array();

	// Push the data into the array

        	while($row = mysqli_fetch_array($result)) {
           	array_push($arrData["data"], array(
                    "label" => $row["NOMBREP"],
                    "value" => posdecimal($row["VALOR"],$row["POSDECIMAL"]),
                    )
           	);
        	}

        	/*JSON Encode the data to retrieve the string containing the JSON representation of the data in the array. */

        	$jsonEncodedData = json_encode($arrData);

        	/*Create an object for the column chart. Initialize this object using the FusionCharts PHP class constructor. The constructor is used to initialize the chart type, chart id, width, height, the div id of the chart container, the data format, and the data source. */

        	$columnChart = new FusionCharts("pie3d", "myFirstChart" , 600, 300, "chart-1", "json", $jsonEncodedData);

        	// Render the chart
        	$columnChart->render();

        	// Close the database connection
        	$dbhandle->close();

     	}

  	?>
  	<div id="chart-1"><!-- Fusion Charts will render here--></div>
   </body>
</html>