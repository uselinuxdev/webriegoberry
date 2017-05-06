<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AlertClass
 *
 * @author Administrador
 */
class AlertClass {
    // Actualizar Usuarios
    public function updatealert()
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
        for($i=0;$i <count($_POST['idalert']);$i++)
        {
            // Controlar password si tiene datos se pinta
            // Preparar sentencia
            $stmt = $mysqli->prepare("UPDATE alertserver SET idparametro = ?,
                idusuario = ?, 
                estado = ?,  
                tipo = ?,
                operacion = ?,
                valor = ?,
                textalert = ?,
                nbit = ?,
                horaminbit = ?,
                horamaxbit = ?
                WHERE idalert = ?");
            // Bind variables
            $stmt->bind_param('iiiisisissi',
            $_POST['idparametro'][$i],
            $_POST['idusuario'][$i],
            $_POST['estado'][$i],
            $_POST['tipo'][$i],
            $_POST['operacion'][$i],
            $_POST['valor'][$i],
            $_POST['textalert'][$i],
            $_POST['nbit'][$i],
            $_POST['horaminbit'][$i],
            $_POST['horamaxbit'][$i],
            $_POST['idalert'][$i]);
            
            //echo "stmt bind_param correcto.";
            // Ejecutar
            $stmt->execute();
            // Finalizar
            $stmt->close();
        }
    }
    public function deletealert()
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
        $stmt = $mysqli->prepare("DELETE FROM alertserver WHERE idalert = ?");
        // Vincular variables
        if (!$stmt->bind_param("i", $_POST['idalertdelete'])) {
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
    public function insertalert()
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
        
        $sinsert = "INSERT INTO alertserver (idserver) VALUES (".$_SESSION['idserver'].")";
       
        if ($mysqli->query($sinsert) === TRUE)
        {
            //echo "Nuevo bitname creado.";
        } else {
            echo "Falló la inserción: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $mysqli->close();
        return 0;
    }
    public function cargacomboparam($name,$idparam)
        {
            mysql_connect($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass']) or die ("No se puede establecer la conexion!!!!"); 
            mysql_select_db($_SESSION['dbname']) or die ("Imposible conectar a la base de datos!!!!"); //Selecionas tu base
            mysql_set_charset('utf8'); // Importante juego de caracteres a utilizar.
            
            $sql = "SELECT parametros_server.idparametro, parametros_server.parametro FROM parametros_server,server_instalacion";
            $sql.=" where server_instalacion.idserver = parametros_server.idserver " ;
            $sql.=" and server_instalacion.estado = 1 " ;
            $sql.=" and parametros_server.estado > 0 " ;
            $sql.=" and parametros_server.nivel <= ".$_SESSION['nivel'];
            $sql.=" and parametros_server.idserver = ".$_SESSION['idserver'];
            $sql.=" order by parametros_server.parametro,parametros_server.estado ";
            // Pintar combo
            echo '<select name="'.$name.'" style="width: 180px;">'; 
            echo "<option value=0>Seleccionar parámetro</option>"; 
            // No definido
            $resparametros = mysql_query($sql);
            // Parametros de la select
            while($row = mysql_fetch_array($resparametros)) { //Iniciamos un ciclo para recorrer la variable $resparametros que tiene la consulta previamente hecha 
                $id = $row["idparametro"] ; //Asignamos el id del campo que quieras mostrar
                $vparametro = substr($row["parametro"],0,50); // Asignamos el nombre del campo que quieras mostrar
                //echo "<option value=".$id.">".$vparametro."</option>"; //Llenamos el option con su value que sera lo que se lleve al archivo registrar.php y que sera el id de tu campo y luego concatenamos tbn el nombre que se mostrara en el combo 
                $vcombo = "<option value=".$id;
                if($idparam==$id) {$vcombo = $vcombo. " SELECTED ";}
                $vcombo = $vcombo.">";
                $vcombo = $vcombo.$vparametro."</option>"; 
                echo $vcombo;
            } //Cerramos el ciclo 
            echo '</select>';
        }
        public function cargacombouser($name,$iduser)
        {
            mysql_connect($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass']) or die ("No se puede establecer la conexion!!!!"); 
            mysql_select_db($_SESSION['dbname']) or die ("Imposible conectar a la base de datos!!!!"); //Selecionas tu base
            mysql_set_charset('utf8'); // Importante juego de caracteres a utilizar.
            
            $sql = "SELECT idusuario,usuario from usuarios ";
            $sql.=" where idserver = ".$_SESSION['idserver'];
            $sql.=" order by usuario ";
            
            // Pintar combo
            echo '<select name="'.$name.'" style="width: 90px;">'; 
            // No definido
            $resuser= mysql_query($sql);
            // Parametros de la select
            while($row = mysql_fetch_array($resuser)) { //Iniciamos un ciclo para recorrer la variable $resparametros que tiene la consulta previamente hecha 
                $id = $row["idusuario"] ; //Asignamos el id del campo que quieras mostrar
                $vparametro = substr($row["usuario"],0,50); // Asignamos el nombre del campo que quieras mostrar
                //echo "<option value=".$id.">".$vparametro."</option>"; //Llenamos el option con su value que sera lo que se lleve al archivo registrar.php y que sera el id de tu campo y luego concatenamos tbn el nombre que se mostrara en el combo 
                $vcombo = "<option value=".$id;
                if($iduser==$id) {$vcombo = $vcombo. " SELECTED ";}
                $vcombo = $vcombo.">";
                $vcombo = $vcombo.$vparametro."</option>"; 
                echo $vcombo;
            } //Cerramos el ciclo 
            echo '</select>';
        }
    
    // End of class
}
