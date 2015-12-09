$(function() {

    var chessKnightManager = {
        hod: {},
        hodOrd: [],
        currentCell: null,
    
        replayModeOn: false,
        replayStopped: false,

        init: function(){
            var _this = this;
        
            var chessKnightDiv = $('.chessknight');
        
            this.knightDesk = chessKnightDiv.find('.chessboard');
            this.ctrlDiv = chessKnightDiv.find('.ctrl');
        
            this.ansBlock = chessKnightDiv.children('.answers');
            this.ansSelect = this.ansBlock.find('select');
        
            this.boardMngr = new ChessBoardManager(this.knightDesk);
        
            this.cells = this.boardMngr.getCells();
        
            this.controller = new ButtonsController(
                chessKnightDiv.find('.centered_block.controls>button'), {
                    click: this.onButtonClick,
                    ctxt: this
                });

            this.ansPlayA = this.ansBlock.find('a');
        
            this.ansPlayA.click(function(event) {
                event.preventDefault();
                _this.execute(function(){
                    _this.onSolution();
                }, !_this.isLastAnsSelected());
            });

            this.bindInit();
        },
    
        bindInit: function() {
            this.clearState();
            this.cells.css('cursor', 'pointer').bind('click', function(event){
                chessKnightManager.cellClicked($(event.target));
            });
        },

        clearState: function(){
            this.hod = {};
            this.hodOrd = [];
            this.currentCell = null;
            this.cells.empty().unbind('click').css('cursor', 'default');
            this.resetControls();
        },

        onButtonClick: function(action) {
            switch (action) {
                case 'back':
                    this.execute(function(){
                        this.onBack();
                    }, false);
                    break;
                case 'roll':
                    this.execute(function(){
                        this.onNew();
                    }, true);
                    break;
                case 'replay':
                    this.onReplay();
                    break;
                case 'stop':
                    this.onStop();
                    break;
                case 'rewind':
                    this.execute(function(){}, true);
                    break;
            }
        },
    
        knight: function(){
            return $('<span />').addClass('wn');
        },
    
        wasMarker: function(){
            return $('<img>').attr('src', '/resources/images/icons/chess/flag.png');
        },
    
        findCell : function(x, y){
            return this.boardMngr.getCell(x, y);
        },
    
        cellClicked: function($cell, emulate){
            var x = $cell.data('x');
            var y = $cell.data('y');
        
            this.cells.css('cursor', 'default').unbind('click').filter(':contains(x)').empty();
        
            var allowed = this.getAllowed(x, y);

            $.each(allowed, function(idx, arr){
                var x = arr[0];
                var y = arr[1];
            
                var $allowedCell = chessKnightManager.findCell(x, y);
            
                if($allowedCell){
                    $allowedCell.html('x');
                
                    if(!emulate){
                        $allowedCell.css('cursor', 'pointer').bind('click', function(event){
                            chessKnightManager.cellClicked($(event.target));
                        });
                    }
                }
            });
        
            if (this.currentCell) {
                this.currentCell.empty().append(this.wasMarker());
            }
        
            $cell.empty().append(this.knight());
        
            this.currentCell = $cell;
        
            this.updateInfo(64 - this.hodOrd.length, allowed.length <= 0);
        },
    
        getAllowed: function(x, y){
            this.hod['c'+x+'x'+y] = true;
            this.hodOrd.push([x, y]);

            var allowed = [
            [x-1, y-2],
            [x-1, y+2],
            [x-2, y-1],
            [x-2, y+1],
            [x+1, y-2],
            [x+1, y+2],
            [x+2, y-1],
            [x+2, y+1]
            ];
        
            var allHodes = [];
        
            $.each(allowed, function(idx, arr){
                var x = arr[0];
                var y = arr[1];
            
                if(x>0 && x<9 && y>0 && y<9 && !chessKnightManager.hod['c'+x+'x'+y]){
                    allHodes.push([x,y]);
                }
            });
        
            return allHodes;
        },
    
        updateInfo: function(leftCount, noMovies) {
            var finished = leftCount==0;
            noMovies = !finished && leftCount<64 && noMovies;

            var infoMsg = this.ctrlDiv.find('.info_msg');
        
            infoMsg.empty().removeClass('done no_move info');
        
            if(finished){
                infoMsg.addClass('done').html('Ура, победа!!! Вам это удалось!! Поздравляю!:)');
                this.onSuccess();
            }else if (noMovies){
                infoMsg.addClass('no_move').html('Ходов больше нет, непокрыто клеток: ' + leftCount);
            }
            else{
                infoMsg.addClass('info').html('Осталось пройти клеток: <span>'+leftCount+'</span>');
            }
        
            this.updateButtonsState();
        },
    
        updateButtonsState: function() {
            var isHodesDone = this.hodOrd.length > 0;
            var is2moreHodesDone = this.hodOrd.length > 1;
            var isNowReplay = this.replayModeOn;

            this.controller.recalcState({
                back: {
                    colored: isHodesDone && !isNowReplay,
                    enabled: isHodesDone && !isNowReplay
                },
            
                roll: {
                    colored: isHodesDone,
                    enabled: isHodesDone
                },
                
                replay: {
                    visible: !isNowReplay,
                    colored: is2moreHodesDone,
                    enabled: is2moreHodesDone
                },
            
                rewind: {
                    visible: isNowReplay,
                    colored: 1,
                    enabled: 1
                }
            //stop: isNowReplay ? true : null,
            });
        },
    
        onSuccess: function() {
            if(this.hodOrd.length == 64) {
                var res = PsArrays.joinExpanded(this.hodOrd);
            
                if(!this.hasAns(res)) {
                    AjaxExecutor.execute('ChessKnightAns', {
                        hodes: res
                    });
                    this.regAns(res);
                }
            }
        },
    
        resetControls: function(){
            this.updateInfo(64, false);
        },
    
        /*
     * Новая игра
     */
        hasDeferred: false,
        deferredF: null,
    
        execute: function(callback, deferred){
            if(this.replayModeOn){
                if(deferred){
                    /* Команда должна быть выполнена после проигрывания */
                    this.hasDeferred = true;
                    this.deferredF = callback;
                }
                else{
                /* Команда должна быть выполнена сразу, либо уже не обрабатываться */
                }
                this.replay(null);
            }
            else{
                callback.call(this);
            }
        },
    
        executeDeferred: function(){
            if(this.hasDeferred && this.deferredF && $.isFunction(this.deferredF)){
                this.hasDeferred = false;
                this.deferredF.call(this);
                this.deferredF = null;
            }
        },
    
        /*
     * Новая игра
     */
        onNew: function(){
            this.bindInit();
        },    
    
        /*
     * Шаг назад
     */
        onBack: function(){
            var current = this.hodOrd.pop();
            var last = current ? this.hodOrd.pop() : null;
        
            /*
         * Установим предыдущий ход
         */
            if(last){
                this.hod['c'+current[0]+'x'+current[1]] = false;
                this.currentCell = null;
            
                var $lastCell = (this.findCell(last[0], last[1]));
                this.cellClicked($lastCell);
            }
            else{
                this.bindInit();
            }
        },
    
        onStop: function() {
            this.replayStopped = true;
        },
    
        /*
     * Повторение ходов пользователя
     */
        onReplay: function(){
            this.replay(this.hodOrd);
        },
    
        replay: function(moves){
            if(this.replayModeOn) {
                this.replayInterval = null;
                return;
            }

            if (moves && moves.length > 0) {
                this.replayModeOn = true;
                this.replayStopped = false;
                this.replayInterval = 400;
            
                moves = moves.clone();

                /*
             * Полностью очищаем состояние
             */
                this.clearState();

                /*
             * Выстраиваем ходы
             */
            
                this.replayImpl(moves.reverse());
            }
            else{
                this.executeDeferred();
            }
        },
    
        replayImpl: function(moves) {
            var move = this.replayStopped ? null : moves.pop();
            if(move) {
                this.cellClicked(this.findCell(move[0], move[1]), moves.length > 0);
                if(this.replayInterval){
                    setTimeout(function(){
                        chessKnightManager.replayImpl(moves);
                    }, this.replayInterval);
                }
                else{
                    this.replayImpl(moves);
                }
            }
            else{
                this.replayModeOn = false;
                this.executeDeferred();
            
                this.updateButtonsState();
            }
        },
    
        /*
     * Демонстрация решения.
     */
        curAns: null,
    
        getSelectedAns: function(){
            return this.ansSelect.find(':selected').val();
        },
    
        hasAns: function(val) {
            return this.ansSelect.hasChild('option[value="'+val+'"]');
        },
    
        regAns: function(res){
            var optCnt = this.ansSelect.find('option').size();
            var option = $('<option>').val(res).html('Решение ' + (optCnt + 1));
            this.ansSelect.append(option);
        },
    
        onSolution: function(){
            this.curAns = this.getSelectedAns();
            var ansArr = PsArrays.string2arrays(this.curAns, 2);
            this.replay(ansArr);
        },
    
        isLastAnsSelected: function(){
            return this.curAns!=null && this.curAns==this.getSelectedAns();
        }
    }


    chessKnightManager.init();
});
