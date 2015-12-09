$(function() {
    var FMANAGER = PsFoldingManager.FOLDING('funique', 'eident');
    var LOGGER = FMANAGER.logger();
    var STORE = FMANAGER.store();
    
    //alert(FMANAGER.src('img.png'));
    //alert(FMANAGER.store().get('my param', 'my dflt value'));
    //FMANAGER.store().set('my param', 'my value');

    /*
     * Регистрируем горячую клавишу для открытия окна
     */
    PsFastOpenDialogs.register({
        'Ctrl+Alt+?': {
            /*
             * Id диалогового окна, с которым оно будет зарегистрировано в Dialog
             */
            id: 'eunique-dialog',
            /*
             * Идентификатор окна, по которому потом будет произведена загрузка содержимого
             */
            ident: 'eident',
            /*
             * Контекст вызова.
             * Если не указан, то вызов будет произведён в контексте данного объекта
             */
            ctxt: null,
            /*
             * Настройки для HotKeyManager'а
             */
            hotkey: {
                descr: 'Название диалогового окна',
                enableInInput: true,  //Разрешаем в полях ввода
                stopPropagate: true   //Прерываем событие
            },
            /*
             * Настройки самого окна
             */
            wnd: {
                title: 'Заголовок окна',
                buttons: ['Открыть']
            },
            /*
             * Функция вызывается при нажатии горячей клавиши и позволяет отменить дальнейшую обработку
             * @return true - продолжаем обработку
             * @return false - прерываем обработку
             */
            preHotKey: function(DIALOG) {
                return true;
            },
            /*
             * Функция обратного вызова для загрузки содержимого окна
             */
            build: function(onDone, DIALOG) {
                onDone('DivContent', 'isError');
            },
            /*
             * Функция вызывается после загрузки и добавления содержимого окна в DIALOG.div
             */
            onAdd: function(DIALOG) {
                
            },
            /*
             * Функция вызывается при показе окна
             */
            onShow: function(DIALOG) {
                
            },
            /*
             * Функция вызывается при нажатии на кнопку
             */
            doAction: function(DIALOG, text) {
                DIALOG.close();
            }
        }
    });
    
});