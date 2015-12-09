/**
 * Менеджер открывает popup - окна.
 * pageIdent может быть задан в виде:
 * 1) popup.php?window=Plugin&ident=atom
 * 2) Plugin?ident=atom
 * Если он пуст, то откроется базовая страница popup.php
 */
var popupWindowManager = {
    base: 'popup.php',
    windowWidth: /*635*/720,
    windowHeight: 770,
    logger: PsLogger.inst('PopupWindowManager').setDebug()/*.disable()*/,
    
    init: function() {
        
        PsJquery.on({
            ctxt: this,
            item: '[pageIdent]',
            data: {
                progress: false
            },
            click: function(e, $btn, data) {
                //Не забываем, что могут быть не только ссылки, но и кнопки
                e.preventDefault();
                //Проверим, не кликает ли пользователь просто так
                if (data.progress) {
                    this.logger.logDebug('Потоплено событие открытия окна');
                    return;//---
                }
                data.progress = true;
                //Открываем окно
                this.openWindow($btn.attr('pageIdent'));
                //Не может пользователь кликнуть осознанно так быстро...
                PsUtil.startTimerOnce(function() {
                    data.progress = false;
                }, 5000);
                
            }
        });
    },
    
    windowFeatures: function(){
        return 'status=no,toolbar=no,menubar=no,scrollbars=yes'+
        ',width=' + this.windowWidth +
        ',height=' + this.windowHeight+
        ',left=' + ((screen.width-this.windowWidth)/2) +
        ',top=' + ((screen.height-this.windowHeight)/2-20);
    },
    
    openWindow: function(pageIdent, paramsObj, paramsStr) {
        pageIdent = pageIdent || '';
        paramsObj = paramsObj || {};
        paramsStr = paramsStr || '';
        this.logger.logInfo('Открываем всплывающее окно. Идентификатор: [{}], параметры в объекте: [{}], параметры в строке: [{}].', pageIdent, PsObjects.toString(paramsObj), paramsStr);
        
        var ident= '', params = '';
        
        var parts = pageIdent.split('?');
        switch (parts.length) {
            case 0:
                //Пустой идентификатор
                break;
            case 1:
                //Идентификатор или параметры
                if(parts[0].contains('=')){
                    //Параметры
                    params = parts[0];
                }else{
                    //Идентификатор
                    ident=parts[0];
                }
                break;
            default:
                ident=parts[0];
                params = parts[1];
                break;
        }
        
        if (ident.toLowerCase()==this.base) {
            ident = '';
        }
        
        //Строим параметры
        var OB = {};
        if (ident) {
            OB[defs.POPUP_WINDOW_PARAM] = ident;
        }
        $.extend(OB, paramsObj||{});
        $.extend(OB, PsUrl.getParams2Obj(paramsStr));
        $.extend(OB, PsUrl.getParams2Obj(params));
        
        var get = PsUrl.obj2getParams(OB);
        var url = this.base + (get ? '?' : '') + get;
        var winIdent = MD5(url);
        window.open(url, winIdent, this.windowFeatures()).focus();
        this.logger.logInfo('Конечный url: [{}], идетификатор окна: [{}]', url, winIdent);
    }
}



/**
 * Менеджер для работы с TeX формулами
 */
var MathJaxManager = {
    
    init: function() {
        //1. Кнопка, всплывающая над формулами и позволяющая перейти к её редактированию
        PsBubble.registerBubbleStick('.TeX:not(.TeX-no-tooltip .TeX)', function(onDone, $href) {
            var hash = MathJaxManager.getTexHash($href);
            
            var $button = $('<button>').attr('title', 'Загрузить формулу в редактор').attr('type', 'button').addClass('imaged');
            $button.append(crIMG(CONST.IMG_FORMULA));
            $button.click(function(){
                popupWindowManager.openWindow('formula', {
                    hash: hash
                });
            });
            
            onDone($('<div>').addClass('tex_ctrl').append($button));
        });
    },
    
    isEnabled: function() {
        return typeof(window.MathJax) != 'undefined';
    },
    
    updateFormules: function(){
        if (this.isEnabled()) {
            window.MathJax.Hub.Typeset();
        }
    },
    
    getTexHash: function($el){
        var hash;
        if ($el.is('img')){
            hash = $el.attr('src');
            hash = getStringEnd(hash, '/', true);
            hash = getStringStart(hash, '.', false);
        }
        else {
            hash = $el.data('tex');
        }
        return hash;
    },
    
    /*
     * 
     */
    decoded: {},
    loading: {},
    waiting: {},
    decodeJax: function(hash, callback) {
        hash = $.trim(hash);
        var key = 'f' + hash;
        
        if(this.decoded.hasOwnProperty(key)){
            callback.call(this.decoded[key], this.decoded[key]);
            return;//---
        }
        
        if(!this.waiting.hasOwnProperty(key)){
            this.waiting[key] = [];
        }
        this.waiting[key].push(callback);
        
        if(this.loading.hasOwnProperty(key)) {
            return;//---
        }
        this.loading[key] = true;
        
        AjaxExecutor.execute('TexDecode', {
            ctxt: this,
            hash: hash
        }, 
        function(tex) {
            this.decoded[key] = tex;
            return tex;
        },
        function(err) {
            InfoBox.popupError(err);
            this.decoded[key] = null;
            return null;
        },
        function(tex) {
            $.each(this.waiting[key], function(num, fn){
                fn.call(tex, tex);
            });
            
            delete this.waiting[key];
        });
    }
}


