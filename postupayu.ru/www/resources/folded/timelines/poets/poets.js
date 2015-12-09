$(function() {
    PsTimeLine.create({
        ctxt: null,
        div: 'PoetsTL',
        data: {
            lident: 'poets'
        },
        preProcessData: function(jsonData) {
            jsonData.events.walk(function(event) {
                event.link = '#';
            });
        },
        tds: [
        function($td, tlEvent, CONTROLLER) {
            $td.append(crA('', tlEvent.title).html('загрузить').clickClbck(function() {
                CONTROLLER.showItem(tlEvent);
            }));
        }
        ],
        onHeaderClicked: function(clickEvent, tlEvent, CONTROLLER) {
            clickEvent.preventDefault();
            CONTROLLER.showItem(tlEvent);
        }
    });
});