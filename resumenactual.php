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
        <?php
            $Classactual = new riegoresumenClass();
            $Classactual->cargarClase('resumenactual'); 
            $amax = $Classactual->maxlecturap();
            // Cargar todos los del array
            $ssql = "select * from vgrafica where idlectura in(";
            // Realizar la carga de todos los max encontrados 1-3.
            $long = count($amax);
            for($i=0;$i<$long;$i++)
            {
                // Controlar ,
                if ($i > 0){$ssql .=",";}
                $ssql .= $amax[$i];
            }
            // Cerrar in
            $ssql .= ")";
            // Cargar valores
            $result = $dbhandle->query($ssql) or exit("Codigo de error ({$dbhandle->errno}): {$dbhandle->error}");
        ?>
    </head>
    <body>
        <div class="background">
            <p> Actualmente </p>
        <div class="front">
            <?php
                echo '<table cellpadding="0" cellspacing="0" height="100%" class="db-tbresumen">';
                echo '<tbody>';
                    // Recorrer todas la filas encontradas
                    while($ahora = mysqli_fetch_array($result))
                    {
                        $vvalor = $Classactual->posdecimal($ahora["VALOR"],$ahora["POSDECIMAL"]);
                        echo '<tr>';
                        echo '<td align="right"><strong>',$ahora["NOMBREP"].":</strong> ".'</td>';
                        echo '<td align="left">'.number_format($vvalor,2,",",".")." ".$ahora["PREFIJO"].'</td>';
                        echo '</tr>';
                    }
                echo '</tbody>';
                echo '</table><br/>';
                // Tabla de imagen
                echo '<table cellpadding="0" cellspacing="0" height="100%" class="db-tbresumen">';
                echo '<tbody>';
                echo '<tr><tda lign="center">';
                echo '<img src="imagenes/1_instalacion.jpg" alt="IMGINSTALL" style="width:200px;height:112px;"/>';
                echo '</td></tr>';
                echo '</tbody>';
                echo '</table><br/>';
                
            ?>
        </div>
       </div>
    </body>
</html>