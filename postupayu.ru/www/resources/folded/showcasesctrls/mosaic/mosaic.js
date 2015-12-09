$(function(){

    /*
        <div class="covers">
            {foreach $posts as $post}
                {post_href post=$post}
                <span class="name">{$post->getName()}</span>
                {img post=$post dim='156x156'}
                {/post_href}
            {/foreach}
            <div class="clearall"></div>
        </div>
        */

    var process = function($div, onDone, plugins) {
        var $divCalendar = $('<div>').addClass('ps-centered-datepicker').hide().appendTo($div);
        var $divCovers = $('<div>').addClass('covers').appendTo($div);
            
        var minDate = null, maxDate = null, dates = {};
        ClientCtxt.pagePostsDesc.walk(function(post) {
            //Обложки
            var $href = post.a().empty();//Сразу сотрём текст
            $href.append($('<span>').addClass('name').html(post.name));
            $href.append(crIMG(post.cover156x156).addClass('cover'));
            $divCovers.append($href);
                
            //Даты
            minDate = post.pdate_dmy;
            maxDate = maxDate ? maxDate : minDate;
            dates[minDate] = dates[minDate] ? dates[minDate].add($href) : $href;
        });

        var $hrefs = $divCovers.children('a').bind('click', function(e){
            if($hrefs.is('.cur'))
            {
                //Повторный клик - потопим событие
                e.preventDefault();
            }
            else 
            {
                $(this).addClass('cur loading').append(crIMG(CONST.IMG_LOADING_PROGRESS, 'loading').addClass('loading'));
                //У остальных ссытлок - удалим аттрибут href вообще
                $hrefs.filter(':not(.cur)').removeAttr('href');
            }
        });
            
        //Подключим плагины
        var curDate = null;
        var onSelect = function(newDate) {
            if(!curDate && !newDate) {
                return;//--
            }
            newDate = newDate instanceof Date ? newDate.format('dd-mm-yyyy') : newDate;
            if (curDate===newDate) return;//---
            curDate = newDate;
                
            if(!curDate) {
                $hrefs.show();
                return;//---
            }
                
            $hrefs.hide();
            dates[curDate].show();
        }
            
        plugins.onInitCalendar = function() {
            $divCalendar.datepicker({
                changeMonth: 0,
                changeYear: 0,
                stepMonths: 1,
                numberOfMonths: 3/*,
                showCurrentAtPos: 1*/,
                showButtonPanel: true,
                showOn: 'focus',
                minDate: minDate,
                maxDate: maxDate,
                beforeShowDay: function(date) {
                    var dateText = date.format('dd-mm-yyyy');
                    var canShow = dates.hasOwnProperty(dateText);
                    var tooltip = canShow ? 'Постов: '+dates[dateText].size() : '';
                    return [canShow, '', tooltip];
                },
                onSelect: onSelect
            });
        /*
                 * Теперь нам нужно добиться того, чтобы по умолчанию никакая дата выбрана не была
                 * defaultDate=null не помогает...
                 * $divCalendar.datepicker('setDate', null);
                 * $divCalendar.find('.ui-state-active').removeClass('ui-state-active');
                 */
        }
        plugins.onShowCalendar = function() {
            $divCalendar.slideToggle('fast');
            onSelect($divCalendar.datepicker('getDate'));
        }
        plugins.onHideCalendar = function() {
            $divCalendar.slideToggle('fast');
            onSelect(null);
        }
            
        onDone();
    }
    
    /*
     * Регистрируется процессор
     */
    PsShowcasesViewController.register({
        mosaic: process
    });
});