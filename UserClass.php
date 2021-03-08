<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserClass
 *
 * @author Administrador
 */
class UserClass {
    // Actualizar Usuarios
    public function updateuser()
    {
        // Control post
        $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
        if ($mysqli->connect_errno)
        {
            echo $mysqli->host_info."\n";
            return -1;
        }
        // Importante juego de caracteres
        if (!mysqli_set_charset($mysqli, "utf8")) {
            printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($mysqli));
            exit();
        }
        for($i=0;$i<count($_POST['usuario']);$i++)
        {
            // Control password si hay valor actalizar
            $spreparate = "";
            $spassmd5 = "";
            $sbind ="ssiisii";
            if (strlen($_POST['password'][$i])>0)
            {
               //echo 'Cambio password.';
               $spassmd5 = md5($_POST['password'][$i]); 
               $spreparate ="password = ?,";
               $sbind ="sssiisii";
            }
            // Controlar password si tiene datos se pinta
            // Preparar sentencia
            $stmt = $mysqli->prepare("UPDATE usuarios SET usuario = ?,".$spreparate." 
                email = ?, 
                telephone = ?,
                nivel = ?,  
                descripcion = ?,
                idserver = ?
                WHERE idusuario = ?");
            if(strlen($spassmd5)> 0)
            {
                $stmt->bind_param($sbind,
                $_POST['usuario'][$i],
                $spassmd5,
                $_POST['email'][$i],
                $_POST['telephone'][$i],
                $_POST['nivel'][$i],
                $_POST['descripcion'][$i],
                $_POST['idserver'][$i],
                $_POST['idusuario'][$i]);
            }else
            {
                $stmt->bind_param($sbind,
                $_POST['usuario'][$i],
                $_POST['email'][$i],
                $_POST['telephone'][$i],
                $_POST['nivel'][$i],
                $_POST['descripcion'][$i],
                $_POST['idserver'][$i],
                $_POST['idusuario'][$i]);
            }

            //echo "stmt bind_param correcto.";
            // Ejecutar
            $stmt->execute();
            // Finalizar
            $stmt->close();
            // Usuario telegram
            if ($this->admintelegram($_POST['usuario'][$i],$_POST['telephone'][$i],$mysqli) < 0)
            {
                return 0;
            }
        }
    }
    public function deleteuser()
    {
        $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
        if ($mysqli->connect_errno)
        {
            echo $mysqli->host_info."\n";
            return -1;
        }
        // Importante juego de caracteres
        //printf("Conjunto de caracteres inicial: %s\n", mysqli_character_set_name($mysqli));
        if (!mysqli_set_charset($mysqli, "utf8")) {
            printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($mysqli));
            exit();
        }
        $stmt = $mysqli->prepare("DELETE FROM usuarios WHERE idusuario = ?");
        // Vincular variables
        if (!$stmt->bind_param("i", $_POST['idusuariodelete'])) {
            echo "Falló la vinculación de parámetros: (" . $stmt->errno . ") " . $stmt->error;
            return -1;
        }
        // Ejecutar
        if (!$stmt->execute()) {
            echo "Falló la ejecución del delete: (" . $stmt->errno . ") " . $stmt->error;
            return -1;
        }else{
            //echo "Se borró correctamente el ID:".$_POST['idbitdelete'];
        }
        $stmt->close();
        return 0;
    }
    public function insertuser()
    {
        $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
        {
            echo $mysqli->host_info."\n";
            return -1;
        }
        // Importante juego de caracteres
        //printf("Conjunto de caracteres inicial: %s\n", mysqli_character_set_name($mysqli));
        if (!mysqli_set_charset($mysqli, "utf8")) {
            printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($mysqli));
            exit();
        }
        
        $sinsert = "INSERT INTO usuarios(usuario,password,nivel,idserver) "
                . "VALUES ('NUEVO USUARIO','CAMBIAR',0,".$_SESSION['idserver'].")";
       
        if ($mysqli->query($sinsert) === TRUE)
        {
            //echo "Nuevo bitname creado.";
        } else {
            echo "Falló la inserción: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $mysqli->close();
        return 0;
    }
     
    public function cargacomboserver($name,$idparam)
        {
            $cndb=mysqli_connect($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
            if (!$cndb) {
                echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
                echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
                echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
                exit;
            }
            mysqli_set_charset($cndb, "utf8");
        
            $sql = "SELECT idserver,nombreserver from server_instalacion where estado > 0 ";
            
            // Pintar combo
            echo '<select name="'.$name.'" style="width: 115px;">'; 
            // No definido
            $resparametros = mysqli_query($cndb,$sql);
            // Parametros de la select
            while($row = mysqli_fetch_array($resparametros,MYSQLI_ASSOC)) { //Iniciamos un ciclo para recorrer la variable $resparametros que tiene la consulta previamente hecha 
                $id = $row["idserver"] ; //Asignamos el id del campo que quieras mostrar
                $vparametro = substr($row["nombreserver"],0,50); // Asignamos el nombre del campo que quieras mostrar
                //echo "<option value=".$id.">".$vparametro."</option>"; //Llenamos el option con su value que sera lo que se lleve al archivo registrar.php y que sera el id de tu campo y luego concatenamos tbn el nombre que se mostrara en el combo 
                $vcombo = "<option value=".$id;
                if($idparam==$id) {$vcombo = $vcombo. " SELECTED ";}
                $vcombo = $vcombo.">";
                $vcombo = $vcombo.$vparametro."</option>"; 
                echo $vcombo;
            } //Cerramos el ciclo 
            echo '</select>';
        }
    // Crear usuario telegram. Telephone es el chatID
    private function admintelegram($username,$telephone,$mysqli)
    {
        // Si el usuario tiene telefono añadir a la lista telegram
        if(!isset($telephone))
        {
            return 0;
        }
        // Cargar datos de instalacion
        $sql = "SELECT nombre,tokenbot from instalacion ";
        $result = $mysqli->query($sql) or exit("Codigo de error ({$mysqli->errno}): {$mysqli->error}");
        $row = mysqli_fetch_assoc($result);
        // Set your Bot ID and Chat ID.
        $telegramchatid=$telephone;
        if($telegramchatid>0) $telegramchatid=$telegramchatid*(-1);
        $telegrambot=$row["tokenbot"];
        $installname=$row["nombre"];
        // Function call with your own text or variable
        $stext="Telegram activado en Raspberry $installname !!";
        $this->telegram($telegrambot,$telegramchatid,$stext);
    }
    
    //// Temporal funtions TELEGRAM
    
    // Telegram function which you can call
    private function telegram($telegrambot,$telegramchatid,$msg) {
        $url='https://api.telegram.org/bot'.$telegrambot.'/sendMessage';$data=array('chat_id'=>$telegramchatid,'text'=>$msg);
        //echo $url;
        //return 0;
        $options=array('http'=>array('method'=>'POST','header'=>"Content-Type:application/x-www-form-urlencoded\r\n",'content'=>http_build_query($data),),);
        $context=stream_context_create($options);
        $result=file_get_contents($url,false,$context);
        return $result;
    }

//End of class
}
