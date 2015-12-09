$(function() {
    var FMANAGER = PsFoldingManager.FOLDING('dg', 'plugins');
    var STORE = FMANAGER.store();
    
    //alert(FMANAGER.src('img.png'));
    //alert(FMANAGER.store().get('my param', 'my dflt value'));
    //FMANAGER.store().set('my param', 'my value');

    /*
     * Регистрируем горячую клавишу для открытия окна
     */
    PsFastOpenDialogs.register({
        'Ctrl+Alt+R': {
            id: 'dg-plugins-dialog',
            ident: 'plugins',
            /*
             * Настройки для HotKeyManager'а
             */
            hotkey: {
                descr: 'Быстрое открытие плагина',
                enableInInput: true,
                stopPropagate: true
            },
            /*
             * Настройки самого окна
             */
            wnd: {
                title: 'Открыть плагин',
                buttons: ['Открыть']
            },

            onAdd: function(DIALOG) {
                var self = this;
                var $img = DIALOG.div.find('img');
                var $select = DIALOG.div.find('select');
        
                var sync = function(notSaveState) {
                    $img.imgSrc($select.children(':selected').data('cover'));
                    if(notSaveState) return;//---
                    STORE.set('FAST_OPEN_PLUGIN', $select.val());
                }
        
                $select.keyup(sync).change(sync).
                keydown(function(e){
                    sync();
                    if (e.which == 13) {
                        //Enter
                        self.doAction(DIALOG);
                    }
                })
        
                DIALOG.doShow = function(pluginId) {
                    $select.selectOption(pluginId);
                    sync();
                }
        
                sync(true);

            },
            
            onShow: function(DIALOG) {
                var last = STORE.get('FAST_OPEN_PLUGIN');
                if(!last) return;
                DIALOG.doShow.call(this, last);
            },
            
            doAction: function(DIALOG, text) {
                DIALOG.close();
                var $selected = DIALOG.div.find('select').children(':selected');
                var url = $selected.data('url');
                popupWindowManager.openWindow(url);
            }
        }
    });
    
});