$(function() {
    //Форма редактирования фолдинга
    FormHelper.registerOnce({
        form: '#AdminFoldingEditForm',
        onConfirm: function(button) {
            return button == 'Сохранить' ? null : 'Подтвердите удаление';
        },
        onOk: function(ok) {
            return {
                msg: null,
                url: ok=='OK' ? null : ok //Может быть возвращён URL для перехода после сохранения формы
            } ;
        }
    });
});