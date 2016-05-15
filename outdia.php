<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.Add comment to github.com
-->
<?php
//Primero hacemos las conexiones
$hostdb = $_SESSION['serverdb'];  // MySQl host
$userdb = $_SESSION['dbuser'];  // MySQL username
$passdb = $_SESSION['dbpass'];  // MySQL password
$namedb = $_SESSION['dbname'];  // MySQL database name
mysql_set_charset('utf8'); // Importante juego de caracteres a utilizar.

// Definir el titulo para la exportacion
$_SESSION['expnombre'] = 'exphoras';

// Establish a connection to the database
$dbhandle = new mysqli($hostdb, $userdb, $passdb, $namedb);

// Array de meses.
$vtiposalida = 0; // 1 dia,2 mes, 3 año, 4 total.
$vdesde = date("Y-m-d H:i:s");
$vhasta = date("Y-m-d H:i:s");

$sqlexp = $sql;


/*Render an error message, to avoid abrupt failure, if the database connection parameters are incorrect */
if ($dbhandle->connect_error) {
   exit("No se ha podido conectar a la Base de Datos: ".$dbhandle->connect_error);
}
// Incluir php de gráficas.
include("fusioncharts/fusioncharts.php");

// Funciones de diferentes select
function selectdia(&$vdesde,&$vhasta,$expexcel = 0) {
    $vtiposalida = 1;
    $_SESSION['vparam'] = $_POST['cbvalor'];
    // Formato de fecha estandar yyyy-mm-dd HH:mm:ss
    $vfecha =date('Y-m-d',strtotime($_POST['fhasta'])); 
    //echo $vfecha;
    //$vhora = $_POST['nhora'].':00:00';
    //echo $vhora;
    //$vfecha .= ' '.$vhora;
    $vdesde = date("Y-m-d H:i:s", strtotime('+0 hours', strtotime($vfecha)));
    //echo $vdesde;
    $vhasta = date("Y-m-d H:i:s", strtotime('+1 days',strtotime($vfecha)));
    // No pintar la hora actual
  //  if (date('Y-m-d') == $vfecha){
  //      $vhasta = date("Y-m-d H:i:s", strtotime("-1 hour")); 
  //  }
    //vhasta = '2015-03-07 00:00:00';
    //echo $vhasta;
    // Usar vista unión parametros_server y lectura_parametros
    $sselect = "SELECT IDLECTURA,IDPARAMETRO,NOMBREP,PREFIJO,POSDECIMAL,VALOR,DATE_FORMAT(FLECTURA,'%H') AS HORA,ESTLINK FROM vgrafica_horas ";
    // Controlar si es para exportar
    if ($expexcel == 1) {
        $sselect = "SELECT NOMBREP AS PARAMETRO,VALOR,DATE_FORMAT(FLECTURA,'%H') AS HORA,FLECTURA AS FECHA,POSDECIMAL FROM vgrafica_horas ";
    }
    $sselect.="WHERE idparametro = ".$_SESSION['vparam'];
    $sselect.=" AND flectura >= '".date($vdesde)."'";
    $sselect.=" AND flectura < '".date($vhasta)."'";
    $sselect.=" order by flectura,idparametro";
    return $sselect;
}

function selectmes(&$vdesde,&$vhasta,$expexcel = 0) {
    $vtiposalida = 2;
    $_SESSION['vparam'] = $_POST['cbvalorm'];
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
    $sselect ="SELECT IDPARAMETRO,NOMBREP,PREFIJO,POSDECIMAL,VALOR,DIA AS HORA,ESTLINK,DATE_FORMAT(flectura,'%Y-%m-%d') AS FLECTURA FROM vgrafica_dias ";
    // Controlar si es para exportar
    if ($expexcel == 1) {
        $sselect ="SELECT NOMBREP AS PARAMETRO,VALOR,DIA,FLECTURA AS FECHA,POSDECIMAL FROM vgrafica_dias ";
    }
    $sselect.="WHERE idparametro = ".$_SESSION['vparam'];
    $sselect .=" AND flectura >= '".date($vdesde)."'";
    $sselect .=" AND flectura < '".date($vhasta)."'";
    // Si es  el mes actual añadir los últimos días sino menter el order by.
    if(date('n') == $vmes) {
        $sselect .=" union ";
        if ($expexcel == 0) {
            $sselect .=" SELECT IDPARAMETRO,NOMBREP AS PARAMETRO,PREFIJO,POSDECIMAL,SUM(VALOR) AS VALOR,DIA AS HORA,ESTLINK,DATE_FORMAT(flectura,'%Y-%m-%d') AS FLECTURA FROM vgrafica_horas "; 
        }else {
            $sselect .=" SELECT NOMBREP AS PARAMETRO,SUM(VALOR) AS VALOR,DIA,FLECTURA AS FECHA,POSDECIMAL FROM vgrafica_horas "; 
        }
        $sselect.="WHERE idparametro = ".$_SESSION['vparam'];
        $sselect .=" AND flectura > CURRENT_DATE() - INTERVAL 2 DAY";  
        $sselect .=" group by NOMBREP,DATE_FORMAT(flectura,'%Y-%m-%d') order by flectura";
    }else {
        $sselect .=" order by flectura";
    }
    //echo $sselect;
    return $sselect;
}

