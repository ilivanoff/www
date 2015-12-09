<?php

/**
 * Description of FoldingsStore
 *
 * @author azazello
 */
final class FoldingsProvider extends FoldingsProviderAbstract {

    public static function listFoldings() {
        return array(EmailManagerPs::inst());
    }

}

?>