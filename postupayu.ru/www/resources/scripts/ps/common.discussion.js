/*
 * ===============================================
 * = Базовый функционал для работы с дискуссиями =
 * ===============================================
 */
var PsDiscussion = {
    logger: PsLogger.inst('PsDiscussion').setDebug()/*.disable()*/,

    init: function() {
        
        //Надпись 'Пока сообщений нет'
        $('ul.discussion:has(li:visible:not(.no_comments))').livequery(function(){
            $(this).find('.no_comments').remove();
        });
        
        $('ul.discussion:not(:has(li:visible, li.msg, li.no_comments))').livequery(function(){
            $(this).prepend($('<li>').addClass('no_comments').html('Пока сообщений нет'));
        });
        
        //Удалённые сообщения
        $('ul.discussion li.deleted:not(:has(li))').livequery(function(){
            var $li = $(this);
            var $ul = $li.parent('ul');
            var $discussion = $ul.extractParent('ul.discussion');
            $li.fadeOut('fast', function(){
                $li.remove();
                if (!$ul.is('.discussion') && !$ul.hasChild()) $ul.remove();
                PsDiscussion.fireDiscussionChangeEvent($discussion);
            });
        });
        
        $('ul.discussion li.deleted:has(li)').livequery(function(){
            var $comment = $(this).children('div.comment:first').children('.comment-content');
            $comment.find('.pscontrols').remove();
            $comment.find('.vote').remove();
            $comment.children('h4').remove();
            $comment.find('.comment-text').html($('<span>').addClass('deleted').html('Сообщение удалено'));
        });
        
        //Верхние кнопки управления сообщением (переход к родительскому сообщению)
        PsJquery.on({
            ctxt: this,
            item: 'ul.discussion li>.comment .meta .controls>a',
            click: function(e, $a) {
                e.preventDefault();
                var $liMsg = $a.extractParent('li.msg');
                PsScroll.scrollTo($a.is('.parent') ? PsDiscussion.getParentLiComment($liMsg) : $liMsg);
            }
        });
        
        //Отметка родительских сообщений
        PsJquery.on({
            ctxt: this,
            item: 'ul.discussion li>.comment',
            mouseenter: function(e, $comment) {
                PsDiscussion.clearParentMark($comment);
                var $parentComment = this.getParentLiComment($comment.parent('li'));
                if ($parentComment) $parentComment.addClass('parent');
            },
            mouseleave: function(e, $comment) {
                PsDiscussion.clearParentMark($comment);
            }
        });
        
        //Инициализируем менеджера управления сообщениями в дереве
        PsDiscussionMsg.init();
        
        //Кнопки управления сообщениями (клик по ссылкам в сообщениях)
        PsJquery.onHrefClick('ul.discussion.default li .pscontrols>a.delete', {
            msg: 'Удалить?',
            ctxt: this,
            data: 'delete',
            yes:  this.executeAction
        });
        
        PsJquery.onHrefClick('ul.discussion.default li .pscontrols>a.confirm', {
            msg: null,
            ctxt: this,
            data: 'confirm',
            yes:  this.executeAction
        });
        
        PsJquery.onHrefClick('ul.discussion.default li .pscontrols>a.reply', {
            msg: null,
            ctxt: this,
            data: 'reply',
            clbk:  this.executeAction
        });
        
        PsJquery.onHrefClick('ul.discussion.default li .comment a.known', {
            msg: null,
            ctxt: this,
            data: 'known',
            clbk:  this.executeAction
        });

        PsJquery.onHrefClick('ul.discussion.default li .comment .vote>a.clickable.like', {
            msg: null,
            ctxt: this,
            data: 'like',
            clbk:  this.executeAction
        });
        
        PsJquery.onHrefClick('ul.discussion.default li .comment .vote>a.clickable.dislike', {
            msg: null,
            ctxt: this,
            data: 'dislike',
            clbk:  this.executeAction
        });

        PsJquery.onHrefClick('ul.discussion.default li .comment .vote>a.clickable.votes', {
            msg: null,
            ctxt: this,
            data: 'unvote',
            clbk:  this.executeAction
        });
    },
    
    /**
     * Обработчик клика по ссылке, выполняющей действия над комментарием.
     * Нужно помнить, что данное событие должно быть транслировано во все деревья дискуссий,
     * в которых есть этот комментарий.
     */
    executeAction: function($href, anchor, action) {
        var $comment = $href.extractParent('div.comment');
        var msgUnique = $comment.data('unique');
        
        if (action=='reply') {
            PsDiscussionMsg.fireTreeEvent($comment, PsDiscussionMsg.makeEvent(msgUnique, action));
            return;//---
        }

        if (this.isLocked()) return;//---

        this.lock()

        var request = this.gatherData($comment.extractParent('ul.discussion'));
        
        $.extend(request, {
            ctxt: this,
            action: action,
            msgId: this.getCommentId($comment)
        });
        
        var $progress = null;
        var $votes = null;
        switch (action) {
            case 'known':
                $progress = $('<span>').addClass('known').insertAfter($href.hide());
                break;
            case 'delete':
            case 'confirm':
                $progress = span_progress('Выполняем').insertAfter($href.hide());
                break;
            case 'like':
            case 'dislike':
            case 'unvote':
                $href.parent().children('a').removeClass('clickable active');
                $votes = ($href.is('.votes') ? $href : $href.siblings('.votes')).addClass('progress');
                break;
        }
        
        AjaxExecutor.execute('DiscussionAction', request, function(response) {
            //Разблокируем дерево
            this.unlock();
            
            //Фактическое выполнение действия
            switch (action) {
                case 'delete':
                    //Подтвердим
                    PsDiscussionMsg.makeEventAndDo(msgUnique, 'confirm');
                    //Мы о нём знаем (админ может удалить сообщение, написанное ему)
                    PsDiscussionMsg.makeEventAndDo(msgUnique, 'known');
                    //Все дочерние сообщения должны быть отмечены, как прочтённые
                    response.known.walk(function(childUnique) {
                        PsDiscussionMsg.makeEventAndDo(childUnique, 'known', null, true);
                    });
                    //Добавляем класс, всё остальное будет сделано в основном менеджере
                    PsDiscussionMsg.makeEventAndDo(msgUnique, 'remove');
                    //В случае удаления не нужно удалять прогресс, он будет обработан выше
                    break;

                case 'confirm':
                    //Удаляем прогресс
                    $progress.remove();
                    //Подтвердим
                    PsDiscussionMsg.makeEventAndDo(msgUnique, 'confirm');
                    //Отметим, как прочитанный
                    PsDiscussionMsg.makeEventAndDo(msgUnique, 'known');
                    break;
                    
                case 'known':
                    //Удаляем прогресс
                    $progress.remove();
                    //Подтвердим
                    PsDiscussionMsg.makeEventAndDo(msgUnique, 'confirm');
                    //Отметим, как прочитанный
                    PsDiscussionMsg.makeEventAndDo(msgUnique, 'known');
                    break;
                
                case 'like':
                    //Удаляем прогресс
                    $votes.removeClass('progress');
                    //Лайкнем сообщение
                    PsDiscussionMsg.makeEventAndDo(msgUnique, 'like', response);
                    break;
                
                case 'dislike':
                    //Удаляем прогресс
                    $votes.removeClass('progress');
                    //ОтЛайкнем сообщение
                    PsDiscussionMsg.makeEventAndDo(msgUnique, 'dislike', response);
                    break;
                
                case 'unvote':
                    //Удаляем прогресс
                    $votes.removeClass('progress');
                    //Отметим, что мы не голосовали за сообщение
                    PsDiscussionMsg.makeEventAndDo(msgUnique, 'unvote', response);
                    break;
            }

        }, function(error) {
            InfoBox.popupError(error);
            if ($votes) $votes.removeClass('progress');
            if ($progress) $progress.remove();
            this.unlock();
        });
    },
    
    /*
     * События
     */
    DiscussionChangeEvent: 'DiscussionChangeEvent',
    DiscussionCommentEvent: 'DiscussionCommentEvent',

    /**
     * Метод выбрасывает событие изменения структуры дерева, а именно - в него были добавлены,
     * или из него были удалены ветки li.
     * Вызывается, например, при создании комментариев, их дозагрузки или удалении из дерева.
     */
    fireDiscussionChangeEvent: function($discussions) {
        $discussions.each(function() {
            var $discussion = $(this);

            var newState = {
                roots: $discussion.children('li.msg').size(),
                total: $discussion.find('li.msg').size()
            }

            //Загрузим старое состояние
            var oldState = $discussion.data('discussion_state');
            
            //Установим новое состояние
            $discussion.data('discussion_state', newState);

            //Установим новое кол-во комментариев
            $discussion.siblings('h2').children('span.mcount').html(' {'+newState.total+'}');
            
            //Были ли добавлены комментарии?
            if (newState.total > (oldState ? oldState.total : 0)) {
                MathJaxManager.updateFormules();
            }
        
            PsDiscussion.logger.logDebug('Событие {}: {} отправлено слушателям дерева #{}', PsDiscussion.DiscussionChangeEvent, PsObjects.toString(newState), $discussion.attr('id'));
            $discussion.trigger(jQuery.Event(PsDiscussion.DiscussionChangeEvent, newState));
        });
    },
    
    /**
     * Подписывание на собятия данного дерева.
     * Если $discussion=null, то мы подпишемся сразу на все деревья и будем получать события,
     * возникающие в кажом из деревьев на странице.
     */
    globalListeners: new ObjectsStore(), //Слушатели всех деревьев
    connectToEvent: function($discussion, eventType, ctxt, callback) {
        if ($discussion==null) {
            this.globalListeners.putToArray(eventType, {
                ctxt: ctxt,
                f: callback
            });
        } else {
            $discussion.bind(eventType, function(event) {
                callback.call(ctxt, event);
            });
        }
    },
    
    callGlobalListeners: function(eventType, processor) {
        this.globalListeners.doIfHas(eventType, processor);
    },
    
    //Удаляет у комментария отметку о том, что он является родительским в данный момент
    clearParentMark: function($item) {
        $item.extractParent('.discussion').find('div.comment.parent').removeClass('parent');        
    },
    
    //Собирает информацию о дискуссии
    gatherData: function($discussion) {
        return {
            updown: $discussion.data('updown'),
            unique: $discussion.data('unique'),
            thread: $discussion.data('thread'),
            themed: $discussion.data('themed')
        }
    },
    
    //Добавляет сообщение в корень
    addRootMsg: function ($discussion, $li) {
        var data = this.gatherData($discussion);
        data.updown ? $discussion.append($li) : $discussion.prepend($li);
        this.fireDiscussionChangeEvent($discussion);
    },
    
    /*
     * Lock дискуссии
     */
    locked: false,
    lock: function() {
        this.locked = true;
    },
    
    unlock: function() {
        this.locked = false;
    },
    
    isLocked: function() {
        if (this.locked) InfoBox.popupWarning('Дождитесь окончания выполнения действия');
        return this.locked;
    },
    
    /*
     * Утилитные методы для работы с деревом
     */
    getLiComment: function($li){
        return $li.children('.comment:first');
    },
    
    getParentLi: function($li){
        var $ul = $li.parent('ul:not(.discussion)');
        return $ul.isEmptySet() ? null : $ul.parent('li');
    },
    
    getParentLiComment: function($li){
        $li = this.getParentLi($li);
        return $li ? this.getLiComment($li) : null;
    },
    
    getCommentId: function($div) {
        var parts = $.trim(PsIs.jQuery($div) ? $div.data('unique') : $div).split('-');
        return parts.length > 1 ? parts[parts.length-1] : null;
    }
}


