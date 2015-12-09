var trainingsManager = {
    init: function() {
        
        $('.tr .left .info .tr-anons-toggler').each(function() {
            var $hrefDiv = $(this);
            var $anonsDiv = $hrefDiv.parents('.tr').find('.right .tr-anons-info');
            var $anonsOl = $anonsDiv.find('.anons');
            if(!$anonsOl.hasChild('li')) {
                $hrefDiv.remove();
                $anonsDiv.remove();
                return;//---
            }
            
            $hrefDiv.show().children('a').clickClbck(function() {
                var active = this.toggleClass('active').hasClass('active');
                $anonsDiv.setVisible(active);
                this.html('[$ содержание]'.replace('$', active ? 'Скрыть' : 'Показать'));
            });
        });
    }
}

var trainingsStateManager = {
    passed: defs.passedLessons || {},
    
    init: function() {
        var _this = this;
        
        this.togglers = $('.tr .post_meta .toggle');
        
        this.togglers.
        each(function(){
            _this.updateLessonTriggerState(getHrefAnchor(this));
        }).
        clickClbck(function(lessonId){
            _this.toggleLessonState(lessonId);
        });
    },
    
    toggleLessonState: function(lessonId) {
        var _this = this;
        
        var triggerHrefs = this.togglers.filter('[href=#'+lessonId+']').hide();//Работаем со всеми ссылками
        var processing = span_progress('Выполняем...').insertAfter(triggerHrefs);
        var passedNow = !this.isLessonPassed(lessonId);
        
        AjaxExecutor.execute('ToggleLessonState', {
            id: lessonId
        }, function(response) {
            processing.remove();
            
            processAjaxResponse(
                response, 
                function() {
                    _this.setLessonPassed(lessonId, passedNow);
                    triggerHrefs.show();
                },
                triggerHrefs);
        });
    },
    
    isLessonPassed: function(lessonId) {
        return PsObjects.getValue(this.passed, 'tr'+lessonId, false);
    },
    
    setLessonPassed: function(lessonId, passed) {
        var lessonIdent = 'tr'+lessonId;
        this.passed[lessonIdent] = passed;
        this.updateLessonTriggerState(lessonId);
        this.rebuildMediaView();
    },
    
    updateLessonTriggerState: function(lessonId){
        var $a = this.togglers.filter('[href=#'+lessonId+']');
        var $aPostHref = $a.parents('.tr').children('a.post_head');
        if (this.isLessonPassed(lessonId)) {
            $a.addClass('passed').html('Пройден');
            $aPostHref.addClass('passed');
        }
        else 
        {
            $a.removeClass('passed').html('Не пройден');
            $aPostHref.removeClass('passed');
        }
    
    },
    
    trainingsMediaInfo: null,
    umvDiv: null,
    loadig: false,
    wasError: false,
    
    /*
    <div class="units_media_view">
        <div class="notpassed">
            <h2>Непройденные уроки:</h2>
            <div class="items">
            </div>
        </div>
        <div class="passed">
            <h2>Пройденные уроки:</h2>
            <div class="items">
            </div>
        </div>
        <div class="clearall"></div>
    </div>
    */
    
    /*
     * Графическое отображение уроков. Мы его создадим, приаттачим к дереву и 
     * потом будем следить за его "свежестью".
     */
    umv: null,
    umvDeferredUpdate: false,
    makeMediaView: function($div) {
        var $umv = $('<div>').addClass('units_media_view');
        
        var _this = this;
        
        this.notPassedDiv = $('<div>').addClass('items').droppable({
            scope: 'passed',
            hoverClass: 'ready',
            tolerance: 'pointer',
            drop: function(event, ui) {
                _this.toggleLessonState(extractPostId(ui.draggable));
            }
        });
        
        this.passedDiv = $('<div>').addClass('items').droppable({
            scope:'notpassed',
            hoverClass: 'ready',
            tolerance: 'pointer',
            drop: function(event, ui){
                _this.toggleLessonState(extractPostId(ui.draggable));
            }
        });
        
        $umv.append($('<div>').addClass('notpassed').append($('<h2>').html('Непройденные уроки:')).append(this.notPassedDiv));
        $umv.append($('<div>').addClass('passed').append($('<h2>').html('Пройденные уроки:')).append(this.passedDiv));
        $umv.append($('<div>').addClass('clearall'));
        
        this.umv = $umv.appendTo($div);
        this.rebuildMediaView();
        
        //Мы не будем перестраивать список на каждый чих, а сделаем это только тогда, когда блок станет видимым
        $('.units_media_view:visible').livequery(function() {
            if (_this.umvDeferredUpdate) {
                _this.rebuildMediaView();
            }
        });
    },
    
    rebuildMediaView: function() {
        if(!this.umv) return; //---
        if(!this.umv.isVisible()) {
            this.umvDeferredUpdate = true;
            return; //---
        }
        
        //Мы делаем обновление, поэтому можем убрать отложенное
        this.umvDeferredUpdate = false;
        
        this.passedDiv.empty();
        this.notPassedDiv.empty();
        
        var posts = PsProjectData.getPostsAsc('tr');
        
        posts.walk(function(post) {
            var passed = this.isLessonPassed(post.pid);
            var parentDiv = passed ? this.passedDiv : this.notPassedDiv;
            
            var $a = post.a().addClass('umv').empty();
            $a.append(crIMG(post.cover96x96));
            $a.append($('<span>').addClass('caption').html(PsProjectData.getRubric(post).name));
            $a.append($('<span>').addClass('title').html(post.name));
            
            if ($a.is('[href="'+defs.url+'"]')) {
                $a.addClass('current');
            }
            
            $a.draggable({
                helper: 'clone',
                opacity: 1,
                scope: passed ? 'passed' : 'notpassed'
            //,revert: true
            });
            
            var _this = this;
            $a.click(function(e) {
                if ($a.is('.current')) {
                    //Если клик произошёл по ссылке, указывающей на текущий открытый пост, то ничего не делаем.
                    return;//---
                }
                if (_this.loading) {
                    e.preventDefault();
                } else {
                    _this.loading = true;
                    $a.addClass('loading').append(crIMG(CONST.IMG_LOADING_PROGRESS, 'loading').addClass('loading'))
                }
            });
            
            parentDiv.append($a);
        }, false, this);
        
        if(!this.passedDiv.hasChild()){
            this.passedDiv.append(noItemsDiv('Нет пройденных уроков'));
        }
        
        if(!this.notPassedDiv.hasChild()){
            this.notPassedDiv.append(noItemsDiv('Все уроки пройдены'));
        }
    }
}

$(function(){
    trainingsManager.init();
    trainingsStateManager.init();
});