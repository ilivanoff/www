/*
 *
 *
 *  --== СВЯЗУЮЩИЙ МЕНЕДЖЕР ==--
 *
 *
 */
$(function(){
    
    var $GYM = $('.gym_tool');
    
    var gymController = {
        currentState: null,
        selectedExes: [],
    
        states: {
            exercises: 'gym_exercises_block',
            progConstr: 'gym_programm_block',
            programms: 'gym_programms_block'
        },
    
        init: function() {
            this.divs = $GYM.children('div').not('.centered_buttons');
            
            this.btns = $GYM.children('.centered_buttons').buttonset().disableSelection().find('input').change(function(){
                gymController.setState($(this).val());
            });
            
            gymAnatomyManager.init();
            gymProgrammConstructor.init();
            gymProgrammsManager.init();

            this.setState(this.states.exercises);
        },
    
        setState: function(newState){
            if(this.currentState==newState){
                return;//---
            }
            
            this.btns.filter('[value='+newState+']').setChecked(true).button("refresh");

            this.divs.hide().filter('.'+newState).show();
        
            if(this.currentState){
                switch (this.currentState) {
                    case this.states.exercises:
                        this.selectedExes = gymAnatomyManager.getSelectedExes();
                        break;
                    case this.states.progConstr:
                        this.selectedExes = gymProgrammConstructor.getSelectedExes();
                        break;
                }
            }
            //todo добавить проверку в менеджерах - если текущее и предыдуще состояния совпадают, то не обновлять состояния
            switch (newState) {
                case this.states.exercises:
                    gymAnatomyManager.markAddedExes(this.selectedExes);
                    break;
                case this.states.progConstr:
                    gymProgrammConstructor.markAddedExes(this.selectedExes);
                    gymProgrammConstructor.onShow();
                    break;
            }

            this.currentState = newState;
        },
    
        /*
     * Поступила команда начать редактировать программу
     */
        startEditProgramm: function(programm, prevState){
            this.selectedExes = gymTools.getProgrammExesIds(programm);
            gymProgrammConstructor.loadProgramm(programm, prevState);
            this.setState(this.states.progConstr);
            gymTools.jumpToGymHeader();
        },
    
        /*
     * Поступила команда удалить программу
     */
        afterProgrammDeleted: function(programm){
            gymProgrammConstructor.clearProgrammIf(programm.id);
            this.selectedExes = gymProgrammConstructor.getSelectedExes();
        },
    
        afterProgrammSaved: function(programm, prevState){
            this.setState(this.states.programms);
            gymProgrammsManager.programmUpdated(programm, prevState);
        },
    
        afterProgrammCancelled: function(programm, prevState){
            this.setState(this.states.programms);
            gymProgrammsManager.programmUpdated(programm, prevState);
        },
    
        /*
     * Показывает информацию об упражнении (чаще - как help)
     */
        showExerciseInfo: function(exId){
            this.setState(this.states.exercises);
            gymAnatomyManager.showExerciseInfo(exId);
        }
    }

    /*
    var programm = {
        id: код программы,
        name: название программы,
        comment: комментарий ко всей программе,
        datas: [
            {
                id: код упражнения (заполнен только для комбо),
                name: название упражнения,
                comments: комментарий к упражнению,
                sets: [подход1, подход2]
            }
        ]
    }
 */

    /*
$(function(){
    var prog1={
        id: 3,
        name: 'xxx',
        comment: '',
        datas: [{
            id: 1,
            name: 'tr1',
            sets: [1,2,3]
        }]
    };
    var prog2={
        name: 'xxx',
        comment: '',
        datas: [{
            id: 1,
            name: 'tr1',
            sets: [1,2,3]
        }]
    };
    
    alert(gymTools.isEqualProgramms(prog1, prog2));
    
});
 */

    var gymTools = {
        jumpToGymHeader: function(){
            jumpToElement('gym_tool');
        },
    
        /*
     * Возвращает текстовое id программы
     */
        progId: function(programm){
            return 'gymprog_'+(PsIs.object(programm) ? programm.id : programm)
        },
    
        /*
     * Возвращает текстовое id упражнения
     */
        exId: function(ex_id){
            return 'gymex_'+ex_id;
        },
    
        exCoverSrc: function(exId){
            return '/resources/images/GymExercises/'+exId+'/cover.jpg';
        },
    
        /*
     * Программа считается пустой, если в ней нет упражнений
     */
        isEmptyProgramm: function(programm){
            return !(programm!=null && $.isArray(programm.datas) && programm.datas.length>0);
        },
    
        isEqualProgramms: function(prog1, prog2){
            if(prog1==null || prog2==null){
                return prog1!=null && prog2!=null;
            }

            if(prog1.id!=prog2.id){
                return false;
            }

            if(prog1.name!=prog2.name){
                return false;
            }
        
            if(prog1.comment!=prog2.comment){
                return false;
            }
        
            if(!$.isArray(prog1.datas) || !$.isArray(prog2.datas)){
                return ($.isArray(prog1.datas) && $.isArray(prog2.datas)) ||
                (!$.isArray(prog1.datas) && !$.isArray(prog2.datas));
            }
        
            if(prog1.datas.length != prog2.datas.length){
                return false;
            }
        
            for (var i = 0; i < prog1.datas.length; i++) {
                var ex1 = prog1.datas[i];
                var ex2 = prog2.datas[i];
            
                if(ex1.id != ex2.id){
                    return false;
                }
                if(ex1.name != ex2.name){
                    return false;
                }
                if(ex1.comment != ex2.comment){
                    return false;
                }
                if(!PsArrays.equals(ex1.sets, ex2.sets)){
                    return false;
                }
            }
        
            return true;
        },
    
        /*Возвращает массив айдишников упражнений в прогамме*/
        getProgrammExesIds: function(programm){
            var result = [];
            $.each(programm.datas, function(i, ex){
                if(ex.id){
                    result.push(ex.id);
                }
            });
            return result;
        },

        /*
     * Дробь
     * todo - вынести в базовые функции
     */
        fraction: function(numerator, denominator){
            return $('<table>').append(
                $('<tbody>').append(
                    $('<tr>').append(
                        $('<td>').addClass('top').html(numerator)
                        )
                    ).append(
                    $('<tr>').append(
                        $('<td>').html(denominator)
                        )
                    )
                );
        },
    
        /*
     * Возвращает представление сета - как строку или как таблицу.
     * Таблица вернётся только для текста вида [числитель/знаменатель] в мозиле или опере.
     */
        getSetView: function(text){
            if(isEmpty(text)){
                return null;
            }
        
            var i1 = text.indexOf('/');
            var i2 = text.lastIndexOf('/');
        
            if(($.browser.opera || $.browser.mozilla)  && i1>0 && i1==i2 && (i1+1)<text.length){
                i1 = text.substr(0, i1);
                i2 = text.substr(i2+1, text.length);
            
                return this.fraction(i1, i2);
            }
            else{
                return $('<span>').addClass('set').html(text);
            }
        },
    
        /*
    var programm = {
        id: код программы,
        name: название программы,
        comment: комментарий ко всей программе,
        datas: [
            {
                id: код упражнения (заполнен только для комбо),
                name: название упражнения,
                comments: комментарий к упражнению,
                sets: [подход1, подход2]
            }
        ]
    }
     */
   
   
        /*
     * Функция принимает на вход тренировочную программу, а также первичные параметры 
     * построения этой программы.
     * Последним параметром принимается функция, которая будет вызвана при изменении этих параметров.
     * Снаружи можно будет на это изменение соотетствующим образом отреагировать или сохранить 
     * текущее состояние в переменную.
     */
        buildProgrammPreview: function(programm, initialState, callback){
            initialState = jQuery.extend({
                showBlock: false
            }, initialState);

            var mngr = this;
            var gymProgDiv = $('<div>').addClass('gym_programm');

            var stateChanged = function(newState){
                gymProgDiv.data('state', newState)
                if($.isFunction(callback)){
                    callback.call(newState, newState);
                }
            };
        
            var name = $('<h3>').html(programm.name ? programm.name : 'Тренировочная программа');

            var exercisesOl = null;
        
            var hasSelectedEx = false;// Признак - есть ли упражнения, выбранные как select
        
            $.each(programm.datas, function(i, exercise){
                /*Название*/
                if(!exercise.name){
                    return;
                }
            
                if(!exercisesOl){
                    exercisesOl = $('<ol>').addClass('user_exercises');
                }
            
                /*Сеты*/
                var sets = null;
                $.each(exercise.sets, function(i, setValue){
                    sets = sets ? sets : $('<div>').addClass('sets');
                    sets.append(mngr.getSetView(setValue));
                });

                /*Примечание*/
                var commentP = exercise.comment ? $('<p>').addClass('comment').html(exercise.comment) : null;
    
                var popupImgPrev = null;
                var popupImgBlock = null;

                if(exercise.id) {
                    hasSelectedEx = true;
                    /*Картинка*/
                    var coverSrc = gymTools.exCoverSrc(exercise.id);
                
                    popupImgPrev = popupImg(coverSrc);
                    popupImgBlock = crIMG(coverSrc).addClass('block');
                
                    popupImgPrev.click(function(event){
                        event.preventDefault();
                        gymController.showExerciseInfo(exercise.id);
                    });
                
                    popupImgBlock.click(function(event){
                        event.preventDefault();
                        gymController.showExerciseInfo(exercise.id);
                    });
                }

                var newLi = $('<li>').
                append(popupImgPrev).
                append(exercise.name).
                append(popupImgBlock).
                append(sets).
                append(commentP);
        
                exercisesOl.append(newLi);
            });
        
            var exViewControl = null;
            // gymController.showExerciseInfo(exercise.id);

            if(hasSelectedEx) {
                var $aSetView = crA().addClass('view').html('Просмотр');
            
                var setPrevState = function(showBlock){
                    if(showBlock){
                        gymProgDiv.addClass('show_block');
                        $aSetView.removeClass('view').addClass('user');
                    }
                    else{
                        gymProgDiv.removeClass('show_block');
                        $aSetView.removeClass('user').addClass('view');
                    }

                    initialState.showBlock = showBlock;
                    stateChanged(initialState);
                };
            

                $aSetView.click(function(event){
                    event.preventDefault();
                    setPrevState(!gymProgDiv.hasClass('show_block'));
                });

                exViewControl = $('<div/>').addClass('view_control').addClass('pscontrols').append($aSetView);

                setPrevState(initialState.showBlock);
            }
        
            var contentDiv = $('<div/>').addClass('content').
            append(exViewControl).
            append(exercisesOl);
        
            return gymProgDiv.append(name).append(contentDiv);
        }
    }



    /*
 *
 *
 *  --== УПРАЖНЕНИЯ ==--
 *
 *
 */

    var gymAnatomyManager = {
        group: null,
    
        init: function(){
            var _this = this;
        
            this.exsTbl = $GYM.find('table.exercises');
            this.exsTblTrs = this.exsTbl.find('tr:has(a.gym_ex)');

            this.exsCov = $('div.exercises_covers');
            this.exsCovA = this.exsCov.find('a');
        
            this.exes = $GYM.find('.exercise');

            this.exsTblTrs.find('a.gym_ex').click(function(event){
                event.preventDefault();
                
                var id = getHrefAnchor(this);
                _this.showExercise(id);
            });
        
            this.exsTblTrs.find('a.add').click(function(event){
                event.preventDefault();
            
                var exId = getHrefAnchor($(this).siblings('a.gym_ex'));
                _this.markAdded(exId);
            });
        
            this.exsTblTrs.find('a.del').click(function(event){
                event.preventDefault();

                var exId = getHrefAnchor($(this).siblings('a.gym_ex'));
                _this.unMarkAdded(exId);
            });
        
            this.exsTblTrs.find('a.gym_gr').click(function(event){
                event.preventDefault();
            
                var id = getHrefAnchor(this);
                grSelect.val(id).change();
            });
        
            this.exsCovA.click(function(event){
                event.preventDefault();
            
                _this.exsCovA.removeClass('cur');
                var $a = $(this).addClass('cur');
            
                var exId = getHrefAnchor($a);
                _this.exes.hide().filter('#' + gymTools.exId(exId)).show();
            });
        
                
            /*
         * Кнопки добавления/удаления упражнения из самого упражнения
         */
            this.exes.find('.exercise_ctrl a.add').click(function(event){
                event.preventDefault();
                var exId = elId($(this).parents('div.exercise:first'));
                _this.markAdded(exId);
            });
        
            this.exes.find('.exercise_ctrl a.del').click(function(event){
                event.preventDefault();
                var exId = elId($(this).parents('div.exercise:first'));
                _this.unMarkAdded(exId);
            });
        
            /*
         * УПРАВЛЕНИЕ
         */
            this.exesControl = $('.exercises_control');
        
            /*
         * Группа мышц
         */
            var grSelect = this.exesControl.find('.group .col2 select');

            var aGrClose = this.exesControl.find('.group .col2 a').click(function(event){
                event.preventDefault();
                grSelect.val('').change();
            });
        
            $('.anatomy a').click(function(event){
                event.preventDefault();
            
                var $a = $(this);
                var grId = getHrefAnchor($a);
                grSelect.val(grId).change();
            });
        
            grSelect.change(function(){
                var value = grSelect.val();
                var empty = isEmpty(value);
                aGrClose.setVisibleInline(!empty);
                _this.showGroup(value);
            }).change();
        
            /*
         * Мозайка/таблица
         */
            var vwTr = this.exesControl.find('.view .col2');
        
            var tableView = $('<a href="#">Таблица</a>').addClass('table').click(function(event){
                event.preventDefault();
                tableView.hide();
                mosView.setVisibleInline(true);
                _this.setMode('mosaic');
            });
        
            var mosView = $('<a href="#">Мозайка</a>').addClass('mosaic').click(function(event){
                event.preventDefault();
                tableView.setVisibleInline(true);
                mosView.hide();
                _this.setMode('table');
            });
        
            vwTr.append(tableView).append(mosView);
            mosView.click();
        
            /*
         * Показать/скрыть упражнения
         */
            $GYM.find('.show_hide_exercises>button').first().button({
                icons: {
                    primary: 'ui-icon-arrowthick-1-n'
                }
            }).click(function() {
                _this.doHideAll();
            }).next().button({
                icons: {
                    primary: 'ui-icon-arrowthick-1-s'
                }
            }).click(function() {
                _this.doShowAll();
            });
        },
    
        showExercise: function(exId){
            this.exsTblTrs.removeClass('cur').filter(':has(a.gym_ex[href="#'+exId+'"])').addClass('cur');
            this.exes.hide().filter('#'+gymTools.exId(exId)).show();
        },
    
        /*
     * Сбразывает состояние таблицы и просмотра коверов,
     * т.е. удаляет отметку о текущем выделенном посте и прячет тела всех упражнений.
     */
        clearState: function(){
            this.exsTblTrs.removeClass('cur').show();
            this.exsCovA.removeClass('cur').show();
            this.exes.hide();
        },
    
        /*
     * Вид
     */
        setMode: function(mode){
            this.mode = mode;
            switch (this.mode) {
                case 'table':
                    this.exsTbl.show();
                    this.exsCov.hide();
                    break;
                case 'mosaic':
                    this.exsTbl.hide();
                    this.exsCov.show();
                    break;
            }
            this.showGroup(this.group);
        },
    
        /*
     * Показывает упражнения на группу мышц
     */
    
        showGroup: function(group){
            this.clearState();
            this.group = group;
            if(group){
                switch (this.mode) {
                    case 'table':
                        this.exsTblTrs.hide().filter('.g'+group).show();
                        break;
                    case 'mosaic':
                        this.exsCovA.hide().filter('.g'+group).show();
                        break;
                }
            }
        },
    
        doShowAll: function(){
            this.showGroup(this.group);
            if(this.group){
                this.exes.filter('.g'+this.group).show();
            }
            else{
                this.exes.show();
            }
        },
    
        doHideAll: function(){
            this.showGroup(this.group);
        },
    
    
        /*
     * Методы для вызова другими менеджерами
     */
    
        selected: [],
    
        getSelectedExes: function(){
            return this.selected;
        },
    

        markAddedExes: function(selectedExes){
            if(this.selected.equals(selectedExes)){
                return;//---
            }
        
            this.selected = [];
            this.exes.removeClass('added');
            this.exsTblTrs.removeClass('added');
        
            $.each(selectedExes, function(idx, value){
                gymAnatomyManager.markAdded(value);
            });
        },
    
        markAdded: function(exId){
            this.selected.push(exId);

            this.exes.filter('#'+gymTools.exId(exId)).addClass('added');
            this.exsTblTrs.filter(':has(a.gym_ex[href$="#'+exId+'"])').addClass('added');
        },
    
        unMarkAdded: function(exId){
            this.selected.removeValue(exId);
            this.exes.filter('#'+gymTools.exId(exId)).removeClass('added');
            this.exsTblTrs.filter(':has(a.gym_ex[href$="#'+exId+'"])').removeClass('added');
        },
    
        showExerciseInfo: function(exId){
            this.showExercise(exId);
            jumpToElement(gymTools.exId(exId));
        }
    }


    /*
 *
 *
 *  --== КОНСТРУКТОР ПРОГРАММ ==--
 *
 *
 */

    //todo сделать кнопку "Отменить" для загруженных упражнений
    var gymProgrammConstructor = {
        programm: null, // Программа, переданная извне на редактирование
        prevState: null, // Состояние блока предватирельного просмотра

        init: function(){
            var mngr = this;
        
            this.progDiv = $GYM.find('.user_training_programm');
        
            this.select = $GYM.find('select.gym_exercises:first').remove();
        
            this.progOl = this.progDiv.find('ol.user_exercises');
        
            this.prevDiv = $('#gymex_prev').hide();
        
            this.progName = this.progDiv.find('h2.name input:text').keyup(function(){
                mngr.onChange();
            });
        
            /*
            this.gym.find('.prew_ctrl .prev').click(function(){
                //Событие не топим, т.к. после обновления перейдём на просмотр
                mngr.buildPreview();
            });
             */
       
            var ctrlButtons = $GYM.find('.prog_ctrl>button');

            this.btnSave = ctrlButtons.first().button().click(function(){
                mngr.saveProgramm(false);
            });
        
            this.btnClone = this.btnSave.next().button().click(function(){
                mngr.saveProgramm(true);
            });
        
            this.btnCancel = this.btnClone.next().button().click(function(){
                mngr.cancel();
            });
            
            this.infoMngr = new infoBlockController($GYM.find('.info'));
        
            this.saveModel = new PsUpdateModel(this, 
                function(){
                    this.btnSave.button('disable');
                    this.btnClone.button('disable');
                }, function() {
                    this.btnSave.button('enable');
                    this.btnClone.button('enable');
                });
        
            new ButtonsController(this.progDiv.find('h2.name button'), {
                click: function(){
                    mngr.initState();
                }
            });

            this.initState();
        },
    
        initState: function(){
            this.programm = null;
            this.clearInfo();
            this.clearPreview();
            this.recalcCtrlButtonsVisibility();
        
            this.progOl.empty().append(this.newLi());
            this.progName.val('').focus();
        },
    
        recalcCtrlButtonsVisibility: function(){
            this.btnClone.setVisibleInline(this.programm!=null);
            this.btnCancel.setVisibleInline(this.programm!=null);
        },
    
        /*
    var data = {};
    data.id = null;
    data.name = null;
    data.sets = [];
    data.comment = null;
     */

        newLi: function(ex){
            var mngr = this;
            //todo Добавить клонирование сета
            /*
         <li>
            <input type='text' class='manual'/> <a class='ctrl change'>&hArr;</a> <a class='ctrl add'>+</a> <a class='ctrl del'>&minus;</a>
            <div class='sets'>
                <div class='set'><input type='text' /> <a class='ctrl add'>+</a> <a class='ctrl del'>&minus;</a></div>
            </div>
            <p class='comment'>
                Примечание:
            </p>
            <textarea></textarea>
        </li>
         */
            var newExLi = $('<li>');

            var input = $('<input>').attr('type', 'text').addClass('manual').
            keyup(function(){
                mngr.onChange();
            }).hide();
        
            var select = this.select.clone().change(function(){
                mngr.setFocus(newExLi);
                mngr.onChange();
            }).keyup(function(){
                mngr.onChange();
            }).show();
        
            if(ex){
                if(ex.id){
                    select.val(ex.id);
                }
                else{
                    input.val(ex.name).setVisibleInline(true);
                    select.hide()
                }
            }
        
            var a_change = $('<a>').attr('href', '#').addClass('ctrl').addClass('change').html('&hArr;');
            var a_add_ex = $('<a>').attr('href', '#').addClass('ctrl').addClass('add').html('+');
            var a_del_ex = $('<a>').attr('href', '#').addClass('ctrl').addClass('del').html('&minus;');
            var a_up_ex = $('<a>').attr('href', '#').addClass('ctrl').addClass('up').html('&uarr;');
            var a_down_ex = $('<a>').attr('href', '#').addClass('ctrl').addClass('down').html('&darr;');
        
               
        
            var sets = $('<div>').addClass('sets');
        
            if(ex && ex.sets.length>0){
                $.each(ex.sets, function(i, set){
                    sets.append(mngr.newSet(set));
                });
            }
            else{
                sets.append(this.newSet());
            }
        
            var comment = $('<p>').addClass('comment').html('Примечание:');

            var textarea = $('<textarea>').keyup(function(){
                mngr.onChange();
            }).val(ex && ex.comment ? ex.comment : '');
        
            /*
         * Вешаем слушатели
         */
        
            a_change.click(function(event){
                event.preventDefault();
                input.toggleVisibilityInline();
                select.toggleVisibilityInline();
                mngr.setFocus(newExLi);

                mngr.onChange();
            });
        
            a_add_ex.click(function(event){
                event.preventDefault();
            
                var tmpLi = mngr.newLi();
                tmpLi.insertAfter(newExLi);
                mngr.setFocus(tmpLi);

                mngr.onChange();
            });
        
            a_del_ex.click(function(event){
                event.preventDefault();
            
                newExLi.remove();
            
                if(mngr.progOl.find('li').isEmptySet()){
                    mngr.progOl.append(mngr.newLi());
                }
                mngr.onChange();
            });
        
            a_up_ex.click(function(event){
                event.preventDefault();
            
                var prev = newExLi.prev();
                if(!prev.isEmptySet()){
                    newExLi.insertBefore(prev);
                }
                mngr.onChange();
            });

            a_down_ex.click(function(event){
                event.preventDefault();
            
                var next = newExLi.next();
                if(!next.isEmptySet()){
                    newExLi.insertAfter(next);
                }
                mngr.onChange();
            });
        
            var exCtrls = $('<span>').addClass('ex_ctrls').
            append(a_change).
            append(a_add_ex).
            append(a_del_ex).
            append(a_up_ex).
            append(a_down_ex);
        
            return newExLi.
            append(input).append(select).append(exCtrls).
            append(sets).append(comment).append(textarea);
        
        },
    
        /*Метод вызывается при изменении программы*/
        onChange: function(){
            this.buildPreview();
        },
    
        getVisibleInput: function($li){
            var select = $li.find('select');

            if(select.css('display')!='none'){
                return select;
            }
            else{
                return $li.find('input:text.manual');
            }
        },
    
        setFocus: function($li){
            /*В один момент времени виден только один элемент ввода*/
            var input = this.getVisibleInput($li);
            if(isEmpty(input.val())){
                input.find('option:first').attr('selected', 'selected').end().focus();
            }
            else{
                $li.find("div.set input[value='']:first").focus();
            }
        },
    
        /*
        <div class='set'><input type='text' /> <a class='ctrl add'>+</a> <a class='ctrl del'>&minus;</a></div>
     */
        newSet: function(setVal){
            var mngr = gymProgrammConstructor;
        
            var newSet = $('<div>').addClass('set');

            var input = $('<input>').attr('type', 'text').val(setVal ? setVal : '').
            keyup(function(){
                mngr.onChange();
            });
            var a_add = $('<a>').attr('href', '#').addClass('ctrl').addClass('add').html('+');
            var a_del = $('<a>').attr('href', '#').addClass('ctrl').addClass('del').html('&minus;');
        
        
            a_add.click(function(event){
                event.preventDefault();
            
                mngr.newSet().insertAfter(newSet).find('input').focus();
            
                mngr.onChange();
            });
 
            a_del.click(function(event){
                event.preventDefault();
            
                var sets = $(this).parents('.sets:first');
                newSet.remove();
                if(!sets.hasChild('.set')){
                    mngr.newSet().appendTo(sets);
                }
            
                sets.find('.set:has(input:empty):first').find('input').focus();
            
                mngr.onChange();
            });
        
            newSet.append(input).append(a_add).append(a_del);
            return newSet;
        },
    
        /*
        * Собирает данные из формы в объект
        */
        getProgramm: function(){
            var mngr = this;
        
            var nameValue = this.progName.val();
            nameValue = nameValue ? nameValue : null;
        
            var progId = this.programm ? this.programm.id : null;
        
            var trainingData = {
                id: progId,
                name: nameValue,
                comment: null,
                datas: []
            }
        
            this.progOl.find('li').each(function(){
                var data = {};
                data.id = null;
                data.name = null;
                data.sets = [];
                data.comment = null;

                var li = $(this);

                var input = mngr.getVisibleInput(li);
                var exName = input.val();
                if(isEmpty(exName)){
                    return;//---
                }
            
                if(input.is('input')){
                    data.name = exName;
                }
                else{
                    data.id = exName;
                    data.name = input.find(':selected').html();
                }
            
                li.find('.sets input').each(function(){
                    var setVal = $(this).val();
                    if(isEmpty(setVal)){
                        return;
                    }
                    data.sets.push(setVal);
                });
            
                var comment = li.find('textarea').val();
            
                if(!isEmpty(comment)){
                    data.comment = comment;
                }
            
                trainingData.datas.push(data);
            });

            return trainingData;
        },
    
        clearPreview: function(){
            this.prevDiv.empty().hide();
        },
    
        buildPreview: function(){
            this.clearPreview();
        
            var programm = this.getProgramm();
        
            if(!programm.name && gymTools.isEmptyProgramm(programm)){
                return;
            }
        
            var mngr = this;
            var prevDiv = gymTools.buildProgrammPreview(programm, this.prevState, function(data){
                mngr.prevState = data;
            });
            this.prevDiv.append(prevDiv).show();
        
            updateFormules();
        },
    
        cancel: function(){
            var programm = this.programm;
            this.initState();
            gymController.afterProgrammCancelled(programm, this.prevState);
        },
    
        saveProgramm: function(clone){
            var mngr = this;
        
            var editProgramm = this.getProgramm();
        
            if(gymTools.isEmptyProgramm(editProgramm)){
                this.showError('Программа не содержит ни одного упражнения');
                return;//---
            }
        
            editProgramm.id = clone ? null : editProgramm.id;
        
            var changed = !gymTools.isEqualProgramms(this.programm, editProgramm);
        
            if(changed){
                var smodel = this.saveModel;
                if (smodel.isStarted()){
                    return;
                }
                smodel.start(clone ? 'clone' : 'save');

                $.post('ajax/GymSaveProgramm.php', {
                    programm: editProgramm
                }, function(response){
                    smodel.stop();
                
                    var result = ajax_success(response);
                    if(result){
                        editProgramm.id = result;
                        mngr.afterProgramSaved(editProgramm);
                    }
                    else{
                        var error = ajax_error(response);
                        mngr.showError(error);
                    }
                });
            }
            else{
                mngr.afterProgramSaved(editProgramm);
            }
        },
    
        afterProgramSaved: function(programm){
            this.initState();
            gymController.afterProgrammSaved(programm, this.prevState);
        },
    
        /*
     * Отображает информацию
     */
    
        showError: function(error){
            this.infoMngr.errorAH(error);
        },

        clearInfo: function(){
            this.infoMngr.clear();
        },
    
        /*
     *
     * -------------------------------------
     * Методы для вызова другими менеджерами
     * -------------------------------------
     * 
     */
    
        onShow: function(){
            this.buildPreview();
            if(isEmpty(this.progName.val())){
                this.progName.focus();
            }
        },
    
        getSelectedExes: function(){
            var data = [];
            this.progOl.find('li').each(function(){
                var select = gymProgrammConstructor.getVisibleInput($(this));
                if(select.is('select') && select.val()){
                    data.push(select.val());
                }
            });
            return data;
        },
    

        markAddedExes: function(mustBeState){
            var currentState = this.getSelectedExes();
            if (currentState.equals(mustBeState)){
                return;//---
            }

            var mngr = this;
            var diff = PsArrays.getDiff(mustBeState, currentState);
        
            var mustAdd = diff.a1has;
            var mustDel = diff.a2has;
        
            $.each(mustAdd, function(i, value){
                mngr.addExercise(value);
            });
        
            $.each(mustDel, function(i, value){
                mngr.delExercise(value);
            });
        },

        addExercise: function(id){
            /*
         * Попробуем добавить к селекту, который есть но без значения.
         */
            var added = false;
            this.progOl.find('li').each(function(){
                if(!added){
                    var select = gymProgrammConstructor.getVisibleInput($(this));
                    if(select.is('select') && !select.val()){
                        select.val(id);
                        added = true;
                    }
                }
            });
        
            if(!added){
                var newLi = this.newLi();
                newLi.find('select').val(id);
                this.progOl.append(newLi);
            }
        },
    
        delExercise: function(id){
            var mngr = this;

            this.progOl.find('li').each(function(){
                var $li = $(this);
                var select = mngr.getVisibleInput($li);
                if(select.is('select') && select.val()==id){
                    $li.find('.ex_ctrls a.del').click();
                }
            });
        },
    
        loadProgramm: function(programm, prevState){
            var mngr = this;

            this.programm = programm;
            this.prevState = prevState;

            this.progName.val(programm.name);
            this.progOl.empty();
        
            $.each(programm.datas, function(i, ex){
                var li = mngr.newLi(ex);
                mngr.progOl.append(li);
            });

            this.clearInfo();
            this.recalcCtrlButtonsVisibility();
        },
    
        /*Прекращает редактирование программы, если она была удалена*/
        clearProgrammIf: function(progId){
            if(this.programm && this.programm.id==progId){
                this.initState();
            }
        }
    
    }


    /*
 *
 *
 *  --== МЕНЕДЖЕР ПРОГРАММ ==--
 *
 *
 */

    var gymProgrammsManager = {
        init: function(){
            this.programmsDiv = $('.gym_programms_block');
            
            this.programms = this.programmsDiv.find('.gym_programms');

            this.reload();
        },


        /*
     * Данная функция расширяет возможности стандартного {gymTools.buildProgrammPreview},
     * добавляя кнопки управления
     */
        buildGymProgramm: function(programm, prevState){
            var mngr = this;
        
            var gymProgDiv = gymTools.buildProgrammPreview(programm, prevState);
        
            if(programm.id){
                /*Если это ранее созданная программа, то установим диву id и поставим кнопки управления*/
                gymProgDiv.attr('id', gymTools.progId(programm.id));
            
                /*Кнопки управления*/
                var ctrlDiv = $('<div/>').addClass('ctrl').addClass('pscontrols');
            
                var $aEdit = $('<a/>').attr('href', '#').html('Редактировать').addClass('edit').
                //todo позже сделать подтверждаемый клик
                click(function(event) {
                    event.preventDefault();
                    mngr.editProgramm(programm, gymProgDiv.data('state'));
                });

                var $aDel = $('<a/>').attr('href', '#').html('Удалить').addClass('delete').
                confirmableClick({
                    msg: 'Подтвердите удаление программы',
                    yes: function(){
                        var loading = span_progress('Удаляем');
                        $aDel.replaceWith(loading);
                        mngr.deleteProgramm(programm, loading);
                    }
                });
        
                ctrlDiv.append($aEdit).append($aDel);
        
                gymProgDiv.find('.content').append(ctrlDiv);
            }
            return gymProgDiv;
        },
    
        /*
     * Функция вызывается для выполнения комманды редактирования программы
     */
        editProgramm: function(programm, prevState){
            gymController.startEditProgramm(programm, prevState);
        },
    
        /*
     * Функция вызывается для выполнения комманды удаления программы
     */
        deleteProgramm: function(programm, $loading){
            var _this = this;
        
            $.post('ajax/GymDelProgramm.php', {
                id: programm.id
            }, function(response){
                var result = ajax_success(response);
                if(result){
                    $('#'+gymTools.progId(programm)).remove();
                    _this.check4noProgs();
                    gymController.afterProgrammDeleted(programm);
                }
                else{
                    var error = ajax_error(response);
                    $loading.replaceWith(span_error(error));
                }
            });
        },
    
    
        /*
     * ПЕРВИЧНАЯ ЗАГРУЗКА ПРОГРАММ
     */
        reload: function(){
            $.get('ajax/GetGymProgramms.php', {
                }, function(response){
                    processAjaxResponse(response, function(result){
                        gymProgrammsManager.onDataLoaded(result);
                    }, function(error){
                        gymProgrammsManager.onLoadError(error)
                    })
                });
        },
    
        onLoadError: function(error){
            this.programms.empty().append(span_error(error));
        },
    
        onDataLoaded: function(dataArray){
            var mngr = this;
            this.programms.empty();
        
            $.each(dataArray, function(i, programm){
                var divPrev = mngr.buildGymProgramm(programm);
                mngr.programms.append(divPrev);
            });
            
            this.check4noProgs();
        },
    
        check4noProgs: function(){
            var showEmptyMsg = !this.programms.hasChild('.gym_programm');
            if(showEmptyMsg){
                this.programms.empty().append(noItemsDiv('У вас нет программ'));
            }
            else{
                this.programms.children(':not(.gym_programm)').remove();
            }
            return showEmptyMsg;
        },
    
        /*
     *
     * -------------------------------------
     * Методы для вызова другими менеджерами
     * -------------------------------------
     * 
     */
        programmUpdated: function(programm, prevState){
            var progId = gymTools.progId(programm);
            var divPrev = this.buildGymProgramm(programm, prevState);
        
            var curDiv = $('#'+progId);
            if(curDiv.isEmptySet()){
                this.programms.append(divPrev);
            }
            else{
                curDiv.replaceWith(divPrev);
            }
        
            if(!this.check4noProgs()){
                jumpToElement(progId);
            }

        }
    }

    gymController.init();
});