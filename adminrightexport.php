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
        // Calc funtions
        if(isset($_POST['gentcalc_exp']))
        {
            //$pdate=date("Y-m-d", strtotime( '-1 days' ) ); 
            $pdate='2019-09-03';           
            //echo "Gentcalc_exp funtion parm called.".$pdate;
            $ClassExp->GenCalcExp($pdate); 
            $ClassExp->openexpfile($pdate);
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
