<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Administración Instalación.</title>
        <link href="css/jquery-ui.css" rel="stylesheet">
        <link href="css/jq-styles.css" rel="stylesheet" type="text/css">
        <script src="java/jquery.js"></script>
        <script src="java/jquery-ui.js"></script>
        <script src="java/jquery.multi-select.js"></script>    
    </head>
    <body>  
        <div id="secciones">
        <ul>
              <li><a href="#form_install">Instalación</a></li>
              <li><a href="#form_param">Parametros</a></li>
              <li><a href="#form_resum">Resumen</a></li>
              <li><a href="#form_zigbee">Zigbee</a></li>
        </ul>
        <div id="form_install"> 
        </div>
        <div id="form_param">
        </div>
        <div id="form_resum"> 
        </div>
        <div id="form_zigbee"> 
            <?php
                include 'adminrightnode.php';
            ?>
        </div>
        </div>
        <script>
            // Cargar tabs de jquery
            $( "#secciones" ).tabs();
            // Seleccionar tab coger el valor de la sesión php
            //vselect = <?php //echo $_SESSION['stabindex']; ?>;
            //$( "#secciones" ).tabs( "option", "active", vselect );
        </script>
    </body>
</html>
