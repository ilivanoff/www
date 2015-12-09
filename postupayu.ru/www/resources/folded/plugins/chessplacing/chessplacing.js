$(function(){

    var ChessPlacingPlugin = {
        hodOrd: [],
    
        replayModeOn: false,
    
        figureImg: null,
        figureCode: null,
    
        mode: 0,
        modes: {
            m0: {
                name: 'независимость',
                title: 'расставить фигуры так, чтобы никакие две из них не угрожали друг другу',
                combinations: {
                    'R': 8,
                    'K': 16,
                    'B': 14,
                    'N': 32,
                    'Q': 8
                }
            },
            m1: {
                name: 'доминирование',
                title: 'расставить фигуры так, чтобы они держали под обстрелом все свободные поля доски',
                combinations: {
                    'R': 8,
                    'K': 9,
                    'B': 8,
                    'N': 12,
                    'Q': 5
                }
            }
        },
    
        userResults: {
            m0: {},
            m1: {}
        },

        init: function(){
            var _this = this;
        
            var chessPlacingDiv = $('.chessplacing');
        
            this.chessPlacingDesk = chessPlacingDiv.find('.chessboard');
        
            this.cbm = new ChessBoardManager(this.chessPlacingDesk);
        
            this.cells = this.cbm.getCells();

            this.figsDesk = chessPlacingDiv.find('.figs');
        
            this.info = chessPlacingDiv.find('.info');
        
            this.figuresLeft = chessPlacingDiv.find('.figures_left');
        
            /*
         * Установим режим: доминирование/независимость
         */
            this.modeHr = chessPlacingDiv.find('.mode a').empty().click(function(event){
                event.preventDefault();
                _this.toggleMode();
            });
            this.initMode();

            /*
         * Инициализируем кнопки управления
         */
            this.controller = new ButtonsController(
                chessPlacingDiv.find('.centered_block.controls>button'), {
                    click: this.onButtonClick,
                    ctxt: this
                });
        
            //this.updateButtonsState(); - обновление произойдёт на следующем шаге

            /*
         * Установим фигуру
         */
            this.buttons = this.figsDesk.find('tr.chessfigs td');
        
            this.buttons.click(function(){
                _this.figureSelected($(this));
            }).filter(':first').click();
        },
    
        initMode: function(){
            var modeOb = this.modes['m'+this.mode];
            this.modeHr.html(modeOb.name).attr('title', modeOb.title).addClass('m'+this.mode);
        },
    
        toggleMode: function(){
            var oldMode = this.mode;
            var newMode = (this.mode + 1) % 2;
        
            var modeOb = this.modes['m' + newMode];
        
            this.modeHr.html(modeOb.name).attr('title', modeOb.title).removeClass('m'+oldMode).addClass('m'+newMode);
        
            this.mode = newMode;
            this.bindInit();
        },
    
        onButtonClick: function(action) {
            switch (action) {
                case 'back':
                    this.onBack();
                    break;
                case 'roll':
                    this.onNew();
                    break;
                case 'clear':
                    this.onDrop();
                    break;
            }
        },
    
        updateButtonsState: function() {
            if(this.controller) {
                var isHodesDone = this.hodOrd.length > 0;
                var hasPassed = this.hasPassed();
            
                this.controller.recalcState({
                    back: {
                        colored: isHodesDone,
                        enabled: isHodesDone
                    },
                    roll: {
                        colored: isHodesDone,
                        enabled: isHodesDone
                    },
                    clear: {
                        colored: hasPassed,
                        enabled: hasPassed
                    }
                });
            }
        },
    
        figureSelected: function($td){
            this.buttons.removeClass('current');
            this.figureCode = $td.data('f');
            this.figureImg = $td.find('span');
            $td.addClass('current');
        
            this.bindInit();
        },
    
        bindInit: function(){
            this.clearState();
            this.cells.css('cursor', 'pointer').bind('click', function(event){
                ChessPlacingPlugin.cellClicked($(event.target));
            });
        },

        clearState: function(){
            this.hodOrd = [];
            this.cells.empty().unbind('click').css('cursor', 'default');
            this.updateInfo();
        },

        findCell : function(x, y){
            return this.cbm.getCell(x, y);
        },
    
        cellClicked: function($cell){
            var x = $cell.data('x');
            var y = $cell.data('y');
        
            this.cells.css('cursor', 'default').unbind('click');

            $cell.empty().append(this.getFigure());

            var allowed = this.getAllowed(x, y);

            $.each(allowed, function(idx, arr){
                var x = arr[0];
                var y = arr[1];
            
                var $allowedCell = ChessPlacingPlugin.findCell(x, y);
            
                if($allowedCell && $.trim($allowedCell.html())==''){
                    $allowedCell.html('x');
                }
            });
        
            if(this.figsLeft() > 0){
                this.cells.each(function(){
                    var $td = $(this);
                    var val = $.trim($td.html());
                    if(val=='' || (ChessPlacingPlugin.mode==1 && val=='x')){
                        $td.css('cursor', 'pointer').bind('click', function(event){
                            ChessPlacingPlugin.cellClicked($(event.target));
                        });
                    }
                });
            }
        
            this.updateInfo();
        },
    
        /*
     * Метод отмечает клетки, которые заняты после клика на ней и возвращает массив
     * клеток, на которые можно поставить фигуру.
     */
        getAllowed: function(x, y){
            this.hodOrd.push([x, y]);
        
            var allowed = [];
            var xtmp, ytmp, i;

            switch(this.figureCode){
                /*
             * Ладья
             */
                case 'R':
                    for (i = 1; i <= 8; i++) {
                        if(i>0 && i<9 && i!=x){
                            allowed.push([i, y]);
                        }
                    }
                    for (i = 1; i <= 8; i++) {
                        if(i>0 && i<9 && i!=y){
                            allowed.push([x, i]);
                        }
                    }
                    return allowed;

                /*
             * Король
             */
                case 'K':
                    allowed = [
                    [x, y-1],
                    [x, y+1],
                    [x-1, y],
                    [x+1, y],

                    [x-1, y-1],
                    [x-1, y+1],
                    [x+1, y-1],
                    [x+1, y+1]
                    ];
                    return allowed;

                /*
         * Ферзь
         */
                case 'Q':
                    for (i = 1; i <= 8; i++) {
                        if(i>0 && i<9 && i!=x){
                            allowed.push([i, y]);
                        }
                    }
                    for (i = 1; i <= 8; i++) {
                        if(i>0 && i<9 && i!=y){
                            allowed.push([x, i]);
                        }
                    }
                
                    for (i = 1; i < 8; i++) {
                        xtmp = x+i;
                        ytmp = y+i;
                        if(xtmp>=1 && xtmp<=8 && ytmp>=1 && ytmp<=8){
                            allowed.push([xtmp, ytmp]);
                        }
                        xtmp = x-i;
                        ytmp = y-i;
                        if(xtmp>=1 && xtmp<=8 && ytmp>=1 && ytmp<=8){
                            allowed.push([xtmp, ytmp]);
                        }
                        xtmp = x-i;
                        ytmp = y+i;
                        if(xtmp>=1 && xtmp<=8 && ytmp>=1 && ytmp<=8){
                            allowed.push([xtmp, ytmp]);
                        }
                        xtmp = x+i;
                        ytmp = y-i;
                        if(xtmp>=1 && xtmp<=8 && ytmp>=1 && ytmp<=8){
                            allowed.push([xtmp, ytmp]);
                        }
                    }
                    return allowed;
            
                /*
     * Слон
     */
                case 'B':
                    for (i = 1; i < 8; i++) {
                        xtmp = x+i;
                        ytmp = y+i;
                        if(xtmp>=1 && xtmp<=8 && ytmp>=1 && ytmp<=8){
                            allowed.push([xtmp, ytmp]);
                        }
                        xtmp = x-i;
                        ytmp = y-i;
                        if(xtmp>=1 && xtmp<=8 && ytmp>=1 && ytmp<=8){
                            allowed.push([xtmp, ytmp]);
                        }
                        xtmp = x-i;
                        ytmp = y+i;
                        if(xtmp>=1 && xtmp<=8 && ytmp>=1 && ytmp<=8){
                            allowed.push([xtmp, ytmp]);
                        }
                        xtmp = x+i;
                        ytmp = y-i;
                        if(xtmp>=1 && xtmp<=8 && ytmp>=1 && ytmp<=8){
                            allowed.push([xtmp, ytmp]);
                        }
                    }
                    return allowed;

                /*
 * Конь
 */
                case 'N':
                    allowed = [
                    [x-1, y-2],
                    [x-1, y+2],
                    [x-2, y-1],
                    [x-2, y+1],
                    [x+1, y-2],
                    [x+1, y+2],
                    [x+2, y-1],
                    [x+2, y+1]
                    ];
                    return allowed;
            }
        
            alert('Неизвестная фигура: '+this.figureCode);
        },
    
        figsLeft: function(){
            return this.modes['m'+this.mode].combinations[this.figureCode] - this.cbm.getCellsWithFigsCount();
        },
    
        updateInfo: function() {
            var _this = this;
        
            var infoMsg = '&nbsp;';
            var done = false;

            var figs = this.cbm.getCellsWithFigsCount();
            var busy = this.cells.filter(':contains(x)').size();
            var free = 64 - figs - busy;
            var left = this.figsLeft();
        
            switch (this.mode) {
                case 0:
                    done = left == 0;
                    if(!done) {
                        infoMsg = 
                        'Фигур расставлено: <span>' + figs + 
                        '</span>, осталось расставить: <span>' + left+
                        '</span>, клеток свободно: <span class="red">' + free + '</span>';
                    }
                    break;
                case 1:
                    done = left == 0 && free==0;
                    if(!done) {
                        infoMsg = 
                        'Фигур расставлено: <span>' + figs + 
                        '</span>, осталось расставить: <span>' + left+
                        '</span>, клеток свободно: <span class="red">' + free + '</span>';
                    }
                    break;
            }

            if(done) {
                this.info.addClass('done').html('Поздравляю, расстановка верна!');
                this.onDone();
            }
            else{
                this.info.removeClass('done').html(infoMsg);
            }
        
            /*
* Оставшиеся фигуры
*/
            this.figuresLeft.empty();
        
            for (var i = 0; i < left; ++i) {
                if(i>0 && i%3 == 0){
                    this.figuresLeft.append('<br />');
                }
                this.figuresLeft.append(this.getFigure());
            }
        
            /*
* Отмечаем пройденные задачи
*/
            this.buttons.removeClass('done0 done1');
            $.each(this.userResults['m'+this.mode], function(i, val){
                _this.buttons.filter('.'+i).addClass('done'+_this.mode);
            });
        
            /*
* Видимость управляющих кнопок (назад, сбросить)
*/
            this.updateButtonsState();
        },
    
        replay: function(moves){
            if(this.replayModeOn){
                return;
            }
        
            if(moves && moves.length>0){
                this.replayModeOn = true;
            
                //Полностью очищаем состояние
                this.clearState();
                //Выстраиваем ходы
                $.each(moves, function(idx, move){
                    ChessPlacingPlugin.cellClicked(ChessPlacingPlugin.findCell(move[0], move[1]));
                });

                this.replayModeOn = false;
            }
        },
    
        /*
* Новая игра
*/
    
        onNew: function(){
            this.bindInit();  
        },
    
        onBack: function(){
            if(this.hodOrd.length<=1){
                this.bindInit();
            }
            else{
                this.hodOrd.pop();
                this.replay(this.hodOrd);  
            }
        },
    
        onDrop: function() {
            for (var mode in this.userResults) {
                this.userResults[mode] = {};
            }
            this.bindInit();  
        },
    
        sended: false,
        onDone: function() {
            this.userResults['m'+this.mode][this.figureCode] = PsArrays.joinExpanded(this.hodOrd);

            if (this.sended) {
                return;//---
            }

            var send = true;
            for (var mode in this.modes) {
                for (var comb in this.modes[mode].combinations) {
                    send = send && this.userResults[mode].hasOwnProperty(comb);
                }
            }
        
            if (send) {
                this.sended = true;
                PsPointsGiverManager.check('pl-chessplacing', {
                    hodes: this.userResults
                });
            }
        },
    
        hasPassed: function() {
            for (var mode in this.userResults) {
                for (var fig in this.userResults[mode]) {
                    return true;
                }
            }
            return false;
        },
    
        getFigure: function(){
            return this.figureImg.clone();
        }
    }


    ChessPlacingPlugin.init();
});
