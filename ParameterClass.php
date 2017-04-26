<?php
/**
 * Description of ParameterClass
 *
 * @author Administrador
 */
class ParameterClass {
    public function updatebit()
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
        for($i =0;$i <count($_POST['idbit']);$i++)
        {
            $stmt = $mysqli->prepare("UPDATE parametros_bitname SET posicion=?,"
                    . "nombrebit=? "
                    . "WHERE idbit = ?");
            // Vincular variables
            if (!$stmt->bind_param("isi",
                    $_POST['posicion'][$i],
                    $_POST['nombrebit'][$i],
                    $_POST['idbit'][$i])) {
                echo "Falló la vinculación de parámetros: (" . $stmt->errno . ") " . $stmt->error;
                return -1;
            }
            // Ejecutar
            if (!$stmt->execute()) {
                echo "Falló la ejecución del update: (" . $sentencia->errno . ") " . $stmt->error;
                return -1;
            }else{
                //echo "Se borró correctamente el ID:".$_POST['idbitdelete'];
            }
            $stmt->close();
        }
        return 0;
    }
    public function deletebit()
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
        $stmt = $mysqli->prepare("DELETE FROM parametros_bitname WHERE idbit = ?");
        // Vincular variables
        if (!$stmt->bind_param("i", $_POST['idbitdelete'])) {
            echo "Falló la vinculación de parámetros: (" . $stmt->errno . ") " . $stmt->error;
            return -1;
        }
        // Ejecutar
        if (!$stmt->execute()) {
            echo "Falló la ejecución del delete: (" . $sentencia->errno . ") " . $stmt->error;
            return -1;
        }else{
            //echo "Se borró correctamente el ID:".$_POST['idbitdelete'];
        }
        $stmt->close();
        return 0;
    }
}
