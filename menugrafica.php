<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
            <link href="css/jquery-ui.css" rel="stylesheet" type="text/css"/> 
            <script src="java/jquery.js"></script>
            <script src="java/jquery-ui.js"></script>
             <script>
                $(function() {
                    $( "#menuacordeon" ).accordion();
                });
            </script>
    </head>
    <body>
        <?php
            //echo $_SESSION['pag'];
            //Primero hacemos las conexiones
            mysql_connect($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass']) or die ("No se puede establecer la conexion!!!!"); 
            mysql_select_db($_SESSION['dbname']) or die ("Imposible conectar a la base de datos!!!!"); //Selecionas tu base
            mysql_set_charset('utf8'); // Importante juego de caracteres a utilizar.
        ?>
        <div id="menuacordeon">
            <h4>
                <h4>
                    <?php
                    // Cargar el menú custom. Nivel igual o inferior
                    $vsql = "select descripcion,php,nivel "
                            . "from menucustom "
                            . "where nivel <= ".$_SESSION['nivel'].";";
                    //echo $vsql;
                    // Recorrer los resultados
                    $resmenu = mysql_query($vsql);
                    while($row = mysql_fetch_array($resmenu)) {
                        $vdescripcion = $row['descripcion'];
                        $vphp = $row['php'];
                        echo "<br>";
                        // Control de menú seleccionado
                        if ($_SESSION['pag'] == $vphp) {
                            echo "<pos>";
                        }else{
                            echo "<npos>";
                        }  
                        // Añadir menú con datos de select
                        $vmenu ="<p onClick=\"location.href='".$vphp."'\" onMouseover=\"\" style=\" cursor: pointer;\">".$vdescripcion."</p>";
                        echo $vmenu;
                        echo "</pos>";
                        echo "<npos>";
                    }
                    mysql_free_result($resmenu);       
                    ?>  
<!--                Dejar último br.-->
                    <br>
                </h4>
            </h4>
        </div>
        <div id="divriegotec">
            <img src="imagenes/riego-tec.jpg" alt="Logpie"/>
        </div>
    </body>
</html>

