<?php

class SmartyBlockContext extends AbstractContext {

    const HAS_TOOL_BODY = 'HAS_TOOL_BODY';
    const MULTIPLE_ANSWERS = 'MULTIPLE_ANSWERS';
    const ALTERNATE_SOLUTION_NUM = 'ALTERNATE_SOLUTION_NUM';
    const CHOICE_OPTION = 'CHOICE_OPTION';

    public function registerBlock($content, $__FUNCTION__) {
        $contextParams = null;
        if ($content === null) {
            $this->setContext(PSSmartyTools::getFunctionName($__FUNCTION__));
            $this->LOGGER->info('{' . $this->getContextIdent() . '}');
        } else {
            $this->LOGGER->info('{/' . $this->getContextIdent() . '}');
            $contextParams = $this->getParams();
            $this->dropContext();
        }
        return $contextParams;
    }

    public function isParentBlock($name) {
        return $this->isSetted($name);
    }

    public function getParentBlock($blockNames, $__FUNCTION__, $mandatory) {
        $blockNames = to_array($blockNames);

        foreach ($blockNames as $name) {
            if ($this->hasContext($name)) {
                return $name;
            }
        }

        if ($mandatory) {
            $text = "Function $__FUNCTION__ must have one of this parents: " . implode(',', $blockNames);
            $this->LOGGER->info($text);
            check_condition(false, $text);
        }

        return null;
    }

    public function getParentBlockSetVirtualCtxt($blockNames, $__FUNCTION__, $mandatory) {
        $block = $this->getParentBlock($blockNames, $__FUNCTION__, $mandatory);
        if ($block) {
            $this->setVirtualContext($block);
        }
        return $block;
    }

    public function hasParentBlock($blockName) {
        $parent = $this->getParentBlock($blockName, null, false);
        return $parent !== null;
    }

    public function hasParentBlockSetVirtualCtxt($blockName) {
        $parent = $this->getParentBlock($blockName, null, false);
        if ($parent) {
            $this->setVirtualContext($parent);
        }
        return $parent !== null;
    }

    /** @return SmartyBlockContext */
    public static function getInstance() {
        return parent::inst();
    }

}

?>
