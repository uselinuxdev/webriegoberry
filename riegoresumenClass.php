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
        $strQuery = "SELECT idparametroa,idparametrob
                    FROM admresumen
                    where divid ='".$this->divid."'";
        // Execute the query, or else return the error message.
     	$link = new PDO("mysql:host=".$_SESSION['serverdb'].";dbname=".$_SESSION['dbname'], $_SESSION['dbuser'], $_SESSION['dbpass']);
	$result = $link->query($strQuery);
	$this->aparam= $result->fetchAll(PDO::FETCH_ASSOC);
        // Cargar la configuracion de los parametros
        $this->loadconfigparam($this->aparam);
    }
    private function loadconfigparam($param) 
    {
        // La funcion controla posibles valores: -1 no definido, 0 estimado, > 0 cargar valor.
        // Dependiendo del parametro el filtro de fechas sería diario, mensual o anual.
        return 0;
    }
    private function datosgraf()
    {
        // La función se encarga de obtener los datos de un parametro y el filtro de fechas.
    }
}