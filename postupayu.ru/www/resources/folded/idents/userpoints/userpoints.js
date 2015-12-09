$(function() {
    /*
     * Регистрируется процессор
     */
    PsIdentPagesManager.register({
        userpoints: {
            onAfterShow: function() {
                //Если мы показали страницу, то пользователь знает обо всех очках
                PsOfficeTools.setCnt('userpoints');
            }
        }
    });
});