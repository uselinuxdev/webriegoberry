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
    
    
    private function getfecha() 
    {
        // La función con la tipo lectura, returna un rango de fechas
        $sqldate = "";
        $vfecha =date('Y-m-d'); 
        // Fecha pruebas /////////////////////////////////////////////////////////////////////////////////////////////// <--
        $vfecha = "2016-08-15";
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