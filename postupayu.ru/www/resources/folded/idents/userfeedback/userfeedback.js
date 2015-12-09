$(function() {
    
    var $DISCUSSION = null;
    
    //Подпишемся на форму обратной связи, чтобы добавлять сообщения в дерево
    FormHelper.registerListener('FeedbackForm', function(li) {
        if (!$DISCUSSION) return;//---
        PsDiscussion.addRootMsg($DISCUSSION, $(li));
    });

    //Подпишимся на изменения дерева сообщений, чтобы менять кол-во непрочитанных сообщений
    PsDiscussion.connectToEvent(null, PsDiscussion.DiscussionCommentEvent, null, function(event) {
        if (event.action=='known' && event.group=='feed') {
            PsOfficeTools.setCnt('userfeedback', null, -1);
        }
    });

    /*
     * Регистрируется процессор
     */
    PsIdentPagesManager.register({
        userfeedback: {
            onAdd: function(page) {
                $DISCUSSION = page.div.find('ul.discussion');
            }
        }
    });
});