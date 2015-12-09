/*
 * =====================
 * Управление постом
 * =====================
 */

$(function() {
    var postController = {
        viewPrevNext: null,
        opening: false, //Признак того, что сейчас загружается пост

        init: function() {
            var $head = $('.'+CONST.POST_HEAD);
            var $ctrl = $('.'+CONST.POST_HEAD_CONTROLS);
            /*
             * Мы рассчитываем на то, что заголовок и контроль для него будут одни.
             * Если их сможет быть несколько, мы всего можем ввести код на уровне кнопок управления.
             */
            if ($head.size()!=1 || $ctrl.size()!=1) return;//---
            
            //Поместим кнопки управления в заголовок
            $head.append($ctrl);
            
            this.postTop = $head;
            this.postControlDiv = $ctrl;
            this.postDiv = $head.parents('.is, .tr, .bp');
            
            //position:absolute отсчитывается от бордера элемента, поэтому сложим "высоту + верхний педдинг + нижний педдинг"
            $ctrl.css('top', $head.height()+$head.cssDimension('padding-top')+$head.cssDimension('padding-bottom'));
                
            this.updateModel = new PsUpdateModel();
                
            this.preProcessButtons();
            //После вычисления доступности кнопок сделаем всю панель видимой при наведении
            $ctrl.setVisibility(true);
                
            $ctrl.children('a').click(function (){
                return postController.executeAction($(this));
            });
        },
        
        /*
         * Фактическое выполнение действия по клику на ссылку.
         * Если возвращается true, то это означает, что нужно прервать стандартное выполнение onClick.
         */
        executeAction: function($a) {
            var executeDefault = false;
            
            if (this.updateModel.isStarted()) {
                return executeDefault;
            }
            
            this.updateModel.start();
            
            var anchor = getHrefAnchor($a);
            
            if (anchor) {
                switch (anchor) {
                    case 'print':
                        popupWindowManager.openWindow('print', 
                        {
                            postId: defs.postId,
                            postType: defs.postType
                        });
                        break;
                    
                    case 'originalView':
                        popupWindowManager.openWindow('postoriginalview', {
                            postId: defs.postId,
                            postType: defs.postType
                        });
                        break;
                    
                    case 'prevNextView':
                        var enabled = !$a.toggleClass('current').hasClass('current');
                        if (enabled) {
                            this.viewPrevNext.hide();
                        } else {
                            if (this.viewPrevNext) {
                                this.viewPrevNext.show();
                            }
                            else
                            {
                                var self = this;
                                var onClose = function() {
                                    $a.removeClass('current');
                                    self.viewPrevNext.hide();
                                }
                                
                                var $load = loadingMessageDiv().insertAfter(this.postTop);
                                this.viewPrevNext = this.getPrevNextHtml(onClose);
                                $load.replaceWith(this.viewPrevNext);
                            }
                        }
                        break;
                }
            }
            else
            {
                //Если пост ещё не загружается - не будем прерывать дефолтное действие (переход по ссылке) и отметим загружаемый пост.
                if(!this.opening) {
                    this.opening = true;
                    executeDefault = true;

                    if (this.viewPrevNext) {
                        /*
                         * Мы кликнули на кнопку перехода к предыдущему/следующему посту.
                         * Остаётся показать индикатор загрузки на одной из обложек.
                         */
                        this.viewPrevNext.find('a[href="'+$a.attr('href')+'"]').click();
                    }
                }
            }
            this.updateModel.stop();
            
            return executeDefault;
        },
        
        /*
         * Строит переключалку: 
         * 
        <table class="fastprev">
            <tr class="covers">
                <td class="prev">
                    {*sprite name='back'*}
                    {img dir='icons' name='back.png'}
                    <div></div>
                </td>
                {foreach $posts as $post}
                    <td>
                        {post_href post=$post}
                        <span class="name">{$post->getName()}</span>
                        {img post=$post dim='156x156'}
                        {/post_href}
                    </td>
                {/foreach}
                <td class="next">
                    {*sprite name='forward'*}
                    {img dir='icons' name='forward.png'}
                    <div></div>
                </td>
            </tr>
        </table>
         */
        
        getPrevNextHtml: function(onClose) {
            
            var $table = $('<table>').addClass('fastprev');
            
            var $closer = crA().addClass('close').html('x').clickClbck(onClose);
            var $switcherText = $('<span>').html('&nbsp;');
            var $switcher = crA().addClass('switcher').toggleClass('disabled', !ClientCtxt.curRubric).append($switcherText).append($closer);
            
            var $box = $('<div>').addClass('fastprev-box').append($table).append($switcher);
            
            var build = function(switcherText, posts) {
                
                if(!$switcher.is('.disabled')) {
                    $switcherText.html(switcherText+' ('+posts.length+')');
                }
                $table.html('');
                
                var $tr = $('<tr>').addClass('covers').appendTo($table);
                $tr.append($('<td>').addClass('prev').append(crIMG('/resources/images/icons/back.png')).append($('<div>')));
                posts.walk(function(post) {
                    $tr.append($('<td>').append(post.a().empty().append($('<span>').addClass('name').html(post.name)).append(crIMG(post.cover156x156))));
                });
                for (var i=posts.length; i<3; i++) {
                    $tr.append($('<td>').append(crA().clickClbck().setVisibility(false)));
                }
                $tr.append($('<td>').addClass('next').append(crIMG('/resources/images/icons/forward.png')).append($('<div>')));
                
                var tds = $table.find('td:not(.prev, .next)');
                var ctrlTds = $table.find('.prev, .next').disableSelection();
                var prevButton = ctrlTds.filter('.prev');
                var nextButton = ctrlTds.filter('.next');
                
                var cur = tds.filter(':has(a[href$="='+ClientCtxt.postId+'"])').find('a').addClass('cur').end();
                //Текущего поста может не быть из-за сбоя в очистке кеша (после показа поста сохранённая навигация не была сброшена)
                var selected = cur.isEmptySet() ? tds.first() : cur;
                
                var posToSelected = function(){
                    //Прячем все колонки
                    tds.removeClass('vis');
                    
                    var next=selected.next();
                    var nextnext=next.next();
                    var prev=selected.prev();
                    var prevprev=prev.prev();
                    
                    selected.addClass('vis');
                    if (next.is(nextButton)){
                        prev.addClass('vis');
                        prevprev.addClass('vis');
                    } else if(prev.is(prevButton)){
                        next.addClass('vis');
                        nextnext.addClass('vis');
                    } else {
                        next.addClass('vis');
                        prev.addClass('vis');
                    }
                    
                    //Разбираемся с кнопками управления
                    ctrlTds.removeClass('disabled');
                    if (next.is(nextButton) || nextnext.is(nextButton)) {
                        nextButton.addClass('disabled');
                    }
                    if (prev.is(prevButton) || prevprev.is(prevButton)) {
                        prevButton.addClass('disabled');
                    }
                    
                    //пересчитаем счётчики перелистывания постов
                    var cnt;
                    
                    if (prevButton.isEnabled()) {
                        cnt = 0;
                        next = prevButton.next();
                        while(!next.is('.vis')){
                            ++cnt;
                            next = next.next();
                        }
                        prevButton.find('div').html(cnt);
                    }
                    
                    if (nextButton.isEnabled()) {
                        cnt = 0;
                        prev = nextButton.prev();
                        while(!prev.is('.vis')){
                            ++ cnt;
                            prev = prev.prev();
                        }
                        nextButton.find('div').html(cnt);
                    }
                }
                
                //Таймер для перелистывания
                var lastCtrlTd = null;
                var timer = new PsTimerAdapter(function() {
                    if (lastCtrlTd) {
                        lastCtrlTd.click();
                    }
                }, 1000);
                
                //Повесим слушатели на кнопки перелистывания
                ctrlTds.clickClbck(function($a){
                    timer.stop();
                    
                    var goNext = $a.is('.next');
                    
                    var next = selected.next();
                    var nextnext = next.next();
                    var prev = selected.prev();
                    var prevprev = prev.prev();
                    
                    if (goNext) {
                        if (next.is(nextButton) || nextnext.is(nextButton)) {
                            return;//---
                        }
                        selected = next;
                    } else {
                        if (prev.is(prevButton) || prevprev.is(prevButton)) {
                            return;//---
                        }
                        selected = prev;
                    }
                    posToSelected();
                    
                    timer.start();
                });
                
                //При наведении на стрелки перелистывания будем автоматически листать прокрутку
                PsJquery.on({
                    ctxt: null,
                    item: ctrlTds,
                    mouseenter: function(e, $td) {
                        lastCtrlTd = $td;
                        timer.start();
                    },
                    mouseleave: function() {
                        lastCtrlTd = null;
                        timer.stop();
                    }
                });
                
                // Поставим защиту от повторных кликов, а также будем показывать картинку - прогресс
                // todo вынести этот дизейбл на утилиты и сделать так-же в мозаичном просмотре
                var $postHrefs = tds.children('a').bind('click', function(e){
                    var $a = $(this);
                    if ($a.is('[href]')) {
                        if ($a.is('.cur'))
                        {
                            //Кликнули по текущей или по той, что сейчас загружается
                            e.preventDefault();
                        }
                        else
                        {
                            //Кликнули по той, которую нужно открыть. Остальные ссылки кастрируем, эту - поставим активной.
                            postController.opening = true;
                            $postHrefs.removeClass('cur');
                            $a.addClass('cur loading').append(crIMG(CONST.IMG_LOADING_PROGRESS, 'loading').addClass('loading'))
                            $postHrefs.filter(':not(.cur)').removeAttr('href');
                            $switcher.addClass('disabled');
                        }
                    }
                });
                
                /* 
                 * selected должен быть центральным элементом
                 */
                if (selected.next().is('.next')) {
                    selected = selected.prev();
                } else if (selected.prev().is('.prev')) {
                    selected = selected.next();
                }
                posToSelected();
            }
            
            /*
             * Если рубрики есть и мы можем переключать вид - сделаем это, иначе просто вешаем заглушку и эмулируем клик.
             * Как минимум один раз мы должны 'кликнуть', так как только по клику происходит построение списка.
             */
            if ($switcher.is('.disabled')) {
                $switcher.clickClbck();
                build(null, ClientCtxt.allPostsAsc);
            } else {
                var state = PsLocalStore.CLIENT.get('fast.prev.state', 0) - 1;//0 - все, 1 - в рубрике
            
                $switcher.clickClbck(function() {
                    if ($switcher.is('.disabled')) return; //Свитчер можно отключить после начала загрузки поста
                    state = ++state%2;
                    PsLocalStore.CLIENT.set('fast.prev.state', state);                
                    switch (state) {
                        case 0:
                            //Все посты
                            build('Все', ClientCtxt.allPostsAsc);
                            break;
                    
                        case 1:
                            //Посты рубрики
                            build(ClientCtxt.curRubric.name, ClientCtxt.curRubric.postsAsc);
                            break;
                
                    }
                }).click();
            }
            
            return $box;
        },
        /*
         * Метод для проверки кнопок, их включения/отключения.
         */
        preProcessButtons: function() {
            var texFormulesCount = this.postDiv.find('.TeX').size();
            var $origViewBtn = this.postControlDiv.extractHrefsByAnchor('originalView');
            if (texFormulesCount==0) {
                $origViewBtn.remove();
            }
            else
            {
                var attrName = $origViewBtn.is('[data-hint]') ? 'data-hint' : 'title'
                var title = $origViewBtn.attr(attrName);
                title += ' (всего формул: %)'.replaceAll('%', texFormulesCount);
                $origViewBtn.attr(attrName, title);
            }
        }
    };
    
    postController.init();
});
