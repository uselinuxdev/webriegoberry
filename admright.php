<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
// Con el objeto si existe o no en el post
// Poner los requeridos
require("InstallClass.php");
require("ZigbeeClass.php");
require("ParameterClass.php");
require("UserClass.php");
require("AlertClass.php");
require("riegoresumenClass.php");

function checktab() {
    // Control de tab seleccionadas
    // Control parametros
    if (!empty($_POST['update_p']) or !empty($_POST['delete_p']) or !empty($_POST['insert_p']) or !empty($_POST['update_bitname'])or !empty($_POST['delete_bit']) or !empty($_POST['cbvalorbit']) or !empty($_POST['insert_bitname'])) {
        $_SESSION['stabindex'] = 1;
    }
    // Control resumen
    if (!empty($_POST['update_resumen'])or !empty($_POST['insert_resumen']) or !empty($_POST['comboestimado']) or !empty($_POST['update_estimacion']) or !empty($_POST['check_estimacion']) or !empty($_POST['insert_estimacion'])) {
        $_SESSION['stabindex'] = 2;
    }
    if (!empty($_POST['update_nodo']) or !empty($_POST['insert_nodo']) or !empty($_POST['delete_nodo']) or !empty($_POST['update_sector']) or !empty($_POST['insert_sector']) or !empty($_POST['delete_sector']) or !empty($_POST['carganodo'])) {
        $_SESSION['stabindex'] = 3;
    }
    if (!empty($_POST['update_user']) or !empty($_POST['insert_user']) or !empty($_POST['delete_user'])) {
        $_SESSION['stabindex'] = 4;
    }
    if (!empty($_POST['update_alert']) or !empty($_POST['insert_alert']) or !empty($_POST['delete_alert'])  or !empty($_POST['check_alert']) or !empty($_POST['check_email'])) {
        $_SESSION['stabindex'] = 5;
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
              <li><a href="#form_user">Usuarios</a></li>
              <li><a href="#form_alert">Alertas</a></li>
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
            <?php
                include 'adminrightresumen.php';
            ?>
        </div>
        <div id="form_zigbee"> 
            <?php
                include 'adminrightnode.php';
            ?>
        </div>
         <div id="form_user"> 
            <?php
                include 'adminrightuser.php';
            ?>
        </div>
        <div id="form_alert"> 
            <?php
                include 'adminrightalert.php';
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
