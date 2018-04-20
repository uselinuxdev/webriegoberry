<?php
    /* 
    Esta pagina es llamada desde los formularios para realizar la exportacion.
    Los datos necesarios se obtienen de las variables de sesion
     */
    // Controlar que exista sesion iniciada
    require('adminsession.php');
    // Exportación CSV
    require('exportcsv.php');
    if (CheckLogin() == false)
    {
        header("Location: login.php");
    }
    $_SESSION['pag'] = "adminExpCsv.php";
    // Borrar fichero temporal en servidor.
    //borrarcsv();
    // Limpiar path raspberry de csv y xls
    //cleanpathcsv();
    // Exportar a fichero csv
    
    // Controlar si es Csv o XLSX. CSV para ficheros muy grandes.
    if ($_SESSION['escsv'] == 1) {
        exportMysqlToCsv('ficherocsv');
    }else {
         // Exporta a excel con PHPExcel
        exportMysqlToexcel('ficheroxlsx');
    }
    

    // Abrir fichero csv. Realiza el download.
    opencsv();
 ?>

