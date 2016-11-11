<!DOCTYPE html>
<!--
Php es llamado desde la web corporativa pasandole 2 parámetros:
- Usuario.
- Password encriptada en MD5.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Login web parametros GET</title>
        <?php
            require('adminsession.php');
            // Lectura de parametros desde get. Pasar en base64 permite codec y decodec.
            $vuser = addslashes(strip_tags($_GET["usuario"]));
            $vpass = addslashes(strip_tags($_GET["pass"]));
        ?>
    </head>
    <body>
        <?php
        // Pruebas
        //echo 'La password viene en Code64:'.addslashes(strip_tags($_GET["pass"]));
        IF (checkuserdb($vuser, base64_decode($vpass)) == 1)
        {
            //echo 'Validación checkuserbd ==1.';
            if (CheckLogin())
            {
                //echo 'Validación check login correcta.';
                header("Location: riegoresumen.php"); 
            } else {
                // Mandar a login si no se han podido obtener los datos con get
                header("Location: login.php");               
            }
        } else {
            // Mandar a login si no se han podido obtener los datos con get
            header("Location: login.php");
        }

        ?>
    </body>
</html>
