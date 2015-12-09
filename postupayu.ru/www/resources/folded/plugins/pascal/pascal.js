$(function() {

    var FMANAGER = PsFoldingManager.FOLDING('pl', 'pascal');
    var STORE = FMANAGER.store();

    function PascalManager ($table, rowsCnt) {
        var _this = this;
    
        this.dirty = false;
        this.buildTreangle = function(rows) {
            this.dirty = false;

            rows = parseInt(rows);
            //rows - то-же самое, что последний Y
            if(rows <= 0) return;
        
            this.TABLE = $table.empty();
    
            this.LAST_X = 2 * rows;
            this.LAST_Y = rows;
            this.CENTER_X = rows;
        
            this.ABS2TD = {}; // Все ячейки поля
            this.BINOM2TD = {}; // Все числа в треугольнике
        
            this.Y2NUMB_TD = {};
            this.X2HEADER_TD = {};
        
            var rowNums = [];
            var binoms2abs = {};

            var k;
            var absCoords;
        
            //Верхняя полоска
            var $td;
            var $tr;
        
            for (var y = -1; y <= this.LAST_Y; y++) {
                k = 0;
                $tr = $('<tr></tr>');
                for (var x = -1; x <= this.LAST_X; x++) {
                    $td = $('<td></td>');
                    $tr.append($td);

                    if(x < 0 && y < 0) {
                        //Нулевая ячейка
                        $td.addClass('zero');
                        continue;
                    }

                    if(y < 0) {
                        //Верхняя полоска
                        $tr.addClass('top');
                        $td.addClass('top').data('x', x);
                        this.X2HEADER_TD[x] = $td;
                        continue;
                    }
                
                    if(x < 0) {
                        //Номер строки
                        $td.html(y).addClass('numb').data('y', y);
                        this.Y2NUMB_TD[y] = $td;
                        continue;
                    }
                
                    $td.data('x', x).data('y', y);
                
                    absCoords = 'x'+x+'y'+y;
                
                    this.ABS2TD[absCoords] = $td;
            
                    if(y == 0 && x == this.CENTER_X) {
                        binoms2abs[absCoords] = 1;
                    } else {
                        var leftTop = binoms2abs['x'+(x-1)+'y'+(y-1)];
                        var rightTop = binoms2abs['x'+(x+1)+'y'+(y-1)];
                        if(leftTop || rightTop){
                            leftTop = leftTop ? leftTop : 0;
                            rightTop = rightTop ? rightTop : 0;
                            binoms2abs[absCoords] = leftTop + rightTop;
                        }
                    }
                
                    if(binoms2abs[absCoords]) {
                        var num = binoms2abs[absCoords];

                        $td.html(num).addClass('num').data('numMod', y > 0 ? num%y : num);
                        $td.data('num', num).data('n', y).data('k', k);
                        rowNums.push(num);
                    
                        this.BINOM2TD['n'+y+'k'+k] = $td;
                        ++k;
                    }
                }
            
                if(y >= 0) {
                    $tr.data('nums', rowNums).data('y', y).data('n', y);
                    rowNums = [];
                }
                $table.append($tr);
            }
            //        $table.append($table.find('tr.top').clone());
        
            //Наполнение данными
            this.TR_ALL = $table.find('tr');
            this.TD_ALL = $table.find('td');
        
            this.TR_TOP = this.TR_ALL.filter('.top');
            this.TD_TOP = this.TR_TOP.children('td.top');
        
            this.TRS = this.TR_ALL.not('.top');
            this.TD_NUMS = this.TD_ALL.filter('.num');

            this.TD_CELLS = this.TRS.children('td').not('.numb');
        }
    
        this.buildTreangle(rowsCnt);
    
        //Вспомогательные функции
        this.tdByBinom = function(n, k) {
            if(n < 0 || n > this.LAST_Y || k < 0 || k > n){
                return null;
            }
            return this.BINOM2TD['n'+n+'k'+k];
        }
    
        this.tdByAbs = function(x, y) {
            if(x < 0 || x > this.LAST_X || y < 0 || y > this.LAST_Y){
                return null;
            }
            return this.ABS2TD['x'+x+'y'+y];
        }

        this.trByNum = function(n) {
            return $(this.TRS.get(n));
        }
    
        this.tdNumb = function(n) {
            return this.Y2NUMB_TD[n];
        }

        this.tdTop = function(x) {
            return this.X2HEADER_TD[x];
        }
    
        this.cellsBetweenNums = function() {
            var $res = $();
            for (var n = 1; n <= this.LAST_Y; n++) {
                var lX = this.tdByBinom(n, 0).data('x') + 1;
                var rX = this.tdByBinom(n, n).data('x') - 1;
                for (var x = lX; x <= rX; x = x + 2) {
                    $res = $res.add(this.tdByAbs(x, n));
                }            
            }
            return $res;
        }
    
        this.CLASSES = [
        'selected', 
        'selected_red', 
        'selected_blue', 
        'selected_green', 
        'gray', 
        'green'
        ];
    
        this.classesSearch = '.' + this.CLASSES.join(', .');
        this.classesRemove = this.CLASSES.join(' ');
        this.clearSelection = function(cssClass) {
            if(cssClass) {
                $table.find('.'+cssClass).removeClass(cssClass);
            } else {
                $table.find(this.classesSearch).removeClass(this.classesRemove);
            }
        }
    
        this.addHoverToNums = function(callbackIn, callbackOut, $tds) {
            $tds = $tds ? $tds : this.TD_NUMS;
            $tds.unbind();
            $tds.bind('mouseenter', function() {
                var $td = $(this);
                callbackIn.call(_this, $td);
            });
            $tds.bind('mouseleave', function() {
                var $td = $(this);
                callbackOut.call(_this, $td);
            });
        }
    
        this.addHoverToField = function(callbackIn, callbackOut) {
            var $tds = this.TD_CELLS.add(this.TD_TOP);
            $tds.bind('mouseenter', function() {
                var $td = $(this);
                callbackIn.call(_this, $td);
            });
            $tds.bind('mouseleave', function() {
                var $td = $(this);
                callbackOut.call(_this, $td);
            });
        }
    
        this.addHoverTr = function(callbackIn, callbackOut, $trs) {
            //$trs - Возможность передать строки извне, чтобы не вешать слешатели на все строки
            $trs = $trs ? $trs : this.TRS;
            $trs.unbind();
            $trs.bind('mouseenter', function() {
                callbackIn.call(_this, $(this));
            });
            $trs.bind('mouseleave', function() {
                callbackOut.call(_this, $(this));
            });
        }
    
        this.clearState = function() {
            this.clearSelection();
            this.TR_ALL.unbind();
            this.TD_ALL.unbind();
        
            if(this.dirty) {
                this.TD_TOP.html('');
                this.TABLE.removeClass('painted');
                this.TD_CELLS.removeClass('chet nechet clickable').html('');
                this.TD_NUMS.each(function(){
                    var $td = $(this);
                    $td.html($td.data('num'));
                }).removeAttr('title');
                this.dirty = false;
            }
        }
    
        this.setHeaderTdNums = function() {
            this.dirty = true;
            this.TD_TOP.each(function() {
                var $td = $(this);
                $td.html($td.data('x'));
            });
        }
    
        this.replaceNums = function(callback) {
            this.dirty = true;
            this.TD_NUMS.each(function() {
                var $td = $(this);
                var replace = callback.call(_this, $td);
                if(replace!==null){
                    $td.html(replace);
                }
            });
        }
    
        this.replaceNumsWithMod = function() {
            this.replaceNums(function($td){
                return $td.data('n') > 0 ? $td.data('numMod') : null;
            });
        }

        this.addNumsClickOnce = function(callback) {
            this.dirty = true;
            this.TD_NUMS.unbind('click').addClass('clickable').one('click', function() {
                callback.call(_this, $(this));
                _this.TD_NUMS.unbind('click').removeClass('clickable');
            });
        }

        //Передвигаем числа вправо на 2n
        this.moveNumsTo2n = function() {
            this.dirty = true;
            this.TD_NUMS.html('');

            var $shifted = this.TD_ALL.filter('.shifted');
            if ($shifted.isEmptySet()) {
                var _this = this;
                this.TRS.each(function(){
                    var $tr = $(this);
                    var n = $tr.data('n');
            
                    var mod;
                    var k = 0;
                    var $_tdBinom;
                    var $_tdCell;
                    for (var x = 2*n; x <= 3*n; x++) {
                        $_tdBinom = _this.tdByBinom(n, k++);
                        $_tdCell = _this.tdByAbs(x, n);
                        if($_tdBinom && $_tdCell) {
                            mod = $_tdBinom.data('numMod');
                            $_tdCell.html(mod).addClass('shifted').data('shifted', mod);
                        } else {
                            break;
                        }
                    }
                });
            }
            else {
                $shifted.each(function(){
                    var $td = $(this);
                    $td.html($td.data('shifted'));
                });
            }
        }
    
        //Заглушка, которой заполняются ячейки закрашенного треугольника
        this.makePainted = function() {
            this.replaceNums(function($td) {
                var num = $td.data('num');
                var chet = num%2==0;
                $td.addClass(chet ? 'chet' : 'nechet').attr('title', num);
                return '<span class="fill">xxx</span>';
            });
            this.TABLE.addClass('painted');
        }

        this.fillPainted = function() {
            this.cellsBetweenNums().each(function() {
                var $td = $(this);
                var x = $td.data('x');
                var y = $td.data('y');

                var $tdLeft = _this.tdByAbs(x-1, y);
                var $tdRight = _this.tdByAbs(x+1, y);
            
                if (!$tdLeft || !$tdRight) {
                    return;
                }

                var $tdTop = _this.tdByAbs(x, y-1);
                var $tdBottom = _this.tdByAbs(x, y+1);

                if ($tdLeft.is('.chet') && $tdRight.is('.chet')) {
                    if (($tdTop && $tdTop.is('.chet')) || ($tdBottom && $tdBottom.is('.chet'))) {
                        $td.addClass('chet');
                    }
                }
                else
                if ($tdLeft.is('.nechet') && $tdRight.is('.nechet')) {
                    if (($tdTop && $tdTop.is('.nechet')) || ($tdBottom && $tdBottom.is('.nechet'))) {
                        $td.addClass('nechet');
                    }
                }
            });
        }

        this.fillPaintedClear = function() {
            this.cellsBetweenNums().removeClass('chet nechet')
        }
    
        this.makePaintedMoveLeft = function() {
            this.dirty = true;
            this.TD_NUMS.html('');
            this.TRS.each(function(){
                var $tr = $(this);
                var n = $tr.data('n');
            
                var $_tdBinom;
                var $_tdCell;
                for (var x = 0; x <= n; x++) {
                    $_tdBinom = _this.tdByBinom(n, x);
                    $_tdCell = _this.tdByAbs(x, n);
                    if($_tdBinom && $_tdCell) {
                        var num = $_tdBinom.data('num');
                        var chet = num%2==0;
                        $_tdCell.html('<span class="fill">xxx</span>').addClass(chet ? 'chet' : 'nechet').attr('title', num);
                    } else {
                        break;
                    }
                }
            });
        
            this.TABLE.addClass('painted');
        }
    
        //Сдвигает треугольник Паскаля влево
        this.moveLeft = function() {
            this.dirty  = true;
            this.TD_NUMS.html('').each(function() {
                var $td = $(this);
                _this.tdByAbs($td.data('k'), $td.data('n')).html($td.data('num'));
            }); 
        }
    
        //Передвигаем числа влево
        this.moveNumsLeft = function() {
            this.dirty = true;
            this.TD_NUMS.html('');

            var $shifted = this.TD_ALL.filter('.shifted');
            if ($shifted.isEmptySet()) {
                var _this = this;
                this.TRS.each(function(){
                    var $tr = $(this);
                    var n = $tr.data('n');
            
                    var mod;
                    var k = 0;
                    var $_tdBinom;
                    var $_tdCell;
                    for (var x = 2*n; x <= 3*n; x++) {
                        $_tdBinom = _this.tdByBinom(n, k++);
                        $_tdCell = _this.tdByAbs(x, n);
                        if($_tdBinom && $_tdCell) {
                            mod = $_tdBinom.data('numMod');
                            $_tdCell.html(mod).addClass('shifted').data('shifted', mod);
                        } else {
                            break;
                        }
                    }
                });
            }
            else {
                $shifted.each(function(){
                    var $td = $(this);
                    $td.html($td.data('shifted'));
                });
            }
        }
    
        this.getColTds = function($td) {
            var idx = $td.data('x') + 1;
            var res = $();
            this.TR_ALL.each(function() {
                res = res.add($($(this).children('td').get(idx)));
            });
            return res;
        }
    
        this.getParentNums = function($td) {
            var n = $td.data('n');
            var k = $td.data('k');

            var $left = this.tdByBinom(n-1, k-1);
            var $right = this.tdByBinom(n-1, k);
            return $left && $right ? $left.add($right) : ($left ? $left : $right);
        }
    
        //Возвращает ячейку и симметричную ей
        this.getSameTds = function($td) {
            var n = $td.data('n');
            var k = $td.data('k');
            var $same = this.tdByBinom(n, n - k);
            return $same && $same.data('k')!=k ? $td.add($same) : $td;
        }
    
        //Возвращает биномиальные элементы строки
        this.getLineNumbTd = function($TdTr) {
            var $tr = $TdTr.is('tr') ? $TdTr : $TdTr.parent('tr:first');
            return $tr.children('td.numb');
        }
    
        //Возвращает первый и последний элемент в строке
        this.getFirstLast = function($tr) {
            var n = $tr.data('n');
            var $tdF = this.tdByBinom(n, 0);
            var $tdL = n > 0 ? this.tdByBinom(n, n) : null;
            return $tdL ? $tdF.add($tdL) : $tdF;
        }
    
        //Возвращает каждый k-тый элемент строки
        this.getEach = function(k) {
            var $res = $();
            for(var n=0; n<=this.LAST_Y; ++n) {
                $res = $res.add(this.tdByBinom(n, k));
            }
            return $res;
        }
    
        //Получает числа Каталана - для чётных строк выбирается 
        //центральное число и следующее за ним
        this.getKatalan = function($tr) {
            var n = $tr.data('n');
            if(n%2 != 0) {
                return null;//---
            }
            var k = n/2;
            var $center = this.tdByBinom(n, k);
            var $next = this.tdByBinom(n, k + 1);
        
            return {
                c: $center,
                n: $next,
                num: k
            };
        }
    
        //Возвращает строки с простыми номерами
        this.getPrimeTrs = function() {
            var $res = $();
            for(var n = 2; n <= this.LAST_Y; n++) {
                if(PsMath.isprime(n)) {
                    $res = $res.add(this.trByNum(n));
                }
            }
            return $res;
        }
    
        //Возвращает строки с простыми номерами
        this.get2mMinus1Trs = function() {
            var $res = $();
            var m = 0;
            var n = Math.pow(2, m) - 1;
            while (n <= this.LAST_Y) {
                $res = $res.add(this.trByNum(n).data('m', m));
                n = Math.pow(2, ++m) - 1;
            }
            return $res;
        }
    
        //Возвращает числа, стоящие от текущего числа буквой V
        this.getVnums = function($td, exclude) {
            var n = $td.data('n');
            var k = $td.data('k');
        
            var $res = exclude ? $() : $td;

            var $_td;
            do {
                //Берём слева
                $_td = this.tdByBinom(--n, --k);
                $res = $_td ? $res.add($_td) : $res;
            } while($_td);
        
            n = $td.data('n');
            k = $td.data('k');
            do {
                //Берём справа
                $_td = this.tdByBinom(--n, k);
                $res = $_td ? $res.add($_td) : $res;
            } while($_td);
            return $res;
        }

        //Получает элементы нисходящей/восходящей диагонали над ячейкой
        this.getNumsDiagAbove = function($td, nishod) {
            var n = $td.data('n');
            var k = $td.data('k') + (nishod ? 0 : -1);

            var $res = $();
            var $_td = this.tdByBinom(--n, k);
            while(n>=0 && $_td) {
                $res = $res.add($_td);
            
                k = k + (nishod ? -1 : 0);
                $_td = this.tdByBinom(--n, k);
            }

            return $res;
        }

        //Возвращает все ячейки, находящиеся над этой
        this.getNumsAbove = function($td, skipLines) {
            skipLines = skipLines ? skipLines : 0;
            var n = $td.data('n') - skipLines * 2;
            var k = $td.data('k') - skipLines;
        
            var $res = $();
            var $_td = this.tdByBinom(n, k);
            while ($_td && n >= 0) {
                $res = $res.add(this.getVnums($_td));
                --k;
                n = n - 2;
                $_td = this.tdByBinom(n, k);
            }

            return $res;
        }
    
        //Возвращает числа Фибоначчи
        this.getFibonacci = function($TdTr) {
            var n = $TdTr.data('n');
            var k = 0;
        
            var $res = $();
            var $td = this.tdByBinom(n, k);
            while($td) {
                $res = $res.add($td);
                $td = this.tdByBinom(--n, ++k);
            }
            return $res.reverse();
        }
    
        //Вернёт ячейку с номером строки, первую и последнюю td в треугольнике.
        this.getLineNumbered = function($tr) {
            var res = $tr.children('.numb');
            if($tr.children('.num').size() > 1) {
                res = res.add($tr.children('.num:first').next().next('.num'));
            }
            return res;
        }
    }

    /*
 * 
 * 
 * --== МЕНЕДЖЕР ==--
 * 
 * 
 */
    var PascalManagerPlugin = {
        STATE: {
            tab: null,
            act: {}
        },
    
        DEF_SIZE: 10,
        MIN_SIZE: 2,
        MAX_SIZE: 53,
        HUGE_SIZE: 16,
    
        init: function() {
            var _this = this;
        
            this.BODY = $('.pascal_tr');

            this.INFO = this.BODY.children('.info');
            this.info();

            this.INPUT = this.BODY.find('.ptr_name input');
            this.INPUT.change(function() {
                var $input = $(this);
                var val = $input.val();
                val = PsIs.number(val) ? val : _this.DEF_SIZE;
                val = val <  _this.MIN_SIZE ? _this.MIN_SIZE : val;
                val = val >  _this.MAX_SIZE ? _this.MAX_SIZE : val;
                $input.val(val);
            
                _this.MANAGER.buildTreangle(val);
                _this.BODY.toggleClass('huge', val >= _this.HUGE_SIZE);
                _this.doSetState();
            }).val(this.DEF_SIZE);
        
            this.MANAGER = new PascalManager(this.BODY.children('.pascal'), this.DEF_SIZE);
        
            //ТАБЫ
            this.TAB_NAMES = [];
            this.TABS = this.BODY.find('.tabs').children().hide();
            this.TABS_A = this.BODY.find('h3.ctrl a');
            this.TABS_A.clickClbck(function(tab) {
                _this.doTab(tab);
            }).each(function() {
                var $tabHref = $(this);
                var tab = getHrefAnchor($tabHref);
                _this.TABS.filter('.' + tab).find('.triggers a').clickClbck(function(action) {
                    _this.doAction(tab, action);
                });
                _this.TAB_NAMES.push(tab);
            });
        
            this.TABS.filter('.props').find('.help').hide().each(function() {
                var $help = $(this);
                var $a = $('<span>').html('?').
                /*hover(function(){
                $help.show();
            }, function() {
                if(!$a.is('.cur')) {
                    $help.hide();
                }
            })*/
                click(function(e) {
                    e.preventDefault();
                    $a.toggleClass('cur');
                    $help.setVisible($a.is('.cur'));
                }).insertBefore($help).addClass('help_trigger noselect');
            });

            this.BODY.find('a.select_cell').hover(function(){
                _this.MANAGER.clearSelection();
            
                var coords = getHrefAnchor(this);
                var $td = _this.MANAGER.BINOM2TD[coords];
                $td.addClass('selected');
            }, 
            function() {
                _this.MANAGER.clearSelection();
            });
        
            this.restoreState();
            this.doSetState();
            //Показываем тело плагина
            this.BODY.show();
        },
    
        info: function(msg) {
            msg = msg ? msg : '';
            msg = '<span class="fill">' + PsHtml.binom('n', 'k') + PsHtml.combination('n', 'k') + '</span>' + msg;
            this.INFO.html(msg);
        },
    
        clearState: function() {
            this.MANAGER.clearState();
            this.info();
        },
    
        storeState: function(){
            STORE.set('tab', this.STATE.tab);
            for (var tab in this.STATE.act) {
                if(this.STATE.act[tab]){
                    STORE.set('action_'+tab, this.STATE.act[tab]);
                }
            }
        },
    
        restoreState: function() {
            var _this = this;
            this.STATE.tab = STORE.get('tab', this.STATE.tab);
            this.STATE.tab = this.STATE.tab ? this.STATE.tab : this.TAB_NAMES[0];
            $.each(this.TAB_NAMES, function() {
                _this.STATE.act[this] = STORE.get('action_'+this, _this.STATE.act[this]);
            });
        },
    
        isState: function(tab, action) {
            return (this.STATE.tab == tab) && (action == this.STATE.act[tab]);
        },
    
        doTab: function(tab, force) {
            var $tabHref = this.TABS_A.filter('a[href="#'+tab+'"]');
            if (!force && $tabHref.is('.cur')) {
                return;
            }
            this.TABS_A.removeClass('cur');
            $tabHref.addClass('cur');
            this.TABS.hide().filter('.' + tab).show();
        
            //Выполняем установку меню
            this.clearState();
            this.doMenu(tab);
        
            this.STATE.tab = tab;
            if (this.STATE.act[tab]) {
                this.doAction(tab, this.STATE.act[tab]);
            }
            this.storeState();
        },
    
        doAction: function(tab, action) {
            if(isEmpty(action)) return;
            
            var $triggers = this.TABS.filter('.' + tab).find('.triggers');
            var $li = $triggers.children('li').removeClass('cur').has('a[href="#'+action+'"]').addClass('cur');

            if(isEmpty($li)) return;
            
            var method = 'do_' + tab;
            if(this.hasOwnProperty(method)) {
                this.clearState();
                this.STATE.act[tab] = action;
                this.storeState();
                this[method].call(this, action);
            }
        },
    
        doSetState: function() {
            this.doTab(this.STATE.tab, true);
        },
    
        doMenu: function(menu) {
            var _this = this;
        
            var info = null;
        
            switch (menu) {
                case 'binom_coeff':
                    this.MANAGER.addHoverToNums(function($td) {
                        $td.addClass('selected');
                    
                        var n = $td.data('y');
                        var k = $td.data('k');
                        var num = $td.data('num');
                        info = PsHtml.combination('n', 'k') + ' = ' + PsHtml.binom('n', 'k') + ' = ' + PsHtml.combination(n, k) + ' = ' + PsHtml.binom(n, k) + ' = ' + num;
                        _this.info(info);
                    }, function() {
                        _this.info();
                        _this.MANAGER.clearSelection();
                    });
                    break;

                case 'binom':
                    this.MANAGER.addHoverTr(function($tr) {
                        var n = $tr.addClass('selected').data('y');
                    
                        info = '';
                        if(n > 0) {
                            var nums = $tr.data('nums');
                            var i = 0;
                    
                            var slag;
                            var aPow;
                            var bPow;
                            $.each(nums, function(i, num) {
                                aPow = n-i;
                                bPow = i;
                                slag = '';
                                slag+= num > 1 ? num : '';
                                slag+= aPow > 0 ? 'a' : '';
                                slag+= aPow > 1 ? '<sup>'+aPow+'</sup>' : '';
                                slag+= bPow > 0 ? 'b' : '';
                                slag+= bPow > 1 ? '<sup>'+bPow+'</sup>' : '';
                                info += info ? ' + ' : '';
                                info += slag;
                            });
                        } else {
                            info = '1';
                        }
                        info = '(a + b)<sup>' + n + '</sup> = ' + info;
                        
                        _this.info(info);
                    }, function() {
                        _this.info();
                        _this.MANAGER.clearSelection();
                    });
                    break;
                
                case 'prime':
                    //Простые числа
                    break;
            }
        },
    
        do_props: function(prop) {
            var _this = this;
        
            var info = null;
        
            switch (prop) {
                case 'sum_prev':
                    this.MANAGER.addHoverToNums(function($td){
                        _this.info();
                        $td.addClass('selected_blue');
                        var $parNums = _this.MANAGER.getParentNums($td);
                        if(!isEmpty($parNums)) {
                            $parNums.addClass('selected');
                            info = '';
                            $parNums.each(function(){
                                var num = $(this).data('num') + '';
                                info = info + (info ? ' + ' : '') + num;
                            });
                            _this.info(info + ' = ' + $td.data('num'));
                        }
                    }, function() {
                        _this.info();
                        _this.MANAGER.clearSelection();
                    });
                    break;

                case 'symmetry':
                    this.MANAGER.addHoverToNums(function($td){
                        _this.MANAGER.getSameTds($td).addClass('selected');
                    }, function(){
                        _this.MANAGER.clearSelection();
                    });
                    break;


                case 'odin':
                    this.MANAGER.addHoverTr(function($tr){
                        _this.MANAGER.getFirstLast($tr).addClass('selected');
                    }, function() {
                        _this.MANAGER.clearSelection();
                    });
                    break;


                case 'dva':
                    this.MANAGER.addHoverTr(function($tr){
                        _this.MANAGER.getLineNumbered($tr).addClass('selected');
                    }, function() {
                        _this.MANAGER.clearSelection();
                    });
                    break;

                case 'nchis':
                    this.MANAGER.addHoverTr(function($tr){
                        $tr.addClass('selected');
                        var n = $tr.data('y');
                        _this.info('n: ' + n + ', чисел в строке: ' + (n+1));
                    }, function() {
                        _this.info();
                        _this.MANAGER.clearSelection();
                    });
                    break;

                case 'dva_nat':
                    _this.MANAGER.getEach(1).addClass('selected');
                    break;
            
                case 'tri':
                    _this.MANAGER.getEach(2).addClass('selected');
                    break;

                case 'piramidal':
                    _this.MANAGER.getEach(3).addClass('selected');
                    break;

                case 'kv_piramidal':
                    this.MANAGER.addHoverTr(function($tr){
                        var n = $tr.data('n');
                        var chisNum = n - 2;
                        if (chisNum < 1) {
                            return;
                        }
                        var $n1 = this.tdByBinom(n - 1, 3);
                        var $n2 = this.tdByBinom(n, 3).addClass('selected');
                    
                        info = '';
                        var res = 0;
                        if($n1) {
                            $n1.addClass('selected');
                            info += $n1.data('num');
                            res += $n1.data('num');
                        }

                        info += info ? ' + ' : '';
                        info += $n2.data('num');
                        res += $n2.data('num');

                        _this.info('T<sub>'+chisNum+'</sub> = ' + info + (chisNum > 1 ? ' = ' + res : ''));
                    }, function() {
                        _this.info();
                        _this.MANAGER.clearSelection();
                    });
                    break;
                    break;

                case 'katalan':
                    this.MANAGER.addHoverTr(function($tr){
                        _this.info();
                        var katalan = _this.MANAGER.getKatalan($tr);
                        if(!katalan){
                            return;
                        }
                        var $center = katalan.c.addClass('selected');
                        var cn = $center.data('num');
                        var $next = katalan.n ? katalan.n.addClass('selected') : null;
                        var nn = $next ? $next.data('num') : 0;

                        _this.info('C<sub>'+katalan.num+'</sub> = ' + cn + (nn ? ' &minus; ' + nn : '') + ' = ' + (cn - nn));

                    }, function() {
                        _this.info();
                        _this.MANAGER.clearSelection();
                    });
                    break;
                
                case 'sum2n':
                    this.MANAGER.addHoverTr(function($tr){
                        $tr.addClass('selected');

                        var nums = $tr.data('nums');
                        info = '';
                        var res = 0;
                        $.each(nums, function(i, num){
                            info = info + (info ? ' + ' + num : num);
                            res+=num;
                        });
                        info += ' = 2<sup>'+$tr.data('y')+'</sup> = ' + res;
                        _this.info(info);
                    
                    }, function() {
                        _this.info();
                        _this.MANAGER.clearSelection();
                    });
                    break;
                
                case 'sum2sumprev':
                    this.MANAGER.addHoverTr(function($tr) {
                        var n = $tr.data('n');
                        if(n < 1) return;
                    
                        $tr.addClass('selected');
                        var $prev = $tr.prev('tr').addClass('selected_blue');
                    
                        var sumTr = '';
                        var sumPrev = '';
                        $.each($tr.data('nums'), function(i, num){
                            sumTr += sumTr ? ' + ' + num : num;
                        });
                    
                        $.each($prev.data('nums'), function(i, num){
                            sumPrev += sumPrev ? ' + ' + num : num;
                        });
                        info = '2&sdot;(' + sumPrev + ') = ' + sumTr + ' = ' + Math.pow(2, n);
                        _this.info(info);
                    
                    }, function() {
                        _this.info();
                        _this.MANAGER.clearSelection();
                    });
                    break;
                
                case 'lucas':
                    var $primeTrs = this.MANAGER.getPrimeTrs();

                    this.MANAGER.addHoverTr(function($tr) {
                        var n = $tr.data('n');
                    
                        if(!PsMath.isprime(n)) return;
                    
                        this.tdNumb(n).addClass('selected_green');
                    
                        info = '';
                        var nums = {};
                        $tr.children('.num').each(function(){
                            var $td = $(this);
                            var num = $td.data('num');
                            if(num != 1) {
                                $td.addClass('selected');
                                if(!nums['x'+num]) {
                                    nums['x'+num] = true;
                                
                                    info += info ? '; ' : '';
                                    info += num + '/' + n + '=' + (num/n);
                                }
                            }
                        });
                        $tr.children('.numb').addClass();
                        _this.info(info);
                    
                    }, function() {
                        _this.info();
                        _this.MANAGER.clearSelection();
                    },
                    $primeTrs);
                
                    break;
                
                case 'findpath':
                    this.MANAGER.addHoverToNums(function($td) {
                        this.getNumsAbove($td).addClass('selected');
                        info = 'Кол-во способов добраться: ' + $td.data('num') + '. Возможные маршруты принадлежат выдел. параллелограмму.';
                        _this.info(info);
                    }, function() {
                        _this.info();
                        _this.MANAGER.clearSelection();
                    });
                    break;
                
                case 'fibonacci':
                    this.MANAGER.addHoverTr(function($tr) {
                        info = '';
                        var res = 0;
                        var n = $tr.data('y');
                        var $fibTds = this.getFibonacci($tr).addClass('selected');
                        this.tdNumb(n).addClass('selected_blue');
                        $fibTds.each(function(){
                            var num = $(this).data('num');
                            info += info ? ' + ' : '';
                            info += num;
                            res += num;
                        });
                        info = 'F<sub>' + (n+1) + '</sub> = ' + info + (info == '1' ? '' : ' = ' + res);
                        _this.info(info);
                    }, function() {
                        _this.info();
                        _this.MANAGER.clearSelection();
                    });
                    break;
         
                case 'zerosum':
                    this.MANAGER.addHoverTr(function($tr) {
                        var n = $tr.data('y');
                        if(n==0) return;
                    
                        info = '';
                        var res = 0;
                        var num;
                        $tr.children('td.num').each(function(i, $td) {
                            $td = $($td);
                            num = $td.data('num');
                        
                            if(i%2==0) {
                                //plus
                                res += num;
                                info += info ? ' + ' : '';
                                $td.addClass('selected');
                            }
                            else{
                                //minus
                                res -= num;
                                info += info ? ' &minus; ' : '';
                                $td.addClass('selected_blue');
                            }
                            info += num;
                        
                        });
                    
                        info += ' = ' + res;

                        _this.info(info);
                    }, function() {
                        _this.info();
                        _this.MANAGER.clearSelection();
                    });
                    break;
                
                case 'chet_nechet':
                    this.MANAGER.addHoverTr(function($tr) {
                        var n = $tr.data('y');
                    
                        if(n==0) return;
                    
                        var res = 0;
                        var chetRes = '';
                        var nechetRes = '';
                        var num;
                        $tr.children('td.num').each(function(i, $td) {
                            $td = $($td);
                            num = $td.data('num');

                            if(i % 2 ==0) {
                                //Чёт
                                chetRes += (chetRes ? ' + ' : '') + num;
                                res += num;
                                $td.addClass('selected');
                            }
                            else{
                                //Нечёт
                                nechetRes += (nechetRes ? ' + ' : '')  + num;
                                $td.addClass('selected_blue');
                            }
                        });
                        
                        info = chetRes + ' = ' + nechetRes + ' = ' + res;
                    
                        _this.info(info);
                    }, function() {
                        _this.info();
                        _this.MANAGER.clearSelection();
                    });
                    break;
                
                case 'nechet_2nminus1':
                    var $trs = this.MANAGER.get2mMinus1Trs();
                    $trs.each(function(){
                        var $tr = $(this);
                        var n = $tr.data('n');
                        $tr.addClass('selected');
                        _this.MANAGER.tdNumb(n).addClass('selected_blue');
                    });
                
                    this.MANAGER.addHoverTr(function($tr) {
                        var n = $tr.data('n');
                        var m = $tr.data('m');
                    
                        _this.info('2<sup>' + m + '</sup> &minus; 1 = ' + Math.pow(2, m) + ' &minus; 1 = ' + n);
                    }, function() {
                        _this.info();
                    },$trs);
                    break;
            
                case 'nechet_stepen2':
                    this.MANAGER.addHoverTr(function($tr) {
                        var n = $tr.data('n');
                        var bin = new Number(n).toString(2);
                        var edinic = bin.charsCnt('1');
                    
                        var nums = '';
                        $tr.find('td.num').each(function() {
                            var $td = $(this);
                            var num = $td.data('num');
                            if (num%2!=0) {
                                nums += nums ? ', ' : '';
                                nums += num;
                                $td.addClass('selected');
                            }
                        });
                    
                        _this.info('n = ' + n + ', n<sub>2</sub> = ' + bin +', единиц: ' + edinic + ', нечётных чисел в строке: 2<sup>'+edinic+'</sup> = '+Math.pow(2, edinic) + (nums ? ' ('+nums+')' : ''));
                    }, function() {
                        _this.info();
                        _this.MANAGER.clearSelection();
                    },$trs);
                    break;
                
                case 'diagMinusOdin':
                    this.MANAGER.addHoverToNums(function($td) {
                        var $parr = this.getNumsAbove($td, 1).addClass('selected');
                        this.getVnums($td, true).addClass('gray');
                        $td.addClass('selected_blue');
                    
                        info = '';
                        //Сортируем
                        $parr.sort(function(td1, td2){
                            td1 = $(td1);
                            td2 = $(td2);
                            var n1 = td1.data('n');
                            var k1 = td1.data('k');
                            var n2 = td2.data('n');
                            var k2 = td2.data('k');
                            return n1 > n2 ? 1 : (k1 > k2 ? 1 : -1);
                        }).each(function() {
                            var $td = $(this);
                            info += (info ? ' + ' : '') + $td.data('num');
                        });

                        info = $td.data('num') + ' &minus; 1 = ' + (info ? info : '0') + ' = ' + ($td.data('num') - 1);
                    
                        _this.info(info);
                    }, function() {
                        _this.info();
                        _this.MANAGER.clearSelection();
                    });
                    break;
                
                case 'kvadratIsN2N':
                    this.MANAGER.addHoverTr(function($tr) {
                        var n = $tr.addClass('selected').data('y');
                        var $td = this.tdByBinom(2*n, n);
                        if ($td) {
                            $td.addClass('selected_green'); 
                        }
                    
                        info = '';
                        var res = 0;
                        $.each($tr.data('nums'), function(i, num) {
                            info += info ? ' + ' : '';
                            info += num + '<sup>2</sup>';
                            res  += Math.pow(num, 2);
                        });

                        _this.info(info + ' = ' + res);
                    
                    }, function() {
                        _this.info();
                        _this.MANAGER.clearSelection();
                    },$trs);
                    break;
                
                case 'diagSumNish':
                    this.MANAGER.addHoverToNums(function($td) {
                        if($td.data('k') == $td.data('n')){
                            return;//Число находится на правой грани
                        }
                    
                        info = '';
                        var $tds = this.getNumsDiagAbove($td.addClass('selected_blue'), true).addClass('selected');
                        $tds.each(function(){
                            var num = $(this).data('num');
                            info += (info ? ' + ' : '') + num;
                        });
                        info = $td.data('num') + ' = ' + info;
                        _this.info(info);
                    }, function() {
                        _this.info();
                        _this.MANAGER.clearSelection();
                    });
                    break;
                
                case 'diagSumVosh':
                    this.MANAGER.addHoverToNums(function($td) {
                        if($td.data('k') == 0){
                            return;//Число находится на левой грани
                        }
                    
                        info = '';
                        var $tds = this.getNumsDiagAbove($td.addClass('selected_blue'), false).addClass('selected');
                        $tds.each(function(){
                            var num = $(this).data('num');
                            info += (info ? ' + ' : '') + num;
                        });
                        info = $td.data('num') + ' = ' + info;
                        _this.info(info);
                    }, function() {
                        _this.info();
                        _this.MANAGER.clearSelection();
                    });
                    break;
                
                case 'hexagon':
                    //Cn - 1k - 1 . Cnk + 1 . Cn + 1k = Cn - 1k . Cn + 1k + 1 . Cnk - 1. 
                    this.MANAGER.addHoverToNums(function($td) {
                        var n = $td.data('n');
                        var k = $td.data('k');
                    
                        var arr = [
                        [n-1, k-1],
                        [n, k+1],
                        [n+1, k],

                        [n-1, k],
                        [n, k-1],
                        [n+1, k+1]
                        ];
                    
                        var info1 = '';
                        var info2 = '';
                        var res = 1;
                    
                        var m_this = this;
                        $.each(arr, function(i, nk) {
                            var $_td = m_this.tdByBinom(nk[0], nk[1]);
                            var num = $_td ? $_td.data('num') : PsMath.cnk(nk[0], nk[1]);
                            if(i < 3) {
                                info1 += info1 ? '&sdot;' : '';
                                info1 += num;
                                res *= num;
                                if ($_td) $_td.addClass('selected_green');
                            } else {
                                info2 += info2 ? '&sdot;' : '';
                                info2 += num;
                                if ($_td) $_td.addClass('selected_blue');
                            }
                        });
                    
                        _this.info(info1 + ' = ' + info2 + ' = ' + res);
                    }, function() {
                        _this.info();
                        _this.MANAGER.clearSelection();
                    });
                    break;

                case 'makePainted':
                    this.MANAGER.makePainted();
                    this.info('Для большей наглядности треугольник можно <a href="#" class="brush">раскрасить</a>');
                    var $trigger = this.INFO.find('a').clickClbck(function() {
                        var fillPainted = this.toggleClass('active').is('.active');
                        if (fillPainted) {
                            _this.MANAGER.fillPainted();
                        } else {
                            _this.MANAGER.fillPaintedClear();
                        }
                        STORE.set('fill_painted', fillPainted);
                    });
                    if(STORE.get('fill_painted', false)) {
                        $trigger.click();
                    }
                    break;
                
                case 'makePaintedMoveLeft':
                    this.MANAGER.makePaintedMoveLeft();
                    break;

                case 'moveLeft':
                    this.MANAGER.moveLeft();
                    break;
            }
        },
    
        // СВОЙСТВА ПРОСТЫХ ЧИСЕЛ
        do_prime: function(action) {
            var _this = this;
            var info = null;
        
            this.MANAGER.setHeaderTdNums();
        
            switch (action) {
                case 'show_header':
                    break;
                
                case 'replace':
                    this.MANAGER.replaceNumsWithMod();
                    this.MANAGER.addHoverToNums(function($td) {
                        $td.addClass('selected');

                        var n = $td.data('n');
                        var num = $td.data('num');

                        info = '';
                        if(n > 0) {
                            info = num + ' mod ' + n + ' = ';
                        }
                        info += '' + $td.html();
                        _this.info(info);
                    }, function() {
                        _this.info();
                        _this.MANAGER.clearSelection();
                    });
                    break;
                
                case 'move':
                    this.MANAGER.replaceNumsWithMod();
                    this.MANAGER.moveNumsTo2n();
                
                    //Раскрасим столбцы с простыми числами
                    this.MANAGER.TD_TOP.each(function() {
                        var $td = $(this);
                        var x = $td.data('x');
                    
                        var $collTds = _this.MANAGER.getColTds($td).filter('.shifted, .top');
                        if(x==0 || x==1){
                            //0,1
                            $collTds.addClass('selected_blue');
                        }else if(PsMath.isprime(x)) {
                            //простое
                            if(x < 139) {
                                $collTds.addClass('selected_green');
                            }else{
                                $collTds.each(function() {
                                    var $td = $(this);
                                    if($td.is('.top') || $td.data('shifted')==0) {
                                        $td.addClass('selected_green');
                                    } else {
                                        $td.addClass('selected_red');
                                    }
                                });
                            }
                        }else{
                        //составное
                        }
                    });
                
                    this.MANAGER.addHoverToField(function($td) {
                        var x = $td.data('x');
                        var isPrime = PsMath.isprime(x);
                        if(!isPrime && x > 1) {
                            var $tds = _this.MANAGER.getColTds($td);
                            $tds.filter('.shifted, .top').addClass('selected');
                        }

                        _this.info('Число ' + x + ' &mdash; ' + (x < 2 ? 'ни простое ни составное' : (isPrime ? 'простое' : 'составное')));
                    }, function() {
                        _this.info();
                        _this.MANAGER.clearSelection('selected');
                    });
                    break;
            }
        },
    
        // ТЕСТЫ
        test1_valid: 0,
        test1_invalid: 0,

        test2_valid: 0,
        test2_invalid: 0,
    
        do_tests: function(action) {
            if(!this.isState('tests', action)) return;
        
            this.MANAGER.clearSelection();
            var n = PsUtil.nextInt(0, this.MANAGER.LAST_Y);
            var k = PsUtil.nextInt(0, n);

            var lX = this.MANAGER.CENTER_X - 2;
            var rX = this.MANAGER.CENTER_X + 2;

            var _this = this;

            switch (action) {
                case 'binom_koef':
                    this.info((PsRand.bool() ? PsHtml.binom(n, k) : PsHtml.combination(n, k)) + ' = ?');
                
                    this.MANAGER.tdTop(lX).html(this.test1_valid).addClass('selected_green');
                    this.MANAGER.tdTop(rX).html(this.test1_invalid).addClass('selected_red');
                
                    this.MANAGER.addNumsClickOnce(function($td) {
                        var valid = $td.data('n')==n && $td.data('k')==k;
                        if(valid) {
                            ++_this.test1_valid;
                            $td.addClass('selected_green');
                        } else {
                            ++_this.test1_invalid;
                            $td.addClass('selected_red');
                            this.tdByBinom(n, k).addClass('selected_green');
                        }
                        
                        PsUtil.startTimerOnce(function(){
                            _this.do_tests(action);
                        }, 1000);
                    });
                
                    break;
                
                case 'random_question':
                
                    this.MANAGER.tdTop(lX).html(this.test2_valid).addClass('selected_green');
                    this.MANAGER.tdTop(rX).html(this.test2_invalid).addClass('selected_red');
                
                
                    var qNum = PsUtil.nextInt(1, 4);
                    var queston;
                    switch (qNum) {
                        case 1:
                            queston = 'Какая строка содержит биномиальные коэффициенты разложения (a + b)<sup>' + n + '</sup>?';
                            this.MANAGER.addNumsClickOnce(function($_td) {
                                this.trByNum(n).addClass('selected_green');

                                var nSelected = $_td.data('y');
                                if(nSelected == n) {
                                    ++_this.test2_valid;
                                } else {
                                    ++_this.test2_invalid;
                                    this.trByNum(nSelected).addClass('selected_red');
                                }

                                PsUtil.startTimerOnce(function(){
                                    _this.do_tests(action);
                                }, 1000);
                                
                            });
                            break;
                        
                        case 2:
                            queston = 'В какой строке сумма чисел равна 2<sup>' + n + '</sup>?';
                            this.MANAGER.addNumsClickOnce(function($td) {
                                this.trByNum(n).addClass('selected_green');

                                var nSelected = $td.data('y');
                                if(nSelected == n) {
                                    ++_this.test2_valid;
                                } else {
                                    ++_this.test2_invalid;
                                    this.trByNum(nSelected).addClass('selected_red');
                                }

                                PsUtil.startTimerOnce(function(){
                                    _this.do_tests(action);
                                }, 1000);
                            });
                            break;

                        case 3:
                            var nishod = PsRand.bool();

                            queston = 'Какой элемент непосредственно под выделенной ' + (nishod ? 'нисходящей' : 'восходящей') + ' диагональю равен сумме её членов?';
                        
                            n = PsUtil.nextInt(2, this.MANAGER.LAST_Y);
                            k = PsUtil.nextInt(nishod ? 0 : 1, nishod ? n-1 : n);
                        
                            var $td = this.MANAGER.tdByBinom(n, k);
                        
                            this.MANAGER.getNumsDiagAbove($td, nishod).addClass('selected');

                            this.MANAGER.addNumsClickOnce(function($_td) {
                                this.tdByBinom(n, k).addClass('selected_green');
                                var valid = $_td.data('n')==n && $_td.data('k')==k;

                                if(valid) {
                                    ++_this.test2_valid;
                                } else {
                                    ++_this.test2_invalid;
                                    $_td.addClass('selected_red');
                                }

                                PsUtil.startTimerOnce(function(){
                                    _this.do_tests(action);
                                }, 1000);
                            });
                            break;

                        case 4:
                            n = PsUtil.nextInt(2, this.MANAGER.LAST_Y);
                            k = PsUtil.nextInt(1, n-1);
                        
                            queston = 'Сколькими различными способами можно составить букет из '+k+' различных цветов, если имеется '+n+' наименований цветов?';
                        
                            this.MANAGER.addNumsClickOnce(function($_td) {
                                var cnk = this.tdByBinom(n, k).data('num');
                                var valid = cnk == $_td.data('num');

                                this.TD_NUMS.each(function() {
                                    var $td = $(this);
                                    if ($td.data('num')==cnk) {
                                        $td.addClass('selected_green');
                                    }
                                });

                                if(valid) {
                                    ++_this.test2_valid;
                                } else {
                                    ++_this.test2_invalid;
                                    $_td.addClass('selected_red');
                                }

                                PsUtil.startTimerOnce(function(){
                                    _this.do_tests(action);
                                }, 1000);
                            });
                            break;
                        
                    }
                    this.info(queston);

                    break;
                
            }
        }

    }

    PascalManagerPlugin.init();
});