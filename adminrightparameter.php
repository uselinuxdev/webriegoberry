<!DOCTYPE html>
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
        <!Funciones post>
        <?php
        // Crear clase de para llamada a funciones genericas
        require("ParameterClass.php");
        // Control post
        // Update logic  
        if(isset($_POST['update_parametros']))
        {
            $ClassParam = new ParameterClass();
            $ClassParam->updateparameter(); 
        }
        if(isset($_POST['update_bitname']))
        {
            $ClassParam = new ParameterClass();
            $ClassParam->updatebit(); 
        }
        // Delete logic
        if(isset($_POST['delete_parametros']))
        {
            $ClassParam = new ParameterClass();
            $ClassParam->deleteparameter(); 
        }
        if(isset($_POST['delete_bit']))
        {
            $ClassParam = new ParameterClass();
            $ClassParam->deletebit(); 
        }
        // Insert si esta un parametro seleccionado en combo
        if(isset($_POST['insert_parametros']))
        {
            $ClassParam = new ParameterClass();
            $ClassParam->insertparameter(); 
        }
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
           $result = mysql_query("SELECT idparametro,idserver,parametro,tipo,posiciones,lectura,pmemoria,estado,prefijonum,posdecimal,falta,comentario,estlink,nivel,color from parametros_server where idserver=".$_SESSION['idserver']." order by parametro,estado");
           while( $row = mysql_fetch_assoc( $result ) ){
           ?>
           <input type="hidden" name="idparametro[]" value="<?php echo $row['idparametro'];?>">
           <input type="hidden" name="idserver[]" value="<?php echo $row['idserver'];?>">
           <tr>
              <td><input type="text" name="parametro[]" style="width: 140px;"  value="<?php echo $row['parametro'];?>" required="required"/> </td>
              <td>
                <select name = "tipo[]"style="width: 120px;">
                    <option value="I" <?php if($row['tipo'] == 'I') {echo " SELECTED ";} echo">"; ?>IMPUT</option>
                    <option value="IB" <?php if($row['tipo'] == 'IB') {echo " SELECTED ";} echo">"; ?>IMPUT BIN</option>
                    <option value="C" <?php if($row['tipo'] == 'C') {echo " SELECTED ";} echo">"; ?>BOBINA</option>
                    <option value="HB" <?php if($row['tipo'] == 'HB') {echo " SELECTED ";} echo">"; ?>HOLDING BIN</option>
                    <option value="H" <?php if($row['tipo'] == 'H') {echo " SELECTED ";} echo">"; ?>HOLDING</option>
                    <option value="HF" <?php if($row['tipo'] == 'HF') {echo " SELECTED ";} echo">"; ?>HOLDING FLOAT</option>
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
              <td><input type="color" name="color[]" value="<?php echo $row['color'];?>"/> </td>
              <td><input type="number" name="estado[]" min="0" max="99" value="<?php echo $row['estado'];?>" required="required" /> </td>
              <td><input type="text" name="prefijonum[]" value="<?php echo $row['prefijonum'];?>"/> </td>
              <td><input type="number" name="posdecimal[]" min="0" max="6" value="<?php echo $row['posdecimal'];?>" required="required" /> </td>
              <td><input type="number" name="estlink[]" min="0" max="9" value="<?php echo $row['estlink'];?>" required="required" /> </td>
              <td><input type="number" name="nivel[]" min="0" max="4" value="<?php echo $row['nivel'];?>" required="required" /> </td>
              <form name="fdeleteparam" method="post">
              <input type="hidden" name="idparamdelete" value="<?php echo $row['idparametro'];?>"/>
              <td><input type="submit" name="delete_parametros" value="Borrar"/></td>
              </form>
           </tr>
           <?php
           }
           ?>
        </tbody>
        </table>
        </div>
        <input type="submit" name="update_parametros" value="Actualizar" />
        <input type="submit" name="insert_parametros" value="Insertar" />
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
              <input type="hidden" name="idbitdelete" value="<?php echo $row['idbit'];?>"/>
              <td><input type="submit" name="delete_bit" value="Borrar"/></td>
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
