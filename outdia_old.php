<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
//Primero hacemos las conexiones
$hostdb = $_SESSION['serverdb'];  // MySQl host
$userdb = $_SESSION['dbuser'];  // MySQL username
$passdb = $_SESSION['dbpass'];  // MySQL password
$namedb = $_SESSION['dbname'];  // MySQL database name
mysql_set_charset('utf8'); // Importante juego de caracteres a utilizar.
// Establish a connection to the database
$dbhandle = new mysqli($hostdb, $userdb, $passdb, $namedb);

// Array de meses.
$ameses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio', 'Agosto','Septiembre','Octubre','Noviembre','Diciembre');
$vtiposalida = 0; // 1 dia,2 mes, 3 año, 4 total.


/*Render an error message, to avoid abrupt failure, if the database connection parameters are incorrect */
if ($dbhandle->connect_error) {
   exit("No se ha podido conectar a la Base de Datos: ".$dbhandle->connect_error);
}
// Incluir php de gráficas.
include("fusioncharts/fusioncharts.php");

// Funciones de diferentes select
function selectdia($nombre) {
    $vtiposalida = 1;
    $vparam = $_POST['cbvalor'];
    // Formato de fecha estandar yyyy-mm-dd HH:mm:ss
    $vfecha =date('Y-m-d',strtotime($_POST['fhasta'])); 
    //echo $vfecha;
    //$vhora = $_POST['nhora'].':00:00';
    //echo $vhora;
    //$vfecha .= ' '.$vhora;
    $vdesde = date("Y-m-d H:i:s", strtotime('+0 hours', strtotime($vfecha)));
    //echo $vdesde;
    $vhasta = date("Y-m-d H:i:s", strtotime('+1 days',strtotime($vfecha)));
    //vhasta = '2015-03-07 00:00:00';
    //echo $vhasta;
    // Usar vista unión parametros_server y lectura_parametros
    $sselect = "SELECT NOMBREP,PREFIJO,POSDECIMAL,VALOR,HORA FROM vgrafica ";
    $sselect.="WHERE idparametro = ".$vparam;
    $sselect.=" AND flectura >= '".date($vdesde)."'";
    $sselect.=" AND flectura < '".date($vhasta)."'";
    return $sselect;
}

function selectmes($nombre) {
    $vtiposalida = 2;
    $vparam = $_POST['cbvalorm'];
    $vmes = $_POST['cbmes'];
    $vyear = $_POST['cbyear'];
    // Formato de fecha estandar yyyy-mm-dd HH:mm:ss
    $vfecha = "01-".$vmes."-".$vyear;
    $vdesde = date("Y-m-d H:i:s", strtotime('+0 hours', strtotime($vfecha)));
    //echo $vdesde;
    $vhasta = date("Y-m-d H:i:s", strtotime('+1 month',strtotime($vfecha)));
    //vhasta = '2015-03-07 00:00:00';
    //echo $vhasta;
    // Usar vista unión parametros_server y lectura_parametros
    $sselect = "SELECT NOMBREP,PREFIJO,POSDECIMAL,MAX(VALOR)-MIN(VALOR) AS VALOR,DIA AS HORA FROM vgrafica ";
    $sselect.="WHERE idparametro = ".$vparam;
    $sselect.=" AND flectura >= '".date($vdesde)."'";
    $sselect.=" AND flectura < '".date($vhasta)."'";
    $sselect.=" GROUP BY DIA";
    return $sselect;
}

function selectyear($nombre) {
    $vtiposalida=3;
    $vparam = $_POST['cbvalory'];
    $vyear = $_POST['cbyear'];
    // Formato de fecha estandar yyyy-mm-dd HH:mm:ss
    $vfecha = "01-01-".$vyear;
    //echo $vfecha;
    $vdesde = date("Y-m-d H:i:s", strtotime('+0 hours', strtotime($vfecha)));
    $vhasta = date("Y-m-d H:i:s", strtotime('+1 year',strtotime($vfecha)));
    // Usar vista unión parametros_server y lectura_parametros
    $sselect = "SELECT NOMBREP,PREFIJO,POSDECIMAL,MAX(VALOR)-MIN(VALOR) AS VALOR,MES AS HORA FROM vgrafica ";
    $sselect.="WHERE idparametro = ".$vparam;
    $sselect.=" AND flectura >= '".date($vdesde)."'";
    $sselect.=" AND flectura < '".date($vhasta)."'";
    $sselect.=" GROUP BY MES";
    return $sselect;
}

function selectall($nombre) {
    $vtiposalida=4;
    $vparam = $_POST['cbvalort'];
    // Formato de fecha estandar yyyy-mm-dd HH:mm:ss
    // Usar vista unión parametros_server y lectura_parametros
    $sselect = "SELECT NOMBREP,PREFIJO,POSDECIMAL,MAX(VALOR)-MIN(VALOR) AS VALOR,YEAR AS HORA FROM vgrafica ";
    $sselect.="WHERE idparametro = ".$vparam;
    $sselect.=" GROUP BY YEAR";
    return $sselect;
}

