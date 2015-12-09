var formulaSandboxManager = {
    init: function(){
        this.form = $('#formulaTest');
        if(this.form.isEmptySet()){
            return;
        }
        
        var _this = this;
        
        this.textarea = this.form.find('textarea');

        $(':reset', this.form).click(function (){
            _this.textarea.empty();
            _this.textarea.focus();
        });
        
        var $buttons = this.form.find('.textarea_tools button');
        
        $buttons.first().button().click(function(e){
            e.preventDefault();
            _this.toolFormula(true);
        }).next().button().click(function(e){
            e.preventDefault();
            _this.toolFormula(false);
        });
        
        //Если формула уже введена, отображаем её и ставим на форму фокус
        if(!isEmpty(this.textarea.val())){
            jumpToElement(this.textarea);
            this.textarea.focus();
        }
    },

    toolFormula: function(block){
        var sel = this.textarea.getSelection();
        
        var prefix = '';
        var text = this.textarea.val();
        var suffix = '';
        
        if(!isEmpty(sel.text)){
            prefix = text.substr(0, sel.start);
            suffix = text.substr(sel.end);
            text = sel.text;
        }
        
        if(text.startsWith('\\[') || text.startsWith('\\(')){
            text = text.substr(2);
        }

        if(text.endsWith('\\]') || text.endsWith('\\)')){
            text = text.substr(0, text.length-2);
        }

        this.textarea.val(prefix+'\\'+(block ? '[' : '(') + text + '\\'+(block ? ']' : ')')+suffix).focus();
    }
}

jQuery(function(){
    formulaSandboxManager.init();
});