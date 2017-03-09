<?php
/**
 * Description of ZigbeeClass
 *
 * @author Administrador
 */
class ZigbeeClass {
    // Private vars
    
    // Resetear nodos
    public function resetnodes()
    {
        $sql = "update nodos set estado = 0 where source_addr = '0000'";
    }
}
