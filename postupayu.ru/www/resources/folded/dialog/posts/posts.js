$(function() {
    var FMANAGER = PsFoldingManager.FOLDING('dg', 'posts');
    var STORE = FMANAGER.store();
    
    //alert(FMANAGER.src('img.png'));
    //alert(FMANAGER.store().get('my param', 'my dflt value'));
    //FMANAGER.store().set('my param', 'my value');

    /*
     * Регистрируем горячую клавишу для открытия окна
     */
    PsFastOpenDialogs.register({
        'Ctrl+Alt+E': {
            id: 'dg-posts-dialog',
            ident: 'posts',
            /*
             * Настройки для HotKeyManager'а
             */
            hotkey: {
                descr: 'Быстрый переход к посту',
                enableInInput: true,
                stopPropagate: true
            },
            /*
             * Настройки самого окна
             */
            wnd: {
                title: 'Загрузить пост',
                buttons: ['Открыть']
            },
            
            onAdd: function(DIALOG) {
                var self = this;
                var $img = DIALOG.div.find('img');
                var $select = DIALOG.div.find('select');
        
                var sync = function(notSaveState) {
                    $img.imgSrc($select.children(':selected').data('cover'));
                    if(notSaveState) return;//---
                    STORE.set('FAST_OPEN_POST', $select.val());
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
                var last = STORE.get('FAST_OPEN_POST');
                if(!last) return;
                DIALOG.doShow.call(this, last);
            },
            
            doAction: function(DIALOG, text) {
                DIALOG.close();
                var $selected = DIALOG.div.find('select').children(':selected');
                var url = $selected.data('url');
                if (defs.url === url) return; //---
                location.href = url;
            }
        }
    });
    
});