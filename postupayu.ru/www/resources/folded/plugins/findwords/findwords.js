$(function() {
    
    var findWordsManager = {
        init: function() {
            var _this = this;
        
            this.table = $('.findwords');
        
            this.table.find('tr:not(.ctrl)').each(function(){
                var $tr = $(this);
                var $input = $tr.find('input');
                var attr = $input.attr('ans');
                if(attr){
                    attr = attr.toUpperCase();
                    $input.data('answer', attr).removeAttr('ans').val('');
                
                    $tr.find('.col1').click(function(){
                        $(this).parents('tr:first').find('input').focus();
                    });
                }
                else{
                    $tr.remove();
                }
            });
        
            /*
         * Слушатель печати в поле
         */
            this.table.find('input').keyup(function(){
                _this.checkInput($(this));
            });
        
            /*
         * Слушатель клика на "Показать ответ"
         */
            this.table.find('span.ans').click(function(){
                var $input = $(this).parents('tr:first').find('input');
                $input.val($input.data('answer'));
                _this.checkInput($input);
            });
        
            /*
         * Слушатель клика на "Смешать"
         */
            this.table.find('tr.ctrl a').click(function(event){
                event.preventDefault();
                _this.shuffle($(this).parents('table:first'));
            });
        
            this.shuffle(this.table);
        },
    
        shuffle: function(table){
            table.each(function(){
                $(this).find('tr:not(.ctrl)').removeClass('correct').shuffle().each(function(){
                    var $tr = $(this);
                    var $input = $tr.find('input').val('');
                    $tr.find('.col1').html($input.data('answer').shuffle());
                });
            }); 
        },
    
        checkInput: function($input){
            var $tr = $input.parents('tr:first');

            var inputVal = jQuery.trim($input.val());
            var correct = inputVal.toUpperCase() == $input.data('answer');

            if(correct){
                $tr.addClass('correct');
            }
            else{
                $tr.removeClass('correct');
            }
        
        }
    }


    findWordsManager.init();
});