function selectyear(&$vdesde,&$vhasta,$expexcel = 0) {
    $vtiposalida=3;
    $_SESSION['vparam'] = $_POST['cbvalory'];
    $vyear = $_POST['cbyear'];
    // Formato de fecha estandar yyyy-mm-dd HH:mm:ss
    $vfecha = "01-01-".$vyear;
    //echo $vfecha;
    $vdesde = date("Y-m-d H:i:s", strtotime('+0 hours', strtotime($vfecha)));
    $vhasta = date("Y-m-d H:i:s", strtotime('+1 year',strtotime($vfecha)));
    // Usar vista unión parametros_server y lectura_parametros
    $sselect = "SELECT NOMBREP,PREFIJO,POSDECIMAL,SUM(VALOR) AS VALOR,MES AS HORA,ESTLINK FROM vgrafica_dias ";
    // Controlar si es para exportar
    if ($expexcel == 1) {
        $sselect = "SELECT NOMBREP AS PARAMETRO,SUM(VALOR) AS VALOR,MES,FLECTURA AS FECHA,POSDECIMAL FROM vgrafica_dias ";
    }
    $sselect.="WHERE idparametro = ".$_SESSION['vparam'];
    $sselect.=" AND flectura >= '".date($vdesde)."'";
    $sselect.=" AND flectura < '".date($vhasta)."'";
    $sselect.=" GROUP BY idparametro,MES order by flectura,idparametro";
    return $sselect;
}

