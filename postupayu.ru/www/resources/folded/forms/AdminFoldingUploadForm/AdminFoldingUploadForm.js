$(function() {
    FormHelper.registerOnce({
        form: '#AdminFoldingUploadForm',
        onOk: 'Действие выполнено',
        onOk: function(res) {
            //Перенаправим страницу на форму редактирования обновлённого фолдинга
            window.location.href = res.url;
            return  'Операция успешно выполнена';
        }
    });
});