function posdecimal($valor,$posiciones) {
    if ($posiciones > 0) {
        $div = 1;
        for ($x = 0; $x < $posiciones; $x++) {
            $div = $div * 10;
        }
        // Resultado
        $valor = round($valor/$div,$posciones);
    }
    // Retorna valor sin tocar o con la division
    return $valor;
            
}
?>

<html>
    <head>
        <meta charset="UTF-8">
        <script src="fusioncharts/fusioncharts.js"></script>
        <title></title>

    </head>
    <body>
        <?php
            if (!empty($_POST['cbvalor'])) {
                $sql = selectdia();
                $vtiposalida = 1;
                $vtxtpie= 'Fecha de informe:'.$_POST['fhasta'].'.';
                //echo $sql;
            }
            if (!empty($_POST['cbvalorm'])) {
                $sql = selectmes();
                $vtiposalida = 2;
                $vtxtpie= 'Diferencia del mes de '.$ameses[$_POST['cbmes']-1].' de '.$_POST['cbyear'].'.';
                //echo $sql;
            }
            if (!empty($_POST['cbvalory'])) {
                $sql = selectyear();
                $vtiposalida = 3;
                $vtxtpie= 'Diferencia meses del ejercicio '.$_POST['cbyear'].'.';
                //echo 'Combo año:'.$_POST['cbvalory'];
                //echo $sql;
            }
            if (!empty($_POST['cbvalort'])) {
                $sql = selectall();
                $vtiposalida = 4;
                //echo 'Combo total:'.$_POST['cbvalort'];
                $vtxtpie= 'Diferencia entre ejercicios. Código de parámetro:'.$_POST['cbvalort'].'.';
                //echo $sql;
            }
         // Execute the query, or else return the error message.
         $result = $dbhandle->query($sql) or exit("Código de error ({$dbhandle->errno}): {$dbhandle->error}");

         // If the query returns a valid response, prepare the JSON string
         
         // Quiere que se coga la diferencia, por lo tanto la primera fila no se pinta y la siguiente es la dif de valores.
         if ($result) {
            $fila1 = mysqli_fetch_array($result);
            $vvalor = substr($fila1["NOMBREP"],0,20);
            $vprefijo = $fila1["PREFIJO"];
            
            // Control de diferencia.
            $vprev = 0;
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
                  "showAlternateHGridColor" => "0"
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
            // Controlar si es tipo 1 día realizar la resta del primer valor
            $vresta = 0;
            if ($vtiposalida == 1) {
                $vresta = posdecimal($fila1["VALOR"],$fila1["POSDECIMAL"]);
            }
            // Si es tipo 3 pintar meses.
            if ($vtiposalida == 3) {
                 $vlabel = $ameses[intval($fila1["HORA"])-1];
            }else {
                $vlabel = $fila1["HORA"];
            }    
            array_push($arrData["data"], array(
                  "label" => $vlabel,
                  "value" => posdecimal($fila1["VALOR"],$fila1["POSDECIMAL"]) - $vresta
                  )
               );
            // Resto de filas en array
            while($row = mysqli_fetch_array($result)) {
               if ($vtiposalida == 3) {
                    $vlabel = $ameses[intval($row["HORA"])-1];
               }else {
                   $vlabel = $row["HORA"];
               }
               array_push($arrData["data"], array(
                  "label" => $vlabel,
                  "value" => posdecimal($row["VALOR"],$row["POSDECIMAL"])  - $vresta // <-- Se hace la resta con el valor anterior.
                  )
               );
               if ($vtiposalida == 1) {
                $vresta = posdecimal($row["VALOR"],$row["POSDECIMAL"]);
               }
            }

            /*JSON Encode the data to retrieve the string containing the JSON representation of the data in the array. */

            $jsonEncodedData = json_encode($arrData);

    /*Create an object for the column chart using the FusionCharts PHP class constructor. Syntax for the constructor is ` FusionCharts("type of chart", "unique chart id", width of the chart, height of the chart, "div id to render the chart", "data format", "data source")`. Because we are using JSON data to render the chart, the data format will be `json`. The variable `$jsonEncodeData` holds all the JSON data for the chart, and will be passed as the value for the data source parameter of the constructor.*/

            //$columnChart = new FusionCharts("column2D", "Grafica / Hora" , 600, 300, "graf_hora", "json", $jsonEncodedData);
            $columnChart = new FusionCharts("column3d", "Grafica / Hora" , 600, 300, "t_dia", "json", $jsonEncodedData);
            //$columnChart = new FusionCharts("area2d", "Grafica / Hora" , 600, 300, "t_dia", "json", $jsonEncodedData);

            // Render the chart
            $columnChart->render();

            // Close the database connection
            $dbhandle->close();
         }
      ?>     
      <div id="t_dia"> </div>
      <p> <?php echo $vtxtpie;?> </p>
      </div>
   </body>
</html>
