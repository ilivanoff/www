$(function() {
    
    var UserCommentsManager = {
        BOX: null,
        init: function($div) {
            this.BOX = $div.extractTarget('#user_posts_comments');
            this.updateCommentsCnt();
        },

        updateCommentsCnt: function() {
            var cnt = this.BOX.find('ul.discussion li.msg a.known').size();
            if (cnt > 0) {
                this.BOX.find('h2.upc_cnt span').html(cnt);
            } else {
                this.BOX.html(noItemsDiv());
            }
        },
        
        onHrefEvent: function(event) {
            if (event.action!='known' || event.group!='post') return;//---
            
            //Сразу поставим соответствующую отметку
            PsOfficeTools.setCnt('usercomments', null, -1);
            
            if (!this.BOX) return;//Страница ещё не была открыта и пользователь кликнул по дереву под постом

            var $commentDiv = this.BOX.find('.'+event.msgUnique);
            if ($commentDiv.isEmptySet()) return;//Этого комментария нет на странице ---

            var $discussion = $commentDiv.extractParent('ul.discussion');
            var $commentLi = $commentDiv.parent('li');
            
            var $commentsBox = $discussion.parent('.upc_comments');
            var $commentsBoxCtrls = $commentsBox.children('.upc_controls').hide();
            var $postComments = $commentsBox.extractParent('.user_post_comments');
            $commentLi.fadeOut('fast', function() {
                $commentLi.remove();
                UserCommentsManager.updateCommentsCnt();
                if ($discussion.hasChild('a.known')) {
                    $commentsBoxCtrls.show();
                } else {
                    $commentsBox.fadeOut('fast', function() {
                        $commentsBox.remove();
                        
                        if ($postComments.hasChild('.upc_comments')) return;//---
                        $postComments.remove();
                    });
                }
            });
        }
    }

    /*
     * Регистрируется процессор
     */
    PsIdentPagesManager.register({
        usercomments: {
            onAdd: function(page) {
                UserCommentsManager.init(page.div);
            }
        }
    });
    
    /*
     * Добавляем глобальный слушатель на события о том, что пользователь увидел ответ на своё сообщение.
     * Нужно помнить, что он может кликнуть как на ссылку a.known на странице usercomments, так и в дереве
     * обсуждения поста под самим постом.
     * 
     * Прелесть в том, что нам совершенно всё равно, кто это событие принесёт... Мы подпишемся на все деревья
     * сразу, а потом будем искать своё сообщение на usercomments и удалять его оттуда. При этом на событие
     * отреалигуем всего один раз, так как событие будет проброшено для каждого дерева, в котором будет
     * этот ответ на сообщение пользователя.
     */
    PsDiscussion.connectToEvent(null, PsDiscussion.DiscussionCommentEvent, UserCommentsManager, UserCommentsManager.onHrefEvent);

});