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
            // Cargar imagen instalación
            $ssql ="select * from instalacion";
            $result = $dbhandle->query($ssql) or exit("Codigo de error ({$dbhandle->errno}): {$dbhandle->error}");
            $install = mysqli_fetch_array($result);
            // Cargar todos los del array
            $ssql = "select * from vgrafica where idlectura in(";
            // Realizar la carga de todos los max encontrados 1-3.
            $long = count($amax);
            for($i=0;$i<$long;$i++)
            {
                // Controlar 
                if ($i > 0){$ssql .=",";}
                if (empty($amax[$i])) {$amax[$i] = 0;}
                $ssql .= $amax[$i];
            }
            // Cerrar in
            $ssql .= ")";
            // Cargar valores
            //echo $ssql;
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
                        echo '<td align="left"><strong>',$ahora["NOMBREP"]."</strong> ".'</td>';
                        echo '<td align="left"><strong>: </strong>'.number_format($vvalor,2,",",".")." ".$ahora["PREFIJO"].'</td>';
                        echo '</tr>';
                    }
                echo '</tbody>';
                echo '</table>';     
            ?>
        </div>
       </div>
    </body>
</html>
