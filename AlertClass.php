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
class AlertClass {
    // Actualizar Usuarios
    public function updatealert()
    {
        // Control post
        $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname']);
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
            
            //echo "stmt bind_param correcto.";
            // Ejecutar
            $stmt->execute();
            // Finalizar
            $stmt->close();
        }
    }
    public function deletealert()
    {
        $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname']);
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
        $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname']);
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
        
        $sinsert = "INSERT INTO alertserver (idserver) VALUES (".$_SESSION['idserver'].")";
       
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
            mysql_connect($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass']) or die ("No se puede establecer la conexion!!!!"); 
            mysql_select_db($_SESSION['dbname']) or die ("Imposible conectar a la base de datos!!!!"); //Selecionas tu base
            mysql_set_charset('utf8'); // Importante juego de caracteres a utilizar.
            
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
            $resparametros = mysql_query($sql);
            // Parametros de la select
            while($row = mysql_fetch_array($resparametros)) { //Iniciamos un ciclo para recorrer la variable $resparametros que tiene la consulta previamente hecha 
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
            mysql_connect($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass']) or die ("No se puede establecer la conexion!!!!"); 
            mysql_select_db($_SESSION['dbname']) or die ("Imposible conectar a la base de datos!!!!"); //Selecionas tu base
            mysql_set_charset('utf8'); // Importante juego de caracteres a utilizar.
            
            $sql = "SELECT idusuario,usuario from usuarios ";
            $sql.=" where idserver = ".$_SESSION['idserver'];
            $sql.=" order by usuario ";
            
            // Pintar combo
            echo '<select name="'.$name.'" style="width: 90px;">'; 
            // No definido
            $resuser= mysql_query($sql);
            // Parametros de la select
            while($row = mysql_fetch_array($resuser)) { //Iniciamos un ciclo para recorrer la variable $resparametros que tiene la consulta previamente hecha 
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
    // Funcion publica, recorre las alertas por tipo: 1 diaria,0 última
    public function checkalert()
        {
            // Conexiones
            $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname']);
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
            $sselect ="select a.*,p.lectura from alertserver a,parametros_server p where a.estado=1 and";
            $sselect .=" a.idparametro=p.idparametro order by idparametro";
            $result = $mysqli->query($sselect) or exit("Codigo de error ({$mysqli->errno}): {$mysqli->error}");
            while($rowalert = mysqli_fetch_array($result)) {
                // Por cada parametero recuperar la select
                $rowdb = $this->valorbd($rowalert['idparametro'],$rowalert['tipo'],$rowalert['lectura']);
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
                        case "<":  
                            if ($valorcal < $rowalert['valor']){
                                $rowalert['operacion'] ="Menor";
                                // Mail alerta
                                $bmail = true;
                            }
                            break;
                    }
                    // Control de filtro de horas
                    if($rowalert['horaminbit'] > time('00:00:00') or $rowalert['horamaxbit'] > time('00:00:00'))
                    {
                        if(time() < $rowalert['horaminbit'] or time() >$rowalert['horamaxbit'])
                        {
                            $bmail = false;
                        }
                    }
                    // Si hay que enviar mail
                    if($bmail) {
                        // Array con string keys.
                        $aalert[$icont]['idusuario']=$rowalert['idusuario'];
                        $aalert[$icont]['TEXTOALERTA']=$rowalert['textalert'];
                        IF(empty($aalert[$icont]['TEXTOALERTA'])){$aalert[$icont]['TEXTOALERTA']=$rowdb['NOMBREP'];}
                        $aalert[$icont]['PREFIJO']=$rowdb['PREFIJO'];
                        $aalert[$icont]['VALOR']=$valorcal;
                        $aalert[$icont]['valory']=$rowalert['valor'];
                        $aalert[$icont]['operacion']=$rowalert['operacion'];
                        $aalert[$icont]['vporcent']="";
                        $icont++;
                    }
                    
                }
            }
            //print_r($aalert);
            // Llamar a la función
            if (sizeof($aalert) > 0) {
                $this->mailalert($aalert);
            }else{
               // echo "No existen filas a tratar.";
            }
        }
    // Datos de la tabla de estimación
    public function checkstimate($idparametro = NULL)
        {
            // Conexiones
            $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname']);
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
                $sselect ="select * from admestimacion where idparametro = ".$idparametro." and valorx=".$vmes;
            }else {
                $sselect ="select * from admestimacion where valorx=".$vmes;
            }
            $sselect .= " ORDER BY idusuario,idparametro,valorx";
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
//                echo 'Valor de diferencia:'.$vdif." / Valor de la alerta:".$vporcent;
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
                        $aalert[$icont]['TEXTOALERTA']=$rowdb['NOMBREP'];
                        $aalert[$icont]['PREFIJO']=$rowdb['PREFIJO'];
                        $aalert[$icont]['VALOR']=$rowdb['VALOR'];
                        $aalert[$icont]['valory']=$rowalert['valory'];
                        $aalert[$icont]['operacion']=$rowalert['operacion'];
                        $aalert[$icont]['poralert']=$rowalert['poralert'];
                        $aalert[$icont]['vdif']=$vdif;
                        $aalert[$icont]['vporcent']=$vporcent;
                        $icont++;
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
    private function mailalert($aalert)
    {
        // Se el pasa $rowvalor: Datos del dia/mes. $row los datos de la alerta.
        // Coger los datos de la instalación.
        $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname']);
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
        // Crear un array con los detalles del correo.
        // Recorrer todas las filas del array y pintar array final
        $afinal = array();
        $iduser = null;
        $icont = 0;
        // Variables de proceso
        $from = "alarmas@riegosolar.net"; // Siempre se tiene que usar el smtp de riegosolar
        $toemail ="";
        $subject="";
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
                    // Se necesita en el header y el adicional from para que el server poxtfix lo recoja.
                    mail($toemail,$subject,$message,$headers,"-f".$from);
                    // Log de mail enviado.
                    $this->logmail($toemail,$subject);
                }
                $iduser = $vfila['idusuario'];
                $sselect ="SELECT i.nombre,i.titular,i.ubicacion,s.nombreserver,s.falta,u.email 
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
                // Always set content-type when sending HTML email
                //$headers = "MIME-Version: 1.0" .PHP_EOL;
                //$headers .= "Content-type:text/html;charset=UTF-8" .PHP_EOL;
                // Siempre mandar desde alertas@riegoslar.net. Se ha configurado el postfix y ssl con el certificado de ese usuario.      
                $headers = "From: Alertas Riegosolar <".$from.">".PHP_EOL;
                $headers .= "MIME-Version: 1.0".PHP_EOL;
                $headers .= "Content-Type: text/html; charset=UTF-8".PHP_EOL;
                //$headers .= 'Cc: myboss@example.com' . "\r\n";
                
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
                $message .='<table>
                <tr><td>Instalación: </td><td>'.$row["nombre"].'</td></tr>
                <tr><td>Titular: </td><td>'.$row["titular"].'</td></tr>
                <tr><td>Ubicación: </td><td>'.$row["ubicacion"].'</td></tr>
                <tr></tr><tr></tr>
                <tr><td>Fecha</td><td>Alerta</td><td>Valor Real</td><td>Valor Esperado</td><td>Calculo</td></tr>';                 
            }
            // Pintar detalles de cada fila
            $message .='<tr>';
            $message .='<td>'.date("d/m/Y").'</td><td>'.$vfila['TEXTOALERTA'].'</td><td ALIGN=RIGHT>'.$vfila['VALOR'].$vfila['PREFIJO'].'</td><td ALIGN=RIGHT>'.$vfila['valory'].$vfila['PREFIJO'].'</td><td ALIGN=RIGHT>'.$vfila['poralert'].$vfila['operacion'].'</td>';
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
        // Se necesita en el header y el adicional from para que el server poxtfix lo recoja.
        mail($toemail,$subject,$message,$headers,"-f".$from);
        $this->logmail($toemail,$subject);
        return 1;
    }
    // Retorna array. Tipo lectura. 0 última,1 diaria,2 mes. Tipo parametro inmediato (M), contador (H)....
    private function valorbd($vparam,$tipolectura,$tipoparametro='M')
        {
            // Conexiones
            $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname']);
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
                // Coger el máximo valor de lectura por tipo parametro
                $sselect ="SELECT * FROM vgrafica ";
                $sselect .=" WHERE idparametro = ".$vparam;
                $sselect .=" order by idlectura desc LIMIT ";
                if($tipoparametro == 'M'){
                    $sselect.=" 1";
                }else{
                    $sselect.=" 2";
                }
                break;
            case 2:
                // Mes actual.
                $sselect ="SELECT NOMBREP,PREFIJO,POSDECIMAL,SUM(VALOR) AS VALOR FROM vgrafica_dias ";
                $sselect.="WHERE idparametro = ".$vparam;
                $sselect.= $sdate;
                $sselect .=" group by idparametro";
                break;
//            case 3:
//                // Año actual
//                $sselect = "SELECT NOMBREP,PREFIJO,POSDECIMAL,SUM(VALOR) AS VALOR FROM vgrafica_dias ";
//                $sselect.="WHERE idparametro = ".$vparam;
//                $sselect.= $sdate;
//                $sselect.=" GROUP BY idparametro";
//                break;
            default:
                // Valor diario
                $sselect = "SELECT NOMBREP,COLOR,PREFIJO,POSDECIMAL,SUM(VALOR) AS VALOR FROM vgrafica_dias ";
                $sselect.="WHERE idparametro = ".$vparam;
                $sselect.= $sdate;
                //echo $sselect;
            }
            // Recuperar array
            echo $sselect;
            $result = $mysqli->query($sselect) or exit("Codigo de error ({$mysqli->errno}): {$mysqli->error}");
            $rowvalor = mysqli_fetch_array($result);
            // Si tiene 2 filas es tipo contador H y se retorna la diferencia de row1 - row2.
            $rowvalor2 = mysqli_fetch_array($result);
            if(!empty($rowvalor2))
            {
                $rowvalor['VALOR'] = $rowvalor['VALOR'] - $rowvalor2['VALOR'];
            }
            print_r($rowvalor);
            // Retorna un array.
            return $rowvalor;
        }
    // Log de mail en mysql
    private function logmail($toemail,$subject)
    {
        $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname']);
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
        
        $sinsert = "INSERT INTO alertserverlog (toemail,subject) VALUES ('".$toemail."','".$subject."')";
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
    //public function getfecha($tipolectura) 
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