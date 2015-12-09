$(function() {
    FormHelper.registerOnce({
        form: '#AdminFoldingConstructorForm',
        onOk: function() {
            return 'Действие выполнено';
        },
        validator: {
            rules: {
                EntityName: {
                    required: true
                },
                FoldingGroup: {
                    required: true
                },
                FoldingType: {
                    required: true
                },
                FoldingClassPrefix: {
                    required: true
                }
            },
            messages: {
                EntityName: {
                    required: 'Введите название сущности'
                },
                FoldingGroup: {
                    required: 'Укажите группу фолдинга'
                },
                FoldingType: {
                    required: 'Укажите тип фолдинга'
                },
                FoldingClassPrefix: {
                    required: 'Не указан префикс классов'
                }
            }
        }
    });
});