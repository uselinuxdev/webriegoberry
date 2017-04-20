<!DOCTYPE html>
<?php
//Primero hacemos las conexiones
mysql_connect($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass']) or die ("No se puede establecer la conexion!!!!"); 
mysql_select_db($_SESSION['dbname']) or die ("Imposible conectar a la base de datos!!!!"); //Selecionas tu base
mysql_set_charset('utf8'); // Importante juego de caracteres a utilizar.

?>
<html>
    <head>
        <meta charset="UTF-8">
        <style>
        input[type=text] {
            width: 100%;
            margin: 0px 0;
            border: none;
        }
        div#parameter {
            background-color: white;
            overflow: hidden;
            overflow-y: scroll;
            height: 270px;
        }
        div#bitname {
            background-color: white;
            overflow: hidden;
            overflow-y: scroll;
            height: 180px;
        }
        table {
            border-collapse: collapse;
            tab-size: 100%;
            width: inherit;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #3A72A5;
            color: white;
        }
        tr:hover{background-color:#f5f5f5}
        </style>
        <title></title>
        <!Funciones post>
        <?php
        // Crear clase de para llamada a funciones genericas
        require("ParameterClass.php");
        // Control post

        ?>
    </head>
    <body>
        <h4 style="color:#3A72A5;">Administración parametros server</h4>
        <div id="parameter" style="overflow-x:auto;" >
        <form name="fparameter" method="post">
        <table id="tparameter" >
       <thead>
           <tr>
             <th>Nombre parametro</th>
             <th>Tipo</th>
             <th>P. Memoria</th>
             <th>Posiciones</th>
             <th>Lectura</th>
             <th>Orden</th>
             <th>Prefijo</th>
             <th>Decimales</th>
             <th>Sectores</th>
             <th>Nivel</th>
             <th>Color</th>
           </tr>
        </thead
        <tbody>
           <?php
           $result = mysql_query("SELECT idparametro,idserver,parametro,tipo,posiciones,lectura,pmemoria,estado,prefijonum,posdecimal,falta,comentario,estlink,nivel,color from parametros_server where idserver=".$_SESSION['idserver']." order by estado,parametro");
           while( $row = mysql_fetch_assoc( $result ) ){
           ?>
           <input type="hidden" name="idparametro[]" value="<?php echo $row['idparametro'];?>">
           <input type="hidden" name="idserver[]" value="<?php echo $row['idserver'];?>">
           <tr>
              <td><input type="text" name="parametro[]" size="35" value="<?php echo $row['parametro'];?>" required="required" /> </td>
              <td><input type="text" name="tipo[]" value="<?php echo $row['tipo'];?>" required="required" /> </td>
              <td><input type="text" name="pmemoria[]" value="<?php echo $row['pmemoria'];?>" required="required" /> </td>
              <td><input type="text" name="posiciones[]" value="<?php echo $row['posiciones'];?>" required="required" /> </td>
              <td><input type="text" name="lectura[]" value="<?php echo $row['lectura'];?>" required="required" /> </td>
              <td><input type="text" name="estado[]" value="<?php echo $row['estado'];?>" required="required" /> </td>
              <td><input type="text" name="prefijonum[]" value="<?php echo $row['prefijonum'];?>" required="required" /> </td>
              <td><input type="text" name="posdecimal[]" value="<?php echo $row['posdecimal'];?>" required="required" /> </td>
              <td><input type="text" name="estlink[]" value="<?php echo $row['estlink'];?>" required="required" /> </td>
              <td><input type="text" name="nivel[]" value="<?php echo $row['nivel'];?>" required="required" /> </td>
              <td><input type="text" name="color[]" value="<?php echo $row['color'];?>" required="required" /> </td>
           </tr>
           <?php
           }
           ?>
        </tbody>
        </table>
        </div>
        <input type="submit" name="update_parametros" value="Actualizar" />
        </form>
        <!--Form de binarios.-->    
        <h4 style="color:#3A72A5;">Administración nombres de bits</h4>
        <div id="bitname" style="overflow-x:auto;" >
        <form name="fbitname" method="post">
        <table id="tbitname" >
        <thead>
           <tr>
             <th>Posicion</th>
             <th>nombrebit</th>
           </tr>
        </thead
        <tbody>
           <?php
           $result = mysql_query("SELECT idbit,idparametro,posicion,nombrebit from parametros_bitname order by idparametro,posicion");     
           while( $row = mysql_fetch_assoc( $result ) ){
           ?>
           <input type="hidden" name="idbit[]" value="<?php echo $row['idbit'];?>">
           <input type="hidden" name="idparametro[]" value="<?php echo $row['idparametro'];?>">
           <tr>
              <td><input type="number" name="posicion[]" min="0" max="31" value="<?php echo $row['posicion'];?>" required="required" /> </td>
              <td><input type="text" name="nombrebit[]" size="80" value="<?php echo $row['nombrebit'];?>" required="required" /> </td>
           </tr>
           <?php
           }
           ?>
        </tbody>
        </table>
        </div>
        <input type="submit" name="update_bitname" value="Actualizar" />
        </form>
    </body>
</html>
