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
        require("riegoresumenClass.php");
        // Control post
        if(isset($_POST['update_resumen']))
        {
            $Classresumen = new riegoresumenClass();
            $Classresumen->updateresumen(); 
        }
        if(isset($_POST['insert_resumen']))
        {
            $Classresumen = new riegoresumenClass();
            $Classresumen->insertresumen(); 
        }
        // Control de estimación
        if(isset($_POST['update_estimacion']))
        {
            $Classresumen = new riegoresumenClass();
            $Classresumen->updateestimacion();
        }
        if(isset($_POST['delete_estimacion']))
        {
            $Classresumen = new riegoresumenClass();
            $Classresumen->deleteestimacion();
        }
        if(isset($_POST['insert_estimacion']))
        {
            if(!empty($_POST['comboestimado']))
            {
                $Classresumen = new riegoresumenClass();
                $Classresumen->insertestimacion($_POST['comboestimado']);
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
           $result = mysql_query("SELECT idresumen,divid,tipolectura,idparametroa,idparametrob,idparametroc,falta from admresumen order by idresumen");
           while( $row = mysql_fetch_assoc( $result ) ){
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
             <th>Borrar</th>
           </tr>
        </thead>
        <tbody>
           <?php
           $sql = "SELECT idestimacion,p.idparametro,p.parametro,valorx,valory "
                   . "from admestimacion a,parametros_server p"
                   . " where p.idparametro = a.idparametro"
                   . " and p.idparametro=".$_POST['comboestimado']." order by idparametro,valorx";
           $result = mysql_query($sql);
           while( $row = mysql_fetch_assoc( $result ) ){
           ?>
           <input type="hidden" name="idestimacion[]" value="<?php echo $row['idestimacion'];?>" />
           <input type="hidden" name="idparametro[]" value="<?php echo $row['idparametro'];?>" />
           <tr>
              <td><?php echo $row['parametro'];?></td>
              <td><input type="number" name="valorx[]" value="<?php echo $row['valorx'];?>" required="required"/> </td>
              <td><input type="number" name="valory[]" value="<?php echo $row['valory'];?>" required="required"/> </td>
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
        </form>
    </body>
</html>
