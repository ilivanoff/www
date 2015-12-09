/*
 * ОФИС - АВАТАРЫ
 */
$(function() {
    function UserAvatarsController($PAGE) {
        var $AVATARS = $PAGE.find('div.avatars');
        var $BIG_AVATAR = $PAGE.find('.user_info img.avatar');
        var $BUTTONS = $PAGE.find('.avatar-controls a.main');
        
        //Функционал управления блоком держателей аватаров пользователя.
        var AVATARS = {
            currentId: null,
            selectedId: null,
            // 1 -> av-1
            toElId: function(id) {
                return id ? id.ensureStartsWith(CONST.AVATAR_ID_PREFIX) : null;
            },
            // av-1 -> 1
            toCode: function(id) {
                return id ? id.removeFirstCharIf(CONST.AVATAR_ID_PREFIX) : null;
            },
            //Возвращает код выделенного аватара
            getSelectedCode: function() {
                return this.toCode(this.selectedId);
            },
            //Проверяет, выделен ли сейчас дефолтный аватар
            isDefaultSelected: function() {
                return !PsIs.number(this.getSelectedCode());
            },
            //Проверяет, выделен ли сейчас текущий аватар
            isCurrentSelected: function() {
                return this.currentId==this.selectedId;
            },
            //Блоки с картинками-аватарами
            holders: function() {
                return $AVATARS.children('.avatar_holder');
            },
            //Возвращает конкретный холдер аватара
            holder: function(id) {
                return this.holders().filter('#'+this.toElId(id));
            },
            //Проверяет, есть ли холдер с таким id
            hasHolder: function(id) {
                return !this.holder(id).isEmptySet();
            },
            //Устанавливает новый текущий аватар
            setCurrent: function(id) {
                this.currentId = this.toElId(id);
                this.selectedId = this.toElId(id);
                this.layout();
            },
            //Выделяет новый аватар
            setSelected: function(id) {
                this.selectedId = this.toElId(id);
                this.layout();
            },
            //Размечает текущей и выделенный аватары
            layout: function() {
                this.holders().each(function(){
                    var $holder = $(this);
                    var id = $holder.attr('id');
                    $holder.toggleClass('selected', id==AVATARS.selectedId).toggleClass('current', id==AVATARS.currentId);
                });
        
                //Незагруженные аватары нужно соответствующим образом отметить
                PsJquery.onLoad($AVATARS, function() {
                    this.holders().filter(':has(img.x-error)').addClass('box-img-x-error').find('img').removeAttr('alt');
                }, this);
                
                //Если показано менее трёх аватаров (с дефолтным) - можем загрузить ещё
                var canUpload = this.holders().size()<=2;
                $('#file_upload').setVisible(canUpload);
                $BUTTONS.extractHrefsByAnchor('upload').parent().setVisible(!canUpload);
                
                //Если тукущий и выделенный аватары не совпадают - можем переустановить текущий
                $BUTTONS.extractHrefsByAnchor('set').toggleClass('disabled', this.isCurrentSelected());
                
                //Если выделен не дефолтный аватар, то его можно удалить
                $BUTTONS.extractHrefsByAnchor('del').toggleClass('disabled', this.isDefaultSelected());
            },
            onAvatarAdd: function(avatars) {
                $.each(avatars, function(id, src) {
                    if (AVATARS.hasHolder(id)) return;//---
                    //Создадим новый элемент
                    $('<div>').addClass('avatar_holder').attr('id', id).append($('<img>').attr('src', src)).appendTo($AVATARS);
                    //Выделим загруженный аватар
                    AVATARS.setSelected(id);
                });
            }
        }
        
        //При клике по холдеру аватара - его нужно выделить
        PsJquery.on({
            parent: $AVATARS,
            item: '.avatar_holder',
            click: function(e, $holder) {
                e.preventDefault();
                AVATARS.setSelected($holder.attr('id'));
            }
        });
        
        //Сделаем кнопку загрузки файлов
        $('#file_upload').psUploadify({
            postData: {
                type: 'Avatar'
            },
            onUploadComplete : function() {
                AjaxExecutor.execute('UserAvatars', {
                    ctxt: AVATARS
                }, AVATARS.onAvatarAdd, 'Загрузка аватаров');
            },
            buttonText : 'Загрузить аватар'
        });
        
        //Весь функционал управления кнопками подтверждения действий над аватарами вынесем в отдельный класс
        var CONTROLS = {
            $YES: null,
            $NO: null,
            TIMER: null,
            cancel: function() {
                if (this.TIMER) {
                    this.TIMER.stop();
                }
                if (this.$YES) {
                    this.$YES.remove();
                    this.$YES = null;
                }
                if (this.$NO) {
                    this.$NO.remove();
                    this.$NO = null;
                }
            },
            show: function(action, $href) {
                if ($href.is('.disabled')) return;//---
                
                this.cancel();
                
                if (!this.TIMER) this.TIMER = new PsTimerAdapter(this.cancel, 3000, this);
                
                this.$YES = crA().html('Да').clickClbck(function() {
                    this.cancel();
                    this.execute(action);
                }, this).insertAfter($href);
                
                this.$NO = crA().html('Нет').clickClbck(this.cancel, this).insertAfter(this.$YES);
                
                this.TIMER.start();
            },
            execute: function(action) {
                var code = AVATARS.getSelectedCode();
                var data = {
                    id: code,
                    action: action
                };

                AjaxExecutor.execute('UserAvatarsAction', data, function(response) {
                    if (action == 'del') {
                        AVATARS.holder(code).remove();
                    }
                    $BIG_AVATAR.attr('src', response.src_big);
                    AVATARS.setCurrent(response.id);
                }, action);
            }
        }
        
        //При клике на кнопку мы должны показать кнопки подтверждения
        $BUTTONS.clickClbck(CONTROLS.show, CONTROLS);
        
        //Установим текущий аватар пользователя
        AVATARS.setCurrent(defs.curAvatar);
    }

    //СОЗДАДИМ КОНТРОЛЛЕР АВАТАРОВ
    new UserAvatarsController($('#bp-office'));
});
