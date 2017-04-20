<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
// Con el objeto si existe o no en el post
function checktab() {
    // Control de tab seleccionadas
    if (!empty($_POST['update_nodo'])) {
        $_SESSION['stabindex'] = 3;
    }
    if (!empty($_POST['update_sectores'])) {
        $_SESSION['stabindex'] = 3;
    }
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Administración Instalación.</title>
        <link href="css/jquery-ui.css" rel="stylesheet">
        <link href="css/jq-styles.css" rel="stylesheet" type="text/css">
        <script src="java/jquery.js"></script>
        <script src="java/jquery-ui.js"></script>
        <script src="java/jquery.multi-select.js"></script>
        <?php
            // Validar el botón pinchado
            checktab();
        ?>
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
            <?php
                include 'adminrightinstall.php';
            ?>
        </div>
        <div id="form_param">
            <?php
                include 'adminrightparameter.php';
            ?>
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
            vselect = <?php echo $_SESSION['stabindex']; ?>;
            $( "#secciones" ).tabs( "option", "active", vselect );
        </script>
    </body>
</html>
