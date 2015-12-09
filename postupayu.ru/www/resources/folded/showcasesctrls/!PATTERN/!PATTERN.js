$(function() {
    
    var FMANAGER = PsFoldingManager.FOLDING('funique', 'eident');
    
    var process = function($div, onDone, plugins, data) {
        
        /*
         * Здесь выполняется основная обработка. Все необходимые элементы добавляются к div.
         */
        $div.append('My custom posts preview is ready, u can do what u want;)');

        /*
         * Здесь вешаются слушатели на плагины, подключённые для этого предпросмотра (если они есть).
         */
        plugins.onInitPluginName = function() {
        }
        plugins.onShowPluginName = function() {
        }
        plugins.onHidePluginName = function() {
        }

        //data.myParam; - данные, возвращаемые ShowcasesControllerItem::getJsParams()
        
        //ClientCtxt.pagePostsAsc - посты данной страницы
        
        /*
         * Оповещаем внешний блок о том, что мы готовы - можно спрятать прогресс и показать
         * построенное нами содержимое.
         */
        onDone();
    }
    
    /*
     * Регистрируется процессор
     */
    PsShowcasesViewController.register({
        pattern: process
    });
});