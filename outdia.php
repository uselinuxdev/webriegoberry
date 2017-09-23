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
$ilink = 0;
$vdesde = date("Y-m-d H:i:s");
$vhasta = date("Y-m-d H:i:s");
$vgroup = "checked";

$sqlexp = $sql;


/*Render an error message, to avoid abrupt failure, if the database connection parameters are incorrect */
if ($dbhandle->connect_error) {
   exit("No se ha podido conectar a la Base de Datos: ".$dbhandle->connect_error);
}
// Incluir php de gráficas.
include("fusioncharts/fusioncharts.php");

// Funciones de diferentes select
function selectdia($vparam,&$vdesde,&$vhasta,$expexcel = 0) {
    $vtiposalida = 1;
    $_SESSION['vparam'] = $vparam;
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
    $sselect = "SELECT IDLECTURA,IDPARAMETRO,NOMBREP,PREFIJO,POSDECIMAL,COLOR,VALOR,DATE_FORMAT(FLECTURA,'%H') AS HORA,ESTLINK FROM vgrafica_horas ";
    // Controlar si es para exportar
    if ($expexcel == 1) {
        $sselect = "SELECT NOMBREP AS PARAMETRO,COLOR,VALOR,DATE_FORMAT(FLECTURA,'%H') AS HORA,FLECTURA AS FECHA,POSDECIMAL FROM vgrafica_horas ";
    }
    $sselect.="WHERE idparametro in(".$_SESSION['vparam'].")";
    $sselect.=" AND flectura >= '".date($vdesde)."'";
    $sselect.=" AND flectura < '".date($vhasta)."'";
    $sselect.=" order by flectura,idparametro";
    return $sselect;
}

function selectmes($vparam,&$vdesde,&$vhasta,$expexcel = 0) {
    $vtiposalida = 2;
    $_SESSION['vparam'] = $vparam;
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
    $sselect ="SELECT IDPARAMETRO,NOMBREP,PREFIJO,POSDECIMAL,COLOR,VALOR,DIA AS HORA,ESTLINK,DATE_FORMAT(flectura,'%Y-%m-%d') AS FLECTURA FROM vgrafica_dias ";
    // Controlar si es para exportar
    if ($expexcel == 1) {
        $sselect ="SELECT NOMBREP AS PARAMETRO,COLOR,VALOR,DIA,FLECTURA AS FECHA,POSDECIMAL FROM vgrafica_dias ";
    }
    $sselect.="WHERE idparametro in(".$_SESSION['vparam'].")";
    $sselect .=" AND flectura >= '".date($vdesde)."'";
    $sselect .=" AND flectura < '".date($vhasta)."'";
    // Si es  el mes actual añadir los últimos días sino menter el order by.
    if(date('n') == $vmes) {
        $sselect .=" union ";
        if ($expexcel == 0) {
            $sselect .=" SELECT IDPARAMETRO,NOMBREP AS PARAMETRO,PREFIJO,POSDECIMAL,COLOR,SUM(VALOR) AS VALOR,DIA AS HORA,ESTLINK,DATE_FORMAT(flectura,'%Y-%m-%d') AS FLECTURA FROM vgrafica_horas "; 
        }else {
            $sselect .=" SELECT NOMBREP AS PARAMETRO,COLOR,SUM(VALOR) AS VALOR,DIA,FLECTURA AS FECHA,POSDECIMAL FROM vgrafica_horas "; 
        }
        $sselect.="WHERE idparametro in(".$_SESSION['vparam'].")";
        $sselect .=" AND flectura > CURRENT_DATE() - INTERVAL 2 DAY";  
        $sselect .=" group by NOMBREP,DATE_FORMAT(flectura,'%Y-%m-%d') order by flectura,idparametro";
    }else {
        $sselect .=" order by flectura,idparametro";
    }
    //echo $sselect;
    return $sselect;
}

function selectyear($vparam,&$vdesde,&$vhasta,$expexcel = 0) {
    $vtiposalida=3;
    $_SESSION['vparam'] = $vparam;
    $vyear = $_POST['cbyear'];
    // Formato de fecha estandar yyyy-mm-dd HH:mm:ss
    $vfecha = "01-01-".$vyear;
    //echo $vfecha;
    $vdesde = date("Y-m-d H:i:s", strtotime('+0 hours', strtotime($vfecha)));
    $vhasta = date("Y-m-d H:i:s", strtotime('+1 year',strtotime($vfecha)));
    // Usar vista unión parametros_server y lectura_parametros
    $sselect = "SELECT NOMBREP,PREFIJO,POSDECIMAL,COLOR,SUM(VALOR) AS VALOR,MES AS HORA,ESTLINK FROM vgrafica_dias ";
    // Controlar si es para exportar
    if ($expexcel == 1) {
        $sselect = "SELECT NOMBREP AS PARAMETRO,COLOR,SUM(VALOR) AS VALOR,MES,FLECTURA AS FECHA,POSDECIMAL FROM vgrafica_dias ";
    }
    $sselect.="WHERE idparametro in(".$_SESSION['vparam'].")";
    $sselect.=" AND flectura >= '".date($vdesde)."'";
    $sselect.=" AND flectura < '".date($vhasta)."'";
    $sselect.=" GROUP BY idparametro,MES order by flectura,idparametro";
    return $sselect;
}

