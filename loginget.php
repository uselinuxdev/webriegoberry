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
        echo 'La password viene en Code64:'.addslashes(strip_tags($_GET["pass"]));
        echo 'La password viene en decode Code64:'.base64_decode(addslashes(strip_tags($_GET["pass"])));
        echo 'El usuario es:'.$vuser;
        IF (checkuserdb($vuser, base64_decode($vpass)) == 1)
        {
            echo 'checkuserdb ==1';
            if (CheckLogin() == true)
            {
              echo 'CheckLogin OK';
              // header("Location: riegoresumen.php"); 
            }
        }
        // Mandar a login si no se han podido obtener los datos con get
        header("Location: login.php");
        ?>
    </body>
</html>
