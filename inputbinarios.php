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
//mysql_set_charset('latin1_spanish_ci');

// Combo dependiente
//$sql = "SELECT idparametro, parametro FROM parametros_server where tipo='H' and estado=1 order by idparametro";
$sql = "SELECT parametros_server.idparametro, parametros_server.parametro FROM parametros_server,server_instalacion";
$sql.=" where server_instalacion.idserver = parametros_server.idserver " ;
$sql.=" and server_instalacion.estado = 1 " ;
$sql.=" and parametros_server.tipo like'%B%' and parametros_server.lectura ='M' and parametros_server.estado > 0" ;
$sql.=" and parametros_server.nivel <= ".$_SESSION['nivel'];
//$sql.=" and parametros_server.idserver = ".$_SESSION['idserver'];
$sql.=" order by parametros_server.estado,parametros_server.parametro ";
//$sql.=" and parametros_server.tipo='C' and parametros_server.lectura ='M' and parametros_server.estado=1 " ;


// Cargar la máx fecha para el valor
//if ($_POST['cbvalor'] > 0 ){
//    $sqlf ="select max(flectura) FMAX from lectura_parametros where idparametro=".$_POST['cbvalor'];
//    $rfecha = mysql_query($sqlf);
//    $row = mysql_fetch_array($rfecha);
//    $vfecha =$row["FMAX"];
//}

$vfecha = date("Y-m-d");
if ($_POST['fhasta'] > '1970-01-01') {
   $vfecha = date("Y-m-d", strtotime($_POST['fhasta']));   
}

$vhora = $_POST["nhora"];

// Comprobar valores POST $_POST
//echo 'Valor server' . $_POST['cbserver'];
//echo 'Valor parametro' . $_POST['cbvalor'];

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Grafica de parámetros de la instalación por horas.</title>
          <link href="css/jquery-ui.css" rel="stylesheet">
            <script src="java/jquery.js"></script>
            <script src="java/jquery-ui.js"></script>
            <script>
               // Carga el tab
                $(function() {
                    $( "#digital" ).tabs();
                });
            </script>
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
                });
            </script>
    </head>
    <body>
        <div id="digital">
        <ul>
              <li><a href="#form_digital">Día</a></li>
        </ul>
        <div id="form_digital"> 
        <form id="fserver" method="post"> <!--El parametro action es el archivo que procesara el dato y el parametro method es la forma en que enviara el dato en este caso sera por post -->
            <table border="0" width="90%" cellspacing="10">
                <tbody>
                    <tr>
                    </tr>
                    <tr>
                        <td>
                            <p> <label for="cbvalor">Elegir parámetro</label></p>
                            <!--<select name="cbvalor" onchange='submit()'> Creamos el select con el atributo name "combo" que identificara el archivo -->
                            <select name="cbvalor"> 
                            <?php
                                $resparametros = mysql_query($sql);
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
                            ?>
                            </select>
                        </td>
                        <td>
                            <p><label for="nhora">Hora</label></p>
                            <select name="nhora" id="nhora">
                                <option value="00"<?php if ($vhora == '00') echo ' selected="selected"'; ?>>00</option>
                                <option value="01"<?php if ($vhora == '01') echo ' selected="selected"'; ?>>01</option>
                                <option value="02"<?php if ($vhora == '02') echo ' selected="selected"'; ?>>02</option>
                                <option value="03"<?php if ($vhora == '03') echo ' selected="selected"'; ?>>03</option>
                                <option value="04"<?php if ($vhora == '04') echo ' selected="selected"'; ?>>04</option>
                                <option value="05"<?php if ($vhora == '05') echo ' selected="selected"'; ?>>05</option>
                                <option value="06"<?php if ($vhora == '06') echo ' selected="selected"'; ?>>06</option>
                                <option value="07"<?php if ($vhora == '07') echo ' selected="selected"'; ?>>07</option>
                                <option value="08"<?php if ($vhora == '08') echo ' selected="selected"'; ?>>08</option>
                                <option value="09"<?php if ($vhora == '09') echo ' selected="selected"'; ?>>09</option>
                                <option value="10"<?php if ($vhora == '10') echo ' selected="selected"'; ?>>10</option>
                                <option value="11"<?php if ($vhora == '11') echo ' selected="selected"'; ?>>11</option>
                                <option value="12"<?php if ($vhora == '12') echo ' selected="selected"'; ?>>12</option>
                                <option value="13"<?php if ($vhora == '13') echo ' selected="selected"'; ?>>13</option>
                                <option value="14"<?php if ($vhora == '14') echo ' selected="selected"'; ?>>14</option>
                                <option value="15"<?php if ($vhora == '15') echo ' selected="selected"'; ?>>15</option>
                                <option value="16"<?php if ($vhora == '16') echo ' selected="selected"'; ?>>16</option>
                                <option value="17"<?php if ($vhora == '17') echo ' selected="selected"'; ?>>17</option>
                                <option value="18"<?php if ($vhora == '18') echo ' selected="selected"'; ?>>18</option>
                                <option value="19"<?php if ($vhora == '19') echo ' selected="selected"'; ?>>19</option>
                                <option value="20"<?php if ($vhora == '20') echo ' selected="selected"'; ?>>20</option>
                                <option value="21"<?php if ($vhora == '21') echo ' selected="selected"'; ?>>21</option>
                                <option value="22"<?php if ($vhora == '22') echo ' selected="selected"'; ?>>22</option>
                                <option value="23"<?php if ($vhora == '23') echo ' selected="selected"'; ?>>23</option>
                            </select> <p></p>
                            
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Fecha:</p>          
                            <input type="text" name="fhasta" id="fhasta" value=" <?php
                            echo date("Y-m-d",  strtotime($vfecha))  ?>" size=10" />
                        </td>
                        <td>
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
            
        </div>
    </body>
</html>
