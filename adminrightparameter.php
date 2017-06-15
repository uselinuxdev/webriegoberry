<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <!Funciones post>
        <?php
        // Crear clase de para llamada a funciones genericas
        // Control post
        $ClassParam = new ParameterClass();
        // Update logic  
        if(isset($_POST['update_p']))
        {   
            $ClassParam->updateparameter(); 
        }
        if(isset($_POST['insert_p']))
        {
            $ClassParam->insertparameter(); 
        }
        if(isset($_POST['delete_p']))
        {
            $ClassParam->deleteparameter(); 
        }
        if(isset($_POST['update_bitname']))
        {
            $ClassParam->updatebit(); 
        }
        // Delete logic
        if(isset($_POST['delete_bit']))
        {
            $ClassParam->deletebit(); 
        }
        // Insert si esta un parametro seleccionado en combo
        if(isset($_POST['insert_bitname']))
        {
            if(!empty($_POST['cbvalorbit']))
            {
                $ClassParam->insertbit(); 
            }else{
                echo "Debe seleccionar algún parámetro del desplegable.";
            }
        }
        ?>
    </head>
    <body>
        <?php
            // Form de parametros
            include 'adminrightparameterup.php';
            // Form de binarios
            include 'adminrightparameterdown.php';
        ?>
    </body>
</html>
