$(function(){

    var issue1Manager = {
        init: function(){
            this.initCoins();
        },
    
        /*
         * Управление монетками
         */
        initCoins: function() {
            var _this = this;
        
            var coins = $('table.coins tr:first td.r1');
        
            var hodes = '';
        
            var button = $('table.coins button').button().click(function(){
                hodes = '';
                coins.removeClass('r2').addClass('r1');
                button.uiButtonRestoreLabel().uiButtonDisable();
            }).uiButtonStoreLabel().uiButtonDisable();
        
            coins.click(function(){
                var $td = $(this);
            
                if(!$td.is('.r1')){
                    return;
                }
            
                var coin = $td.next();
                var count = 0;
            
                while(!coin.isEmptySet()){
                
                    if(coin.is('.r1') || coin.is('.r2')){
                        ++count;
                    }
                
                    if(count==3){
                        break;
                    }
                
                    coin = coin.next();
                }
            
                if (coin.hasClass('r1')) {
                    $td.removeClass('r1');
                    coin.removeClass('r1').addClass('r2');
                    button.uiButtonEnable();
                
                    hodes += coins.index($td) + 1;
                
                    if(!coins.is('.r1')){
                        button.uiButtonLabel('Поздравляю!!!:)');
                        _this.onSuccess(hodes);
                    }
                }
            });
        },
    
        onSuccess: function(hodes) {
            if (PsIs.string(hodes) && hodes.length==4 && hodes!='5123' && hodes!='1352') {
                PsPointsGiverManager.check('up-8monets', {
                    hodes: hodes
                });
            }
        }
    }


    issue1Manager.init();
});