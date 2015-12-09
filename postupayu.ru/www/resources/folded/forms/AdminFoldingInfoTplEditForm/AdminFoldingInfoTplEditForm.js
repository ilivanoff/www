$(function() {
    FormHelper.registerOnce({
        form: '#AdminFoldingInfoTplEditForm',
        onOk: function() {
            return 'Действие выполнено';
        }
    });
});