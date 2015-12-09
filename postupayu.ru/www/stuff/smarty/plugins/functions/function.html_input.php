<?php

/*
 * type
 * id
 * val
 * label
 */

function smarty_function_html_input($params, Smarty_Internal_Template & $smarty) {
    $adapter = ArrayAdapter::inst($params);

    $fieldId = $adapter->get('id');
    $label = $adapter->get('label');
    $value = $adapter->get('val');
    $inputType = $adapter->str('type');
    $help = $adapter->str('help');

    $RQ = PostArrayAdapter::inst();

    $attrs = array();

    switch ($inputType) {
        case 'hidden':
            echo PsHtml::hidden($fieldId, $value);
            break;

        case 'text':
            echo PsHtmlForm::text($label, $fieldId, $RQ->str($fieldId, $value), $attrs, $help);
            break;

        case 'datetime':
            $attrs['class'][] = 'ps-datetime-picker';
            echo PsHtmlForm::text($label, $fieldId, $RQ->str($fieldId, $value), $attrs, $help);
            break;

        case 'pass':
            echo PsHtmlForm::password($label, $fieldId, $RQ->str($fieldId));
            break;

        case 'file':
            $label = $adapter->get('label', 'Файл');
            $fieldId = $fieldId ? $fieldId : FORM_PARAM_FILE;
            echo PsHtmlForm::file($label, $fieldId, $help);
            break;

        case 'user':
            $label = $adapter->get('label', 'Ваше имя');
            if (AuthManager::isAuthorized()) {
                echo PsHtmlForm::textInfo($label, PsUser::inst()->getName());
            } else {
                $fieldId = $fieldId ? $fieldId : FORM_PARAM_NAME;
                echo PsHtmlForm::text($label, $fieldId, $RQ->str($fieldId), $attrs, $help);
            }
            break;

        case 'textarea':
            $label = $adapter->get('label', 'Текст сообщения');
            $fieldId = $fieldId ? $fieldId : FORM_PARAM_COMMENT;
            $value = $value ? $value : $RQ->str($fieldId, $value);
            $maxlen = $adapter->str(array('maxlen', 'maxlength'));
            $manual = $adapter->bool('manual');
            $codemirror = $adapter->str('codemirror');

            echo PsHtmlForm::textarea($label, $fieldId, $value, $maxlen, $manual, $codemirror, $attrs, $help);
            break;

        case 'submit':
            $buttons[] = $label ? $label : 'Отправить';

            for ($idx = 0; $idx <= 10; $idx++) {
                $button = $adapter->get("label$idx");
                if ($button) {
                    $buttons[] = $button;
                }
            }

            $canReset = $adapter->bool('reset');

            echo PsHtmlForm::submit($buttons, $canReset);
            break;


        case 'yesno':
            $fieldId = $fieldId ? $fieldId : 'yesno';

            $options[] = PsHtml::comboOption(0, 'Нет');
            $options[] = PsHtml::comboOption(1, 'Да');
            echo PsHtmlForm::select($label, $fieldId, $attrs, $options, 0);
            break;

        case 'sex':
            $value = $value ? $value : $RQ->int($fieldId);
            $options[] = PsHtml::comboOption(SEX_GIRL, 'Женский');
            $options[] = PsHtml::comboOption(SEX_BOY, 'Мужской');
            echo PsHtmlForm::select($label, $fieldId, $attrs, $options, $value);
            break;

        case 'posttype':
            $label = $label ? $label : 'Тип поста';
            $fieldId = $fieldId ? $fieldId : FORM_PARAM_POST_TYPE;

            $options = array();
            /* @var $pr PostsProcessor */
            foreach (Handlers::getInstance()->getPostsProcessors() as $type => $pr) {
                $title = $pr->postTitle();
                $options[] = PsHtml::comboOption($type, "$title ($type)");
            }
            echo PsHtmlForm::select($label, $fieldId, $attrs, $options);
            break;

        case 'select':
            echo PsHtmlForm::select($label, $fieldId, $attrs, $adapter->arr('options'), $adapter->str('curVal'), $adapter->bool('hasEmpty'), $help);
            break;

        case 'timezone':
            $label = $label ? $label : 'Выберите временную зону';
            $tzSelect = PsTimeZone::inst()->zonesSelectHtml();
            echo PsHtmlForm::field($label, $tzSelect, $help);
            break;

        default:
            raise_error("Unsupported html input type: [$inputType]");
    }
}

?>