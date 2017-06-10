<?php

?>
<html>
    <head>
        <meta charset="UTF-8">
        <style>
        div#parameter {
            background-color: white;
            overflow: hidden;
            overflow-y: scroll;
            height: 270px;
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
        <h4 style="color:#3A72A5;">Administración parametros server</h4>
        <form name="fparameter" method="post">
        <input type="submit" name="update_p" value="Actualizar">
        <input type="submit" name="insert_p" value="Insertar">
        <div id="parameter" style="overflow-x:auto;">
        <table id="tparameter">
        <thead>
           <tr>
             <th>Servidor</th>  
             <th>Nombre parametro</th>
             <th>Tipo</th>
             <th>Memoria</th>
             <th>Posiciones</th>
             <th>Lectura</th>
             <th>Color</th>
             <th>Orden</th>
             <th>Prefijo</th>
             <th>Decimales</th>
             <th>Sectores</th>
             <th>Nivel</th>
             <th>Borrar</th>
           </tr>
        </thead>
        <tbody>
           <?php
           //$result = mysql_query("SELECT idparametro,idserver,parametro,tipo,posiciones,lectura,pmemoria,estado,prefijonum,posdecimal,falta,comentario,estlink,nivel,color from parametros_server where idserver=".$_SESSION['idserver']." order by tipo,pmemoria,parametro,estado");
           $result = mysql_query("SELECT idparametro,idserver,parametro,TIPO,posiciones,lectura,pmemoria,estado,prefijonum,posdecimal,falta,comentario,estlink,nivel,color from parametros_server order by CAST(pmemoria as decimal),parametro,estado");
           while( $row = mysql_fetch_assoc( $result ) ){
           ?>
           <input type="hidden" name="idparametro[]" value="<?php echo $row['idparametro'];?>">
           <tr>
              <td><?php $ClassParam->cargacomboserver("idserver[]",$row['idserver']);?></td> 
              <td><input type="text" name="parametro[]" style="width: 140px;"  value="<?php echo $row['parametro'];?>" required="required"/> </td>
              <td>
                <select name = "TIPO[]"style="width: 120px;">
                    <option value="I" <?php if($row['TIPO'] == 'I') {echo " SELECTED ";} echo">"; ?>IMPUT</option>
                    <option value="IB" <?php if($row['TIPO'] == 'IB') {echo " SELECTED ";} echo">"; ?>IMPUT BIN</option>
                    <option value="C" <?php if($row['TIPO'] == 'C') {echo " SELECTED ";} echo">"; ?>BOBINA</option>
                    <option value="HB" <?php if($row['TIPO'] == 'HB') {echo " SELECTED ";} echo">"; ?>HOLDING BIN</option>
                    <option value="H" <?php if($row['TIPO'] == 'H') {echo " SELECTED ";} echo">"; ?>HOLDING</option>
                    <option value="HF" <?php if($row['TIPO'] == 'HF') {echo " SELECTED ";} echo">"; ?>HOLDING FLOAT</option>
                </select>
              </td>
              <td><input type="text" name="pmemoria[]" value="<?php echo $row['pmemoria'];?>" required="required" /> </td>
              <td><input type="number" name="posiciones[]" min="1" max="8" value="<?php echo $row['posiciones'];?>" required="required" /> </td>
              <td>
                <select name = "lectura[]" style="width: 80px;">
                    <option value="M" <?php if($row['lectura'] == 'M') {echo " SELECTED ";} echo">"; ?>MINUTO</option>
                    <option value="H" <?php if($row['lectura'] == 'H') {echo " SELECTED ";} echo">"; ?>HORA</option>
                    <option value="D" <?php if($row['lectura'] == 'D') {echo " SELECTED ";} echo">"; ?>DÍA</option>
                </select>
              </td>
              <!--Datapiker color-->
              <td><input type="color" name="color[]" value="<?php echo $row['color'];?>"/></td>
              <td><input type="number" name="estado[]" min="0" max="99" value="<?php echo $row['estado'];?>" required="required" /></td>
              <td><input type="text" name="prefijonum[]" value="<?php echo $row['prefijonum'];?>"/> </td>
              <td><input type="number" name="posdecimal[]" min="0" max="6" value="<?php echo $row['posdecimal'];?>" required="required" /> </td>
              <td><input type="number" name="estlink[]" min="0" max="9" value="<?php echo $row['estlink'];?>" required="required" /> </td>
              <td><input type="number" name="nivel[]" min="0" max="4" value="<?php echo $row['nivel'];?>" required="required" /> </td>
              <form name="fdeleteparam" method="post">
              <input type="hidden" name="idparamdelete" value="<?php echo $row['idparametro'];?>">
              <td><input type="submit" name="delete_p" value="Borrar"></td>
              </form>
           </tr>
           <?php
           }
           ?>
        </tbody>
        </table>
        </div>
        </form>
    </body>
</html>