/**
 * Менеджер, отвечающий за сквозную нумерацию элементов в рамках блока сквозной нумерации.
 * Всё работает довольно просто - у блоков нумерации, вложенных в другие блоки, удаляется специальный класс.
 * Затем все элементы в рамках самого верхнего блока получают сквозную нумерацию.
 */
var PsNumeratorManager = {
    init: function() {
        
        function recalcBlock(css) {
            //Удалим признак блока нумерации у всех вложенных блоков
            $('.'+css+' .'+css).removeClass(css);
            
            $('.'+css).each(function() {
                //Дадим сквозную нумерацию всем элементам внутри блока нумерации
                $(this).find('.'+css+'-index').html(function(idx) {
                    return idx+1;
                });
                
                //Дадим сквозную нумерацию всем ссылкам на элементы нумерации, взяв их у элементов, на которые они указывают
                $(this).find('.'+css+'-href-index').html(function() {
                    var elId = $(this).extractParent('.'+CONST.BUBBLE_HREF_MOVE_CLASS).data('bubble');
                    return $(elId.ensureStartsWith('#')+' .'+css+'-index').text();
                });
            });
        }
        
        function addListener(css) {
            PsJquery.executeOnElVisible('.'+css, function() {
                recalcBlock(css);
            });
        }
        
        PsObjects.keys2array(CONST).walk(function(key) {
            if (key.startsWith('CSS_NUMERATOR_')) {
                addListener(CONST[key]);
            }
        });
    
    }
}


/**
 * Менеджер, отвечающий за позиционирование номеров формул по центру блоков
 */
var TexFormules = {
    init: function() {
        PsJquery.executeOnElVisible('.formula .num', function($num) {
            var $formula = $num.extractParent('.formula');
            var nh = $num.height();
            var nf = $formula.height();
            //Приподнимем формулу, так как различные стрелочки над векторами могут вылезать из разметки
            var top = Math.floor((nf-nh)/2) - 2;
            if (top > 0) {
                $num.css('top', top+'px');
            }
        });
    }
}


/**
 * Галлереи
 */
