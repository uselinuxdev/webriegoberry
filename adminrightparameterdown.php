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
        div#bitname {
            background-color: white;
            overflow: hidden;
            overflow-y: scroll;
            height: 180px;
        }
        input[type=text] {
            width: 100%;
            margin: 0px 0;
            border: none;
        }
        th {
            background-color: #3A72A5;
            color: white;
        }
        tr:hover{background-color:#f5f5f5}
        </style>
        <title></title>
    </head>
    <body>
       <!--Form de binarios.-->    
        <h4 style="color:#3A72A5;">Administración nombres de bits</h4>
        <form name="fbitname" method="post">
        <div id="bitname" style="overflow-x:auto;" >
        <!--Cargar combo-->
        <?php $ClassParam->cargacombobit();?>
        <table id="tbitname" >
        <thead>
           <tr>
             <th>Posicion</th>
             <th>nombrebit</th>
             <th>Borrar</th>
           </tr>
        </thead>
        <tbody>
           <?php
           $cndb=mysqli_connect($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname']);
           if (!$cndb) {
                echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
                echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
                echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
                exit;
           }
           mysqli_set_charset($cndb, "utf8");
           $result = mysqli_query($cndb,"SELECT idbit,idparametro,posicion,nombrebit from parametros_bitname where idparametro=".$_POST['cbvalorbit']." order by idparametro,posicion");   
           while( $row = mysqli_fetch_array($result,MYSQLI_ASSOC) ){
               $iddelete = $row['idbit'];
           ?>
           <input type="hidden" name="idbit[]" value="<?php echo $row['idbit'];?>">
           <input type="hidden" name="idparametro[]" value="<?php echo $row['idparametro'];?>">
           <tr>
              <td><input type="number" name="posicion[]" min="0" max="31" value="<?php echo $row['posicion'];?>" required="required" /> </td>
              <td><input type="text" name="nombrebit[]" size="80" value="<?php echo $row['nombrebit'];?>"/> </td>
              <form name="fdeletebit" method="post">
              <input type="hidden" name="idbitdelete" value="<?php echo $row['idbit'];?>">
              <td><input type="submit" name="delete_bit" value="Borrar"></td>
              </form>
           </tr>
           <?php
           }
           ?>
        </tbody>
        </table>
        </div>
        <input type="submit" name="update_bitname" value="Actualizar">
        <input type="submit" name="insert_bitname" value="Insertar">
        </form>
    </body>
</html>
