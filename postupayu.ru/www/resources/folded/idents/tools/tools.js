$(function() {
    /*
     * Регистрируется процессор
     */
    PsIdentPagesManager.register({
        tools: {
            onAdd: function(page) {
                if (page.adds > 1) return; //Нас интересует только первое добавление
                PsLocalBus.connect(PsLocalBus.EVENT.FAVORITES, function() {
                    PsIdentPagesManager.reloadPage(page.ident);
                });
            },
            
            onAfterShow: function(page) {
                if(!defs.isAuthorized || page.shows > 1) return; //Нас интересует только первый показ
        
                var $tools = page.div.extractTarget('.tools');
                if ($tools.isEmptySet()) return; //---
                var $ctrl = page.div.extractTarget('.tools-ctrl');
                if ($ctrl.isEmptySet()) return; //---
        
                var $sortable = $tools
                /*.children().sameHeight().end()*/
                .children().height(function(){
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
        
                var ItemsManager = {
                    sortableSetEnabled: function(enabled) {
                        $sortable.uiSortableSetEnabled(enabled);
                    },
                    sortableIsEnabled: function() {
                        return $sortable.uiSortableIsEnabled();
                    },
                    states: [],
                    getStates: function() {
                        return $tools.children().toArray();
                    },
                    saveStates: function() {
                        this.states = this.getStates();
                    },
                    restoreStates: function() {
                        this.states.walk(function(item) {
                            $tools.append(item);
                        });
                    },
                    isStateChanged: function() {
                        return !this.getStates().equals(this.states, function(i1, i2) {
                            return $(i1).data('type')==$(i2).data('type') && $(i1).data('ident')==$(i2).data('ident');
                        });
                    },
                    getStates4send: function() {
                        var states = [];
                        this.getStates().walk(function(i) {
                            states.push({
                                type: $(i).data('type'),
                                ident: $(i).data('ident')
                            })
                        })
                        return states;
                    }
                }
                ItemsManager.saveStates();
        
                //Кнопки
                $ctrl.buttonset();
        
                var $btns = function() {
                    return $ctrl.extractTarget('button');
                }
        
                var $btn = function(cl) {
                    return $btns().filter('.'+cl);
                }
        
                var isSortable = false;
        
                function setSortable(sortable) {
                    isSortable = sortable;
                    $btn('sort').
                    uiButtonLabel(sortable ? 'Сохранить' : 'Сортировать').
                    uiButtonIcons('ui-icon-'+(sortable ? 'disk' : 'carat-2-n-s')).
                    uiButtonEnable();
            
                    $btn('cancel').uiButtonSetEnabled(sortable);
                    $tools.toggleClass('sortable', sortable);
                    ItemsManager.sortableSetEnabled(sortable);
            
                    if(!sortable) {
                        ItemsManager.restoreStates();
                    }
                }
        
                $btn('sort').button({
                    text: false
                }).click(function() {
                    if(isSortable) {
                        //SAVE
                        if(!ItemsManager.isStateChanged()) {
                            setSortable(false);
                            return;//---
                        }
                
                        ItemsManager.sortableSetEnabled(false);
                        $btns().uiButtonDisable();
                        AjaxExecutor.execute('UserToolsOrder', {
                            states: ItemsManager.getStates4send()
                        }, function() {
                            ItemsManager.saveStates();
                            setSortable(false);
                        }, function(err) {
                            InfoBox.popupError(err);
                            setSortable(false);
                        });
                    } else {
                        setSortable(!isSortable);
                    }
                });
        
                $btn('cancel').button({
                    text: false,
                    icons: {
                        primary: 'ui-icon-cancel'
                    }
                }).click(function() {
                    setSortable(false);
                });
        
                setSortable(false);
            }
        }
    });
});