var GalleryManager = {
    counter: 0,
    logger: PsLogger.inst('GalleryManager').setTrace()/*.disable()*/,
    
    init: function(){
        if(!$.fn.galleria){
            return;//---
        }
        this.updateModel = new PsUpdateModel();
        
        this.process();
        this.initLazy();
    },
    
    initLazy: function(){
        var _this = this;
        $('.'+ CONST.GALLERY_BOX).livequery(function() {
            var $BOX = $(this);
            $BOX.find('a.toggler').clickClbck(function() {
                _this.toggleState($BOX);
            });
            $BOX.find('a.popup').clickClbck(function() {
                var id = $BOX.data('id');
                popupWindowManager.openWindow('gallery', {
                    id: id
                });
            });
        }).show();
    },
    
    toggleState: function($box) {
        var isOpen = $box.hasClass('open');
        if (isOpen) {
            //Закрываем
            $box.removeClass('open');
        } else {
            //Открываем
            var $images = $box.find('.content .images');
            
            if ($images.hasChild()) {
                $box.addClass('open');
            }
            else
            {
                if(this.updateModel.isStarted()){
                    return;
                }
                this.updateModel.start();
                
                var $info = $box.find('.controls .info');
                var $progress = span_progress('Загружаем');
                
                $info.hide();
                $progress.insertAfter($info);
                
                AjaxExecutor.execute('GetGalleryImages', {
                    ctxt: this,
                    id: $box.data('id')
                }, 
                function(ok) {
                    return $(ok);
                },
                function(err) {
                    return InfoBox.divError(err);
                },
                function($ctt) {
                    $images.append($ctt);
                    
                    $progress.remove();
                    $info.show();
                    
                    $box.addClass('open');
                    this.updateModel.stop();
                });
            }
        }
    },
    
    /*
     * Добавляем слушателя на появление блока с картинками, который нужно преобразовать в галерею.
     * Он может и просто быть на странице, а может и быть добавлен с помощью поздней загрузки.
     */
    process: function(){
        
        var NUM = ++this.counter;
        var LOGGER = this.logger;
        
        $('.'+CONST.GALLERY_IMAGES).livequery(function() {
            
            var $box = $(this);
            var id = $box.data('id');
            var name = $box.data('name');
            var secundomer = new PsSecundomer(true);
            
            LOGGER.logInfo('<{}> Построение галереи [{}] ({})', NUM, id, name);
            
            PsJquery.onLoad($box, function() {
                
                //Удалим картинки, которые не загрузились
                var cntErr = this.find('img.x-error').remove().size();
                var cntScc = this.find('img').size();
                
                LOGGER.logInfo('<{}> Картинки загружены за {} секунд. Успешно: {}{}.', NUM, secundomer.time(), cntScc, cntErr ? ', с ошибкой: ' + cntErr : ' (все картинки валидны)');
                
                if(!cntScc) {
                    //Картинок нет, напишем текст
                    this.replaceWith(InfoBox.divWarning('В галерее нет картинок'));
                    return;//---
                }
                
                //Строим галерею
                this.galleria({
                    //image_crop: true, // crop all images to fit
                    thumb_crop: true, /* crop all thumbnails to fit */
                    transition: 'fade', /* crossfade photos */
                    transition_speed: 700, /* slow down the crossfade */
                    data_config: function(img) {
                        // will extract and return image captions from the source:
                        return  {
                            title: $(img).attr('title'),
                            description: $(img).attr('alt')
                        };
                    },
                    extend: function() {
                        this.bind(Galleria.IMAGE, function(e) {
                            // bind a click event to the active image
                            $(e.imageTarget).css('cursor','pointer').click(this.proxy(function() {
                                // open the image in a lightbox
                                this.openLightbox();
                            }));
                        });
                    }
                }).show();
                
                LOGGER.logInfo('<{}> Галерея построена за {} секунд.', NUM, secundomer.stop());
            
            }, $box);
        });
    }
}



/**
 * Управление задачами
 */
var tasksManager = {
    init: function(){
        $('div.answer_block>p>a.ctrl').each(function(){
            $(this).html($(this).html() + '&nbsp;»');
        }).clickClbck(function() {
            this.parent('p:first').toggleClass('active').next('div').toggleVisibility();
            this.html(function(index, oldhtml) {
                return oldhtml.contains(':') ? oldhtml.replace(':', '»') : oldhtml.replace('»', ':');
            });
        });
    },
    
    expandAll: function() {
        $('div.answer_block>p:not(.active)>a.ctrl').click();
    },
    
    showAnswerBlocks: function($parent){
        $parent.find('div.answer_block').show();
    },
    
    hideAnswerBlocks: function($parent){
        $parent.find('div.answer_block').hide();
    }
}


/**
 * Менеджер, выдающий очки авторизованным пользователям
 */
var PsPointsGiverManager = {
    logger: PsLogger.inst('PsPointsGiverManager').setDebug()/*.disable()*/,
    check: function(fentity, data) {
        if (!defs.isAuthorized) return;//---
        data = data || {};
        data.fentity = fentity;
        
        if (AjaxExecutor.isExecuted('GivePointsCommon', data)) {
            this.logger.logDebug('Запрос с параметрами {} уже выполнялся, пропускаем.', AjaxExecutor.dataToString(data));
            return;//---
        }

        AjaxExecutor.execute('GivePointsCommon', data, function(OK) {
            if (!OK) return;//---
            InfoBox.popupInfo('Вам были начислены очки');
            PsPointsGiverManager.firePointsGiven();
        }, 'Выдача очков пользователю');
    },
    
    //Кладёт сообщение в шину о том, что пользователю были начислены очки
    firePointsGiven: function() {
        if (!defs.isAuthorized) return;//---
        PsLocalBus.fire(PsLocalBus.EVENT.POINTSGIVEN);
    }
}

jQuery(function() {
    popupWindowManager.init();
    
    MathJaxManager.init();
    
    PsNumeratorManager.init();
    
    GalleryManager.init();
    
    tasksManager.init();
    
    TexFormules.init();
});