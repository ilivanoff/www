/**
 * Менеджер для работы с всплывающими bubbles.
 * Функционал разделён на два уровня: загрузка данных и их отображение в виде всплывающих bubble.
 * 
 * Отображение bubble зависит от того, с каким типом регистрируется загрузчик содержимого: 
 * registerBubbleMove(selector, ...) или registerBubbleStick(selector, ...).
 * 
 * Далее при наведении на элемент $(selector) будет вызван переданный для него загрузчик содержимого - либо 
 * специально определенная функция (переданная после selector, ...), либо загрузчик/извлекатель, определённый 
 * по данным из самой ссылки.
 */
var PsBubble = {
    //Регистрирует менеджер bubble`оф
    registerBubbleImpl: function(selector, callback, ctxt, type) {
        PsJquery.on({
            ctxt: this,
            item: selector,
            data: {
                type: type,
                href: null,
                inst: null,
                loader: $.isFunction(callback) ? {
                    f: callback,
                    ctxt: ctxt
                } : null
            },
            mouseenter: this.onOver,
            mousemove: this.onMove,
            mouseleave: this.onOut
        });
    },
    
    //Регистрирует ссылки для показа bubble, который следует за курсором
    //callback.call(ctxt, onDone($div), $href)
    registerBubbleMove: function(selector, callback, ctxt) {
        this.registerBubbleImpl(selector, callback, ctxt, PsBubbleViewImpls.move);
    },
    
    //Регистрирует ссылки для показа bubble, который прикрёпляется к углу элемента-ссылки
    //callback.call(ctxt, onDone($div, showAtOnce), $href)
    registerBubbleStick: function(selector, callback, ctxt) {
        this.registerBubbleImpl(selector, callback, ctxt, PsBubbleViewImpls.stick);
    },
    /*
     * Функции, извлекающие данные из элементов для показа в bubble`ах.
     * Функция onDone должна быть вызвана в тот момент, когда содержимое будет готово к показу.
     * callback.call(ctxt, onDone($div), $box, $href)
     */
    EXTRACTORS: {},
    
    /*
     * Функции, загружающие данные для показа в bubble`ах.
     * Функция onDone должна быть вызвана в тот момент, когда содержимое будет готово к показу.
     * callback.call(ctxt, onDone($div), $href)
     */
    LOADERS: {},
    
    /*
     * Регистрируем загрузчик/извлекатель, убедившись, что все они - объекты.
     * ctxt - контекст, который будет установлен для объекта, если он передан без контекста.
     */
    firstRegistered: false,
    registerCallable: function (store, objects, ctxt) {
        for (var v in objects) {
            var ob = objects[v];
            var f = PsIs.object(ob) ? ob.f : ob;
            if($.isFunction(f)) {
                ctxt = PsIs.object(ob) && ob.ctxt ? ob.ctxt : ctxt;
                store[v] = {
                    f: f,
                    ctxt: ctxt
                }
            }
        }
        
        if(!this.firstRegistered && PsObjects.hasKeys(store)) {
            this.firstRegistered = true;
            /*
             * Если мы в первый раз регистрируем loader или extractor, то зарегистрируем базовые ссылки,
             * при наведении на которые будет показан bubble.
             */
            this.registerBubbleMove('.' + CONST.BUBBLE_HREF_MOVE_CLASS);
            this.registerBubbleStick('.' + CONST.BUBBLE_HREF_STICK_CLASS);
        }
    },
    
    registerExtractor: function(extractors, ctxt) {
        this.registerCallable(this.EXTRACTORS, extractors, ctxt);
    },
    
    registerLoader: function(loaders, ctxt) {
        this.registerCallable(this.LOADERS, loaders, ctxt);
    },
    
    /*
     * Метод отпередяет, экземпляр какого менеджера должен быть создан для управления bubble`ом.
     */
    chain: [],
    onOver: function(event, $href, data) {
        var inst = $href.data('inst');
        if (inst) {
            inst.onOver(event);
            return;//---
        }
        
        /*
         * Если мы создаём экземпляр, то нужно подготовить загрузчик doLoad - метод, 
         * который может быть вызван для получения содержимого показываемого bubble`а.
         */
            
        var self = this;
            
        var doLoad = function(onDone) {
            if (data.loader) {
                //Показываем bubble с помощью переданного нам загрузчика
                self.loadByLoader($href, data.loader, onDone);
                return;//---
            }
                
            //Определим тип загрузки. Начнём с отложенного загрузчика.
            var loader = $href.data('loader');
            if (loader) {
                var loaderOb = self.LOADERS[loader];
                if (loaderOb) {
                    self.loadByLoader($href, loaderOb, onDone);
                } else {
                    onDone();
                    InfoBox.popupError('Не зарегистрирован загрузчик данных ['+loader+']');
                }
                return;//---
            }
                
            //Остаётся только загрузчик по id.
            var extractor = $href.data('extractor') ? $href.data('extractor') : 'dflt';
            if (extractor) {
                var extractorOb = self.EXTRACTORS[extractor];
                if (extractorOb) {
                    self.loadByExtractor($href, extractorOb, onDone);
                } else {
                    onDone();
                    InfoBox.popupError('Не зарегистрирован получатель данных ['+extractor+']');
                }
                return;//---
            }
        }
        
        //Функция передас экземпляру представления бабла информацию о том, что он должен быть уничтожен
        var destructBubble = function($bubbleHref) {
            var viewInst = $bubbleHref.data('inst');
            if (viewInst) {
                $bubbleHref.data('inst', null);
                viewInst.onDestruct();
            }
        }
        
        //Теперь пройдёмся по цепочкам открытых баблов и удалим те их них, которые открыты уже после бабла,
        //на котором находится сслыка, на которую только что навели.
        var remove = 0;
        
        if(!$href.isChildOf('.ps-bubble-holder')) {
            remove = this.chain.length;
        } else {
            var inChain = false;
            this.chain.walk(function($chainHref) {
                if (inChain) {
                    //Ещё один бабл будет закрыт
                    ++remove;
                } else {
                    //К данной ссылке относится бабл, на котором находится ссылка, на которую только что навели
                    //Все ссылки, находящиеся до неё, нужно заморозить, а после неё - закрыть.
                    inChain = $chainHref.data('inst').hasChild && $chainHref.data('inst').hasChild($href);
                }
            });
            //Если ссылка принаджелит одному из открытых баблов, то мы закроем не все, но если ни одному - закроем всю открытую цепочку
            remove = inChain ? remove : this.chain.length;
        }
        
        //Закрываем все баблы, которые нужно закрыть
        for(var i=0; i<remove; i++) {
            destructBubble(this.chain.pop());
        }

        //Замораживаем все баблы, находящие в цепочке до бабла, на котором находится текущая ссылка
        this.chain.walk(function($chainHref) {
            if ($chainHref.data('inst').setFrosen) {
                $chainHref.data('inst').setFrosen(true);
            }
        });
        
        //Нужно дать возможность открытому баблу сказать о том, что он закрылся,
        //чтобы разморозить предшествующий бабл и закрыть все последующие
        var destructMe = function() {
            var remove = 0;
            var inChain = false;
            this.chain.walk(function($chainHref) {
                //Как только мы найдём текущую ссылку в цепочке - мы удалим этот бабл и всё, что идёт после
                inChain = inChain || ($chainHref[0]===$href[0]);
                if (inChain) {
                    ++remove;
                }
            });
            //Если нас нет в текущей цепочке - ничего и не делаем, так как мало ли кто из умерших нас там о чём оповещает:)

            for(var i=0; i<remove; i++) {
                destructBubble(this.chain.pop());
            }
            if (this.chain.length > 0) {
                var $chainHref = this.chain[this.chain.length - 1];
                if ($chainHref.data('inst').setFrosen) {
                    $chainHref.data('inst').setFrosen(false);
                }
            }
        }
        
        
        //Создаём экземпляр представления для новой ссылки и добавляем её в цепочку показов
        inst = new data.type(doLoad, $href, function() {
            destructMe.call(self);
        });
        
        $href.data('inst', inst);
        this.chain.push($href);
        inst.onOver(event);
    },
    
    onMove: function(event, $href) {
        if ($href.data('inst') && $href.data('inst').onMove) {
            $href.data('inst').onMove(event);
        }
    },
    
    onOut: function(event, $href) {
        if ($href.data('inst') && $href.data('inst').onOut) {
            $href.data('inst').onOut(event);
        }
    },
    
    loadByLoader: function($href, loaderOb, onDone) {
        loaderOb.f.call(loaderOb.ctxt, onDone, $href);
    },
    
    loadByExtractor: function($href, extractorOb, onDone) {
        var id = $href.data('bubble');
        id = $.trim(PsIs.string(id) && id ? id : getHrefAnchor($href));
        if(!id) {
            onDone();
            return; //---
        }
        id = id.ensureStartsWith('#');
        
        var $box = $(id);
        
        if ($box.isEmptySet()) {
            onDone();
            return; //---
        }
        
        extractorOb.f.call(extractorOb.ctxt, onDone, $box, $href);
    },
    
    /*
     * Утиличные методы, которые могут использовать загрузчики/извлекатели для подготовки отображаемого контента
     */
    wrap: function($content) {
        return $('<div>').addClass('ps-bubble-content-box').append($('<div>').addClass('ps-bubble-content').append($content));
    }
}

