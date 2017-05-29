<?php
/**
 * Description of ParameterClass
 *
 * @author Administrador
 */
class ParameterClass {
    // Lógica parametros_server    
    public function updateparameter()
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
        for($i =0;$i <count($_POST['idparametro']);$i++)
        {
            $stmt = $mysqli->prepare("UPDATE parametros_server SET idserver =?,"
                    . "parametro=?,"
                    . "TIPO=?,"
                    . "posiciones=?,"
                    . "lectura=?,"
                    . "pmemoria=?,"
                    . "estado=?,"
                    . "prefijonum=?,"
                    . "posdecimal=?,"
                    . "estlink=?,"
                    . "nivel=?,"
                    . "color=? "
                    . " WHERE idparametro = ?");

            // Vincular variables
            if (!$stmt->bind_param("ississisiiisi",
                    $_POST['idserver'][$i],
                    $_POST['parametro'][$i],
                    $_POST['tipo'][$i],
                    $_POST['posiciones'][$i],
                    $_POST['lectura'][$i],
                    $_POST['pmemoria'][$i],
                    $_POST['estado'][$i],
                    $_POST['prefijonum'][$i],
                    $_POST['posdecimal'][$i],
                    $_POST['estlink'][$i],
                    $_POST['nivel'][$i],
                    $_POST['color'][$i],
                    $_POST['idparametro'][$i])) {
                echo "Falló la vinculación de parámetros: (" . $stmt->errno . ") " . $stmt->error;
                return -1;
            }
            // Ejecutar
            if (!$stmt->execute()) {
                echo "Falló la ejecución del update: (" . $stmt->errno . ") " . $stmt->error;
                return -1;
            }else{
                //echo "Update realizado.";
            }
            $stmt->close();
        }
        return 0;
    }
    public function deleteparameter()
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
        $stmt = $mysqli->prepare("DELETE FROM parametros_server WHERE idparametro = ?");
        // Vincular variables
        if (!$stmt->bind_param("i", $_POST['idparamdelete'])) {
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
    public function insertparameter()
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
        $sinsert = "INSERT INTO parametros_server(idserver,parametro,lectura,pmemoria) VALUES (".$_SESSION['idserver'].",'0 Parametro insertado.','M',-1)";
       
        if ($mysqli->query($sinsert) === TRUE)
        {
            //echo "Nuevo bitname creado.";
        } else {
            echo "Falló la inserción: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $mysqli->close();
        return 0;
        
//        $stmt = $mysqli->prepare("INSERT INTO parametros_server(idserver,"
//                . "parametro,"
//                . "lectura,"
//                . "pmemoria) "
//                . "VALUES (?, ?, ?, ?)");
//        // Vincular variables
//        if (!$stmt->bind_param('isss', 
//                $_SESSION['idserver'],
//                '0 Parametro insertado.',
//                'M',
//                '-1')) {
//            echo "Falló la vinculación de parámetros: (" . $stmt->errno . ") " . $stmt->error;
//            return -1;
//        }
//        echo "Preparado.";
//        // Ejecutar
//        if (!$stmt->execute()) {
//            echo "Falló la ejecución del insert: (" . $stmt->errno . ") " . $stmt->error;
//            return -1;
//        }else{
//            //echo "Se borró correctamente el ID:".$_POST['idbitdelete'];
//        }
//        $stmt->close();
//        return 0;
    }
    // Logica parametros_bitname
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
                echo "Falló la ejecución del update: (" . $stmt->errno . ") " . $stmt->error;
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
            echo "Falló la ejecución del delete: (" . $stmt->errno . ") " . $stmt->error;
            return -1;
        }else{
            //echo "Se borró correctamente el ID:".$_POST['idbitdelete'];
        }
        $stmt->close();
        return 0;
    }
    public function insertbit()
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
        $sinsert = "INSERT INTO parametros_bitname(idparametro,posicion,nombrebit) VALUES (".$_POST['cbvalorbit'].",-1,'Nombre bit.')";
       
        if ($mysqli->query($sinsert) === TRUE)
        {
            //echo "Nuevo bitname creado.";
        } else {
            echo "Falló la inserción: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $mysqli->close();
        return 0;
//        $stmt = $mysqli->prepare("INSERT INTO parametros_bitname(idparametro,posicion,nombrebit) VALUES (?, ?, ?)");
//        // Vincular variables
//        if (!$stmt->bind_param('iis', $_POST['cbvalorbit'],-1,'Nombre bit.')) {
//            echo "Falló la vinculación de parámetros: (" . $stmt->errno . ") " . $stmt->error;
//            return -1;
//        }
//        echo "Preparado.";
//        // Ejecutar
//        if (!$stmt->execute()) {
//            echo "Falló la ejecución del insert: (" . $stmt->errno . ") " . $stmt->error;
//            return -1;
//        }else{
//            //echo "Se borró correctamente el ID:".$_POST['idbitdelete'];
//        }
//        $stmt->close();
//        return 0;
    }
    public function cargacomboserver($name,$idparam)
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
            
            $sql = "SELECT idserver,nombreserver from server_instalacion where estado > 0 ";
            
            // Pintar combo
            echo '<select name="'.$name.'" style="width: 115px;">'; 
            // No definido
            $resparametros = mysql_query($sql);
            // Parametros de la select
            while($row = mysql_fetch_array($resparametros)) { //Iniciamos un ciclo para recorrer la variable $resparametros que tiene la consulta previamente hecha 
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
}
