$(function() {
    
    var $table = $('.mappings');
    
    var $mappingSelect = $('#mapping-select');
    
    var $lsrcInfo = $('#left-source');
    var $rsrcInfo = $('#right-source');
    
    var $leftList = $('#left-list');
    var $rightList = $('#right-list');
    
    var $buttons = $('button');
    var $buttonSave = $buttons.filter('.save');
    var $buttonReload = $buttons.filter('.update');
    var $buttonClean = $buttons.filter('.clean');

    var $disabled = null;
    var disable = function() {
        if($disabled) return;
        $disabled = $table.activeFormInputs();
        $disabled.disable();
        $buttonReload.uiButtonDisable();
    }
    var enable = function() {
        if(!$disabled) return;
        $disabled.enable();
        $disabled = null;
        $buttonReload.uiButtonEnable();
    }
    
    var ulSortable = function(idents, $parent, converter, sortable) {
        var $ul = $('<ul>').addClass('ps-sortable-base').appendTo($parent.empty());
        idents.walk(function(ident) {
            converter(ident, $('<label>').appendTo($('<li>').appendTo($ul)).disableSelection());
        });
            
        $ul.children('li').sameHeight();
        
        if (sortable) {
            $ul.sortable({
                axis: 'y',
                distance: 5,
                placeholder: "placeholder",
                start: function(event, ui) {
                    ui.placeholder.height(ui.item.height());
                    ui.item.addClass('move');
                },
                stop: function(event, ui) {
                    ui.item.removeClass('move');
                },
                change: function() {
                    $buttonSave.uiButtonEnable();
                }
            });
        }
        
        return $ul;
    }
    
    $mappingSelect.change(function() {
        disable();
        var mhash = $(this).val();
        PsLocalStore.ADMIN.set('mhash', mhash);
        AdminAjaxExecutor.execute('MappingAction', {
            action: 'load_left',
            mhash: mhash
        }, function(data) {
            $lsrcInfo.html(data.lsrc);
            $rsrcInfo.html(data.rsrc);
            
            var $ul = ulSortable(data.lidents, $leftList, function(lident, $label) {
                var ident = lident[0];
                var count = lident[1];
                $label.append($('<input>').attr({
                    name: 'leftlist',
                    value: ident,
                    type: 'radio'
                })).append(' ' + ident).append(count ? $('<span>').html('['+count+']').addClass('count') : '');
            });
            
            $buttonClean.uiButtonSetEnabled(!$ul.find('span.count:not(:contains(0))').isEmptySet());

            var $radios = $ul.find(':radio');
            $radios.change(function() {
                var $radio = $(this);
                var lident = $radio.val();
                //InfoBox.popupInfo(val);
                PsLocalStore.ADMIN.set('lident', lident);
                
                $radio.extractParent('li').siblings('li').removeClass('selected').end().addClass('selected');
                
                disable();
                $buttonSave.uiButtonDisable();
                AdminAjaxExecutor.execute('MappingAction', {
                    action: 'load_right',
                    mhash: mhash,
                    lident: lident
                }, function(items) {
                    ulSortable(items, $rightList, function(item, $label) {
                        var ident = item[0];
                        var checked = !!(1*item[1]);
                        var known = !!(1*item[2]);
                        
                        var $check = $('<input>').attr({
                            name: 'rightlist',
                            value: ident,
                            type: 'checkbox'
                        }).setChecked(checked);
                        
                        $check.change(function() {
                            var isChecked = $(this).is(':checked');
                            var modified = checked!=isChecked;
                            $label.toggleClass('modified', modified);
                            $buttonSave.uiButtonSetEnabled(!$rightList.find('.modified').isEmptySet());
                        });
                        
                        $label.append($check).append(' ' + ident+' '+(known ? '' : '[unknown]'));
                    }, true);

                    enable();
                }, 'Загрузка содержимого правой панели');
                
            });

            enable();
            
            var lastRadio = PsLocalStore.ADMIN.get('lident');
            var $lastRadio = lastRadio ? $radios.filter('[value="'+lastRadio+'"]') : null;
            $lastRadio = PsIs.empty($lastRadio) ? $radios.filter(':first') : $lastRadio;
            $lastRadio.setChecked(true).change();

        }, 'Загрузка содержимого левой панели');
    });
    
    //Создание нового списка
    $buttonSave.button().uiButtonConfirm(function() {
        $buttons.uiButtonDisable();
        
        var mhash = $mappingSelect.val();
        var lident = $leftList.find(':checked').val();
        var ridents = [];
        $rightList.find(':checked').each(function() {
            ridents.push($(this).val())
        });

        AdminAjaxExecutor.execute('MappingAction', {
            action: 'save',
            mhash: mhash,
            lident: lident,
            ridents: ridents
        }, function() {
            InfoBox.popupSuccess('Маппинг успешно сохранён');
            $mappingSelect.change();
        }, function(err) {
            InfoBox.popupError(err);
        }, function() {
            $buttons.uiButtonEnable();
        });
    }).uiButtonDisable();

    $buttonReload.button({
        text: false,
        icons: {
            primary: 'ui-icon-refresh'
        }
    }).click(function() {
        $mappingSelect.change();
    });


    $buttonClean.button().uiButtonConfirm(function() {
        var mhash = $mappingSelect.val();

        AdminAjaxExecutor.execute('MappingAction', {
            action: 'clean',
            mhash: mhash
        }, function() {
            InfoBox.popupSuccess('Маппинг успешно очищен');
            $mappingSelect.change();
        }, function(err) {
            InfoBox.popupError(err);
        }, function() {
            $buttons.uiButtonEnable();
        });
    }).uiButtonDisable();

    $mappingSelect.val(PsLocalStore.ADMIN.get('mhash')).change();

});