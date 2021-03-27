<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TelegramClass
 *
 * @author use
 */
class TelegramClass {
    public function AlertTelegram($aalert,$mysqli) 
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
                $telegrambot="1673994063:AAH2xoLkvydPiIk89p1sEWxQw0LgI-aYQ24";
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
    public function SummaryTelegram($iduser,$subject,$asumaryprod,$mysqli) 
    {
        // Recorrer al array de alertas
        //echo "AlertTelegram funtion.";
        $sselect = "select usuarios.usuario,usuarios.telephone,instalacion.nombre,instalacion.tokenbot from usuarios,instalacion where idusuario=".$iduser;
        $result = $mysqli->query($sselect) or exit("Codigo de error ({$mysqli->errno}): {$mysqli->error}");
        $row = mysqli_fetch_assoc($result);
        //print_r($row);
        // Control de valores
        if(!isset($row['telephone'])) return 0;
        if(!isset($row['tokenbot']))
        {
            $telegrambot="1673994063:AAH2xoLkvydPiIk89p1sEWxQw0LgI-aYQ24";
        }else{
            $telegrambot=$row['tokenbot'];
        }
        $telegramchatid=$row['telephone'];
        if($telegramchatid>0) $telegramchatid=$telegramchatid*(-1);

        //echo $msg;
        $this->telegram($telegrambot,$telegramchatid,$msg);
        //Descrip resumen
        $msg=$subject;
        $this->telegram($telegrambot,$telegramchatid,$msg);
        // Detalles resumen
        $msg="Hoy : ".$asumaryprod[0]['hoy'];
        $this->telegram($telegrambot,$telegramchatid,$msg);
        $msg="Mes actual : ".$asumaryprod[0]['month'];
        $this->telegram($telegrambot,$telegramchatid,$msg);
        $msg="Año ".date('Y')." :".$asumaryprod[0]['year'];
        $this->telegram($telegrambot,$telegramchatid,$msg);
        $msg="Hasta ".date('Y')." :".$asumaryprod[0]['preyear'];
        $this->telegram($telegrambot,$telegramchatid,$msg);
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
}
