$(function() {
    var FOLDING = PsFoldingManager.FOLDING('funique', 'pattern');
    
    //alert(FMANAGER.src('img.png'));
    //alert(FMANAGER.store().get('my param', 'my dflt value'));
    //FMANAGER.store().set('my param', 'my value');
    
    
    /*
     * Регистрируется процессор
     */
    /*
     * Объект page, это: 
    page: {
        ident: Тип страницы,
        adds: кол-во добавлений,
        shows: кол-во показов страницы после добавления,
        div: див, возвращённый при вызове load или, если функция load не была объявлена - при загрузке с сервера,
        js: данные javascript, возвращённые с сервера или с помощью функции load
    }
    */
    
    PsIdentPagesManager.register({
        userlessonsstate: {
            load: function(callback) {
            //Самостоятельная загрузка данных
            },
            onAdd: function(page) {
            //Действия, выполняемые при добавлении страницы
            },
            onBeforeShow: function(page) {
            //Действия, выполняемые до показа страницы
            },
            onAfterShow: function(page) {
            //Действия, выполняемые после показа страницы
            }
        }
    });
});