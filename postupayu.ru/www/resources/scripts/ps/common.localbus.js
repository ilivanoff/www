/*
 * ЛОКАЛЬНАЯ ШИНА
 * 
 * Работает через хранилище. Мы создаём свой экземпляр хранилища, подписываемся на все его события '*',
 * после кладём и вычитываем свои сообщения.
 * 
 * Если одно и то-же событие может произойти сразу несколько раз подряд, то его лучше отправлять в отложенном режиме,
 * чтобы не загружать шину однотипными событиями.
 */
var PsLocalBus = {
    deferredDelay: 5000,
    logger: PsLogger.inst('PsLocalBus').setTrace()/*.disable()*/,
    
    EVENT: {
        LOGIN: 'LOGIN', /* Пользователь авторизовался */
        LOGOUT: 'LOGOUT', /* Пользователь разлогинился */
        FAVORITES: 'FAVORITES', /* Изменён список избранных приложений */
        POINTSGIVEN: 'POINTSGIVEN', /* Пользователю были даны очки */
        DISCUSSIONMSG: 'DISCUSSIONMSG' /* Событие с сообщением в дискуссии */
    },
    
    BUS: null,
    INST: function() {
        if (this.BUS) return this.BUS;
        //Имплементация шины
        this.BUS = new function() {
            
            var STORE = PsLocalStore.inst('PsLocalBus');
            var LOGGER = PsLocalBus.logger;
            var LISTENERS = new ObjectsStore();
            var SESSION_ID = PsRand.string(5);
            
            var started = false;
            var cleaned = false;

            var EVENTS = {
                hasType: function(type) {
                    return PsLocalBus.EVENT.hasOwnProperty(type);
                },
                make: function(EventType, data, time, session) {
                    return {
                        type: EventType,
                        data: PsObjects.clone(data, [], true), //Если передан объект с функциями - отбросим их автоматически
                        time: PsIs.number(time) ? time : null,
                        session: session ? session : null,
                        toString: function() {
                            return PsObjects.toStringData(this);
                        },
                        equals: function(event) {
                            return this.toString() == event.toString();
                        },
                        isSameSession: function() {
                            return this.session==SESSION_ID;
                        }
                    }      
                },
                getLifetimeMs: function(event) {
                    return Math.max(new Date().getTime() - event.time, 0);
                },
                serialize: function(event) {
                    return JSON.stringify(PsObjects.clone(event, [], true));
                },
                deserialize: function(sign, jsonString) {
                    if (!sign || !jsonString) return null;//---
                    var event = null;
                    var onError = function(err) {
                        event = null;
                        LOGGER.logError('Event {}.{} deserialization error: ' + err, sign, jsonString);
                    }
                    try {
                        event = JSON.parse(jsonString);
                        //Проверим наличие всех обязательных свойств в событии.
                        ['type', 'time', 'session'].walk(function(key) {
                            if (event && !event.hasOwnProperty(key)) {
                                onError('not has key ' + key);
                            }
                        });
                    } catch(err) {
                        onError(err);
                    }
                    
                    return event ? this.make(event.type, event.data, event.time, event.session) : null;
                }
            }

            //Очистка старых сообщений из хранилища
            function doClean() {
                if (cleaned) return;//---
                cleaned = true;
                var allEvents = STORE.toObject();
                if(!PsObjects.hasKeys(allEvents)) return;//---
                LOGGER.logDebug('Cleaning old bus events...');
                $.each(allEvents, function(sign, jsonString) {
                    var event = EVENTS.deserialize(sign, jsonString);
                    if(!event && sign && jsonString) {
                        LOGGER.logWarn(' Event {} is corrupted, remove...', sign);
                        STORE.remove(sign);
                    }
                    if(!event) return;//---
                    var lifetime = EVENTS.getLifetimeMs(event);
                    var lifetimeS = Math.round(lifetime/1000);
                    var doRemove = lifetime > 5 * PsLocalStore.readTimeout;
                    LOGGER.logDebug(' Event {} {} lifetime: {} sec. {}', sign, event, lifetimeS, doRemove ? 'Remove!' : '');
                    STORE.remove(sign);
                });
            }
            
            /*
             * Метод вызывается при получении события из хранилища. Важный момент - когда мы очищаем старые события из хранилища,
             * мы получим событие с jsonString=undefined. Поэтому лучше шину очищать в самом начале (до добавления слушателей),
             * а пустые события - просто игнорировать.
             */
            var onEvent = function (jsonString, sign) {
                LOGGER.logTrace('Event {}.{} is received...', sign, jsonString);
                var event = EVENTS.deserialize(sign, jsonString);
                if(!event) return;//---
                LOGGER.logInfo('Event received... {}. Sign: {}. Listeners: {}. Same session ? {}.', 
                    event, sign, LISTENERS.get(event.type, []).length, event.isSameSession());
                LISTENERS.doIfHas(event.type, function(listeners) {
                    listeners.walk(function(ob) {
                        ob.f.call(ob.ctxt, event.data, event.isSameSession());
                    });
                });
            }
            
            function doStart() {
                if (started || LISTENERS.isEmpty()) return;//---
                started = true;
                doClean();
                LOGGER.logDebug('Bus actually started... Session id={}', SESSION_ID);
                STORE.addEvent('*', onEvent);
            }
            
            function doStop() {
                if (!started || !LISTENERS.isEmpty()) return;//---
                started = false;
                LOGGER.logDebug('Bus actually stopped...');
                STORE.removeEvent('*', onEvent);
            }
            
            var comparator = function(listener, ob) {
                return listener===ob.f;//---
            }

            function doAssert(check, msg) {
                if(check) return true;
                var params = PsUtil.functionArgs2array(arguments);
                params.shift();
                LOGGER.logError.apply(LOGGER, params);
                return false;
            }
            
            var ASSERT = {
                enabled: function() {
                    return doAssert(PsLocalStore.isEnabled, 'Локальное хранилище не работает, шина отключена');
                },
                hasEventType: function(EventType) {
                    return doAssert(EVENTS.hasType(EventType), 'Тип события {} не зарегистрирован', EventType);
                },
                validCallback: function(callback) {
                    return doAssert($.isFunction(callback), 'В качестве слушателя передана не функция');
                }
            }
            
            var doFire = function(EventType, data, isDeferred) {
                var sign = PsRand.string(5);
                var event = EVENTS.make(EventType, data, new Date().getTime(), SESSION_ID);
        
                LOGGER.logInfo('Fire {}event... {}. Sign: {}', isDeferred ? 'deferred ' : '', event, sign);

                if(!ASSERT.enabled()) return;//---
                if(!ASSERT.hasEventType(EventType)) return;//---
                
                var serialized = EVENTS.serialize(event);
                LOGGER.logTrace('Serialized: {}', serialized);
                STORE.set(sign, serialized);
            }
            
            //Внешние методы
            this.connect = function(EventType, callback, ctxt) {
                LOGGER.logInfo('Connected... Тип события: [{}]', EventType);
                
                if(!ASSERT.enabled()) return;//---
                if(!ASSERT.hasEventType(EventType)) return;//---
                if(!ASSERT.validCallback(callback)) return;//---
                
                //Защитимся от повторного добавления слушателя
                if (LISTENERS.get(EventType, []).contains(callback, comparator)) {
                    LOGGER.logTrace('Данный слушатель уже ранее регистрировался');
                    return;//Такой слушатель уже добавлен---
                };
        
                //Такого callback ранее небыло, регистрируем
                LISTENERS.putToArray(EventType, {
                    f: callback,
                    ctxt: ctxt
                });
                
                //Проверяем, не нужно ли стартовать шину
                doStart();
            }
            
            this.disconnect = function(EventType, callback) {
                LOGGER.logInfo('Disconnected... Тип события: [{}]', EventType);

                if(!ASSERT.enabled()) return;//---

                //Удаляем слушателя
                var listeners = LISTENERS.get(EventType, []).removeValue(callback, comparator);
                if(!listeners.length){
                    LOGGER.logTrace('Все слушатели событий {} удалены', EventType);
                    LISTENERS.remove(EventType);
                }
        
                //Проверяем, можно ли остановить шину
                doStop();
            }
            
            this.fire = function(EventType, data) {
                doFire(EventType, data);
            }
            
            var deferredEvent = null;
            var deferredTimer = new PsTimerAdapter(function() {
                if (!deferredEvent) return;//---
                var _evt = deferredEvent;
                deferredEvent = null;
                doFire(_evt.type, _evt.data, true);
            }, PsLocalBus.deferredDelay);
            
            var onPageUnload = function() {
                if (!deferredEvent) return;//---
                LOGGER.logInfo('Страница перезагружается, форсированно отправляем отложенное событие');
                deferredTimer.flush();
            }
            
            this.fireDeferred = function(EventType, data) {
                var event = EVENTS.make(EventType, data);
                //ОТПРАВКА В ОТЛОЖЕННОМ РЕЖИМЕ
                LOGGER.logInfo('Schedule deffered event... {}', event);
        
                if(!ASSERT.enabled()) return;//---
                if(!ASSERT.hasEventType(EventType)) return;//---
                
                if (deferredEvent) {
                    if (event.equals(deferredEvent)) {
                        LOGGER.logDebug('Событие {} ожидает отправки, начнём отсчёт заново', deferredEvent);
                        deferredTimer.stop().start();
                        return;//---
                    } else {
                        LOGGER.logDebug('Событие {} ожидает отправки, отправляем его форсированно', deferredEvent);
                        deferredTimer.flush();
                    }
                }

                //Если пользователь нажмёт f5 или закроет страницу, мы должны будем отправить отложенные события форсированно
                PsUnloadListener.addListener(onPageUnload);

                deferredEvent = event;
                deferredTimer.start();
            }
            
        }
        return this.BUS;
    },

    connect: function(EventType, callback, ctxt) {
        this.INST().connect(EventType, callback, ctxt);
    },
    
    disconnect: function(EventType, callback) {
        this.INST().disconnect(EventType, callback);
    },
    
    //ОТПРАВКА
    fire: function(EventType, data) {
        this.INST().fire(EventType, data);
    },
    
    fireDeferred: function(EventType, data) {
        this.INST().fireDeferred(EventType, data);
    }
}