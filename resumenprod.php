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
            $Classresprod = new riegoresumenClass();
            $Classresprod->cargarClase('resumenprod'); 
            $aparam = $Classresprod->verParam();
            // Cargar los datos de hoy ,año y hasta año.
            $ssql = "select Coalesce(max(l.intvalor)-min(l.intvalor),0) AS hoy,p.prefijonum As unidades,p.posdecimal As posdecimal" 
                . " from lectura_parametros l,parametros_server p"
                . " where l.idparametro = ".$aparam[0]['idparametroa']
                . " and l.idparametro = p.idparametro"
                . " and l.flectura > CURDATE();";
            $result = $dbhandle->query($ssql) or exit("Codigo de error ({$dbhandle->errno}): {$dbhandle->error}");
            $rowhoy = mysqli_fetch_array($result);
            // Mes actual
            $mesyear = strtotime(date("Y")."-".date("m")."-01");
            $mesyearfin = date("Y-m-d", strtotime("+1 month", $mesyear));
            $ssql = "select sum(intvalor) as month"
            . " from grafica_dias"
            . " where idparametro=".$aparam[0]['idparametroa']
            . " and flectura >= '".$mesyear."'"
            . " and flectura < '".$mesyearfin."'";
            $result = $dbhandle->query($ssql) or exit("Codigo de error ({$dbhandle->errno}): {$dbhandle->error}");
            $rowmonth = mysqli_fetch_array($result);
            // Cargar año actual
            $eneroyear = date("Y")."01-01";
            $ssql = "select sum(intvalor) as year"
                . " from grafica_dias"
                . " where idparametro=".$aparam[0]['idparametroa']
                . " and flectura > '".$eneroyear."';";
            $result = $dbhandle->query($ssql) or exit("Codigo de error ({$dbhandle->errno}): {$dbhandle->error}");
            $rowyear = mysqli_fetch_array($result);
            // Cargar años ateriores
            $ssql = "select sum(intvalor) as preyear"
            . " from grafica_dias"
            . " where idparametro=".$aparam[0]['idparametroa']
            . " and flectura < '".$eneroyear."';";
            $result = $dbhandle->query($ssql) or exit("Codigo de error ({$dbhandle->errno}): {$dbhandle->error}");
            $rowpreyear = mysqli_fetch_array($result);
        ?>
    </head>
    <body>
        <div class="background">
            <p> Resumen Producción </p>
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
                    $valor = $Classresprod->posdecimal($rowhoy['hoy'],$rowhoy['posdecimal']);
                    $valor = round($valor);
                    echo '<td align="right">',$valor.''.$rowhoy['unidades'],'</td>';
                    // Valor mes
                    $valor = $Classresprod->posdecimal($rowmonth['month'],$rowhoy['posdecimal']);
                    $valor = round($valor);
                    echo '<td align="right">',$valor.''.$rowhoy['unidades'],'</td>';
                    // Valor year
                    $valor = $Classresprod->posdecimal($rowyear['year'],$rowhoy['posdecimal']);
                    $valor = round($valor);
                    echo '<td align="right">',$valor.''.$rowhoy['unidades'],'</td>';
                    // Valor preyear
                    $valor = $Classresprod->posdecimal($rowpreyear['preyear'],$rowhoy['posdecimal']);
                    $valor = round($valor);
                    echo '<td align="right">',$valor.''.$rowhoy['unidades'],'</td>';
                    echo '</tr>';
                echo '</tbody>';
                echo '</table>';
            ?>
        </div>
       </div>
    </body>
</html>
