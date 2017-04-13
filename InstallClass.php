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
                $_POST['idinstalacion']);
            //echo "stmt bind_param correcto.";
            // Ejecutar
            $stmt->execute();
            // Finalizar
            $stmt->close();
        }
    }
    public function updateimagen()
    {
        echo "Clase y funcion updateimagen";
    }  
//End class
}
