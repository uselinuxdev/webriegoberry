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
        ?>
        <div id="menuacordeon">
            <h4><p onClick="location.href='riegohoras.php'" onmouseover="" style="cursor: pointer;"> 
                    Valores Instantaneos
                </p>
            </h4>
            <h4><p onClick="location.href='riegodia.php'" onmouseover="" style="cursor: pointer;">
                Valores por Horas
                </p>
            </h4>
            <h4><p onClick="location.href='riegobinarios.php'" onmouseover="" style="cursor: pointer;">
                Estado de Valores Digitales
                </p>
            </h4>
        </div>
    </body>
</html>
