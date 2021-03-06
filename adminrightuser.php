<!DOCTYPE html>
<?php
//Primero hacemos las conexiones
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
        div#duser {
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
        $ClassUser = new UserClass();
        if(isset($_POST['update_user']))
        {
            $ClassUser->updateuser();
        }
        if(isset($_POST['insert_user']))
        {
            $ClassUser->insertuser();
        }
        if(isset($_POST['delete_user']))
        {
            $ClassUser->deleteuser();
        }
        ?>
    </head>
    <body>
        <h4 style="color:#3A72A5;">Administración usuarios</h4>
        <form name="user" method="post">
        <div id="duser" style="overflow-x:auto;" >
        <table id="tuser" >
        <thead>
           <tr>
             <th>Server</th>
             <th>Usuario</th>
             <th>Password</th>
             <th>Email</th>
             <th>Telegram_CHAT</th>
             <th>Clase</th>
             <th>Comentario</th>
             <th>Alta</th>
             <th>Borrar</th>
           </tr>
        </thead>
        <tbody>
           <?php
           $result = mysqli_query($cndb,"SELECT idusuario,usuario,password,email,telephone,nivel,descripcion,falta,idserver from usuarios order by usuario");  
           while( $row = mysqli_fetch_array($result,MYSQLI_ASSOC) ){
           ?>
           <input type="hidden" name="idusuario[]" value="<?php echo $row['idusuario'];?>">
           <tr>
              <td><?php $ClassUser->cargacomboserver("idserver[]",$row['idserver']);?></td> 
              <td><input type="text" name="usuario[]" style="width: 100px;" value="<?php echo $row['usuario'];?>" required="required" /> </td>
              <!--La password se actualiza si se escribe-->
              <td><input type="password" name="password[]" style="width: 100px;" value=""/> </td>
              <td><input type="email" name="email[]" style="width: 210px;" value="<?php echo $row['email'];?>"/> </td>
              <td><input type="number" name="telephone[]" style="width: 120px;" value="<?php echo $row['telephone'];?>"/> </td>
              <td>
                <select name = "nivel[]" style="width: 7em;">
                    <option value="1" <?php if($row['nivel'] == 1) {echo " SELECTED ";} echo">"; ?>Admin</option>
                    <option value="0" <?php if($row['nivel'] == 0) {echo " SELECTED ";} echo">"; ?>Estandar</option>
                </select>
              </td>
              <td><input type="text" name="descripcion[]" style="width: 230px;" value="<?php echo $row['descripcion'];?>"/> </td>
              <td><?php echo date("d/m/Y", strtotime($row['falta']));?></td>
              <form name="fdeleteuser" method="post">
              <input type="hidden" name="idusuariodelete" value="<?php echo $row['idusuario'];?>" />
              <td><input type="submit" name="delete_user" value="Borrar"/></td>
              </form>
           </tr>
           <?php
           }
           ?>
        </tbody>
        </table>
        </div>
        <input type="submit" name="update_user" value="Actualizar" />
        <input type="submit" name="insert_user" value="Insertar" />
        </form>
    </body>
</html>
