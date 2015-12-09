<?php

class AP_APAudit extends BaseAdminPage {

    public function title() {
        return 'Аудит';
    }

    public function buildContent() {
        $SMARTY['dumps'] = $this->getAuditDumpsInfo();
        $SMARTY['portion'] = PsDefines::getTableDumpPortion();
        return $this->getFoldedEntity()->fetchTpl($SMARTY);
    }

    public function getAuditDumpsInfo() {
        $SMARTY['dumps'] = AdminTableDump::getAllDumpsInfo('ps_audit');
        $dumpsTpl = $this->getFoldedEntity()->getResourcesDm()->getDirItem('src/tpls', 'dumps', PsConst::EXT_TPL);
        return PSSmarty::template($dumpsTpl, $SMARTY)->fetch();
    }

    /** @return AP_APAudit */
    public static function getInstance() {
        return parent::inst();
    }

}

?>