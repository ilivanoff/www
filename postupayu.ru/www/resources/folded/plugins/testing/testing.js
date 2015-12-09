$(function() {
    var FMANAGER = PsFoldingManager.FOLDING('pl', 'testing');
    var STORE = FMANAGER.store();

    function TestsManager(_block) {
        var _this = this;
    
        this.BLOCK = _block;
        this.BODY = _block.children('.test_body');
        this.finished = false;
        this.curTaskNum = 1;
    
        var taskTime = strToInt(this.BLOCK.data('time'));
        
        /*
         * -=ЗАДАЧИ=-
         */
    
        this.tasks = this.BODY.children('.test_tasks').children('.task_container');
        this.tasksCnt = this.tasks.size();
        this.answers = this.tasks.find('.answers').disableSelection();
        this.checkBoxes = this.answers.find(':checkbox');
    
        /*
         * -=Кнопки запуска/остановки теста=-
         */
        var $btnStart = this.BLOCK.find('.start_test button').button({
            icons: {
                primary: 'ui-icon-clock'
            }
        });
        
        var $btnStop = this.BLOCK.find('.stop_test button').button();
        
        var $btnUpdate = this.BLOCK.find('.update_test_res button').button({
            icons: {
                primary: 'ui-icon-disk'
            }
        });
    
        /*
         * Результат теста
         */
        this.testResults = this.BLOCK.children('.test_results');
    
        /*
         * Переключение между задачами (ячейки таблицы)
         */
        this.tasksSwitcherTable = this.BODY.children('table.test_nums');
        this.tasksSwitcherTr = this.tasksSwitcherTable.find('tr.nums');
    
        /*
     * Чек-бокс автоматического переключения между задачами
     */
        this.autoSlide = this.BODY.find('.auto_slide');
        this.boxAutoSlide = this.autoSlide.find(':checkbox');
        this.boxAutoSlide.change(function(){
            _this.isAutoSlide = $(this).is(':checked');
            STORE.set('auto_slide', _this.isAutoSlide);
        }).enable().setChecked(STORE.get('auto_slide', false)).change();
    
        /*
     * Перечключение между задачами - ползунком
     */
        this.tasksSlider = this.BODY.children('.tasks_slider');
    
        /*
     * Подготовка, добавление слушателей и т.д.
     */
    
        this.tasksSwitcherTds = this.tasksSwitcherTr.children('td');
        this.tasksSwitcherTds.click(
            function() {
                _this.curTaskNum = strToInt($(this).html());
                _this.onTaskNumUpdate();
            });
    
        /*
        * Очистка результатов
        */
        this.testResults.find('.last .ctrl a').confirmableClick({
            msg: 'Уверены?',
            yes: function($a) {
                var resId = getHrefAnchor($a);
                var progress = span_progress('Удаляем');
                $a.replaceWith(progress);
            
                AjaxExecutor.execute('TestingClearRes', {
                    id:resId
                }, function() {
                    progress.replaceWith(span_success('Результаты сброшены'));
                    span_ah(_this.testResults.find('.last'), function() {
                        _this.testResults.find('.last, .lastfull').remove();
                    });
                }, 
                progress);
            }
        });
    
        /*
     * Переключалка задач из предыдущих результатов
     */
        this.tasksSwitcherA = this.testResults.find('.lastfull a');
    
        this.tasksSwitcherA.click(function(e){
            e.preventDefault();
            _this.curTaskNum = strToInt(getHrefAnchor(this));
            _this.onTaskNumUpdate();
        });
    
        //Спрячем ответы к задачам
        tasksManager.hideAnswerBlocks(this.tasks);
    
        //Добавим слушатели на выбор ответа
        this.checkBoxes.change(function() {
            var $box = $(this);
            var checked = $box.is(':checked');
            var $ans = $box.parents('.answers');
            $ans.find(':checkbox').each(function(){
                var $cb = $(this);
                if(!$cb.is($box)){
                    //"Отчекаем" все другие варианты
                    $cb.setChecked(false);
                }
            });
        
            _this.tasksSwitcherTds.filter('.t'+_this.curTaskNum).toggleClass('done', checked);
            var allTasksDone = _this.tasksSwitcherTds.not('.done').isEmptySet();
            $btnStop.uiButtonSetEnabled(allTasksDone);
        
            /*
         * Автоматически перелистнём на следующую задачу.
         */
            if(_this.isAutoSlide && checked && !allTasksDone){
                var i;
                var $cur;
                var nextNotDone = null;
                for (i = _this.curTaskNum; i <= _this.tasksCnt; i++) {
                    $cur = _this.tasksSwitcherTds.filter('.t'+i);
                    if($cur.is(':not(.done)')){
                        nextNotDone = $cur.html();
                        break;
                    }
                }
            
                if(!nextNotDone){
                    for (i = 1; i <= _this.curTaskNum; i++) {
                        $cur = _this.tasksSwitcherTds.filter('.t'+i);
                        if($cur.is(':not(.done)')){
                            nextNotDone = $cur.html();
                            break;
                        }
                    }
                }
            
                if(nextNotDone){
                    _this.curTaskNum = nextNotDone;
                    _this.onTaskNumUpdate();
                }
            }
        }).enable().setChecked(false);
    

        //Добавим слушатели на перелистывание задач
        var leftArr = this.tasksSlider.find('.left');
        var rightArr = this.tasksSlider.find('.right');
        var pos = this.tasksSlider.find('.pos');
    
        leftArr.click(function(e){
            e.preventDefault();
            if(_this.curTaskNum > 1){
                --_this.curTaskNum;
                _this.onTaskNumUpdate();
            }
        });
    
        rightArr.click(function(e){
            e.preventDefault();
            if(_this.curTaskNum < _this.tasksCnt){
                ++_this.curTaskNum;
                _this.onTaskNumUpdate();
            }
        })
    
    
        //Основная функция, вызываемая при обновлении номера задачи
        this.onTaskNumUpdate = function(){
            //Покажем выбранную задачу
            $(this.tasks.hide().get(this.curTaskNum-1)).show();

            //Обновление переключалки задач
            this.tasksSwitcherTds.removeClass('cur').filter('.t'+this.curTaskNum).addClass('cur');
        
            //Обновление переключалки задач предыдущего результата
            this.tasksSwitcherA.removeClass('cur').filter('.t'+this.curTaskNum).addClass('cur');
        
            //Обновление листалки задач
            pos.html(this.curTaskNum);

            var active = this.curTaskNum > 1;
            leftArr.toggleClass('whitegray', !active).css('cursor', active ? 'pointer' : 'default');
            active = this.curTaskNum < this.tasksCnt;
            rightArr.toggleClass('whitegray', !active).css('cursor', active ? 'pointer' : 'default');
        }
    
        /*
     * Начало теста
     */
        this.startTest = function() {
            $btnStart.parent('div').remove();
            this.onTaskNumUpdate();
            //"Подчистим" и спрячем информационную панель
            this.testResults.find('.nainfo, .last .ctrl').remove();
            this.testResults.hide();
            this.BODY.show();
            this.startSecundomer(taskTime*60);
        }
    
        /*
     * ЗАВЕРШЕНИЕ ТЕСТА
     */
        this.passed = [];
        this.stopTest = function() {
            this.finished = true;
        
            this.tasksSwitcherTds.removeClass('done');
        
            var totalCnt = 0;
            var validCnt = 0;
            var errorCnt = 0;
            var skippedCnt = 0;
        
            this.tasks.each(function(idx) {
                var $task = $(this);
                var $box = $task.find('.answers :checkbox:checked');
            
                var taskNum = idx+1;
                var checked = !$box.isEmptySet();
                var valid = checked ? $box.is('.correct') : false;
            
                var $td = _this.tasksSwitcherTds.filter('.t'+taskNum);
                if(checked) {
                    if(valid){
                        ++validCnt;
                        _this.passed.push(taskNum);
                        $td.addClass('valid');
                    }
                    else{
                        ++errorCnt;
                        $td.addClass('err');
                    }
                }
                else{
                    ++skippedCnt;
                }
                ++totalCnt;
            });
        
            tasksManager.showAnswerBlocks(_this.tasks);
        
            this.checkBoxes.filter('.correct').parents('.answers li').addClass('correct');
            this.checkBoxes.disable();
            this.autoSlide.remove();
        
            var percents = Math.round((validCnt/totalCnt) * 100);
        
            this.testResults.children('.new').html('Результаты теста: '+validCnt+' из '+totalCnt+' ('+percents+'%) ');
            this.testResults.show().children('.new, .lastfull').show();
            //this.BODY.append(this.testResults);
            this.tasksSlider.replaceWith(this.testResults);
            
            $btnStop.parent('div').remove();
        
            var hasPrevRes = this.testResults.hasChild('.last');
            $btnUpdate.uiButtonLabel((hasPrevRes ? 'Обновить' : 'Сохранить') + ' результаты теста');
            $btnUpdate.parent('div').show();
        }
    
        this.updateTestResults = function(testingId) {
            $btnUpdate.uiButtonDisable();

            AjaxExecutor.execute('TestingSave', {
                id: testingId,
                tasks: this.passed,
                time: this.secsPassed
            }, function(given) {
                var $btnParent = $btnUpdate.parent('div');
                $btnParent.empty().append(span_ah(span_success("Результаты теста успешно сохранены"), function() {
                    $btnParent.remove();
                }));
                if (given) {
                    PsPointsGiverManager.firePointsGiven();
                }
            }, $btnUpdate);
        }
    
        this.timerSpan = this.BODY.find('.timer>.time');
    
        this.secsPassed = -1;
        this.startSecundomer = function(sec) {
            if(this.finished) {
                return;
            }
        
            this.timerSpan.html(PsTimeHelper.formatMS(sec));
            ++this.secsPassed;

            if(sec > 0){
                setTimeout(function(){
                    _this.startSecundomer(--sec);
                }, 1000);
            }
            else {
                this.checkBoxes.disable();
                this.boxAutoSlide.disable();
                $btnStop.uiButtonEnable();
            }
        }
    
        $btnStop.uiButtonDisable().uiButtonConfirm(function(){
            _this.stopTest();
        });
        
        $btnUpdate.uiButtonConfirm(function($button) {
            _this.updateTestResults($button.data('id'));
        });

        $btnStart.click(function() {
            _this.startTest();
        });

        /*
     * FOR TESTS
     */
    
        return;
        $btnStart.click();
        return;
        tasksManager.showAnswerBlocks(_this.tasks);
        _this.tasks.show();
        tasksManager.expandAll();
    }


    $('.test').each(function(){
        new TestsManager($(this));
    });
});
