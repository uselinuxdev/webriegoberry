<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <style>
        div#exportparm{
            background-color: white;
            overflow: hidden;
            overflow-y: scroll;
            height: 250px;
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
            $consulta=$ClassExp->selectParm(); 
            //echo 'Row cargada: '.$row[0];
        ?>
    </head>
    <body>
    <!--Form de parametros.-->
    <form name="exportparm" method="post">
    <h4 style="color:#3A72A5;">Listado de parámetros de exportación</h4>
    <div id="exportparm">
        <table id="tparmname" >
        <thead>
           <tr>
             <th>Parametro</th>
             <th>Nombre C.H.E.</th>
             <th>Divisor</th>
             <th>Alta</th>
             <th>Borrar</th>
           </tr>
        </thead>
        <tbody>
           <?php
           while($row = mysqli_fetch_array($consulta,MYSQLI_ASSOC)) 
           {
                //echo $row[id];
                echo '<input type="hidden" name="id[]" value="'.$row['id'].'">';
                echo '<input type="hidden" name="idexport[]" value="'.$row['idexport'].'">';
                echo '<tr>';
                echo '<td>';
                $ClassExp->cargacomboparam("idparametro[]",$row['idparametro']);
                echo '</td>';
                echo '<td>';
                echo '<input type="text" name="nombreche[]" value="'.$row['nombreche'].'" required="required" /> ';
                echo "</td>";
                echo '<td>';
                echo '<input type="number" name="divisor[]" min="1" max="99999" value="'.$row['divisor'].'" required="required" /> ';
                echo "</td>";
                echo '<td>'.date("d/m/Y", strtotime($row['falta'])).'</td>';
                echo '<form name="fdeleteparm" method="post">';
                echo '<input type="hidden" name="idparmdelete" value="'.$row['id'].'">';
                echo '<td><input type="submit" name="delete_parmexp" value="Borrar"></td>';
                echo '</form>';
                echo '</tr>';
           }    
           ?>
        </tbody>
        </table>
    </div>
    <input type="submit" name="update_parmexp" value="Actualizar">
    <input type="submit" name="insert_parmexp" value="Insertar">
    </form>
    </body>
</html>
