/*
 * Менеджер калькулятора
 */

var calculatorManager = {
    emptySpan: $('<span>').addClass('gray').html("Введите выражение и начните расчёт"),

    init: function(){
        this.form = $('form');
        if(this.form.isEmptySet()){
            return;
        }
        
        var _this = this;

        this.textarea = $('textarea', this.form);
        /*
         * todo сделать галочку 'рассчитывать автоматически'
         *
        this.textarea.keyup(function(){
            _this.doEval();
        });
        */
        this.content = $('div.result .content', this.form);

        $(':submit', this.form).click(function (event){
            event.preventDefault();
            _this.doEval();
        });

        $(':reset', this.form).click(function (){
            _this.clearResult();
        });

        this.clearResult();
    },

    doEval:function() {
        var value = this.textarea.val();

        if(isEmpty(value)){
            this.content.html(this.emptySpan.clone());
        }
        else{
            var result = null;
            var error = null;
            try
            {
                result = PsMathEval.eval(value);
            }
            catch (err)
            {
                error = err;
            }

            if(typeof(result)=='number' || typeof(result)=='boolean'){
                this.content.html(result.toString());
            }
            else if(result instanceof Array){
                this.content.html('['+result+']');
            }
            else{
                this.content.html(span_error(error ? "Ошибка обработки выражения: "+error : 'Неприемлимый результат операции'));
            }

        }
        this.textarea.focus();
    },

    clearResult: function(){
        this.textarea.val('');
        this.doEval();
    }

}

jQuery(function(){
    calculatorManager.init();
});