function selectall($expexcel = 0) {
    $vtiposalida=4;
    $_SESSION['vparam'] = $_POST['cbvalort'];
    // Formato de fecha estandar yyyy-mm-dd HH:mm:ss
    // Usar vista unión parametros_server y lectura_parametros
    $sselect = "SELECT NOMBREP,PREFIJO,POSDECIMAL,SUM(VALOR) AS VALOR,YEAR AS HORA,ESTLINK FROM vgrafica_dias ";
    // Controlar si es para exportar
    if ($expexcel == 1) {
        $sselect = "SELECT NOMBREP AS PARAMETRO,SUM(VALOR) AS VALOR,YEAR,FLECTURA AS FECHAPOSDECIMAL FROM vgrafica_dias ";
    }
    $sselect.="WHERE idparametro = ".$_SESSION['vparam'];
    $sselect.=" GROUP BY YEAR order by flectura,idparametro";
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
// Procesa los valores y retorna array de valores para el chart
function datachart($row,$ilink,$vtiposalida,$vdesde,$vhasta) {
    $ameses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio', 'Agosto','Septiembre','Octubre','Noviembre','Diciembre');
    $vlabel = "";
    if ($vtiposalida == 3) {
         $vlabel = $ameses[intval($row["HORA"])-1];
    }else {
        $vlabel = $row["HORA"];
    }
    $vlink = "";
    if($ilink == 1) {      
        $vlink = "P-detailsPopUp,width=700,height=400,toolbar=no,scrollbars=no,resizable=no-sectoresdown.php?gdesde=".$vdesde."&ghasta=".$vhasta."&gtiposalida=".$vtiposalida."&gcolumna=".$vlabel;
    }
    $adata = array(
       "label" => $vlabel,
       "value" => posdecimal($row["VALOR"],$row["POSDECIMAL"]), // <-- Se hace la resta con el valor anterior.
       "link" => $vlink
    );
    return $adata;
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
        // Variables desde, hasta para subinforme
        $vdesde = date("Y-m-d");
        $vhasta = date("Y-m-d");
        if (!empty($_POST['cbvalor'])) {
            $sql = selectdia($vdesde,$vhasta);
            $sqlexp = selectdia($vdesde,$vhasta,1);
            $vtiposalida = 1;
            $vtxtpie= 'Fecha de informe:'.$_POST['fhasta'].'.';
            $_SESSION['escsv'] = 1;
            //echo $sql;
        }
        if (!empty($_POST['cbvalorm'])) {
            $sql = selectmes($vdesde,$vhasta);
            $sqlexp = selectmes($vdesde,$vhasta,1);
            $vtiposalida = 2;
            $_SESSION['escsv'] = 1;
            //$vtxtpie= 'Diferencia del mes de '.$ameses[$_POST['cbmes']-1].' de '.$_POST['cbyear'].'.';
            //echo $sql;
        }
        if (!empty($_POST['cbvalory'])) {
            $sql = selectyear($vdesde,$vhasta);
            $sqlexp = selectyear($vdesde,$vhasta,1);
            $vtiposalida = 3;
            $_SESSION['escsv'] = 1;
            //$vtxtpie= 'Diferencia meses del ejercicio '.$_POST['cbyear'].'.';
            //echo 'Combo año:'.$_POST['cbvalory'];
            //echo $sql;
        }
        if (!empty($_POST['cbvalort'])) {
            $sql = selectall();
            $sqlexp = selectall(1);
            $vtiposalida = 4;
            $_SESSION['escsv'] = 1;
            //echo 'Combo total:'.$_POST['cbvalort'];
            //$vtxtpie= 'Diferencia entre ejercicios. Código de parámetro:'.$_POST['cbvalort'].'.';
            //echo $sql;
        }
        $vdesde= date("Y-m-d", strtotime($vdesde));
        $vhasta = date("Y-m-d",strtotime($vhasta));

        // Execute the query, or else return the error message.
        $result = $dbhandle->query($sql) or exit("Código de error ({$dbhandle->errno}): {$dbhandle->error}");

         // If the query returns a valid response, prepare the JSON string
         
         // Quiere que se coga la diferencia, por lo tanto la primera fila no se pinta y la siguiente es la dif de valores.
        // Usar la select de sesión para mostar o no botón.
        $_SESSION['ssql'] = "0";
        
        if ($result) {
            // Guardar SQL en $_POST para realizar el export
            $_SESSION['ssql'] = $sqlexp;
            
            $fila1 = mysqli_fetch_array($result);
            $vvalor = substr($fila1["NOMBREP"],0,20);
            $vprefijo = $fila1["PREFIJO"];
            $ilink = $fila1["ESTLINK"];
            $vtxtpie= "Descargar.";
            //$vvalor.=" / ".$vprefijo;
            // The `$arrData` array holds the chart attributes and data
            $arrData = array(
                "chart" => array(
                  "caption" => "".$vvalor." en (".$vprefijo.")",
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
            $adet = datachart($fila1,$ilink,$vtiposalida,$vdesde,$vhasta);
            array_push($arrData["data"], $adet);
            // Resto de filas en array
            while($row = mysqli_fetch_array($result)) {
                $adet = datachart($row,$ilink,$vtiposalida,$vdesde,$vhasta);
                array_push($arrData["data"], $adet);
            }
            /*--------------------------------------------------------------------------------------------------------------*/
            /*--------------------------------------------------------------------------------------------------------------*/
            
            /*JSON Encode the data to retrieve the string containing the JSON representation of the data in the array. */
            $jsonEncodedData = json_encode($arrData);

    /*Create an object for the column chart using the FusionCharts PHP class constructor. Syntax for the constructor is ` FusionCharts("type of chart", "unique chart id", width of the chart, height of the chart, "div id to render the chart", "data format", "data source")`. Because we are using JSON data to render the chart, the data format will be `json`. The variable `$jsonEncodeData` holds all the JSON data for the chart, and will be passed as the value for the data source parameter of the constructor.*/

            //$columnChart = new FusionCharts("column2D", "Grafica / Hora" , 600, 300, "graf_hora", "json", $jsonEncodedData);
            $columnChart = new FusionCharts("column3d", "Grafica / Hora" , 700, 300, "t_dia", "json", $jsonEncodedData);
            //$columnChart = new FusionCharts("area2d", "Grafica / Hora" , 600, 300, "t_dia", "json", $jsonEncodedData);

            // Render the chart
            $columnChart->render();

            // Close the database connection
            $dbhandle->close();
        }
      ?>     
      <div id="t_dia"> </div>
      <div id="piegrafica">
            <p>  <?php
              if (!empty($vvalor)) {
                  echo $vtxtpie.' ';
                  if($ilink == 1) {      
                    echo 'Pinche sobre el valor para ver su grafíca detallada.';
                  }
              }else {
                  exit("Realice la selección de datos.");
              }
              ?>
           </p>
            <form action="adminExpCsv.php" method="POST" target="_blank">
                <input type="image" name="btexp" src="imagenes/excel.png" border="0" height="50" width="50" alt="Excel" />
            </form>
      </div>
   </body>
</html>
