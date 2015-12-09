$(function() {
    
    var DekarVectPlugin = {
        mode: null,
        init: function(){
            var _this = this;
        
            this.BODY = $('.dekartvect_plugin');
            
            var MODUL_ID = 'DekarVectPlugin';
        
            //Контроллер Декартова поля
            var  df = new DekartFieldController(this.BODY, {
                axes: {
                    en:0,
                    vis: 0
                },
                marks: {
                    en: 0,
                    vis: 0
                },
                marksText: {
                    en: 0,
                    vis: 0
                },
                grid: {
                    en: 1,
                    vis: 0
                }
            });
            this.df = df;
            
            var $HGSELF = this.BODY.children('.hg-self');
            var $HGNEXT = this.BODY.children('.hg-next');
            this.$INFO = this.BODY.children('.info');
            
            var HG = new HrefsGroup($HGSELF, 'self', {
                id: MODUL_ID,
                callback: function(state) {
                    _this.doSetMode(state);
                }
            });
            //Переносим ссылки из информационного блока в триггер
            HG.cloneAndPlaceTo($HGNEXT, 'next');
            
            //Очистка состояния
            new ButtonsController($HGNEXT, {
                on_clear: function() {
                    _this.resetMode();
                }
            });
            
            //Установка состояния
            HG.setFirst();
        },
        
        doSetMode: function(mode) {
            if (!mode) return;
            this.mode = mode;
            this.df.unbind();
            this.df.clear();
            this['do_' + mode].call(this, this.df);
        },
        
        resetMode: function() {
            this.doSetMode(this.mode);
        },
        
        doInfo: function(info, wrap) {
            this.$INFO.html(wrap ? PsHtml.span(info, 'gray') : info);
        },
        
        
        //a=λi + μj
        do_ij: function(df) {
            /*
             * 0 - ничего
             * 1 - стоит серая точка
             * 2 - стоит красная точка (может быть вектор)
             * 3 - вектор зафиксирован
             */
            var state = 0;
            var _this = this;

            var A_COLOR = 'red';
            var E1_COLOR = '#6F6';
            var E2_COLOR = '#69F';
            
            var dravIJ = function(state) {
                var p1 = [state.minX, state.minY];
                df.putVector({
                    p1: p1,
                    coords: [1, 0],
                    pen: E1_COLOR,
                    text: PsHtmlCnst.VECT_I,
                    id: 'i'
                });
                
                df.putVector({
                    p1: p1,
                    coords: [0, 1],
                    pen: E2_COLOR,
                    text: PsHtmlCnst.VECT_J,
                    id: 'j'
                });
            }
            
            df.bind({
                discr: true,
                move: function(e) {
                    switch (state) {
                        case 0:
                            ++state;
                        case 1:
                        case 3:
                            this.clearMarker('gray');
                            this.putDotMarker(e.x, e.y, 'gray');
                            break;
                        case 2:
                            var x0 = this.getMarker('red').x;
                            var y0 = this.getMarker('red').y;
                            var coords = [e.x-x0, e.y-y0];
                            
                            this.clearVectorsExcept(['i', 'j']);

                            var putted = this.putVector({
                                p1: [x0, y0],
                                coords: coords,
                                pen: A_COLOR,
                                text: PsHtml.vector('a', coords[0], coords[1]),
                                id: 'a'
                            });

                            if (putted) {
                                this.putVector({
                                    p1: [x0, y0],
                                    coords: [coords[0], 0],
                                    pen: E1_COLOR,
                                    text: PsHtml.num2strTrim(coords[0]) + PsHtmlCnst.VECT_I,
                                    id: 'i2'
                                });                                

                                this.putVector({
                                    p1: [x0+coords[0], y0],
                                    coords: [0, coords[1]],
                                    pen: E2_COLOR,
                                    text: PsHtml.num2strTrim(coords[1]) + PsHtmlCnst.VECT_J,
                                    id: 'j2'
                                });                                
                            }
                            break;
                    }
                    _this.do_ij_info(state, this.getVector('a'));
                },
                click: function(e) {
                    switch (state) {
                        case 0:
                            break;
                        case 1:
                            this.clearMarker('gray');
                            this.putDotMarker(e.x, e.y, 'red');
                            ++state;
                            break;
                        case 2:
                            if (this.hasMarker('red', [e.x, e.y])) {
                                this.clearMarker('red');
                                this.putDotMarker(e.x, e.y, 'gray');
                                state = 1;
                                break;
                            }
                            if (this.hasVector('a')){
                                this.clearMarker('red');
                                //this.putDotMarker(x, y, 'gray');
                                ++state;
                            };
                            break;
                        case 3:
                            this.clearVectorsExcept(['i', 'j']);
                            this.clearMarker('gray');
                            this.putDotMarker(e.x, e.y, 'red');
                            state = 2;
                            break;
                    }

                    _this.do_ij_info(state, this.getVector('a'));
                },
                out: function() {
                    this.clearMarker('gray');
                },
                rebuild: function(state) {
                    dravIJ(state);
                }
            });
            dravIJ(df.state);
            
            _this.do_ij_info(state, null);
        },
        
        do_ij_info: function(state, vec) {
            if(state==3) return;
            /*
             * 0 - ничего
             * 1 - стоит серая точка
             * 2 - стоит красная точка (может быть вектор)
             * 3 - вектор зафиксирован
             */
            
            var x, y, info;
            var gray = true;
            switch (state) {
                case 0:
                case 1:
                    info = 'Укажите начало вектора';
                    break;
                case 2:
                    if (vec) {
                        gray = false;
                        x = vec.x;
                        y = vec.y;
                        info = 'В базисе {' + PsHtmlCnst.VECT_I +', ' + PsHtmlCnst.VECT_I + '}:&nbsp; ' + 
                        PsHtmlCnst.VECT_A + ' = ' + PsHtml.vecSum(x, 'i', y, 'j') + ',&nbsp; ' + PsHtml.vector('a', x, y);
                    } else {
                        info = 'Укажите конец вектора';
                    }
                    break;
            }
            
            this.doInfo(info, gray);
        },
        
        
        //a(λa, μa) + b(λb, μb)=c(λa+λb, μa+μb)
        do_ab: function(df) {
            /*
             * 0 - ничего
             * 1 - стоит серая точка
             * 2 - стоит красная точка (может быть первый вектор)
             * 3 - стоит первый вектор (может быть второй вектор)
             * 4 - векторы зафиксированы
             */
            var state = 0;
            var _this = this;
 
            var A_COLOR = 'red';
            var B_COLOR = 'blue';
            var C_COLOR = '#ccc';
            var E1_COLOR = '#6F6';
            var E2_COLOR = '#69F';

            var dravIJ = function(state) {
                var p1 = [state.minX, state.minY];
                df.putVector({
                    p1: p1,
                    coords: [1, 0],
                    pen: E1_COLOR,
                    text: PsHtmlCnst.VECT_I,
                    id: 'i'
                });
                
                df.putVector({
                    p1: p1,
                    coords: [0, 1],
                    pen: E2_COLOR,
                    text: PsHtmlCnst.VECT_J,
                    id: 'j'
                });
            }
            
            var x, y, x0, y0, coords;
            df.bind({
                discr: true,
                move: function(e) {
                    x = e.x;
                    y = e.y;
                    switch (state) {
                        case 0:
                            ++state;
                        case 1:
                        case 4:
                            this.clearMarker('gray');
                            this.putDotMarker(x, y, 'gray');
                            break;
                        case 2:
                            x0 = this.getMarker('red').x;
                            y0 = this.getMarker('red').y;
                            coords = [x-x0, y-y0];
                            
                            this.clearVectorsExcept(['i', 'j']);

                            this.putVector({
                                p1: [x0, y0],
                                coords: coords,
                                pen: A_COLOR,
                                text: PsHtml.vector('a', coords[0], coords[1]),
                                id: 'a'
                            });

                            break;
                        case 3:
                            var vec = this.getVector('a');
                            x0 = vec.x2;
                            y0 = vec.y2;
                            
                            this.clearVectorsExcept(['i', 'j', 'a']);
                            
                            if (x!=x0 || y!=y0) {
                                coords = [x-x0, y-y0];
                                this.putVector({
                                    p1: [x0, y0],
                                    coords: coords,
                                    pen: B_COLOR,
                                    text: PsHtml.vector('b', coords[0], coords[1]),
                                    id: 'b'
                                });

                                coords = [x-vec.x1, y-vec.y1];
                                this.putVector({
                                    p1: [vec.x1, vec.y1],
                                    coords: coords,
                                    pen: C_COLOR,
                                    text: PsHtml.vector('c', coords[0], coords[1]),
                                    id: 'c'
                                });
                            }
                            break;
                    }
                    _this.do_ab_info(state, this.getVector('a'), this.getVector('b'), this.getVector('c'));
                },
                click: function(e) {
                    x = e.x;
                    y = e.y;
                    switch (state) {
                        case 0:
                            break;
                        case 1:
                            this.clearMarker('gray');
                            this.putDotMarker(x, y, 'red');
                            ++state;
                            break;
                        case 2:
                            if (this.hasMarker('red', [x, y])) {
                                this.clearMarker('red', [x, y]);
                                this.putDotMarker(x, y, 'gray');
                                state = 1;
                                break;
                            }
                            if (this.hasVector('a')){
                                this.clearMarker('red');
                                //this.putDotMarker(x, y, 'gray');
                                ++state;
                            };
                            break;
                        case 3:
                            if (this.hasVector('b')){
                                ++state;
                            };
                            break;
                        case 4:
                            this.clearVectorsExcept(['i', 'j']);
                            this.clearMarker('gray');
                            this.putDotMarker(x, y, 'red');
                            state = 2;
                            break;
                    }
                    _this.do_ab_info(state, this.getVector('a'), this.getVector('b'), this.getVector('c'));
                },
                out: function() {
                    this.clearMarker('gray');
                },
                rebuild: function(state) {
                    dravIJ(state);
                }
            });
            dravIJ(df.state);

            _this.do_ab_info(state);
        },
        
        do_ab_info: function(state, a, b, c) {
            /*
             * 0 - ничего
             * 1 - стоит серая точка
             * 2 - стоит красная точка (может быть первый вектор)
             * 3 - стоит первый вектор (может быть второй вектор)
             * 4 - векторы зафиксированы
             */
            var hasA = !!a;
            var hasB = !!b;

            var xa = a ? a.x : 0;
            var ya = a ? a.y : 0;
            var xb = b ? b.x : 0;
            var yb = b ? b.y : 0;
            var xc = c ? c.x : 0 ;
            var yc = c ? c.y : 0;
            
            var aStarted = state==2;
            var aFixed = state==3;
            
            var info, gray = true;
            if(!hasA) {
                //Ничего не выбрано
                info = aStarted ? 'Укажите конец первого вектора' : 'Укажите начало первого вектора';
            } else if (!hasB) {
                //Выбран один вектор
                info = aFixed ? 'Укажите конец второго вектора' : 'Укажите конец первого вектора';
            } else {
                //Выбрано два вектора
                gray = false;
                
                var sa = PsHtml.vector('a', xa, ya); 
                var sb = PsHtml.vector('b', xb, yb);
                var sc = PsHtml.vector('c', xc, yc);

                info = 'В базисе {' + PsHtmlCnst.VECT_I +', ' + PsHtmlCnst.VECT_I + '}:&nbsp; ' + sa + ' + ' + sb + ' = ' + sc;
            }
            this.doInfo(info, gray)
        },
        
        
        //Разложение вектора по базису 
        do_bas: function(df) {
            //Разложение вектора по базису
            /*
             * 0 - начальное состояние
             * 1 - стоит красная точка (может быть вектор)
             * 2 - вектор 1 зафиксирован, стоит серая точка
             * 3 - вектор 1 зафиксирован, стоит красная точка (может быть вектор)
             * 4 - вектор 2 зафиксирован, стоит серая точка
             * 5 - базис есть, стоит красная точка (может быть вектор)
             * 6 - вектор в базисе зафиксирован
             */
            var A_COLOR = 'red';
            var E1_COLOR = '#6F6';
            var E2_COLOR = '#69F';
            
            var state = 0;
            var _this = this;
            
            var x, y;
            df.bind({
                discr: true,
                move: function(e) {
                    var x0, y0, e1, e2, a, lambda, mu;
                    x = e.x;
                    y = e.y;
                    switch (state) {
                        case 0:
                        case 2:
                        case 4:
                        case 6:
                            this.clearMarker('gray');
                            this.putDotMarker(x, y, 'gray');
                            break;
                        case 1:
                            x0 = this.getMarker('red').x;
                            y0 = this.getMarker('red').y;
                            this.clearVector('e1');
                            this.putVector({
                                p1: [x0, y0],
                                p2: [x, y],
                                pen: E1_COLOR,
                                text: PsHtmlCnst.VECT_E1,
                                id: 'e1'
                            });
                            break;
                        case 3:
                            x0 = this.getMarker('red').x;
                            y0 = this.getMarker('red').y;
                            this.clearVector('e2');
                            this.putVector({
                                p1: [x0, y0],
                                p2: [x, y],
                                pen: E2_COLOR,
                                text: PsHtmlCnst.VECT_E2,
                                id: 'e2'
                            });
                            break;
                        case 5:
                            x0 = this.getMarker('red').x;
                            y0 = this.getMarker('red').y;
                            this.clearVectorsExcept(['e1', 'e2']);
                            this.putVector({
                                p1: [x0, y0],
                                p2: [x, y],
                                pen: A_COLOR,
                                text: PsHtmlCnst.VECT_A,
                                id: 'a'
                            });

                            a = this.getVector('a');
                            if (a) {
                                e1 = this.getVector('e1');
                                e2 = this.getVector('e2');
                                
                                var xa = a.x;
                                var ya = a.y;
                                var x1 = e1.x;
                                var y1 = e1.y;
                                var x2 = e2.x;
                                var y2 = e2.y;
                                
                                var koef = x1*y2-x2*y1;
                                lambda = (xa*y2-ya*x2)/koef;
                                mu = (x1*ya-xa*y1)/koef;
                                //todo - пересчёт
                                
                                var lambdaR = PsMath.round(lambda, 2);
                                var muR = PsMath.round(mu, 2);
                                
                                //this.setVectorText('a', PsHtml.vector('a', lambdaR, muR));
                                
                                var xc = x0+lambda*x1;
                                var yc = y0+lambda*y1;
                                
                                this.putVector({
                                    p1: [x0, y0],
                                    p2: [xc, yc],
                                    pen: E1_COLOR,
                                    text: PsHtml.num2strTrim(lambdaR) + PsHtmlCnst.VECT_E1
                                });
                                
                                this.putVector({
                                    p1: [xc, yc],
                                    p2: [xc+mu*x2, yc+mu*y2],
                                    pen: E2_COLOR,
                                    text: PsHtml.num2strTrim(muR) + PsHtmlCnst.VECT_E2
                                });
                            }
                            break;
                    }
                    _this.do_bas_info(state, this.getVector('e1'), this.getVector('e2'), this.getVector('a'), lambda, mu);
                },
                click: function(e) {
                    x = e.x;
                    y = e.y;
                    switch (state) {
                        case 0:
                            this.clearMarker('gray');
                            this.putDotMarker(x, y, 'red');
                            ++state;
                            break;
                        case 1:
                            if (this.hasMarker('red', [x, y])) {
                                this.clearMarker('red');
                                this.putDotMarker(x, y, 'gray');
                                --state;
                                break;
                            }
                            if (this.hasVector('e1')){
                                this.clearMarker('red');
                                //this.putDotMarker(x, y, 'gray');
                                ++state;
                            };
                            break;
                        case 2:
                            this.putDotMarker(x, y, 'red');
                            ++state;
                            break;
                        case 3:
                            if (this.hasMarker('red', [x, y])) {
                                this.clearMarker('red');
                                this.putDotMarker(x, y, 'gray');
                                --state;
                                break;
                            }
                            if (this.hasVector('e2')) {
                                var e1 = this.getVector('e1');
                                var e2 = this.getVector('e2');
                                
                                var x1 = e1.x;
                                var y1 = e1.y;
                                var x2 = e2.x;
                                var y2 = e2.y;
                                
                                if ((x2==0 && x1==0) || (y2==0 && y1==0) || (x2!=0 && y2!=0 && x1/x2==y1/y2)) {
                                    InfoBox.popupError('Вектор не подходит для базисного');
                                } else {
                                    this.clearMarker('red');
                                    ++state;
                                }
                            };
                            break;
                        case 4:
                            this.putDotMarker(x, y, 'red');
                            ++state;
                            break;
                        case 5:
                            if (this.hasVector('a')){
                                this.clearMarker('red');
                                //this.putDotMarker(x, y, 'gray');
                                ++state;
                            };
                            if (this.hasMarker('red', [x, y])) {
                                this.clearMarker('red');
                                this.putDotMarker(x, y, 'gray');
                                --state;
                            }
                            break;
                        case 6:
                            this.clearVectorsExcept(['e1', 'e2']);
                            this.putDotMarker(x, y, 'red');
                            --state;
                            break;
                    }

                    _this.do_bas_info(state, this.getVector('e1'), this.getVector('e2'), this.getVector('a'));
                },                
                out: function() {
                    this.clearMarker('gray');
                }
            });
            
            _this.do_bas_info(state);
        },
        
                
        do_bas_info: function(state, e1, e2, a, lambda, mu) {
            if (state==6) return;
            /*
             * 0 - начальное состояние
             * 1 - стоит красная точка (может быть вектор)
             * 2 - вектор 1 зафиксирован, стоит серая точка
             * 3 - вектор 1 зафиксирован, стоит красная точка (может быть вектор)
             * 4 - вектор 2 зафиксирован, стоит серая точка
             * 5 - базис есть, стоит красная точка (может быть вектор)
             * 6 - вектор в базисе зафиксирован
             */
            var x1 = e1 ? e1.x : 0;
            var y1 = e1 ? e1.y : 0;
            var x2 = e2 ? e2.x : 0;
            var y2 = e2 ? e2.y : 0;
            var xa = a ? a.x : 0 ;
            var ya = a ? a.y : 0;
            
            var hasA  = !!a;
            
            var info = '';
            var wrap = true;
            
            switch (state) {
                case 0:
                    info = 'Укажите начало первого базисного вектора';
                    break;
                case 1:
                    info = 'Укажите конец первого базисного вектора';
                    break;
                case 2:
                    info = 'Укажите начало второго базисного вектора';
                    break;
                case 3:
                    info = 'Укажите конец второго базисного вектора';
                    break;
                case 4:
                    info = 'Укажите начало вектора';
                    break;
                case 5:
                    if (hasA) {
                        wrap = false;
                        
                        lambda = PsMath.round(lambda, 2);
                        mu = PsMath.round(mu, 2);
                        
                        info = 'в базисе {' + PsHtmlCnst.VECT_E1 +', ' + PsHtmlCnst.VECT_E2 + '}:&nbsp; ' + PsHtmlCnst.VECT_A + ' = ' + 
                        PsHtml.vecSum(lambda, PsHtmlCnst.VECT_E1, mu, PsHtmlCnst.VECT_E2) + ',&nbsp; ' + PsHtml.vector('a', lambda, mu);
                        
                        info += '<br/>';
                        
                        info+= 'в базисе {' + PsHtmlCnst.VECT_I +', ' + PsHtmlCnst.VECT_I + '}:&nbsp; ' + 
                        PsHtml.vector(PsHtmlCnst.VECT_E1, x1, y1) + ',&nbsp; ' + PsHtml.vector(PsHtmlCnst.VECT_E2, x2, y2) + ',&nbsp; ' +
                        PsHtml.vector('a', xa, ya);
                            
                    } else {
                        info = 'Укажите конец вектора';
                    }
                    break;
            }
            
            this.doInfo(info, wrap);
        },


        //Определение угла между векторами
        do_ang: function(df) {
            /*
             * 0 - начальное состояние
             * 1 - стоит красная точка (может быть вектор)
             * 2 - вектор 1 зафиксирован, стоит серая точка
             * 3 - вектор 1 зафиксирован, стоит красная точка (может быть вектор)
             * 4 - вектор 2 зафиксирован, стоит серая точка
             */
            var E1_COLOR = '#6F6';
            var E2_COLOR = '#69F';
            
            var state = 0;
            var _this = this;
            
            var dravIJ = function(state) {
                var p1 = [state.minX, state.minY];
                df.putVector({
                    p1: p1,
                    coords: [1, 0],
                    pen: E1_COLOR,
                    text: PsHtmlCnst.VECT_I,
                    id: 'i'
                });
                
                df.putVector({
                    p1: p1,
                    coords: [0, 1],
                    pen: E2_COLOR,
                    text: PsHtmlCnst.VECT_J,
                    id: 'j'
                });
            }
            
            var x0, y0, x, y;
            df.bind({
                discr: true,
                move: function(e) {
                    x = e.x;
                    y = e.y;
                    switch (state) {
                        case 0:
                        case 2:
                        case 4:
                            this.clearMarker('gray');
                            this.putDotMarker(x, y, 'gray');
                            break;
                        case 1:
                            x0 = this.getMarker('red').x;
                            y0 = this.getMarker('red').y;

                            this.clearVectorsExcept(['i', 'j']);
                            this.putVector({
                                p1: [x0, y0],
                                p2: [x, y],
                                pen: E1_COLOR,
                                text: PsHtmlCnst.VECT_A,
                                id: 'a'
                            });
                            break;
                        case 3:
                            x0 = this.getMarker('red').x;
                            y0 = this.getMarker('red').y;
                            
                            this.clearVectorsExcept(['i', 'j', 'a']);
                            this.putVector({
                                p1: [x0, y0],
                                p2: [x, y],
                                pen: E2_COLOR,
                                text: PsHtmlCnst.VECT_B,
                                id: 'b'
                            })
                            break;
                    }
                    _this.do_ang_info(state, this.getVector('a'), this.getVector('b'));
                },
                click: function(e) {
                    x = e.x;
                    y = e.y;
                    switch (state) {
                        case 0:
                            this.clearMarker('gray');
                            this.putDotMarker(x, y, 'red');
                            ++state;
                            break;
                        case 1:
                            if (this.hasMarker('red', [x, y])) {
                                this.clearMarker('red');
                                this.putDotMarker(x, y, 'gray');
                                --state;
                                break;
                            }
                            if (this.hasVector('a')){
                                this.clearMarker('red');
                                //this.putDotMarker(x, y, 'gray');
                                ++state;
                            };
                            break;
                        case 2:
                            this.putDotMarker(x, y, 'red');
                            ++state;
                            break;
                        case 3:
                            if (this.hasMarker('red', [x, y])) {
                                this.clearMarker('red');
                                this.putDotMarker(x, y, 'gray');
                                --state;
                                break;
                            }
                            if (this.hasVector('b')) {
                                this.clearMarkers();
                                ++state;
                            };
                            break;
                        case 4:
                            this.clearVectorsExcept(['i', 'j']);
                            this.clearMarkers();
                            
                            this.putDotMarker(x, y, 'red');
                            state=1;
                            break;
                    }

                    _this.do_ang_info(state, this.getVector('a'), this.getVector('b'));
                },                
                out: function() {
                    this.clearMarker('gray');
                },
                rebuild: function(state) {
                    dravIJ(state);
                }
            });
            dravIJ(df.state);
            
            _this.do_ang_info(state);
        },
        
                
        do_ang_info: function(state, a, b) {
            if (state==4) return;
            /*
             * 0 - начальное состояние
             * 1 - стоит красная точка (может быть вектор)
             * 2 - вектор 1 зафиксирован, стоит серая точка
             * 3 - вектор 1 зафиксирован, стоит красная точка (может быть вектор)
             * 4 - вектор 2 зафиксирован, стоит серая точка
             */
            var x1 = a ? a.x : 0;
            var y1 = a ? a.y : 0;
            var x2 = b ? b.x : 0;
            var y2 = b ? b.y : 0;
            
            var info = '';
            var wrap = true;
            
            switch (state) {
                case 0:
                    info = 'Укажите начало первого вектора';
                    break;
                case 1:
                    info = 'Укажите конец первого вектора';
                    break;
                case 2:
                    info = 'Укажите начало второго вектора';
                    break;
                case 3:
                    if (b) {
                        wrap = false;
                        
                        var modA = PsMath.dist(x1, y1);
                        var modAr = PsMath.round(modA, 2);
                        var modB = PsMath.dist(x2, y2);
                        var modBr = PsMath.round(modB, 2);
                        var ab = x1*x2+y1*y2;
                        var cos = ab/(modA*modB);
                        var cosr = PsMath.round(cos, 2);
                        var alphar = PsMath.round(PsMath.radToGrad(PsMath.arccos(cos)), 2);
                        
                        info = 'В базисе {' + PsHtmlCnst.VECT_I +', ' + PsHtmlCnst.VECT_J + '}:&nbsp; ' + 
                        PsHtml.vector('a', x1, y1) + ', ' +
                        PsHtml.vector('b', x2, y2) + '<br/>' +
                        
                        'a=' + modAr + ',&nbsp; b=' + modBr + ',&nbsp; ' +
                        PsHtmlCnst.VECT_A+PsHtmlCnst.VECT_B + '=' +
                        PsHtml.num2strBr(x1)+'&sdot;'+PsHtml.num2strBr(x2)+'+'+
                        PsHtml.num2strBr(y1)+'&sdot;'+PsHtml.num2strBr(y2) + '=' + PsHtml.num2str(ab)+',&nbsp; ' +
                        'cos'+PsHtmlCnst.ALPHA+'='+PsHtmlCnst.VECT_A+PsHtmlCnst.VECT_B+'/ab='+PsHtml.num2str(cosr)+',&nbsp; ' +
                        PsHtmlCnst.ALPHA+'='+PsHtml.num2str(alphar)+'&deg;';
                    
                        
                    } else {
                        info = 'Укажите конец второго вектора';
                    }
                    break;
            }
            
            this.doInfo(info, wrap);
        }
    }
    
    
    DekarVectPlugin.init();
});
