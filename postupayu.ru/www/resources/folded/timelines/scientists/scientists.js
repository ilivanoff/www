jQuery(function(){
    PsTimeLine.create({
        data: {
            lident: 'scientists'
        },
        div: 'timeline',
        onHeaderClicked: function(clickEvent, evt, CONTROLLER) {
            clickEvent.preventDefault();
            CONTROLLER.showItem(evt);
        },
        tds: [
        function($td, evt, CONTROLLER) {
            if (!evt.link) return;
            /* <a class="details" href="#1">читать здесь</a> */
            $td.html(crA().html('читать здесь').clickClbck(function(){
                CONTROLLER.showItem(evt);
            }));
        },
        function($td, evt) {
            if (!evt.link) return;
            /* <a class="details" href="#1">читать здесь</a> */
            $td.html(crA(evt.link).html('читать на сайте').attr('target', '_blank'));
        }
        ]
    });
});