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
        <?php
            $Classresprod = new riegoresumenClass();
            $Classresprod->cargarClase('resumenprod'); 
            $asumaryprod = $Classresprod->calcsumaryprod();
           // print_r($asumaryprod);
        ?>
    </head>
    <body>
        <div class="background">
            <p> Resumen Producción: <?php //echo substr($asumaryprod[0]['parametro'],0,20); ?> </p>
        <div class="front">
            <?php
                echo '<table cellpadding="0" cellspacing="5" height="100%" class="db-tbresumen">';
                echo '<tbody>';
                    echo '<tr>';
                    echo '<td align="right"><strong>','Hoy','</strong></td>';
                    echo '<td align="right"><strong>','Mes actual','</strong></td>';
                    echo '<td align="right"><strong>','Año '.date("Y"),'</strong></td>';
                    echo '<td align="right"><strong>','Hasta '.date("Y"),'</strong></td>';
                    echo '</tr>';
                    echo '<tr>';
                    // Valor hoy
                    echo '<td align="right">',$asumaryprod[0]['hoy'],'</td>';
                    // Valor mes
                    echo '<td align="right">',$asumaryprod[0]['month'],'</td>';
                    // Valor year
                    echo '<td align="right">',$asumaryprod[0]['year'],'</td>';
                    // Valor preyear
                    echo '<td align="right">',$asumaryprod[0]['preyear'],'</td>';
                    echo '</tr>';
                echo '</tbody>';
                echo '</table>';
            ?>
        </div>
       </div>
    </body>
</html>
