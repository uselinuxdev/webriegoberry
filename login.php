<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <link rel="stylesheet" type="text/css" href="css/riegoestilos.css">
    <?php
    require('adminsession.php');
    // creamos la sesion y comprobamos si el user ha dado al boton del form.
    session_start();
    if($_POST['btenviado'])
    {
        //Si se ha dado al boton metemos variables
        $vuser=$_POST['user'];
        $vpass=$_POST['passuser'];
        checkuserdb($vuser, $vpass);
    }
    ?>
    <head>
        <meta charset="UTF-8">
        <title>Ventana de entrada Riegosolar.</title>
    </head>
    <body>
    <div id="contenedor">
    <div id="cabecera">
         <div id="imgcabecera">
             <img src="imagenes/RIEGOSOLAR_Blanco.png" alt="Logo"/>
         </div>
    </div>
    <div id="imputlogin">
        <form id="login" action="?" method="post">
            <table border="0">
                <tbody>
                    <tr>
                        <br>
                        <td><p>Usuario: </p>
                        <input type="text" name="user"> <br> <br>
                        <p>Contraseña: </p>
                        <input type="password" name="passuser">
                        </td>
                    </tr>
                    <tr>
                        <td><input type="submit" value="Validar" name="btenviado">
                        <br>
                        <p>
                        <?php
                            echo $_SESSION['textsesion'];
                            if (CheckLogin() == true)
                            {  
                                header("Location: riegoresumen.php");
                            }
                        ?>
                        </p>
                        </td>
                    </tr>
                </tbody>
            </table>
            </form>
            <div id="divriegotec">
                <img src="imagenes/riego-tec.jpg" alt="Logpie"/>
            </div>
    </div>
    </div> <!-- Fin del cuperpo -->
    <div id="pie">
    </div>
    </body>
</html>
