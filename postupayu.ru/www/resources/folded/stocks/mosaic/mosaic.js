jQuery(function() {
    
    var $BODY = $('.ps-mosaic-img');
    var $CTRL = $BODY.find('.ctrl');
    var $ANS_HOLDER = $BODY.find('.ans_holder');
    var $HOLDER = $BODY.find('.holder');
    var $BINDS = null;

    /* 
     * Показать/скрыть ячейки пользователя.
     * Делаем очень просто - выбираем все ячейки, которые не принадлежат пользователю,
     * и на их месте показываем дивы, имеющие фот такой-же, как у картинки.
     */
    
    $CTRL.find('a[href="#showBinds"]').clickClbck(function() {
        if(!$BINDS) {
            $BINDS = $();
            $('#mosaicmap area[data-id!="'+defs.userId+'"]').each(function() {
                var coords = $(this).attr('coords').split(',');
                var x1 = strToInt(coords[0]);
                var y1 = strToInt(coords[1]);
                var x2 = strToInt(coords[2]);
                var y2 = strToInt(coords[3]);
                $BINDS = $BINDS.add($('<div>').width(x2-x1).height(y2-y1).addClass('bind').css('left', x1+'px').css('top', y1+'px'));
            });
            $BINDS.prependTo($HOLDER);
        }
        var active = this.toggleClass('active').hasClass('active');
        $HOLDER.toggleClass('binds-show', active);
        this.html(active ? 'все' : 'только мои' );
    }).html('только мои');
    
    
    /* Открыть ячейки пользователя */
    $CTRL.find('a[href="#showCells"]').confirmableClick({
        yes: function($a) {
            var $progress = span_progress('открываем');
            $a.replaceWith($progress);
            
            PsStockManager.execute('OpenCells', {},
                //ok
                function() {
                    $progress.replaceWith(span_success('Все ячейки открыты, обновляем страницу'));
                    locationReload();
                }, 
                //err
                function(err, timeout) {
                    $progress.replaceWith(span_error(
                        timeout ? 
                        'К сожалению, не все ячейки были открыты. Просьба обновить страницу и повторить попытку.' : 
                        err));
                });
        }
    });
    
    
    /* Удалить ответ */
    PsJquery.onHrefClick('.ans_holder .pscontrols a.delete', {
        msg: 'Удалить ответ?',
        yes: function($a, ansId){
            var progress = span_progress('Удаляем');
            $a.replaceWith(progress);

            PsStockManager.execute('DeleteAnswer', {
                id: ansId
            }, function(){
                $ANS_HOLDER.empty();
            }, progress);
        }
    });
    
    FormHelper.registerListener('MosaicAnswerForm', function(res) {
        $ANS_HOLDER.empty().append(res);
    });

    PsLocalBus.connect(PsLocalBus.EVENT.LOGIN, locationReload);
    PsLocalBus.connect(PsLocalBus.EVENT.LOGOUT, locationReload);
    
    // # 1.
    function MosaicMapController() {
        var $div = null;
        
        var onHide = function() {
            if (!$div) return;
            $div.remove();
            $div = null;
        }
        
        var onShow = function(e, $item) {
            onHide();
            var id = $item.data('id');
            
            if(!defs.cellowners.hasOwnProperty(id)) {
                return;//---
            }

            var ob = defs.cellowners[id];

            /*
             <div class="mosaic-popup">
                <img class="avatar" src="mmedia/avatars/u/u1/22_42x.jpg"/>
                <div class="content">
                    <h5>Имя Пользователя</h5>
                    <div class="message">Сообщение</div>
                </div>
                <div class="clearall"></div>
             </div>
             */

            $div = $('<div>').addClass('mosaic-popup');
            $div.append(crIMG(ob.avatar).addClass('avatar'));
            var $content = $('<div>').addClass('content').appendTo($div);
            $content.append($('<h5>').html(ob.name));
            if (ob.msg) {
                $content.append($('<div>').addClass('message').html(ob.msg));
            }
            $div.append($('<div>').addClass('clearall'));
            $div.appendTo('body').width($div.width());
            onUpdate(e);
        }
        var onUpdate = function(e) {
            $div.calculatePosition(e);
        }
        
        PsJquery.on({
            parent: '#mosaicmap',
            item: 'area',
            mouseenter: onShow,
            mousemove: onUpdate,
            mouseleave: onHide
        });
    }
    // # 1.
    
    
    new MosaicMapController();
});