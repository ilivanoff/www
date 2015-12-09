$(function() {
    var $BODY = $('.APTables');
    
    /* 
     * Перестановка таблиц местами
     */
    $BODY.find('span.down, span.up').disableSelection().click(function() {
        var $span = $(this);
        var $div = $span.extractParent('.table');
        if ($span.is('.up')) {
            $div.insertBefore($div.prevAll('.table.selected').first());
        }
        if ($span.is('.down')) {
            $div.insertAfter($div.nextAll('.table.selected').first());
        }
    });
    
    /*
     * Включение/выключение таблицы
     */
    var onSelectTableChecked = function() {
        $(this).extractParent('.table').toggleClass('selected', $(this).isChecked());
    };
    $('input:checkbox.selecttable').click(onSelectTableChecked).change(onSelectTableChecked).change();

    /*
     * Включение/отключение настройки таблицы
     */
    var onTablePropChecked = function() {
        $(this).extractParent('div').toggleClass('on', $(this).isChecked());
    };
    $('.table-settings input:checkbox').click(onTablePropChecked).change(onTablePropChecked).change();
    
    /*
     * Выбор элементов
     */
    var $BUTTONS = $BODY.find('.controls button');

    $BUTTONS.first().button({
        text: true,
        icons: {
            primary: 'ui-icon-disk'
        }
    }).click(function() {
        //Можно импортировать настройки. Запросим подтверждение экспорта
        var $tab = $('#APTables-tab>.tab:visible');
        var type = $tab.data('type');
        PsDialogs.confirm('Вы подтверждаете сохранение для <b>'+type+'</b>.', function() {
            doExport(type, $tab);
        });
    });
    
    $BUTTONS.last().button({
        text: false,
        icons: {
            primary: 'ui-icon-refresh'
        }
    }).click(function() {
        $BUTTONS.uiButtonDisable();
        locationReload();
    });
    
    //Экспорт
    var doExport = function(type, $TAB) {
        $BUTTONS.uiButtonDisable();

        var callAjax = function(action, data) {
            data.scope = type.removeLastCharIf('.ini');
            data.action = action;
            AdminAjaxExecutor.execute('TableSettingsEdit', data , function() {
                InfoBox.popupSuccess('Настройки успешно сохранены');
                locationReload();
            }, function(err) {
                InfoBox.popupError(err);
                $BUTTONS.uiButtonEnable();
            });
        }

        /*
         * Сохранение содержимого .ini фалов
         */
        if (type.endsWith('.ini')) {
            //Сохраняем ini файл
            callAjax('saveIni', {
                content: $TAB.find('textarea').val()
            });
            return;//---
        }

        /*
         * Сохранение .ini файлов на освнове настроек
         */
        var tables = {};
        $TAB.find('div.table:has(input.selecttable:checked)').each(function() {
            var $div = $(this);
            //Порядок таблиц
            var table = $div.data('name');
            tables[table] = {};
            tables[table][$('input.selecttable', this).val()] = true;
            //Свойства таблицы
            $div.find('.table-settings input:checkbox:checked').each(function() {
                tables[table][$(this).val()] = true;
            });
            //Свойства столбцов
            $div.find('tbody tr').each(function() {
                var $tr = $(this);
                var type = $tr.data('type');
                if(!type) return; //---
                var colls = [];
                $tr.find('input:checkbox:checked').each(function() {
                    colls.push($(this).val());
                });
                if(!colls.length) return; //---
                tables[table][type] = colls;
            });
        });
        
        callAjax('saveProps', {
            tables: tables
        });

    }
})