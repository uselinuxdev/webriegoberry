<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <!-- Meta para que IE se comporte como crome -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title></title>
        <link href="css/jquery-ui.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" type="text/css" href="./css/riegoestilos.css">
        <script type="text/javascript" src="fusioncharts/fusioncharts.js"></script>
        <?php
        // Crear clase de para llamada a funciones genericas
        require("riegoresumenClass.php");
        // Controlar que exista sesion iniciada
        require('adminsession.php');
        if (CheckLogin() == false)
        {
            header("Location: login.php");
        }
        $_SESSION['pag'] = "riegoresumen.php";
        // Incluir php de gráficas.
        include("fusioncharts/fusioncharts.php");
        $hostdb = $_SESSION['serverdb'];  // MySQl host
        $userdb = $_SESSION['dbuser'];  // MySQL username
        $passdb = $_SESSION['dbpass'];  // MySQL password
        $namedb = $_SESSION['dbname'];  // MySQL database name

        // Establish a connection to the database
        $dbhandle = new mysqli($hostdb, $userdb, $passdb, $namedb);
        if (!$dbhandle->set_charset("utf8")) {
            printf("Error cargando el conjunto de caracteres utf8: %s\n", $mysqli->error);
            exit();
        }

        if ($dbhandle->connect_error) {
           exit("No se ha podido conectar a la Base de Datos: ".$dbhandle->connect_error);
        }
        ?>
    </head>
    <body>
       <div id="contenedor">
           <div id="cabecera">
                <div id="imgcabecera">
                    <img src="imagenes/RIEGOSOLAR_Blanco.png" alt="Logo"/>
                </div>
           </div>
           <div id="cuerpo">
               <div id="menuleft">
                   <?php
                       //echo 'El nivel del usuario es:'.$_SESSION['nivel'];
                       include 'menugrafica.php';
                   ?>
               </div>
               <div id="resumenleft">
                    <div id="resumenprod">
                         <?php
                             include 'resumenficha.php';
                         ?>
                     </div>

                      <div id="resumenficha">
                         <?php
                             include 'resumenprod.php';
                         ?>
                     </div>
                     <div id="resumenactual">
                         <?php
                           include 'resumenactual.php';
                         ?>
                     </div>
               </div>
               <div id="resumenright">
                    <div id="resumengrafica1">
                        <?php
                          include 'resumengrafica1.php';
                        ?>
                    </div> 
                     <div id="resumengrafica2">
                        <?php
                            include 'resumengrafica2.php';
                        ?>
                    </div>
                     <div id="resumengrafica3">
                        <?php
                           include 'resumengrafica3.php';
                        ?>
                    </div>                      
               </div>
            </div> <!-- Fin del contendor principal -->
            <div id="pie">
                <?php             
                    // Close the database connection
                    $dbhandle->close();
                ?>
            </div>
       </div>
    </body>
</html>