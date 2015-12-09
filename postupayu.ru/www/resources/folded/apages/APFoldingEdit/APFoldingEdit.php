<?php

class AP_APFoldingEdit extends BaseAdminPage {

    const MODE_FOLDINGS_LIST = 'list';
    const MODE_FOLDING_CONTENT = 'content';
    const MODE_FOLDING_EDIT = 'edit';
    const MODE_FOLDING_LIST_EDIT = 'list_edit';
    const MODE_FOLDING_TPLS_LIST = 'tpls_list';
    const MODE_FOLDING_TPL_EDIT = 'tpl_edit';

    public function title() {
        return 'Редактирование фолдингов';
    }

    public static function url($ftype = null, $fsubtype = null, $fident = null, $flist = null) {
        $params = array();

        if ($ftype instanceof FoldedResources) {
            $params['ftype'] = $ftype->getFoldingType();
            $params['fsubtype'] = $ftype->getFoldingSubType();
            if ($fident) {
                $params['fident'] = $fident;
            }
        } elseif ($ftype instanceof FoldedEntity) {
            $params['ftype'] = $ftype->getFolding()->getFoldingType();
            $params['fsubtype'] = $ftype->getFolding()->getFoldingSubType();
            $params['fident'] = $ftype->getIdent();
        } else {

            if ($ftype) {
                $params['ftype'] = $ftype;
                if ($fsubtype) {
                    //Не забываем, что у фолдинга может и не быть подтипа, поэтому условный оператор не вложенный
                    $params['fsubtype'] = $fsubtype;
                }
                if ($fident) {
                    $params['fident'] = $fident;
                }
            }
        }

        if ($flist) {
            $params['flist'] = $flist;
        }

        return AdminPagesManager::getInstance()->pageUrl(self::getPageIdent(), $params);
    }

    private static function getUrl($mode, array $params = array()) {
        foreach ($params as $key => $value) {
            if ($value instanceof FoldedResources) {
                $params[$key] = $value->getUnique();
            }
            if ($value instanceof FoldedEntity) {
                $params[$key] = $value->getUnique();
            }
        }
        $params['mode'] = $mode;
        return self::pageUrl($params);
    }

    public static function urlFoldingsList() {
        return self::getUrl(self::MODE_FOLDINGS_LIST);
    }

    public static function urlFoldingEntitys($folding) {
        return self::getUrl(self::MODE_FOLDING_CONTENT, array('folding' => $folding));
    }

    public static function urlFoldingEdit($entity) {
        return self::getUrl(self::MODE_FOLDING_EDIT, array('entity' => $entity));
    }

    public static function urlFoldingListEdit($folding, $list) {
        return self::getUrl(self::MODE_FOLDING_LIST_EDIT, array('folding' => $folding, 'list' => $list));
    }

    public static function urlFoldingInfoTplsList($entity) {
        return self::getUrl(self::MODE_FOLDING_TPLS_LIST, array('entity' => $entity));
    }

    public static function urlFoldingTplInfoEdit($entity, FoldedInfoTpl $tpl) {
        return self::getUrl(self::MODE_FOLDING_TPL_EDIT, array('entity' => $entity, 'tplpath' => urlencode($tpl->getInfoRelPath())));
    }

