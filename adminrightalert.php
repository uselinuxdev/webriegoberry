<!DOCTYPE html>
<?php
$cndb=mysqli_connect($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
if (!$cndb) {
    echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
    echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
    echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
mysqli_set_charset($cndb, "utf8");
?>
<html>
    <head>
        <meta charset="UTF-8">
        <style>
        div#dalert {
            background-color: white;
            overflow: hidden;
            overflow-y: scroll;
            height: 380px;
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
        //print_r($_POST);
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
            // Check si es de tipo 4
            $ClassAlert->checkalertAll();
        }
        if(isset($_POST['check_email']))
        {
            $ClassAlert->checkMail();
        }
        ?>
        
        <script>
        $("estado[]").change(function () {
        var res = this.value;
        alert(res);
        //OR
        alert(this.value);
        });
        </script>
        </head>
    <body>
        <h4 style="color:#3A72A5;">Administración alertas</h4>
        <form name="alert" method="post">
        <div id="dalert" style="overflow-x:auto;" >
        <table id="talert" >
        <thead>
           <tr>
             <th>Parametro</th>
             <th>Texto alerta</th>
             <th>Activada</th>
             <th>Último Estado</th>
             <th>Hora min.</th>
             <th>Hora max.</th>
             <th>Usuario</th>
             <th>Tipo</th>
             <th>Operación</th>
             <th>Valor</th>
             <th>Nº Bit</th>
             <th>Alta</th>
             <th>Borrar</th>
           </tr>
        </thead>
        <tbody>
           <?php
           $result = mysqli_query($cndb,"SELECT idalert,idparametro,idusuario,estado,tipo,operacion,valor,textalert,nbit,horaminbit,horamaxbit,falta,iflag from alertserver where idserver=".$_SESSION['idserver']." order by idusuario,idparametro");  
            
           while( $row = mysqli_fetch_array($result,MYSQLI_ASSOC) ){
           //print_r($row);
           ?>
           <input type="hidden" name="idalert[]" value="<?php echo $row['idalert'];?>">
           <tr>
              <td>
                  <?php
                    $ClassAlert->cargacomboparam("idparametro[]",$row['idparametro']);
                  ?>
              </td>
              <td><input type="text" name="textalert[]" style="width: 250px;" value="<?php echo $row['textalert'];?>" required="required" /> </td>
              <td>
                <select name = "estado[]" style="width: 7em;">
                    <option value="1" <?php if($row['estado'] == 1) {echo " SELECTED ";} echo">"; ?>Activa</option>
                    <option value="0" <?php if($row['estado'] == 0) {echo " SELECTED ";} echo">"; ?>No activa</option>
                </select>
              </td>
              <td>
                <select name = "iflag[]" style="width: 9em;">
                    <option value="1" <?php if($row['iflag'] == 1) {echo " SELECTED ";} echo">"; ?>Alarma</option>
                    <option value="0" <?php if($row['iflag'] == 0) {echo " SELECTED ";} echo">"; ?>Correcta</option>
                </select>
              </td>
              <td><input type="time" name="horaminbit[]" min=00:00 max=23:58 step=60 value="<?php echo $row['horaminbit'];?>" /> </td>
              <td><input type="time" name="horamaxbit[]" min=00:00 max=23:59 step=60 value="<?php echo $row['horamaxbit'];?>" /> </td>
              <td>
                  <?php
                    $ClassAlert->cargacombouser("idusuario[]",$row['idusuario']);
                  ?>
              </td>
              <td>
                <select name = "tipo[]" style="width: 7em;">
                    <option value="0" <?php if($row['tipo'] == 0) {echo " SELECTED ";}; ?>>Ultima</option>
                    <option value="1" <?php if($row['tipo'] == 1) {echo " SELECTED ";}; ?>>Diaria</option>
                    <option value="2" <?php if($row['tipo'] == 2) {echo " SELECTED ";}; ?>>Mensual</option>
                    <option value="3" <?php if($row['tipo'] == 3) {echo " SELECTED ";}; ?>>Anual</option>
                    <option value="4" <?php if($row['tipo'] == 4) {echo " SELECTED ";}; ?>>Estimación mes</option>
                </select>
              </td>
              <td>
                <select name = "operacion[]" style="width: 7em;">
                    <option value="<" <?php if($row['operacion'] == '<') {echo " SELECTED ";} echo">"; ?>Menor</option>
                    <option value=">" <?php if($row['operacion'] == '>') {echo " SELECTED ";} echo">"; ?>Mayor</option>
                </select>
              </td>
              <td><input type="number" name="valor[]" style="width: 70px;" value="<?php echo $row['valor'];?>" required="required" /> </td>            
              <td>
                  <?php
                    $ClassAlert->cargarcombobit("nbit[]",$row['idparametro'],$row['nbit']);
                  ?>
              </td>
              
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
        <!-- <input type="submit" name="check_email" value="Check Email"> -->
        </form>
        </textarea>
    </body>
</html>
