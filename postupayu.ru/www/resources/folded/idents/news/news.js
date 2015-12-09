$(function() {
    //Менеджер для работы с новостями
    var PsNewsManager = {
        logger: PsLogger.inst('PsNewsManager').setDebug()/*.disable()*/,
    
        init: function(jsParams) {
            var _this = this;
        
            this.updateNewsCount();
        
            var $loadDiv = $('#load_news');
            if ($loadDiv.isEmptySet()) return;
        
            this.states = jsParams.states;
            this.logger.logInfo('Initial states: {}', PsObjects.toString(this.states));
        
            $loadDiv.children('button').button({
                icons: {
                    primary: 'ui-icon-signal-diag'
                }
            }).click(function() {
                var $button = $(this);
            
                $button.uiButtonDisable();
            
                AjaxExecutor.execute('LoadNews', {
                    ctxt: _this,
                    states: _this.states
                }, function(news) {
                    $(news.line).insertBefore($loadDiv);
                    $.extend(this.states, news.states);
                    this.logger.logInfo('Loaded states: {}, merged states: {}. Has more: {}.', PsObjects.toString(news.states), PsObjects.toString(this.states), news.has_more);
                    $button.setVisible(news.has_more).uiButtonSetEnabled(news.has_more);
                    this.updateNewsCount();
                }, $button);
        
            });
        },
    
        updateNewsCount: function() {
            var cnt = $('.date_news .news_list>li').size();
        
            $('#news_cnt').html(cnt);
        
            if (cnt==0) return;//---
        
            var $div = $('#ps-news-datepicker');
            
            if ($div.isEmptySet()) return;//---
        
            if(!$div.is('.hasDatepicker')) {
                $div.datepicker({
                    changeMonth: 0,
                    changeYear: 0,
                    stepMonths: 1,
                    numberOfMonths: 3/*,
                showCurrentAtPos: 1*/,
                    showButtonPanel: true,
                    showOn: 'focus'
                });
            }
        
            var maxDate, minDate, dates = {};
            $('.date_news').each(function() {
                var $el = $(this);
                var date = $el.data('date');
                maxDate = maxDate ? maxDate : date;
                minDate = date;
                var cnt = $el.find('.news_list>li').size();
                dates[date]= {
                    el: $el,
                    cnt: cnt
                };
            });
        
            $div.datepicker('option', 'minDate', minDate);
            $div.datepicker('option', 'maxDate', maxDate);
            $div.datepicker('option', 'beforeShowDay', function(date) {
                var dateText = date.format("dd-mm-yyyy");
                var canShow = dates[dateText] && !!dates[dateText].cnt;
                var tooltip = canShow ? 'Новостей: '+dates[dateText].cnt : '';
                return [canShow, '', tooltip];
            });
            $div.datepicker('option', 'onSelect', function(dateText) {
                PsScroll.jumpTo(dates[dateText].el);
            });
        
            $div.datepicker('refresh');
        }
    }

    /*
     * Регистрируется процессор
     */
    PsIdentPagesManager.register({
        news: {
            onAdd: function(page) {
                PsNewsManager.init(page.js);
            }
        }
    });
});