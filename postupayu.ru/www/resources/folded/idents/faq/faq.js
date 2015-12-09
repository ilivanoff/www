$(function() {
    /*
     * Регистрируется процессор
     */
    PsIdentPagesManager.register({
        faq: {
            onAdd: function(page) {
                page.div.find('.question .head').disableSelection().click(function() {
                    $(this).next('.body').toggleVisibility();
                });
            }
        }
    });
});