<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <?php
            // Crear clase de para llamada a funciones genericas
            // Control post
            if(isset($_POST['update_install']))
            {
                $ClassInstall = new InstallClass();
                $ClassInstall->updateinstall(); 
            }
            // Funcion para imagen
            if(isset($_POST['update_imagen']))
            {
                $ClassInstall = new InstallClass();
                $ClassInstall->updateimagen(); 
            }
            if(isset($_POST['mail_install']))
            {
                $Classresprod = new riegoresumenClass();
                $Classresprod->cargarClase('resumenprod'); 
                $asumaryprod = $Classresprod->calcsumaryprod();
                $ClassAlert = new AlertClass();
                $ClassAlert->mailsumary($asumaryprod);
            }
            // Cargar datos
            $hostdb = $_SESSION['serverdb'];  // MySQl host
            $userdb = $_SESSION['dbuser'];  // MySQL username
            $passdb = $_SESSION['dbpass'];  // MySQL password
            $namedb = $_SESSION['dbname'];  // MySQL database name
            $portdb = $_SESSION['dbport'];
            // Establish a connection to the database
            $dbhandle = new mysqli($hostdb, $userdb, $passdb, $namedb,$portdb);
            if (!$dbhandle->set_charset("utf8")) {
                printf("Error cargando el conjunto de caracteres utf8: %s\n", $mysqli->error);
                exit();
            }

            if ($dbhandle->connect_error) {
               exit("No se ha podido conectar a la Base de Datos: ".$dbhandle->connect_error);
            }
            $sql = "select cif,titular,falta as farranque,nombre as instalacion,ubicacion,pico,modulos,inversor as variador,imagen,idinstalacion "
                        . "from instalacion "
                        . "where estado = 1";
            // Execute the query, or else return the error message.
            $result = $dbhandle->query($sql) or exit("Codigo de error ({$dbhandle->errno}): {$dbhandle->error}");
            $row = mysqli_fetch_array($result);
            $valta=$row[2];
        ?>
        <script src="java/jquery.js"></script>
        <script src="java/jquery-ui.js"></script>
        <script src="java/jquery.multi-select.js"></script>
        <script>
            $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: '<Ant',
            nextText: 'Sig>',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
            weekHeader: 'Sm',
            dateFormat: 'yy-mm-dd',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''
            };
            $.datepicker.setDefaults($.datepicker.regional['es']);
            //Meter punteros a los diferentes fecha
            $(function () {
                $("#falta").datepicker({
                    changeMonth: true,
                    changeYear: true
                });
                // Controlar si hay un post para asignar valor
                $( "#falta" ).datepicker("setDate", new Date("<?php echo $valta ?>"))
                
            });
        </script>
        <title></title>
    </head>
    <body>
        <div id="install">
            <h4 style="color:#3A72A5;">Administración datos instalación</h4>
            <?php
            echo '<form action="" method="POST" enctype="multipart/form-data">';
            echo '<img src="'.$row[8].'" alt="IMGINSTALL" style="width:400px;" align="middle"/>';
            echo '<input type="hidden" name="idinstalacion[]" value="'.$row['idinstalacion'].'">';
            echo '<input type="file" name="image" />';
            echo '<input type="submit" name="update_imagen" value="Actualizar imagen"/>';
            echo '</form>';
            echo '<form name="install" method="post">';
                    echo '<table cellpadding="0" cellspacing="0" class="db-tbresumen">';
                    echo '<tr>';
                    echo '<input type="hidden" name="idinstalacion[]" value="'.$row['idinstalacion'].'">';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td align="left"><strong>','Instalación','</strong></td>';
                    echo '<td><input type="text" name="nombre[]" size="90" value="'.$row[3].'" required="required" /> </td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td align="left"><strong>','Titular','</strong></td>';
                    echo '<td><input type="text" name="titular[]" size="90" value="'.$row[1].'" required="required" /> </td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td align="left"><strong>','CIF','</strong></td>';
                    echo '<td><input type="text" name="cif[]" size="10" value="'.$row[0].'" required="required" /> </td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td align="left"><strong>','F.Arranque','</strong></td>';
                    echo '<td><input type="text" name="falta[]" id="falta" value="'.$valta.'" required="required size=10" /> </td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td align="left"><strong>','Ubicación','</strong></td>';
                    echo '<td><input type="text" name="ubicacion[]" size="90" value="'.$row[4].'" required="required" /> </td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td align="left"><strong>','Potencia','</strong></td>';
                    echo '<td><input type="text" name="pico[]" size="30" value="'.$row[5].'" required="required" /> </td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td align="left"><strong>','Módulos','</strong></td>';
                    echo '<td><input type="text" name="modulos[]" size="90" value="'.$row[6].'" required="required" /> </td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td align="left"><strong>','Variador','</strong></td>';
                    echo '<td><input type="text" name="variador[]" size="90" value="'.$row[7].'" required="required" /> </td>';
                    echo '</tr>';
                echo '</table>';
                echo '<input type="submit" name="update_install" value="Actualizar"/>';
                echo '<input type="submit" name="mail_install" value="Mail resumen"/>';
                ?>
            </form>
        </div>
    </body>
</html>
