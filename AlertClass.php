<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AlertClass
 *
 * @author Administrador
 */
/* Use PHPMailer lib to send mails */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require dirname(__FILE__).'/PHPMailer/src/Exception.php';
require dirname(__FILE__).'/PHPMailer/src/PHPMailer.php';
require dirname(__FILE__).'/PHPMailer/src/SMTP.php';
class AlertClass {
    // Actualizar Usuarios
    public function updatealert()
    {
        // Control post
        $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
        if ($mysqli->connect_errno)
        {
            echo $mysqli->host_info."\n";
            return -1;
        }
        // Importante juego de caracteres
        if (!mysqli_set_charset($mysqli, "utf8")) {
            printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($mysqli));
            exit();
        }
        for($i=0;$i <count($_POST['idalert']);$i++)
        {
            // Controlar password si tiene datos se pinta
            // Preparar sentencia
            $stmt = $mysqli->prepare("UPDATE alertserver SET idparametro = ?,
                idusuario = ?, 
                estado = ?,  
                tipo = ?,
                operacion = ?,
                valor = ?,
                textalert = ?,
                nbit = ?,
                horaminbit = ?,
                horamaxbit = ?,
                iflag =?
                WHERE idalert = ?");
            // Bind variables
            $stmt->bind_param('iiiisisissii',
            $_POST['idparametro'][$i],
            $_POST['idusuario'][$i],
            $_POST['estado'][$i],
            $_POST['tipo'][$i],
            $_POST['operacion'][$i],
            $_POST['valor'][$i],
            $_POST['textalert'][$i],
            $_POST['nbit'][$i],
            $_POST['horaminbit'][$i],
            $_POST['horamaxbit'][$i],
            $_POST['iflag'][$i],
            $_POST['idalert'][$i]);  // La alarma se restablece iflag = 0
            //
            ///print_r($_POST);    
            //echo "stmt bind_param correcto.";
            // Ejecutar
            $stmt->execute();
            // Finalizar
            $stmt->close();
            // Crear el evento
            $this->EventMailType($_POST['tipo'][$i]);
        }
    }
    public function deletealert()
    {
        $mysqli = mysqli_connect($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
        if ($mysqli->connect_errno)
        {
            echo $mysqli->host_info."\n";
            return -1;
        }
        // Importante juego de caracteres
        //printf("Conjunto de caracteres inicial: %s\n", mysqli_character_set_name($mysqli));
        if (!mysqli_set_charset($mysqli, "utf8")) {
            printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($mysqli));
            exit();
        }
        $stmt = $mysqli->prepare("DELETE FROM alertserver WHERE idalert = ?");
        // Vincular variables
        if (!$stmt->bind_param("i", $_POST['idalertdelete'])) {
            echo "Falló la vinculación de parámetros: (" . $stmt->errno . ") " . $stmt->error;
            return -1;
        }
        // Ejecutar
        if (!$stmt->execute()) {
            echo "Falló la ejecución del delete: (" . $stmt->errno . ") " . $stmt->error;
            return -1;
        }else{
            //echo "Se borró correctamente el ID:".$_POST['idbitdelete'];
        }
        $stmt->close();
        return 0;
    }
    public function insertalert()
    {
        $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
        if ($mysqli->connect_errno)
        {
            echo $mysqli->host_info."\n";
            return -1;
        }
        // Importante juego de caracteres
        //printf("Conjunto de caracteres inicial: %s\n", mysqli_character_set_name($mysqli));
        if (!mysqli_set_charset($mysqli, "utf8")) {
            printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($mysqli));
            exit();
        }
        // Control de alertas con iflag
        $sinsert = "INSERT INTO alertserver (idserver,horaminbit,horamaxbit,operacion,iflag) VALUES (".$_SESSION['idserver'].",'00:00:00','23:59:00','<',0)";
       
