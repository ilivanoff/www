<?php

/**
 * Базовая админская страница, доступна всем пользователям.
 *
 * @author Admin
 */
class AP_APCommon extends BaseAdminPage {

    public function title() {
        return 'Базовая страница';
    }

    public function buildContent() {
        echo "<strong>Товарищи админы, будте ооочен аккуратны с админкой</strong>";
    }

}

?>