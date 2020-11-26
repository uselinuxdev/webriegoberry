<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <!Funciones post>
        <?php
        // Crear clase de para llamada a funciones genericas
        // Control post
        $ClassExp = new ExportClass();
        // Update logic
        if(isset($_POST['update_exp']))
        {   
            $ClassExp->updateexport(); 
        }
        if(isset($_POST['gentcalc_exp']))
        {   
            ///////$ClassExp->updateexport(); 
        }
        if(isset($_POST['upload_exp']))
        {   
            /////$ClassExp->updateexport(); 
        }
        if(isset($_POST['update_parmexp']))
        {    
            $ClassExp->updateexportparm(); 
        }
        if(isset($_POST['insert_parmexp']))
        {
            $ClassExp->insertexportparm(); 
        }
        if(isset($_POST['delete_parmexp']))
        {
            //echo "Delete funtion parm called.".$_POST['idparmdelete'];
            $ClassExp->deleteparameter(); 
        }
        ?>
    </head>
    <body>
        <?php
            // Form de parametros
            include 'adminrightexportup.php';
            // Form de binarios
            include 'adminrigthexportdown.php';
        ?>
    </body>
</html>
