<?php

/* 
Modulo de gestion de sesion.
Tienes las variables de sesion como B.D. usuario, pass, tiempo logon ...
 */
// Variables generales

function checkuserdb($vuser,$vpass)
{
    // creamos la sesion y comprobamos si el user ha dado al boton del form.
    session_start();
    $_SESSION['textsesion'] = "Sesión no iniciada.";
    //Validar las variables pasadas.
    if(!empty($vuser))
    {
        if(!empty($vpass))
        {
            $_SESSION['textsesion'] = 'Se han introducido el usuario y contraseña.';
        }else {
            $_SESSION['textsesion'] = 'No ha introducido la password.';
            return -1;          
        }
    }else
    {
        $_SESSION['textsesion'] = 'No ha introducido un usuario.';
        return -1;
    }
    //Primero hacemos las conexiones
    $_SESSION['serverdb'] = 'localhost';
    $_SESSION['dbuser'] = 'riegosql';
    $_SESSION['dbpass'] = 'riegoprod15';
    $_SESSION['dbname'] = 'riegosolar';
    $_SESSION['minsesion'] = 0;
    //Variable de sesion de selección de tabs
    $_SESSION['stabindex'] = 0;
    $ver = (float)phpversion();
    if ($ver > 7.0) {
        //do something for php7.1 and above.
        $cndb=mysqli_connect($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname']) or die ("No se puede establecer la conexion!!!!");
        mysqli_set_charset($cndb, 'utf8');
    } else {
        //do something for php5.6 or lower.
        $cndb=mysql_connect($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass']) or die ("No se puede establecer la conexion!!!!");  // dep php 7
        mysql_select_db($_SESSION['dbname'],$cndb) or die ("Imposible conectar a la base de datos!!!!"); //Selecionas tu base
        mysql_set_charset('utf8'); // Importante juego de caracteres a utilizar.
    }
    
    // En la columna password se ha grabado el valor con la funcion MD5. update campo=MD5('valor');
    $vpass=md5($vpass); // Encrypted Password
    
    // Comprobar la tabla de usuarios.
    $sql = "select idusuario,usuario,nivel,idserver from usuarios";
    $sql.= " where usuario ='".$vuser."'";
    $sql.= " and password ='".$vpass."'";
    //echo $sql;
    // Execute the query, or else return the error message.
    $ifilas = 0;
    if ($ver > 7.0) {
        mysqli_query($cndb,$sql);
        $ifilas = mysqli_num_rows($cndb,$consulta);
    } else {
        $consulta = mysql_query($sql);
        $ifilas = mysql_num_rows($consulta);
    }
    
    if (ifilas) {
        // Datos de la primera fila
        $row = mysql_fetch_array($consulta);
        // Variable de tiempo de sesion.
        $_SESSION['tlogon'] = time();
        $_SESSION['minsesion'] = 10;
        $_SESSION['usuario'] = $row['usuario'];
        $_SESSION['nivel'] = $row['nivel'];
        $_SESSION['idserver'] = $row['idserver'];
        $_SESSION['textsesion'] = 'Conexión establecida '.$_SESSION['tlogon'];
        return 1;
    }else {
        $_SESSION['textsesion'] = 'Los datos introducidos no corresponden con ninguna instalación.';
        //$_SESSION['textsesion'] = $sql;
        return -1;
    }
    
}
 
function CheckLogin()
{
     session_start();
     
     if(empty($_SESSION['usuario']))
     {
        $_SESSION['textsesion'] = "Sesión no iniciada.";
         return false;
     }
      if ($_SESSION['tlogon'] + $_SESSION['minsesion'] * 60 < time()) {
          $_SESSION['textsesion'] = "Por razones de seguridad su sesión ha espirado, vuelva a ingresar sus datos en el sistema.";
          return false;
          // session timed out
      }
      // Añadimos tiempo a la sesion
      $_SESSION['tlogon'] = time();
     return true;
}