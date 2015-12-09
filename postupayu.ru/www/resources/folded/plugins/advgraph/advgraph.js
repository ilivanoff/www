AdvGraphPlugin = null;
$(function() {
    
    var GrapherStore = PsLocalStore.inst('GrapherStore');
    
    //Контроллер кнопок управления функциями (добавление новых, редактирование, удаление)
    function AdvGraphFunctionsController($table, /*DekartFieldController*/df, listener) {
        var _this = this;
    
        var $TR_ADD = $table.find('tr.add');
        var $ADD_INPUT = $TR_ADD.find('input');
        var $TR_NEW = $table.find('tr.new').hide();
    
        new ButtonsController($TR_ADD.find('button'), {
            ctxt: this,
            click: function(action) {
                switch (action) {
                    case 'accept':
                        var f = $.trim($ADD_INPUT.val());
                        if (f) {
                            if (df.hasGr(f)){
                                InfoBox.popupError('График уже существует');
                                $ADD_INPUT.focus();
                                break;
                            } else {
                                this.onAdd(f);
                            }
                        }
                    case 'remove':
                        $ADD_INPUT.val('');
                        break;
                }

            }
        });
    
        //Всегда должен быть выбран один график
        this.checkChecked = function() {
            if(!$table.hasChild('tr.func .rb>input:checked')) {
                $table.find('tr.func .rb>input:first').setChecked(true).change();
            }
        }
    
        this.onAdd = function(_f) {
            if (df.hasGr(_f)) return;

            var f = _f;
        
            var $TR = $TR_NEW.clone().removeClass('new').addClass('func');
        
            //RadioButton
            var $RB = $TR.find('.rb>input');
            $RB.change(function() {
                if($RB.is(':checked')) {
                    //Radio button clicked
                    listener.onRadio(f);
                }
            }).attr('title', 'Показывать производную этого графика');

            //CheckBox
            var $CH = $TR.find('.cb>input');
            var VIS = true;
            $CH.change(function() {
                VIS = $(this).is(':checked');
                //FORMULA SHOWN
                if (VIS) {
                    listener.onShow(f);
                    $CH.attr('title', 'Скрыть график');
                } else {
                    listener.onHide(f);
                    $CH.attr('title', 'Показать график');
                }
            }).setChecked(VIS).attr('title', 'Скрыть график');

            //ColorPicker
            var $CP = $TR.find('.cp>input');
            COLOR = '#000000';
            $CP.PsColorPicker(function(color) {
                //FORMULA COLOR CHANGED
                COLOR = color;
                listener.onColorChange(f, color);
            }, COLOR);
        
            //Value TD
            var $VAL = $TR.find('.val').html('y = ' + f);
            var $INPUT;
        
            var BC = new ButtonsController($TR.find('button'), {
                ctxt: this,
                click: function(action) {
                    var checked = $RB.is(':checked');
                
                    switch (action) {
                        case 'edit':
                            $INPUT = $('<input>').attr('type', 'text').val(f);
                            $VAL.html('y = ').append($INPUT);
                            BC.recalcState({
                                edit: {
                                    visible: 0
                                },
                                accept: {
                                    visible: 1
                                },
                                remove: {
                                    visible: 1
                                }
                            });
                            $TR.addClass('edit');
                            $INPUT.focus().select();
                            break;

                        case 'accept':
                            var tmp = $.trim($INPUT.val());
                            if (tmp) {
                                $TR.removeClass('edit');
                                $VAL.html('y = ' + tmp);

                                BC.recalcState({
                                    edit: {
                                        visible: 1
                                    },
                                    accept: {
                                        visible: 0
                                    },
                                    remove: {
                                        visible: 1
                                    }
                                });
                            
                                if (f != tmp) {
                                    //FORMULA CHANGED
                                    listener.onChange(f, tmp, COLOR, VIS, checked);
                                }
                                f = tmp;
                            
                                break;
                            }
                        
                        case 'remove':
                            //FORMULA REMOVED
                            $TR.remove();
                            listener.onRemove(f, checked);
                        
                            _this.checkChecked();
                            break;
                    }
                }
            });
            BC.recalcState({
                accept: {
                    visible: 0
                }
            });
        
            $TR.show().insertBefore($TR_ADD);
            //FORMULA ADDED
            listener.onAdd(f, COLOR);
        
            this.checkChecked();
        }
    }

    //КОНТРОЛЛЕР ПОКАЗА ИНТЕГРАЛА
    function IntegralController(df, $BOX) {
        var intColor = GrapherStore.get('dekart_int_color', '#000000');
        $BOX.find('[type="text"]').PsColorPicker(function(color) {
            GrapherStore.set('dekart_int_color', color);
            intColor = color;
            df.setIntegralColor(intColor);
        }, intColor);
            
        //Выделим нужные нам элементы, заведём все переменные
        var F = null;
        var hasF = false;
        var $INFO = $BOX.find('.info');
        var $ERROR = $BOX.find('.error');
        

        /*
         * 0 - x1 не установлен
         * 1 - x2 не установлен
         * 2 - зафиксировано
         */
        var state = 0;
        var x1 = null, x2 = null;
        this.onHide = function(){
            df.clearIntegral();
        }
        
        this.onShow = function(f){
            x1 = null;
            x2 = null;
            state = 0;
            F = f;
            hasF = !!F;
            
            $ERROR.setVisible(!hasF);
            $INFO.setVisible(hasF);
            
            if(hasF) {
                var evX;
                var x0 = null; //Точка, которую пользователь выбрал первой (для выбора, какая будет x1, а какая - x2)
                df.bind({
                    move: function(e) {
                        evX = e.x;
                        switch (state) {
                            case 0:
                                x0 = evX;
                                x1 = evX;
                                updateInfo();
                                break;
                            case 1:
                                x1 = evX < x0 ? evX : x0;
                                x2 = evX < x0 ? x0 : evX;
                                df.clearIntegral();
                                df.putIntegral({
                                    y: F,
                                    bounds: [x0, evX],
                                    pen: [1, intColor]
                                });
                                updateInfo();
                                break;
                        }
                    },
                    click: function(e) {
                        evX = e.x;
                        var edit = false;
                        switch (state) {
                            case 0:
                                if(x1!==null){
                                    ++state;
                                }
                                break;
                            case 1:
                                if(x2!==null){
                                    ++state;
                                    edit = true;
                                }
                                break;
                            case 2:
                                df.clearIntegral();
                                x0 = evX;
                                x1 = evX;
                                x2 = null;
                                state=0;
                                break;
                        }
                        updateInfo(edit);
                    },
                    out: function() {
                        switch (state) {
                            case 0:
                                x1 = null;
                                updateInfo();
                                break;
                        }
                    }
                });
                updateInfo();
            }
        }
        
        var info = function(info, gray) {
            $INFO.html(gray ? PsHtml.span(info, 'gray') : info);
        }
        
        var updateInfo = function(bind) {
            var x1r = x1===null ? null : PsHtml.num2str(PsMath.round(x1, 2));
            var x2r = x2===null ? null : PsHtml.num2str(PsMath.round(x2, 2));
            
            switch (state) {
                case 0:
                    if (x1===null) {
                        info('Наведите на поле', true);
                    } else {
                        info(PsHtmlCnst.X1+'='+x1r);
                    }
                    break;
                case 1:
                case 2:
                    if(x2r===null) return;
                    info(PsHtmlCnst.X1+'='+PsHtml.span(x1r,'x1')+', '+PsHtmlCnst.X2+'='+PsHtml.span(x2r,'x2'));
                    //bindX1X2change();
                    break;
            }
            
            if(!bind) return;

            var $x1 = $INFO.find('.x1');
            var $x2 = $INFO.find('.x2');
            $x1.add($x2).click(function() {
                var $span = $(this);
                var $input = $('<input>').attr('type', 'text').val($.trim($span.html()));
                $span.hide();
                $input.insertAfter($span).width('40').focus().select();
                var isX1 = $span.is('.x1');
                $input.keyup(function() {
                    var num = $input.valEnsureIsNumber();
                    if (num===null) return;
                    num = 1*num;
                    if (isX1) {
                        x1 = num;
                    } else {
                        x2 = num;
                    }
                    df.clearIntegral();
                    df.putIntegral({
                        y: F,
                        bounds: [x1, x2],
                        pen: [1, intColor]
                    });
                }).blur(function() {
                    var min = Math.min(x1, x2);
                    var max = Math.max(x1, x2);
                    x1 = min;
                    x2 = max;
                    $input.remove();
                    $x1.html(PsHtml.num2str(PsMath.round(x1, 2))).show();
                    $x2.html(PsHtml.num2str(PsMath.round(x2, 2))).show();
                    updateInfo(true);
                });
            });
        }
    }
    //#IntegralController
    
    
    //КОНТРОЛЛЕР ПОКАЗА ПРОИЗВОДНОЙ
    function DerivativeController(df, $BOX) {
        var _this = this;
        
        var derColor = GrapherStore.get('dekart_der_color', '#000000');
        $BOX.find('[type="text"]').PsColorPicker(function(color) {
            GrapherStore.set('dekart_der_color', color);
            derColor = color;
            df.setDerivPen([2, derColor]);
        }, derColor);
            
        //Показывать/скрывать угол производной
        var showAngle = GrapherStore.get('dekart_dangle_show', true);
        $BOX.find('.ang input').change(function() {
            showAngle = $(this).is(':checked');
            GrapherStore.set('dekart_dangle_show', showAngle);
            df.setDerivShowAngle(showAngle);
        }).setChecked(showAngle).change();
        
        //Выделим нужные нам элементы, заведём все переменные
        var F = null;
        var hasF = false;
        var $INFO = $BOX.find('.info');
        var $ERROR = $BOX.find('.error');
        var $SUB = $BOX.find('.sub');


        var x = null; //Зафиксированная точка

        var info = function(info, gray) {
            $INFO.html(gray ? PsHtml.span(info, 'gray') : info);
        }

        var updateInfo = function(bind) {
            var deriv = df.getDeriv();
            var derX = deriv ? deriv.shape.o.x0 : null;
            
            if (x===null && derX===null) {
                //Ни точки, ни производной
                info("Наведите на поле", true);
            } else {
                //Есть производнай, но курсор уведён с поля
                var xStr = PsHtml.num2str(PsMath.round(x===null ? derX : x, 2));
                info(PsHtmlCnst.X0+'='+PsHtml.span(xStr, 'x0'));

                if(derX!==null && !bind) return; //---
                
                var $span = $INFO.find('.x0');
                $span.click(function() {
                    var $input = $('<input>').attr('type', 'text').val($.trim($span.html()));
                    $span.hide();
                    $input.insertAfter($span).width('40').focus().select();
                    $input.keyup(function() {
                        var num = $input.valEnsureIsNumber();
                        if (num===null) return;
                        derX = 1*num;
                        df.clearDeriv();
                        df.putDeriv({
                            f: F,
                            x0: derX,
                            pen: [2, derColor],
                            showAngle: showAngle
                        });
                    }).blur(function() {
                        $input.remove();
                        $span.html(PsHtml.num2str(PsMath.round(derX, 2))).show();
                        updateInfo(true);
                    });
                });

            }
        }

        //Кнопка проигрывания, сообщение об ошибке
        var CONTROLS = new IBStatesController($BOX.find('.play button'), {
            on_plays: function() {
                _this.startDerPlay();
            },
            on_stops: function() {
                _this.stopDerPlay();
            }
        });
        
        this.startDerPlay = function() {
            if (!hasF) return;

            df.playDeriv({
                f: F,
                color: function() {
                    return derColor;
                },
                onPut: function(der) {
                    x = der.x0;
                    updateInfo();
                },
                onEnd: function() {
                    CONTROLS.resetState();
                    x = null;
                    updateInfo();
                },
                showAngle: function() {
                    return showAngle;
                }
            });

            CONTROLS.setState('stops');
        }
    
        this.stopDerPlay = function() {
            df.stopDerivPlay();
            CONTROLS.resetState();
        }
        
        
        this.onHide = function(){
            this.stopDerPlay();
            df.clearDeriv();
        }
        
        this.onShow = function(f){
            x = null;
            F = f;
            hasF = !!F;
            
            $ERROR.setVisible(!hasF);
            $INFO.setVisible(hasF);
            $SUB.setVisible(hasF);
            CONTROLS.resetState();
            
            if(hasF) {
                df.bind({
                    move: function(e) {
                        x = e.x;
                        updateInfo();
                    },
                    click: function(e){
                        x = e.x;
                        _this.stopDerPlay();
                        df.clearDeriv();
                        df.putDeriv({
                            f: F,
                            x0: x,
                            pen: [2, derColor],
                            showAngle: showAngle
                        });
                        updateInfo();
                    },
                    out: function(){
                        x = null;
                        updateInfo(true);
                    }
                });
                updateInfo();
            }
        }
        
    }
    //#DerivativeController
    
    
    AdvGraphPlugin = {
        MODE: null,
        RADIO_F: null,
        
        init: function() {
            var _this = this;
            
            this.BODY = $('.advgraph_plugin');
            
            var MODUL_ID = 'AdvGraphPlugin';
            
            //Контроллер Декартова поля
            var df = new DekartFieldController(this.BODY, {
                id: MODUL_ID,
                originMove: true,
                scrollRescale: true
            });
            this.df = df;
        
            //Показать/скрыть справку по написанию формул
            this.BODY.find('.help .h').clickClbck(function() {
                this.parent().toggleClass('active');
            });
        
            //Табы (режимы работы)
            this.TABS = this.BODY.find('.modes>div').hide();
            
            var $triggerHrefs = this.BODY.find('.hg-next');
            this.HG = new HrefsGroup($triggerHrefs, 'next', {
                id: MODUL_ID,
                callback: function(mode) {
                    _this.MODE = mode;
                    _this.setAgMode();
                }
            });
        
            //СПИСОК ГРАФИКОВ
            this.GRAPHS = this.BODY.find('table.controls');

            this.FC = new AdvGraphFunctionsController(this.GRAPHS, df, {
                onAdd: function(f, color) {
                    df.putGr({
                        y: f,
                        pen: [2, color]
                    });
                },
                
                onShow: function(f) {
                    df.showGr(f);
                },
                
                onHide: function(f) {
                    df.hideGr(f);
                },
                
                onRemove: function(f, isRadio) {
                    df.clearGr(f);
                    if (isRadio) {
                        _this.RADIO_F = null;
                        _this.setAgMode();                    
                    }
                },
                
                onChange: function(of, nf, color, vis, isRadio) {
                    this.onRemove(of);
                    this.onAdd(nf, color);
                    if (!vis) {
                        this.onHide(nf);
                    }
                    if (isRadio) {
                        _this.RADIO_F = nf;
                        _this.setAgMode();
                    }
                },
                
                onRadio: function(f) {
                    _this.RADIO_F = f;
                    _this.setAgMode();
                },
                
                onColorChange: function(f, color) {
                    df.setGrPen(f, [2, color]);
                }
            });
        
            this.prepeared = true;
            
            this.HG.callbackCall();
        },
        prepeared: false,
        
        /*
         * Контроллер панели живёт от onHide до onShow.
         * Между этими событиями функция не меняется (надо учитывать, что функция может быть и не выбрана).
         */
        
        controller: null,
        setAgMode: function() {
            if (!this.prepeared) return;//---
            
            if (this.controller) {
                this.controller.onHide();
                this.controller = null;
            }
            this.df.unbind();
            
            this.TABS.hide();
            this.df.legendDisable();
            this.GRAPHS.removeClass('rb');
            
            if (this.MODE) {
                this.GRAPHS.addClass('rb');
                this.df.legendEnable(this.MODE);
                this.TABS.filter('.'+this.MODE).show();
                this.controller = this.getController(this.MODE);
                this.controller.onShow(this.RADIO_F);
            }
        },
        
        //Фабрика контроллеров
        controllers: new ObjectsStore(),
        getController: function(mode) {
            if(!this.controllers.has(mode)) {
                var ctrl = this.getControllerImpl(mode);
                if(!ctrl) return null;
                this.controllers.put(mode, ctrl);
            }
            return this.controllers.get(mode);
        },
        getControllerImpl: function(mode) {
            var $panel = this.TABS.filter('.'+mode);
            switch (mode) {
                case 'der':
                    return new DerivativeController(this.df, $panel);
                case 'int':
                    return new IntegralController(this.df, $panel);
            }
            return null;
        },
        
        /*
         * Методы, которые можно вызвать извне
         */
        setMode: function(mode) {
            this.HG.setState(mode);
        },
        
        addFunctions: function(arr) {
            arr = isString(arr) ? [arr] : arr;
            var _this = this;
            $.each(arr, function(i, val) {
                _this.FC.onAdd(val);
            });
        }
    }

    AdvGraphPlugin.init();
});
