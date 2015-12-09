/*
 * Обратная связь
 */

$(function() {
    
    function executeFeedbackAction(action, $a, feedId){
        var $progress = span_progress('Выполняем');
        $a.replaceWith($progress);

        AdminAjaxExecutor.execute('FeedbackAction', {
            id: feedId,
            action: action
        }, function() {
            $progress.extractParent('li.msg').addClass('deleted');
        }, $progress)
    }

    
    PsJquery.onHrefClick('ul.discussion li .pscontrols>a.delete', {
        yes: function($a, feedId) {
            executeFeedbackAction('delete', $a, feedId);
        }
    });

});
