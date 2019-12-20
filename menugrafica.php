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
            $cndb=mysqli_connect($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname']);
            if (!$cndb) {
                echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
                echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
                echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
                exit;
            }
            mysqli_set_charset($cndb, "utf8");
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
                    $resmenu = mysqli_query($cndb,$vsql); 
                    while($row = mysqli_fetch_array($resmenu,MYSQLI_ASSOC)) {
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
                    mysqli_free_result();     
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

