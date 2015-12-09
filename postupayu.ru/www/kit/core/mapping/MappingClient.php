<?php

/**
 * Интерфейс для маппинга. Содержит все методы, доступные в клиентской части (не администраторам).
 * 
 * @author azazello
 */
interface MappingClient {

    public function getMappedEntitys($lident);

    public function getAllMappedEntitys(array $lidents);
}

?>
