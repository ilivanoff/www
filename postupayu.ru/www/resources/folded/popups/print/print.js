$(function() {
    
    var FMANAGER = PsFoldingManager.FOLDING('pp', 'print');
    var STORE = FMANAGER.store();
    
    var printManager = {
        init: function() {
            this.settings = $('#print_setting');
            /*
             * Печать ответов к задачам
             */
            var tasksCheckBox = this.settings.find('#p_tasks');
            var answersBlocks = $('.answer_block');
            if (answersBlocks.isEmptySet()) {
                tasksCheckBox.parent('div').remove();
            } else {
                /* 
                 * Раскроем все задачи. В стиле они закрыты, но мы будем рулить
                 * их видимостью с помощью javascript.
                 */
                tasksManager.expandAll();

                tasksCheckBox.change(function() {
                    var checked = $(this).is(':checked');
                    answersBlocks.setVisible(checked);
                    STORE.set('p_tasks', checked);
                }).setChecked(STORE.get('p_tasks', true)).change();
            }

            /*
         * Печать комментариев
         */
            var commentsCheckBox = this.settings.find('#p_comments');
            var commentsTypeRadio = this.settings.find('.comments_type');
            var commentsBlock = $('#comments');
            if (commentsBlock.isEmptySet() || !commentsBlock.hasChild('li.msg')){
                commentsCheckBox.parent('div').remove();
                commentsTypeRadio.remove();
            }
            else
            {
                var commentsTypeRadios = commentsTypeRadio.find('input');
                var discussion = commentsBlock.find('.discussion');
            
                commentsCheckBox.setChecked(STORE.get('p_comments', true))

                var defPrintType = STORE.get('p_comments_type', $(commentsTypeRadios.get(0)).attr('id'));
                commentsTypeRadios.filter('#'+defPrintType).setChecked(true);
            
                var updateState = function() {
                    var commentsChecked = commentsCheckBox.is(':checked');
                    var typeChecked = commentsTypeRadios.filter(':checked').attr('id');
                
                    if(!typeChecked){
                        return;
                    }
                
                    commentsBlock.setVisible(commentsChecked);
                    commentsTypeRadios.setEnabled(commentsChecked);
                    
                    STORE.set('p_comments', commentsChecked);
                    STORE.set('p_comments_type', typeChecked);
                
                    switch (typeChecked) {
                        case 'comments_full':
                            discussion.removeClass('simple_view');
                            break;
                        case 'comments_short':
                            discussion.addClass('simple_view');
                            break;
                    }
                }
            
                commentsCheckBox.change(function(e) {
                    updateState();
                });

                commentsTypeRadios.change(function(e){
                    updateState();
                });

                updateState();
            }
        
            this.addChoke();
            this.processTesting();
        
            if(!this.settings.hasChild('input')) {
                this.settings.remove();
            } else {
                /*
                 * Мы не можем воспользоваться методом .show, так как будет добавлен стиль display: block,
                 * и блок управления будет виден и при печати. Выход - при готовности добавлять класс .ready,
                 * и прятать его путём наложения стилей из print.print.css
                 */
                this.settings.addClass('ready');
            }
        },
    
        /*
     * Заглушки
     */
        addChoke: function(){
            $('.comment .user_name a').clickClbck();
        },
    
        processTesting: function(){
            //В тестах перенесём все задачи сразу после названия теста и удалим всё,
            //кроме названия теста и задач
            $('.test').each(function(){
                var $block = $(this);
                var $name = $block.children('.name');
                $block.find('.test_tasks').children().insertAfter($name);
                $block.children(':not(.name, .task_container)').remove();
            });
        }
    }

    printManager.init();
    
    
    PsUserInfoManager.setEnabled(false);
});
