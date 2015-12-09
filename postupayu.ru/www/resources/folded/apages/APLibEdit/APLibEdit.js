$(function() {
    var $page = $('.APLibEdit');
    var $table = $page.find('table.editable');
    
    if ($table.isEmptySet()) return;//Мы на просмотре списка библиотек
    
    var fsubtype = $table.data('fsubtype');
    
    $table.psEditableGrid({
        saver: function(CONTROLLER, onDone) {
            var models = CONTROLLER.models();
            AdminAjaxExecutor.execute('LibEdit', {
                fsubtype: fsubtype,
                models: models
            }, onDone, onDone)
        },
        reload: false
    });
});