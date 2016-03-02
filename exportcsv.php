<?php
// echo $_SESSION['ssql'];
function posdecimal($valor,$posiciones) {
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
function exportMysqlToexcel($filename = 'export') 
{
    // Se desactiva porque no se usa.
    //////include_once("PHPExcel/Classes/PHPExcel.php");
    
     // Creamos la conexión
    //Primero hacemos las conexiones
    $hostdb = $_SESSION['serverdb'];  // MySQl host
    $userdb = $_SESSION['dbuser'];  // MySQL username
    $passdb = $_SESSION['dbpass'];  // MySQL password
    $namedb = $_SESSION['dbname'];  // MySQL database name
    mysql_set_charset('utf8'); // Importante juego de caracteres a utilizar.
    $conn = mysql_connect($hostdb,$userdb,$passdb) or die("Error en conexión a B.D.");
    mysql_select_db($namedb,$conn);
    
    // La sentencia pasada por sesion.
    $sql = mysql_query($_SESSION['ssql']) or die ("Error en SQL:".$_SESSION['ssql'].". ".mysql_error()); 
    
    if ($sql) {
        // Cargar array por índice, para obtener el primer valor
        $row = mysql_fetch_row($sql);
        // Fichero a generar
        $filename=$row[0];
        $filename= $filename.'_'.date("Y-m-d");
        $filename= 'export/uploads/'.$filename.'.xlsx';

        /// Generamos objeto excel
        $pexcel = new PHPExcel(); 
        
        $pexcel->getActiveSheet()->setTitle("Valores exportados instalación");
        $pexcel->getProperties()->setCreator("Riegosolar");
        $pexcel->getProperties()->setTitle("Valores exportados instalación"); 
        
        //$pexcel->setActiveSheetIndex(0)->setCellValue("A1","Valores Exportados"); 
        // Recorrer el excel
        $icolum = 0;
        $ifilas = 1;
        // Crear array de columnas
        $aletras = range("A","Z");
        // Volver a primera fila para obtener sus valores
        mysql_data_seek($sql,0);
        $row = mysql_fetch_assoc($sql);
        // Pintamos la primera fila de nombres de columnas
        foreach($row as $name => $value)
        {
            //$pexcel->setActiveSheetIndex(0)->setCellValue($aletras($icolum).$ifilas, $name);
            // Controlar que no sea POSDECIMAL
            if ($name != 'POSDECIMAL') {
                $colum = $aletras[$icolum].$ifilas;
                $pexcel->setActiveSheetIndex(0)->setCellValue($colum, $name); 
                $icolum = $icolum +1; 
            }
        }    
        // Volver a primera fila para obtener sus valores
        mysql_data_seek($sql,0);

        // Detalles de filas, pintar valores
        while($row = mysql_fetch_assoc($sql))
        {
            // Realizar la división del valor
            $row["VALOR"] = posdecimal($row["VALOR"],$row["POSDECIMAL"]);
            // Recorrer el array
            $icolum = 0;
            $ifilas = $ifilas + 1 ;
            foreach($row as $name => $value)
            {
                // Controlar que no sea POSDECIMAL
                if ($name != 'POSDECIMAL') {
                    $colum = $aletras[$icolum].$ifilas;
                    $pexcel->setActiveSheetIndex(0)->setCellValue($colum, $value); 
                    $icolum = $icolum +1;
                }
            }


        }

        // Valor del fichero generado
        $objWriter = PHPExcel_IOFactory::createWriter($pexcel, 'Excel2007'); 
        $objWriter->save($filename); 

        $_SESSION['ficherocsv'] = $filename;
    }
}

function exportMysqlToCsv($filename = 'export')
{
    // Creamos la conexión
    //Primero hacemos las conexiones
    $hostdb = $_SESSION['serverdb'];  // MySQl host
    $userdb = $_SESSION['dbuser'];  // MySQL username
    $passdb = $_SESSION['dbpass'];  // MySQL password
    $namedb = $_SESSION['dbname'];  // MySQL database name
    mysql_set_charset('utf8'); // Importante juego de caracteres a utilizar.
    $conn = mysql_connect($hostdb,$userdb,$passdb) or die("Error en conexión a B.D.");
    mysql_select_db($namedb,$conn);
    
    
    // La sentencia pasada por sesion.
    $sql = mysql_query($_SESSION['ssql']) or die ("Error en SQL:".$_SESSION['ssql'].". ".mysql_error()); 
    
    if ($sql) {
        // Cargar array por índice, para obtener el primer valor
        $row = mysql_fetch_row($sql);
        // echo $row;
        // Crear puntero a ficheros uploads (relativo al php
        //$filename= 'export/uploads/'.$filename.'_'.strtotime("now").'.csv';    

        // El nombre del fichero es el parámetro1 + fecha
        $filename=$row[0];
        $filename= $filename.'_'.date("Y-m-d");
        $filename= 'export/uploads/'.$filename.'.csv';
        
        // Poner el puntero en la primera fila y cargar array asociativo.
        mysql_data_seek($sql,0);
        $row = mysql_fetch_assoc($sql);

        // Variables del CSV
        $separador = "";
        $coma = "";

        // Recorrer el array
        // Formato de salida CSV: "campo1","campo2".... Cabeceras
        foreach($row as $name => $value)
        {
            // Controlar que no sea POSDECIMAL
            if ($name != 'POSDECIMAL') {
            //echo $name."=".$value. '<br />';
                // Componer filas a grabar
                $separador .= $coma . '' .str_replace('', '""', strtoupper($name));
                $coma = ";";
            }
        }
        $separador .= "\n";


        // Puntero a fichero
        $fp = fopen($filename, 'w');
        // Probar.
        //echo $separador;
        // Grabar a fichero
        fputs($fp,$separador);


        // Volver a primera fila para obtener sus valores
        mysql_data_seek($sql,0);
        // Detalles de filas
        while($row = mysql_fetch_assoc($sql))
        {
            // Variables del CSV
            $separador = "";
            $coma = "";
            // Realizar la división del valor
            $row["VALOR"] = posdecimal($row["VALOR"],$row["POSDECIMAL"]);
            // Recorrer el array
            // Formato de salida CSV: "campo1","campo2".... Filas
            foreach($row as $name => $value)
            {
                //echo $name."=".$value. '<br />';
                // Controlar que no sea POSDECIMAL
                if ($name != 'POSDECIMAL') {
                    // Componer filas a grabar
                    $separador .= $coma . '' .str_replace('', '""', $value);
                    $coma = ";";
                }
            }
            $separador .= "\n";
            //echo $separador;

            // Grabar a fichero
            fputs($fp,$separador);

        }
        // Cerrar fichero.
        fclose($fp);

        $_SESSION['ficherocsv'] = $filename; 
    }
 
}

function opencsv() {
    if (file_exists($_SESSION['ficherocsv'])) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($_SESSION['ficherocsv']).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($_SESSION['ficherocsv']));
        readfile($_SESSION['ficherocsv']);

    }
}

function borrarcsv() {
    if (file_exists($_SESSION['ficherocsv'])) {
        if(unlink($_SESSION['ficherocsv'])) {
            //echo "Fichero ".$_SESSION['ficherocsv']." borrado ";
        }
    }
    // Resetea valor
    $_SESSION['ficherocsv'] = "";
}

function cleanpathcsv() {
    $files = new Files();
    $results = $files->delete('export/uploads/*.csv');
    $results = $files->delete('export/uploads/*.xls');
}

?>