<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-- PRUEBA GIT LOCAL
-->
<html>
<?php
//Primero hacemos las conexiones
$hostdb = $_SESSION['serverdb'];  // MySQl host
$userdb = $_SESSION['dbuser'];  // MySQL username
$passdb = $_SESSION['dbpass'];  // MySQL password
$namedb = $_SESSION['dbname'];  // MySQL database name
mysql_set_charset('utf8'); // Importante juego de caracteres a utilizar.
// Establish a connection to the database
$dbhandle = new mysqli($hostdb, $userdb, $passdb, $namedb);

// Definir el titulo para la exportacion
$_SESSION['expnombre'] = 'expinstantaneos';

$vtiposalida = 0; // 1 dia,2 mes, 3 año, 4 total.
$vlabelstep = 12;
$vgroup = "checked";
$sqlexp = $sql;

/*Render an error message, to avoid abrupt failure, if the database connection parameters are incorrect */
if ($dbhandle->connect_error) {
   exit("No se ha podido conectar a la Base de Datos: ".$dbhandle->connect_error);
}

// Incluir php de gráficas.
include("fusioncharts/fusioncharts.php");

// Funciones de diferentes select
function selectdia($vparam,$expexcel = 0) {
    $vtiposalida = 1; 
    $_SESSION['vparam'] = $vparam;
    // Formato de fecha estandar yyyy-mm-dd HH:mm:ss
    $vfecha =date('Y-m-d',strtotime($_POST['fhasta'])); 
    //echo $_POST['fhasta'];
    $vdesde = date("Y-m-d H:i:s", strtotime('+0 hours', strtotime($vfecha)));
    //echo $vdesde;
    $vhasta = date("Y-m-d H:i:s", strtotime('+1 days',strtotime($vfecha)));
    //vhasta = '2015-03-07 00:00:00';
   // echo $vhasta;
    // Usar vista unión parametros_server y lectura_parametros
    $sselect = "SELECT IDPARAMETRO,NOMBREP,PREFIJO,POSDECIMAL,COLOR,VALOR,DATE_FORMAT(FLECTURA,'%H:%i') AS HORA,DATE_FORMAT(FLECTURA,'%H:%i') AS FECHA FROM vgrafica ";
    // Controlar si es para exportar
    if ($expexcel == 1) {
        $sselect = "SELECT IDPARAMETRO,NOMBREP AS PARAMETRO,COLOR,VALOR,DATE_FORMAT(FLECTURA,'%H:%i') AS HORA,FLECTURA AS FECHA,POSDECIMAL FROM vgrafica ";
    }
    $sselect.="WHERE idparametro in (".$vparam.")";
    $sselect.=" AND flectura >= '".date($vdesde)."'";
    $sselect.=" AND flectura < '".date($vhasta)."'";
    $sselect.=" ORDER BY idparametro,flectura";
    return $sselect;
}

function selectmes($vparam,$expexcel = 0) {
    $vtiposalida = 2; 
    $_SESSION['vparam'] = $vparam;
    $vmes = $_POST['cbmes'];
    $vyear = $_POST['cbyear'];
    // Formato de fecha estandar yyyy-mm-dd HH:mm:ss
    $vfecha = $vyear."-".$vmes."-01";
    $vdesde = date("Y-m-d H:i:s", strtotime($vfecha));
    //echo $vdesde;
    $vhasta = date("Y-m-d H:i:s", strtotime('+1 month',strtotime($vfecha)));
    //$vhasta = '2018-01-02 00:00:00';
    //echo $vhasta;
   // Usar vista unión parametros_server y lectura_parametros
    $sselect = "SELECT IDPARAMETRO,NOMBREP,PREFIJO,POSDECIMAL,COLOR,VALOR,DATE_FORMAT(FLECTURA,'%d %H:%i') AS HORA,DATE_FORMAT(FLECTURA,'%H:%i') AS FECHA FROM vgrafica ";
    // Controlar si es para exportar
    if ($expexcel == 1) {
        $sselect = "SELECT IDPARAMETRO,NOMBREP AS PARAMETRO,COLOR,VALOR,DATE_FORMAT(FLECTURA,'%d %H:%i') AS HORA AS HORA,FLECTURA AS FECHA,POSDECIMAL FROM vgrafica ";
    }
    $sselect.="WHERE idparametro in (".$vparam.")";
    $sselect.=" AND flectura >= '".date($vdesde)."'";
    $sselect.=" AND flectura < '".date($vhasta)."'";
    $sselect.=" ORDER BY idparametro,flectura";
    return $sselect;
}

