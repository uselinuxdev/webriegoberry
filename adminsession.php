<?php

/* 
Modulo de gestion de sesion.
Tienes las variables de sesion como B.D. usuario, pass, tiempo logon ...
 */
// Variables generales

function checkuserdb($vuser,$vpass,$vport)
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
    // Puerto por defecto 3306
    $_SESSION['dbport'] = 3306;
    if(!empty($vport))
    {
        $_SESSION['dbport'] =$vport;
    }
    //Primero hacemos las conexiones
    $_SESSION['serverdb'] = 'localhost';
    $_SESSION['dbuser'] = 'riegosql';
    $_SESSION['dbpass'] = 'riegoprod15';
    $_SESSION['dbname'] = 'riegosolar';
    $_SESSION['minsesion'] = 0;
    //Variable de sesion de selección de tabs
    $_SESSION['stabindex'] = 0;
    //do something for php7.1 and above.
    //mysqli_connect(host, username, password, dbname, port, socket)
    $cndb=mysqli_connect($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
    if (!$cndb) {
        echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
        echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
        echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
        exit;
    }
    mysqli_set_charset($cndb, "utf8");
    
    // En la columna password se ha grabado el valor con la funcion MD5. update campo=MD5('valor');
    $vpass=md5($vpass); // Encrypted Password
    
    // Comprobar la tabla de usuarios.
    $sql = "select idusuario,usuario,nivel,idserver from usuarios";
    $sql.= " where usuario ='".$vuser."'";
    $sql.= " and password ='".$vpass."'";
    //echo $sql;   
    $consulta = mysqli_query($cndb, $sql) or die( mysqli_error($cndb));

    if ($consulta) {
        // Datos de la primera fila
        // Variable de tiempo de sesion.
        $row = mysqli_fetch_array($consulta,MYSQLI_ASSOC); 
        $_SESSION['tlogon'] = time();
        $_SESSION['minsesion'] = 10;
        $_SESSION['usuario'] = $row['usuario'];
        $_SESSION['nivel'] = $row['nivel'];
        $_SESSION['idserver'] = $row['idserver'];
        $_SESSION['textsesion'] = 'Conexión establecida '.$_SESSION['tlogon'];
        //echo $_SESSION['usuario'];
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
          echo $_SESSION['textsesion'];
          return false;
          // session timed out
      }
      // Añadimos tiempo a la sesion
      $_SESSION['tlogon'] = time();
      return true;
}