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

// Coger los parametros del único server que puede tener estado 1 para las gráficas.

$sql = "SELECT parametros_server.idparametro, parametros_server.parametro FROM parametros_server,server_instalacion";
$sql.=" where server_instalacion.idserver = parametros_server.idserver " ;
$sql.=" and server_instalacion.estado = 1 " ;
$sql.=" and parametros_server.tipo in('H','HF') and parametros_server.lectura ='H' and parametros_server.estado > 0 " ;
$sql.=" order by parametros_server.estado,parametros_server.parametro ";

// Quitar este if cuando este en producción
//if ($_POST['cbvalor'] > 0){
//    $sqlf ="select max(flectura) FMAX from lectura_parametros where idparametro=".$_POST['cbvalor'];
//    $rfecha = mysql_query($sqlf);
//    $row = mysql_fetch_array($rfecha);
//    $vfecha = $row["FMAX"];   
//}
if (empty($_POST['fhasta'])){
    $vfecha = date("Y-m-d");
}else {
    $vfecha = date("Y-m-d", strtotime($_POST['fhasta']));   
}


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
    if (!empty($_POST['cbvalory'])) {
        $_SESSION['stabindex'] = 2;
    }
    if (!empty($_POST['cbvalort'])) {
        $_SESSION['stabindex'] = 3;
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
                });
            </script>
    </head>
    <body>
        <div id="periodos">
        <ul>
              <li><a href="#form_dia">Día</a></li>
              <li><a href="#form_mes">Mes</a></li>
              <li><a href="#form_year">Año</a></li>
              <li><a href="#form_total">Total</a></li>
        </ul>
        <div id="form_dia"> 
            <form id="fdia" method="post"> <!--El parametro action es el archivo que procesara el dato y el parametro method es la forma en que enviara el dato en este caso sera por post -->
                <table border="0" width="90%" cellspacing="10">
                    <tbody>
                        <tr>
                            <td>
                                <p> <label for="cbvalor">Elegir parámetro</label></p>
                                <select name="cbvalor"> <!--Creamos el select con el atributo name "combo" que identificara el archivo -->
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
                                    mysql_free_result($resparametros);
                                ?>
                                </select>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <p>Fecha:</p>          
                                <input type="text" name="fhasta" id="fhasta" value=" <?php
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
                                <p> <label for="cbvalorm">Elegir parámetro</label></p>
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
                            <td></td>
                            <td>
                                <input type="submit" value="Aceptar"/> 
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div id="form_year">
                <form id="fyear" method="post"> <!--El parametro action es el archivo que procesara el dato y el parametro method es la forma en que enviara el dato en este caso sera por post -->
                    <table border="0" width="90%" cellspacing="10">
                        <tbody>
                            <tr>
                                <td>
                                    <p> <label for="cbvalory">Elegir parámetro</label></p>
                                    <select name="cbvalory"> <!--Creamos el select con el atributo name "combo" que identificara el archivo -->
                                    <?php
                                        $resparametros = mysql_query($sql);
                                        echo "<option value=0> ( Seleccionar un Parámetro ) </option>"; 
                                        while($row = mysql_fetch_array($resparametros)) { //Iniciamos un ciclo para recorrer la variable $resparametros que tiene la consulta previamente hecha 
                                            $id = $row["idparametro"] ; //Asignamos el id del campo que quieras mostrar
                                            $vparametro = substr($row["parametro"],0,50); // Asignamos el nombre del campo que quieras mostrar
                                            //echo "<option value=".$id.">".$vparametro."</option>"; //Llenamos el option con su value que sera lo que se lleve al archivo registrar.php y que sera el id de tu campo y luego concatenamos tbn el nombre que se mostrara en el combo 
                                            $vcombo = "<option value=".$id;
                                            if($_POST['cbvalory']==$id) {
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
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <p> <label for="cbyear">Ejercicio</label></p>
                                    <?php
                                        $resultado = fouryear("cbyear"); 
                                        echo $resultado;
                                    ?>
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
        <div id="form_total">
                <form id="ftotal" method="post"> <!--El parametro action es el archivo que procesara el dato y el parametro method es la forma en que enviara el dato en este caso sera por post -->
                    <table border="0" width="90%" cellspacing="10">
                        <tbody>
                            <tr>
                                <td>
                                    <p> <label for="cbvalort">Elegir parámetro</label></p>
                                    <select name="cbvalort"> <!--Creamos el select con el atributo name "combo" que identificara el archivo -->
                                    <?php
                                        $resparametros = mysql_query($sql);
                                        echo "<option value=0> ( Seleccionar un Parámetro ) </option>"; 
                                        while($row = mysql_fetch_array($resparametros)) { //Iniciamos un ciclo para recorrer la variable $resparametros que tiene la consulta previamente hecha 
                                            $id = $row["idparametro"] ; //Asignamos el id del campo que quieras mostrar
                                            $vparametro = substr($row["parametro"],0,50); // Asignamos el nombre del campo que quieras mostrar
                                            //echo "<option value=".$id.">".$vparametro."</option>"; //Llenamos el option con su value que sera lo que se lleve al archivo registrar.php y que sera el id de tu campo y luego concatenamos tbn el nombre que se mostrara en el combo 
                                            $vcombo = "<option value=".$id;
                                            if($_POST['cbvalort']==$id) {
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
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
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
