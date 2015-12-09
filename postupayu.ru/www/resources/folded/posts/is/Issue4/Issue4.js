$(function(){
    
    var FOLDING = PsFoldingManager.FOLDING('post-is', 'Issue4');
    
    var issue4Manager = {
        init: function(){
            /*
         * Управление видимостью досье шахматистов
         */
            var champsTableHrefs = $('table.chess_champs a');

            var champsDivs = $('.chess_champs_list');
            var champsInfo = champsDivs.children('.human_info');
            var champsButtons = champsDivs.children('.centered_buttons');
        
            champsTableHrefs.clickClbck(function(anchor) {
                champsTableHrefs.removeClass('as_text');
                this.addClass('as_text');
                champsInfo.hide().filter('#'+anchor).show();
            });
        
            champsButtons.children().first().button({
                icons: {
                    primary: 'ui-icon-arrowthick-1-n'
                }
            }).click(function() {
                champsInfo.hide();
                champsTableHrefs.removeClass('as_text');
            }).next().button({
                icons: {
                    primary: 'ui-icon-arrowthick-1-s'
                }
            }).click(function() {
                champsInfo.show();
                champsTableHrefs.addClass('as_text');
            });
        }
    }

    var issue4anonsTasks ={
        maxTaskNum: 10,
    
        init: function(){
            this.img =$('.anons_carrier img'); 

            $('.anons_control a').click(function(event){
                event.preventDefault();
            
                var cls = $(this).attr('class');
                switch (cls) {
                    case 'prev':
                        issue4anonsTasks.prev();
                        break;
                    case 'ans':
                        issue4anonsTasks.ans();
                        break;
                    case 'next':
                        issue4anonsTasks.next();
                        break;
                }

            });
        
            this.initState();
        },
    
    
    
        initState: function(){
            this.curTaskNum = 1;
            this.setImg();
        },
    
        prev: function(){
            if(--this.curTaskNum < 1){
                this.curTaskNum = this.maxTaskNum;
            }
            this.setImg(false);
        },
    
        ans: function(){
            this.setImg(true);
        },
    
        next: function(){
            if(++this.curTaskNum > this.maxTaskNum){
                this.curTaskNum = 1;
            }
            this.setImg(false);
        },
    
        setImg: function(ans){
            var src = FOLDING.src('task'+this.curTaskNum+(ans ? 'a' : '')+'.png');
            this.img.attr('src', src);
        }
    }

    issue4Manager.init();
    issue4anonsTasks.init();
});