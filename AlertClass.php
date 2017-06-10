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
    // Funcion publica, recorre las alertas por tipo: 0 bit, 1 diaria,2 mensual
    public function checkalert($tipolectura)
        {
            // Conexiones
            $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname']);
            if ($mysqli->connect_errno)
            {
                echo $mysqli->host_info."\n";
                exit();
            }
            // Importante juego de caracteres
            if (!mysqli_set_charset($mysqli, "utf8")) {
                printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($mysqli));
                exit();
            } 
            $sselect ="select * from alertserver where tipo=".$tipolectura." and estado=1 order by idparametro";
            $result = $mysqli->query($sselect) or exit("Codigo de error ({$mysqli->errno}): {$mysqli->error}");
            while($row = mysqli_fetch_array($result)) {
                // Por cada parametero recuperar la select
               //echo "Correcto:".$row['idparametro'];
               $rowvalor = $this->valorbd($row['idparametro'],$tipolectura);
               // Controlar q $rowvalor tiene filas. Procesar la filas encontradas
               if(!empty($rowvalor))
               {
                  //echo 'Valor del día:'.$rowvalor['VALOR']." / Valor de la alerta:".$row['valor'];
                  switch ($row['operacion']) {
                        case "=":
                            if ($rowvalor['VALOR'] == $row['valor']){
                                // Mail alerta
                                $this->mailalert($rowvalor,$row);
                            }
                            break;
                        case "!=":
                            if ($rowvalor['VALOR'] != $row['valor']){
                                // Mail alerta
                                $this->mailalert($rowvalor,$row);
                            }
                            break;
                        case ">=":
                            if ($rowvalor['VALOR'] >= $row['valor']){
                                // Mail alerta
                                $this->mailalert($rowvalor,$row);
                            }
                            break;
                        case "<=": 
                            if ($rowvalor['VALOR'] <= $row['valor']){
                                // Mail alerta
                                $this->mailalert($rowvalor,$row);
                            }
                            break;
                        case ">":  
                            if ($rowvalor['VALOR'] > $row['valor']){
                                // Mail alerta
                                $this->mailalert($rowvalor,$row);
                            }
                            break;
                        case "<":  
                            if ($rowvalor['VALOR'] < $row['valor']){
                                // Mail alerta
                                $this->mailalert($rowvalor,$row);
                            }
                            break;
                  }
               }
            }
        }
    // Función mail alerta
    private function mailalert($rowvalor,$row)
    {
        // Se el pasa $rowvalor: Datos del dia/mes. $row los datos de la alerta.
       
        return 1;
    }
    // Retorna array 
    private function valorbd($vparam,$tipolectura)
        {
            // Conexiones
            $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname']);
            if ($mysqli->connect_errno)
            {
                echo $mysqli->host_info."\n";
                exit();
            }
            // Importante juego de caracteres
            if (!mysqli_set_charset($mysqli, "utf8")) {
                printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($mysqli));
                exit();
            }
            // Coger la variable string de filtro de fecha
            $sdate=$this->getfecha($tipolectura);
            switch ($tipolectura) {
            case 2:
                // Mes actual. Coger los 2 días últimos calculados
                $sselect ="SELECT NOMBREP,PREFIJO,POSDECIMAL,SUM(VALOR) AS VALOR FROM vgrafica_dias ";
                $sselect.="WHERE idparametro = ".$vparam;
                $sselect.= $sdate;
                $sselect .=" group by idparametro";
                break;
//            case 3:
//                // Año actual
//                $sselect = "SELECT NOMBREP,PREFIJO,POSDECIMAL,SUM(VALOR) AS VALOR FROM vgrafica_dias ";
//                $sselect.="WHERE idparametro = ".$vparam;
//                $sselect.= $sdate;
//                $sselect.=" GROUP BY idparametro";
//                break;
            default:
                $sselect = "SELECT NOMBREP,COLOR,PREFIJO,POSDECIMAL,SUM(VALOR) AS VALOR FROM vgrafica_dias ";
                $sselect.="WHERE idparametro = ".$vparam;
                $sselect.= $sdate;
               // echo $sselect;
            }
            // Recuperar array
            $result = $mysqli->query($sselect) or exit("Codigo de error ({$mysqli->errno}): {$mysqli->error}");
            $rowvalor = mysqli_fetch_array($result);
            // Retorna un array.
            return $rowvalor;
        }
    // Retorna el filtro de fechas según el tipo deseado(1 día, 2 mes, 3 año) 
    //public function getfecha($tipolectura) 
    private function getfecha($tipolectura) 
    {
        // La función con la tipo lectura, returna un rango de fechas
        // Para alertas se coge el día anterior, mes anterior, año aterior.
        $sqldate = "";
        $vfecha =date('Y-m-d'); 
        // Fecha pruebas /////////////////////////////////////////////////////////////////////////////////////////////// <--
        $vfecha = "2017-05-31";
        switch ($tipolectura) {
        case 2:
            // Fecha del mes anterior
            $vmes = date('m',strtotime($vfecha));
            $vyear = date('Y',strtotime($vfecha));
            // Formato de fecha estandar yyyy-mm-dd HH:mm:ss
            $vfecha = "01-".$vmes."-".$vyear;
            $vdesde = date("Y-m-d H:i:s", strtotime('-1 month', strtotime($vfecha)));
            $vhasta = date("Y-m-d H:i:s", strtotime('+0 month',strtotime($vfecha)));
            break;
        case 3:
            // Ejecicio anterior
            $vyear = date('Y',strtotime($vfecha));
            // Formato de fecha estandar yyyy-mm-dd HH:mm:ss
            $vfecha = "01-01-".strval(intval($vyear)-1);
            //echo $vfecha;
            $vdesde = date("Y-m-d H:i:s", strtotime('+0 hours', strtotime($vfecha)));
            $vhasta = date("Y-m-d H:i:s", strtotime('+1 year',strtotime($vfecha)));
            break;
        default:
            // fecha de ayer.
            $vdesde = date("Y-m-d H:i:s", strtotime('-1 days', strtotime($vfecha)));
            $vhasta = date("Y-m-d H:i:s", strtotime('+0 days',strtotime($vfecha)));
        }
        // Retornar rango de fechas
        $sqldate = " AND flectura >= '".date($vdesde)."'";
        $sqldate.=" AND flectura < '".date($vhasta)."'";
        // Retornar fechas
        return $sqldate;
    }
    // End of class
}
