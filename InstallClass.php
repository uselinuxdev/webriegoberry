<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of InstallClass
 *
 * @author Administrador
 */
class InstallClass {
    // Private vars
    
    // Public function
    // Actualizar datos install
    public function updateinstall()
    {
        for($i =0;$i <count($_POST['idinstalacion']);$i++)
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
            //printf("Conjunto de caracteres actual: %s\n", mysqli_character_set_name($mysqli));
            // Preparar sentencia
            $stmt = $mysqli->prepare("UPDATE instalacion SET nombre = ?,
                titular = ?, 
                cif = ?, 
                falta = ?,
                ubicacion = ?,
                pico = ?,
                modulos = ?,
                inversor = ?
                WHERE idinstalacion = ?");
            //echo "stmt preparado correctamente.";
            $stmt->bind_param('ssssssssi',
                $_POST['nombre'][$i],
                $_POST['titular'][$i],
                $_POST['cif'][$i],
                $_POST['falta'][$i],
                $_POST['ubicacion'][$i],
                $_POST['pico'][$i],
                $_POST['modulos'][$i],
                $_POST['variador'][$i],
                $_POST['idinstalacion'][$i]);
            //echo "stmt bind_param correcto.";
            // Ejecutar
            $stmt->execute();
            // Finalizar
            $stmt->close();
        }
    }
    public function updateimagen()
    {
        $target_dir = "imagenes/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        //echo $target_file;
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
        // Check if image file is a actual image or fake image
        if(isset($_POST["submit"])) {
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if($check !== false) {
                echo "El fichero es del tipo imagen - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "El fichero no se ha detectado como imagen, selecione otro fichero.";
                $uploadOk = 0;
            }
        }
        // Check if file already exists
        if (file_exists($target_file)) {
        //    echo "Se procederá a sustituir la imagen actual.";
        //    $uploadOk = 0;
        }
        // Check file size. "2MB"
        if ($_FILES["fileToUpload"]["size"] > 2097152) {
            echo "Sólo se permite subir imagenes de hasta 2MB.";
            $uploadOk = 0;
        }
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" && $imageFileType != "bmp") {
            echo "Formatos de imagen válidos JPG, JPEG, PNG, GIF y BMP.";
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Lo sentimos, el fichero no puede subirse al servidor.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                // Actualizar la tabla instalación
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
                $sql = "update instalacion set imagen ='".$target_file."' where idinstalacion=".$_POST['idinstalacion'][0];
               // echo $sql;
                if ($mysqli->query($sql) === TRUE) {
                    echo "El fichero ".$target_file. " se ha subido correctamente.";
                } else {
                    echo "Error al actualizar instalación " . $mysqli->error;
                }
                $mysqli->close();
            } else {
                echo "Lo sentimos, se produjo un error en la subida del fichero. Vuelva a intentarlo.";
            }
        }
    }  
//End class
}
