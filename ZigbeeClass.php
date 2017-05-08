<?php
/**
 * Description of ZigbeeClass
 *
 * @author Administrador
 */
class ZigbeeClass {
    // Private vars
    private $segscan=15;
    // Resetear nodos
    public function resetnodes()
    {
        $sql = "update nodos set estado = 0 where source_addr = '0000'";
    }
    // Actualizar nodos
    public function updatenodes()
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
        for($i =0;$i <count($_POST['nombre_nodo']);$i++)
        {
            $rescan = 0;
            //echo "Formulario update_nodo pulsado.".$_POST['nombre_nodo'][$i];
            //echo "Se actualizará el nodo Nº:".$_POST['idnodo'][$i]."\n";
            // Preparar sentencia
            $stmt = $mysqli->prepare("UPDATE nodos SET nombre_nodo = ?, 
                source_addr = ?, 
                source_addr_long = ?,  
                node_identifier = ?,  
                parent_address = ?,
                device_type = ?,
                estado = ?
                WHERE idnodo = ?");
           // echo "stmt preparado correctamente.";
            $stmt->bind_param('ssssssii',
                $_POST['nombre_nodo'][$i],
                $_POST['source_addr'][$i],
                $_POST['source_addr_long'][$i],
                $_POST['node_identifier'][$i],
                $_POST['parent_address'][$i],
                $_POST['device_type'][$i],
                $_POST['estado'][$i],
                $_POST['idnodo'][$i]);
            //echo "stmt bind_param correcto.";
            // Ejecutar
            $stmt->execute();
            // Finalizar
            $stmt->close();

            //echo "El estado es:".$_POST['estado'][$i];
            if ($_POST['estado'][$i] == 0) {
               $rescan = 1;
            }
        }
        // Control de reescaneo por todos los nodos solo 1 sleep.
        if ($rescan == 1) {
            echo "Reescaneo de nodos.Se ha definido un tiempo espera de ".$this->segscan." segundos.\n";
            echo date('h:i:s') . "\n";
            sleep($this->segscan);
            echo date('h:i:s') . "\n";
        }
    }
    public function deletenodes()
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
        $stmt = $mysqli->prepare("DELETE FROM nodos WHERE idnodo = ?");
        // Vincular variables
        if (!$stmt->bind_param("i", $_POST['idnododelete'])) {
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
    public function insertnodes()
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
        
        $sinsert = "INSERT INTO nodos(nombre_nodo,source_addr,source_addr_long,parent_address,device_type,estado) "
                . "VALUES ('NUEVO NODO','9999','9999','9999','99',1)";
       
        if ($mysqli->query($sinsert) === TRUE)
        {
            //echo "Nuevo bitname creado.";
        } else {
            echo "Falló la inserción: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $mysqli->close();
        return 0;
    }
    
    public function updatesector()
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
        for($i =0;$i <count($_POST['num_sector']);$i++)
        {
            // Preparar sentencia
            $stmt = $mysqli->prepare("UPDATE sectores SET num_sector = ?, 
                nombre_sector = ?, 
                num_salida = ?,  
                time_latch = ?
                WHERE idsector = ?");
           // echo "stmt preparado correctamente.";
            $stmt->bind_param('isiii',
                $_POST['num_sector'][$i],
                $_POST['nombre_sector'][$i],
                $_POST['num_salida'][$i],
                $_POST['time_latch'][$i],
                $_POST['idsector'][$i]);
            //echo "stmt bind_param correcto.";
            // Ejecutar
            $stmt->execute();
            // Finalizar
            $stmt->close();
        }
    }
    
    public function deletesector()
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
        $stmt = $mysqli->prepare("DELETE FROM sectores WHERE idsector = ?");
        // Vincular variables
        if (!$stmt->bind_param("i", $_POST['idsectordelete'])) {
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
    public function insertsector()
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
        // Tiene que haber combo seleccionado
        $sinsert = "INSERT INTO sectores(num_sector,nombre_sector,idnodo,num_salida) "
                . "VALUES (0,'NUEVO SECTOR',".$_POST['cbnodos'].",0)";
       
        if ($mysqli->query($sinsert) === TRUE)
        {
            //echo "Nuevo bitname creado.";
        } else {
            echo "Falló la inserción: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $mysqli->close();
        return 0;
    }
    // Función combo
    function cargacombonodos()
    {
        mysql_connect($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass']) or die ("No se puede establecer la conexion!!!!"); 
        mysql_select_db($_SESSION['dbname']) or die ("Imposible conectar a la base de datos!!!!"); //Selecionas tu base
        mysql_set_charset('utf8'); // Importante juego de caracteres a utilizar.

        $sql = "SELECT idnodo,node_identifier,source_addr_long FROM nodos where device_type <> '00' order by node_identifier ";
  
        // Pintar combo
        echo '<select name="cbnodos">'; 
        $resparametros = mysql_query($sql);
        echo "<option value=0>  Seleccionar un nodo </option>"; 
        while($row = mysql_fetch_array($resparametros)) { //Iniciamos un ciclo para recorrer la variable $resparametros que tiene la consulta previamente hecha 
            $id = $row["idnodo"] ; //Asignamos el id del campo que quieras mostrar
            $vparametro = substr($row["node_identifier"]." - ".$row["source_addr_long"],0,50); // Asignamos el nombre del campo que quieras mostrar
            //echo "<option value=".$id.">".$vparametro."</option>"; //Llenamos el option con su value que sera lo que se lleve al archivo registrar.php y que sera el id de tu campo y luego concatenamos tbn el nombre que se mostrara en el combo 
            $vcombo = "<option value=".$id;
            if($_POST['cbnodos']==$id) {
                $vcombo = $vcombo. " SELECTED ";
            }
        $vcombo = $vcombo.">";
        $vcombo = $vcombo.$vparametro."</option>"; 
        echo $vcombo;
        } //Cerramos el ciclo 
        echo '</select>';
    }
// end class
}
