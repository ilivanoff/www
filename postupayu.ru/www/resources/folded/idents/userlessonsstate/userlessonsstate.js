$(function() {
    /*
     * Регистрируется процессор
     */
    PsIdentPagesManager.register({
        userlessonsstate: {
            load: function(callback) {
                var $div = $('<div>');
                $div.append('<h4>В данном разделе Вы можете просматривать и менять статусы уроков</h4>');
                $div.append('<p>Для изменения статуса урока достаточно перетащить его иконку в облать, соответствующую новому состоянию.</p>');
                callback($div);
            },
            onAdd: function(page) {
                trainingsStateManager.makeMediaView(page.div);
            }
        }
    });
});