function selectyear($vparam,$expexcel = 0) {
    $vtiposalida=3;
    $_SESSION['vparam'] = $vparam;
    $vyear = $_POST['cbyear'];
    // Formato de fecha estandar yyyy-mm-dd HH:mm:ss
    $vfecha = "01-01-".$vyear;
    //echo $vfecha;
    $vdesde = date("Y-m-d H:i:s", strtotime('+0 hours', strtotime($vfecha)));
    $vhasta = date("Y-m-d H:i:s", strtotime('+1 year',strtotime($vfecha)));
    // Usar vista unión parametros_server y lectura_parametros
    $sselect = "SELECT IDPARAMETRO,NOMBREP,PREFIJO,POSDECIMAL,COLOR,VALOR,MES as HORA,DATE_FORMAT(FLECTURA,'%H:%i') AS FECHA FROM vgrafica ";
    // Controlar si es para exportar
    if ($expexcel == 1) {
        $sselect = "SELECT IDPARAMETRO,NOMBREP AS PARAMETRO,COLOR,VALOR,MES,FLECTURA AS FECHA,POSDECIMAL FROM vgrafica ";
    }
    $sselect.="WHERE idparametro in (".$vparam.")";
    $sselect.=" AND flectura >= '".date($vdesde)."'";
    $sselect.=" AND flectura < '".date($vhasta)."'";
    $sselect.=" ORDER BY idparametro,flectura";
    return $sselect;
}

function selectall($vparam,$expexcel = 0) {
    $vtiposalida=4;
    $_SESSION['vparam'] = $vparam;
    // Formato de fecha estandar yyyy-mm-dd HH:mm:ss
    // Usar vista unión parametros_server y lectura_parametros
    $sselect = "SELECT IDPARAMETRO,NOMBREP,PREFIJO,POSDECIMAL,COLOR,VALOR,YEAR as HORA,DATE_FORMAT(FLECTURA,'%H:%i') AS FECHA FROM vgrafica ";
    // Controlar si es para exportar
    if ($expexcel == 1) {
        $sselect = "SELECT IDPARAMETRO,NOMBREP AS PARAMETRO,COLOR,VALOR,YEAR,FLECTURA AS FECHA,POSDECIMAL FROM vgrafica ";
    }
    $sselect.="WHERE idparametro in (".$vparam.")";
    $sselect.=" ORDER BY idparametro,flectura";
    return $sselect;
}

function selectrango($vparam,$expexcel = 0) {
    $vtiposalida = 5; 
    $_SESSION['vparam'] = $vparam;
    // Formato de fecha estandar yyyy-mm-dd HH:mm:ss
    $vfecha =date('Y-m-d',strtotime($_POST['fechar'])); 
    //echo $_POST['fhasta'];
    //echo $vfecha;
    $vhorad = $_POST['nhorad'].':00:00';
    //echo $vhorad;
    $vhorah = $_POST['nhorah'].':00:00';
    //echo $vhorah;
    //$vfecha .= ' '.$vhora;
    $vformat = intval($vhorad).' hours';
    $vdesde = date("Y-m-d H:i:s", strtotime($vformat, strtotime($vfecha)));
    //echo $vdesde;
    $vformat = intval($vhorah).' hours';
    $vhasta = date("Y-m-d H:i:s", strtotime($vformat,strtotime($vfecha)));
    //vhasta = '2015-03-07 00:00:00';
    //echo $vhasta;
    // Usar vista unión parametros_server y lectura_parametros
    $sselect = "SELECT IDPARAMETRO,NOMBREP,PREFIJO,POSDECIMAL,COLOR,VALOR,DATE_FORMAT(FLECTURA,'%H:%i') AS HORA,DATE_FORMAT(FLECTURA,'%H:%i') AS FECHA FROM vgrafica ";
    // Controlar si es para exportar
    if ($expexcel == 1) {
        $sselect = "SELECT IDPARAMETRO,NOMBREP AS PARAMETRO,VALOR,COLOR,DATE_FORMAT(FLECTURA,'%H:%i') AS HORA,FLECTURA AS FECHA,POSDECIMAL FROM vgrafica ";
    }
    $sselect.="WHERE idparametro in (".$vparam.")";
    $sselect.=" AND flectura > '".date($vdesde)."'";
    $sselect.=" AND flectura < '".date($vhasta)."'";
    $sselect.=" ORDER BY idparametro,FECHA";
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
        
    }
    // Retorna valor sin tocar o con la division
    return $valor;           
}

