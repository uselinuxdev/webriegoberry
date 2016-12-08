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
            $sselect ="SELECT IDPARAMETRO,NOMBREP,PREFIJO,POSDECIMAL,VALOR,DIA AS HORA,ESTLINK,DATE_FORMAT(flectura,'%Y-%m-%d') AS FLECTURA FROM vgrafica_dias ";
            $sselect.="WHERE idparametro = ".$param;
            $sselect.= $sdate;
            $sselect .=" union ";
            $sselect .="SELECT IDPARAMETRO,NOMBREP,PREFIJO,POSDECIMAL,SUM(VALOR) AS VALOR,DIA AS HORA,ESTLINK,DATE_FORMAT(flectura,'%Y-%m-%d') AS FLECTURA FROM vgrafica_horas ";
            $sselect.="WHERE idparametro = ".$param;
            $sselect .=" AND flectura > CURRENT_DATE() - INTERVAL 2 DAY";  
            $sselect .=" group by NOMBREP,DATE_FORMAT(flectura,'%Y-%m-%d') order by flectura";
            break;
        case 3:
            // Año actual
            $sselect = "SELECT NOMBREP,PREFIJO,POSDECIMAL,SUM(VALOR) AS VALOR,MES AS FLECTURA,ESTLINK FROM vgrafica_dias ";
            $sselect.="WHERE idparametro = ".$param;
            $sselect.= $sdate;
            $sselect.=" GROUP BY idparametro,MES order by flectura,idparametro";
            break;
        default:
            $sselect = "SELECT NOMBREP,PREFIJO,POSDECIMAL,VALOR,DATE_FORMAT(FLECTURA,'%H') AS HORA FROM vgrafica ";
            $sselect.="WHERE idparametro = ".$param;
            $sselect.=" AND DATE_FORMAT(FLECTURA,'%i') = '00'";
            $sselect.= $sdate;
        }
        // Tengo la select cargar el array en un Fetch assoc
        //return $sselect;
        $link = new PDO("mysql:host=".$_SESSION['serverdb'].";dbname=".$_SESSION['dbname'], $_SESSION['dbuser'], $_SESSION['dbpass']);
        $result = $link->query($sselect);
	return $result->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getfecha() 
    {
        // La función con la tipo lectura, returna un rango de fechas
        $sqldate = "";
        $vfecha =date('Y-m-d'); 
        // Fecha pruebas /////////////////////////////////////////////////////////////////////////////////////////////// <--
        $vfecha = "08-08-2015";
        switch ($this->aparam[0]['tipolectura']) {
        case 2:
            // Fecha del mes actual
            $vmes = date('m');
            $vyear = date('Y');
            // Formato de fecha estandar yyyy-mm-dd HH:mm:ss
            $vfecha = "01-".$vmes."-".$vyear;
            $vdesde = date("Y-m-d H:i:s", strtotime('+0 hours', strtotime($vfecha)));
            $vhasta = date("Y-m-d H:i:s", strtotime('+1 month',strtotime($vfecha)));
            break;
        case 3:
            // Ejecicio actual
            $vyear = date('Y');
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