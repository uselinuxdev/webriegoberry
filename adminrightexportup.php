<?php

?>
<html>
    <head>
        <meta charset="UTF-8">
        <style>
        div#export {
            overflow: hidden;
            overflow-y: scroll;
            height: 200px;
        }
        input[type=text] {
            width: 100%;
            margin: 0px 0;
        }
        th {
            background-color: #3A72A5;
            color: white;
        }
        tr:hover{background-color:#f5f5f5}
        </style>
        <title></title>
        <?php
            $row=$ClassExp->selectMaster(); 
            //echo 'Row cargada: '.$row[0];
        ?>
    </head>
    <body>
        <div id="export">
            <h4 style="color:#3A72A5;">Administración exportación de datos</h4>
            <?php
            echo '<form name="export" method="post">';
                    echo '<table cellpadding="0" cellspacing="0" class="db-tbresumen">';
                    echo '<tr>';
                    echo '<input type="hidden" name="id[]" value="'.$row['id'].'">';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td align="left"><strong>','Protocolo','</strong></td>';
                    echo '<td>';
                        echo '<select name = "copytype[]" style="width: 90px;">';
                            echo '<option value="S"'; 
                            if($row['copytype'] == 'S') {echo " SELECTED ";} echo">SFTP</option>";
                            echo '<option value="F"'; 
                            if($row['copytype'] == 'F') {echo " SELECTED ";} echo">FTP</option>";
                        echo '</select>';
                    echo '</td>';
                    echo '<td align="left"><strong>','Formato','</strong></td>';
                    echo '<td>';
                        echo '<select name = "format[]" style="width: 90px;">';
                            echo '<option value="CHE"'; 
                            if($row['format'] == 'CHE') {echo " SELECTED ";} echo">C.H.E.</option>";
                        echo '</select>';
                    echo '</td>';
                    echo '<td align="left"><strong>','Intervalo','</strong></td>';
                    echo '<td>';
                        echo '<input type="number" name="grouptime[]" min="0" max="60" value="';
                        echo $row['grouptime'].'" required="required" />';
                    echo '</td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td align="left"><strong>','Servidor','</strong></td>';
                    echo '<td><input type="text" name="server[]" size="50" value="'.$row['server'].'"/> </td>';
                    echo '<td align="left"><strong>','Path','</strong></td>';
                    echo '<td><input type="text" name="path[]" size="50" value="'.$row['path'].'"/> </td>';                    
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td align="left"><strong>','Usuario','</strong></td>';
                    echo '<td><input type="text" name="user[]" size="10" value="'.$row['user'].'"/> </td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td align="left"><strong>','Password','</strong></td>';                    
                    echo '<td><input type="password" name="pass[]" id="falta" value="'.$row['pass'].'" size=10" /> </td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td align="left"><strong>','H. envío','</strong></td>';
                    echo '<td><input type="time" name="hoursend[]" size="30" value="'.$row['hoursend'].'" required="required" /> </td>';
                    echo '<td align="left"><strong>','Activo','</strong></td>';
                    echo '<td>';
                        echo '<select name = "status[]" style="width: 90px;">';
                            echo '<option value="1"'; 
                            if($row['status'] == 1) {echo " SELECTED ";} echo">ACTIVO</option>";
                            echo '<option value="0"'; 
                            if($row['status'] == 0) {echo " SELECTED ";} echo">NO ACTIVO</option>";
                        echo '</select>';
                    echo '</td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td align="left"><strong>','Comentario','</strong></td>';
                    echo '<td><input type="text" name="comment[]" size="30" value="'.$row['comment'].'"/> </td>';
                    echo '<td align="left"><strong>','F.Calculo','</strong></td>';
                    echo '<td><input type="date" name="fcalc[]" size="25" value=""/> </td>';
                    echo '</tr>';
                echo '</table>';
                echo '<input type="submit" name="update_exp" value="Actualizar"/>';
                echo '<input type="submit" name="gentcalc_exp" value="Calcular"/>';
                echo '<input type="submit" name="upload_exp" value="Upload"/>';
                ?>
            </form>
        </div>
    </body>
</html>
