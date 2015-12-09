/*
 * Контроллер декартовой сетки
 */
var DekartFieldController = function($ModulDiv, options) {
    var _this = this;
    
    var logger = PsLogger.inst('Dekart').setInfo();
    
    var store = PsLocalStore.WIDGET('DekartFieldController');
    
    /*
    НАСТРОЙКИ

    В настройки можно передать как видимость всех элементов, так и их начальное состояние
    
    axes: {
        en: 0,
        vis: 0
    }
     */
    options = $.extend({
        id: null,           //id - если передан, то настройки поля будут сохранены (показ сетки и т.д.)
        dim: [10, 1, 20],   //Размеры, в которых меняется масштаб
        origin: null,       //[left, top] - положение начала координа в пикселях
        originMove: false,  //Флаг включения возможности перетаскивания начала координат
        scrollRescale: false //Флаг изменения масштаба по скроллингу (также учитываются переданные размеры)
    }, options);
    
    //Флаг - отмечать ли точки, по которым строится график
    var MarkGrPoints = false;
    
    //Определяем модуль
    $ModulDiv = $ModulDiv.extractTarget('.DekartModul');
    
    var $ctrl = $ModulDiv.children('.DekartCtrl');
    var $field = $ModulDiv.children('.Dekart');
    var $grid = $field.find('.Grid');
    
    var CtrlButtons = new ButtonsController(
        $ctrl.find('button'), {
            ctxt: this
        });
    
    
    var W = PsMath.num2bounds($field.cssDimension('width'), [100, 680]);
    $field.width(W+1).height(W+1);//Прибавим 1px к границам, чтобы помещалась сетка
    var A = W/2;
    
    
    //Определяем исходное положение начала координат
    var originL = options.origin ? options.origin[0] : A;
    var originT = options.origin ? options.origin[1] : A;
    
    if(isString(originL)) {
        originL = {
            left: 0,
            center: A,
            right: W
        }
        [originL];
    }
    
    if(isString(originT)) {
        originT = {
            top: 0,
            center: A,
            bottom: W
        }
        [originT];
    }
    
    options.origin = [originL, originT];
    
    
    //Глобальные переменные
    var SCALE, minX, maxX, minY, maxY, l0, t0;
    
    //УТИЛИТНЫЕ КЛАССЫ
    var jsH = {
        point: function(xy) {
            return new jsPoint(xy[0], xy[1]);
        },
        points: function(array) {
            var res = [];
            $.each(array, function(i, arr) {
                res.push(jsH.point(arr));
            });
            return res;
        },
        color: function(color) {
            return new jsColor(color);  
        },
        parsePen: function(cw) {
            var wid = 1;
            var clr = 'black';
            if(!cw) {
            } else if(isString(cw)){
                clr = cw;
            } else if(PsIs.number(cw)){
                wid = 1*cw;
            } else {
                var wIdx = PsIs.number(cw[0]) ? 0 : 1;
                var cIdx = (wIdx+1)%2;
                wid = cw[wIdx];
                clr = cw[cIdx];
            }
            return {
                c: clr,
                w: wid
            }
        },
        pen: function(cw) {
            var pen = jsH.parsePen(cw);
            return new jsPen(jsH.color(pen.c), pen.w);
        }
    }
    
    var l2p = {
        l: function(x) {
            return x*SCALE+l0;
        },
        t: function(y) {
            return t0-y*SCALE;
        },
        boundsX: function(bounds) {
            return bounds ? PsMath.interval2bounds(bounds, [minX, maxX]) : [minX, maxX];
        },
        boundsY: function(bounds) {
            return bounds ? PsMath.interval2bounds(bounds, [minY, maxY]) : [minY, maxY];
        },
        isXin: function(x, boundsX) {
            return PsIntervals.isIn(x, this.boundsX(boundsX));
        },
        isYin: function(y, boundsY) {
            return PsIntervals.isIn(y, this.boundsY(boundsY));
        },
        isIn: function(x, y, boundsX, boundsY) {
            if($.isArray(x)){
                y = x[1];
                x = x[0];
            }
            return this.isXin(x, boundsX) && this.isYin(y, boundsY);
        },
        e2lt: function(event) {
            return {
                l: event.pageX - $field.offset().left,
                t: event.pageY - $field.offset().top
            }
        }
    }
    
    var p2l = {
        x: function(l) {
            return (l-l0)/SCALE;
        },
        y: function(t) {
            return (t0-t)/SCALE;
        },
        isIn: function(l, t){
            return (PsIs.number(l) && l>=0 && l<=W) && (!isDefined(t) || (PsIs.number(t) && t>=0 && t<=W));
        },
        e2xy: function(event, round) {
            var lt = l2p.e2lt(event);
            var x = this.x(lt.l);
            var y = this.y(lt.t);
            
            return {
                x: round ? Math.round(x) : x,
                y: round ? Math.round(y) : y,
                eq: function(xy) {
                    return xy && xy.x==this.x && xy.y==this.y;
                }
            }
        },
        dl2dx: function(dl) {
            return dl / SCALE;
        }
    }
    
    
    //ИНИЦИАЦИЯ ГРАФИЧЕСКОГО АДАПТЕРА
    var gr = new jsGraphics($field.get(0));
    gr.setCoordinateSystem("cartecian");
    gr.setOrigin(jsH.point(options.origin));
    
    //Добавление класса к Декартову полю
    this.toggleFieldClass = function(_class, add) {
        $field.toggleClass(_class, add);
    }
    
    this.buildField = function() {
        SCALE = W/MAX;
        gr.setScale(SCALE);
        
        var or = gr.getOrigin();
        l0 = or.x;
        t0 = or.y;
        minX = -l0/SCALE;
        maxX = MAX + minX;
        
        maxY = t0/SCALE;
        minY = maxY - MAX;
        
        this.state.update();
        
        this.buildGridLair();
        this.shapeRedraw();
        
        //$field.children().not($grid).not($info).remove();
        
        LISTENERS.each(function(k, listener) {
            if (listener.rebuild) {
                listener.rebuild.call(_this, _this.state);
            }
        });
    }
    
    /*
     * ВКЛ/ОТКЛ перетаскивания начала координат
     */
    var ORIGIN_DRAG_LISTENER = 'ORIGIN_DRAG_LISTENER';
    
    this.setOriginDragEnabled = function(enabled) {
        this.unbind(ORIGIN_DRAG_LISTENER);
        this.toggleFieldClass('originDrag', enabled);
        
        if(!enabled) return;
        
        var dlt = null;
        this.bind({
            down: function(e) {
                /*
                 * Фиксируем разницу между точкой клика и началом координат,
                 * она должна сохраниться при перетаскивании.
                 */
                dlt = {
                    l: l0-e.l,
                    t: t0-e.t
                }
            },
            up: function() {
                dlt = null;
            },
            move: function(e) {
                if(dlt) {
                    _this.setOrigin([e.l+dlt.l, e.t+dlt.t], 10);
                }
            },
            out: function() {
                dlt = null;
            }
        }, ORIGIN_DRAG_LISTENER);
    },
    
    CtrlButtons.recalcState({
        origin: {
            visible: options.originMove
        }
    }).setCallbacks({
        on_origin: function(isOn) {
            _this.setOriginDragEnabled(isOn);
        }
    });
    
    /*
     * GRID
     */
    this.buildGridLair = function() {
        $grid.empty();
        var minXr = Math.ceil(minX);
        var maxXr = Math.floor(maxX);
        
        //Подписи под метками втодль оси Ох ставим по умолчанию - снизу.
        var xMarkTextDist=null;
        function poseXmarktext($div) {
            if(xMarkTextDist===null) {
                var h = $div.height();
                h = h<=1 ? 12 : h;
                xMarkTextDist = t0+h+1<=W ? t0+1  : t0-h-1;
            }
            $div.css('top', xMarkTextDist);
        }
        
        for (var x = minXr; x <= maxXr; x++) {
            var l = l2p.l(x);
            $('<div>').addClass('line').css('left', l).appendTo($grid).height(W);
            $('<div>').addClass('mark').css('left', l).css('top', t0-1).height(3).appendTo($grid);
            poseXmarktext($('<div>').addClass('markText').css('left', l+3).html(PsHtml.num2str(x)).appendTo($grid));
        }
        
        var minYr = Math.ceil(minY);
        var maxYr = Math.floor(maxY);
        for (var y = minYr; y <= maxYr; y++) {
            var t = l2p.t(y);
            $('<div>').addClass('line').css('top', t).appendTo($grid).width(W);
            $('<div>').addClass('mark').css('top', t).css('left', l0-1).width(3).appendTo($grid);
            if(y==0) continue;//Мы уже поставили отметку ноля
            $('<div>').addClass('markText').css('top', t+1).css('left', l0+3).html(PsHtml.num2str(y)).appendTo($grid);
        }
        
        //Оси
        if(PsIntervals.isIn(l0, [0, W])) {
            $('<div>').addClass('axe').css('top', 0).css('left', l0).appendTo($grid).height(W);
        }
        if(PsIntervals.isIn(t0, [0, W])) {
            $('<div>').addClass('axe').css('left', 0).css('top', t0).appendTo($grid).width(W);
        }
    }
    
    //Всё поле зависит от расположения начала координат и кол-ва ячеек в строке
    //checkShift - проверка сдвига, иначе ничего не сдвигаем 
    //(нужно для перетаскивания, чтобы не перерисовывать всё поле при мельчайшем сдвиге)
    this.setOrigin = function(p, checkShift) {
        var or = gr.getOrigin();
        if (or && checkShift && Math.abs(or.x-p[0])<checkShift && Math.abs(or.y-p[1])<checkShift) {
            return; //---
        }
        gr.setOrigin(jsH.point(p));
        this.buildField();
    }
    
    
    // 1. Изменение масштаба
    var WHEEL_SCALE_LISTENER = 'WHEEL_SCALE_LISTENER';
    
    var scaleManager = {
        min: null,
        max: null,
        
        initial: null,
        
        $select: null,
        canResize: false,
        
        init: function() {
            this.canResize = !PsIs.number(options.dim);
            var $select = $ctrl.children('.max').setVisible(this.canResize).children('select').empty();
            if (this.canResize) {
                this.initial = options.dim[0];
                this.min = options.dim[1];
                this.max = options.dim[2];
                
                for (var i = this.min; i <= this.max; i++) {
                    $select.append($('<option>').val(i).html('&nbsp;'+i+'&nbsp;'));
                }
                
                var _SM_ = this;
                $select.change(function() {
                    _SM_.setScale($select.val());
                });
                
                this.$select = $select;
            } else {
                this.initial = options.dim;
            }
            
            options.scrollRescale = options.scrollRescale && this.canResize;
            CtrlButtons.recalcState({
                rescale: {
                    visible: options.scrollRescale
                }
            }).setCallbacks({
                on_rescale: function(isOn) {
                    scaleManager.setScrollingEnabled(isOn);
                }
            });
            
            this.setInitialScale(true);
        },
        
        setInitialScale: function(silent) {
            this.setScale(this.initial, silent);
        },
        
        setScale: function(val, silent) {
            MAX = 1*val;
            if(this.$select) this.$select.val(MAX);
            if(silent) return;
            _this.buildField();
        },
        
        onScroll: function(dy) {
            var val = MAX + (dy>0 ? 1 : -1);
            if (val<this.min || val>this.max) return;
            this.setScale(val);
        },
        
        setScrollingEnabled: function(enabled) {
            if(!this.canResize) return;
            
            _this.unbind(WHEEL_SCALE_LISTENER);
            
            if(!enabled) return;
            
            var _SM_ = this;
            _this.bind({
                enter: function() {
                    PsMouseWheelHelper.addListener(_SM_.onScroll, true, _SM_);
                },
                out: function() {
                    PsMouseWheelHelper.removeListener(_SM_.onScroll);
                }
            }, WHEEL_SCALE_LISTENER);
        }
    }
    scaleManager.init();
    
    function CheckBoxController(options, labelClass) {
        var opsOb = options[labelClass];
        var en = !!(opsOb ? opsOb.en : true);
        var vis = !!(opsOb ? opsOb.vis : true);
        
        //Работаем с хранилищем, если передан id и чек-бокс виден
        var storeId = options.id && vis ? 'dekart_'+options.id+'_'+labelClass : null;
        var initEnable = storeId ? store.get(storeId, en) : en;
        var $label = $ctrl.children('label.'+labelClass);
        $label.find('input').change(function() {
            var enable = $(this).is(':checked');
            if (storeId) {
                store.set(storeId, enable);
            }
            $grid.toggleClass(labelClass, enable);
        }).setChecked(initEnable).change();
        $label.setVisible(vis);
    }
    // 2. Оси
    new CheckBoxController(options, 'axes');
    // 3. Метки (засечки)
    new CheckBoxController(options, 'marks');
    // 4. Подписи под метками
    new CheckBoxController(options, 'marksText');
    // 5. Сетка
    new CheckBoxController(options, 'grid');
    
    
    CtrlButtons.recalcState({
        clear: {
            visible: options.scrollRescale || options.originMove
        }
    }).setCallbacks({
        on_clear: function() {
            scaleManager.setInitialScale(true);
            this.setOrigin(options.origin);
        }
    });
    
    
    // --== PUTS ==--
    
    /*Возвращает точки, в которых переданная прямая "пересекает" границы Декартова поля*/
    var getLineBounds = function(p1, p2, sortByY, boundsX, boundsY) {
        boundsX = l2p.boundsX(boundsX);
        boundsY = l2p.boundsY(boundsY);
        return p1 && p2 && boundsX && boundsY ? PsIntervals.line2rectangle(boundsX, boundsY, p1, p2, sortByY) : null;
    }
    
    /*
     * Проверяет, имеет ли хоть одна сторона фигуры пересечение с Декартовым полем
     * При этом, если стоит режим заполнения, проверены будут все диагонали фигуры
     */
    var isCanDraw = function(options) {
        options = $.extend({
            points: [],
            fill: false,
            close: false,
            limited: true
        }, options);
        
        var points = options.points;
        var pointsCnt = points.length;
        if (pointsCnt<2) return false;
        
        var i, j, b;
        if(options.fill) {
            for(i=0; i<pointsCnt; i++) {
                for(j=i+1; j<pointsCnt; j++) {
                    b = getLineBounds(points[i], points[j]);
                    if(b && (!options.limited || b.lim)){
                        return true;
                    }
                }
            }
        } else {
            for(i=0; i<pointsCnt; i++) {
                j = i+1==pointsCnt ? 0 : i+1;
                if(j==0 && !options.close) continue;
                b = getLineBounds(points[i], points[j]);
                if(b && (!options.limited || b.lim)){
                    return true;
                }
            }
        }
        
        return false;
    }
    
    //Менеджер по работе с фигурами
    var SHAPES = {
        shapes: [],
        
        level: 0,
        sh: null,
        shRedraw: null,
        startShape: function(fnc, opt) {
            if(++this.level==1) {
                if (this.shRedraw) {
                    return this.sh = this.shRedraw;//---
                }
                
                if(opt.id) {
                    this.eraise(opt.id, fnc);
                }
                
                this.sh = {
                    f: fnc, //function
                    o: opt, //options
                    d: $(), //divs
                    addDiv: function($div){
                        this.d = this.d.add($div);
                    },
                    eraise: function() {
                        this.d.remove();
                        this.d = $();
                        this.drawed = false;
                        this.lastRedrawCommand = null;
                    },
                    redraw: function(opts) {
                        this.eraise();
                        this.lastRedrawCommand = opts ? opts : {};
                        this.redrawImpl();
                    },
                    
                    drawed: true,
                    visible: true,
                    lastRedrawCommand: null,
                    setVisible: function(visible) {
                        this.visible = visible;
                        this.redrawImpl();
                        if (this.drawed){
                            this.d.setVisible(visible);
                        }
                    },
                    
                    redrawImpl: function() {
                        if (this.visible && this.lastRedrawCommand) {
                            this.o = $.extend(this.o, this.lastRedrawCommand);
                            this.lastRedrawCommand = null;
                            SHAPES.shRedraw = this;
                            this.f.call(_this, this.o);
                            this.drawed = true;
                        }
                    }
                }
                this.shapes.push(this.sh);
            }
            return this.sh;
        },
        endShape: function() {
            if(--this.level==0) {
                this.sh.d.addClass(this.sh.o.classes);
                this.sh = null;
                this.shRedraw = null;
            }
        },
        
        __processShapes: function(id, drawFunc, callback, excludeBeforeProcess, invertIdSelection) {
            //В качестве ID можно передать: id, массив id, функцию
            var isTake4Id = function(shape) {
                if(!id) return true;
                if($.isFunction(id)) return id.call(_this, shape);
                if(id===shape) return true;
                return shape.o.id && PsArrays.inArray(shape.o.id, $.isArray(id) ? id : [id]);
            }
            
            //В качестве функции может быть передана и строка, тогда ищем среди функций рисования
            if (isString(drawFunc)) {
                drawFunc = _this['put'+drawFunc.firstCharToUpper()];
                //Если передана невалидная группа - ничего не делаем
                if(!$.isFunction(drawFunc)) return []; //---
            }
            
            
            //Начинает отбор
            var shapesSelected = [];
            var shapesDeselected = [];
            
            $.each(this.shapes, function(i, shape) {
                //Если условия не переданы - берём фигуру
                var take4type = drawFunc ? shape.f===drawFunc : true;
                var take4id = take4type ? isTake4Id(shape) : false;
                
                //По типу ограничиваем сразу. Воспринимаем тип, как сужение области действия
                if(take4type && ((take4id && !invertIdSelection) || (!take4id && invertIdSelection))) {
                    shapesSelected.push(shape);
                }
                else {
                    shapesDeselected.push(shape);
                }
            });
            
            if(excludeBeforeProcess){
                this.shapes = shapesDeselected;
            }
            
            if(callback) {
                $.each(shapesSelected, function(i, shape) {
                    callback.call(_this, shape);
                });
            }
            return shapesSelected;
        },
        
        //Физическая работа с фигурами
        get: function(id, drawFunc) {
            return this.__processShapes(id, drawFunc);
        },
        
        eraise: function(id, drawFunc) {
            this.__processShapes(id, drawFunc, function(ob) {
                ob.eraise();
            }, true);
        },
        
        eraiseExcept: function(id, drawFunc) {
            this.__processShapes(id, drawFunc, function(ob) {
                ob.eraise();
            }, true, true);
        },
        
        redraw: function(id, drawFunc, newOptions) {
            this.__processShapes(id, drawFunc, function(ob) {
                ob.redraw(newOptions);
            });
        },
        
        setVisible: function(id, drawFunc, visible) {
            this.__processShapes(id, drawFunc, function(ob) {
                ob.setVisible(visible);
            });
        }
    }
    
    /*
     * Методы для работы с фигурами
     */
    this.shapeGet = function(id, type) {
        return SHAPES.get(id, type);
    }
    
    this.shapeEraise = function(id, type) {
        SHAPES.eraise(id, type);
    }
    
    this.shapeEraiseExcept = function(id, type) {
        SHAPES.eraiseExcept(id, type);
    }
    
    this.shapeRedraw = function(id, type, newOptions) {
        SHAPES.redraw(id, type, newOptions);
    }
    
    this.shapeHas = function(id, type) {
        return !isEmpty(this.shapeGet(id, type));
    }
    
    this.shapeShow = function(id, type) {
        SHAPES.setVisible(id, type, true);
    }
    
    this.shapeHide = function(id, type) {
        SHAPES.setVisible(id, type, false);
    }
    
    /*
     * Методы получения состояния поля
     */
    this.state = {
        update: function(){
            this.minX = minX;
            this.maxX = maxX;
            this.minY = minY;
            this.maxY = maxY;
            this.W = W;
            this.Ol = gr.getOrigin().x;
            this.Ot = gr.getOrigin().y;
        }
    }
    
    //ДОБАВЛЕНИЕ ФИГУР
    
    //@@ ЛИНИЯ @@
    this.putLine = function(options) {
        var sh = SHAPES.startShape(this.putLine, options);
        
        options = $.extend({
            p1: [0, 0],
            p2: [0, 0],
            limited: true,
            pen: ['black', 1]
        }, options);
        
        var bounds = getLineBounds(options.p1, options.p2);
        if(!bounds) {
            SHAPES.endShape();
            return;//---
        } 
        
        var p1, p2;
        if(options.limited) {
            if(bounds.lim) {
                p1 = bounds.lim[0];
                p2 = bounds.lim[1];
            }else{
                SHAPES.endShape();
                return;//---
            }
        } else {
            p1 = bounds.unlim[0];
            p2 = bounds.unlim[1];
        }
        
        var pen = jsH.pen(options.pen);
        p1 = jsH.point(p1);
        p2 = jsH.point(p2);
        
        var $div = $(gr.drawLine(pen, p1, p2));
        sh.addDiv($div);
        
        SHAPES.endShape();
    }
    
    //@@ ДУГА @@
    this.putArc = function(options) {
        var sh = SHAPES.startShape(this.putArc, options);
        
        options = $.extend({
            p: [0, 0],
            pen: ['black', 1],
            ang: [0, 360],
            angSS: null,/*[0, 0] - Start and Swap*/
            angSE: null,/*[0, 0] - Start and End*/
            wh: [1, 1],/*width_height*/
            fill: false
        }, options);
        
        var c = options.p;
        var wh = options.wh;
        
        var cx = c[0];
        var cy = c[1];
        
        var w = wh[0];
        var w2 = w/2;
        
        var h = wh[1];
        var h2 = h/2;
        
        //Проверим, виден ли прямоугольник, в который вписан элипс
        if(!isCanDraw({
            points: [[cx-w2, cy+h2], [cx+w2, cy+h2], [cx+w2, cy-h2], [cx-w2, cy-h2]],
            fill: true
        })) {
            SHAPES.endShape();
            return;//---
        }
        
        var start, end, swap;
        if(options.angSS) {
            start = options.angSS[0];
            swap = options.angSS[1];
            
            start = PsIntervals.angleTo0_360(start + (swap>0 ? 0 : swap));
            swap = Math.abs(swap);
        } else {
            start = options.angSE[0];
            end = options.angSE[1];
            swap = Math.abs(start-end);
            start = PsIntervals.angleTo0_360(Math.min(start, end));
        }
        
        var p = jsH.point(c);
        var pen = jsH.pen(options.pen);
        
        var $div;
        if(options.fill) {
            $div = $(gr.fillArc(pen.color, p, w, h, start, swap));
        }else{
            $div = $(gr.drawArc(pen, p, w, h, start, swap));
        }
        sh.addDiv($div);
        
        SHAPES.endShape();
    }
    
    //@@ СТРЕЛКА (для векторов, угла и т.д.) @@
    this.putArrow = function(options) {
        SHAPES.startShape(this.putArrow, options);
        
        options = $.extend({
            p2: [0, 0],
            p1: [-1, -1],/* - точка, откуда пришли, или...*/
            ang: null, /*Угол - направление*/
            pen: [1, 'black']
        }, options);
        
        var ang = options.ang;
        
        var p1 = options.p1;
        var p2 = options.p2;
        
        var x1, y1;
        var x2 = p2[0];
        var y2 = p2[1];
        
        if(!ang) {
            x1 = p1[0];
            y1 = p1[1];
            
            if(x1==x2) {
                ang = y2>y1 ? 90 : -90;
            } else if(y1==y2) {
                ang = x2>x1 ? 0 : 180;
            } else {
                var tgA = (y2-y1)/(x2-x1);
                ang = PsMath.radToGrad(PsMath.arctg(tgA));
                ang += x1>x2 ? 180 : 0;
            }
        }
        ang-=180;//Развернёмся в сторону рисования
        
        var r = 0.1; //Радиус рисования стрелки (отступ)
        var a = 25; //Раствор стрелки
        var p3 = [x2+r*PsMath.cosGrad(ang+a), y2+r*PsMath.sinGrad(ang+a)];
        var p4 = [x2+r*PsMath.cosGrad(ang-a), y2+r*PsMath.sinGrad(ang-a)];
        
        this.putPolyLine({
            points: [p2, p3, p4],
            pen: options.pen,
            fill: true
        });
        
        SHAPES.endShape();
    }
    
    //@@ ТЕКСТ @@
    this.putText = function(options) {
        var sh = SHAPES.startShape(this.putText, options);
        
        options = $.extend({
            c: null, /*[0, 0] - центр отрезка*/
            p1: [0, 0], /*Отрезки, между которыми зазмещается текст*/
            p2: [0, 0],
            text: null
        }, options);
        
        var c = options.c ? options.c : [(options.p1[0]+options.p2[0])/2, (options.p1[1]+options.p2[1])/2];
        
        var x = c[0];
        var y = c[1];
        if (l2p.isIn(x, y)) {
            var l = l2p.l(x);
            var t = l2p.t(y);
            var $div = $('<div>').addClass('meta').append(options.text).appendTo($field);
            l = l - $div.width()/2;
            t = t - $div.height()/2;
            $div.css('left', l).css('top', t);
            
            sh.addDiv($div);
        }
        
        SHAPES.endShape();
    }
    
    //@@ ВЕКТОР @@
    this.putVector = function(options) {
        options = $.extend({
            p1: [0, 0], //Начало вектора
            p2: null, // [0, 0] - конец вектора
            coords: null, //[x, y] - координаты вектора
            pen: [2, 'black'],
            text: null
        }, options);
        
        if(isString(options.pen)) {
            //По умолчанию рисовать будем двойными пикселями
            options.pen = [2, options.pen];
        }
        
        if (options.p2) {
            options.coords = [options.p2[0]-options.p1[0], options.p2[1]-options.p1[1]];
        }else {
            options.p2 = [options.p1[0]+options.coords[0], options.p1[1]+options.coords[1]];
        }
        
        if(options.coords[0]==0 && options.coords[1]==0) {
            //Вектор имеет нулевые координаты, пропускаем.
            if(options.id) {
                this.shapeEraise(options.id, 'Vector');
            }
            return false;//----
        }
        
        SHAPES.startShape(this.putVector, options);
        
        this.putLine(options);
        this.putArrow(options);
        
        if (options.text) {
            this.putText(options);
        }
        
        SHAPES.endShape();
        
        return true;
    }
    
    this.getVector = function(id) {
        var shape = this.shapeGet(id, 'vector');
        shape = isEmpty(shape) ? null : shape[0].o;
        if (!shape) return null;
        var p1 = shape.p1;
        var p2 = shape.p2;
        var coords = shape.coords;
        
        return {
            x1: p1[0],
            y1: p1[1],
            x2: p2[0],
            y2: p2[1],
            x: coords[0],
            y: coords[1]
        }
    }
    
    this.hasVector = function(id) {
        return !!this.getVector(id);
    }
    
    this.setVectorText = function(id, name) {
        this.shapeRedraw(id, 'Vector', {
            text: name
        });
    }
    
    this.clearVectorsExcept = function(id) {
        this.shapeEraiseExcept(id, 'Vector');
    }
    
    this.clearVector = function(id) {
        this.shapeEraise(id, 'Vector');
    }
    
    this.clearVectors = function() {
        this.clearVector(null);
    }
    
    //@@ ОТМЕЧАЕТ УГОЛ @@
    this.putAngle = function(options) {
        SHAPES.startShape(this.putAngle, options);
        
        options = $.extend({
            p: [0, 0],
            pen: [2, 'black'],
            angSS: null,/*[0, 0] - Start and Swap*/
            angSE: null,/*[0, 0] - Start and End*/
            r: 1
        }, options);
        
        var from = options.angSE ? options.angSE[0] : options.angSS[0];
        var to = options.angSE ? options.angSE[1] : from + options.angSS[1];
        var poCh = to < from;
        from += poCh ? -2 : 2;
        to += poCh ? 2 : -2;
        
        var arcOps = $.extend(options, {
            wh: [2*options.r, 2*options.r],
            angSE: [from, to],
            angSS: null
        });
        this.putArc(arcOps);
        
        /*Стрелка*/
        var x0 = options.p[0];
        var y0 = options.p[1];
        var r = options.r;
        
        var ang = PsMath.circBetha4Alpha(to, poCh);
        
        this.putArrow({
            p2: [x0+r*PsMath.cosGrad(to), y0+r*PsMath.sinGrad(to)],
            ang: ang,
            pen: options.pen
        });
        
        SHAPES.endShape();
    }
    
    //@@ ПРОИЗВОДНАЯ @@
    this.putDeriv = function(options) {
        SHAPES.startShape(this.putDeriv, options);
        options = $.extend({
            f: null,
            x0: null,
            pen: ['black', 1],
            visOnly: false, //Производная будет нарисована, только если точка касания видна
            showAngle: true //Отрисовывание угла
        }, options);
        
        LEGEND_DERIV.clear();
        //this.clearDeriv();
        
        var f = options.f;
        var x0 = options.x0;
        
        var _der = PsMath.derivative(f, x0);
        if(!_der) {
            SHAPES.endShape();
            return null;//---
        }
        
        var der = _der.der;
        var fx0 = _der.fx0;
        
        if (options.visOnly && !l2p.isIn(x0, fx0)) {
            SHAPES.endShape();
            return null;//---
        }
        
        //Расчитаем границы производной
        this.putLine({
            p1: [_der.x0, _der.fx0],
            p2: [_der.x0dx, _der.fx0dx],
            pen: options.pen,
            limited: false
        });
        
        //Угол
        var alpha = PsMath.radToGrad(PsMath.arctg(der));
        
        if (options.showAngle) {
            var angOptions = {
                p: [x0, fx0],
                pen: [1, '#bbb'],
                angSE: [0, alpha],
                r: 1.2
            }
            this.putAngle(angOptions);
            
            this.putLine({
                p1: [x0-0.2, fx0],
                p2: [x0+angOptions.r+0.2, fx0],
                pen: angOptions.pen
            });
        }
        
        //Обновляем легенду
        LEGEND_DERIV.update(_der);
        
        SHAPES.endShape();
        
        return _der;
    }
    
    this.getDeriv = function() {
        var shape = this.shapeGet(null, 'Deriv');
        
        shape = isEmpty(shape) ? null : shape[0];
        if (!shape) return null;
        
        return {
            shape: shape
        }
    }
    
    this.hasDeriv = function() {
        return !!this.getDeriv();
    }
    
    this.clearDeriv = function() {
        var der = this.getDeriv();
        if(!der) return;
        LEGEND_DERIV.clear();
        this.shapeEraise(der.shape, 'Deriv');
    }
    
    this.setDerivPen = function(pen) {
        var der = this.getDeriv();
        if(!der) return;
        this.shapeRedraw(der.shape, 'Deriv', {
            pen: pen
        });       
    }
    
    this.setDerivShowAngle = function(show) {
        var der = this.getDeriv();
        if(!der) return;
        this.shapeRedraw(der.shape, 'Deriv', {
            showAngle: show
        });       
    }
    
    //"Проигрывание" производной
    var derivInterval = null;
    this.playDeriv = function(options) {
        options = $.extend({
            f: null,
            x1: null,
            x2: null,
            dx: 0.1,
            delay: 100,
            color: function() {
                return '#000000';
            },
            onPut: function(der) {
            //Функция вызывается при построении производной
            },
            onEnd: function() {
            //Проигрывание производной завершено
            },
            showAngle: function() {
                //Показывать или нет угол при проигрывании
                return true;
            }
        }, options);
        
        var DerivBoundsX = function() {
            return l2p.boundsX([options.x1 ? options.x1 : minX, options.x2 ? options.x2 : maxX]);
        }
        
        var x0 = DerivBoundsX()[0];
        
        this.stopDerivPlay();
        derivInterval = new PsIntervalAdapter(function() {
            _this.clearDeriv();
            
            if (x0>DerivBoundsX()[1]) {
                _this.stopDerivPlay();
                options.onEnd();
                return;//---
            }
            
            do {
                _this.clearDeriv();
                var der = _this.putDeriv({
                    f: options.f,
                    x0: x0,
                    pen: [options.color(), 2],
                    visOnly: true,
                    showAngle: options.showAngle()
                });
                x0+=options.dx;
            } while (!der && x0<=DerivBoundsX()[1]);
            
            if(der) {
                options.onPut(der);
            }
        
        }, options.delay, true);
        
        derivInterval.start();
    }
    
    this.stopDerivPlay = function(){
        if (derivInterval) {
            derivInterval.stop();
            derivInterval = null;
        }
    }
    
    
    //@@ ПОЛИГОН @@
    this.putPolyLine = function(options) {
        var sh = SHAPES.startShape(this.putPolyLine, options);
        
        options = $.extend({
            points: [],
            pen: ['black', 1],
            fill: false,
            close: false
        }, options);
        
        if (!isCanDraw(options)){
            SHAPES.endShape();
            return;//---
        } 
        
        var pen = jsH.pen(options.pen);
        var points = jsH.points(options.points);
        
        var $div;
        if(options.fill) {
            $div=$(gr.fillPolygon(pen.color, points));
        } else if(options.close) {
            $div=$(gr.drawPolygon(pen, points));
        } else {
            $div=$(gr.drawPolyline(pen, points));
        }
        sh.addDiv($div);
        
        SHAPES.endShape();
    }
    
    //@@ КРИВАЯ @@
    this.putCurve = function(options) {
        var sh = SHAPES.startShape(this.putCurve, options);
        
        options = $.extend({
            points: [],
            pen: ['black', 1],
            fill: false,
            close: false,
            tension: 0
        }, options);
        
        var pen = jsH.pen(options.pen);
        var points = jsH.points(options.points);
        
        var $div;
        if(options.fill) {
            $div = $(gr.fillClosedCurve(pen.color, points, options.tension));
        } else {
            $div = $(gr.drawCurve(pen, points, options.tension, options.close));
        }
        sh.addDiv($div);
        
        SHAPES.endShape();
    }
    
    //@@ КРУГ @@
    this.putCircle = function(options) {
        var sh = SHAPES.startShape(this.putCircle, options);
        
        options = $.extend({
            c: [0, 0],
            r: 1, /*radius*/
            pen: ['black', 1],
            fill: false
        }, options);
        
        var c = options.c;
        var r = options.r;
        
        var cx = c[0];
        var cy = c[1];
        
        //Проверим, виден ли прямоугольник, в который вписан круг
        if(!isCanDraw({
            points: [[cx-r, cy+r], [cx+r, cy+r], [cx+r, cy-r], [cx-r, cy-r]],
            fill: true
        })) {
            SHAPES.endShape();
            return;//---
        } 
        
        
        var pen = jsH.pen(options.pen);
        var center = jsH.point(c);
        
        var $div;
        if(options.fill) {
            $div = $(gr.fillCircle(pen.color, center, r));
        } else {
            $div = $(gr.drawCircle(pen, center, r));
        }
        sh.addDiv($div);
        
        SHAPES.endShape();
    }
    
    //@@ ЭЛЛИПС @@
    this.putEllipse = function(options) {
        var sh = SHAPES.startShape(this.putEllipse, options);
        
        options = $.extend({
            c: [0, 0],
            wh: null, /*[1, 1] - width and height*/
            ab: null, /*[1, 1] - a=width/2 b=height/2*/
            pen: ['black', 1],
            fill: false
        }, options);
        
        var c = options.c;
        var wh = options.wh;
        var ab = options.ab;
        
        var w = wh ? wh[0] : 2*ab[0];
        var w2 = w/2;
        
        var h = wh ? wh[1] : 2*ab[1];
        var h2 = h/2;
        
        var cx = c[0];
        var cy = c[1];
        
        //Проверим, виден ли прямоугольник, в который вписан круг
        if(!isCanDraw({
            points: [[cx-w2, cy+h2], [cx+w2, cy+h2], [cx+w2, cy-h2], [cx-w2, cy-h2]],
            fill: true
        })) {
            SHAPES.endShape();
            return;//---
        }
        
        var pen = jsH.pen(options.pen);
        var center = jsH.point(c);
        
        var $div;
        if(options.fill) {
            $div = $(gr.fillEllipse(pen.color, center, w, h));
        } else {
            $div = $(gr.drawEllipse(pen, center, w, h));
        }
        sh.addDiv($div);
        
        SHAPES.endShape();
    }
    
    //@@ ПРЯМОУГОЛЬНИК @@
    this.putRectangle = function(options) {
        var sh = SHAPES.startShape(this.putRectangle, options);
        
        options = $.extend({
            p: null, /*[0, 0] - Верхний левый угол*/
            c: null, /*[0, 0] - Центр*/
            whpx: null, /*[5, 5] - width and height in pixels*/
            wh: null, /*[1, 1] - width and height*/
            pen: ['black', 1],
            fill: false
        }, options);
        
        var p = options.p;
        var c = options.c;
        
        var whpx = options.whpx;
        var wh = options.wh;
        
        var w = wh ? wh[0] : whpx[0]/SCALE;
        var h = wh ? wh[1] : whpx[1]/SCALE;
        
        p = p ? p : [c[0]-w/2, c[1]+h/2];
        
        var px = p[0];
        var py = p[1];
        
        //Проверим, виден ли прямоугольник, в который вписан круг
        if(!isCanDraw({
            points: [[px, py], [px+w, py], [px+w, py-h], [px, py-h]],
            fill: options.fill
        })) {
            SHAPES.endShape();
            return;//---
        } 
        
        var pen = jsH.pen(options.pen);
        var leftTop = jsH.point(p);
        
        var $div;
        if(options.fill) {
            $div = $(gr.fillRectangle(pen.color, leftTop, w, h));
        } else {
            $div = $(gr.drawRectangle(pen, leftTop, w, h));
        }
        sh.addDiv($div);
        
        SHAPES.endShape();
    }
    
    //@@ МАРКЕР ТОЧКИ @@
    this.putMarker = function(options) {
        options = $.extend({
            p: null, /*[0, 0] - Верхний левый угол*/
            c: null, /*[0, 0] - Центр*/
            whpx: [5, 5], /*[1, 1] - width and height*/
            pen: ['black', 1],
            fill: true,
            classes: 'dotMarker'
        }, options);
        
        if(!options.c) {
            options.c = [options.p[0]+options.whpx[0]/2, options.p[1]-options.whpx[1]/2];
        }
        
        SHAPES.startShape(this.putMarker, options);
        this.putRectangle(options);
        SHAPES.endShape();
    }
    
    this.putDotMarker = function(x, y, color, id) {
        this.clearMarker(null, [x, y]);
        
        id = id ? id : color;
        this.putMarker({
            c: [x, y],
            pen: color,
            id: id
        });
    }
    
    this.getMarker = function(id, c) {
        var shape = this.shapeGet(function(shape) {
            var o = shape.o;
            if(id && o.id!=id) return false;
            if(c && (o.c[0]!=c[0] || o.c[1]!=c[1])) return false;
            return true;
        }, 'Marker');
        
        shape = isEmpty(shape) ? null : shape[0];
        if (!shape) return null;
        
        return {
            id: shape.o.id,
            shape: shape,
            x: shape.o.c[0],
            y: shape.o.c[1]
        }
    }
    
    this.hasMarker = function(id, c) {
        return !!this.getMarker(id, c);
    }
    
    this.clearMarker = function(id, c) {
        var m = this.getMarker(id, c);
        if(!m) return;
        this.shapeEraise(m.shape, 'Marker');
    }
    
    this.clearMarkers = function() {
        this.shapeEraise(null, 'Marker');
    }
    
    //@@ КАРТИНКА @@
    var getImgDim = function(url, callback) {
        PsResources.getImgSize(url, function(dim) {
            callback.call(_this, dim ? {
                w: dim.w/SCALE,
                h: dim.h/SCALE
            } : null);
        });
    }
    
    this.putImg = function(options) {
        var sh = SHAPES.startShape(this.putImg, options);
        
        options = $.extend({
            url: null, /*url of the img*/
            p: null, /*[0, 0] - Верхний левый угол*/
            c: null, /*[0, 0] - Центр*/
            wh: null /*[1, 1] - width and height*/
        }, options);
        
        var url = options.url;
        
        getImgDim(url, function(dim) {
            if(!dim) return;
            
            var wh = options.wh;
            var w = wh ? wh[0] : dim.w;
            var h = wh ? wh[1] : dim.h;
            
            var p = options.p;
            var c = options.c;
            
            if (c) {
                p = [c[0]-w/2, c[1]+h/2];
            }
            
            var px = p[0];
            var py = p[1];
            
            if(!isCanDraw({
                points: [[px, py], [px+w, py], [px+w, py-h], [px, py-h]],
                fill: true
            })) return;
            
            var leftTop = jsH.point(p);
            var $div = $(gr.drawImage(url, leftTop, w, h));
            
            sh.addDiv($div);
        });
        
        SHAPES.endShape();
    }
    
    //@@ ГРАФИК @@
    this.putGr = function(options) {
        options = $.extend({
            x: null,
            y: null,
            pen: ['green', 2],
            bounds: null /*[-1, 1] - границы*/
        }, options);
        
        var f, poX;
        if(options.y) {
            f = options.y;
            poX = true;
        } else if(options.x) {
            f = options.x;
            poX = false;
        } else {
            return;
        }
        
        SHAPES.startShape(this.putGr, options);
        
        var boundsX = l2p.boundsX(poX ? options.bounds : null);
        var boundsY = l2p.boundsY(poX ? null : options.bounds);
        var boundsC = poX ? boundsX : boundsY;
        var minC = boundsC[0];
        var maxC = boundsC[1];
        
        var dx = 0.1;//ШАГ ПРОРИСОВКИ
        
        logger.logInfo('DRAWING graphic {}, boundsX: ({}), boundsY: ({}), step: {}', poX ? 'y=f(x)' : 'x=f(y)', boundsX, boundsY, dx);
        
        if(Math.abs(minC-maxC)<dx) {
            logger.logInfo('Scipping draw, bounds are too close!');
            SHAPES.endShape();
            return; //---
        }
        
        minC = minC - dx;
        maxC = maxC + dx;
        
        logger.logDebug('Fetched interval: [{}, {}]', minC, maxC);
        
        var points = [];
        var addGrPart = function() {
            if(points.length>1) {
                logger.logTrace('Draw {} points', points.length);
                
                _this.putCurve({
                    pen: options.pen,
                    points: points,
                    close: false
                });
                if(MarkGrPoints){
                    //Трассировка добавляемых точек
                    $.each(points, function(i, point){
                        _this.putMarker({
                            c: point
                        });
                    });
                }
            }
            points = [];
        }
        
        
        var pointC1 = function(point) {
            return point[poX ? 0 : 1];
        }
        var pointC2 = function(point) {
            return point[poX ? 1 : 0];
        }
        var pointFromC1C2 = function(c1, c2) {
            return [poX ? c1 : c2, poX ? c2 : c1];
        }
        
        var getFuncBreakBounds = function(point1, point2) {
            var bounds = getLineBounds(point1, point2, !poX, boundsX, boundsY);
            return bounds && bounds.lim ? bounds.lim : null;
        }
        
        var checkCrossed = function(prevPoint, point) {
            logger.logTrace('Check crossed: ({}), ({})', prevPoint, point);
            
            var crosses = getFuncBreakBounds(prevPoint, point);
            if (crosses) {
                points.push(crosses[0]);
                points.push(crosses[1]);
            }
            addGrPart();
        }
        
        var stepIn = function(prevPoint, point) {
            logger.logTrace('Step in: ({}), ({})', prevPoint, point);
            
            var crosses = getFuncBreakBounds(prevPoint, point);
            if (crosses) {
                points.push(crosses[0]);
                points.push(crosses[1]);
            }
        }
        
        var stepOut = function(prevPoint, point) {
            logger.logTrace('Step out: ({}), ({})', prevPoint, point);
            
            var crosses = getFuncBreakBounds(prevPoint, point);
            if (crosses) {
                points.push(crosses[1]);
            }
            addGrPart();
        }
        
        /*
         * state
         * 
         * 0 - вне поля
         * 1 - в декартовом, но не в видимом
         * 2 - в видимом
         */
        
        var getPointState = function(point) {
            var inField = l2p.isIn(point);
            var inFetchedField = inField && l2p.isIn(point, null, boundsX, boundsY);
            var state = inField ? (inFetchedField ? 2 : 1) : 0;
            
            return {
                s: state,
                p: point
            }
        }
        
        var intervals = PsMathEval.calcFuncDefInterval(f, [minC, maxC], 0.0025);
        $.each(intervals, function(i, interval) {
            var prevSTATE;
            var doProcessPoint = function(c1, c2) {
                var point = pointFromC1C2(c1, c2);
                var prevPoint = prevSTATE ? prevSTATE.p : null;
                var curSTATE = getPointState(point);
                
                if(!prevSTATE) {
                    prevSTATE = curSTATE;
                    if(curSTATE.s==2) points.push(point);
                    return;//---
                }
                
                switch (prevSTATE.s) {
                    case 0:
                        switch (curSTATE.s) {
                            case 0:
                                //+ Остаёмся в невидимой области
                                checkCrossed(prevPoint, point);
                                break;
                            case 1:
                                //+ Перешли из невидимой области в Декартово поле
                                checkCrossed(prevPoint, point);
                                break;
                            case 2:
                                //Перешли из невидимой области в видимую
                                stepIn(prevPoint, point);
                                break;
                        }
                        break;
                    
                    case 1:
                        switch (curSTATE.s) {
                            case 0:
                                //Перешли из Декартова поля в невидимую область
                                checkCrossed(prevPoint, point);
                                break;
                            case 1:
                                //+ Остаёмся в Декартовом поле
                                checkCrossed(prevPoint, point);
                                break;
                            case 2:
                                //Перешли из Декартова поля в видимую область
                                stepIn(prevPoint, point);
                                break;
                        }
                        break;
                    
                    case 2:
                        switch (curSTATE.s) {
                            case 0:
                                //Перешли из видимой области в невидимую
                                stepOut(prevPoint, point);
                                break;
                            case 1:
                                //Перешли из видимой области в Декартово поле
                                stepOut(prevPoint, point);
                                break;
                            case 2:
                                //Остаёмся в видимой области
                                points.push(point);
                                break;
                        }
                        break;
                }
                prevSTATE = curSTATE;
            }
            
            
            //Обработка
            var c1 = interval[0], c2;
            while(c1<interval[1]){
                c2 = PsMathEval.evalSave(f, c1);
                if(c2===null) {
                    c1+=0.0025;
                    continue;
                }
                
                doProcessPoint(c1, c2);
                c1+=dx;
            }
            c1 = interval[1]-dx;
            c2 = PsMathEval.evalSave(f, c1);
            doProcessPoint(c1, c2);
            
            addGrPart();
        });
        
        SHAPES.endShape();
    },
    
    this.getGr = function(fOrId) {
        var shape = this.shapeGet(function(shape) {
            var o = shape.o;
            return !fOrId || o.id==fOrId || o.x==fOrId || o.y==fOrId;
        }, 'Gr');
        
        shape = isEmpty(shape) ? null : shape[0];
        if (!shape) return null;
        
        return {
            shape: shape
        }
    }
    
    this.hasGr = function(fOrId) {
        return !!this.getGr(fOrId);
    }
    
    this.clearGr = function(fOrId) {
        var gr = this.getGr(fOrId);
        if(!gr) return;
        this.shapeEraise(gr.shape, 'Gr');
    }
    
    this.hideGr = function(fOrId) {
        var gr = this.getGr(fOrId);
        if(!gr) return;
        this.shapeHide(gr.shape, 'Gr');
    }
    
    this.showGr = function(fOrId) {
        var gr = this.getGr(fOrId);
        if(!gr) return;
        this.shapeShow(gr.shape, 'Gr');
    }
    
    this.setGrPen = function(fOrId, pen) {
        var gr = this.getGr(fOrId);
        if(!gr) return;
        this.shapeRedraw(gr.shape, 'Gr', {
            pen: pen
        });
    }
    
    //@@ ИНТЕГРАЛ (штрихованная область) @@
    this.putIntegral = function(options) {
        options = $.extend({
            x: null,
            y: null,
            pen: ['green', 2],
            bounds: null, /*[-1, 1] - границы*/
            classes: 'back'
        }, options);
        
        var f, poX;
        if(options.y) {
            f = options.y;
            poX = true;
        } else if(options.x) {
            f = options.x;
            poX = false;
        } else {
            return;
        }
        
        SHAPES.startShape(this.putIntegral, options);
        
        LEGEND_INTEG.clear();
        
        var bounds = options.bounds;
        
        var boundsX = l2p.boundsX(poX ? bounds : null);
        var boundsY = l2p.boundsY(poX ? null : bounds);
        var boundsC = poX ? boundsX : boundsY;
        var minC = boundsC[0];
        var maxC = boundsC[1];
        
        var l2r = !bounds || bounds[0]<bounds[1];
        var pen = jsH.parsePen(options.pen);
        
        var dl = pen.w+2;//Растояние в пикселях между линиями
        var dx = p2l.dl2dx(dl);
        
        logger.logInfo('DRAWING integral {}, boundsX: ({}), boundsY: ({}), step: {}', poX ? 'y=f(x)' : 'x=f(y)', boundsX, boundsY, dx);
        
        if(Math.abs(minC-maxC)<dx) {
            logger.logInfo('Scipping draw, bounds are too close!');
            SHAPES.endShape();
            return; //---
        }
        
        var putLine = function(c1) {
            var c2 = PsMathEval.evalSave(f, c1);
            if(!PsIs.number(c2)) return;
            
            var p1, p2;
            if (poX){
                p1 = [c1, 0];
                p2 = [c1, c2];
            }else{
                p1 = [0 , c1];
                p2 = [c2, c1];
            }
            _this.putLine({
                p1: p1,
                p2: p2,
                pen: options.pen
            });
        }
        
        var cur;
        if(l2r) {
            //→
            for (cur = minC; cur <= maxC; cur+=dx) {
                putLine(cur);
            }
        }else{
            //←
            for (cur = maxC; cur >= minC; cur-=dx) {
                putLine(cur);
            }
        }
        
        //Информация
        LEGEND_INTEG.update({
            f: f,
            bounds: bounds ? bounds : boundsC,
            poX: poX
        });
        
        SHAPES.endShape();
    }
    
    this.getIntegral = function() {
        var shape = this.shapeGet(null, 'Integral');
        
        shape = isEmpty(shape) ? null : shape[0];
        if (!shape) return null;
        
        return {
            shape: shape
        }
    }
    
    this.hasIntegral = function() {
        return !!this.getIntegral();
    }
    
    this.clearIntegral = function() {
        var integral = this.getIntegral();
        if(!integral) return;
        LEGEND_INTEG.clear();
        this.shapeEraise(integral.shape, 'Integral');
    }
    
    this.setIntegralColor = function(color) {
        var integral = this.getIntegral();
        if(!integral) return;
        this.shapeRedraw(integral.shape, 'Integral', {
            pen: color
        });       
    }
    
    /*
     * РАБОТА С ЛЕГЕНДАМИ
     * 
     * При включении работы с легендами будет показана кнопка открытия легенды.
     * Можно открыть как одну из базовых легенд (производная, интеграл), 
     * так и включить поддержку собственной легенды. В таком случае легенда заполняется руками.
     */
    var LEGEND = null;
    var LEGENDS = new ObjectsStore();
    
    var LEGEND_DERIV = new PsLegend({
        title: 'Производная',
        updater: function(derivative) {
            if(!derivative) return;//---
            
            var x0 = derivative.x0;
            var fx0 = derivative.fx0;
            var der = derivative.der;
            var alpha = PsMath.radToGrad(PsMath.arctg(der));
            
            //Информация
            x0 = PsMath.round(x0, 2);
            fx0 = PsMath.round(fx0, 2);
            alpha = PsMath.round(alpha, 2);
            der = PsMath.round(der, 2);
            var derGr = PsMath.round(-der*x0+fx0, 2);
            
            var $infoBox = $('<div>');
            $infoBox.append('<div>x<sub>0</sub> = '+PsHtml.num2str(x0)+', y<sub>0</sub> = '+PsHtml.num2str(fx0)+'</div>');
            $infoBox.append('<div>y\'('+PsHtml.num2str(x0)+') = '+PsHtml.num2str(der)+', &alpha; = ' + PsHtml.num2str(alpha) + '&deg;</div>');
            $infoBox.append('<div>y<sub>кас</sub>('+PsHtml.num2str(x0)+') = '+PsHtml.num2str(der)+'&sdot;(x &minus; '+(x0<0?'('+PsHtml.num2str(x0)+')' : x0)+') ' + PsHtml.sum2str('', fx0) + '</div>');
            $infoBox.append('<div class="green"><b>y<sub>кас</sub>('+PsHtml.num2str(x0)+') = '+(der==0 ? '' : PsHtml.num2str(der)+'&sdot;x ')+PsHtml.sum2str('', derGr) + (der==0 && derGr==0 ? '0' : '') + '</b></div>');
            
            this.fill($infoBox);
        }
    });
    
    var LEGEND_INTEG = new PsLegend({
        title: 'Интергал',
        updater: function(options) {
            //При развёрнутой легенде пересчитываем интеграл
            
            var integral = PsMath.integral(options.f, options.bounds[0], options.bounds[1]);
            var coord = options.poX ? 'x' : 'y';
            
            var Sr = PsHtml.num2str(PsMath.round(integral.S, 2));
            var Sabsr = PsHtml.num2str(PsMath.round(integral.Sabs, 2));
            var minCr = PsHtml.num2str(PsMath.round(integral.from, 2));
            var maxCr = PsHtml.num2str(PsMath.round(integral.to, 2));
            
            var $infoBox = $('<div>');
            $infoBox.append($('<div>').html(PsStrings.replaceOneByOne('{}<sub>1</sub>={}, {}<sub>2</sub>={}', '{}', [coord, minCr, coord, maxCr])));
            $infoBox.append($('<div>').html(PsStrings.replaceOneByOne('&int;f({})={}, &int;|f({})|={}', '{}', [coord, Sr, coord, Sabsr])));
            
            this.fill($infoBox);
        }
    });
    
    
    LEGENDS.put('der', LEGEND_DERIV);
    LEGENDS.put('int', LEGEND_INTEG);
    LEGENDS.put('custom', new PsLegend());
    
    this.legendEnable = function(type) {
        this.legendDisable();
        type = type ? type : 'custom';
        if (!LEGENDS.has(type)) return null;
        LEGEND = LEGENDS.get(type);
        CtrlButtons.recalcState({
            legend: {
                visible: true
            }
        });
        return LEGEND;
    }
    
    this.legendDisable = function() {
        if (LEGEND) {
            LEGEND.hide();
            LEGEND = null;
        }
        CtrlButtons.recalcState({
            legend: {
                visible: false
            }
        });
    }
    
    CtrlButtons.recalcState({
        legend: {
            visible: false
        }
    }).setCallbacks({
        on_legend: function($btn) {
            LEGEND.showAtRbOf($btn);
        }
    });
    
    
    // --== СЛУШАТЕЛИ ==--
    var LISTENERS = new ObjectsStore();
    
    //Если включён слушатель перетаскивания начала координат, то оставляем только его и слушатель
    //изменения масштаба прокручиванием колёсика мыши
    var isCanProcessEvent = function(lId) {
        return !LISTENERS.has(ORIGIN_DRAG_LISTENER) || PsArrays.inArray(lId, [ORIGIN_DRAG_LISTENER, WHEEL_SCALE_LISTENER]);
    }
    
    this.unbind = function(lId) {
        var listener = LISTENERS.get(lId);
        if (listener) {
            for (var ev in listener) {
                $field.unbind(ev, listener[ev]);
            }
            LISTENERS.remove(lId);
        }
    }
    
    this.bind = function(options, lId) {
        //Если передана функция, то считаем, что подписались на onClick
        options = $.isFunction(options) ? {
            click: options
        } : options;
        
        options = $.extend({
            discr: false, //Дискретное оповещение
            move: null, //Слушатель движения
            click: null, //Слушатель клика
            rebuild: null, //Слушатель перестроения поля
            out: null, //Слушатель покидания мышки Декартова поля
            enter: null,//Слушатель наведения мышки на Декартово поле
            up: null,//Слушатель отпускания мыши
            down: null//Слушатель нажатия кнопки мыши
        }, options);
        
        this.unbind(lId);
        
        var newListener = {
        };
        
        //Сначала вешаем jQuery слушатели
        var event2ob = function(event) {
            var xy = p2l.e2xy(event, options.discr);
            var lt = l2p.e2lt(event);
            return {
                xy: xy,
                x: xy.x,
                y: xy.y,
                lt: lt,
                l: lt.l,
                t: lt.t
            }
        }
        
        if (options.click) {
            newListener.click = function(e) {
                if(!isCanProcessEvent(lId)) return;//---
                options.click.call(_this, event2ob(e));
            }
        }
        
        var curXY = null;
        if (options.move) {
            newListener.mousemove = function(e) {
                if(!isCanProcessEvent(lId)) return;//---
                var ob = event2ob(e);
                if (ob.xy.eq(curXY)) return;
                curXY = ob.xy;
                options.move.call(_this, ob);
            }
        }
        
        if (options.out || options.move) {
            newListener.mouseleave = function(e) {
                curXY = null;
                if (options.out) {
                    if(!isCanProcessEvent(lId)) return;//---
                    options.out.call(_this, event2ob(e));
                }
            }
        }
        
        if (options.enter) {
            newListener.mouseenter = function(e) {
                if(!isCanProcessEvent(lId)) return;//---
                options.enter.call(_this, event2ob(e));
            }
        }
        
        if (options.down) {
            newListener.mousedown = function(e) {
                if(!isCanProcessEvent(lId)) return;//---
                options.down.call(_this, event2ob(e));
            }
        }
        
        if (options.up) {
            newListener.mouseup = function(e) {
                if(!isCanProcessEvent(lId)) return;//---
                options.up.call(_this, event2ob(e));
            }
        }
        
        //Непосредственно добавляем к слушателям поля
        for (var ev in newListener) {
            $field.bind(ev, newListener[ev]);
        }
        
        //Остальные слушатели
        newListener.rebuild = options.rebuild;
        
        LISTENERS.set(lId, newListener);
    }
    
    /* 
     * Очистка поля
     */
    this.clear = function() {
        this.shapeEraise();
    }
    
    this.buildField();
}
