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
                // Execute the query, or else return the error message.
                $result = $dbhandle->query($sql) or exit("Codigo de error ({$dbhandle->errno}): {$dbhandle->error}");
                $row = mysqli_fetch_array($result);
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
