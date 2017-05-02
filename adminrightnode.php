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
        div#tnode {
            background-color: white;
        }
        table {
            border-collapse: collapse;
            tab-size: 100%;
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
        require("ZigbeeClass.php");
        // Control post
        $ClassZigbee = new ZigbeeClass();
        if(isset($_POST['update_nodo']))
        {
            $ClassZigbee->updatenodes(); 
        }
        if(isset($_POST['insert_nodo']))
        {
            $ClassZigbee->insertnodes(); 
        }
        if(isset($_POST['delete_nodo']))
        {
            $ClassZigbee->deletenodes();
        }
        // Control de sectores
        if(isset($_POST['update_sector']))
        {
            $ClassZigbee->updatesector(); 
        }
        if(isset($_POST['delete_sector']))
        {
            $ClassZigbee->deletesector(); 
        }
        // Controlar combo seleccionado
        if(isset($_POST['insert_sector']))
        {
            if(!empty($_POST['cbnodos']))
            {
                $ClassZigbee->insertsector();
            }else{
                echo "Debe seleccionar algún nodo del desplegable.";
            }
        }
        ?>
    </head>
    <body>
        <h4 style="color:#3A72A5;">Administración nodos</h4>
        <form name="nodos" method="post">
        <div id="tnode" style="overflow-x:auto;" >
        <table id="tnodos" >
        <thead>
           <tr>
             <th>Nombre nodo</th>
             <th>Address</th>
             <th>Addr. Long</th>
             <th>Id nodo</th>
             <th>D.padre</th>
             <th>Tipo</th>
             <th>Estado</th>
             <th>Borrar</th>
           </tr>
        </thead>
        <tbody>
           <?php
           $result = mysql_query("SELECT idnodo,nombre_nodo,source_addr,source_addr_long,node_identifier,parent_address,device_type,estado,falta,fmodif,fbaja FROM nodos");
           while( $row = mysql_fetch_assoc( $result ) ){
           ?>
           <input type="hidden" name="idnodo[]" value="<?php echo $row['idnodo'];?>">
           <tr>
              <td><input type="text" name="nombre_nodo[]" size="35" value="<?php echo $row['nombre_nodo'];?>" required="required" /> </td>
              <td><input type="text" name="source_addr[]" value="<?php echo $row['source_addr'];?>" required="required" /> </td>
              <td><input type="text" name="source_addr_long[]" value="<?php echo $row['source_addr_long'];?>" required="required" /> </td>
              <td><input type="text" name="node_identifier[]" value="<?php echo $row['node_identifier'];?>" required="required" /> </td>
              <td><input type="text" name="parent_address[]" value="<?php echo $row['parent_address'];?>" required="required" /> </td>
              <td><input type="text" name="device_type[]" value="<?php echo $row['device_type'];?>" required="required" /> </td>
              <td>
                <select name = "estado[]" style="width: 7em;">
                    <option value="1" <?php if($row['estado'] == 1) {echo " SELECTED ";} echo">"; ?>Activado</option>
                    <option value="0" <?php if($row['estado'] == 0) {echo " SELECTED ";} echo">"; ?>Reescanear</option>
                </select>
              </td>
              <form name="fdelenodo" method="post">
              <input type="hidden" name="idnododelete" value="<?php echo $row['idnodo'];?>" />
              <td><input type="submit" name="delete_nodo" value="Borrar"/></td>
              </form>
           </tr>
           <?php
           }
           ?>
        </tbody>
        </table>
        </div>
        <input type="submit" name="update_nodo" value="Actualizar" />
        <input type="submit" name="insert_nodo" value="Insertar" />
        </form>
        <h4 style="color:#3A72A5;">Administración sectores</h4>
        <form name="nodos" method="post">
        <div id="dnodos" style="overflow-x:auto;" >
        <?php
            $ClassZigbee = new ZigbeeClass();
            $ClassZigbee->cargacombonodos();
            echo ' <input type="submit" name="carganodo" value="Cargar"/>';
        ?>
        <table id="tsectores" >
        <thead>
           <tr>
             <th>Nº Sector</th>
             <th>Nombre</th>
             <th>Salida</th>
             <th>Reintentos</th>
             <th>Tiempo latch</th>
             <th>Borrar</th>
           </tr>
        </thead>
        <tbody>
           <?php
           $result = mysql_query("SELECT idsector,num_sector,nombre_sector,idnodo,num_salida,time_latch,reintentos,falta,fmodif,fbaja FROM sectores where idnodo=".$_POST['cbnodos']." order by num_sector");
           while( $row = mysql_fetch_assoc( $result ) ){
           ?>
           <input type="hidden" name="idsector[]" value="<?php echo $row['idsector'];?>">
           <tr>
              <td><input type="number" name="num_sector[]" min="1" max="99" value="<?php echo $row['num_sector'];?>" required="required" /> </td>
              <td><input type="text" name="nombre_sector[]" size=35 value="<?php echo $row['nombre_sector'];?>" required="required" /> </td>
              <td><input type="number" name="num_salida[]" min="1" max="99" value="<?php echo $row['num_salida'];?>" required="required" /> </td>
              <td><input type="number" name="reintentos[]" min="-1" max="99" value="<?php echo $row['reintentos'];?>" required="required" /> </td>
              <td><input type="number" name="time_latch[]" value="<?php echo $row['time_latch'];?>" required="required" /> </td>
              <form name="fdelensector" method="post">
              <input type="hidden" name="idsectordelete" value="<?php echo $row['idsector'];?>" />
              <td><input type="submit" name="delete_sector" value="Borrar"/></td>
              </form>
           </tr>
           <?php
           }
           ?>
        </tbody>
        </table>
        </div>
        <input type="submit" name="update_sector" value="Actualizar" />
        <input type="submit" name="insert_sector" value="Insertar" />
        </form>
    </body>
</html>
