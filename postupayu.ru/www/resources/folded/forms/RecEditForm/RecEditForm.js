$(function() {
    FormHelper.registerOnce({
        form: '#RecEditForm',
        single: null,
        onConfirm: function(button) {
            return button.startsWith('Удалить') ? 'Подтвердите удаление' : null;
        }
    });
});