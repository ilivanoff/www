$(function() {
    /*
     * Регистрируем горячую клавишу для открытия окна
     */
    var HOTKEY = 'Ctrl+Alt+S';
    var PARAMS = {};
    PARAMS[HOTKEY] = {
        id: 'dg-hotkeys-dialog',
        ident: 'hotkeys',
        /*
         * Настройки для HotKeyManager'а
         */
        hotkey: {
            descr: 'Список горячих клавиш',
            enableInInput: true,
            stopPropagate: true
        },
        /*
         * Настройки самого окна
         */
        wnd: {
            title: 'Список горячих клавиш'
        },
        
        build: function(onDone, DIALOG) {
            PsHotKeysManager.getInfo().walk(function(ob) {
                var $a = crA('#'+ob.key).html(ob.key).clickClbck(function(key) {
                    if(key==HOTKEY) return;//---
                    DIALOG.close();
                    PsHotKeysManager.process(key);
                });
                DIALOG.div.append($('<div>').append($a).append(' &mdash; ' + ob.descr));
            });
            onDone();
        }
    };
    
    PsFastOpenDialogs.register(PARAMS);
    
});