        if ($mysqli->query($sinsert) === TRUE)
        {
            //echo "Nuevo bitname creado.";
        } else {
            echo "Falló la inserción: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $mysqli->close();
        return 0;
    }
    // Actualiza flag de alarma. Retornando si se tiene que enviar mail o no
    private function ActFlagAlarm($idalert,$iflag)
    {
        // Conexiones
        $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
        if ($mysqli->connect_errno)
        {
            echo $mysqli->host_info."\n";
            exit();
        }
        // Importante juego de caracteres
        if (!mysqli_set_charset($mysqli, "utf8")) {
            printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($mysqli));
            exit();
        }
        // Control de alertas con iflag
        $supdate = "UPDATE alertserver set iflag=".$iflag." WHERE idalert=".$idalert;
        /////echo $supdate;
        if ($mysqli->query($supdate) === TRUE)
        {
            //echo "Nuevo bitname creado.";
        } else {
            echo "Falló en actualización de Flag: (" . $mysqli->errno . ") " . $mysqli->error;
            return -1;
        }
        $mysqli->close();
        return 1;        
    }
    public function cargacomboparam($name,$idparam)
        {
            $cndb=mysqli_connect($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
            if (!$cndb) {
                echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
                echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
                echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
                exit;
            }
            mysqli_set_charset($cndb, "utf8");
            
            $sql = "SELECT parametros_server.idparametro, parametros_server.parametro FROM parametros_server,server_instalacion";
            $sql.=" where server_instalacion.idserver = parametros_server.idserver " ;
            $sql.=" and server_instalacion.estado = 1 " ;
            $sql.=" and parametros_server.estado > 0 " ;
            $sql.=" and parametros_server.nivel <= ".$_SESSION['nivel'];
            $sql.=" and parametros_server.idserver = ".$_SESSION['idserver'];
            $sql.=" order by parametros_server.parametro,parametros_server.estado ";
            // Pintar combo
            echo '<select name="'.$name.'" style="width: 180px;">'; 
            echo "<option value=0>Seleccionar parámetro</option>"; 
            // No definido
            $resparametros = mysqli_query($cndb,$sql);  
            // Parametros de la select
            while($row = mysqli_fetch_array($resparametros,MYSQLI_ASSOC)) { //Iniciamos un ciclo para recorrer la variable $resparametros que tiene la consulta previamente hecha 
                $id = $row["idparametro"] ; //Asignamos el id del campo que quieras mostrar
                $vparametro = substr($row["parametro"],0,50); // Asignamos el nombre del campo que quieras mostrar
                //echo "<option value=".$id.">".$vparametro."</option>"; //Llenamos el option con su value que sera lo que se lleve al archivo registrar.php y que sera el id de tu campo y luego concatenamos tbn el nombre que se mostrara en el combo 
                $vcombo = "<option value=".$id;
                if($idparam==$id) {$vcombo = $vcombo. " SELECTED ";}
                $vcombo = $vcombo.">";
                $vcombo = $vcombo.$vparametro."</option>"; 
                echo $vcombo;
            } //Cerramos el ciclo 
            echo '</select>';
        }
    public function cargacombouser($name,$iduser)
        {
            $cndb=mysqli_connect($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
            if (!$cndb) {
                echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
                echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
                echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
                exit;
            }
            mysqli_set_charset($cndb, "utf8");
            
            $sql = "SELECT idusuario,usuario from usuarios ";
            $sql.=" where idserver = ".$_SESSION['idserver'];
            $sql.=" order by usuario ";
            
            // Pintar combo
            echo '<select name="'.$name.'" style="width: 90px;">'; 
            // No definido
            $resuser = mysqli_query($cndb,$sql); 
            // Parametros de la select
            while($row = mysqli_fetch_array($resuser,MYSQLI_ASSOC)) { //Iniciamos un ciclo para recorrer la variable $resparametros que tiene la consulta previamente hecha 
                $id = $row["idusuario"] ; //Asignamos el id del campo que quieras mostrar
                $vparametro = substr($row["usuario"],0,50); // Asignamos el nombre del campo que quieras mostrar
                //echo "<option value=".$id.">".$vparametro."</option>"; //Llenamos el option con su value que sera lo que se lleve al archivo registrar.php y que sera el id de tu campo y luego concatenamos tbn el nombre que se mostrara en el combo 
                $vcombo = "<option value=".$id;
                if($iduser==$id) {$vcombo = $vcombo. " SELECTED ";}
                $vcombo = $vcombo.">";
                $vcombo = $vcombo.$vparametro."</option>"; 
                echo $vcombo;
            } //Cerramos el ciclo 
            echo '</select>';
        }
        
    public function cargarcombobit($name,$idparametro,$posact)
    {
        $cndb=mysqli_connect($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
        if (!$cndb) {
            echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
            echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
            echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
            exit;
        }
        mysqli_set_charset($cndb, "utf8");
        $sql = "select posicion,nombrebit from parametros_bitname ";
        // Controlar si es para exportar
        $sql.=" WHERE idparametro = ".$idparametro;
        $sql.=" order by posicion";
        //echo $sql;
        //return 0;
        // Pintar combo
        echo '<select name="'.$name.'" style="width: 160px;">'; 
        // Sin valor
        $vcombo = "<option value=-1";
        // Controlar array
        if(!$posact) {$vcombo = $vcombo. " SELECTED ";}
        $vcombo = $vcombo.">";
        $vcombo = $vcombo." Selección de bit </option>"; 
        echo $vcombo;
        // No definido
        $resbit = mysqli_query($cndb,$sql); 
        // Parametros de la select
        ///echo "Posición actual:".$posact;
        while($row = mysqli_fetch_array($resbit,MYSQLI_ASSOC)) { //Iniciamos un ciclo para recorrer la variable $resparametros que tiene la consulta previamente hecha 
            $pos = $row["posicion"] ; //Asignamos el id del campo que quieras mostrar
            $vnombre = substr($row["nombrebit"],0,55); // Asignamos el nombre del campo que quieras mostrar
            //echo "<option value=".$id.">".$vparametro."</option>"; //Llenamos el option con su value que sera lo que se lleve al archivo registrar.php y que sera el id de tu campo y luego concatenamos tbn el nombre que se mostrara en el combo 
            $vcombo = "<option value=".$pos;
            // Controlar array
            if($posact===$pos) {$vcombo = $vcombo. " SELECTED ";}
            $vcombo = $vcombo.">";
            $vcombo = $vcombo.$vnombre."</option>"; 
            echo $vcombo;
        } //Cerramos el ciclo 
        echo '</select>';
    }
    // Test mail funtion
    public function checkMail()
    {
        $toemail = 'alarmas@riegosolar.net';
        //$toemail = 'info@riegosolar.net';
        $subject = "Alertas automáticas instalación ";
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        // More headers
        $headers .= 'From: <alarmas@riegosolar.net>' . "\r\n";
        //$headers .= 'Cc: myboss@example.com' . "\r\n";

        $message = 'Correo de pruebas';
        ///////////////////////////// mail($toemail,$subject,$message,$headers);
        
        $phpmailer=$this->newPHPMailer();
        // Always set content-type when sending HTML email
        $headers = 'From: <alertas@riegosolar.net>' . "\r\n";
        //$headers .= 'Cc: myboss@example.com' . "\r\n";
        $headers .= "MIME-Version: 1.0"."\r\n"."Content-type: text/html; charset=UTF-8"."\r\n";
        ///<meta charset="UTF-8">
        $message = '
        <html>
        <head>
        <title>'.$subject.'</title>
        </head>
        <body>
        <img src="http://www.riegosolar.net/wp-content/uploads/2016/01/RIEGOSOLAR_LOGO-3.png" alt="Logo RiegoSolar" style="background-color:#3A72A5;">
        <hr style="color: #3A72A5;" />';
        // Cabecera del mensaje
        $message .='<p/>Listado de alertas instalación<p/>';
        // Recorrer todas las lineas de detalle
        $message .='<img src="cid:imginstall" width="300" style="background-color:#3A72A5;>';
        $message .='<hr style="color: #3A72A5;" />';
        $message .='<table>
        <tr><td>Instalación: </td><td>'.$row["nombre"].'</td></tr>
        <tr><td>Titular: </td><td>'.$row["titular"].'</td></tr>
        <tr><td>Ubicación: </td><td>'.$row["ubicacion"].'</td></tr>
        <tr></tr><tr></tr>
        <tr><td>Fecha</td><td>Alerta</td><td>Periodo</td><td>Valor Real</td><td>Valor Esperado</td><td>Calculo</td><td>Estado Alarma</td></tr>';   
        $message .='<tr></tr></table>
        <hr style="color: #3A72A5;" />
        <p>Final de listado de alertas.</p>
        </body>
        </html>';				
        $phpmailer->AddAddress($toemail); // recipients email
        $phpmailer->Subject = $subject;	
        $phpmailer->Body .= $message;
        $phpmailer->CharSet = 'UTF-8';
        echo "phpmail sending.";
        $phpmailer->Send();
    
    }
    // Funcion publica, recorre las alertas por tipo: 0 última,1 diaria,2 mensual y 3 anual
    public function checkalertAll()
    {
        for($itipo=0;$itipo<4;$itipo++)
        {
            $this->checkalert($itipo);
        }
    }
    // La función será llamada por eventos por lo tanto no filtrar fechas.
    public function checkalert($itipo)
        {
         // Conexiones
          $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
          if ($mysqli->connect_errno)
          {
              echo $mysqli->host_info."\n";
              exit();
          }
          // Importante juego de caracteres
          if (!mysqli_set_charset($mysqli, "utf8")) {
              printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($mysqli));
              exit();
          } 
          // Array con datos a funcion mail
          $aalert = array();
          $icont=0;
          $sselect ="select * from alertserver where estado=1 and tipo=".$itipo." order by idparametro";
          //printf($sselect);
          $result = $mysqli->query($sselect) or exit("Codigo de error ({$mysqli->errno}): {$mysqli->error}");
          while($rowalert = mysqli_fetch_array($result)) {
              // Coger flag de alerta: Correo al dispararse, correo al finalizar.
              $aalert[$icont]['idalert'] = $rowalert['idalert'];
              $aalert[$icont]['iflag'] = $rowalert['iflag'];
              // El diario 1 sólo se calcula a las 8am
              // Mensual 2 sólo el primer día del mes a las 9am
              // Anual 3 sólo el primer día del año
              // Por cada parametero recuperar la select
              switch ($rowalert['tipo']) {
                  case 0:
                    $vnow = date("H:i:s"); 
                    //echo $vnow;
                    $aalert[$icont]['desctipo'] = 'Última';
                    $rowdb = $this->valorbd($rowalert['idparametro'],$rowalert['tipo']);
                    //print_r($rowdb);
                    break;  
                  case 1:
                    // Fecha del día anterior
                    $vnow = date("H:i:s"); // 17:16:18 
                    //echo $vnow;
                    $rowdb = $this->valorbd($rowalert['idparametro'],$rowalert['tipo']);
                    $aalert[$icont]['desctipo'] = 'Diaria';
                    break;
                  case 2:
                    // Fecha del mes anterior
                    $rowdb = $this->valorbd($rowalert['idparametro'],$rowalert['tipo']);
                    $aalert[$icont]['desctipo'] = 'Mensual';
                    break;
                  case 3:
                    // Fecha del año anterior
                    $rowdb = $this->valorbd($rowalert['idparametro'],$rowalert['tipo']);
                    $aalert[$icont]['desctipo'] = 'Anual';
                    break;
              }
              //print_r($aalert);
              // Calcular el valor descontando decimales  
              // Controlar q $rowvalor tiene filas. Procesar la filas encontradas
              if(!empty($rowdb))
              {
                  // Control del tipo de parametro
                  if(!empty($rowdb['WORDVALOR']))
                  {
                      // Se trata de un WORDVALOR coger el bit que se desea check
                      $valorcal = substr($rowdb['WORDVALOR'],$rowalert['nbit'],1);
                  } else
                  {
                      $valorcal = $this->posdecimal($rowdb['VALOR'],$rowdb['POSDECIMAL']);  
                  }
                  // Alarma activada
                  $balarm = false;
                  
                  $valorop=$rowalert['operacion'];

                  // Controlar el tipo de operación
                  if($valorop=='=')
                  {
                      $aalert[$icont][operacion]='Igual';
                      if($valorcal == $rowalert['valor'])
                      {
                          $balarm = true;
                      }
                  }
                  if($valorop=='!=')
                  {
                    $aalert[$icont][operacion]='Distinto';
                    if($valorcal != $rowalert['valor'])
                      {
                          $balarm = true;
                      }
                  }
                  
                  if($valorop=='>=')
                  {
                    $aalert[$icont]['operacion']='Mayor o igual';
                    if($valorcal >= $rowalert['valor'])
                    {
                        $balarm = true;
                    }
                  }
                  
                  if($valorop=='<=')
                  {
                    $aalert[$icont]['operacion']='Menor o igual';
                    if($valorcal <= $rowalert['valor'])
                    {
                        $balarm = true;
                    }
                  }
                  
                  if($valorop=='>')
                  {
                    $aalert[$icont][operacion]='Mayor';
                    if($valorcal > $rowalert['valor'])
                    {
                        $balarm = true;
                    }
                  }
                  if($valorop=='<')
                  {
                    $aalert[$icont][operacion]='Menor';
                    if($valorcal < $rowalert['valor'])
                    {
                        $balarm = true;
                    }
                  }
                  ////////////////////////////////////////////////////////////////////////////////////////////////////
                  //echo "Impresión aalert después de operacion:";
                  //print_r($aalert);
                  //return 0;
                  // Control de filtro de horas
                  if($rowalert['horaminbit'] > '00:00:00' or $rowalert['horamaxbit'] < '23:59:00')
                  {
                      if(date('H:i:s') < $rowalert['horaminbit'] or date('H:i:s') >$rowalert['horamaxbit'])
                      {
                          $balarm = false;
                      }
                  }
                  ////////////////////////////////////////////////////////////////////////////////////////////////////
                  // Control de iFlag
                  // La alarma ya estaba activada y sigue la alarma. No mandar mail
                  if($balarm == true)
                  {
                      //Control IFLAG
                      if($aalert[$icont]['iflag']==0)
                      {
                        $aalert[$icont]['iflag']=1;
                        echo "Alarma disparada. Valor anterior correcto.";
                      }
                      else
                      {
                        if($rowalert['tipo']<>1)
                        {
                            $balarm=false;
                            echo "Alarma disparada. Valor anterior ALARMA. No enviar nuevo correo.";
                        }
                      }
                  }
                  else
                  {
                      if($aalert[$icont]['iflag']==1)
                      {
                          $aalert[$icont]['iflag']=0;
                          $balarm=true;
                          echo "Alarma finalizada. Valor anterior de alarma activa.";
                      }
                      else
                      {
                          echo "Valor dentro de rango permitido. Sin notificación.";
                      }
                  }
                  // Si hay que enviar mail
                  if($balarm) {
                    // Array con string keys.
                    $aalert[$icont]['idusuario']=$rowalert['idusuario'];
                    $aalert[$icont]['idparametro']=$rowalert['idparametro'];
                    $aalert[$icont]['TEXTOALERTA']=$rowalert['textalert'];
                    IF(empty($aalert[$icont]['TEXTOALERTA'])){$aalert[$icont]['TEXTOALERTA']=$rowdb['NOMBREP'];}
                    $aalert[$icont]['PREFIJO']=$rowdb['PREFIJO'];
                    $aalert[$icont]['VALOR']=$valorcal;
                    $aalert[$icont]['valory']=$rowalert['valor'];
                    $aalert[$icont]['vporcent']="";
                    // Madar siempre array con 1 fila
                    /////////////////////////////////////////// Envio de mail
                    //print_r($aalert[0]);
                    $this->mailalert($aalert);
                    // Mandar por telegram si está configurado.
                    $this->AlertTelegram($aalert, $mysqli);
                    // Llamar a función de actualización de iflag
                    // print_r($aalert);
                    if ($this->ActFlagAlarm($aalert[$icont]['idalert'],$aalert[$icont]['iflag'])<0) return -1;
                  }
                  //////print_r($aalert[0]);
              }
          }
        }
    
    // Datos de la tabla de estimación
    public function checkstimate($idparametro = NULL)
        {
            // Conexiones
            $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
            if ($mysqli->connect_errno)
            {
                echo $mysqli->host_info."\n";
                exit();
            }
            // Importante juego de caracteres
            if (!mysqli_set_charset($mysqli, "utf8")) {
                printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($mysqli));
                exit();
            }
            // Array con datos a funcion mail
            $aalert = array();

            // Coger la fecha actual
            $vmes = (int)date('m', strtotime('-1 month') );
            // Todas las lineas del mes pasado
            if (!empty($idparametro)) {
                $sselect ="select p.parametro,p.prefijonum,a.idparametro,a.valorx,a.valory,a.idusuario,a.operacion,a.poralert from admestimacion a,parametros_server p where a.idparametro = ".$idparametro." and a.valorx=".$vmes;
            }else {
                $sselect ="select p.parametro,p.prefijonum,a.idparametro,a.valorx,a.valory,a.idusuario,a.operacion,a.poralert from admestimacion a,parametros_server p where a.valorx=".$vmes;
            }
            $sselect .= " and p.idparametro = a.idparametro";
            $sselect .= " ORDER BY a.idusuario,a.idparametro,a.valorx";
            //echo $sselect;

            $result = $mysqli->query($sselect) or exit("Codigo de error ({$mysqli->errno}): {$mysqli->error}");
            $icont=0;
            while($rowalert = mysqli_fetch_array($result)) {
                // Por cada parametero recuperar la select
               $rowdb = $this->valorbd($rowalert['idparametro'],2);
               // Calcular el valor descontando decimales
               $valorcal = $this->posdecimal($rowdb['VALOR'],$rowdb['POSDECIMAL']);
               // Añadir al array $rowdb el valor calculado
               $rowdb['VALOR'] = $valorcal;
               // Controlar q $rowvalor tiene filas. Procesar la filas encontradas
               if(!empty($rowdb))
               {
                // Variables de calculo por %
//                //// Calculo : Valor estamado --- 100 como valorreal ----x x=valorreal*100/valor estamado
                  //// Si valorporcent operador 100-x --> mail
                  
                  // Porcentaje real sobre estimado puede ser <> 100%.
                  $porcentreal = ($rowdb['VALOR']*100)/$rowalert['valory']; 
                  $porcentreal = 100 - $porcentreal;
                  
//                  echo "Valor real:".$rowdb['VALOR']." / valor estamado: ".$rowalert['valory'];
//                  echo ".El % real sobre estimado:".$porcentreal.".Operador ".$rowalert['operacion'].".Nº porcentaje configurado:".$rowalert['poralert'];
                  
                  $bmail = false;
                  // Logica de calculo pasar a mail las filas que cumplan los criterios
                  switch ($rowalert['operacion']) {
                    case "=":
                        if ($rowalert['poralert'] == $porcentreal){
                            $rowalert['operacion'] ="% igual";
                            $bmail = true;
                        }
                        break;
                    case "!=":
                        if ($rowalert['poralert'] != $porcentreal){
                            $rowalert['operacion'] ="% distinto";
                            $bmail = true;
                        }
                        break;
                    case ">=":
                        if ($rowalert['poralert'] >= $porcentreal){
                            $rowalert['operacion'] ="% mayor o igual";
                            $bmail = true;
                        }
                        break;
                    case "<=": 
                        if ($rowalert['poralert'] <= $porcentreal){
                            $rowalert['operacion'] ="% menor o igual";
                            $bmail = true;
                        }
                        break;
                    case ">":  
                        if ($rowalert['poralert'] > $porcentreal){
                            $rowalert['operacion'] ="% mayor";
                            $bmail = true;
                        }
                        break;
                    case "<":  
                        if ($rowalert['poralert'] < $porcentreal){
                            $rowalert['operacion'] ="% menor";
                            $bmail = true;
                        }
                        break;
                    }
                    if($bmail) {
                        // Array con string keys.
                        $aalert[$icont]['idusuario']=$rowalert['idusuario'];
                        $aalert[$icont]['idparametro']=$rowalert['idparametro'];
                        $aalert[$icont]['TEXTOALERTA']=$rowalert['parametro'];
                        $aalert[$icont]['desctipo']='Desviación mensual';
                        $aalert[$icont]['PREFIJO']=$rowalert['prefijonum'];
                        $aalert[$icont]['VALOR']=$rowdb['VALOR'];
                        $aalert[$icont]['valory']=$rowalert['valory'];
                        $aalert[$icont]['operacion']=$rowalert['operacion'];
                        $aalert[$icont]['poralert']=$rowalert['poralert'];
                        $aalert[$icont]['vdif']=$porcentreal;
                        $aalert[$icont]['iflag']=1;
                        echo $icont;
                    }
                }
            }
            // Llamar a la función
            if (sizeof($aalert) > 0) {
                $this->mailalert($aalert);
            }else{
               // echo "No existen filas a tratar.";
            }
                    
        }
    private function newPHPMailer()
    {
        // Coger los datos de la instalación.
        $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
        if ($mysqli->connect_errno)
        {
            echo $mysqli->host_info."\n";
            exit();
        }
        // Importante juego de caracteres
        if (!mysqli_set_charset($mysqli, "utf8")) {
            printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($mysqli));
            exit();
        }
        $sql = "select passmail "
                    . "from instalacion "
                    . "where estado = 1";
        // Execute the query, or else return the error message.
        $result = $mysqli->query($sql) or exit("Codigo de error ({$mysqli->errno}): {$mysqli->error}");
        $row = mysqli_fetch_array($result);
        $phpmailer = new PHPMailer();
        $phpmailer->Username = "alarmas@riegosolar.net";
        ///$phpmailer->Password = "Riegosolar77_";  // Old
        $phpmailer->Password = $row['passmail'];
        //echo "Password de mail en tabla instalación:".$phpmailer->Password;
        if(!isset($row['passmail'])) $phpmailer->Password = "Riegosolar_77";
        //$phpmailer->SMTPDebug = 1;
        $phpmailer->Host = "smtp.riegosolar.net";
        $phpmailer->Port = '587';
        $phpmailer->SMTPAuth = false;
        $phpmailer->SMTPAutoTLS = false; 
        $phpmailer->IsSMTP();
        $phpmailer->SMTPAuth = true;
        $phpmailer->IsHTML(true);
        $phpmailer->setFrom($phpmailer->Username,"Alarmas automaticas.");
        return $phpmailer;
    }
    private function mailalert($aalert)
    {
        // Se el pasa $rowvalor: Datos del dia/mes. $row los datos de la alerta.
        // Coger los datos de la instalación.
        $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
        if ($mysqli->connect_errno)
        {
            echo $mysqli->host_info."\n";
            exit();
        }
        // Importante juego de caracteres
        if (!mysqli_set_charset($mysqli, "utf8")) {
            printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($mysqli));
            exit();
        }
        // Mail settingphpmailer = new PHPMailer();
        $phpmailer=$this->newPHPMailer();
		
        // Crear un array con los detalles del correo.
        // Recorrer todas las filas del array y pintar array final
        $afinal = array();
        $iduser = 0;
        $icont = 0;
        // Variables de proceso
        $simageninstall="";
        $toemail ="";
        $subject="";
        $idparametro="";
        $message="";
        $headers="";
        foreach ($aalert as $vfila) {
            if($iduser <> $vfila['idusuario'])
            {
                // Si icont > 0 mandar correo del usuario anterior. Y procede envio
                if($icont > 0)
                {
                    // Final tabla
                    $message .='<tr></tr></table>
                    <hr style="color: #3A72A5;" />
                    <p>Final de listado de alertas.</p>
                    </body>
                    </html>';				
                    $phpmailer->AddAddress($toemail); // recipients email
                    $phpmailer->Subject = $subject;	
                    $phpmailer->Body .= $message;
                    $phpmailer->CharSet = 'UTF-8';
                    $phpmailer->Send();
                    $phpmailer=$this->newPHPMailer();
                    // Log de mail enviado.
                    $this->logmail($toemail,$subject,$idparametro,$vfila['VALOR'],$vfila['iflag']);
                }
                $iduser = $vfila['idusuario'];
                // Datos logmail;
                $idparametro=$vfila['idparametro'];
                $intvalor=$vfila['VALOR'];
                if(empty($intvalor)) $intvalor=0;
                // Cargar datos para mail
                $sselect ="SELECT i.nombre,i.titular,i.ubicacion,i.imagen,s.nombreserver,s.falta,u.email 
                from instalacion i,server_instalacion s, usuarios u
                where i.idinstalacion = s.idinstalacion
                and u.idserver = s.idserver
                and u.idusuario=".$iduser;
                //echo $sselect;
                $result = $mysqli->query($sselect) or exit("Codigo de error ({$mysqli->errno}): {$mysqli->error}");
                $row = mysqli_fetch_array($result);
                // Datos del correo.
                $toemail = $row['email'];
                $subject = "Alertas automáticas instalación ".$row["nombre"].".Servidor ".$row['nombreserver'];
                // Imagen logo
                $simagenlogo='/var/www/html/riegosolar/imagenes/RIEGOSOLAR_Blanco.png';
                //Pegar full path de imagen
                if (!file_exists($simagenlogo)) {
                    //Old servers
                    $simagenlogo= '/var/www/riegosolar/imagenes/RIEGOSOLAR_Blanco.png';
                    //echo "The file $simageninstall exists";
                }
                $phpmailer->AddEmbeddedImage($simagenlogo,'imagenlogo','RIEGOSOLAR_Blanco.png');
                // Check apache path
                $simageninstall= '/var/www/html/riegosolar/'.$row["imagen"];   
                //Pegar full path de imagen
                if (!file_exists($simageninstall)) {
                    //Old servers
                    $simageninstall= '/var/www/riegosolar/'.$row["imagen"];
                    //echo "The file $simageninstall exists";
                }
                $phpmailer->AddEmbeddedImage($simageninstall,'imginstall','instalacion.jpg');
                
                // Always set content-type when sending HTML email
                $headers = 'From: <alertas@riegosolar.net>' . "\r\n";
                //$headers .= 'Cc: myboss@example.com' . "\r\n";
                $headers .= "MIME-Version: 1.0"."\r\n"."Content-type: text/html; charset=UTF-8"."\r\n";
                ///<meta charset="UTF-8">
                $message = '
                <html>
                <head>
                <title>'.$subject.'</title>
                </head>
                <body>';
                $message .='<img src="cid:imagenlogo" style="background-color:#3A72A5;>';
                $message .='<hr style="color: #3A72A5;" />';
                // Cabecera del mensaje
                $message .='<p>Listado de alertas instalación<p/>';
                $message .='<hr style="color: #3A72A5;" />';

                $message .='<table>
                <tr><td>Instalación: </td><td>'.$row["nombre"].'</td></tr>
                <tr><td>Titular: </td><td>'.$row["titular"].'</td></tr>
                <tr><td>Ubicación: </td><td>'.$row["ubicacion"].'</td></tr>';
                $message .='<tr></tr></table>';
                $message .='<img src="cid:imginstall" width="300" style="background-color:#3A72A5;>';
                
                $message .='<p>Detalles de alarma<p/>';
                $message .='<hr style="color: #3A72A5;" />';

                // Recorrer todas las lineas de detalle
                $message .='<table>
                <tr></tr><tr></tr>
                <tr><td>Fecha</td><td>Alerta</td><td>Periodo</td><td>Valor Real</td><td>Valor Esperado</td><td>Calculo</td><td>Motivo Mail</td></tr>';                 
            }
            // Pintar detalles de cada fila
            // Control de iFlag: 1 Activada 0 restablecida
            $sflag="Activada";
            if($vfila['iflag']==0)
            {
               $sflag="Corregida"; 
            }
            $message .='<tr>';
            $message .='<td>'.date("d/m/Y").'</td><td>'.$vfila['TEXTOALERTA'].'</td><td>'.$vfila['desctipo'].'</td><td ALIGN=RIGHT>'.$vfila['VALOR'].$vfila['PREFIJO'].'</td><td ALIGN=RIGHT>'.$vfila['valory'].$vfila['PREFIJO'].'</td><td ALIGN=RIGHT>'.$vfila['poralert'].$vfila['operacion'].'</td>'.'<td ALIGN=LEFT>'.$sflag.'</td>';
            $message .='</tr>';   
            // Más filas
            $icont ++;
            ////echo "Valor icont función mail:".$icont;
        }
        // Mandar mail de último usuario
        // Final tabla
        $message .='<tr></tr></table>
        <hr style="color: #3A72A5;" />
        <p>Final de listado de alertas.</p>
        </body>
        </html>';
        // Sólo si la alerta se tiene que mandar
        //echo $message;
        $phpmailer->AddAddress($toemail); // recipients email
        $phpmailer->Subject = $subject;	
        $phpmailer->Body .= $message;
        $phpmailer->CharSet = 'UTF-8';
        $phpmailer->Send();
        $this->logmail($toemail,$subject,$idparametro,$intvalor,$vfila['iflag']);
            
        return 1;
    }
 
    public function mailsumary($asumaryprod)
      {
        // Conexiones
        $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
        if ($mysqli->connect_errno)
        {
            echo $mysqli->host_info."\n";
            exit();
        }
        // Importante juego de caracteres
        if (!mysqli_set_charset($mysqli, "utf8")) {
            printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($mysqli));
            exit();
        } 
        // Array con datos a funcion mail
        $auser = array();
        $icont=0;
        $sselect ="select idusuario from usuarios where idserver=".$_SESSION['idserver']." and trim(email) <>''";
        //printf($sselect);
        $result = $mysqli->query($sselect) or exit("Codigo de error ({$mysqli->errno}): {$mysqli->error}");
        while($rowuser = mysqli_fetch_array($result)) {
            // Array con string keys.
            $auser[$icont]['idusuario']=$rowuser['idusuario'];
            $icont++;    
        }
        //print_r($auser);
        // Llamar a la función
        if (sizeof($auser) > 0) {
            $this->sendmailsumary($auser,$asumaryprod);
        }else{
           // echo "No existen filas a tratar.";
        }
      }
    
    private function sendmailsumary($auser,$asumaryprod)
    {
        // Se el pasa $rowvalor: Datos del dia/mes. $row los datos de la alerta.
        // Coger los datos de la instalación.
        $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
        if ($mysqli->connect_errno)
        {
            echo $mysqli->host_info."\n";
            exit();
        }
        // Importante juego de caracteres
        if (!mysqli_set_charset($mysqli, "utf8")) {
            printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($mysqli));
            exit();
        }
        // Mail setting
        $phpmailer=$this->newPHPMailer();
		
        // Crear un array con los detalles del correo.
        // Recorrer todas las filas del array y pintar array final
        $afinal = array();
        $iduser = null;
        $icont = 0;
        // Variables de proceso
        $toemail ="";
        $subject="";
        $message="";
        $headers="";
        $simageninstall="";
        foreach ($auser as $vfila) {
            
            if($iduser <> $vfila['idusuario'])
            {
                // Si icont > 0 mandar correo del usuario anterior. Y procede envio
                if($icont > 0)
                {
                    // Final tabla
                    $message .='<tr></tr></table>
                    <hr style="color: #3A72A5;" />
                    <p>Final de resumen instalación.</p>
                    </body>
                    </html>';
                    $phpmailer->CharSet = 'UTF-8';
                    $phpmailer->AddAddress($toemail); // recipients email
                    $phpmailer->Subject = $subject;	
                    $phpmailer->Body .= $message;
                    $phpmailer->Send();
                    // Log de mail enviado.
                    $this->logmail($toemail,$subject,0,0,1);
                    // Create new mail for the next user
                    $phpmailer=$this->newPHPMailer();
                }
                $iduser = $vfila['idusuario'];
                $sselect ="SELECT i.nombre,i.titular,i.ubicacion,i.imagen,s.nombreserver,s.falta,u.email 
                from instalacion i,server_instalacion s, usuarios u
                where i.idinstalacion = s.idinstalacion
                and u.idserver = s.idserver
                and u.idusuario=".$iduser;
                //echo $sselect;
                $result = $mysqli->query($sselect) or exit("Codigo de error ({$mysqli->errno}): {$mysqli->error}");
                $row = mysqli_fetch_array($result);
                // Datos del correo.
                $toemail = $row['email'];
                $subject = "Resumen instalación ".$row["nombre"].".Servidor ".$row['nombreserver']."(".date('d/m/Y H:i:s').")";
                ///<meta charset="UTF-8">
                // Imagen logo
                $simagenlogo='/var/www/html/riegosolar/imagenes/RIEGOSOLAR_Blanco.png';
                //Pegar full path de imagen
                if (!file_exists($simagenlogo)) {
                    //Old servers
                    $simagenlogo= '/var/www/riegosolar/imagenes/RIEGOSOLAR_Blanco.png';
                    //echo "The file $simageninstall exists";
                }
                $phpmailer->AddEmbeddedImage($simagenlogo,'imagenlogo','RIEGOSOLAR_Blanco.png');
                $message = '
                <html>
                <head>
                <title>'.$subject.'</title>
                </head>
                <body>';
                $message .='<img src="cid:imagenlogo" style="background-color:#3A72A5;>';
                $message .='<hr style="color: #3A72A5;" />';
                // Cabecera del mensaje
                $message .='<p/>Resumen diario instalación<p/>';
                // Recorrer todas las lineas de detalle
                //Pegar full path de imagen
                $simageninstall='/var/www/html/riegosolar/'.$row["imagen"];   
                //Pegar full path de imagen
                if (!file_exists($simageninstall)) {
                    //Old servers
                    $simageninstall='/var/www/riegosolar/'.$row["imagen"];
                    //echo "The file $simageninstall exists";
                }
                //echo $simageninstall;
                $phpmailer->AddEmbeddedImage($simageninstall,'imginstall','instalacion.jpg');
                
                $message .='<img src="cid:imginstall" width="300" style="background-color:#3A72A5;>';
                $message .='<hr style="color: #3A72A5;" />';
                $message .='<table>
                <tr><td>Instalación: </td><td>'.$row["nombre"].'</td></tr>
                <tr><td>Titular: </td><td>'.$row["titular"].'</td></tr>
                <tr><td>Ubicación: </td><td>'.$row["ubicacion"].'</td></tr>
                <tr></tr><tr></tr>
                <tr><td>Hoy</td><td>Mes actual</td><td>Año '.date('Y').'</td><td>Hasta '.date('Y').'</td></tr>';
                $message .='<tr>
                            <td align="left">'.$asumaryprod[0]['hoy'].'</td>
                            <td align="left">'.$asumaryprod[0]['month'].'</td>
                            <td align="left">'.$asumaryprod[0]['year'].'</td>
                            <td align="left">'.$asumaryprod[0]['preyear'].'</td>
                            </tr>';
            }
            $icont ++;
        }
        // Mandar mail de último usuario
        // Final tabla
        $message .='<tr></tr></table>
        <hr style="color: #3A72A5;" />
        <p>Final de resumen instalación.</p>
        </body>
        </html>';
	//echo $message;
        $phpmailer->CharSet = 'UTF-8';
        $phpmailer->AddAddress($toemail); // recipients email
        $phpmailer->Subject = $subject;	
        $phpmailer->Body .= $message;
        $phpmailer->Send();
       
        $this->logmail($toemail,$subject,0,0,1);
        return 1;
    }
    
    // Retorna array. Tipo lectura. 0 última,1 diaria,2 mes,3 anual
    private function valorbd($vparam,$tipolectura)
        {
            // Conexiones
            $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
            if ($mysqli->connect_errno)
            {
                echo $mysqli->host_info."\n";
                exit();
            }
            // Importante juego de caracteres
            if (!mysqli_set_charset($mysqli, "utf8")) {
                printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($mysqli));
                exit();
            }
            // Coger la variable string de filtro de fecha
            $sdate=$this->getfecha($tipolectura);
            switch ($tipolectura) {
            case 0:
                // Coger el máximo valor de lectura
                $sselect ="SELECT MAX(IDLECTURA) as MAXID FROM lectura_parametros ";
                $sselect.="WHERE idparametro = ".$vparam;
                $result = $mysqli->query($sselect) or exit("Codigo de error ({$mysqli->errno}): {$mysqli->error}");
                $rowvalor = mysqli_fetch_array($result);
                if(empty($rowvalor)){
                    return 0;
                }
                $sselect ="SELECT NOMBREP,PREFIJO,POSDECIMAL,VALOR,WORDVALOR FROM vgrafica ";
                $sselect.="WHERE IDLECTURA = ".$rowvalor['MAXID'];
                break;
            case 2:
                // Mes actual.
                $sselect ="SELECT NOMBREP,PREFIJO,POSDECIMAL,SUM(VALOR) AS VALOR FROM vgrafica_dias ";
                $sselect.="WHERE idparametro = ".$vparam;
                $sselect.= $sdate;
                $sselect .=" group by idparametro";
                break;
            case 3:
                // Año actual
                $sselect = "SELECT NOMBREP,PREFIJO,POSDECIMAL,SUM(VALOR) AS VALOR FROM vgrafica_dias ";
                $sselect.="WHERE idparametro = ".$vparam;
                $sselect.= $sdate;
                $sselect.=" GROUP BY idparametro";
                break;
            default:
                // Valor diario
                $sselect = "SELECT NOMBREP,COLOR,PREFIJO,POSDECIMAL,SUM(VALOR) AS VALOR FROM vgrafica_horas ";
                $sselect.="WHERE idparametro = ".$vparam;
                $sselect.= $sdate;
                $sselect.= " group by idparametro,DATE_FORMAT(flectura,'%Y-%m-%d') order by idparametro,flectura"; 
               //echo $sselect;
            }
            // Recuperar array
            //echo $sselect;
            $result = $mysqli->query($sselect) or exit("Codigo de error ({$mysqli->errno}): {$mysqli->error}");
            $rowvalor = mysqli_fetch_array($result);
            //print_r($rowvalor);
            // Retorna un array.
            return $rowvalor;
        }
    // Log de mail en mysql
    private function logmail($toemail,$subject,$parametro,$intvalor,$iflag)
    {
        $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
        if ($mysqli->connect_errno)
        {
            echo $mysqli->host_info."\n";
            return -1;
        }
        // Importante juego de caracteres
        //printf("Conjunto de caracteres inicial: %s\n", mysqli_character_set_name($mysqli));
        if (!mysqli_set_charset($mysqli, "utf8")) {
            printf("Error cargando el conjunto de caracteres deutf8: %s\n", mysqli_error($mysqli));
            exit();
        }
        
        $sinsert = "INSERT INTO alertserverlog (toemail,subject,idparametro,intvalor,iflag) VALUES ('".$toemail."','".$subject."','".$parametro."',".$intvalor.",".$iflag.")";
        //echo $sinsert;
        /////return 0;  /////////////////////////////// <---------------- QUITAR
        if ($mysqli->query($sinsert) === TRUE)
        {
            //echo "Nuevo bitname creado.";
        } else {
            echo "Falló la inserción: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $mysqli->close();
        return 0; 
    }
    // public function getfecha($tipolectura)
    
    private function getfecha($tipolectura) 
    {
        // La función con la tipo lectura, returna un rango de fechas
        // Para alertas se coge el día anterior, mes anterior, año aterior.
        $sqldate = "";
        $vfecha =date('Y-m-d'); 
        // Fecha pruebas /////////////////////////////////////////////////////////////////////////////////////////////// <--
        ////$vfecha = "2017-05-31";
        switch ($tipolectura) {
        case 2:
            // Fecha del mes anterior
            $vmes = date('m',strtotime($vfecha));
            $vyear = date('Y',strtotime($vfecha));
            // Formato de fecha estandar yyyy-mm-dd HH:mm:ss
            $vfecha = "01-".$vmes."-".$vyear;
            $vdesde = date("Y-m-d H:i:s", strtotime('-1 month', strtotime($vfecha)));
            $vhasta = date("Y-m-d H:i:s", strtotime('+0 month',strtotime($vfecha)));
            break;
        case 3:
            // Ejecicio anterior
            $vyear = date('Y',strtotime($vfecha));
            // Formato de fecha estandar yyyy-mm-dd HH:mm:ss
            $vfecha = "01-01-".strval(intval($vyear)-1);
            //echo $vfecha;
            $vdesde = date("Y-m-d H:i:s", strtotime('+0 hours', strtotime($vfecha)));
            $vhasta = date("Y-m-d H:i:s", strtotime('+1 year',strtotime($vfecha)));
            break;
        default:
            // fecha de ayer.
            $vdesde = date("Y-m-d H:i:s", strtotime('-1 days', strtotime($vfecha)));
            $vhasta = date("Y-m-d H:i:s", strtotime('+0 days',strtotime($vfecha)));
        }
        // Retornar rango de fechas
        $sqldate = " AND flectura >= '".date($vdesde)."'";
        $sqldate.=" AND flectura < '".date($vhasta)."'";
        // Retornar fechas
        return $sqldate;
    }
    public function posdecimal($valor,$posiciones) {
        if ($posiciones > 0) {
            $div = 1;
            for ($x = 0; $x < $posiciones; $x++) {
                $div = $div * 10;
            }
            // Resultado
            $valor = $valor/$div;
            $valor = round($valor, $posiciones);
            //$valor = $valor/$div;
        }
        // Retorna valor sin tocar o con la division
        return $valor;           
    }
    
    private function EventMailType($tipolectura) 
    {
        $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname'],$_SESSION['dbport']);
        if ($mysqli->connect_errno)
        {
            echo $mysqli->host_info."\n";
            return -1;
        }

        // Comprobar path de binarios
        $sbin='php /var/www/html/riegosolar/mailalertos.php';
        //Pegar full path de imagen
        if (!file_exists($sbin)) {
            //Old servers
            $sbin='php /var/www/riegosolar/mailalertos.php';
            ///echo "The file $sbin exists";
        }
        // Crear evento  date('Y-m-d H:i:s', strtotime('+1 day')) 
        $sql = "CREATE OR REPLACE EVENT ";
        $date=date('Y-m-d H:i:s');
        switch ($tipolectura) {
        case 0: // 5 Min
            $newtimestamp = strtotime($date. ' + 5 minute');
            $newdate = date('Y-m-d H:i:s', $newtimestamp);
            $sql .= "MAILALERT ON SCHEDULE EVERY 5 MINUTE STARTS '".$newdate."' ON COMPLETION PRESERVE ENABLE ";
            break;
        case 1: // DAY
            $newtimestamp = strtotime($date. '+1 day');
            $newdate = date('Y-m-d', $newtimestamp);
            $sql .= "MAILALERTDAY ON SCHEDULE EVERY 1 DAY STARTS '".$newdate." 08:10:00' ON COMPLETION PRESERVE ENABLE ";
            break;
        case 2: // MONTH
            $date=date('Y-m').'-01';
            $newtimestamp = strtotime($date. '+1 month');
            $newdate = date('Y-m-d', $newtimestamp)." 09:00:00";
            $sql .= "MAILALERTMES ON SCHEDULE EVERY 1 MONTH STARTS '".$newdate."' ON COMPLETION PRESERVE ENABLE ";
            break;            
        case 3: // YEAR
            $date=date('Y').'-01-01';
            $newtimestamp = strtotime($date. '+1 year');
            $newdate = date('Y-m-d', $newtimestamp)." 10:00:00";
            $sql .= "MAILALERTMES ON SCHEDULE EVERY 1 YEAR STARTS '".$newdate."' ON COMPLETION PRESERVE ENABLE ";
            break;  
        }
        $sql.= "DO SELECT sys_exec('".$sbin." ".$_SESSION['usuario']." ".$_SESSION['passap']." ".$tipolectura."')";
        if ($mysqli->query($sql) === FALSE) {
            echo "Error al actualizar B.D. " . $mysqli->error;
            return 0;
        } 
        ///echo $sql;
        // Bien
        return 1;
    }
    private function AlertTelegram($aalert,$mysqli) 
    {
        // Recorrer al array de alertas
        //echo "AlertTelegram funtion.";
        foreach ($aalert as $vfila) {
            $sselect = "select usuarios.usuario,usuarios.telephone,instalacion.nombre,instalacion.tokenbot from usuarios,instalacion where idusuario=".$vfila['idusuario'];
            $result = $mysqli->query($sselect) or exit("Codigo de error ({$mysqli->errno}): {$mysqli->error}");
            $row = mysqli_fetch_assoc($result);
            //print_r($row);
            // Control de valores
            if(!isset($row['telephone'])) return 0;
            if(!isset($row['tokenbot']))
            {
                $telegrambot="1673994063:AAE-DSTVgkowMduh3llR58mewEX8gidu6PA";
            }else{
                $telegrambot=$row['tokenbot'];
            }
            $telegramchatid=$row['telephone'];
            if($telegramchatid>0) $telegramchatid=$telegramchatid*(-1);
            // Control de FLAG
            if($vfila['iflag']==1)
            {
                $msg="@".$row['usuario'].'. Se ha producido la siguiente alarma en '.$row['nombre'].".";
            }else
            {
                $msg="@".$row['usuario'].'.Información, alarma restablecida en '.$row['nombre'].".";
            }
            //echo $msg;
            $this->telegram($telegrambot,$telegramchatid,$msg);
            //Descrip alarma
            $msg="Descripción alarma: ".$vfila['TEXTOALERTA'];
            $this->telegram($telegrambot,$telegramchatid,$msg);
            $msg="El tipo de alarma es: ".$vfila['desctipo'];
            $this->telegram($telegrambot,$telegramchatid,$msg);
            // Control de FLAG
            if($vfila['iflag']==1)
            {
                $msg="El último valor ".$vfila['VALOR'].$vfila['PREFIJO']." es ".$vfila['operacion']." que ".$vfila['valory'].$vfila['PREFIJO'].".";
            }else{
                $msg="El último valor ".$vfila['VALOR'].$vfila['PREFIJO']." ya no es ".$vfila['operacion']." que ".$vfila['valory'].$vfila['PREFIJO'].".";
            }
            $this->telegram($telegrambot,$telegramchatid,$msg);    
        }

    }
    // Telegram function which you can call
    private function telegram($telegrambot,$telegramchatid,$msg) {
        $url='https://api.telegram.org/bot'.$telegrambot.'/sendMessage';$data=array('chat_id'=>$telegramchatid,'text'=>$msg);
        //echo $url;
        //return 0;
        $options=array('http'=>array('method'=>'POST','header'=>"Content-Type:application/x-www-form-urlencoded\r\n",'content'=>http_build_query($data),),);
        $context=stream_context_create($options);
        $result=file_get_contents($url,false,$context);
        return $result;
    }
    // End of class
}
