PsLocalStore.ADMIN = PsLocalStore.inst('admin');

var AdminAjaxExecutor = {
    execute: function (action, data, callback, callbackErr, callbackAfter) {
        AjaxExecutor.scheduleExecute({
            url: 'admin/ajax/Action.php',
            data: data,
            type: 'GET',
            action: action,
            clbcOk: callback,
            clbcErr: callbackErr,
            clbcAfter: callbackAfter
        });
    },
    executePost: function (action, data, callback, callbackErr, callbackAfter) {
        AjaxExecutor.scheduleExecute({
            url: 'admin/ajax/Action.php',
            data: data,
            type: 'POST',
            action: action,
            clbcOk: callback,
            clbcErr: callbackErr,
            clbcAfter: callbackAfter
        });
    }
}


$(function() {
    //Кнопка "Выход"
    $('.adminControls a.logout').one('click', function(event) {
        event.preventDefault();
        $(this).addClass('processing');
        $.get('ajax/Logout.php', locationReload);
    });
    
    //Прокрутка
    PageScroller('#header', '#carrier');
});

/*
 * Редактирование навигации
 */
$(function() {
    
    var $ul = $('#adminPages');
    var $ulEdit = null;
    
    var UM = new PsUpdateModel(null, 
        function() {
            if ($ulEdit) {
                $ulEdit.find('button').uiButtonDisable();
                $ulEdit.uiSortableDisable();
                $ulEdit.find('input').disable();
            }
        }, function() {
            if ($ulEdit) {
                $ulEdit.find('button').uiButtonEnable();
                $ulEdit.uiSortableEnable();
                $ulEdit.find('input').enable();
            }
        });
    
    //Редактировние структуры страниц
    $('.adminControls a.edit').clickClbck(function() {
        if (UM.isStarted()) return;//----
        if ($ulEdit) {
            //Был включен режим редактирования
            $ulEdit.remove();
            $ulEdit = null;
            $ul.attr('id', 'adminPages').show();
            this.removeClass('active');
            return;//---
        }
        
        this.addClass('active');
            
        //У оригинального меню мы удаляем id, прячем его и после него добавляем редактируемый вариант меню
        $ulEdit = $ul.clone(true, true).addClass('editable').sortable({
            axis: 'y',
            items: '> li:not(.nosort)'
        }).insertAfter($ul.hide().removeAttr('id'));
            
        //Поставим заглушки на ссылки для открытия страниц
        $ulEdit.find('a').clickClbck();
            
        //Функция добавления групп
        var $grLi = function(val) {
            return $('<li>').append($('<input>').attr({
                value: $.trim(val),
                type: 'text'
            })).append(crA('#', 'Удалить группу').html('&minus;').addClass('remove').clickClbck(function() {
                if(UM.isStarted()) return;//---
                this.extractParent('li').remove();
            }));
        }
        
        //Названия всех групп сделаем редактируемыми
        $ulEdit.find('li.level1').each(function() {
            var $li = $(this);
            if ($li.hasChild('a')) return;
            var name = $li.text();
            $li.replaceWith($grLi(name));
        });
        
        //Добавим кнопки создания новых групп и сохранения разметки
        $ulEdit.append($('<li>').addClass('nosort buttons ps-ui-btn-small').
            append(
                $('<button>').button({
                    label: 'Добавить группу',
                    text: false,
                    icons: {
                        primary: 'ui-icon-plus'
                    }
                }).click(function() {
                    $grLi().insertBefore($(this).extractParent('li'));
                })).
            append(
                $('<button>').button({
                    label: 'Сохранить',
                    text: false,
                    icons: {
                        primary: 'ui-icon-disk'
                    }
                }).click(function() {
                    PsDialogs.confirm('Подтвердите сохранение', function() {
                        UM.start();

                        var menu = [];
                        $ulEdit.children('li:not(.nosort, :first)').each(function() {
                            var $li = $(this);
                            //Группа?
                            var $gr = $li.find('input');
                            if(!$gr.isEmptySet()) {
                                if (!PsIs.empty($gr.val())) {
                                    menu.push([0, $gr.val()]);
                                }
                                return;//---
                            }
                            //Ссылка на страницу?
                            var $page = $li.find('a');
                            if(!$page.isEmptySet()) {
                                menu.push([1, PsObjects.getValue(PsUrl.getParams2Obj($page.attr('href')), 'page')]);
                                return;//---
                            }
                        });
                        
                        AdminAjaxExecutor.execute('SaveMenuAction', {
                            menu: menu
                        }, function(menu) {
                            InfoBox.popupSuccess('Меню успешно сохранено');
                            $ul.remove();
                            $ul = $(menu).insertAfter($ulEdit);
                            UM.stop();
                            $('.adminControls a.edit').click();
                        }, function(err) {
                            InfoBox.popupError('Меню сохранено c ошибкой:<br/>'+err);
                            UM.stop();
                        });
                    });
                    
                }))
            );
           
    });

});