/**
 * Менеджер, предоставляющий функционал для выполнения действий над сообщениями в дискуссиях.
 * Все эти действия должны транслироваться в другие закладки браузера, в которых открыт наш сайт.
 */
var PsDiscussionMsg = {
    init: function() {
        PsLocalBus.connect(PsLocalBus.EVENT.DISCUSSIONMSG, function(EVENT, sameSession) {
            if (sameSession) return;//---
            EVENT = this.deserializeBusEvent(EVENT);
            PsDiscussion.logger.logInfo('Событие {} получено из локальной шины.', EVENT);
            this.doEvent(EVENT);
        }, this);
    },
    
    /*
     * Истолнители событий.
     * Действие будет выполнено над всеми сообщениями на странице, после чего транслировано:
     * 1. Слушателям деревьев
     * 2. Глобальным слушателям
     * 3. В локальную шину
     * 
     * Нужно понимать, что событие может произойти на другой закладке и прийти из шины, при этом 
     * сообщения на текущей странице может не быть. Пример - изменение счётчика непрочитанных сообщений.
     * В таком случае это событие будет проброшено только глобальным слушателям.
     */
    DOEVENTS: {
        remove: function(EVENT) {
            var $comments = $('ul.discussion.default li.msg:not(".deleted")>div.comment.'+EVENT.msgUnique);
            //Отправим события
            PsDiscussionMsg.fireGlobalEvent($comments, EVENT);
            //Добавим нужный класс, чтобы сообщение было подчищено
            $comments.parent('li').addClass('deleted');
        },
        confirm: function(EVENT) {
            var $comments = $('ul.discussion.default div.comment.not_confirmed.'+EVENT.msgUnique);
            //Очистим сообщение
            $comments.removeClass('not_confirmed').find('.pscontrols>a.confirm').remove();
            //Отправим событие всем слушателям
            PsDiscussionMsg.fireGlobalEvent($comments, EVENT);
        },
        known: function(EVENT) {
            var $comments = $('ul.discussion.default div.comment.'+EVENT.msgUnique+':has(a.known)');
            //Очистим сообщения
            $comments.find('a.known').remove();
            //Отправим событие всем слушателям
            PsDiscussionMsg.fireGlobalEvent($comments, EVENT);
        },
        /* Голосование */
        _doVote: function(EVENT, voteProcessor) {
            //Текущее кол-во лайков
            var likes = EVENT.data;
            //Все комментарии с этим unique
            var $comments = $('ul.discussion.default li.msg>div.comment.'+EVENT.msgUnique+':has(.vote)');
            //Получим блок с кнопками голосования
            var $vote = $comments.find('.vote');
            //Произведём первичную обработку кнопок управления
            var $hoteHrefs = $vote.children('a').removeClass('active clickable green red');
            //Установим текущее кол-во лайков
            $hoteHrefs.filter('a.votes').html(Math.abs(likes)).addClass(likes==0 ? '' : (likes>0 ? 'green' : 'red'));
            //Установим всем соответствующие статусы
            voteProcessor($hoteHrefs);
            //Отправим событие всем слушателям
            PsDiscussionMsg.fireGlobalEvent($comments, EVENT);
        },
        like: function(EVENT) {
            this._doVote(EVENT, function($hrefs) {
                $hrefs.filter('a.like').addClass('active').end().
                filter('a.votes').addClass('clickable').end().
                filter('a.dislike').addClass('clickable');
            });
        },
        dislike: function(EVENT) {
            this._doVote (EVENT, function($hrefs) {
                $hrefs.filter('a.like').addClass('clickable').end().
                filter('a.votes').addClass('clickable').end().
                filter('a.dislike').addClass('active');
            });
        },
        unvote: function(EVENT) {
            this._doVote(EVENT, function($hrefs) {
                $hrefs.filter('a.like').addClass('clickable').end().
                filter('a.votes').end().
                filter('a.dislike').addClass('clickable');
            });
        }
    },
    
    /**
     * Метод обрабатывает событие
     */
    doEvent: function(EVENT) {
        var action = EVENT.action;
        var executor = this.DOEVENTS[action];
        if (!$.isFunction(executor)) {
            PsDiscussion.logger.logError('Нет обработчика для события {}!', EVENT);
            return;//---
        }
        executor.call(this.DOEVENTS, EVENT);
    },
    
    /** 
     * Метод создаёт событие для сообщения в дискуссии
     *
     * Параметр ensure означает:
     * "транслировать событие только в том случае, если такие сообщения были найдены на странице".
     * Например - подтверждение комментария ведёт также и к его прочтению, но мы не уверены, что комментарий
     * может быть отмечен как "прочтённый", тогда просто передаём ensure=true.
     * 
     * Уверенными можно быть только в событиях, которые:
     * 1. действительно случились с событиями дерева
     * 2. пришли из локальной шины
     * 3. пришли с сервера (подтверждение дочерних комментариев при удалении родительского и т.д.)
     */
    makeEvent: function(msgUnique, action, data, ensured, fromBus) {
        var parts = msgUnique.split('-', 4);//msg-post-is-1
        return {
            type: PsDiscussion.DiscussionCommentEvent,
            group: parts[1],
            subgroup: parts[2],
            action: action,
            data: data,
            msgUnique: msgUnique,
            /*Признаки события*/
            fromBus: !!fromBus,
            ensured: !!ensured || !!fromBus,
            /*Методы*/
            toString: function() {
                return PsObjects.toStringData(this);
            },
            //При клонировании мы возвращаем только данные события и toString,
            //удаляя всякие методы для работы с состоянием события
            clone: function() {
                return PsObjects.clone(this, ['fromBus', 'ensured'], true, ['toString', 'clone']);
            },
            //Клонирует событие для откправки в дерево.
            clone4$comment: function($comment) {
                //Раз есть сообщение, значит событие подтверждено.
                this.ensured = true;
                //Склонируем событие и наполним нужными DOM элементами
                var EVENT = PsObjects.clone(this, ['fromBus', 'ensured'], true);
                EVENT.discussion = $comment.extractParent('ul.discussion');
                EVENT.comment = $comment;
                EVENT.li = $comment.parent('li');
                EVENT.toString = function() {
                    return PsObjects.toStringData(EVENT, ['discussion', 'comment', 'li']);
                }
                return EVENT;
            }
        }
    },
    
    makeEventAndDo: function(msgUnique, action, data, ensured) {
        this.doEvent(this.makeEvent(msgUnique, action, data, ensured));
    },
    
    deserializeBusEvent: function(event) {
        return this.makeEvent(event.msgUnique, event.action, event.data, true, true);
    },
    
    /**
     * Метод отправляет событие для слушателей дерева, в котором есть переданные комментарии.
     * @return возвращён будет массив кодов комментариев, по которым было отправлено событие.
     */
    fireTreeEvent: function($comments, EVENT) {
        $comments.each(function() {
            //Поработаем над событием
            var TREE_EVENT = EVENT.clone4$comment($(this));
            //Отправим событие слушателям $discussion
            PsDiscussion.logger.logInfo('Событие {} отправлено слушателям дерева #{}', TREE_EVENT, TREE_EVENT.discussion.attr('id'));
            TREE_EVENT.discussion.trigger(jQuery.Event(TREE_EVENT.type, TREE_EVENT));
        });
    },
    
    fireGlobalEvent: function($comments, EVENT) {
        //Сначала пробросим событие в дерево
        this.fireTreeEvent($comments, EVENT);
        
        //Соберём состояние из события
        var ensured = EVENT.ensured;
        var fromBus = EVENT.fromBus;
        
        //Если событие небыло подтверждено - о нём оповещать не будем
        if(!ensured) return;//---
        
        var LOGGER = PsDiscussion.logger;
        
        //Нам больше не нужны функции для работы с состоянием, сбросим их
        EVENT = EVENT.clone();
        
        //Отправим событие глобальным слушателям
        PsDiscussion.callGlobalListeners(EVENT.type, function(listeners) {
            LOGGER.logInfo('Событие {} отправлено {} глобальным слушателям', EVENT, listeners.length);
            listeners.walk(function(ob) {
                //Каждый слушатель получит "чистенькое" событие, не модифицированное другими слушателями
                ob.f.call(ob.ctxt, EVENT.clone());
            });
        });

        //Если событие пришло из локальной шины - не будем его повторно туда класть
        if (fromBus) return;//---
        
        //Отправим событие в локальную шину
        LOGGER.logInfo('Событие {} отправлено в локальную шину', EVENT);
        PsLocalBus.fire(PsLocalBus.EVENT.DISCUSSIONMSG, EVENT);
    }
}

