$(function() {
    var process = function($div, onDone, plugins, data) {
        var converter = function(post) {
            var rubric = PsProjectData.getRubric(post);
            return {
                src: post.cover,
                href: post.url,
                caption: post.href,
                title: rubric ? rubric.href : null
            }
        }
        onDone();
        PsContentFlow.appendTo(ClientCtxt.pagePostsAsc, converter, $div);
    }
    
    /*
     * Регистрируется процессор
     */
    PsShowcasesViewController.register({
        flow: process
    });
});