function getsql($valor,$vtiposalida,$expexcel = 0)
{
    switch ($vtiposalida) {
    case 1:
        return selectdia($valor,$expexcel);
        break;
    case 2:
        return selectmes($valor,$expexcel);
        break;
    case 3:
        return selectyear($valor,$expexcel);
        break;
    case 4:
        return selectall($valor,$expexcel);
        break;
    case 5:
        return selectrango($valor,$expexcel); 
        break;
    }
}

// Funciones multiseries
function configchar($arrayp,$vlabelstep,$textox,$vtiposalida)
{
    // Recorrer el array de todos los parametros
    set_time_limit(120); // Para que no de timeout
    $adata= array();
    $acat= array();
    $adet= array();
    $ameses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio', 'Agosto','Septiembre','Octubre','Noviembre','Diciembre');
    
    $afilas = array();
    
    // Cargar datos en array
    $link = new PDO("mysql:host=".$_SESSION['serverdb'].";dbname=".$_SESSION['dbname'], $_SESSION['dbuser'], $_SESSION['dbpass']);  
    // General chart
    $adata = chart($vlabelstep,$textox); 
    // Recorrer el resto del array
    $longitud = count($arrayp);
    $sparm = $arrayp[0];
    for($i=1; $i<$longitud; $i++)
    {
        $sparm .= ','.$arrayp[$i];
    }
    $sqlexp = getsql($sparm,$vtiposalida,$vdesde,$vhasta,0);
    $result = $link->query($sqlexp);
    $afilas = $result->fetchAll(PDO::FETCH_ASSOC);
    //print_r($afilas);
    // Las categorias
    $acat = categorychart($afilas,$vtiposalida,$ameses);
    $adata["categories"] = array();
    array_push($adata["categories"], array("category"=>$acat));
    
    // Datos de detalles
    $adata["dataset"] = array();
    $adet = datachart($afilas,$vdesde,$vhasta,$vtiposalida,$ameses,$ilink);
    //print_r($adet);
    for($i = 0; $i< count($adet); $i++) {
        //var_dump($aserie);
        $aserie = $adet[$i]["DET"];
        //var_dump($aserie);
        $aseriefin = array();
    //        // En vez de recorrer el array de valores de la serie. Recorrer siempre el array de categorías y pintar hueco o valor
        foreach($acat as $fcreate) {
            //print_r(array_search($fcreate["label"], array_column($aserie,"label")))." / ".$fcreate["label"];
            // Si existe categoria en array, sino, pinta 0
            $ipos = array_search($fcreate["label"], array_column($aserie,"label"));
            if (false !== $ipos) {
                //echo "Existe ".$fcreate["label"]." en ".$key." / ";
                array_push($aseriefin, array("value" => $aserie[$ipos]["value"]));
            }else {
                //echo "No existe ".$fcreate["label"]." en ".$key." / ";
                array_push($aseriefin, array("value" => 0)); 
            }
        }
     // Añadir serie
     array_push($adata["dataset"], array("seriesName"=> $aserie[0]["serie"],"renderAs"=>"area","data"=>$aseriefin,"color" => $aserie[0]["color"]));
    }
    //print_r($adata);
    // Retornar el excel
    $sqlexp = getsql($sparm,$vtiposalida,$vdesde,$vhasta,1);
    // Guardar SQL en $_POST para realizar el export
    $_SESSION['ssql'] = $sqlexp;
    
    //var_dump($adata);
    return $adata;
}

