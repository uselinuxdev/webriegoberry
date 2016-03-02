<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" href="./css/riegoestilos.css">
        <link href="css/jquery-ui.css" rel="stylesheet" type="text/css">
        <?php
        // Controlar que exista sesion iniciada
        require('adminsession.php');
        if (CheckLogin() == false)
        {
            header("Location: login.php");
        }
        $_SESSION['pag'] = "riegobinarios.php";
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
                        include 'menugrafica.php';
                   ?>
                       
               </div>
               <div id="imput">
                   <?php
                        include 'inputbinarios.php';
                   ?>
               </div>
               <div id="output">
               <?php
                    include 'outbinarios.php';
               ?>
               </div>
           </div> <!-- Fin del cuperpo -->
           <div id="pie">
           </div>
       </div> <!-- Fin del contendor principal -->
    </body>
</html>