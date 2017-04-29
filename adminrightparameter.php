<!DOCTYPE html>
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
        // Update logic
        if(isset($_POST['update_bitname']))
        {
            $ClassParam = new ParameterClass();
            $ClassParam->updatebit(); 
        }
        // Delete logic
        if(isset($_POST['deletebit']))
        {
            $ClassParam = new ParameterClass();
            $ClassParam->deletebit(); 
        }
        // Insert si esta un parametro seleccionado en combo
        if(isset($_POST['insert_bitname']))
        {
            if(!empty($_POST['cbvalorbit']))
            {
                $ClassParam = new ParameterClass();
                $ClassParam->insertbit(); 
            }else{
                echo "Debe seleccionar algún parámetro del desplegable.";
            }
        }
        // Función combo
        function cargacombo()
        {
            mysql_connect($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass']) or die ("No se puede establecer la conexion!!!!"); 
            mysql_select_db($_SESSION['dbname']) or die ("Imposible conectar a la base de datos!!!!"); //Selecionas tu base
            mysql_set_charset('utf8'); // Importante juego de caracteres a utilizar.
            
            $sql = "SELECT parametros_server.idparametro, parametros_server.parametro FROM parametros_server,server_instalacion";
            $sql.=" where server_instalacion.idserver = parametros_server.idserver " ;
            $sql.=" and server_instalacion.estado = 1 " ;
            $sql.=" and parametros_server.tipo like'%B%' and parametros_server.lectura ='M' and parametros_server.estado > 0" ;
            $sql.=" and parametros_server.nivel <= ".$_SESSION['nivel'];
            $sql.=" and parametros_server.idserver = ".$_SESSION['idserver'];
            $sql.=" order by parametros_server.estado,parametros_server.parametro ";
            // Pintar combo
            echo '<select name="cbvalorbit">'; 
            $resparametros = mysql_query($sql);
            echo "<option value=0>  Seleccionar un Parámetro  </option>"; 
            while($row = mysql_fetch_array($resparametros)) { //Iniciamos un ciclo para recorrer la variable $resparametros que tiene la consulta previamente hecha 
                $id = $row["idparametro"] ; //Asignamos el id del campo que quieras mostrar
                $vparametro = substr($row["parametro"],0,50); // Asignamos el nombre del campo que quieras mostrar
                //echo "<option value=".$id.">".$vparametro."</option>"; //Llenamos el option con su value que sera lo que se lleve al archivo registrar.php y que sera el id de tu campo y luego concatenamos tbn el nombre que se mostrara en el combo 
                $vcombo = "<option value=".$id;
                if($_POST['cbvalorbit']==$id) {
                    $vcombo = $vcombo. " SELECTED ";
                }
            $vcombo = $vcombo.">";
            $vcombo = $vcombo.$vparametro."</option>"; 
            echo $vcombo;
            } //Cerramos el ciclo 
            echo '</select>';
            echo ' <input type="submit" name="cargabit" value="Cargar"/>';
        }
        ?>
    </head>
    <body>
        <h4 style="color:#3A72A5;">Administración parametros server</h4>
        <form name="fparameter" method="post">
        <div id="parameter" style="overflow-x:auto;" >
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
       </thead>
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
        <form name="fbitname" method="post">
        <!--Cargar combo-->
        <?php cargacombo();?>
        <div id="bitname" style="overflow-x:auto;" >
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
           $result = mysql_query("SELECT idbit,idparametro,posicion,nombrebit from parametros_bitname where idparametro=".$_POST['cbvalorbit']." order by idparametro,posicion");     
           while( $row = mysql_fetch_assoc( $result ) ){
               $iddelete = $row['idbit'];
           ?>
           <input type="hidden" name="idbit[]" value="<?php echo $row['idbit'];?>">
           <input type="hidden" name="idparametro[]" value="<?php echo $row['idparametro'];?>">
           <tr>
              <td><input type="number" name="posicion[]" min="0" max="31" value="<?php echo $row['posicion'];?>" required="required" /> </td>
              <td><input type="text" name="nombrebit[]" size="80" value="<?php echo $row['nombrebit'];?>" required="required" /> </td>
              <form name="fdeletebit" method="post">
              <input type="hidden" name="idbitdelete" value="<?php echo $row['idbit'];?>">
              <td><input type="submit" name="deletebit" value="Borrar"/></td>
              </form>
           </tr>
           <?php
           }
           ?>
        </tbody>
        </table>
        </div>
        <input type="submit" name="update_bitname" value="Actualizar" />
        <input type="submit" name="insert_bitname" value="Insertar" />
        </form>
    </body>
</html>