/*
 * ===============================================
 * = Функционал для работы с карточками клиентов =
 * ===============================================
 */
var PsUserInfoManager = {
    loading: {},     //Загружаемые пользователи
    userData: {},    //Загруженные пользователи
    enabled: true,   //Признак включённости карточки
    curUserId: null, //Текущий показываемый пользователь
    
    init: function() {
        //Подготовим карту
        this.img = $('<img>').css('float', 'left');
        
        this.closer = $('<span>').addClass('form_closer').click(function(){
            PsUserInfoManager.hideCard();
            return false;
        });
        
        this.content = $('<div>');
        
        var loadingDiv = $('<div>').addClass('loading').text('Загрузка профиля...').append(crIMG(CONST.IMG_LOADING_PROGRESS));
        this.loadingContent = $('<div>').append(this.img).append(loadingDiv);
        
        this.cardBody= $('<div>').addClass('user_info').
        append(this.closer).
        append(this.loadingContent).
        append(this.content);

        //Подпишемся на клик
        PsJquery.on({
            ctxt: this,
            item: 'ul.discussion li>img.avatar.user, ul.discussion div.comment a.author',
            click: function(e, $item) {
                e.preventDefault();
                if (!this.enabled) return;//---
                var $img = $item.is('img.avatar.user') ? $item : $item.parents('li:first').children('img.avatar.user:first');
                var userId = $img.data('uid');
                if(!userId) return;//---
                $img.parent('li:first').css('position', 'relative').prepend(this.fillCard($img, userId).show());
            }
        });
    },
    
    hideCard: function() {
        if (this.cardBody) {
            /*
             * Приатачим карточку к форме, на случай, если ветка будет удалена
             */
            this.cardBody.hide().appendTo('body');
        }
    },
    
    setEnabled: function(enabled) {
        this.enabled = enabled;
        if (enabled) return;//---
        this.hideCard();
    },
    
    processDataSuccess: function(userId, data){
        this.userData[userId] = data;
        if (this.curUserId == userId) {
            this.loadingContent.hide();
            this.closer.show();
            this.content.html(data).show();
        }
    },
    
    fillCard: function($img, userId){
        /*
         * Сохраним текущего пользователя, чтобы правильно обработать ситуацию,
         * когда пользователь кликнет по второй карточке до загрузки первой.
         */
        this.curUserId = userId;
        
        if (this.userData[userId]){
            this.processDataSuccess(userId, this.userData[userId]);
        }
        else
        {
            this.closer.hide();
            this.content.hide();
            this.img.attr('src', $img.attr('src'));
            this.loadingContent.show();
            
            if(!this.loading[userId]){
                this.loading[userId] = true;
                
                AjaxExecutor.execute('UserInfo', {
                    ctxt: this,
                    id: userId
                }, function(ok){
                    return ok;
                }, function(err) {
                    return InfoBox.divError(err);
                },function($ctt) {
                    this.processDataSuccess(userId, $ctt);
                })
            }
        }
        
        return this.cardBody;
    }
}


