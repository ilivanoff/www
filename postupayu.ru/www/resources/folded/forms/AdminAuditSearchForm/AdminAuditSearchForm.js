$(function() {
    FormHelper.registerOnce({
        form: '#AdminAuditSearchForm',
        onInit: function($form) {
            var $process = $form.find('select[name="process"]');
            $form.find('select[name="action"]').childCombo($process, function(process, $childOptions) {
                return $childOptions.filter('[data-process='+process+'], [value=""]');
            });
        },
        onOk: function() {
        },
        validator: {
            rules: {
                parent_action: {
                    digits: true
                }
            },
            messages: {
                parent_action: {
                    digits: 'Требуется целое число'
                }
            }
        }
    });
});