/**
 * Имплементации отображения bubble.
 * При наведении на ссылку для отображения bubble будет создан экземпляр функций данного класса.
 * Название функции зависит от типа отображения, с каким зарегистрирован bubble.
 * 
 * На вход в конструктор будет получена функция загрузки данных (загрузка и представление разделены) и
 * ссылка, на которую был наведён курсор.
 * 
 * Далее требуется реализовать три метода: onOver, onMove, onOut, onDestruct.
 * Метод onDestruct вызывается только тогда, когда пользователь навёл на другую ссылку, отображающую bubble.
 */
var PsBubbleViewImpls = {
    /* 
     * MOVE - прикрепляет bubble к курсору
     */
    move: function(doLoad) {
        var offsetX = 15;
        var offsetY = 15;
        
        var popup = null;
        
        var destructed = false;
        var hrefHovered = false;
        
        var onOut = function() {
            if (popup) {
                popup.remove();
                popup = null;
            }
            hrefHovered = false;
        }
        
        var onMove = function(event) {
            if (popup) {
                //Пока popup не готов, не будем ничего делать
                popup.calculatePosition(event, offsetX, offsetY);
            }
        }
        
        this.onOver = function(event) {
            onOut();
            hrefHovered = true;
            doLoad(function(content) {
                if(!content || !hrefHovered || destructed) return;//---
                popup = $('<div>').addClass('ps-bubble-holder').append(content).
                appendToBody(function() {
                    onMove(event);
                });
            });
        }
        
        
        this.onMove = onMove;
        this.onOut = onOut;
        this.onDestruct = function() {
            destructed = true;
            onOut();
        };
    
    },
    
    /* 
     * STICK - прикрепляет bubble к углу элемента, определяя видимую область
     */
    stick: function(doLoad, $href, onHide) {
        var popup = null;
        
        var hrefHovered = false;
        var bublHovered = false;
        
        var content = null;
        var canShow = false;
        
        var destructed = false;
        var freese = false;
        
        //Внутренние константы
        var offsetX = 3;        //Отступ bubble`а от курсора по горизонтали вправо
        var offsetY = 3;        //Отступ bubble`а от курсора по вуртикали вниз
        var hideInterval = 500; //Интервал времени, после которого bubble будет спрятан
        
        //Работа с таймером
        var timer = null;
        var timerStop = function() {
            if (timer) {
                timer.stop();
                timer = null;
            }
        }
        var timerStart = function(callback, delay) {
            timerStop();
            timer = new PsTimerAdapter(callback, delay).start();
        }
        
        //Очистка popup
        var clearPopups = function() {
            if (popup) {
                popup.remove();
                popup = null;
            }
        }
        
        //Полная очистка состояния
        var clearState = function() {
            timerStop();
            clearPopups();
            
            hrefHovered = false;
                                        
            content = null;
            canShow = false;
            
            $href.removeClass('ps-bubble-wait ps-bubble-done');
        }
        
        //Запуск таймера для полной очистки состояния
        var startHideTimer = function() {
            timerStop();
            if (freese || hrefHovered || bublHovered) return;
            if (popup) {
                timerStart(onHide, hideInterval);
            } else {
                onHide();
            }
        }
        
        
        /*
         * Функции управления
         */
        var onOut = function() {
            hrefHovered = false;
            startHideTimer();
        }
        
        var onShowPopup = function() {
            if (!canShow || destructed || !content || !hrefHovered) {
                return;//---
            }
            
            $href.removeClass('ps-bubble-wait').addClass('ps-bubble-done');
            
            timerStop();
            clearPopups();
            
            popup = $('<div>').addClass('ps-bubble-holder').append(content).hover(function() {
                bublHovered = true;
                timerStop();
            }, function() {
                bublHovered = false;
                startHideTimer();
            });
            popup.appendToBody(function() {
                if (popup) {
                    popup.calculatePosition($href, offsetX, offsetY);
                }
            });
        }
        
        var onOver = function() {
            timerStop();
            hrefHovered = true;
            
            if (popup) return;//---
            
            clearState();
            hrefHovered = true;
            
            $href.addClass('ps-bubble-wait');
            
            var taimerSays = false;
            var callbackSaid = false;
            var tryShow = function(_content, _canShow) {
                if (destructed) return;//---

                if (taimerSays) {
                    taimerSays = false;
                } else {
                    if (callbackSaid) {
                        return;//--- Уже обработали
                    } else {
                        callbackSaid = true;
                        if (_content) {
                            content = _content;
                        } else {
                            //Произошла ошибка загрузки, нечего показывать. Ошибку должен был показать onLoad.
                            $href.removeClass('ps-bubble-wait');
                            return;//---
                        }
                    }
                }
                
                if (_canShow === true) {
                    canShow = true;
                    /*
                     * Очистим таймер на случай, если пользователь нам передал true извне,
                     * а запущенный нами таймер ещё не отработал.
                     */
                    timerStop();
                }
                
                onShowPopup();
            }
            
            timerStart(function() {
                taimerSays = true;
                tryShow(null, true);
            }, 1000);

            doLoad(tryShow);
        }
        
        this.onDestruct = function() {
            destructed = true;
            clearState();
        };
        
        this.setFrosen = function(frosen) {
            freese = frosen;
            if (!frosen) {
                //Разморозка
                if(!bublHovered && !hrefHovered) {
                    onHide();
                } else {
                    startHideTimer();
                }
            }
        }
        
        this.hasChild = function($href) {
            return popup && !popup.find($href).isEmptySet();
        }
        
        this.onOver = onOver;
        this.onOut = onOut;
    }
}

