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
        $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname']);
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
        for($i=0;$i <count($_POST['usuario']);$i++)
        {
            // Control password si hay valor actalizar
            $spreparate = "";
            $spassmd5 = "";
            $sbind ="ssisi";
            if (strlen($_POST['password'][$i])>0)
            {
               //echo 'Cambio password.';
               $spassmd5 = md5($_POST['password'][$i]); 
               $spreparate ="password = ?,";
               $sbind ="sssisi";
            }
            // Controlar password si tiene datos se pinta
            // Preparar sentencia
            $stmt = $mysqli->prepare("UPDATE usuarios SET usuario = ?,".$spreparate." 
                email = ?, 
                nivel = ?,  
                descripcion = ?
                WHERE idusuario = ?");
            if(strlen($spassmd5)> 0)
            {
                $stmt->bind_param($sbind,
                $_POST['usuario'][$i],
                $spassmd5,
                $_POST['email'][$i],
                $_POST['nivel'][$i],
                $_POST['descripcion'][$i],
                $_POST['idusuario'][$i]);
            }else
            {
                $stmt->bind_param($sbind,
                $_POST['usuario'][$i],
                $_POST['email'][$i],
                $_POST['nivel'][$i],
                $_POST['descripcion'][$i],
                $_POST['idusuario'][$i]);
            }

            //echo "stmt bind_param correcto.";
            // Ejecutar
            $stmt->execute();
            // Finalizar
            $stmt->close();
        }
    }
    public function deleteuser()
    {
        $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname']);
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
        $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname']);
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

//End of class
}