    public function buildContent() {
        $navigation = AdminPageNavigation::inst();

        $RQ = RequestArrayAdapter::inst();

        /** @var FoldedEntity */
        $entity = Handlers::getInstance()->getFoldedEntityByUnique($RQ->str('entity'), false);
        /** @var FoldedResources */
        $folding = $entity ? $entity->getFolding() : Handlers::getInstance()->getFoldingByUnique($RQ->str('folding'), false);

        $mode = $RQ->str('mode', self::MODE_FOLDINGS_LIST);
        $PARAMS['mode'] = $mode;
        $PARAMS['error'] = null;
        $PARAMS['entity'] = $entity;
        $PARAMS['folding'] = $folding;
        $PARAMS['foldings'] = Handlers::getInstance()->getFoldings();


        $ftype = $folding ? $folding->getFoldingType() : '';
        $fsubtype = $folding ? $folding->getFoldingSubType() : '';
        $fident = $entity ? $entity->getIdent() : '';
        $flist = $RQ->str('list');
        $ftplpath = $RQ->str('tplpath');


        //РЕДИРЕКТ
        if ($mode != self::MODE_FOLDINGS_LIST && !$folding) {
            PsUtil::redirectTo(self::urlFoldingsList());
        }


        //ВЫПОЛНЕНИЕ
        switch ($mode) {
            //СПИСОК ВСЕХ ФОЛДИНГОВ
            case self::MODE_FOLDINGS_LIST:
                $navigation->setCurrent('Список фолдингов');
                break;

            //СПИСОК СУЩНОСТЕЙ ДАННОГО ФОЛДИНГА
            case self::MODE_FOLDING_CONTENT:
                $newIdent = $folding->getNextEntityIdent();

                //Форма создания
                $FORM = FORM_AdminFoldingCreateForm::getInstance();
                $FORM->setHidden('folding', $folding->getUnique());

                $FORM->setParam(FORM_AdminFoldingCreateForm::PARAM_NEW_FOLDING_IDENT, $newIdent);

                $FORM->setSmartyParam('folding', $folding);

                /* @var $TABLE PsTable */
                $TABLE = TableExporter::inst()->getTable($folding);
                $FORM->setSmartyParam('table', $TABLE);
                $FORM->setSmartyParam('rec', $folding->getDbRec4Entity($newIdent));
                if (!$TABLE) {
                    $FORM->removeButton(FORM_AdminFoldingCreateForm::BUTTON_SAVE_DB);
                }


                //Форма загрузки
                $FORM = FORM_AdminFoldingUploadForm::getInstance();
                $FORM->setHidden('folding', $folding->getUnique());

                $navigation->addPath(self::urlFoldingsList(), 'Список фолдингов');
                $navigation->setCurrent($folding->getEntityName());

                break;

            case self::MODE_FOLDING_LIST_EDIT:
                $PARAMS['list'] = $flist;
                $PARAMS['listIdents'] = $folding->getPossibleListIdents($flist);

                $navigation->addPath(self::urlFoldingsList(), 'Список фолдингов');
                $navigation->addPath(self::urlFoldingEntitys($folding), $folding->getEntityName());
                $navigation->setCurrent('Редактирование списка ' . $flist);

                break;

            case self::MODE_FOLDING_TPLS_LIST:
                $PARAMS['tplsList'] = $folding->getAllInfoTpls($fident);

                $navigation->addPath(self::urlFoldingsList(), 'Список фолдингов');
                $navigation->addPath(self::urlFoldingEntitys($folding), $folding->getEntityName());
                $navigation->addPath(self::urlFoldingEdit($entity), $fident);
                $navigation->setCurrent('Информационные шаблоны');
                break;

            case self::MODE_FOLDING_TPL_EDIT:
                $PARAMS['tplsList'] = $folding->getAllInfoTpls($fident);

                $tpl = $folding->getInfoTpl($entity->getIdent(), $ftplpath);
                $PARAMS['tpl'] = $tpl;
                //Отфетчим содержимое, извлеча из запроса те параметры, которые начинаются на sm_
                $PARAMS['content'] = $tpl->fetchNoCache($RQ->getByKeyPrefix('sm_', true));

                $FORM = FORM_AdminFoldingInfoTplEditForm::getInstance();
                $FORM->setHidden('fentity', $entity->getUnique());
                $FORM->setHidden('ftpl', $tpl->getInfoRelPath());
                $FORM->setSmartyParam('tpl', $tpl->getDirItem()->getFileContents());

                $navigation->addPath(self::urlFoldingsList(), 'Список фолдингов');
                $navigation->addPath(self::urlFoldingEntitys($folding), $folding->getEntityName());
                $navigation->addPath(self::urlFoldingEdit($entity), $fident);
                $navigation->addPath(self::urlFoldingInfoTplsList($entity), 'Информационные шаблоны');
                $navigation->setCurrent($tpl->getInfoRelPath());
                break;

            case self::MODE_FOLDING_EDIT:
                //Ссылка для скачивания спрайтов
                $download['class'] = 'download';
                $download['data'] = array('ftype' => $ftype, 'fsubtype' => $fsubtype, 'fident' => $fident);
                $PARAMS['download'] = PsHtml::a($download, '[Скачать]');

                $spriteImg = $folding->getSprite($fident);
                $spriteImg = $spriteImg && $spriteImg->getImgDi()->isImg() ? $spriteImg->getImgDi() : null;
                $PARAMS['sprite'] = $spriteImg ? PsHtml::a(array('href' => $spriteImg->getRelPath()), '[Спрайт]', true) : null;

                //Информационные шаблоны
                $PARAMS['patterns'] = '';
                if (count($folding->getInfoDiList($fident))) {
                    $patterns['href'] = self::urlFoldingInfoTplsList($entity);
                    $PARAMS['patterns'] = PsHtml::a($patterns, '[Инфо шаблоны]');
                }

                $PARAMS['info'] = '';
                try {
                    $prew = $folding->getFoldedEntityPreview($fident);
                    if (is_array($prew)) {
                        $PARAMS['info'] = array_get_value('info', $prew);
                        $PARAMS['content'] = array_get_value('content', $prew);
                    } else {
                        $PARAMS['content'] = $prew;
                    }
                } catch (Exception $e) {
                    $PARAMS['content'] = ExceptionHandler::getHtml($e);
                }

                /*
                 * Подготовим форму редактирования фолдинга
                 */
                $FORM = FORM_AdminFoldingEditForm::getInstance();

                /* hiddens */
                $FORM->setHidden('ftype', $ftype);
                $FORM->setHidden('fsubtype', $fsubtype);
                $FORM->setHidden('fident', $fident);

                $rtypes = $folding->getAllowedResourceTypes();

                /* fields */
                foreach ($rtypes as $rtype) {
                    $FORM->setParam($rtype, $folding->getResourceDi($fident, $rtype)->getFileContents(false));
                }

                /* smarty */
                $FORM->setSmartyParam('types', $rtypes);
                $FORM->setSmartyParam('covers', $folding->isImagesFactoryEnabled());

                /* table */
                $TABLE = null;
                $DBROW = null;
                if ($folding->getTableName()) {
                    $TABLE = TableExporter::inst()->getTable($folding->getTableName());
                    $DBROW = $TABLE->getFoldingDbRec($folding, $fident);
                }
                $FORM->setSmartyParam('table', $TABLE);
                $FORM->setSmartyParam('row', $DBROW);

                if (!$DBROW) {
                    $FORM->removeButton(FORM_AdminFoldingEditForm::BUTTON_DELETE_ALL);
                }


                /*
                 * Навигация
                 */
                $navigation->addPath(self::urlFoldingsList(), 'Список фолдингов');
                $navigation->addPath(self::urlFoldingEntitys($folding), $folding->getEntityName());
                $navigation->setCurrent($fident);

                break;
        }

        PsDefines::setReplaceFormulesWithImages(false);

        echo $this->getFoldedEntity()->fetchTpl($PARAMS);
    }

    public function getSmartyParams4Resources() {
        return array('MATHJAX_DISABLE' => true, 'TIMELINE_ENABE' => false);
    }

}

?>