/*
 * ===================================================
 * = Функционал для управления конкретной дискуссией =
 * ===================================================
 */
function PsDiscussionController($DISCUSSION) {
    //ФОРМА
    var $FORM = $DISCUSSION.find('.discussion-form');
    var $FORM_TA = $FORM.find('textarea');
    var $FORM_THEME = $FORM.find('input:text');
    var canComment = !$FORM_TA.isEmptySet();

    var DDATA = PsDiscussion.gatherData($DISCUSSION);
    var STORE = PsLocalStore.inst('discussion-tree-'+DDATA.unique+'-'+DDATA.thread);
    
    var FORM = {
        submitBtn: null,
        ErrController: null,
        showParentText: false,
        BtnsController: null,
        
        init: function() {

            //Закрывание формы
            $FORM.find('.form_closer').clickClbck(function() {
                FORM.close();
            });
            
            //Если можем комментировать - добавим всякие слушатели на элементы ввода
            if (canComment) {
                this.ErrController = new PsInfoBoxController($('<div>').insertAfter($FORM_TA));
                this.submitBtn = $FORM.find(':submit').click(function() {
                    FORM.submit();
                });
                
                var onChange = function() {
                    var theme = $FORM_THEME.val();
                    var message = $FORM_TA.val();
                    if(!PsIs.empty(message)) FORM.ErrController.clear();
                    STORE.set('msg-value', message);
                    STORE.set('theme-value', theme);
                }
                
                $FORM_TA.val(STORE.get('msg-value', '')).change().keyup(onChange).change(onChange);
                $FORM_THEME.val(STORE.get('theme-value', '')).change().keyup(onChange).change(onChange);
                
                this.BtnsController = new ButtonsController($FORM.find('.form-tools>button'), {
                    ctxt: this,
                    id: 'discussion-form-ctrl',
                    click: this.doToolAction
                });
            }
            
            //Попытаемся восстановить предыдущее состояние формы
            if (this.show4comment(STORE.get('ta-pos'))) return;//---
            
            //Разместим форму
            //this.show($DISCUSSION);
            this.close();
        },
        
        isRoot: function() {
            return $FORM.parent('ul').is($DISCUSSION);
        },
        
        onFormMove: function() {
            var $comment = PsDiscussion.getParentLiComment($FORM);
            STORE.put('ta-pos', $comment ? $comment.attr('id') : null);
            
            if (canComment) {
                this.updateParentText();
                this.ErrController.clear();
                this.updateToolsPanel();
                $FORM_TA.change();
                $FORM_THEME.parent('div').setVisible(this.isRoot());
            }
        },
        
        show4comment: function($comment, focus) {
            if (PsIs.empty($comment)) return false;//---
            if (PsIs.string($comment)) {
                $comment = $DISCUSSION.find($comment.ensureStartsWith('#'));
                if ($comment.isEmptySet()) return false;//---
            }
            if ($comment.siblings('ul').isEmptySet()){
                $('<ul>').insertAfter($comment);
            }
            FORM.show($comment.siblings('ul:first'), focus);
            return true;
        },
        
        updateParentText: function() {
            $FORM.find('.parent-text').remove();
            if (!canComment || !this.showParentText) return;//---
            var $parentComment = PsDiscussion.getParentLiComment($FORM);
            var parentText = $parentComment ? $.trim($parentComment.find('.comment-text').html()) : null;
            if(!parentText) return;//---
            $('<div>').html(parentText).addClass('parent-text').insertBefore($FORM_TA);
        },
        
        setUpdateParentText: function(doUpdate) {
            this.showParentText = doUpdate;
            this.updateParentText();
        },
        
        showError: function(text) {
            if (this.ErrController) {
                this.ErrController.error(text, true);
                this.focus();
            }
        },
        
        focus: function() {
            if (canComment) {
                //Ставим фокус на textarea
                $FORM_TA.focus();
                //Если мы работаем с темой, тему можно ввести и она сейчас пуста - ставим фокус на неё
                if (DDATA.themed && this.isRoot() && !$.trim($FORM_THEME.val())) {
                    $FORM_THEME.focus();
                }
            } else {
                //Мы не можем комментировать - скроллим на форму
                PsScroll.scrollTo($FORM);
            }
        },
        
        show: function($parentUl, focus) {
            if (PsDiscussion.isLocked()) return;//---
            
            var append = $parentUl.is($DISCUSSION) && DDATA.updown;
            
            append ? $parentUl.append($FORM.hide()) : $parentUl.prepend($FORM.hide());

            this.onFormMove();
            
            $FORM.show();

            if (focus) this.focus();
        },
        
        close: function() {
            PsDiscussion.clearParentMark($DISCUSSION);
            $FORM.hide().appendTo($DISCUSSION);
            this.onFormMove();
        },
        
        submit: function() {
            if (!canComment) return;//---
            
            var $parentComment = PsDiscussion.getParentLiComment($FORM);
            
            var data = {
                ctxt: this,
                theme: $.trim($FORM_THEME.val()),
                comment: $.trim($FORM_TA.val()),
                parent_id: PsDiscussion.getCommentId($parentComment)
            };
            
            if(!data.theme && DDATA.themed && !data.parent_id) {
                this.showError('Введите тему сообщения');
                return;//---
            }
            
            if(!data.comment) {
                this.showError('Введите текст сообщения');
                return;//---
            }
            
            if (PsFormSubmitTimer.start(this.submitBtn)) return;//---
            if (PsDiscussion.isLocked()) return;//---
            
            PsDiscussion.lock();
            
            $.extend(data, DDATA);
            
            this.submitBtn.saveVal("Выполянем...");
            
            var $inputs = $FORM.activeFormInputs().disable();
            
            AjaxExecutor.executePost('DiscussionAddMsg', data,
                function(commentLi) {
                    var $commentLi = $(commentLi);
                    var $commentDiv = $commentLi.children('div');
                    var $parentUl = $FORM.parents('ul:first');
                    var prepand = $parentUl.is($DISCUSSION) && !DDATA.updown;
                    prepand ? $parentUl.prepend($commentLi) : $parentUl.append($commentLi);
                    
                    $FORM_TA.val('');
                    $FORM_THEME.val('');
                    
                    this.close();
                    
                    PsFormSubmitTimer.reinit();
                    
                    var $parentComment = PsDiscussion.getParentLiComment($commentLi);
                    if ($parentComment) {
                        var parentCommentUnique = $parentComment.data('unique');
                        //Если родительский комментарий не подтверждён - удалим эту отметку
                        PsDiscussionMsg.makeEventAndDo(parentCommentUnique, 'confirm');
                        //Если родительский комментарий нов для нас - удалим эту отметку
                        PsDiscussionMsg.makeEventAndDo(parentCommentUnique, 'known');
                    }
                    
                    PsDiscussion.fireDiscussionChangeEvent($DISCUSSION);
                    
                    PsScroll.scrollTo($commentLi, null, function() {
                        //Мигание нового комментария
                        $commentDiv.addClass('new');
                        var doBlink = function(num) {
                            //Мигаем три раза
                            if (num<=5) {
                                $commentDiv.toggleClass('start-blink');
                                return;//---
                            }
                            //Убираем отметку о том, что комментарий - новый. Можем показать кнопки управления.
                            $commentDiv.removeClass('new');
                            //Отписываемся от интервала
                            PsGlobalInterval.unsubscribe(doBlink);
                        }
                        //Время интервала должно совпадать с временем в стиле .comment.new
                        PsGlobalInterval.subscribe(400, doBlink);
                    });
                },
                this.showError,
                function() {
                    this.submitBtn.restoreVal();
                    $inputs.enable();
                    PsDiscussion.unlock();
                });
        },
        
        //Выполнение действия по нажатию внопки формы
        DIALOG: null,
        doToolAction: function(action, isOn) {
            switch (action) {
                case 'show_parent':
                    this.setUpdateParentText(isOn);
                    break;
                case 'fullscreen':
                    if(!this.DIALOG) {
                        this.DIALOG = PsDialog.register({
                            id: 'DiscussionEditDialog',
                            ctxt: this,
                            build: function(DIALOG, whenDone) {
                                DIALOG.div.
                                //append($('<h5>').html('Редактирование комментария')).
                                append($('<textarea>'));
                                whenDone(DIALOG);
                            },
                            onShow: function(DIALOG) {
                                DIALOG.div.find('textarea').val($FORM_TA.val()).change().focus();
                            },
                            doAction: function(DIALOG) {
                                DIALOG.close();
                                $FORM_TA.val(DIALOG.div.find('textarea').val()).change().focus();
                            },
                            wnd: {
                                title: 'Текст сообщения',
                                buttons: 'Перенести в форму'
                            }
                        });
                    }
                    this.DIALOG.open();
                    break;
            }
        },
        
        updateToolsPanel: function() {
            if (this.BtnsController) this.BtnsController.setBtnVisible('show_parent', !!PsDiscussion.getParentLi($FORM));
        }
    }
    
    //Инициализируем форму
    FORM.init();
    
    
    //Дозагрузка сообений
    var $loadLi = $DISCUSSION.find('li.load');
    var $loadButton = $loadLi.children('button');
    if(!$loadButton.isEmptySet()) {
        $loadButton.button({
            icons: {
                primary: 'ui-icon-comment'
            }
        }).uiButtonStoreLabel().click(function() {
            if (PsDiscussion.isLocked()) return;//---
            
            PsDiscussion.lock();

            $loadButton.uiButtonDisable().uiButtonLabel('Загружаем...');
            
            var rootId = PsDiscussion.getCommentId(PsDiscussion.getLiComment($loadLi.prev('li.msg')));
            if(!PsIs.number(rootId)) {
                $loadLi.html(InfoBox.divError('Не удалось определить коренной элемент'));
                PsDiscussion.unlock();
                return;//---
            }
            
            var data = {
                action: 'load-comments',
                rootId: rootId
            };
            
            $.extend(data, DDATA);
            
            AjaxExecutor.executePost('DiscussionAction', data,
                function(result) {
                    var $tree = $(result.tree);
                    
                    //Выкинем из дерева сообщения, которые уже были добавлены нами
                    $tree.each(function() {
                        var $li = $(this);
                        var id = PsDiscussion.getLiComment($li).attr('id');
                        if ($DISCUSSION.hasChild('#'+id)) {
                            result.hasmore = false;
                            $tree = $tree.not($li);
                        }
                    });
                    
                    $tree.insertBefore($loadLi);
                    
                    if (result.hasmore) {
                        $loadButton.uiButtonRestoreLabel().uiButtonEnable();
                    } else {
                        $loadLi.remove();
                    }

                    PsDiscussion.fireDiscussionChangeEvent($DISCUSSION);
                
                }, function(err) {
                    $loadLi.html(InfoBox.divError(err));
                }, function() {
                    PsDiscussion.unlock();
                });
        });
    }
    
    //Кнопки управления дискуссией
    var DiscussionBntsController = new ButtonsController($DISCUSSION.siblings('.discussion-ctrl').children('button'), {
        ctxt: this,
        id: 'discussion-ctrl',
        click: function(action, isOn) {
            switch (action) {
                case 'add_comment':
                    FORM.show($DISCUSSION, true);
                    return;//---
                case 'simple_view':
                    if (isOn) {
                        PsUserInfoManager.hideCard();
                    }
                    break;
            }
            
            $DISCUSSION.toggleClass(action, isOn);
        }
    });
    
    var UpdateDiscussionBntsController = function(state) {
        DiscussionBntsController.recalcState({
            'simple_view': {
                visible: state.total > 0
            },
            'highlight': {
                visible: state.roots > 0 //Мы всегда можем приаттачить форму
            }
        });

    }
    
    //Обработка событий нажатия ссылок управления комментарием
    PsDiscussion.connectToEvent($DISCUSSION, PsDiscussion.DiscussionCommentEvent, this, function(event) {
        var action = event.action;
        
        switch (action) {
            case 'reply':
                FORM.show4comment(event.comment, true);
                break;
            case 'remove':
                if (event.li.hasChild($FORM)) {
                    FORM.close();
                }
                break;
        }
    });

    PsDiscussion.connectToEvent($DISCUSSION, PsDiscussion.DiscussionChangeEvent, this, function(state) {
        UpdateDiscussionBntsController(state);
    });
}


$(function() {
    var PsDiscussionInit = PsUtil.once(PsDiscussion.init, PsDiscussion);
    var PsUserInfoManagerInit = PsUtil.once(PsUserInfoManager.init, PsUserInfoManager);
    $('.discussion').livequery(function() {
        var $discussion = $(this).ensureIdIsSet('DSC');
        PsDiscussionInit();
        PsUserInfoManagerInit();
        if ($discussion.hasChild('.discussion-form')) {
            new PsDiscussionController($discussion);
        }
        //Кидаем событие об изменении дерева, всё остальное будет выполнено автоматически
        PsDiscussion.fireDiscussionChangeEvent($discussion);
    });
});