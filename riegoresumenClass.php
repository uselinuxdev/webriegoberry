<?php
class riegoresumenClass
{
    private $divid;
    private $aparam;

    public function verClase()
    {
        /**
        Put your database code here to extract from database.
        **/
        return($this->divid);
    }
    public function cargarClase($TName)
    {
        $this->divid = $TName;
        /**
        Alamacenar los parametros de la sección
        **/
        $strQuery = "SELECT tipolectura,idparametroa,idparametrob,idparametroc
                    FROM admresumen
                    where divid ='".$this->divid."'";
        // Execute the query, or else return the error message.
     	$link = new PDO("mysql:host=".$_SESSION['serverdb'].";dbname=".$_SESSION['dbname'], $_SESSION['dbuser'], $_SESSION['dbpass']);
	$result = $link->query($strQuery);
	$this->aparam= $result->fetchAll(PDO::FETCH_ASSOC);
    }
    public function verParam()
    {
        return $this->aparam;
    }
    public function maxlecturap()
    {
        // La función obtiene la máxima lectura de los parametros de la sección
        $amax = array();
        $parametros = array();
        // Recorrer array de parametros 1 fila hasta 3 parametros
            
        // Conexión
        $link = new PDO("mysql:host=".$_SESSION['serverdb'].";dbname=".$_SESSION['dbname'], $_SESSION['dbuser'], $_SESSION['dbpass']);
        // Controlar 3 array de parametros asociativo
        array_push($parametros,$this->aparam[0]['idparametroa']);
        array_push($parametros,$this->aparam[0]['idparametrob']);
        array_push($parametros,$this->aparam[0]['idparametroc']);
        
        for($i=0;$i<3;$i++)
        {
            if($parametros[$i] > 0)
            {
                 // Acotar fechas dependiendo campo tipolectura
                 $sdate = $this->getfecha();
                 $sql = "select max(idlectura) AS idmax from lectura_parametros where idparametro=".$parametros[$i];
                 $result = $link->query($sql);
                 $max = $result->fetchAll(PDO::FETCH_ASSOC);
                 // Si hay filas añadir al array
                 if(count($max)>0) 
                 {
                     array_push($amax,$max[0]['idmax']);
                 }   
            }
        }
        // Retornar el array
        return $amax;
    }
    public function loadarrayparam($param) 
    {
        // La funcion carga en array de datos el parametro_server introducido.
        // Dependiendo del parametro el filtro de fechas sería diario, mensual o anual.
        $sselect = "";
        // Acotar fechas dependiendo campo tipolectura
        $sdate = $this->getfecha();
        switch ($this->aparam[0]['tipolectura']) {
        case 2:
            // Mes actual. Coger los 2 días últimos calculados
            $sselect ="SELECT IDPARAMETRO,NOMBREP,COLOR,PREFIJO,POSDECIMAL,VALOR,DIA AS HORA,ESTLINK,DATE_FORMAT(flectura,'%Y-%m-%d') AS FLECTURA FROM vgrafica_dias ";
            $sselect.="WHERE idparametro = ".$param;
            $sselect.= $sdate;
            $sselect .=" union ";
            $sselect .="SELECT IDPARAMETRO,NOMBREP,COLOR,PREFIJO,POSDECIMAL,SUM(VALOR) AS VALOR,DIA AS HORA,ESTLINK,DATE_FORMAT(flectura,'%Y-%m-%d') AS FLECTURA FROM vgrafica_horas ";
            $sselect.="WHERE idparametro = ".$param;
            $sselect .=" AND flectura > CURRENT_DATE() - INTERVAL 2 DAY";  
            $sselect .=" group by NOMBREP,DATE_FORMAT(flectura,'%Y-%m-%d') order by idparametro,flectura";
            break;
        case 3:
            // Año actual
            $sselect = "SELECT NOMBREP,COLOR,PREFIJO,POSDECIMAL,SUM(VALOR) AS VALOR,MES AS HORA,ESTLINK FROM vgrafica_dias ";
            $sselect.="WHERE idparametro = ".$param;
            $sselect.= $sdate;
            $sselect.=" GROUP BY idparametro,MES order by idparametro,CAST(flectura AS SIGNED)";
            break;
        default:
            $sselect = "SELECT NOMBREP,COLOR,PREFIJO,POSDECIMAL,VALOR,DATE_FORMAT(FLECTURA,'%H') AS HORA FROM vgrafica ";
            $sselect.="WHERE idparametro = ".$param;
            $sselect.=" AND DATE_FORMAT(FLECTURA,'%i') = '00'";
            $sselect.= $sdate;
            $sselect.=" order by idparametro,flectura";
        }
        // Tengo la select cargar el array en un Fetch assoc
        //echo $sselect;
        //return $sselect;
        $link = new PDO("mysql:host=".$_SESSION['serverdb'].";dbname=".$_SESSION['dbname'], $_SESSION['dbuser'], $_SESSION['dbpass']);
        $result = $link->query($sselect);
	return $result->fetchAll(PDO::FETCH_ASSOC);
    }
    public function loadstimate($param)
    {
        $sselect = "";
        // Acotar fechas dependiendo campo tipolectura
        $sdate = $this->getfecha();
        $sselect = "select p.idparametro,'Estimación' as NOMBREP,p.prefijonum,p.posdecimal,a.valorx as HORA,a.valory as VALOR "; 
        $sselect .=" from parametros_server p,admestimacion a ";
        $sselect .=" where p.idparametro= ".$param; 
        $sselect .=" and a.idparametro = p.idparametro ";
        $sselect .=" order by CAST(a.valorx AS SIGNED)";
        // Cargar datos estimados
        $link = new PDO("mysql:host=".$_SESSION['serverdb'].";dbname=".$_SESSION['dbname'], $_SESSION['dbuser'], $_SESSION['dbpass']);
        $result = $link->query($sselect);
        //echo $sselect;
	return $result->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function cargacomboparam($name,$idparam)
        {
            mysql_connect($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass']) or die ("No se puede establecer la conexion!!!!"); 
            mysql_select_db($_SESSION['dbname']) or die ("Imposible conectar a la base de datos!!!!"); //Selecionas tu base
            mysql_set_charset('utf8'); // Importante juego de caracteres a utilizar.
            
            $sql = "SELECT parametros_server.idparametro, parametros_server.parametro FROM parametros_server,server_instalacion";
            $sql.=" where server_instalacion.idserver = parametros_server.idserver " ;
            $sql.=" and server_instalacion.estado = 1 " ;
            $sql.=" and parametros_server.estado > 0 and parametros_server.tipo not like'%B%'" ;
            $sql.=" and parametros_server.nivel <= ".$_SESSION['nivel'];
            $sql.=" and parametros_server.idserver = ".$_SESSION['idserver'];
            $sql.=" order by parametros_server.parametro,parametros_server.estado ";
            
            // Pintar combo
            echo '<select name="'.$name.'" style="width: 115px;">'; 
            // No definido
            $resparametros = mysql_query($sql);
            $vcombo = "<option value=-1";
            if($idparam==-1) {$vcombo = $vcombo. " SELECTED ";}
            $vcombo = $vcombo.">";
            $vcombo = $vcombo."Ninguno</option>"; 
            echo $vcombo;
            // Estimado. Si es distino del compo de administración de estimaciones
            if ($name <> 'comboestimado') {
                $vcombo = "<option value=0";
                if($idparam==0) {$vcombo = $vcombo. " SELECTED ";}
                $vcombo = $vcombo.">";
                $vcombo = $vcombo."Estimado</option>"; 
                echo $vcombo;
            }
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
    
    public function updateresumen()
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
        for($i =0;$i <count($_POST['idresumen']);$i++)
        {
            $stmt = $mysqli->prepare("UPDATE admresumen SET divid=?,"
                    . "tipolectura=?,"
                    . "idparametroa=?,"
                    . "idparametrob=?,"
                    . "idparametroc=?"
                    . " WHERE idresumen = ?");

            // Vincular variables
            if (!$stmt->bind_param("siiiii",
                    $_POST['divid'][$i],
                    $_POST['tipolectura'][$i],
                    $_POST['idparametroa'][$i],
                    $_POST['idparametrob'][$i],
                    $_POST['idparametroc'][$i],
                    $_POST['idresumen'][$i])) {
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
    public function deleteresumen()
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
        $stmt = $mysqli->prepare("DELETE FROM admresumen WHERE idresumen = ?");
        // Vincular variables
        if (!$stmt->bind_param("i", $_POST['idresumen'])) {
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
    public function insertresumen()
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
        // Cargar el mín parametro
        $sql = "SELECT min(parametros_server.idparametro) AS MINPARAMETRO FROM parametros_server,server_instalacion";
        $sql.=" where server_instalacion.idserver = parametros_server.idserver " ;
        $sql.=" and server_instalacion.estado = 1 " ;
        $sql.=" and parametros_server.estado > 0 and parametros_server.tipo not like'%B%'" ;
        $sql.=" and parametros_server.nivel <= ".$_SESSION['nivel'];
        $sql.=" and parametros_server.idserver = ".$_SESSION['idserver'];
        $sql.=" order by parametros_server.parametro,parametros_server.estado ";
        
        $resparametros = mysql_query($sql);
        $row = mysql_fetch_array($resparametros);
        
        $sinsert = "INSERT INTO admresumen(divid,idparametroa) VALUES ('Definir sección',".$row["MINPARAMETRO"].")";
       
        if ($mysqli->query($sinsert) === TRUE)
        {
            //echo "Nuevo bitname creado.";
        } else {
            echo "Falló la inserción: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $mysqli->close();
        return 0;
    }
 
   public function updateestimacion()
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
        for($i =0;$i <count($_POST['idestimacion']);$i++)
        {
            $stmt = $mysqli->prepare("UPDATE admestimacion SET valorx=?,"
                    . "valory=?,"
                    . "idusuario=?,"
                    . "operacion=?,"
                    . "poralert=?"
                    . " WHERE idestimacion = ?");

            // Vincular variables
            if (!$stmt->bind_param("iiisii",
                    $_POST['valorx'][$i],
                    $_POST['valory'][$i],
                    $_POST['idusuario'][$i],
                    $_POST['operacion'][$i],
                    $_POST['poralert'][$i],
                    $_POST['idestimacion'][$i])) {
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
    public function deleteestimacion()
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
        $stmt = $mysqli->prepare("DELETE FROM admestimacion WHERE idestimacion = ?");
        // Vincular variables
        if (!$stmt->bind_param("i", $_POST['idestimaciondelete'])) {
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
    public function insertestimacion($idparametro)
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
        // Se tiene q pasar el idparametro
        $sinsert = "INSERT INTO admestimacion(idparametro,valorx) VALUES (".$idparametro.",-1)";
       
        if ($mysqli->query($sinsert) === TRUE)
        {
            //echo "Nuevo bitname creado.";
        } else {
            echo "Falló la inserción: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $mysqli->close();
        return 0;
    }
    
    private function getfecha() 
    {
        // La función con la tipo lectura, returna un rango de fechas
        $sqldate = "";
        $vfecha =date('Y-m-d'); 
        // Fecha pruebas /////////////////////////////////////////////////////////////////////////////////////////////// <--
        //$vfecha = "2016-08-15";
        switch ($this->aparam[0]['tipolectura']) {
        case 2:
            // Fecha del mes actual
            $vmes = date('m',strtotime($vfecha));
            $vyear = date('Y',strtotime($vfecha));
            // Formato de fecha estandar yyyy-mm-dd HH:mm:ss
            $vfecha = "01-".$vmes."-".$vyear;
            $vdesde = date("Y-m-d H:i:s", strtotime('+0 hours', strtotime($vfecha)));
            $vhasta = date("Y-m-d H:i:s", strtotime('+1 month',strtotime($vfecha)));
            break;
        case 3:
            // Ejecicio actual
            $vyear = date('Y',strtotime($vfecha));
            // Formato de fecha estandar yyyy-mm-dd HH:mm:ss
            $vfecha = "01-01-".$vyear;
            //echo $vfecha;
            $vdesde = date("Y-m-d H:i:s", strtotime('+0 hours', strtotime($vfecha)));
            $vhasta = date("Y-m-d H:i:s", strtotime('+1 year',strtotime($vfecha)));
            break;
        default:
            // fecha del día actual 1
            $vdesde = date("Y-m-d H:i:s", strtotime('+0 hours', strtotime($vfecha)));
            $vhasta = date("Y-m-d H:i:s", strtotime('+1 days',strtotime($vfecha)));
        }
        // Retornar rango de fechas
        $sqldate = " AND flectura >= '".date($vdesde)."'";
        $sqldate.=" AND flectura < '".date($vhasta)."'";
        // Retornar fechas
        return $sqldate;
    }
    
    public function posdecimal($valor,$posiciones) {
    if ($posiciones > 0) {
        $div = 1;
        for ($x = 0; $x < $posiciones; $x++) {
            $div = $div * 10;
        }
        // Resultado
        $valor = $valor/$div;
        $valor = round($valor, $posiciones);
        //$valor = $valor/$div;
    }
    // Retorna valor sin tocar o con la division
    return $valor;           
}
}