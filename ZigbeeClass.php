<?php
/**
 * Description of ZigbeeClass
 *
 * @author Administrador
 */
class ZigbeeClass {
    // Private vars
    private $segscan=15;
    // Resetear nodos
    public function resetnodes()
    {
        $sql = "update nodos set estado = 0 where source_addr = '0000'";
    }
    // Actualizar nodos
    public function updatenodes()
    {
        // Control post
        for($i =0;$i <count($_POST['nombre_nodo']);$i++)
        {
            $rescan = 0;
            //echo "Formulario update_nodo pulsado.".$_POST['nombre_nodo'][$i];
            //echo "Se actualizará el nodo Nº:".$_POST['idnodo'][$i]."\n";
            $mysqli = new mysqli($_SESSION['serverdb'],$_SESSION['dbuser'],$_SESSION['dbpass'],$_SESSION['dbname']);
            if ($mysqli->connect_errno)
            {
                echo $mysqli->host_info."\n";
                return -1;
            }
            // Preparar sentencia
            $stmt = $mysqli->prepare("UPDATE nodos SET nombre_nodo = ?, 
                source_addr = ?, 
                source_addr_long = ?,  
                node_identifier = ?,  
                parent_address = ?,
                device_type = ?,
                estado = ?
                WHERE idnodo = ?");
           // echo "stmt preparado correctamente.";
            $stmt->bind_param('ssssssii',
                $_POST['nombre_nodo'][$i],
                $_POST['source_addr'][$i],
                $_POST['source_addr_long'][$i],
                $_POST['node_identifier'][$i],
                $_POST['parent_address'][$i],
                $_POST['device_type'][$i],
                $_POST['estado'][$i],
                $_POST['idnodo'][$i]);
            //echo "stmt bind_param correcto.";
            // Ejecutar
            $stmt->execute();
            // Finalizar
            $stmt->close();

            //echo "El estado es:".$_POST['estado'][$i];
            if ($_POST['estado'][$i] == 0) {
               $rescan = 1;
            }
        }
        // Control de reescaneo por todos los nodos solo 1 sleep.
        if ($rescan == 1) {
            echo "Reescaneo de nodos.Se ha definido un tiempo espera de ".$this->segscan." segundos.\n";
            echo date('h:i:s') . "\n";
            sleep($this->segscan);
            echo date('h:i:s') . "\n";
        }
    }
// end class
}
