$(function() {
    FormHelper.registerOnce({
        form: '#pattern',
        onOk: function() {
            return 'Действие выполнено';
        },
        validator: {
            rules: {
                text: {
                    required: true
                }
            },
            messages: {
                text: {
                    required: 'Введите текст'
                }
            }
        }
    });
});