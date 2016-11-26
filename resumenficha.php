<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <?php
            //Primero hacemos las conexiones
            mysql_connect($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass']) or die ("No se puede establecer la conexion!!!!"); 
            mysql_select_db($_SESSION['dbname']) or die ("Imposible conectar a la base de datos!!!!"); //Selecionas tu base
            mysql_set_charset('utf8'); // Importante juego de caracteres a utilizar.
        ?>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <div class="background">
            <p> Ficha técnica </p>
        <div class="front">
            <?php
                /* Pintar tabla, el diseño de la tabla esta en riegoestilos.db-tbresumen */
                $sql = "select titular,falta as farranque,nombre as instalacion,ubicacion,pico,modulos,inversor as variador "
                        . "from instalacion "
                        . "where estado = 1";
                $result = mysql_query($sql) or die('Error al leer los datos de la instalación !');
                $row = mysql_fetch_row($result);
                echo '<table cellpadding="0" cellspacing="0" class="db-tbresumen">';
                    echo '<tr>';
                    echo '<td align="right"><strong>','Titular:','</strong></td>';
                    echo '<td>',$row[0],'</td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td align="right"><strong>','F.Arranque:','</strong></td>';
                    echo '<td>',date("d/m/Y", strtotime($row[1])),'</td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td align="right"><strong>','Instalación:','</strong></td>';
                    echo '<td>',$row[2],'</td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td align="right"><strong>','Ubicación:','</strong></td>';
                    echo '<td>',$row[3],'</td>';
                    echo '<td align="right"><strong>','Pico:','</strong></td>';
                    echo '<td>',$row[4],'</td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td align="right"><strong>','Módulos:','</strong></td>';
                    echo '<td>',$row[5],'</td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td align="right"><strong>','Variador:','</strong></td>';
                    echo '<td>',$row[6],'</td>';
                    echo '</tr>';
                echo '</table><br />';
            ?>
        </div>
      </div>
    </body>
</html>
