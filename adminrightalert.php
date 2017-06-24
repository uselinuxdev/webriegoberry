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
        div#dalert {
            background-color: white;
            overflow: hidden;
            overflow-y: scroll;
            height: 350px;
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
        // Control post
        $ClassAlert = new AlertClass();
        if(isset($_POST['update_alert']))
        {
            $ClassAlert->updatealert();
        }
        if(isset($_POST['insert_alert']))
        {
            $ClassAlert->insertalert();
        }
        if(isset($_POST['delete_alert']))
        {
            $ClassAlert->deletealert();
        }
        if(isset($_POST['check_alert']))
        {
            $ClassAlert->checkalert();
        }
        ?>
    </head>
    <body>
        <h4 style="color:#3A72A5;">Administración alertas</h4>
        <form name="alert" method="post">
        <div id="dalert" style="overflow-x:auto;" >
        <table id="talert" >
        <thead>
           <tr>
             <th>Parametro</th>
             <th>Usuario</th>
             <th>Estado</th>
             <th>Tipo</th>
             <th>Operación</th>
             <th>Valor</th>
             <th>Texto alerta</th>
             <th>Nº Bit</th>
             <th>Hora min. bit</th>
             <th>Hora max. bit</th>
             <th>Alta</th>
             <th>Borrar</th>
           </tr>
        </thead>
        <tbody>
           <?php
           $result = mysql_query("SELECT idalert,idparametro,idusuario,estado,tipo,operacion,valor,textalert,nbit,horaminbit,horamaxbit,falta from alertserver where idserver=".$_SESSION['idserver']." order by idusuario,idparametro");
           while( $row = mysql_fetch_assoc( $result ) ){
           ?>
           <input type="hidden" name="idalert[]" value="<?php echo $row['idalert'];?>">
           <tr>
              <td>
                  <?php
                    $ClassAlert->cargacomboparam("idparametro[]",$row['idparametro']);
                  ?>
              </td>
              <td>
                  <?php
                    $ClassAlert->cargacombouser("idusuario[]",$row['idusuario']);
                  ?>
              </td>
              <td>
                <select name = "estado[]" style="width: 7em;">
                    <option value="1" <?php if($row['estado'] == 1) {echo " SELECTED ";} echo">"; ?>Activa</option>
                    <option value="0" <?php if($row['estado'] == 0) {echo " SELECTED ";} echo">"; ?>No activa</option>
                </select>
              </td>
              <td>
                <select name = "tipo[]" style="width: 7em;">
                    <option value="0" <?php if($row['tipo'] == 0) {echo " SELECTED ";} echo">"; ?>Ultima</option>
                    <option value="1" <?php if($row['tipo'] == 1) {echo " SELECTED ";} echo">"; ?>Diaria</option>
                </select>
              </td>
              <td>
                <select name = "operacion[]" style="width: 7em;">
                    <option value="<" <?php if($row['operacion'] == '<') {echo " SELECTED ";} echo">"; ?>Menor</option>
                    <option value=">" <?php if($row['operacion'] == '>') {echo " SELECTED ";} echo">"; ?>Mayor</option>
                </select>
              </td>
              <td><input type="number" name="valor[]" style="width: 70px;" value="<?php echo $row['valor'];?>" required="required" /> </td>
              <td><input type="text" name="textalert[]" style="width: 250px;" value="<?php echo $row['textalert'];?>" required="required" /> </td>
              <td><input type="number" name="nbit[]" min="0" max="31" style="width: 40px;" value="<?php echo $row['nbit'];?>" /> </td>
              <td><input type="time" name="horaminbit[]" min=00:00 max=23:45 step=900 value="<?php echo $row['horaminbit'];?>" /> </td>
              <td><input type="time" name="horamaxbit[]" min=00:00 max=23:45 step=900 value="<?php echo $row['horamaxbit'];?>" /> </td>
              <td><?php echo date("d/m/Y", strtotime($row['falta']));?></td>
              <form name="fdeletealert" method="post">
              <input type="hidden" name="idalertdelete" value="<?php echo $row['idalert'];?>" />
              <td><input type="submit" name="delete_alert" value="Borrar"></td>
              </form>
           </tr>
           <?php
           }
           ?>
        </tbody>
        </table>
        </div>
        <input type="submit" name="update_alert" value="Actualizar">
        <input type="submit" name="insert_alert" value="Insertar">
        <input type="submit" name="check_alert" value="Comprobar">
        </form>
        </textarea>
    </body>
</html>
