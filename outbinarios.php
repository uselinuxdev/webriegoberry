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

$sqlexp = $sql;

//mysql_set_charset('latin1_spanish_ci');
// Establish a connection to the database
$dbhandle = new mysqli($hostdb, $userdb, $passdb, $namedb);

// Definir el titulo para la exportacion
$_SESSION['expnombre'] = 'expdigitales';

/*Render an error message, to avoid abrupt failure, if the database connection parameters are incorrect */
if ($dbhandle->connect_error) {
   exit("No se ha podido conectar a la Base de Datos: ".$dbhandle->connect_error);
}

// Incluir php de gráficas.
include("fusioncharts/fusioncharts.php");
/*
 Create a `lineChart` chart object using the FusionCharts PHP class constructor. 
 * Syntax for the constructor is `FusionCharts("type of chart", "unique chart id", "width of chart", "height of chart", "div id to render the chart", "data format", "data source")`. 
 * To load data from an XML string, `xml` is passed as the value for the data format parameter of the constructor. 
 * The actual XML data for the chart is passed as a string to the constructor.
 */

// Cargar datos de tabla. Añadir filas generias para el resto.
function nombrebits($dbhandle, $idparametro, $tbits)
{
    //$sql = "select posicion,nombrebit from parametros_bitname ";
    $sql = "select posicion,nombrebit from parametros_bitname ";
    // Controlar si es para exportar
    $sql.=" WHERE idparametro = ".$idparametro;
    $sql.=" order by posicion"; 
   // echo $sql;
    $rvalores = $dbhandle->query($sql) or exit("Código de error ({$dbhandle->errno}): {$dbhandle->error}");
    $abdfilas = array();
    $abdindice = array();
    if ($rvalores) {
        // Recorrer todas las filas
        while($fila = mysqli_fetch_array($rvalores)) {
           // echo $row['posicion'];
            array_push($abdfilas,$fila);
            // Añadir para las busqueda por ser php < 5.5 (Array unidimensionales).
            array_push($abdindice,$fila['posicion']);
        }
        // Crear el array con el nombre de todos los bits bd + genéricos
        $afinal = array();
        // Ir creando filas
        for($i = 0; $i <$tbits; $i++) {
            // Buscar posición en indices de bd
            $iposbd = array_search($i,$abdindice);
            //echo $iposbd;
            if($iposbd === false){
                // Default
                $fdefault =  array ('posicion' => $i,
                                    'nombrebit' => 'Posición bit '.$i);
                array_push($afinal,$fdefault); 
            }else
                {
                //45echo $abdfilas[$iposbd]['nombrebit'];
                array_push($afinal,$abdfilas[$iposbd]);
            }
        }
    }
    // Retornar el array
    return $afinal;
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
        // Variables post
        //echo 'Valor server' . $_POST['cbserver'];
        //echo 'Valor parametro' . $_POST['cbvalor'];
        ?><p><?php
        if (empty($_POST['cbvalor']) || $_POST['cbvalor'] == 0 ) {
             exit("Realice la selección de datos.");
        }
        ?></p><?php
        
        $_SESSION['vparam'] = $_POST['cbvalor'];
        //echo $_SESSION['vparam'];
        $vparam = $_SESSION['vparam'];
        
        // Formato de fecha estandar yyyy-mm-dd HH:mm:ss
        $vfecha =date('Y-m-d',strtotime($_POST['fhasta'])); 

        $vdesde = date("Y-m-d H:i:s", strtotime('+'.$_POST['nhora'].' hours', strtotime($vfecha)));
        //echo $vdesde;
        $vhasta = date("Y-m-d H:i:s", strtotime('+1 hours', strtotime($vdesde)));
        // Usar vista unión parametros_server y lectura_parametros
        $sql = "SELECT NOMBREP,PREFIJO,WORDVALOR,HORA FROM vgrafica ";
        $sqlexp = "SELECT NOMBREP AS PARAMETRO,WORDVALOR AS VALOR,HORA,FLECTURA AS FECHA,POSDECIMAL FROM vgrafica ";
        $sql.="WHERE idparametro = ".$vparam;
        $sqlexp.="WHERE idparametro = ".$vparam;
        $sql.=" AND flectura >= '".date($vdesde)."'";
        $sqlexp.=" AND flectura >= '".date($vdesde)."'";
        $sql.=" AND flectura < '".date($vhasta)."'";
        $sqlexp.=" AND flectura < '".date($vhasta)."'";
        //echo $sql;
        // Execute the query, or else return the error message.
        $result = $dbhandle->query($sql) or exit("Código de error ({$dbhandle->errno}): {$dbhandle->error}");
        
        // Usar la select de sesión para mostar o no botón.
        $_SESSION['ssql'] = "0";

         // If the query returns a valid response, prepare the JSON string
        if ($result) {
            // Guardar SQL en $_POST para realizar el export
            $_SESSION['ssql'] = $sqlexp;
            
            $_SESSION['escsv'] = 1;
            
            // Cargar primera fila
            $fila1 = mysqli_fetch_array($result);            
            $vvalor = substr($fila1["NOMBREP"],0,20);
            $vprefijo = $fila1["PREFIJO"];
            // The `$arrData` array holds the chart attributes and data
            // Cargar nombre de bits.
            //$nombits = array();
            $nombits = nombrebits($dbhandle,$vparam, strlen($fila1["WORDVALOR"])); 
            $vprin = '{
                        "chart": {
                            "caption": "'.$vvalor.'",
                            "subcaption": "Por mapa de color",
                            "xaxisname": "Hora lectura",
                            "bgcolor": "CACBEE",
                            "showBorder": "0"
                        },';
                            // "yaxisname": "Número de entrada",  <-- Nombre de las filas
            $vdataset = '"dataset": [
                        {
                            "data": [';
            // Pitar primera fila de valores word
            for ($i = 0; $i < strlen($fila1["WORDVALOR"]); $i++) {
                    $vtxtfila ='{ 
                            ';
                   // $vtxtfila.='"rowid": "'.$i.'",
                    $vtxtfila.='"rowid": "'.$nombits[$i]['nombrebit'].'",
                            ';
                    $vtxtfila.='"columnid": "'.$fila1["HORA"].'",
                            ';
                    $vtxtfila.='"value": "'.$fila1["WORDVALOR"][$i].'"
                            ';
                    $vtxtfila.='},
                             ';
                    $vdataset.=$vtxtfila;
            }
            
            // Recorrer todas las filas
            while($row = mysqli_fetch_array($result)) {
                // Recorrer las 16 posiciones de 1 y 0.
                  /* Cada valor tiene q ser el siguiente formato
                   *  {
                        "rowid": "Google",
                        "columnid": "Mon",
                        "value": "68"
                    },*/
                
                for ($i = 0; $i < strlen($row["WORDVALOR"]); $i++) {
                    $vtxtfila ='{ 
                            ';
                    //$vtxtfila.='"rowid": "'.$i.'",
                    $vtxtfila.='"rowid": "'.$nombits[$i]['nombrebit'].'",
                            ';
                    $vtxtfila.='"columnid": "'.$row["HORA"].'",
                            ';
                    $vtxtfila.='"value": "'.$row["WORDVALOR"][$i].'"
                            ';
                    $vtxtfila.='},
                             ';
                    $vdataset.=$vtxtfila;
                }
             }
             // Quitar la última coma del bucle q sobra añadir final de array
             $vdataset = substr($vdataset, 0, strlen($vdataset)-1);
             $vdataset.= '                ]
            }
            ],';
            $vpie = ' "colorrange": {
                        "gradient": "0",
                        "minvalue": "0",
                        "code": "F1C4EE",
                        "startlabel": "OFF",
                        "endlabel": "ON",
                        "color": [
                            {
                                "code": "FF654F",
                                "minvalue": "0",
                                "maxvalue": "1",
                                "label": "OFF"
                            },
                            {
                                "code": "028FF6",
                                "minvalue": "2",
                                "maxvalue": "2",
                                "label": "Desconocido"
                            },
                            {
                                "code": "8BBA00", 
                                "minvalue": "0",
                                "maxvalue": "1",
                                "label": "ON"
                            }
                        ]
                    }
                }';
                // Juntamos las 3 partes.
                $valoresjson = $vprin.$vdataset.$vpie;
                /*
                Create a `lineChart` chart object using the FusionCharts PHP class constructor. 
                * Syntax for the constructor is `FusionCharts("type of chart", "unique chart id", "width of chart", "height of chart", "div id to render the chart", "data format", "data source")`. 
                * To load data from an XML string, `xml` is passed as the value for the data format parameter of the constructor. 
                * The actual XML data for the chart is passed as a string to the constructor.
                */

                // Crear el objeto grafica
                $gChart = new FusionCharts("heatmap", "Migrafica1" , 710, 350, "divgrafica", "json",$valoresjson);
                                                  // <-- Finalizamos el new.

                // Render the chart
                $gChart->render();

                // Close the database connection
                $dbhandle->close();
        }
      ?>

      <div id="divgrafica"><!-- Fusion Charts will render here--></div>
      <div id="piegrafica">
        <p> <?php 
                  echo 'Descargar.';
            ?> 
        </p> 
        <form action="adminExpCsv.php" method="POST" target="_blank">
            <input type="image" name="btexp" src="imagenes/excel.png" border="0" height="50" width="50" alt="Excel" />
        </form>
      </div>
   </body>
</html>
