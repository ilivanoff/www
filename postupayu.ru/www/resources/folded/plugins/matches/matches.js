$(function(){
    var FMANAGER = PsFoldingManager.FOLDING('pl', 'matches');
    var STORE = FMANAGER.store();

    function MatchesManager($table) {
        var _this = this;
    
        this.BT = FMANAGER.src('bt.png'),
        this.BTG = FMANAGER.src('btg.png'),
        this.LR = FMANAGER.src('lr.png'),
        this.LRG = FMANAGER.src('lrg.png'),
    
        this.dimension = function(size) {
            return isDefined(size) && size ? size : 3;
        }
    
        this.isDimensionNow = function(lines, cols) {
            return this.LAST_X && this.LAST_X==this.dimension(lines) && this.LAST_Y && this.LAST_Y==this.dimension(cols);
        }
    
        this.buildTable = function(lines, cols) {
            $table.empty();

            cols = this.dimension(cols);
            lines = this.dimension(lines);

            this.MATCHES = {};
            this.$MATCHES = $();
            this.$EMPTY = $();

            this.LAST_X = cols;
            this.LAST_Y = lines;

            for (var line = 0; line < 2*lines+1; line++) {
                var $tr = $('<tr>');
                var isHorisontal = line%2==0;
                var y = Math.floor(line/2);
            
                for (var col = 0; col < 2*cols+1; col++) {
                    var $td = $('<td>');
                    var x = Math.floor(col/2);
                
                    var hasImg = isHorisontal ? col%2==1 : col%2==0;
                
                    if (hasImg) {
                        var $img = crIMG(isHorisontal ? this.LR : this.BT).addClass('vis');
                        var $img_g = crIMG(isHorisontal ? this.LRG : this.BTG).addClass('hid');
                        $td.append($img).append($img_g);

                        var endX = isHorisontal ? x+1 : x;
                        var endY = isHorisontal ? y : y+1;

                        this.MATCHES['x'+x+'y'+y+'x'+endX+'y'+endY] = $td;
                        this.$MATCHES = this.$MATCHES.add($td);
                        $td.data('coords', x+''+y+''+endX+''+endY);
                    } else {
                        if(!isHorisontal) {
                            $td.addClass('x'+x+'y'+y);
                            this.$EMPTY = this.$EMPTY.add($td);
                        }
                    }
                
                    $tr.append($td);
                }
                $table.prepend($tr);
            }
        
            this.MATCHES_CNT = this.$MATCHES.size();
            this.SQUARES_CNT = this.getSquaresCnt();
        }
    
        this.getMatch = function (x, y, endX, endY) {
            var $match = this.MATCHES[isDefined(y) ? 'x'+x+'y'+y+'x'+endX+'y'+endY : x];
            return $match ? $match : null;
        }

        this.hasMatch = function (x, y, endX, endY) {
            var $match = this.getMatch(x, y, endX, endY);
            return $match ? !$match.is('.hidden') : false;
        }

        this.getHiddenCnt = function() {
            return this.$MATCHES.filter('.hidden').size();
        }

        this.getVisibleCnt = function() {
            return this.MATCHES_CNT - this.getHiddenCnt();
        }
    
        this.getExcludedCoordsAsStr = function() {
            var res = '';
            this.$MATCHES.filter('.hidden').each(function(){
                res += res ? ' ' : '';
                res += $(this).data('coords')
            });
            return res;
        }
    
        this.getSquereContent = function(sqBounds) {
            var $res = $();
            for (var x = sqBounds[0]; x < sqBounds[2]; x++) {
                for (var y = sqBounds[1]; y < sqBounds[3]; y++) {
                    $res = $res.add(this.$EMPTY.filter('.x'+x+'y'+y));
                }
            }
            return $res;
        }
    
        /*
         * ПОДСВЕТКА
         */
        this.doHighlightImpl = function(sqArr, hlFinishedCallback) {
            var _this = this;
            this.$EMPTY.removeClass('highlighted');
            if(sqArr.length > 0 && this.forceStopHl.length==0) {
                var item = sqArr.shift();
                this.getSquereContent(item).addClass('highlighted').size();
                setTimeout(function(){
                    _this.doHighlightImpl(sqArr, hlFinishedCallback);
                }, 300);
            } else {
                hlFinishedCallback.call();
            }
        }
    
        this.progress = false;
        this.highlightStart = function() {
            if(this.progress) return;
        
            this.progress = true;
            
            var wasBinded = this.unBind();
        
            this.doHighlightImpl(this.getSquares(), function() {
                _this.progress = false;
                if (wasBinded) {
                    _this.rebind();
                }
                _this.notifyWaits();
            });
        }
    
        this.highlightToggle = function() {
            if (this.progress) {
                this.highlightStop();
            } else {
                this.highlightStart();
            }
        }
    
        this.forceStopHl = [];
        this.highlightStop = function (callback) {
            this.forceStopHl.push($.isFunction(callback) ? callback : function(){});
            this.notifyWaits();
        }
    
        this.notifyWaits = function() {
            if(!this.progress){
                $.each(this.forceStopHl, function(i, callback) {
                    callback.call(_this);
                });
                this.forceStopHl = [];
            }
        }
    
        this.lastBindParams = null;
        this.bind = function(callback, maxCnt) {
            if (this.progress) return;
        
            this.unBind();
        
            this.lastBindParams = [callback, maxCnt];
            maxCnt = maxCnt && (maxCnt > 0) && (maxCnt < this.MATCHES_CNT) ? maxCnt : this.MATCHES_CNT;

            if (this.getHiddenCnt() >= maxCnt) return;
        
            this.$MATCHES.not('.hidden').one('click', function(e) {
                e.preventDefault();
                $(this).addClass('hidden').removeClass('clickable');
                var hidden = _this.getHiddenCnt();
                if (hidden >= maxCnt) {
                    _this.unBind();
                }
                if ($.isFunction(callback)) {
                    var squares = _this.getSquaresCnt();
                    callback.call(_this, hidden, squares);
                }
            }).addClass('clickable');
        }
    
        this.rebind = function() {
            if(this.lastBindParams) {
                this.bind(this.lastBindParams[0], this.lastBindParams[1]);
            }
        }
    
        this.unBind = function() {
            return !this.$MATCHES.unbind('click').filter('.clickable').removeClass('clickable').isEmptySet();
        }
    
        this.loadState = function(state) {
            for (var i = 0; i < state.length; i+=4) {
                var $m = this.getMatch(state[i], state[i+1], state[i+2], state[i+3]);
                if ($m) $m.addClass('hidden');
            }
        }
    
        this.clear = function(callback) {
            this.highlightStop(function() {
                this.unBind();
                this.$MATCHES.removeClass('hidden err');
                if ($.isFunction(callback)) {
                    callback.call(this);
                }
            });
        }
    
        this.rebuild = function(callback, lines, cols) {
            this.highlightStop(function() {
                this.buildTable(lines, cols);
                if ($.isFunction(callback)) {
                    callback.call(this);
                }
            });
        }
    
        this.clearWithBind = function(callback, maxCnt, lines, cols) {
            if (this.isDimensionNow(lines, cols)) {
                this.clear(function() {
                    this.bind(callback, maxCnt);
                });
            } else {
                this.rebuild(function() {
                    this.bind(callback, maxCnt);
                }, lines, cols);            
            }
        }

        this.clearWithLoad = function(state) {
            this.clear(function() {
                this.loadState(state);
            });
        }
        /*
         * Возвращает полные квадраты в поле
         */
        this.getSquares = function() {
            var res = [];
            for(var y=0; y < this.LAST_Y; y++) {
                for(var x=0; x < this.LAST_X; x++) {
                    //Проверяем точку
                    var endX = x;
                    var endY = y;
                    while ((++endX <= this.LAST_X) && (++endY <= this.LAST_Y)) {
                        if(this.isSquare(x, y, endX, endY)) {
                            res.push([x, y, endX, endY]);
                        }
                    }
                }
            }
        
            return res.sort(function(c1, c2) {
                var x1 = c1[0];
                var x2 = c2[0];
                var y1 = c1[1];
                var y2 = c2[1];
                var a1 = c1[0]-c1[2];
                var a2 = c2[0]-c2[2];
                if(a1!=a2) return a1 < a2 ? 1 : -1;
            
                if(y1!=y2) return y1 > y2 ? 1 : -1;
                return x1 > x2 ? 1 : -1;
            });
        }

        /* Возвращает "плохие" спички */
        this.getBadMatches = function() {
            var $has = {};
            $.each(this.getSquares(), function(i, cr) {
                var xl = cr[0];
                var xr = cr[2];
                var yb = cr[1];
                var yt = cr[3];
                for (var x=xl; x < xr; x++) {
                    $has['x'+x+'y'+yb+'x'+(x+1)+'y'+yb] = true;
                    $has['x'+x+'y'+yt+'x'+(x+1)+'y'+yt] = true;
                }
                for (var y=yb; y < yt; y++) {
                    $has['x'+xl+'y'+y+'x'+xl+'y'+(y+1)] = true;
                    $has['x'+xr+'y'+y+'x'+xr+'y'+(y+1)] = true;
                }
            });
        

            var $res = $();
            for (var v in this.MATCHES) {
                if(this.hasMatch(v) && !$has[v]){
                    $res = $res.add(this.getMatch(v));
                }
            }
            return $res;
        }
    
        /* Возвращает кол-во полных квадратов */
        this.getSquaresCnt = function() {
            return this.getSquares().length;
        }
    
        /* Возвращает кол-во всех квадратов */
        this.getSquaresTotal = function() {
            return this.getSquares().length;
        }
    
        /* Проверяет, является ли данный квадрат - полным */
        this.isSquare = function(_x, _y, _endX, _endY) {
            var x;
            var y;
        
            //Вправо низ
            for(x=_x; x<_endX; x++) {
                if(!this.hasMatch(x, _y, x+1, _y)){
                    return false;
                }
                if(!this.hasMatch(x, _endY, x+1, _endY)){
                    return false;
                }
            }
        
            //Вверх лево
            for(y=_y; y<_endY; y++) {
                if(!this.hasMatch(_x, y, _x, y+1)) {
                    return false;
                }
                if(!this.hasMatch(_endX, y, _endX, y+1)) {
                    return false;
                }
            }        

            return true;
        }
    }


    var MatchesManagerPlugin = {
        init: function() {
            var _this = this;
        
            this.BODY = $('.matches');
            this.MANAGER = new MatchesManager(this.BODY.find('table.match'));

            this.TASKS = this.BODY.find('.tasks_info>div');
            this.SWITCHER = this.BODY.find('.task_switcher').empty();
            this.MATCHES_STATE = this.BODY.find('.play_field .m_state');
            this.SQUARES_STATE = this.BODY.find('.play_field .s_state');
            this.ANSWERS = this.BODY.find('.answers');
        
            //Добавим слушатель начала показа квадратов
            this.SQUARES_STATE.click(function(e){
                e.preventDefault();
                _this.MANAGER.highlightToggle();
            });
        
            //Добавим слушатель сброса состояния
            this.MATCHES_STATE.click(function(e){
                e.preventDefault();
                if($(this).is('.clickable')) {
                    _this.setTask();
                }
            });
        
            //Добавим слушатель переключения задач
            this.SWITCHERS = $();
            this.TASKS.each(function() {
                var $div = $(this);
                var ident = $div.data('ident');
                var $sq = $('<div>').addClass('sq').click(function() {
                    _this.TASKS.removeClass('cur');
                    _this.SWITCHERS.removeClass('cur');
                    $div.addClass('cur');
                    $sq.addClass('cur');
                    _this.setTask();
                }).data('ident', ident).addClass(ident);
                _this.SWITCHER.append($sq);
                _this.SWITCHERS = _this.SWITCHERS.add($sq);
            });
        
            this.loadAnswers();
        },
    
        markPrepeared: function() {
            var ident = STORE.get('matches_task');
            var $switcher = ident ? this.SWITCHERS.filter('.' + ident) : null;
            $switcher = isEmpty($switcher) ? $(this.SWITCHERS.get(0)) : $switcher;
            $switcher.click();
        
            this.BODY.show();
        },
    
        /*Ответы к задачам*/
        loadAnswers: function() {
            if (defs.isAuthorized) {
                var _this = this;
                AjaxExecutor.execute('MatchesAnsLoad', {}, 
                    function(answers) {
                        _this.registerAnswers(answers);
                        _this.markPrepeared();
                    }, 'Загрузка ответов');
            } else {
                this.markPrepeared();
            }
        },
    
        reloadAnswers: function(ident) {
            if (!defs.isAuthorized) return;//---
        
            var _this = this;
            AjaxExecutor.execute('MatchesAnsLoad', {
                ident: ident
            }, function(answers) {
                _this.registerAnswers(answers);
            }, 'Загрузка ответов');
        },
    
        registerAnswers: function(obj) {
            if  (!PsIs.object(obj)) return;
            for (var taskIdent in obj) {
                this.registerAnswer(taskIdent, obj[taskIdent]);
            }
            this.showAnswersSelect();
        },
    
        registerAnswer: function(taskIdent, ansArr) {
            if (!taskIdent) return;
            ansArr = $.isArray(ansArr) ? ansArr : [];
        
            //Добавим select
            this.ANSWERS.children('.'+taskIdent).remove();
        
            var _this = this;
            var $select = $('<select>').addClass(taskIdent);
            $('<option>').val('').html('-- Не задано --').appendTo($select);
            $.each(ansArr, function(i, ans) {
                $('<option>').html('Ответ №'+(i+1)).val(ans).appendTo($select);
                _this.registerSended(taskIdent, ans);
            });
        
            this.ANSWERS.append($select);
        
            $select.change(function (){
                var val = $(this).val();
                _this.setAnswer(taskIdent, val);
            });
        },
    
        showAnswersSelect: function() {
            var $task = this.curTaskDiv();
            if(!$task) return;
            this.ANSWERS.children().val('').removeClass('cur').filter('.' + $task.data('ident')).addClass('cur');
        },
    
        /*Отправленные ответы*/
        sortAnswerString: function(str) {
            var matches = [];
            var i = 0;
            var match = '';
            while(i < str.length) {
                var ch = str.charAt(i++);
                if (ch != ' ') {
                    match += ch;
                }
                if (match.length == 4) {
                    matches.push(match);
                    match = '';
                }
            }

            return matches.sort(function(a, b){
                return a > b ? 1 : -1;
            }).join('');
        },
    
        SENDED: {},
    
        isSended: function(ident, answer) {
            answer = this.sortAnswerString(answer);
            var res = this.SENDED.hasOwnProperty(ident + '_' + answer);
            //consoleLog('is sended: ' + (ident + '_' + answer) + ' - ' + res);
            return res;
        },
    
        registerSended: function(ident, answer) {
            if(!ident || !answer) return;
            answer = this.sortAnswerString(answer);
            this.SENDED[ident + '_' + answer] = true;
        //consoleLog('sended: ' + (ident + '_' + answer));
        },
    
        saveAnswer: function(ident, strCoords) {
            if(this.isSended(ident, strCoords)) return;
            this.registerSended(ident, strCoords);
        
            var _this = this;
            AjaxExecutor.execute('MatchesAnsSave', {
                'ident': ident,
                'matches': strCoords
            }, function(registered) {
                if(registered) {
                    _this.reloadAnswers(ident);
                }
            }, 'Сохранение ответа');
        },
    
        /*Работа с задачами*/
        curTaskDiv: function() {
            var $div = this.TASKS.filter('.cur');
            return $div.size() == 1 ? $div : null;
        },
    
        setTask: function() {
            var $task = this.curTaskDiv();
            if(!$task) return;
            var _this = this;

            var matches = $task.data('m');
            var squares = $task.data('s');
            var lines = $task.data('r');
            var cols = $task.data('c');
            var ident  = $task.data('ident');
        
            //Построение задачи
            this.MANAGER.clearWithBind(function(matchesHidden, squaresLeft) {
                _this.updatePlayState(matches, matchesHidden, squares, squaresLeft);
                if(matchesHidden == matches) {
                    _this.finalizeState(ident, squares, squaresLeft);
                }
            }, matches, lines, cols);

            this.updatePlayState(matches, 0, squares, this.MANAGER.SQUARES_CNT);
            this.showAnswersSelect();
        
            STORE.set('matches_task', ident);
        },
    
        setAnswer: function(taskIdent, answer) {
            if (!answer) {
                this.setTask();
                return;
            }
        
            var $task = this.curTaskDiv();
            if(!$task) return;
            if(taskIdent != $task.data('ident')) return;

            var matches = $task.data('m');
            var squares = $task.data('s');

            this.MANAGER.clearWithLoad(answer);
            this.updatePlayState(matches, matches, squares, squares);
        },
    
        finalizeState: function(ident, squaresNeed, squaresLeft) {
            var valid = this.MANAGER.getBadMatches().addClass('err').isEmptySet();
            valid = valid && (squaresLeft == squaresNeed);
            if (valid) {
                //Задача решена
                this.SWITCHERS.filter('.' + ident).addClass('passed');
                this.MANAGER.highlightStart();
            
                var strCoords = this.MANAGER.getExcludedCoordsAsStr();
                this.saveAnswer(ident, strCoords);
            }
        },
    
        updatePlayState: function(matchesNeed, matchesNow, squaresNeed, squaresNow) {
            this.MATCHES_STATE.empty();
            this.SQUARES_STATE.empty();
        
            var i;
            /*Спички*/
            var has = false;
            for (i = 0; i < matchesNeed-matchesNow; i++) {
                this.MATCHES_STATE.append(crIMG(this.MANAGER.BTG));
            }
            for (i = 0; i < matchesNow; i++) {
                has = true;
                this.MATCHES_STATE.append(crIMG(this.MANAGER.BT));
            }
        
            this.MATCHES_STATE.toggleClass('clickable', has);
        
            /*Квадраты*/
            for (i = 0; i < this.MANAGER.SQUARES_CNT; i++) {
                this.SQUARES_STATE.append($('<div class="sq"></div>'));
            }
        
            this.SQUARES_STATE.children('.sq').each(function(i, td) {
                var $td = $(td);
                if (i < squaresNeed) {
                    $td.addClass('green');
                }
                if(i < squaresNow) {
                    $td.addClass('blue');
                }
            });
        
            this.SQUARES_STATE.append($('<div>').addClass('ctt').html(squaresNow));
        }
    }

    MatchesManagerPlugin.init();
});
