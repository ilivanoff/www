$(function() {
    
    function KinematPlugin($BODY) {
        var _this = this;
        
        var MODUL_ID = 'KinematPlugin';
        
        var $BOXES = $BODY.find('.controls>.box');
        var $CALCS = $BODY.find('.calcs');

        var $HGS = $BODY.find('.hg-self');
        var $HGN = $BODY.find('.hg-next');
        
        /*
         * Контроллер переключения режимов
         */
        var HG = new HrefsGroup($HGS, 'self', {
            id: MODUL_ID
        });
        HG.cloneAndPlaceTo($HGN, 'next');
        
        $HGN.find('.help').click(function() {
            $HGS.toggleVisibility();
        });
        $HGS.find('.close span').click(function(){
            $HGS.hide();
        });
        
        /*
         * Декартова система координат
         */
        var df = new DekartFieldController($BODY, {
            id: MODUL_ID,
            dim: [10, 5, 20],
            origin: ['left', 'bottom'],
            originMove: true,
            scrollRescale: true
        });
        
        df.bind({
            rebuild: function(){
                recalcInitialConditions();
            }
        });
        
        //Вводимые
        var g = 9.8;
        var alpha = 45;
        var h0 = 4;
        var v0 = 8;
        
        var BOX = {
            get: function(id) {
                var $box = $BOXES.filter('.'+id);
                var $slider = $box.find('.slider');
                var $val = $box.find('.val');
                var $select = $box.find('select');
                return {
                    b: $box,
                    s: $slider,
                    v: $val,
                    sel: $select
                }
            },

            boxInit: function(id, options, range) {
                var ob = this.get(id);
                    
                //select
                ob.sel.change(function() {
                    var newVal = $(this).val();
                    if (!PsIs.number(newVal)) return;//---
                    BOX.setVal(id, newVal);
                });
                
                //slider
                options.slide = function(event, ui) {
                    BOX.setVal(id, ui.value);
                }
                ob.s.slider(options).sliderRange(range ? range : ob.sel);
                
                //Установим подписчик на событие изменения
                this.onChange.put(id, options.onChange);
                
                //Установим начальное значение
                this.setVal(id, options.value, true);
            },

            boxUpdate: function(id, options) {
                var ob = this.get(id);
                for(var name in options) {
                    ob.s.slider("option", name, options[name]);
                }
                ob.s.sliderRange(null, true);
            },
            
            values: {},
            onChange: new ObjectsStore(),
            //На вход либо [ключ/значение], либо объект
            setVal: function(keyOrObj, value, silent) {
                if (isString(keyOrObj)){
                    var params = {};
                    params[keyOrObj] = value;
                    keyOrObj = params;
                }
                
                for(var id in keyOrObj) {
                    var val = PsMath.round(keyOrObj[id], 2);
                    var ob = this.get(id);
                    ob.v.html(val);
                    ob.s.slider("value", val);
                    ob.sel.val(val);
                    this.values[id] = val;
                    
                    //Вызываем слушатель
                    if(silent) continue;
                    
                    this.onChange.doIfHas(id, function(callback) {
                        callback.call(_this, val);
                    });
                }
            },
            getVal: function(id){
                return PsIs.number(this.values[id]) ? this.values[id] : null;
            },
            
            setVisible: function(keyOrOb, visible) {
                if (isString(keyOrOb)){
                    var params = {};
                    params[keyOrOb] = visible;
                    keyOrOb = params;
                }
                for(var v in keyOrOb) {
                    this.get(v).b.setVisible(keyOrOb[v]);
                }
            },

            disable: function(valsOrKey, value) {
                if (isString(valsOrKey)){
                    var params = {};
                    params[valsOrKey] = value;
                    valsOrKey = params;
                }
                
                for(var id in valsOrKey) {
                    var s = this.get(id).s;
                    s.data('curVal', s.slider("value"))
                    s.slider("disable");
                    this.setVal(id, valsOrKey[id]);
                }
            },
            enable: function(ids) {
                ids = $.isArray(ids) ? ids : [ids];
                for(var i = 0; i < ids.length; i++) {
                    var id = ids[i];
                    var s = this.get(id).s;
                    var prevVal = s.data('curVal');
                    if (PsIs.number(prevVal)){
                        this.setVal(id, prevVal);
                    }
                    s.slider("enable");
                }
            },
            setEnabled: function(id, enabled, disabledValue) {
                if(enabled){
                    BOX.enable(id);
                }else{
                    BOX.disable(id, disabledValue);
                }
            }
        }
        
        //Угол запуска
        /*http://jqueryui.com/demos/slider/*/
        BOX.boxInit('a', {
            min: 0,
            max: 89,
            step: 1,
            value: alpha,
            onChange: function() {
                recalcInitialConditions();
            }
        });

        BOX.boxInit('h0', {
            min: 0,
            max: 10,
            step: 0.1,
            value: h0,
            onChange: function() {
                recalcInitialConditions();
            }
        });

        BOX.boxInit('v0', {
            min: 0,
            max: 10,
            step: 0.1,
            value: v0,
            onChange: function() {
                recalcInitialConditions();
            }
        });

        BOX.boxInit('g', {
            min: 0.1,
            max: 20,
            step: 0.1,
            value: g,
            onChange: function() {
                recalcInitialConditions();
            }
        });

        //ВременнАя шкала
        var $playBtn = crA().html('проиграть').addClass('play').clickClbck(function() {
            startPlay();
        });
        BOX.boxInit('t', {
            min: 0,
            max: 10,
            step: 0.01,
            value: 0,
            range: 'min',
            onChange: function(value) {
                stopPlay();
                drawTimeState(value);
            }
        }, $playBtn);

        /*
         * ПЕРЕСЧЁТ ПАРАМЕТРОВ
         * 
         * На основе данных параметров будут расчитываться все остальные
         */
        
        var sinA, cosA, tgA, vx, vy, H, Th, L, Tl;
        var trNum = 1;
        
        $CALCS.find('.rb').change(function(){
            trNum = $(this).parent().is('.tr1') ? 1 : 2;
            recalcInitialConditions();
        });
        
        var recalcInitialConditions = function() {
            g = BOX.getVal('g');
            h0 = BOX.getVal('h0');
            v0 = BOX.getVal('v0');
            alpha = BOX.getVal('a');
            
            //Вспомогательные функции
            function calcGlobals(alpha) {
                sinA = PsMath.sinGrad(alpha);
                cosA = PsMath.cosGrad(alpha);
                tgA = PsMath.tgGrad(alpha);
                
                vx = v0*cosA;
                vy = v0*sinA;
                
                Tl = (vy + Math.sqrt(PsMath.sq(vy)+2*g*h0))/g;
                L = vx*Tl;
            
                Th = vy/g;
                H = h0 + vy*Th-g*Th*Th/2;
            }
            
            function updateInfo(trNum, values) {
                for(var key in values) {
                    var val = values[key];
                    var isNum = PsIs.number(val);
                    $CALCS.find('.tr'+trNum+' .'+key).html(isNum ? PsMath.round(val, 2) : 'не определено').toggleClass("gray", !isNum);
                }
                $CALCS.toggleClass('single', trNum==1);
            }
        
            function putTraectory(trNum, alpha) {
                calcGlobals(alpha);
                updateInfo(trNum, {
                    a: alpha,
                    h: H,
                    th: Th,
                    l: L,
                    tl: Tl
                });
                
                df.putGr({
                    y: function(x){
                        return h0+x*tgA - g*PsMath.sq(x)/(2*PsMath.sq(v0)*PsMath.sq(cosA));
                    },
                    pen: [2, trNum==1 ? 'green' : '#ddd'],
                    bounds: [0, L],
                    id: 'gr'+trNum
                });
            }
            
            
            //Рисуем траектории
            df.shapeEraise(['gr1', 'gr2'], 'gr');
            putTraectory(1, alpha);
            if (h0==0 && alpha!=0 && alpha!=45){
                putTraectory(2, 90-alpha);
                if (trNum==1) {
                    calcGlobals(alpha);
                }
            }
            
            //Все параметры пересчитаны
            BOX.boxUpdate('t', {
                max: Tl
            });
            
            resetPlayMode();
        }
        
        /*
         * РИСОВАНИЕ В РАЗЛИЧНЫХ РЕЖИМАХ (траектория, скорость и т.д.)
         */
        
        var PlayFunctions = {
            __getByName: function(fName) {
                return fName && this.hasOwnProperty(fName) ? this[fName] : null;
            },
            
            // Рисование r=r0+v0*t+a*t^2/2
            trajectory: function(t) {
                var xt = vx*t;
                var yt = h0 + vy*t - g*t*t/2;
                var color = '#69f';
            
                df.putVector({
                    p1: [0, 0],
                    coords: [0, h0],
                    text: PsHtml.vector('r<sub>0</sub>'),
                    pen: color,
                    id: 'r0'
                });

                df.putVector({
                    p1: [0, h0],
                    coords: [vx*t, vy*t],
                    text: PsHtml.vector('v<sub>0</sub>')+'t',
                    pen: color,
                    id: 'v0t'
                });

                df.putVector({
                    p1: [vx*t, h0+vy*t],
                    coords: [0, -g*t*t/2],
                    text: PsHtml.vector('g')+'t<sup>2</sup>/2',
                    pen: color,
                    id: 'gt'
                });
            
                df.putVector({
                    p1: [0, 0],
                    coords: [xt, yt],
                    text: PsHtml.vector('r'),
                    pen: color,
                    id: 'r'
                });
            },
            
            // Рисование v=v0+gt
            speed: function(t) {
                var vyt = vy-g*t;
            
                var xt = vx*t;
                var yt = h0 + vy*t - g*t*t/2;
            
                df.putVector({
                    p1: [xt, yt],
                    coords: [vx/3, vyt/3],
                    pen: 'red',
                    text: PsHtml.vector('v'),
                    id: 'v'
                })

                df.putVector({
                    p1: [xt, yt],
                    coords: [vx/3, vy/3],
                    pen: 'green',
                    text: PsHtml.vector('v<sub>0</sub>'),
                    id: 'v0'
                })

                df.putVector({
                    p1: [xt+vx/3, yt+vy/3],
                    coords: [0, -g*t/3],
                    pen: 'blue',
                    text: PsHtml.vector('g')+'t',
                    id: 'gt'
                })  

                df.putVector({
                    p1: [xt, yt],
                    coords: [0, -g/3],
                    pen: '#444',
                    text: PsHtml.vector('g'),
                    id: 'g'
                })
            },
            
            // Рисование v=vx+vy
            projections: function(t) {
                var vyt = vy-g*t;
            
                var xt = vx*t;
                var yt = h0 + vy*t - g*t*t/2;
            
                df.putVector({
                    p1: [xt, yt],
                    coords: [vx/3, vyt/3],
                    pen: 'red',
                    text: PsHtml.vector('v'),
                    id: 'v'
                })

                df.putVector({
                    p1: [xt, yt],
                    coords: [vx/3, 0],
                    pen: 'green',
                    text: PsHtml.vector('v<sub>x</sub>'),
                    id: 'vx'
                })

                df.putVector({
                    p1: [xt, yt],
                    coords: [0, vyt/3],
                    pen: 'blue',
                    text: PsHtml.vector('v<sub>y</sub>'),
                    id: 'vy'
                })
            }
        }
        
        /*
         * Механизмы рисования
         */

        var playFunction = null;
        var playFunctionName = null;
        
        var clearTimeState = function() {
            df.clearVectors();
        }

        var drawTimeState = function(t) {
            t = PsMath.num2bounds(t, [0, Tl]);
            playFunction.call(_this, t);
            BOX.setVal('t', t, true);
        }

        var setPlayMode = function(mode) {
            playFunctionName = mode;
            resetPlayMode();
        }
        
        var resetPlayMode = function() {
            BOX.setVisible('t', false);
            
            var wasPlaying = playing;
            stopPlay();
            clearTimeState();

            playFunction = PlayFunctions.__getByName(playFunctionName);
            if (!playFunction) return;//---
            
            BOX.setVisible('t', true);
            
            var nowT = BOX.getVal('t');
            if (wasPlaying) {
                startPlay(nowT);
            } else {
                drawTimeState(nowT);
            }
        }
        
        var curT;
        var playing = false;
        var interval = new PsIntervalAdapter(function() {
            if (curT > Tl) {
                curT = Tl;
                drawTimeState(curT);
                stopPlay();
            } else {
                drawTimeState(curT);
                curT+=0.05;
            }
        }, 100, true);

        var stopPlay = function() {
            interval.stop();
            playing = false;
        }

        var startPlay = function(fromTime) {
            stopPlay();
            curT = fromTime ? fromTime : 0;
            playing = true;
            interval.restart();
        }
        
        recalcInitialConditions();
        
        //Контроллер установки режима проигрывания
        HG.callbackSet(function(mode) {
            setPlayMode(mode);
        }).callbackCall();
    }

    new KinematPlugin($('.kinemat_plugin'));
});