function chart($vlabelstep,$textox)
{
    $arrData = [
                "chart" => [
                    // Labelstep cada cuanto pinta la barra de abajo  
//                    "labelStep" => "".$vlabelstep."",
//                    "showvalues"=>  "0",
//                    "xaxisname"=>  "".$textox."",
//                    "yaxisvaluespadding"=> "10",
//                    //"canvasborderalpha"=>  "0",
//                   // "canvasbgalpha"=>  "0",
//                    "numvdivlines"=>  "3",
//                    //"plotgradientcolor"=>  "0000FF",
//                    "drawanchors"=>  "1",
//                    //"plotfillangle"=>  "90",
//                    //"plotfillalpha"=>  "63",
//                   // "vdivlinealpha"=>  "22",
//                    //"vdivlinecolor"=>  "6281B5",
//                    //"bgcolor"=>  "ABCAD3,B3CCE1",
//                    "showplotborder"=>  "0",
//                   // "bordercolor"=>  "9DBCCC",
//                   // "borderalpha"=>  "100",
//                  //  "canvasbgratio"=>  "0",
//                   // "basefontcolor"=>  "37444A",
//                   //// "tooltipbgcolor"=>  "37444A",
//                   // "tooltipbordercolor"=>  "37444A",
//                   // "tooltipcolor"=>  "FFFFFF",
//                    "basefontsize"=>  "8",
//                    "outcnvbasefontsize"=>  "11",
//                    "animation"=>  "1",
//                   // "palettecolors"=>  "#FD9927,#FECE2F,#9DCD3F,#CECD42,#64D3D1",   //<--- Colores de la grafica
//                    "showtooltip"=>  "1",
//                    "showborder"=>  "0"
                    "exportEnabled" => 1,
                    "labelStep" => "".$vlabelstep."",
                    "xaxisname"=>  "".$textox."",
                    "plotgradientcolor"=> "",
                    "bgcolor"=> "FFFFFF",
                    "showalternatehgridcolor"=> "0",
                    "showplotborder"=> "0",
                    "showvalues"=> "0",
                    "divlinecolor"=> "CCCCCC",
                    "showcanvasborder"=> "0",
                    "canvasborderalpha"=>"0",
                    "showborder"=> "0"
                  ]
               ];
    //var_dump($arrData);
    return $arrData;
}
function categorychart($afilas,$vtiposalida,$ameses) {
    $acat = array();
    // Recorrer todas la filas
    foreach ($afilas as $afila) {
        $acat[$afila["HORA"]]=$afila["HORA"];
    }
    array_multisort($acat);
    // Recorrer el array y pintar el final
    $acatfin = array();
    foreach ($acat as $aval)
    {
        if ($vtiposalida ==3){
            $aval = $ameses[$aval - 1];
        }
        array_push($acatfin, array("label" => $aval));
    }
//    var_dump($arrCat);
    //var_dump($acatfin);
    return $acatfin;
//    return $arrCat;
}
function datachart($array,$vdesde,$vhasta,$vtiposalida,$ameses,&$ilink)
{
    // Datos. Valores Y de la gráfica. Varias series
    $aserie = array();
    $aseries = array();
   //  Recorrer todas las filas del arraya
    $longitud = count($array);
    $idpar = $array[0]["idparametro"];
    for($i=0; $i<$longitud; $i++)
	{
            // Control de series
            //print_r($array[$i]["idparametro"]);
            if ($idpar <> $array[$i]["idparametro"]) 
            {
                array_push($aseries,array("idparametro" => $idpar,"DET" => $aserie));
                $idpar = $array[$i]["idparametro"];
                //print_r($idpar);
                $aserie = array();
            }
            // Control de link de sectores
            $vserie = substr($array[$i]["NOMBREP"],0,20)." ".$array[$i]["PREFIJO"];

            // Calculo valor
            
            $vvalor = posdecimal($array[$i]["VALOR"],$array[$i]["POSDECIMAL"]);
            //echo " - VALOR ".$vvalor;
            $vlabel = $array[$i]["HORA"];
            if ($vtiposalida ==3){
                $vlabel = $ameses[$vlabel - 1];
            }
            array_push($aserie, array("serie" => $vserie,"renderAs"=>"area", "label"=> "".$vlabel."", "value" => $vvalor," color" => "".$array[$i]["COLOR"].""));
        }
        // Tiene q ser al final igual
        array_push($aseries,array("idparametro" => $idpar,"DET" => $aserie));
    // Recorrer la series y pintar 0 para todas las categorías.
    
    return $aseries;
}

