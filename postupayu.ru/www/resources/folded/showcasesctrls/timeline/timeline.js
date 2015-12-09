$(function(){
    var process = function($div, onDone) {
        
        onDone();
                
        PsTimeLine.create({
            ctxt: null,
            data: {
                lident: 'scientists',
                exists: 1
            },
            div: $div,
            onHeaderClicked: function(clickEvent, tlEvent) {
            //this = href
            },
            tds: [
            function($td, event) {
                if (event.link) {
                    $td.html(crA(event.link).html('читать'));
                }
            }
            ]
        });
    }
    
    /*
     * Регистрируется процессор
     */
    PsShowcasesViewController.register({
        timeline: process
    });
});