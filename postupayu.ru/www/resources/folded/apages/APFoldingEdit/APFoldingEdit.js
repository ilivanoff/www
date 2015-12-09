$(function() {
    var store = PsLocalStore.inst('APFoldingEdit')
    
    //Переключение вида короткий/полный
    function PostPrevTrigger(){
        var isShort = store.has('admin_short_post');
        
        var $toggler = $('.APFoldingEdit .ap_controls a').clickClbck(function(){
            isShort = $('.preview').toggleClass('short').hasClass('short');
            store.set('admin_short_post', isShort ? 1 : null);
        });
        
        $('.preview').toggleClass('short', isShort);
        
        PsHotKeysManager.addListener('Ctrl+Alt+F', {
            f: function() {
                $toggler.first().click();
            },
            descr: 'Короткий/полный вид',
            enableInInput: true,
            stopPropagate: true
        });
    
    }
    new PostPrevTrigger();
    
    /*
     * Редактирование списка
     */
    var $sortable = $('.sortable-list-content').children().height(function(){
        //Фиксируем высоту, чтобы дивы не "плыли" при перетаскивании
        return $(this).height();
    }).end().
    sortable({
        axis: "y",
        placeholder: "placeholder",
        start: function(event, ui) {
            ui.placeholder.height(ui.item.height());
            ui.item.addClass('move');
        },
        stop: function(event, ui) {
            ui.item.removeClass('move');
        }
    });
    
    //Сохранение содержимого списка
    var $saveListBtn = $('#save-list').button().click(function() {
        $saveListBtn.uiButtonDisable();
        
        var data = {};
        $sortable.children('li:has(:checkbox:first:checked)').each(function() {
            var $li = $(this);
            var ident = $.trim($li.text());
            data[ident] = $li.has(':checkbox:last:checked').isEmptySet() ? 0 : 1;
        });
        
        AdminAjaxExecutor.execute('FoldingAction', {
            unique: $sortable.data('unique'),
            list: $sortable.data('list'),
            data: data,
            action: 'save_list'
        }, function() {
            InfoBox.popupSuccess('Список успешно сохранён');
            locationReload();
        }, function(err) {
            $saveListBtn.uiButtonEnable();
            InfoBox.popupError(err);
        });
    }).uiButtonEnable();
    
    
    //Загрузка
    $('.APFoldingEdit .download').clickClbck(function(){
        AdminAjaxExecutor.execute('ExportFoldingZip', this.data(), function(url) {
            PsUtil.showFileSaveDialog(url);
        }, 'Выгрузка в zip');
    });
    
    
    //Редактирование информационных шаблонов
    var $InfoPattenrCtrl = $('.APFoldingEdit #tpl-smarty-params');
    if(!$InfoPattenrCtrl.isEmptySet()) {
        var $ipinput = $InfoPattenrCtrl.children('input');
        
        $InfoPattenrCtrl.children('button').click(function() {
            var params = PsUrl.getParams2Obj($.trim($ipinput.val()));
            if(!PsObjects.hasKeys(params)) {
                InfoBox.popupWarning('Не переданы параметры');
                return;//---
            }
            
            var finalParams = {};
            PsObjects.keys2array(params).walk(function(k) {
                finalParams[k.ensureStartsWith('sm_')] = params[k];
            });
            
            if(!PsUrl.redirectToPathAddParams(finalParams)) {
                InfoBox.popupWarning('Текущие параметры совпадают с установленными');
            }
        });
        
        //Получим все параметры, начинающиеся на sm_, и установим их в поле по умолчанию
        var initParams = [];
        $.trim(location.search).removeFirstCharIf('?').split('&').walk(function(pair) {
            if (pair.startsWith('sm_')) {
                initParams.push(pair);
            }
        });
        
        PsScroll.jumpBottom();
        
        $ipinput.val(initParams.join('&')).focus();
    }
});