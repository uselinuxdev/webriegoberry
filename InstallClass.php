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
        for($i =0;$i <count($_POST['idinstalacion']);$i++)
        {
            //printf("Conjunto de caracteres actual: %s\n", mysqli_character_set_name($mysqli));
            // Preparar sentencia
            $stmt = $mysqli->prepare("UPDATE instalacion SET nombre = ?,
                titular = ?, 
                cif = ?, 
                falta = ?,
                ubicacion = ?,
                pico = ?,
                modulos = ?,
                inversor = ?,
                iflagmail =?
                WHERE idinstalacion = ?");
            //echo "stmt preparado correctamente.";
            $stmt->bind_param('ssssssssii',
                $_POST['nombre'][$i],
                $_POST['titular'][$i],
                $_POST['cif'][$i],
                $_POST['falta'][$i],
                $_POST['ubicacion'][$i],
                $_POST['pico'][$i],
                $_POST['modulos'][$i],
                $_POST['variador'][$i],
                $_POST['iflagmail'][$i],
                $_POST['idinstalacion'][$i]);
            //echo "stmt bind_param correcto.";
            // Ejecutar
            $stmt->execute();
            // Finalizar
            $stmt->close();
            $this->EventMail($_POST['iflagmail'][$i]);
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
    // Control version
    public function getdbversion()
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
        $sql = "select version from instalacion where estado=1";
        //echo $sql;
        $consulta = mysqli_query($mysqli, $sql);
        if (!$consulta) {
            printf("Database Version 0.Creating DB control version.");
            $this->createDbControl($mysqli);
        } else {
            $row = mysqli_fetch_array($consulta,MYSQLI_ASSOC); 
            echo "Versión de base de datos: ".$row['version']."\n";
            $this->syncDbMaster($row['version']);
        }
        return 1;
    }
    // Create dbversion control
    private function createDbControl($mysqli)
    {
        $sql = "ALTER TABLE instalacion ADD COLUMN (version varchar(10) DEFAULT '1.00')";
        if ($mysqli->query($sql) === FALSE) {
            echo "Error al actualizar B.D. " . $mysqli->error;
            return 0;
        }
        // $sql = "alter table instalacion drop column if exists version";
        // Crear tabla de de versiones
        $sql = "CREATE TABLE dbchangever (
                mayorver int NOT NULL,
                minorver int NOT NULL,
                fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
                comentario varchar(100),
                dmlsql varchar(500),
                dmlrollback varchar(500),
                primary key (mayorver,minorver)) ";
        // Create db table changes
        if ($mysqli->query($sql) === FALSE) {
            echo "Error al actualizar B.D. " . $mysqli->error;
            return 0;
        }
        // $sql = "drop table DBCHANGEVER";
        // Crear tabla de de config
        $sql = "CREATE TABLE dbchangecfg (
                source varchar(100) NOT NULL,
                portdb int NULL DEFAULT 3306,
                userdb varchar(100),
                passdb varchar(100),
                primary key (source)) ";
        // Create db table changes
        if ($mysqli->query($sql) === FALSE) {
            echo "Error al actualizar B.D. " . $mysqli->error;
            return 0;
        }
        $vpass64= base64_encode('riegoprod15');
        $sql = "INSERT INTO dbchangecfg (source,portdb,userdb,passdb)"
                . " VALUES('contabo.riegosolar.net',3306,'riegosql','".$vpass64."') ";
        // Create db table changes
        if ($mysqli->query($sql) === FALSE) {
            echo "Error al actualizar B.D. " . $mysqli->error;
            return 0;
        }        
        // Insertar rows 
        $sql = "insert into dbchangever (mayorver, minorver,comentario,dmlsql,dmlrollback) 
                values (1,0,'Cambio tabla instalación.',
               'ALTER TABLE instalacion ADD COLUMN (version varchar(10) DEFAULT \'1.00\')',
               'alter table instalacion drop column if exists version') ";
        //echo $sql;
        if ($mysqli->query($sql) === FALSE) {
            echo "Error al actualizar B.D. " . $mysqli->error;
            return 0;
        }
        $sql = "insert into dbchangever (mayorver, minorver,comentario,dmlsql,dmlrollback) 
                values (1,1,'Creación tabla DBCHANGEVER.',
                'CREATE TABLE dbchangever (
                  mayorver int NOT NULL,
                  minorver int NOT NULL,
                  comentario varchar(100),
                  dmlsql varchar(500),
                  dmlrollback varchar(500),
                  primary key (mayorver,minorver))',
                'drop table dbchangever')";
        if ($mysqli->query($sql) === FALSE) {
            echo "Error al actualizar B.D. " . $mysqli->error;
            return 0;
        }
        $sql = "insert into dbchangever (mayorver, minorver,comentario,dmlsql,dmlrollback) 
                values (1,2,'Creación tabla DBCHANGECFG.',
                'CREATE TABLE dbchangecfg (
                 source varchar(100) NOT NULL,
                 portdb int NULL DEFAULT 3306,
                 userdb varchar(100),
                 passdb varchar(100),
                 primary key (source))','drop table dbchangecfg')";
        //echo $sql;
        if ($mysqli->query($sql) === FALSE) {
            echo "Error al actualizar B.D. " . $mysqli->error;
            return 0;
        }
        $sql = "insert into dbchangever (mayorver, minorver,comentario,dmlsql,dmlrollback) 
                values (1,3,'Row DBCHANGEVER.',
                'INSERT INTO dbchangecfg (source,portdb,userdb,passdb)
                VALUES(\'contabo.riegosolar.net\',3306,\'riegosql\',\'".$vpass64."\')','delete from dbchangecfg')";
        //echo $sql;
        if ($mysqli->query($sql) === FALSE) {
            echo "Error al actualizar B.D. " . $mysqli->error;
            return 0;
        }
        $sql = "insert into dbchangever (mayorver, minorver,comentario,dmlsql,dmlrollback) 
                values (1,4,'Proceso Sync DB.',
                'CREATE OR REPLACE EVENT SYNCBD
                ON SCHEDULE AT CURRENT_TIMESTAMP + INTERVAL 1 DAY
                DO SELECT sys_exec(\'php /var/www/html/riegosolar/InstallClassos.php\')',
                'DROP EVENT IF EXISTS SYNCBD')";
       // echo $sql;
        if ($mysqli->query($sql) === FALSE) {
            echo "Error al actualizar B.D. " . $mysqli->error;
            return 0;
        }
        $sql = "CREATE OR REPLACE EVENT SYNCBD
                ON SCHEDULE AT CURRENT_TIMESTAMP + INTERVAL 1 DAY
                DO SELECT sys_exec('php /var/www/html/riegosolar/InstallClassos.php')";
        //echo $sql;
        if ($mysqli->query($sql) === FALSE) {
            echo "Error al actualizar B.D. " . $mysqli->error;
            return 0;
        }        
        $sql = "UPDATE instalacion set version='1.4' where estado=1";
        //echo $sql;
        if ($mysqli->query($sql) === FALSE) {
            echo "Error al actualizar B.D. " . $mysqli->error;
            return 0;
        }        
        $mysqli->close();        
        // Bien
        echo "DB Version configurada 1.4.\n";
        return 1;
    }
    private function syncDbMaster($verdb)
    {
       // Get dbchangecfg and cursor of all rows order by mayor,minor.
       $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
       if ($mysqli->connect_errno)
       {
            echo $mysqli->host_info."\n";
            return -1;
       }
       mysqli_set_charset($mysqli, "utf8");
       $sql = "select source,portdb,userdb,passdb from dbchangecfg";
       $consulta = mysqli_query($mysqli, $sql);
        if (!$consulta) {
            printf("Error al leer tabla dbchangecfg.");
            return 0;
        }
        $row = mysqli_fetch_array($consulta,MYSQLI_ASSOC); 
        $vpass64= base64_decode($row['passdb']);
        //$row['source'];
        $myMaster = new mysqli($row['source'],$row['userdb'],$vpass64,$_SESSION['dbname'],$row['portdb']);
        if ($myMaster->connect_errno)
        {
            echo $myMaster->host_info."\n";
            return -1;
        }
        mysqli_set_charset($myMaster, "utf8");
        //echo "Conexión correcta dbMaster:".$row['source']."\n";
        $result = mysqli_query($myMaster,"SELECT mayorver,minorver,comentario,dmlsql,dmlrollback from dbchangever where concat(mayorver,'.',minorver) >'".$verdb."'"); 
        while( $row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
            echo $row['dmlsql'];
            // Ejecuta.
            // Crear cnf log & update version
            $this->CreateChangeRow($row,$mysqli);
        }
        //Bien
        return 1;      
    }
    private function CreateChangeRow($vrow,$mysqli)
    {
        // Create log
        $vdml = $vrow['dmlsql'];
        $vdml = str_replace("'", "\'", $vdml);
        $vroll = $vrow['dmlrollback'];
        $vroll = str_replace("'", "\'", $vroll);
        $sql = "insert into dbchangever (mayorver, minorver,comentario,dmlsql,dmlrollback) 
                values (".$vrow['mayorver'].",".$vrow['minorver'].",'".$vrow['comentario']."',
                '".$vdml."',
                '".$vroll."')";
        
        if ($mysqli->query($sql) === FALSE) {
            echo "Error al actualizar B.D. " . $mysqli->error;
            return 0;
        } 
        // Run dml
       if ($mysqli->query($vrow['dmlsql']) === FALSE) {
            echo "Error al actualizar B.D. " . $mysqli->error;
            return 0;
        }
        // UPDATE VERSION
        $sql = "UPDATE instalacion set version='".$vrow['mayorver'].".".$vrow['minorver']."' where estado=1";
        if ($mysqli->query($sql) === FALSE) {
            echo "Error al actualizar B.D. " . $mysqli->error;
            return 0;
        } 
        //Bien
        return 1;
    }
    
    private function EventMail($iflagmail) 
    {
        if($iflagmail==0)
        {
            $sql ="DROP EVENT IF EXISTS MAILSUMARY";
            if ($mysqli->query($sql) === FALSE) {
                echo "Error al actualizar B.D. " . $mysqli->error;
                return 0;
            } 
        }
        else
        {
            // Comprobar path de binarios
            $sbin='/var/www/html/riegosolar/mailsumaryos.php';
            //Pegar full path de imagen
            if (!file_exists($sbin)) {
                //Old servers
                $sbin='/var/www/riegosolar/mailsumaryos.php';
                echo "The file $sbin exists";
            }
            $sql = "CREATE OR REPLACE EVENT MAILSUMARY ON SCHEDULE EVERY '1' DAY ";
            $sql.= "STARTS '".date('d/m/Y H:i:s')."' ON COMPLETATION PRESERVER ENABLE ";
            $sql.= "DO SELECT sys_exec('".$sbin." ".$_SESSION['usuario']." ".$_SESSION['passap']."')";
            echo $sql;
            if ($mysqli->query($sql) === FALSE) {
                echo "Error al actualizar B.D. " . $mysqli->error;
                return 0;
            } 
        }
        // Bien
        return 1;
    }
    
//End class
}
