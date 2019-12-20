<!DOCTYPE html>
<?php
//Primero hacemos las conexiones
$cndb=mysqli_connect($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname']);
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
        input[type=text] {
            width: 100%;
            margin: 0px 0;
            border: none;
        }
        div#resumen {
            background-color: white;
            overflow: hidden;
            overflow-y: scroll;
            height: 270px;
        }
        div#estimacion {
            background-color: white;
            overflow: hidden;
            overflow-y: scroll;
            height: 200px;
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
        $Classresumen = new riegoresumenClass();
        $ClassAlertres = new AlertClass();
        // Control post
        if(isset($_POST['update_resumen']))
        {
            $Classresumen->updateresumen(); 
        }
        if(isset($_POST['insert_resumen']))
        {
            $Classresumen->insertresumen(); 
        }
        // Control de estimación
        if(isset($_POST['update_estimacion']))
        {
            $Classresumen->updateestimacion();
        }
        if(isset($_POST['delete_estimacion']))
        {
            $Classresumen->deleteestimacion();
        }
        if(isset($_POST['insert_estimacion']))
        {
            if(!empty($_POST['comboestimado']))
            {
                // Sólo ese parametro
                $Classresumen->insertestimacion($_POST['comboestimado']);
            }else{
//                echo "Debe seleccionar algún parámetro del desplegable de estimaciones.";
                // Todos los parametros
                $Classresumen->insertestimacion();
            }
        }
        // Lanzar el correo del parametro del combo
        if(isset($_POST['check_estimacion']))
        {
            if(!empty($_POST['comboestimado']))
                {
                    $ClassAlertres->checkstimate($_POST['comboestimado']);
                }else{
                    echo "Debe seleccionar algún parámetro del desplegable de estimaciones.";
                }
        }
        ?>
    </head>
    <body>
        <div id="resumen" style="overflow-x:auto;" >
        <h4 style="color:#3A72A5;">Configuración resumen</h4>
        <form name="resumen" method="post">
        <table id="tresumen" >
        <thead>
           <tr>
             <th>Sección</th>
             <th>Lectura</th>
             <th>Parámetro A</th>
             <th>Parámetro B</th>
             <th>Parámetro C</th>
             <th>F. Alta</th>
           </tr>
        </thead>
        <tbody>
           <?php
           // Declarar clase
           $ClassResumen = new riegoresumenClass();
           $icont = 0;
           $result = mysqli_query($cndb,"SELECT idresumen,divid,tipolectura,idparametroa,idparametrob,idparametroc,falta from admresumen order by idresumen");  
           while( $row = mysqli_fetch_array($result,MYSQLI_ASSOC) ){
               $icont +=1;
           ?>
           <input type="hidden" name="idresumen[]" value="<?php echo $row['idresumen'];?>">
           <tr>
              <td>
                <select name = "divid[]"style="width: 90px;">
                    <option value="resumenprod" <?php if($row['divid'] == 'resumenprod') {echo " SELECTED ";} echo">"; ?>Producción</option>
                    <option value="resumenactual" <?php if($row['divid'] == 'resumenactual') {echo " SELECTED ";} echo">"; ?>Actual</option>
                    <option value="resumengrafica1" <?php if($row['divid'] == 'resumengrafica1') {echo " SELECTED ";} echo">"; ?>Hoy</option>
                    <option value="resumengrafica2" <?php if($row['divid'] == 'resumengrafica2') {echo " SELECTED ";} echo">"; ?>Mes</option>
                    <option value="resumengrafica3" <?php if($row['divid'] == 'resumengrafica3') {echo " SELECTED ";} echo">"; ?>Año</option>
                </select>
              </td>
              <td>
                <select name = "tipolectura[]"style="width: 60px;">
                    <option value="1" <?php if($row['tipolectura'] == '1') {echo " SELECTED ";} echo">"; ?>Día</option>
                    <option value="2" <?php if($row['tipolectura'] == '2') {echo " SELECTED ";} echo">"; ?>Mes</option>
                    <option value="3" <?php if($row['tipolectura'] == '3') {echo " SELECTED ";} echo">"; ?>Año</option>
                </select>
              </td>
              <td>
                  <?php
                    $ClassResumen->cargacomboparam("idparametroa[]",$row['idparametroa']);
                  ?>
              </td>
              <td>
                  <?php
                    $ClassResumen->cargacomboparam("idparametrob[]",$row['idparametrob']);
                  ?>
              </td>
              <td>
                  <?php
                    $ClassResumen->cargacomboparam("idparametroc[]",$row['idparametroc']);
                  ?>
              </td>
              <td><?php echo date("d/m/Y", strtotime($row['falta']));?></td>
           </tr>
           <?php
           }
           ?>
        </tbody>
        </table>
        </div>
        <input type="submit" name="update_resumen" value="Actualizar"/>
        <?php // Si $icont = 5 no dejar insertar más.
        echo '<input type="submit" name="insert_resumen" value="Insertar"';
        if ($icont == 5) {echo ' disabled';}
        echo '/>';
        ?>
        </form>
        <h4 style="color:#3A72A5;">Administración estimaciones</h4>
        <form name="festimacion" method="post">
        <?php 
        $ClassResumen->cargacomboparam("comboestimado",$_POST['comboestimado']);
        echo ' <input type="submit" name="cargaestimate" value="Cargar"/>';
        ?>
        <div id="estimacion" style="overflow-x:auto;">
        <table id="testimacion">
        <thead>
           <tr>
             <th>Parametro</th>
             <th>Valorx</th>
             <th>Valory</th>
             <th>Usuario</th>
             <th>Operacion</th>
             <th>Porcentaje</th>
             <th>Borrar</th>
           </tr>
        </thead>
        <tbody>
           <?php
           $sql = "SELECT idestimacion,p.idparametro,p.parametro,valorx,valory,idusuario,operacion,poralert "
                   . "from admestimacion a,parametros_server p"
                   . " where p.idparametro = a.idparametro"
                   . " and p.idparametro=".$_POST['comboestimado']." order by idparametro,valorx";
           $result = mysqli_query($cndb,$sql); 
           while( mysqli_fetch_array($result,MYSQLI_ASSOC)){
           ?>
           <input type="hidden" name="idestimacion[]" value="<?php echo $row['idestimacion'];?>" />
           <input type="hidden" name="idparametro[]" value="<?php echo $row['idparametro'];?>" />
           <tr>
              <td><?php echo $row['parametro'];?></td>
              <td><input type="number" style="width: 40px;" name="valorx[]" value="<?php echo $row['valorx'];?>" required="required"/> </td>
              <td><input type="number" style="width: 100px;" name="valory[]" value="<?php echo $row['valory'];?>" required="required"/> </td>
              <td>
                  <?php
                    $ClassAlertres->cargacombouser("idusuario[]",$row['idusuario']);
                  ?>
              </td>
              <td>
                <select name = "operacion[]" style="width: 60px;">
                    <option value=">" <?php if($row['operacion'] == '>') {echo " SELECTED ";} echo">";?>Mayor</option>
                    <option value="<" <?php if($row['operacion'] == '<') {echo " SELECTED ";} echo">";?>Menor</option>
                </select>
              </td>
              <td><input type="number" name="poralert[]" min="5" max="50" value="<?php echo $row['poralert'];?>"/> </td>
              <form name="fdelestimate" method="post">
              <input type="hidden" name="idestimaciondelete" value="<?php echo $row['idestimacion'];?>" />
              <td><input type="submit" name="delete_estimacion" value="Borrar"/></td>
              </form>
           </tr>
           <?php
           }
           ?>
        </tbody>
        </table>
        </div>
        <input type="submit" name="update_estimacion" value="Actualizar" />
        <input type="submit" name="insert_estimacion" value="Insertar" />
        <input type="submit" name="check_estimacion" value="Check Alerta" />
        </form>
    </body>
</html>