?>

    <head>
        <meta charset="UTF-8">
        <script src="fusioncharts/fusioncharts.js"></script>
        <title></title>
    </head>
    <body>
        <?php
        if (!empty($_POST['cbvalor'])) {
            $vtiposalida = 1;
            $vlabelstep = 12;
            $textox = "Horas";
            $vtxtpie= 'Fecha de informe:'.$_POST['fhasta'].'.';
            $_SESSION['escsv'] = 1;
            $vgroup = $_POST['ckgroup'];
            $arrData = configchar($_POST['cbvalor'],$vlabelstep,$textox,$vtiposalida);
            //echo $sql;
        }
        if (!empty($_POST['cbvalorm'])) {
            $vtiposalida = 2;
            $vlabelstep = 288;
            $textox = "Dias";
            $_SESSION['escsv'] = 1;
            $vgroup = $_POST['ckgroupm'];
            $arrData = configchar($_POST['cbvalorm'],$vlabelstep,$textox,$vtiposalida);
            //$vtxtpie= 'Informe del mes de '.$ameses[$_POST['cbmes']-1].' de '.$_POST['cbyear'].'.';
            //echo $sql;
        }
        if (!empty($_POST['cbvalory'])) {
            $vtiposalida = 3;
            $vlabelstep = 1;
            $textox = "Meses";
            $_SESSION['escsv'] = 1;
            $vgroup = $_POST['ckgroupy'];
            $arrData = configchar($_POST['cbvalory'],$vlabelstep,$textox,$vtiposalida);
            //$vtxtpie= 'Informe del ejercicio '.$_POST['cbyear'].'.';
            //echo 'Combo año:'.$_POST['cbvalory'];
            //echo $sql;
        }
        if (!empty($_POST['cbvalort'])) {
            $vtiposalida = 4;
            $vlabelstep = 1;
            $textox = "Ejercicio";
            $_SESSION['escsv'] = 1;
            $vgroup = $_POST['ckgroupt'];
            $arrData = configchar($_POST['cbvalort'],$vlabelstep,$textox,$vtiposalida);
            //echo 'Combo total:'.$_POST['cbvalort'];
            //$vtxtpie= 'Informe acumulado total por ejercicio. Código de parámetro:'.$_POST['cbvalort'].'.';
            //echo $sql;
        }
        if (!empty($_POST['cbvalorr'])) {
            $vtiposalida = 5;
            $vlabelstep = 12;
            $textox = "Horas";
            $_SESSION['escsv'] = 1;
            $vgroup = $_POST['ckgroupr'];
            $arrData = configchar($_POST['cbvalorr'],$vlabelstep,$textox,$vtiposalida);
            //echo 'Combo total:'.$_POST['cbvalort'];
            //$vtxtpie= 'Informe acumulado total por ejercicio. Código de parámetro:'.$_POST['cbvalort'].'.';
            //echo $sql;
        }
        //var_dump($arrData);
        // Usar la select de sesión para mostar o no botón.
        $jsonEncodedData = json_encode($arrData);
        //echo $jsonEncodedData;
        /*Create an object for the column chart using the FusionCharts PHP class constructor. Syntax for the constructor is ` FusionCharts("type of chart", "unique chart id", width of the chart, height of the chart, "div id to render the chart", "data format", "data source")`. Because we are using JSON data to render the chart, the data format will be `json`. The variable `$jsonEncodeData` holds all the JSON data for the chart, and will be passed as the value for the data source parameter of the constructor.*/

        //$columnChart = new FusionCharts("area2d", "Grafica / Hora" , 700, 300, "graf_hora", "json", $jsonEncodedData);
        // Crear convinación de areas
        // Controlar la agrupación de datos
        if ($vgroup == 'checked'){
            $columnChart = new FusionCharts("stackedarea2d", "Grafica / Hora" , 700, 300, "graf_hora", "json", $jsonEncodedData);
        }else{
            $columnChart = new FusionCharts("mscombi2d", "Grafica / Hora" , 700, 300, "graf_hora", "json", $jsonEncodedData);
        }
        // Render the chart
        $columnChart->render();

        // Close the database connection
        $dbhandle->close();
      ?>
      <div id="graf_hora"><!-- Fusion Charts will render here--></div>
      <div id="piegrafica">
        <p><?php
            if (!empty($arrData)) {
               echo $vtxtpie.' ';
            }else{
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
