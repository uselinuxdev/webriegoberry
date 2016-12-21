<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
//Primero hacemos las conexiones
mysql_connect($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass']) or die ("No se puede establecer la conexion!!!!"); 
mysql_select_db($_SESSION['dbname']) or die ("Imposible conectar a la base de datos!!!!"); //Selecionas tu base
mysql_set_charset('utf8'); // Importante juego de caracteres a utilizar.

$ameses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio', 'Agosto','Septiembre','Octubre','Noviembre','Diciembre');
//hacemos la consulta de los valores que llenaran los combos
/////////////////////////$sql = "SELECT idserver,nombreserver FROM server_instalacion WHERE estado=1 order by nombreserver";

// Coger los parametros del único server. estado = 0 es parametro calculado y > 0 resto.20/12/2016

$sql = "SELECT parametros_server.idparametro, parametros_server.parametro FROM parametros_server,server_instalacion";
$sql.=" where server_instalacion.idserver = parametros_server.idserver " ;
$sql.=" and server_instalacion.estado = 1 " ;
$sql.=" and parametros_server.tipo='H' and parametros_server.lectura ='M' and parametros_server.estado >= 0 " ;
$sql.=" and parametros_server.nivel <= ".$_SESSION['nivel'];
$sql.=" and parametros_server.idserver = ".$_SESSION['idserver'];
$sql.=" order by parametros_server.estado,parametros_server.parametro ";

// Quitar este if cuando este en producción
//if ($_POST['cbvalor'] > 0){
  //  $sqlf ="select max(flectura) FMAX from lectura_parametros where idparametro=".$_POST['cbvalor'];
  //  $rfecha = mysql_query($sqlf);
  // $row = mysql_fetch_array($rfecha);
  //  $vfecha = $row["FMAX"];
    
//}
if (empty($_POST['fhasta'])){
    $vfecha = date("Y-m-d");
}else {
    $vfecha = date("Y-m-d", strtotime($_POST['fhasta']));   
}

// Variables post RANGO
if (empty($_POST['fechar'])){
    $vfechar = date("Y-m-d");
}else {
    $vfechar = date("Y-m-d", strtotime($_POST['fechar']));   
}
$vhorad = $_POST["nhorad"];
$vhorah = $_POST["nhorah"];


function crearlistas($nombre, $varray,$vseleccion){ 
    $array = $varray; 
    $txt= "<select name='$nombre' id='$nombre'>";  
    for ($i=0; $i<sizeof($array); $i++){ 
        if($i == $vseleccion-1) {
           // <option selected value="1">1. Tipo Cliente</option>
           $txt .= "<option selected value='".$vseleccion."'>". $array[$i] . '</option>';      
        }else {
           $txt .= "<option value='".($i+1)."'>". $array[$i] . '</option>';  
        }    
    } 
    $txt .= '</select>'; 
    return $txt; 
}
function fouryear($nombre) {
    if (empty($_POST[$nombre])){
        $vyear = date("Y");
    }else {
        $vyear = $_POST[$nombre];
    }
    $txt= "<select name='$nombre' id='$nombre'>";  
    for ($i=$vyear -4; $i<$vyear+5; $i++){
        if($i == $vyear) {
           $txt .= "<option selected value='".$vyear."'>". $i . '</option>';      
        }else {
           $txt .= "<option value='".$i."'>". $i . '</option>';  
        }
    }
    $txt .= '</select>'; return $txt; 
}
// Con el objeto si existe o no en el post
function checktab() {
    if (!empty($_POST['cbvalor'])) {
        $_SESSION['stabindex'] = 0;
    }
    if (!empty($_POST['cbvalorm'])) {
        $_SESSION['stabindex'] = 1;
    }
    if (!empty($_POST['cbvalorr'])) {
        $_SESSION['stabindex'] = 2;
    }
}
?>
<html>
    <head>
        <?php
            // Validar el botón pinchado
            checktab();
        ?>
        <meta charset="UTF-8">
        <title>Grafica de parámetros de la instalación por horas.</title>
          <link href="css/jquery-ui.css" rel="stylesheet">
            <script src="java/jquery.js">
            </script>
            <script src="java/jquery-ui.js"></script>
            <script>
                $.datepicker.regional['es'] = {
                closeText: 'Cerrar',
                prevText: '<Ant',
                nextText: 'Sig>',
                currentText: 'Hoy',
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
                dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
                dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
                weekHeader: 'Sm',
                dateFormat: 'yy-mm-dd',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''
                };
                $.datepicker.setDefaults($.datepicker.regional['es']);
                //Meter punteros a los diferentes fecha
                $(function () {
                $("#fhasta").datepicker({
                changeMonth: true,
                changeYear: true
                });
                // Controlar si hay un post para asignar valor
                $( "#fhasta" ).datepicker("setDate", new Date("<?php echo $vfecha ?>"));
                
                $("#fechar").datepicker({
                changeMonth: true,
                changeYear: true
                });
                // Controlar si hay un post para asignar valor
                $( "#fhasta" ).datepicker("setDate", new Date("<?php echo $vfecha ?>"));
                });
            </script>
    </head>
    <body>  
        <div id="periodos">
        <ul>
              <li><a href="#form_dia">Día</a></li>
              <li><a href="#form_mes">Mes</a></li>
              <li><a href="#form_rango">Rango</a></li>
        </ul>
        <div id="form_dia"> 
            <form id="fdia" method="post"> <!--El parametro action es el archivo que procesara el dato y el parametro method es la forma en que enviara el dato en este caso sera por post -->
            <table border="0" width="90%" cellspacing="10">
                <tbody>
                    <tr>
                        <td>
                            <p> <label for="cbvalor">Elegir primer parámetro</label></p>
                            <select name="cbvalor"> <!--Creamos el select con el atributo name "combo" que identificara el archivo -->
                            <?php
                            $resparametros= mysql_query($sql);
                            echo "<option value=0> ( Seleccionar un Parámetro ) </option>"; 
                            while($row = mysql_fetch_array($resparametros)) { //Iniciamos un ciclo para recorrer la variable $resparametros que tiene la consulta previamente hecha 
                            $id = $row["idparametro"] ; //Asignamos el id del campo que quieras mostrar
                            $vparametro = substr($row["parametro"],0,50); // Asignamos el nombre del campo que quieras mostrar
                            //echo "<option value=".$id.">".$vparametro."</option>"; //Llenamos el option con su value que sera lo que se lleve al archivo registrar.php y que sera el id de tu campo y luego concatenamos tbn el nombre que se mostrara en el combo 
                            $vcombo = "<option value=".$id;
                            if($_POST['cbvalor']==$id) {
                                $vcombo = $vcombo. " SELECTED ";
                            }
                            $vcombo = $vcombo.">";
                            $vcombo = $vcombo.$vparametro."</option>"; 
                            echo $vcombo;
                            } //Cerramos el ciclo 
                            mysql_free_result($resparametros);
                            ?>
                            </select>
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Fecha:</p>          
                            <input type="text" name="fhasta" id="fhasta" value="
                                <?php
                                    echo $vfecha;
                                ?>" size=10" />
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                           <input type="submit" value="Aceptar"/>
                        </td>
                    </tr>
                </tbody>
            </table>
            </form> 
        </div>
        <div id="form_mes">
            <form id="fmes" method="post"> <!--El parametro action es el archivo que procesara el dato y el parametro method es la forma en que enviara el dato en este caso sera por post -->
            <table border="0" width="90%" cellspacing="10">
                <tbody>
                    <tr>
                        <td>
                            <p> <label for="cbvalorm">Elegir primer parámetro</label></p>
                            <select name="cbvalorm"> <!--Creamos el select con el atributo name "combo" que identificara el archivo -->
                            <?php
                                $resparametros = mysql_query($sql);
                                echo "<option value=0> ( Seleccionar un Parámetro ) </option>"; 
                                while($row = mysql_fetch_array($resparametros)) { //Iniciamos un ciclo para recorrer la variable $resparametros que tiene la consulta previamente hecha 
                                    $id = $row["idparametro"] ; //Asignamos el id del campo que quieras mostrar
                                    $vparametro = substr($row["parametro"],0,50); // Asignamos el nombre del campo que quieras mostrar
                                    //echo "<option value=".$id.">".$vparametro."</option>"; //Llenamos el option con su value que sera lo que se lleve al archivo registrar.php y que sera el id de tu campo y luego concatenamos tbn el nombre que se mostrara en el combo 
                                    $vcombo = "<option value=".$id;
                                    if($_POST['cbvalorm']==$id) {
                                        $vcombo = $vcombo. " SELECTED ";
                                    }
                                $vcombo = $vcombo.">";
                                $vcombo = $vcombo.$vparametro."</option>"; 
                                echo $vcombo;
                                } //Cerramos el ciclo 
                                mysql_free_result($resparametros);
                            ?>
                            </select>
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p> <label for="cbmes">Mes</label></p>
                            <?php
                                if (empty($_POST['cbmes'])) {
                                    $vmes = date("n");
                                }else{
                                    $vmes = $_POST['cbmes'];
                                }
                                $resultado = crearlistas("cbmes",$ameses, $vmes); 
                                echo $resultado;
                            ?>
                        </td>
                        <td>
                            <p> <label for="cbyear">Ejercicio</label></p>
                            <?php
                                $resultado = fouryear("cbyear"); 
                                echo $resultado;
                            ?>
                        </td>
                    </tr>
                    <tr>
                       <td>
                       </td>
                        <td>
                            <input type="submit" value="Aceptar"/>
                        </td>
                    </tr>
                </tbody>
            </table>
            </form>
        </div>
        <div id="form_rango"> 
            <form id="frango" method="post"> <!--El parametro action es el archivo que procesara el dato y el parametro method es la forma en que enviara el dato en este caso sera por post -->
            <table border="0" width="90%" cellspacing="10">
                <tbody>
                    <tr>
                        <td>
                            <p> <label for="cbvalorr">Elegir primer parámetro</label></p>
                            <select name="cbvalorr"> <!--Creamos el select con el atributo name "combo" que identificara el archivo -->
                            <?php
                            $resparametros= mysql_query($sql);
                            echo "<option value=0> ( Seleccionar un Parámetro ) </option>"; 
                            while($row = mysql_fetch_array($resparametros)) { //Iniciamos un ciclo para recorrer la variable $resparametros que tiene la consulta previamente hecha 
                            $id = $row["idparametro"] ; //Asignamos el id del campo que quieras mostrar
                            $vparametro = substr($row["parametro"],0,50); // Asignamos el nombre del campo que quieras mostrar
                            //echo "<option value=".$id.">".$vparametro."</option>"; //Llenamos el option con su value que sera lo que se lleve al archivo registrar.php y que sera el id de tu campo y luego concatenamos tbn el nombre que se mostrara en el combo 
                            $vcombo = "<option value=".$id;
                            if($_POST['cbvalorr']==$id) {
                                $vcombo = $vcombo. " SELECTED ";
                            }
                            $vcombo = $vcombo.">";
                            $vcombo = $vcombo.$vparametro."</option>"; 
                            echo $vcombo;
                            } //Cerramos el ciclo 
                            mysql_free_result($resparametros);
                            ?>
                            </select>
                        </td>
                       <td>
                            <p>Fecha:</p>          
                            <input type="text" name="fechar" id="fechar" value=" <?php
                            echo $vfechar;
                            ?>" size=10" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p><label for="nhorad">Desde</label></p>
                            <select name="nhorad" id="nhorad">
                                <option value="00"<?php if ($vhorad == '00') echo ' selected="selected"'; ?>>00</option>
                                <option value="01"<?php if ($vhorad == '01') echo ' selected="selected"'; ?>>01</option>
                                <option value="02"<?php if ($vhorad == '02') echo ' selected="selected"'; ?>>02</option>
                                <option value="03"<?php if ($vhorad == '03') echo ' selected="selected"'; ?>>03</option>
                                <option value="04"<?php if ($vhorad == '04') echo ' selected="selected"'; ?>>04</option>
                                <option value="05"<?php if ($vhorad == '05') echo ' selected="selected"'; ?>>05</option>
                                <option value="06"<?php if ($vhorad == '06') echo ' selected="selected"'; ?>>06</option>
                                <option value="07"<?php if ($vhorad == '07') echo ' selected="selected"'; ?>>07</option>
                                <option value="08"<?php if ($vhorad == '08') echo ' selected="selected"'; ?>>08</option>
                                <option value="09"<?php if ($vhorad == '09') echo ' selected="selected"'; ?>>09</option>
                                <option value="10"<?php if ($vhorad == '10') echo ' selected="selected"'; ?>>10</option>
                                <option value="11"<?php if ($vhorad == '11') echo ' selected="selected"'; ?>>11</option>
                                <option value="12"<?php if ($vhorad == '12') echo ' selected="selected"'; ?>>12</option>
                                <option value="13"<?php if ($vhorad == '13') echo ' selected="selected"'; ?>>13</option>
                                <option value="14"<?php if ($vhorad == '14') echo ' selected="selected"'; ?>>14</option>
                                <option value="15"<?php if ($vhorad == '15') echo ' selected="selected"'; ?>>15</option>
                                <option value="16"<?php if ($vhorad == '16') echo ' selected="selected"'; ?>>16</option>
                                <option value="17"<?php if ($vhorad == '17') echo ' selected="selected"'; ?>>17</option>
                                <option value="18"<?php if ($vhorad == '18') echo ' selected="selected"'; ?>>18</option>
                                <option value="19"<?php if ($vhorad == '19') echo ' selected="selected"'; ?>>19</option>
                                <option value="20"<?php if ($vhorad == '20') echo ' selected="selected"'; ?>>20</option>
                                <option value="21"<?php if ($vhorad == '21') echo ' selected="selected"'; ?>>21</option>
                                <option value="22"<?php if ($vhorad == '22') echo ' selected="selected"'; ?>>22</option>
                                <option value="23"<?php if ($vhorad == '23') echo ' selected="selected"'; ?>>23</option>
                            </select> <p></p>
                        </td>
                        <td>
                            <p><label for="nhorah">Hasta</label></p>
                            <select name="nhorah" id="nhorah">
                                <option value="00"<?php if ($vhorah == '00') echo ' selected="selected"'; ?>>00</option>
                                <option value="01"<?php if ($vhorah == '01') echo ' selected="selected"'; ?>>01</option>
                                <option value="02"<?php if ($vhorah == '02') echo ' selected="selected"'; ?>>02</option>
                                <option value="03"<?php if ($vhorah == '03') echo ' selected="selected"'; ?>>03</option>
                                <option value="04"<?php if ($vhorah == '04') echo ' selected="selected"'; ?>>04</option>
                                <option value="05"<?php if ($vhorah == '05') echo ' selected="selected"'; ?>>05</option>
                                <option value="06"<?php if ($vhorah == '06') echo ' selected="selected"'; ?>>06</option>
                                <option value="07"<?php if ($vhorah == '07') echo ' selected="selected"'; ?>>07</option>
                                <option value="08"<?php if ($vhorah == '08') echo ' selected="selected"'; ?>>08</option>
                                <option value="09"<?php if ($vhorah == '09') echo ' selected="selected"'; ?>>09</option>
                                <option value="10"<?php if ($vhorah == '10') echo ' selected="selected"'; ?>>10</option>
                                <option value="11"<?php if ($vhorah == '11') echo ' selected="selected"'; ?>>11</option>
                                <option value="12"<?php if ($vhorah == '12') echo ' selected="selected"'; ?>>12</option>
                                <option value="13"<?php if ($vhorah == '13') echo ' selected="selected"'; ?>>13</option>
                                <option value="14"<?php if ($vhorah == '14') echo ' selected="selected"'; ?>>14</option>
                                <option value="15"<?php if ($vhorah == '15') echo ' selected="selected"'; ?>>15</option>
                                <option value="16"<?php if ($vhorah == '16') echo ' selected="selected"'; ?>>16</option>
                                <option value="17"<?php if ($vhorah == '17') echo ' selected="selected"'; ?>>17</option>
                                <option value="18"<?php if ($vhorah == '18') echo ' selected="selected"'; ?>>18</option>
                                <option value="19"<?php if ($vhorah == '19') echo ' selected="selected"'; ?>>19</option>
                                <option value="20"<?php if ($vhorah == '20') echo ' selected="selected"'; ?>>20</option>
                                <option value="21"<?php if ($vhorah == '21') echo ' selected="selected"'; ?>>21</option>
                                <option value="22"<?php if ($vhorah == '22') echo ' selected="selected"'; ?>>22</option>
                                <option value="23"<?php if ($vhorah == '23') echo ' selected="selected"'; ?>>23</option>
                            </select> 
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                             <input type="submit" value="Aceptar"/>
                        </td>
                    </tr>
                </tbody>
            </table>
            </form> 
        </div>
        </div>
        <script>
            // Cargar tabs de jquery
            $( "#periodos" ).tabs();
            // Seleccionar tab coger el valor de la sesión php
            vselect = <?php echo $_SESSION['stabindex']; ?>;
            $( "#periodos" ).tabs( "option", "active", vselect );
        </script>
    </body>
</html>