function selectall($vparam,$expexcel = 0) {
    $vtiposalida=4;
    $_SESSION['vparam'] = $vparam;
    // Formato de fecha estandar yyyy-mm-dd HH:mm:ss
    // Usar vista unión parametros_server y lectura_parametros
    $sselect = "SELECT NOMBREP,PREFIJO,POSDECIMAL,COLOR,SUM(VALOR) AS VALOR,YEAR AS HORA,ESTLINK FROM vgrafica_dias ";
    // Controlar si es para exportar
    if ($expexcel == 1) {
        $sselect = "SELECT NOMBREP AS PARAMETRO,COLOR,SUM(VALOR) AS VALOR,YEAR,FLECTURA AS FECHAPOSDECIMAL FROM vgrafica_dias ";
    }
    $sselect.="WHERE idparametro in (".$_SESSION['vparam'].")";
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

function getsql($valor,$vtiposalida,&$vdesde,&$vhasta,$expexcel = 0)
{
    switch ($vtiposalida) {
    case 1:
        return selectdia($valor,$vdesde,$vhasta,$expexcel);
        break;
    case 2:
        return selectmes($valor,$vdesde,$vhasta,$expexcel);
        break;
    case 3:
        return selectyear($valor,$vdesde,$vhasta,$expexcel);
        break;
    case 4:
        return selectall($valor,$expexcel);
        break;
    }
}
// Funciones multiseries
function configchar($arrayp,$vtiposalida,&$ilink)
{
    // Recorrer el array de todos los parametros
    $adata= array();
    $acat= array();
    $adet= array();
    
    $afilas = array();
    
    // Variables desde, hasta para subinforme
    $vdesde = date("Y-m-d");
    $vhasta = date("Y-m-d");
    
    // Cargar datos en array
    $link = new PDO("mysql:host=".$_SESSION['serverdb'].";dbname=".$_SESSION['dbname'], $_SESSION['dbuser'], $_SESSION['dbpass']);
    $sql = getsql($arrayp[0],$vtiposalida,$vdesde,$vhasta,0);
    //echo $sql;
    $result = $link->query($sql);
    
    $afilas = $result->fetchAll(PDO::FETCH_ASSOC);
    // General chart
    $adata = chart();
    $adet = datachart($afilas,$vdesde,$vhasta,$vtiposalida,$ilink);
    
    // Las categorias
    $acat = categorychart($afilas,$vtiposalida);

    $adata["categories"]=[["category"=>$acat]];
    // creating dataset object
    $adata["dataset"] = [$adet];
    // Recorrer el resto del array
    $longitud = count($arrayp);
    $sexcel = $arrayp[0];
    for($i=1; $i<$longitud; $i++)
    {
        $sql = getsql($arrayp[$i],$vtiposalida,$vdesde,$vhasta,0);
        $result = $link->query($sql);
        $afilas = $result->fetchAll(PDO::FETCH_ASSOC);
        $adet = datachart($afilas,$vdesde,$vhasta,$vtiposalida);
        // ERROR???
        array_push($adata["dataset"],$adet);
        // Control select excel.
        $sexcel .= ','.$arrayp[$i];
    }
    // Retornar el excel
    $sqlexp = getsql($sexcel,$vtiposalida,$vdesde,$vhasta,0);
    // Añadir todos los parametros del array
    
    // Guardar SQL en $_POST para realizar el export
    $_SESSION['ssql'] = $sqlexp;
    
    //var_dump($adata);
    return $adata;
}

function chart()
{
    $arrData = [
                "chart" => [
                 // Labelstep cada cuanto pinta la barra de abajo  
                  //  "caption" => "".$textox."",
                 // "palettecolors"=>  "0080C0",  // Colores de la grafica
                    "exportEnabled" => 1,
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
                  ]
               ];
    //var_dump($arrData);
    return $arrData;
}

function categorychart($array,$vtiposalida) {
    // Categorias. Valores X de la gráfica
    $arrCat = array();
    // Array de meses.
    $ameses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio', 'Agosto','Septiembre','Octubre','Noviembre','Diciembre');
    // Recorrer todas las filas del arraya
    $longitud = count($array);
    for($i=0; $i<$longitud; $i++)
    {
        if ($vtiposalida ==3){
           $array[$i]["HORA"] = $ameses[$array[$i]["HORA"] - 1];
        }
        array_push($arrCat, array("label" => "".$array[$i]["HORA"]));
    }
    //var_dump($ameses);
    return $arrCat;
}

function datachart($array,$vdesde,$vhasta,$vtiposalida,&$ilink)
{
    // Datos. Valores Y de la gráfica. Varias series
    $arrDat = array();
    $afilas = array();
    // Poner en array la serie y el tipo de renderizado
    $vserie = substr($array[0]["NOMBREP"],0,20)." ".$array[0]["PREFIJO"];
    
   //  Recorrer todas las filas del arraya
    $longitud = count($array);
    for($i=0; $i<$longitud; $i++)
	{
            // Control de link de sectores
            $vlink = "";
            if($array[$i]["ESTLINK"] == 1) {
                $ilink = 1;
                $vlink = "P-detailsPopUp,width=700,height=400,toolbar=no,scrollbars=no,resizable=no-sectoresdown.php?gdesde=".$vdesde."&ghasta=".$vhasta."&gtiposalida=".$vtiposalida."&gcolumna=".$array[$i]["HORA"];
            }   
            // Calculo valor
            $vvalor = posdecimal($array[$i]["VALOR"],$array[$i]["POSDECIMAL"]);
            array_push($afilas, array("value" => $vvalor,"color" => "".$array[$i]["COLOR"]."", "link" => $vlink));
           // array_push($afilas, array("value" => $vvalor, "link" => $vlink));
        }  
    $arrDat = ["seriesName"=> "".$vserie."", "data"=>$afilas,"color" => "".$array[0]["COLOR"].""];
    //var_dump ($arrDat);
    return $arrDat;
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
            $vtiposalida = 1;
            $vtxtpie= 'Fecha de informe:'.$_POST['fhasta'].'.';
            $_SESSION['escsv'] = 1;
            $vgroup = $_POST['ckgroup'];
            $arrData = configchar($_POST['cbvalor'],$vtiposalida,$ilink);
            //echo $sql;
        }
        if (!empty($_POST['cbvalorm'])) {
            $vtiposalida = 2;
            $_SESSION['escsv'] = 1;
            $vgroup = $_POST['ckgroupm'];
            $arrData = configchar($_POST['cbvalorm'],$vtiposalida,$ilink);
            //$vtxtpie= 'Diferencia del mes de '.$ameses[$_POST['cbmes']-1].' de '.$_POST['cbyear'].'.';
            //echo $sql;
        }
        if (!empty($_POST['cbvalory'])) {
            $vtiposalida = 3;
            $_SESSION['escsv'] = 1;
            $vgroup = $_POST['ckgroupy'];
            $arrData = configchar($_POST['cbvalory'],$vtiposalida,$ilink);
            //$vtxtpie= 'Diferencia meses del ejercicio '.$_POST['cbyear'].'.';
            //echo 'Combo año:'.$_POST['cbvalory'];
            //echo $sql;
        }
        if (!empty($_POST['cbvalort'])) {
            $vtiposalida = 4;
            $_SESSION['escsv'] = 1;
            $vgroup = $_POST['ckgroupt'];
            $arrData = configchar($_POST['cbvalort'],$vtiposalida,$ilink);
            //echo 'Combo total:'.$_POST['cbvalort'];
            //$vtxtpie= 'Diferencia entre ejercicios. Código de parámetro:'.printr($_POST['cbvalort']).'.';
            //echo $sql;
        }
            
        /*JSON Encode the data to retrieve the string containing the JSON representation of the data in the array. */
        $jsonEncodedData = json_encode($arrData);

        /*Create an object for the column chart using the FusionCharts PHP class constructor. Syntax for the constructor is ` FusionCharts("type of chart", "unique chart id", width of the chart, height of the chart, "div id to render the chart", "data format", "data source")`. Because we are using JSON data to render the chart, the data format will be `json`. The variable `$jsonEncodeData` holds all the JSON data for the chart, and will be passed as the value for the data source parameter of the constructor.*/

        //$columnChart = new FusionCharts("column2D", "Grafica / Hora" , 600, 300, "graf_hora", "json", $jsonEncodedData);
        // Controlar la agrupación de datos
        if ($vgroup == 'checked'){
            $columnChart = new FusionCharts("stackedcolumn3dline", "Grafica / Hora" , 700, 300, "t_dia", "json", $jsonEncodedData);
        }else{
            $columnChart = new FusionCharts("mscolumn3d", "Grafica / Hora" , 700, 300, "t_dia", "json", $jsonEncodedData);
        }

        // Render the chart
        $columnChart->render();

        // Close the database connection
        $dbhandle->close();
      ?>     
      <div id="t_dia"> </div>
      <div id="piegrafica">
            <p>  <?php
              if (!empty($arrData)) {
                if ($ilink == 1) {
                    echo $vtxtpie.' '.'Pinche sobre el valor para ver su grafíca detallada.';
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
