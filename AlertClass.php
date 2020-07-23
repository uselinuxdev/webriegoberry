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
                horamaxbit = ?
                WHERE idalert = ?");
            // Bind variables
            $stmt->bind_param('iiiisisissi',
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
            $_POST['idalert'][$i]);
            //print_r($_POST);
            
            //echo "stmt bind_param correcto.";
            // Ejecutar
            $stmt->execute();
            // Finalizar
            $stmt->close();
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
        
        $sinsert = "INSERT INTO alertserver (idserver,horaminbit,horamaxbit,operacion) VALUES (".$_SESSION['idserver'].",'00:00:00','23:59:00','<')";
       
        if ($mysqli->query($sinsert) === TRUE)
        {
            //echo "Nuevo bitname creado.";
        } else {
            echo "Falló la inserción: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $mysqli->close();
        return 0;
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
    // Funcion publica, recorre las alertas por tipo: 0 última,1 diaria,2 mensual y 3 anual
    public function checkalert()
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
          $sselect ="select * from alertserver where estado=1 order by idparametro";
          //printf($sselect);
          $result = $mysqli->query($sselect) or exit("Codigo de error ({$mysqli->errno}): {$mysqli->error}");
          while($rowalert = mysqli_fetch_array($result)) {
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
                    if ($vnow > '08:00:00' and $vnow < '08:05:00')
                    //if ($vnow > '08:00:00' and $vnow < '23:05:00')
                    {
                        $rowdb = $this->valorbd($rowalert['idparametro'],$rowalert['tipo']);
                        $aalert[$icont]['desctipo'] = 'Diaria';
                    } else
                    {
                       unset($rowdb);
                    }
                    break;
                  case 2:
                    // Fecha del mes anterior
                    $vnow = date("d H:i:s"); // 17:16:18
                    //echo $vnow;
                    if ($vnow > '01 09:00:00' and $vnow < '01 09:05:00')
                    //if ($vnow > '29 09:00:00' and $vnow < '30 09:05:00')
                    {
                        $rowdb = $this->valorbd($rowalert['idparametro'],$rowalert['tipo']);
                        $aalert[$icont]['desctipo'] = 'Mensual';
                    } else
                    {
                       unset($rowdb);
                    }
                    break;
                  case 3:
                    // Fecha del año anterior
                    $vnow = date("m-d H:i:s"); // 17:16:18
                    //echo $vnow;
                    //if ($vnow > '11-29 10:00:00' or $vnow < '11-29 22:05:00')
                    if ($vnow > '01-01 10:00:00' and $vnow < '01-01 10:05:00')
                    {
                        $rowdb = $this->valorbd($rowalert['idparametro'],$rowalert['tipo']);
                        $aalert[$icont]['desctipo'] = 'Anual';
                    } else
                    {
                       unset($rowdb);
                    }
                    break;
              }
              //print_r($rowdb);
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
                  $bmail = false;
                  switch ($rowalert['operacion']) {
                      case "=":
                          if ($valorcal == $rowalert['valor']){
                              $rowalert['operacion'] ="Igual";
                              // Mail alerta
                              $bmail = true;
                          }
                          break;
                      case "!=":
                          if ($valorcal != $rowalert['valor']){
                              $rowalert['operacion'] ="Distinto";
                              // Mail alerta
                              $bmail = true;
                          }
                          break;
                      case ">=":
                          if ($valorcal >= $rowalert['valor']){
                              $rowalert['operacion'] ="Mayor o igual";
                              // Mail alerta
                              $bmail = true;
                          }
                          break;
                      case "<=": 
                          if ($valorcal <= $rowalert['valor']){
                              $rowalert['operacion'] ="Menor o igual";
                              // Mail alerta
                              $bmail = true;
                          }
                          break;
                      case ">":  
                          if ($valorcal > $rowalert['valor']){
                              $rowalert['operacion'] ="Mayor";
                              // Mail alerta
                              $bmail = true;
                          }
                          break;
                      default:  
                          if ($valorcal < $rowalert['valor']){
                              $rowalert['operacion'] ="Menor";
                              // Mail alerta
                              $bmail = true;
                          }
                          break;
                  }
                  //print_r($rowalert);
                  // Control de filtro de horas
                  if($rowalert['horaminbit'] > '00:00:00' or $rowalert['horamaxbit'] < '23:59:00')
                  {
                      if(date('H:i:s') < $rowalert['horaminbit'] or date('H:i:s') >$rowalert['horamaxbit'])
                      {
                          $bmail = false;
                      }
                  }
                  // Si hay que enviar mail
                  if($bmail) {
                      // Array con string keys.
                      $aalert[$icont]['idusuario']=$rowalert['idusuario'];
                      $aalert[$icont]['idparametro']=$rowalert['idparametro'];
                      $aalert[$icont]['TEXTOALERTA']=$rowalert['textalert'];
                      IF(empty($aalert[$icont]['TEXTOALERTA'])){$aalert[$icont]['TEXTOALERTA']=$rowdb['NOMBREP'];}
                      $aalert[$icont]['PREFIJO']=$rowdb['PREFIJO'];
                      $aalert[$icont]['VALOR']=$valorcal;
                      $aalert[$icont]['valory']=$rowalert['valor'];
                      $aalert[$icont]['operacion']=$rowalert['operacion'];
                      $aalert[$icont]['vporcent']="";
                      $icont++;
                  }
                  //print_r($aalert[0]);
              }
          }
          //print_r($aalert);
          // Llamar a la función
          if ($bmail) {
              $this->mailalert($aalert,1);
          }else{
             // echo "No existen filas a tratar.";
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
                        $icont++;
                    }
                }
            }
            // Llamar a la función
            if (sizeof($aalert) > 0) {
                $this->mailalert($aalert,0);
            }else{
               // echo "No existen filas a tratar.";
            }
                    
        }
    private function newPHPMailer()
    {
        $phpmailer = new PHPMailer();
        $phpmailer->Username = "alarmas@riegosolar.net";
        $phpmailer->Password = "Riegosolar77_";
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
    private function mailalert($aalert, $checkmailday = 0)
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
        $message="";
        $headers="";
        foreach ($aalert as $vfila) {
            // Check 1 mail de alertas/ día
            if($checkmail==1)
            {
              //Comprobar si es el primer mail. Sólo generar 1 mail
              $checkmail=checkmailday($aalert[0]['idparametro']);
            }
            if($iduser <> $vfila['idusuario'])
            {
                // Si icont > 0 mandar correo del usuario anterior. Y procede envio
                if($icont > 0 and $checkmail==0 )
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
                    $this->logmail($toemail,$subject,$vfila['idparametro'],$vfila['VALOR'],1);
                }
                else
                {
                    // Log de mail enviado.
                    $this->logmail($toemail,$subject,$vfila['idparametro'],$vfila['VALOR'],0);                    
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
                $subject = "Alertas automáticas instalación ".$row["nombre"].".Servidor ".$row['nombreserver'];
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
                <tr><td>Fecha</td><td>Alerta</td><td>Periodo</td><td>Valor Real</td><td>Valor Esperado</td><td>Calculo</td></tr>';                 
            }
            // Pintar detalles de cada fila
            $message .='<tr>';
            $message .='<td>'.date("d/m/Y").'</td><td>'.$vfila['TEXTOALERTA'].'</td><td>'.$vfila['desctipo'].'</td><td ALIGN=RIGHT>'.$vfila['VALOR'].$vfila['PREFIJO'].'</td><td ALIGN=RIGHT>'.$vfila['valory'].$vfila['PREFIJO'].'</td><td ALIGN=RIGHT>'.$vfila['poralert'].$vfila['operacion'].'</td>';
            $message .='</tr>';   
            // Más filas
            $icont ++;
        }
        // Mandar mail de último usuario
        // Final tabla
        $message .='<tr></tr></table>
        <hr style="color: #3A72A5;" />
        <p>Final de listado de alertas.</p>
        </body>
        </html>';
        // Sólo si la alerta se tiene que mandar
        if($checkmail==0 )
        {
            //echo $message;
            $phpmailer->AddAddress($toemail); // recipients email
            $phpmailer->Subject = $subject;	
            $phpmailer->Body .= $message;
            $phpmailer->CharSet = 'UTF-8';
            $phpmailer->Send();
            $this->logmail($toemail,$subject,$parametro,$intvalor,1);
        }
        else
        {
            $this->logmail($toemail,$subject,$parametro,$intvalor,0);
        }
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
                    $phpmailer->AddAddress($toemail); // recipients email
                    $phpmailer->Subject = $subject;	
                    $phpmailer->Body .= $message;
                    $phpmailer->CharSet = 'UTF-8';
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
        $phpmailer->AddAddress($toemail); // recipients email
        $phpmailer->Subject = $subject;	
        $phpmailer->Body .= $message;
        $phpmailer->CharSet = 'UTF-8';
        $phpmailer->Send();
       
        $this->logmail($toemail,$subject);
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
                $sselect = "SELECT NOMBREP,COLOR,PREFIJO,POSDECIMAL,SUM(VALOR) AS VALOR FROM vgrafica_dias ";
                $sselect.="WHERE idparametro = ".$vparam;
                $sselect.= $sdate;
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
    // Check mail by day
    private function checkmailday($parametro)
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
        $vfecha =date('Y-m-d'); 
        $vdesde = date("Y-m-d H:i:s", strtotime('-1 days', strtotime($vfecha)));
        $sql = "select count(1) countp from alertserverlog where falta > '".date($vdesde)."' and idparametro=".$parametro;
        
        $result = $mysqli->query($sql) or exit("Codigo de error ({$mysqli->errno}): {$mysqli->error}");
        $row = mysqli_fetch_array($result); 
        return $row['countp'];
    }
    // Log de mail en mysql
    private function logmail($toemail,$subject,$parametro,$intvalor,$bmailday)
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
        
        $sinsert = "INSERT INTO alertserverlog (toemail,subject,idparametro,intvalor,mailday) VALUES ('".$toemail."','".$subject."',".$parametro.",".$intvalor.",".$bmailday.")";
        //echo $sinsert;
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
    // End of class
}
