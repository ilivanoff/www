$(function() {
    function PyantashkiPlugin($BOX) {
        var controller = new PsPyatnashki($BOX);
        
        var $controls = $BOX.children('.controls');

        var $hodes = $controls.find('.hodes');
        var $time = $controls.find('.time');
        var time = 0;
        
        function timeUpdate() {
            return $time.html(PsTimeHelper.formatMS(time));
        }

        var $cntSelect = $controls.find('.cnt select').change(function(){
            var cnt = strToInt($cntSelect.val());

            $hodes.html(0);

            interval.stop();
            time = 0;
            timeUpdate();

            controller.init(cnt);
        }).val(4);
        
        var interval = new PsIntervalAdapter(function(){
            ++time;
            timeUpdate();
        }, 1000);
        
        controller.setListener({
            move: function(hodes){
                $hodes.html(hodes);
                if (hodes==1) {
                    interval.restart();
                }
            },
            finish: function(){
                controller.setEnabled(false);
                interval.stop();
            }
        });

        new ButtonsController($controls, {
            on_dice: function(){
                $hodes.html(0);
                
                interval.stop();
                time = 0;
                timeUpdate();

                controller.reinit();
            }
        });

        $cntSelect.change();
    }

    new PyantashkiPlugin($('.pyatnashki_plugin'));
});
