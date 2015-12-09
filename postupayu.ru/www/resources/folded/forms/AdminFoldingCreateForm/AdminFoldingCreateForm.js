$(function() {
    //Форма созадния/загрузки
    FormHelper.registerOnce({
        form: '#AdminFoldingCreateForm',
        onOk: function(res) {
            //Перенаправим страницу на форму редактирования нового фолдинга
            return  {
                msg: 'Операция успешно выполнена',
                url: res.url
            };
        },
        onConfirm: function(button, data) {
            return 'Подтвердите создание фолдинга с идентификатором <b>'+data.new_folding_ident+'</b>';
        }
    });
});