/**
 * Дефолтные загрузчики/извлекатели данных, которые будут зарегистрированы в основном менеджере.
 */
var PsBubbleLoaderImpls = {
    EXTRACTORS: {
        dflt: function(onDone, $div) {
            onDone(PsBubble.wrap($($div.getFullHtml()).removeAttr('id').show()));
        },
        
        theorem: function(onDone, $div) {
            onDone($div.clone().removeAttr('id').addClass('bubbled').children().remove(':not(.th_head)').end());
        },
        
        example: function(onDone, $div) {
            onDone(PsBubble.wrap($div.clone().removeAttr('id').addClass('bubbled').children().remove('.ex_body_container').end()));
        },
        
        formula: function(onDone, $div) {
            onDone(PsBubble.wrap($div.children('div').html()));
        },
        
        formfield: function(onDone, $div) {
            onDone($div.clone().removeAttr('id').addClass('bubbled'));
        }
    },
    
    LOADERS: {
        //Загрузчик картинок
        image: function(onDone, $href) {
            PsResources.getImgSize($href.data('img'), function(wh, url) {
                var $ctt = wh ? crIMG(url).addClass('bubbled') : null;
                onDone($ctt);
            });
        },
        
        //Загрузчик элементов библиотек
        libStore: {},
        folding: function(onDone, $href) {
            var unique = $href.data(CONST.BUBBLE_LOADER_FOLDING_DATA);
            if(!unique) return;//---
            
            //Попробуем поискать в хранилище
            var $el = $('#'+CONST.BUBBLE_LOADER_FOLDING_STORE_ID+'>.'+unique);
            if(!$el.isEmptySet()) {
                onDone($el.clone(), true); //OK
                return;//---
            }
            
            var doOb = function(ob) {
                
                ob.waits.walk(function(onDoneRef) {
                    if (ob.state==2) {
                        onDoneRef(ob.data, true); //OK
                    }
                    if (ob.state==3) {
                        onDoneRef(null); //ERR
                    }
                });
                
                if (ob.state==3) {
                    InfoBox.popupError(ob.data); //Show ERR
                }
                
                //Сбросим слушателей, чтобы в случае очередного запроса этих данных мы не оповещали прежних подписчиков
                ob.waits = [];
            }
            
            var store = this.libStore;
            var ob = store[unique];
            if(!ob) {
                ob = {
                    state: 0,   //0-не загружали ещё, 1 - идёт загрузка, 2-ОК, 3-ERR
                    data: null, //Возвращённое содержимое
                    waits: []   //Список слушателей, ожидающих загрузки этих данных
                }
                store[unique] = ob;
            }
            
            ob.waits.push(onDone);
            
            switch(ob.state) {
                case 0:
                    ++ob.state;
                    AjaxExecutor.execute('LibBubble', {
                        unique: unique
                    }, function(data) {
                        ob.state = 2;
                        ob.data = data;
                        return ob;
                    }, function(err) {
                        ob.state = 3;
                        ob.data = err;
                        return ob;
                    }, doOb);
                    break;
                case 1:
                    //Загружаются данные
                    break;
                case 2:
                case 3:
                    doOb(ob);
                    break;
            }
        
        }
    }
}

function popupImg(src, text){
    var $el = isEmpty(text) ? crIMG(CONST.IMG_PREVIEW, 'preview').addClass('preview') : $('<span>').html(text);
    return $el.addClass(CONST.BUBBLE_HREF_MOVE_CLASS).data('loader', CONST.BUBBLE_LOADER_IMAGE).data('img', src);
}

jQuery(function() {
    PsBubble.registerExtractor(PsBubbleLoaderImpls.EXTRACTORS);
    PsBubble.registerLoader(PsBubbleLoaderImpls.LOADERS, PsBubbleLoaderImpls.LOADERS);
});
