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
class ExportClass {
    // Private vars
    
    // Public function
    public function selectMaster()
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
        $sql = "select * from exportdata";
        //echo $sql;
        $consulta = mysqli_query($mysqli, $sql);
        if ($consulta->num_rows==0) {
            return $this->insertmaster();
        } else {
            //Tiene que pasar por aqui para ser correcto
            $row = mysqli_fetch_array($consulta,MYSQLI_ASSOC); 
            return $row;
        }
    }
    private function insertmaster()
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
        // Insertar rows 
        $sql = "insert into exportdata (format,copytype,status,hoursend,grouptime,server,path,user,pass) 
                values ('CHE','sftp',0,'0:15',15,'localhost','/tmp','pi','Riegosolar77') ";
        //echo $sql;
        if ($mysqli->query($sql) === FALSE) {
            echo "Error al actualizar B.D. " . $mysqli->error;
            return 0;
        }
        $sql = "select * from exportdata";
        //echo $sql;
        $consulta = mysqli_query($mysqli, $sql);
        if (!$consulta) {
            return 0;
        } else {
            //Tiene que pasar por aqui para ser correcto
            $row = mysqli_fetch_array($consulta,MYSQLI_ASSOC); 
            return $row;
        }
        return 0;
        
    }
    public function selectParm()
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
        $sql = "select * from exportdataparm";
        //echo $sql;
        $consulta = mysqli_query($mysqli, $sql);
        if ($consulta->num_rows>0) {
            //Tiene que pasar por aqui para ser correcto
            /////$row = mysqli_fetch_array($consulta,MYSQLI_ASSOC); 
            return $consulta;
        }
        return 0;
    }
    // Combo parametro details
    public function cargacomboparam($name,$idparam)
        {
            $cndb=mysqli_connect($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
            if (!$cndb) {
                echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
                echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
                echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
                exit;
            }
            mysqli_set_charset($cndb, "utf8");
            
            $sql = "SELECT parametros_server.idparametro, parametros_server.parametro FROM parametros_server,server_instalacion";
            $sql.=" where server_instalacion.idserver = parametros_server.idserver " ;
            $sql.=" and server_instalacion.estado = 1 " ;
            $sql.=" and parametros_server.estado > 0 " ;
            $sql.=" and parametros_server.nivel <= ".$_SESSION['nivel'];
            $sql.=" and parametros_server.idserver = ".$_SESSION['idserver'];
            $sql.=" order by parametros_server.parametro,parametros_server.estado ";
            // Pintar combo
            echo '<select name="'.$name.'" style="width: 300px;">'; 
            echo "<option value=0>Seleccionar parámetro</option>"; 
            // No definido
            $resparametros = mysqli_query($cndb,$sql);  
            // Parametros de la select
            while($row = mysqli_fetch_array($resparametros,MYSQLI_ASSOC)) { //Iniciamos un ciclo para recorrer la variable $resparametros que tiene la consulta previamente hecha 
                $id = $row["idparametro"] ; //Asignamos el id del campo que quieras mostrar
                $vparametro = substr($row["parametro"],0,100); // Asignamos el nombre del campo que quieras mostrar
                //echo "<option value=".$id.">".$vparametro."</option>"; //Llenamos el option con su value que sera lo que se lleve al archivo registrar.php y que sera el id de tu campo y luego concatenamos tbn el nombre que se mostrara en el combo 
                $vcombo = "<option value=".$id;
                if($idparam==$id) {$vcombo = $vcombo. " SELECTED ";}
                $vcombo = $vcombo.">";
                $vcombo = $vcombo.$vparametro."</option>"; 
                echo $vcombo;
            } //Cerramos el ciclo 
            echo '</select>';
        }
    // Actualizar datos install
    public function updateexport()
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
        for($i =0;$i <count($_POST['id']);$i++)
        {
            //printf("Conjunto de caracteres actual: %s\n", mysqli_character_set_name($mysqli));
            // Preparar sentencia
            $stmt = $mysqli->prepare("UPDATE exportdata SET format = ?,
                copytype = ?, 
                status = ?, 
                hoursend = ?,
                grouptime = ?,
                server = ?,
                path = ?,
                user = ?,
                pass = ?,
                comment = ?
                WHERE id = ?");
            //echo "stmt preparado correctamente.";
            if (!$stmt->bind_param('ssisisssssi',
                $_POST['format'][$i],
                $_POST['copytype'][$i],
                $_POST['status'][$i],
                $_POST['hoursend'][$i],
                $_POST['grouptime'][$i],
                $_POST['server'][$i],
                $_POST['path'][$i],
                $_POST['user'][$i],
                $_POST['pass'][$i],
                $_POST['comment'][$i],
                $_POST['id'][$i])){
                     echo "Falló la vinculación de parámetros: (" . $stmt->errno . ") " . $stmt->error;
                     return -1;
                }
            //echo "stmt bind_param correcto.";
            // Ejecutar
            $stmt->execute();
            printf("Error: %s.\n", $sentencia->error);
            // Finalizar
            $stmt->close();
        }
    }
    public function insertexportparm()
    {
        $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
        if ($mysqli->connect_errno)
        {
            echo $mysqli->host_info."\n";
            return -1;
        }
        // Importante juego de caracteres
        //printf("Conjunto de caracteres inicial insertexportparm: %s\n", mysqli_character_set_name($mysqli));
        //return 0;
        if (!mysqli_set_charset($mysqli, "utf8")) {
            printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($mysqli));
            exit();
        }
        // Insertar rows 
        $sql = "insert into exportdataparm (idexport,idparametro) 
                select id,0 from exportdata ";
        if ($mysqli->query($sql) === FALSE) {
            echo "Error al actualizar B.D. " . $mysqli->error;
            return 0;
        }
        $sql = "select max(id) from exportdataparm";
        $consulta = mysqli_query($mysqli, $sql);
        if (!$consulta) {
            echo "Error en consulta max id exportdataparm.";
            return 0;
        } else {
            //Tiene que pasar por aqui para ser correcto
            $row = mysqli_fetch_array($consulta,MYSQLI_ASSOC); 
            return $row[0];
        }
        return 0;
    }
    public function updateexportparm()
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
        for($i =0;$i <count($_POST['id']);$i++)
        {
            //printf("Conjunto de caracteres actual: %s\n", mysqli_character_set_name($mysqli));
            //return 0;
            if(!$_POST['id'][$i] || $_POST['id'][$i] == 0)
            {
                $newid=$this->insertexportparm();
                echo $newid;
                $_POST['id'][$i]=$newid;
            }
            // Preparar sentencia
            $stmt = $mysqli->prepare("UPDATE exportdataparm SET idparametro = ?
                WHERE id = ?");
            if (!$stmt->bind_param('ii',
                $_POST['idparametro'][$i],
                $_POST['id'][$i])){
                     echo "Falló la vinculación de parámetros: (" . $stmt->errno . ") " . $stmt->error;
                     return -1;
                }
            //echo "stmt bind_param correcto.";
            // Ejecutar
            if (!$stmt->execute())
            {
                printf("Error: %s.\n", $sentencia->error);
            }
            // Finalizar
            $stmt->close();
        }
    }
    public function deleteparameter()
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
        //echo "ID to delete:".$_POST['idparmdelete'];
        //return 0;
        $stmt = $mysqli->prepare("DELETE FROM exportdataparm WHERE id = ?");
        // Vincular variables
        if (!$stmt->bind_param("i", $_POST['idparmdelete'])) {
            echo "Falló la vinculación de parámetros: (" . $stmt->errno . ") " . $stmt->error;
            return -1;
        }
        // Ejecutar
        if (!$stmt->execute()) {
            echo "Falló la ejecución del delete: (" . $stmt->errno . ") " . $stmt->error;
            return -1;
        }else{
            echo "Se borró correctamente el ID:".$_POST['idparmdelete'];
        }
        $stmt->close();
        return 0;
    }    
    
//End class
}
