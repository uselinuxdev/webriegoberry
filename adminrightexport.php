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
            $pdate=$_POST['fcalc'][0];           
            //echo "Gentcalc_exp funtion parm called.".$pdate;
            $ClassExp->GenCalcExp($pdate,false); 
            $ClassExp->openexpfile($pdate);
        } 
        if(isset($_POST['upload_exp']))
        {
            //$pdate=date("Y-m-d", strtotime( '-1 days' ) ); 
            $ftptype=$_POST['copytype'][0];   
            $pdate=$_POST['fcalc'][0];           
            //echo "Gentcalc_exp funtion parm called.".$pdate;
            $ClassExp->expupload($ftptype,$pdate); 
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
