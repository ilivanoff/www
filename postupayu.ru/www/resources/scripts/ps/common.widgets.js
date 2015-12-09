/*
 * Менеджер шахматной доски
 */
function ChessBoardManager(board){
    var _this = this;
    
    this.board = $(board);
    this.cells = {};
    this.jqCells = $();
    
    var cellId = function(x, y) {
        return 'c'+x+'x'+y;
    }
    
    this.board.find('tr:not(:first, :last)').reverse().each(function(rowNum){
        rowNum = rowNum%8 + 1;
        
        $(this).find('td:not(:first, :last)').each(function(colNum, item){
            var $cell = $(item);
            colNum = colNum%8 + 1;
            /*
             * rowNum рассчитывается с низа таблицы, от 1 до 8.
             */
            $cell.data('x', colNum).data('y', rowNum);
            _this.cells[cellId(colNum, rowNum)]=$cell;
            _this.jqCells = _this.jqCells.add($cell);
        });
    });
    
    this.getCell = function(x, y){
        return this.cells[cellId(x, y)];
    }
    
    this.getCells = function(){
        return this.jqCells;
    }
    
    this.getCellsWithFigs = function(){
        return this.jqCells.children('.wk, .bk, .wr, .br, .wn, .bn, .wb, .bb, .wq, .bq, .wp, .bp').parent('td');
    }
    
    this.getCellsWithFigsCount = function(){
        return this.getCellsWithFigs().size();
    }
    
    return this;
}


/*
 * Легенда
 * 
   <div class="ps-legend">
    <div class="ps-legend-caption">
        <div class="ps-legend-controls">
            <a href="#u" class="ctrl">&uArr;</a>
            <a href="#d" class="ctrl">&dArr;</a>
            <a href="#c" class="ctrl">&otimes;</a>
        </div>
        <span class="ps-legend-title">Информация</span>
    </div>
    <div class="ps-legend-container">
        <div class="ps-legend-content">
            <span class="ps-legend-empty">Нет информации для отображения</span>
            <div>Содержимое</div>
        </div>
        <div class="ps-legend-owner">© Иванов Илья</div>
    </div>
    <div class="ps-legend-fix"></div>
   </div>

 */

function PsLegend(options) {
    var _this = this;
    
    //Разбираем настройки
    options = $.extend({
        id: null,
        snap: null,
        snapMode: null,
        title: 'Информация',
        emptyText: 'Нет информации для отображения',
        footer: '© Иванов Илья',
        onClose: function() {
        //Если функция не определена, то кнопку закрытия не показываем
        },
        //Задание данной функции позволяет наполнять легенду только если она видна и открыта
        updater: null
    }, options);
    
    options.id = options.id ? 'ps_legend_'+options.id : null;
    
    //Конструируем html-элементы
    var $DIV = $('<div>').addClass('ps-legend');
    if (options.id){
        $('#'+options.id).remove();
        $DIV.attr('id', options.id);
    }
    var $CAPTION = $('<div>').addClass('ps-legend-caption').appendTo($DIV);
    var $CONTROLS = $('<div>').addClass('ps-legend-controls').appendTo($CAPTION);
    var $TITLE = $('<span>').addClass('ps-legend-title').appendTo($CAPTION).html(options.title);
    var $CONTAINER = $('<div>').addClass('ps-legend-container').appendTo($DIV);
    var $CONTENT = $('<div>').addClass('ps-legend-content').appendTo($CONTAINER);
    var $CONTENT_EMPTY = $('<div>').addClass('ps-legend-empty').html(options.emptyText).appendTo($CONTENT);
    var $CONTENT_HOLDER = $('<div>').appendTo($CONTENT).hide();
    var $FOOTER = $('<div>').addClass('ps-legend-owner').appendTo($CONTAINER).hide();
    if (options.footer) {
        $FOOTER.html(options.footer).show();
    }
    //Див для поддержания минимальной ширины
    var $FIX = $('<div>').addClass('ps-legend-fix').appendTo($DIV);
    
    var $A_SHOW = crA().html('&dArr;').appendTo($CONTROLS).hide();
    var $A_HIDE = crA().html('&uArr;').appendTo($CONTROLS);
    $A_SHOW.clickClbck(function() {
        _this.maximize();
    });
    $A_HIDE.clickClbck(function() {
        _this.minimize();
    });
    
    if(options.onClose) {
        crA().html('&otimes;').appendTo($CONTROLS).clickClbck(function() {
            _this.hide();
            options.onClose();
        });
    }
    
    //Вычислим ширину "пустого" элемента
    var minWidth = $DIV.appendTo('body').width();
    $FIX.width(minWidth);
    
    var dragOpts = {
        containment: 'document'
    }
    if (options.snap) {
        dragOpts.snap = options.snap;
    }
    if (options.snapMode) {
        dragOpts.snapMode = options.snapMode;
    }
    $DIV.draggable(dragOpts).hide();
    
    /*
     * ДЛЯ ВЫЗОВА ИЗВНЕ
     */
    this.visible = false;
    this.maximized = true;
    
    this.showAt = function(l, t) {
        this.visible = true;
        this.updateImpl();
        $DIV.hide().css('left', l).css('top', t).fadeIn('fast');
        return this;
    }
    
    this.showAtRbOf = function($el) {
        var offset = $el.offset();
        var l = offset.left+$el.width();
        var t = offset.top+$el.height();
        return this.showAt(l, t);
    }
    
    this.destroy = function() {
        $DIV.remove();
    }
    
    this.setTitle = function(title) {
        $TITLE.html(title);
        return this;
    }
    
    this.setFooter = function(footer) {
        $FOOTER.html(footer);
        return this;
    }
    
    this.hideFooter = function(){
        $FOOTER.hide();
        return this;
    }
    
    this.showFooter = function(){
        $FOOTER.show();
        return this;
    }
    
    this.minimize = function(){
        this.maximized = false;
        $A_HIDE.hide();
        $A_SHOW.show();
        $CONTAINER.hide();
        return this;
    }
    
    this.maximize = function(){
        this.maximized = true;
        this.updateImpl();
        $A_HIDE.show();
        $A_SHOW.hide();
        $CONTAINER.show();
        return this;
    }
    
    this.fill = function($content) {
        this.clear();
        $CONTENT_EMPTY.hide();
        $CONTENT_HOLDER.append($content).show();
        return this;
    }
    
    this.clear = function() {
        //Если мы вызвали fill руками, то lastUpdateCommand нужно сбросить
        this.lastUpdateCommand = null;
        $CONTENT_EMPTY.show();
        $CONTENT_HOLDER.empty().hide();
        return this;
    }
    
    this.hide = function(){
        this.visible = false;
        $DIV.hide();
        return this;
    }
    
    this.show = function(){
        this.visible = true;
        this.updateImpl();
        $DIV.show();
        return this;
    }
    
    this.setVisible = function(vis) {
        return vis ? this.show() : this.hide();
    }
    
    //Функция позволит единожды установить метод, выполняющий обновление легенды, 
    //а затем просто вызывать метод update() с новыми параметрами.
    this.updater = options.updater;
    this.setUpdater = function(updater) {
        this.updater = updater;
        this.updateImpl();
        return this;
    }
    
    this.lastUpdateCommand = null;
    this.update = function(options) {
        this.lastUpdateCommand = options;
        this.updateImpl();
        return this;
    }
    
    this.nowUpdate = false;
    this.updateImpl = function() {
        if (this.nowUpdate || !this.updater || this.lastUpdateCommand===null) return;
        
        if (!this.visible || !this.maximized) return;
        
        //InfoBox.popupInfo('Actual update, command: ' + this.lastUpdateCommand);
        
        this.nowUpdate = true;
        this.updater.call(this, this.lastUpdateCommand);
        this.lastUpdateCommand = null;
        this.nowUpdate = false;
    }
}


/*
 * Контроллер группы ссылок

    <div class="hg-next">
        По клику: 
        <a href="#">не выбрано</a>
        <a href="#act1">Действие 1</a>
        <a href="#act2">Действие 2</a>
    </div>


    или


    <div class="hg-self">
        <a href="#var1">действие 1</a> - описание действия 1
        <a href="#var1">действие 2</a> - описание действия 2
        <a href="#var1">действие 3</a> - описание действия 3
    </div>
    
    далее может быть помещено сюда:

    <div class="hg-next">
        По клику: <span class="hg"></span>
    </div>
 */

function HrefsGroup($baseHrefs, type, options) {
    var _this = this;
    
    $baseHrefs = $baseHrefs.extractTarget('a');
    
    //Может быть передана функция
    options = $.isFunction(options) ? {
        callback: options
    } : options;
    
    //type: 'self or next', self is default
    options = $.extend({
        //Код, для сохранения состояния в хранилище
        id: null,
        
        //Действие, выполняемое при установке нового состояния
        callback: function(anchor) {
            InfoBox.popupInfo(anchor);
        },
        
        //Функция должна вернуть новое состояние, которое будет установлено
        click: function($hrefs, $a, type, anchor) {
            if (!type) return anchor;
            
            var curIdx;
            switch (type) {
                case 'next':
                    curIdx = $hrefs.index($a) + 1;
                    curIdx = curIdx==$hrefs.size() ? 0 : curIdx;
                    return getHrefAnchor($hrefs.get(curIdx));
                case 'prev':
                    curIdx = $hrefs.index($a) - 1;
                    curIdx = curIdx<0 ? $hrefs.size()-1 : curIdx;
                    return getHrefAnchor($hrefs.get(curIdx));
            }
            return anchor;
        },
        
        //Функция переустанавливает состояние группы и выделенной ссылки в ней
        focus: function($hrefs, $a, type) {
            $hrefs.removeClass('cur');
            $a.addClass('cur');
        }
    }, options);
    
    
    function fetchState(state) {
        return PsStrings.trim(state);
    }
    
    function hrefByState($hrefs, state) {
        return $hrefs.filter('a[href="#'+fetchState(state)+'"]');
    }
    
    function hasState(state) {
        return !hrefByState($baseHrefs, state).isEmptySet();
    }
    
    
    var DataGroupParam = 'hg_hrefs_group';
    var curState = '';
    var groups = [];
    var store = options.id ? PsLocalStore.WIDGET('HrefsGroup_'+options.id) : null;
    
    function focusGroups() {
        $.each(groups, function(i, gr) {
            var $a = hrefByState(gr.hrefs, curState);
            options.focus(gr.hrefs, $a, gr.type);
        });
    }
    
    function setState(state, silent) {
        curState = fetchState(state);
        focusGroups();
        if (!silent && options.callback) {
            options.callback(curState);
        }
        if (!silent && store) {
            store.set('state', curState)
        }
    }
    
    var onClick = function() {
        var $a = $(this);
        var gr = $a.data(DataGroupParam);
        var anchor = getHrefAnchor($a);
        var newState = options.click(gr.hrefs, $a, gr.type, anchor);
        _this.setState(newState);
        return false;
    }
    
    function registerHrefsGroup($hrefs, type) {
        var gr = {
            type: type,
            hrefs: $hrefs
        }
        groups.push(gr);
        $hrefs.unbind('click', onClick).bind('click', onClick).data(DataGroupParam, gr);
    }
    
    /*
     * Методы, которые можно вызвать извне
     */
    
    this.setState = function(state, silent) {
        setState(state, silent);
        return this;
    }
    
    this.callbackSet = function(callback) {
        options.callback = callback;
        return this;
    }
    
    this.callbackCall = function() {
        if (options.callback) {
            options.callback(curState);
        }
    }
    
    this.setFirst = function(silent) {
        this.setState(getHrefAnchor($baseHrefs.first()), silent);
        return this;
    }
    
    this.setLast = function(silent) {
        this.setState(getHrefAnchor($baseHrefs.last()), silent);
        return this;
    }
    
    this.clone = function(type) {
        var $new = $baseHrefs.clone();
        registerHrefsGroup($new, type);
        return $new;
    }
    
    this.cloneAndPlaceTo = function($where, type) {
        $where.extractTarget('.hg').replaceWith(this.clone(type));
    }
    
    this.hasState = function(state) {
        return hasState(state);
    }
    
    this.getState = function() {
        return curState;
    }
    
    /*
     * Инициализируем состояние
     */
    //    $baseHrefs.bind('click', onClick)
    hrefByState($baseHrefs, '').addClass('empty');
    registerHrefsGroup($baseHrefs, type);
    
    var storedState = store ? store.get('state') : null;
    if (storedState && hasState(storedState)) {
        this.setState(storedState, true);
    } else {
        this.setFirst(true);
    }
}


/*
    Контроллер панели табов

    <h3 class="tab_items">
        <a href="#tab1">Первый таб</a>
        <a href="#tab2">Второй таб</a>
    </h3>
    <div class="tab_bodies">
    </div>
 */

function TabPanelController($tabsPanel, options) {
    var $items = $tabsPanel.extractTarget('.tab_items');
    var $hrefs = ($items.isEmptySet() ? $tabsPanel : $items).extractTarget('a') 
    var HG = new HrefsGroup($hrefs, 'self', options);
    $.extend(this, HG);
    
    
    var $bodies = $items.next('.tab_bodies');
    if ($bodies.isEmptySet()) return;//---
    //Есть тела табов, привязываем
    
    var UPDATERS = {};
    
    function activateTab() {
        var tab = HG.getState();
        var $tab = $bodies.children().hide().filter('.'+tab).show();
        if (UPDATERS[tab]){
            UPDATERS[tab].call($tab, $tab);
        }
    }
    
    HG.callbackSet(activateTab);
    
    this.setUpdaters = function(updaters) {
        $.extend(UPDATERS, updaters||{});
    }
    
    this.updateTab = function() {
        activateTab();
    }
    
    activateTab();
}

/*
$(function() {
    var TI = new TabPanelController($('.tab_items'), {
        callback: function(name) {
            InfoBox.popupInfo('{'+name+'}');
        }
    });
    TI.setLast(true);
})
 */


function PsSortableCompare($BODY) {
    $BODY = $BODY.extractTarget('.ps-sortable-compare').disableSelection();
    
    var $ul1 = $BODY.find('.ul1');
    var $ul2 = $BODY.find('.ul2');
    var $ctr = $BODY.find('.ctrl');
    var $btns = $ctr.find('button');
    
    function $lis1() {
        return $ul1.find('li');
    }
    
    function $lis2() {
        return $ul2.find('li');
    }
    
    function $lis() {
        return $lis1().add($lis2());
    }
    
    function $li1(idx) {
        return $($lis1().get(idx));
    }
    
    function $li2(idx) {
        return $($lis2().get(idx));
    }
    
    function $btn(name) {
        return $btns.filter('.' + name);
    }
    
    function each(callback) {
        for(var i = 0; i < $lis1().size(); i++) {
            callback($li1(i), $li2(i), i);
        }
    }
    
    var html1 = [];
    var html2 = [];
    
    function setDimensions() {
        $lis1().sameWidth();
        $lis2().sameWidth();
        
        $lis().sameHeight();
        
        $lis().each(function() {
            var $li = $(this);
            var liH = $li.height();
            var $ctt = $li.children();
            var cttH = $ctt.height();
            if (!cttH) return;
            var padding = (liH - cttH)/2;
            $ctt.css('padding-top', padding);
        });
        
        each(function($l, $r){
            html1.push($l.children().clone());
            html2.push($r.children().clone());
        });
    }
    
    function resetHtml() {
        if (isEmpty(html1) || isEmpty(html2)) return;//---
        
        each(function($l, $r, i) {
            $l.html('').append(html1[i].clone()).data('num', i);
            $r.html('').append(html2[i].clone()).data('num', i);
        });
    }
    
    function doHighlight(mark) {
        $lis().removeClass('success error');
        $BODY.toggleClass('ready', !mark);
        
        if(!mark) return;
        
        each(function($l, $r) {
            var cl = $l.data('num')==$r.data('num') ? 'success' : 'error';
            $l.addClass(cl);
            $r.addClass(cl);
        });
    }
    
    var $sortable = $ul2.extractTarget('ul').sortable({
        axis: "y",
        placeholder: "placeholder",
        start: function(event, ui) {
            ui.placeholder.height(ui.item.height());
            ui.item.addClass('move');
            doHighlight();
        },
        stop: function(event, ui) {
            ui.item.removeClass('move');
        }
    });
    
    /*
     * Кнопки
     */
    var isLocked = true;
    var $btnLock = $btn('lock').button({
        text: false,
        icons: {
            primary: "ui-icon-locked"
        }
    });
    
    var $btnShuffle = $btn('shuffle').button({
        text: false,
        icons: {
            primary: "ui-icon-refresh"
        }
    }).click(function(){
        doHighlight();
        $lis2().shuffle();
    });
    
    var $btnCheck = $btn('check').button({
        text: false,
        icons: {
            primary: "ui-icon-check"
        }
    }).click(function(){
        doHighlight(!$lis().hasClass('success') && !$lis().hasClass('error'));
    });
    
    $btns.parent().buttonset();
    
    function setLocked(locked) {
        $btnLock.button("option", {
            label: locked ? 'Разблокировать' : 'Заблокировать',
            icons: {
                primary: "ui-icon-" + (locked ? 'locked' : 'unlocked')
            }
        });
        
        $btnShuffle.add($btnCheck).button({
            disabled: locked
        });
        
        $BODY.toggleClass('editable', !locked);
        
        $sortable.sortable({
            disabled: locked
        });
        
        resetHtml();
        doHighlight();
        isLocked = locked;
    }
    
    $btnLock.click(function(){
        setLocked(!isLocked);
    });
    
    PsJquery.onLoad($lis(), function() {
        setDimensions();
        setLocked(true);
    });
}


/*
 * Пятнашки
 */
function PsPyatnashki($box) {
    var _this = this;
    
    $box = $box.extractTarget('.ps-pyatnashki');
    
    //Сразу определим размер ячейки
    var $cell = $('<div>').addClass('cell').appendTo($box);
    var w = Math.floor($cell.width());
    $box.empty();
    
    var n = 0;//Кол-во ячеек
    
    var listener = null;//Слушатель
    
    function isValidIJ(i, j) {
        return PsIntervals.isIn(i, [1, n]) && PsIntervals.isIn(j, [1, n]);
    }
    
    function getCellInfo($cell) {
        var l = $cell.css('left');
        while (l.length>0 && !PsIs.number(l)) {
            l = l.substr(0, l.length-1);
        }
        
        var t = $cell.css('top');
        while (t.length>0 && !PsIs.number(t)) {
            t = t.substr(0, t.length-1);
        }
        
        var i = 1 + Math.round(t/w);
        var j = 1 + Math.round(l/w);
        
        return {
            l: l,
            t: t,
            i: i,
            j: j
        }
    }
    
    function getCell(i, j) {
        var result = null;
        $box.children().each(function() {
            var $cell = $(this);
            var ij = getCellInfo($cell);
            if (ij.i==i && ij.j==j) {
                result = $cell;
            }
            return !result;
        });
        return result;
    }
    
    function eachCell(callback, withEmpty) {
        for (var i = 1; i <= n; i++) {
            for (var j = 1; j <= n; j++) {
                var $cell = getCell(i, j);
                if ($cell || withEmpty) {
                    callback(i, j, $cell);
                }
            }
        }
    }
    
    function emptyCellIJ() {
        var ij = null;
        eachCell(function(i, j, $cell) {
            if(!$cell){
                ij = {
                    i:i,
                    j:j
                }
            }
        }, true);
        return ij;
    }
    
    function getCells4move(i, j) {
        var eij = emptyCellIJ();
        var ei = eij.i;
        var ej = eij.j;
        var $res = $();
        var direction = null;
        if (!isValidIJ(i, j) || (i!=ei && j!=ej) || (i==ei && j==ej)) {
            return null;
        }
        
        var di = 0;
        var dj = 0;
        if(i==ei) {
            //Горизонталь
            if(j<ej) {
                //Слева от пустой
                direction = 'e';
                dj = 1;
            }else{
                //Справа от пустой
                direction = 'w';
                dj = -1;
            }
        } else 
        if(j==ej) {
            //Вертикаль
            if(i<ei) {
                //Выше пустой
                direction = 's';
                di = 1;
            }else{
                //Ниже пустой
                direction = 'n';
                di = -1;
            }
        }
        
        do {
            $res = $res.add(getCell(i, j));
            i+=di;
            j+=dj;
        } while(!(i==ei && j==ej));
        
        return {
            c: $res,
            d: direction
        };
    }
    
    function getCurArray(withEmpty) {
        var result = [];
        eachCell(function(i, j, $cell){
            result.push($cell ? strToInt($cell.html()) : null);
        }, withEmpty);
        return result;
    }
    
    
    //Возвращает инверсии в виде массива двойных массивов
    function getInversions(array) {
        var res = [];
        
        function getNumsAfter(i) {
            array.walk(function(num, idx) {
                if (num && idx>i && num<array[i]) {
                    res.push([array[i], num]);
                }
            });
        }
        
        array.walk(function(num, idx) {
            if(!num) return;
            getNumsAfter(idx);
        });
        
        return res;
    }
    
    function getPropSum(array){
        function getNumsAfter(i) {
            var nums = [];
            array.walk(function(num, idx) {
                if (idx>i && num<array[i]) {
                    nums.push(num);
                }
            });
            return nums;
        }
        
        var cnt = 0;
        var descr = '';
        array.walk(function(num, idx) {
            var nums = getNumsAfter(idx);
            cnt+=nums.length;
            descr += descr=='' ? '' : '; ';
            descr += '<b>'+num+'</b>:' + (nums.length>0?nums.join(','):'');
        });
        
        return {
            N: cnt,
            D: descr
        }
    }
    
    //Проверяет, является ли расстановка чётной
    function isEven(array) {
        array = array.clone();
        var shifts = 0;
        var empPos = null;
        for (var i = 1; i <= array.length; i++) {
            var tmp = array[i-1];
            if(!tmp) {
                //Пустая ячейка
                empPos = empPos ? empPos : i;
                continue;
            }
            if (tmp==i) {
                //Число на своём месте
                continue;
            } 
            //Меняем
            array[array.indexOf(i)] = tmp;
            array[i-1] = i;
            ++shifts;
        }
        
        var Manh = 0;
        if (empPos) {
            //Манхэттенское расстояние
            var N = Math.round(Math.sqrt(array.length));
            var left = empPos%N;
            left = left ? left : N;
            var top = Math.round((empPos-left)/N + 1);
            Manh = (N-top)+(N-left);
        }
        
        return !((shifts+Manh)%2);
    }
    
    //Проверяет, является ли расстановка собранной
    function isDone(array) {
        for (var i = 0; i < array.length-1; i++) {
            if (!array[i] || (i+1!=array[i])) return false;
        }
        return true;
    }
    
    function isDoneCurrent() {
        return isDone(getCurArray(true));
    }
    
    function setEnabled(enabled) {
        if (enabled){
            $box.removeClass('disabled');
            $box.children().bind('click', onClick);
        }else{
            $box.addClass('disabled');
            $box.children().unbind('click', onClick);
        }
    }
    
    
    var doDisableOnFinish = false;
    
    function checkFinish() {
        if (!isDoneCurrent()) return;
        if(listener && listener.finish) {
            listener.finish.call(_this);
        }
        if(doDisableOnFinish){
            setEnabled(false);
        }
    }
    
    
    /*
     * type - тип комбинации
     * 
     * 0 - любая, валидация не проводится
     * 1 - валидная
     * 2 - невалидная
     * 
     * 10, 11, 12 - то-же самое, только пустая ячейка стоит на произвольном месте
     * 20, 21, 22 - то-же самое, только пустая ячейка стоит на нижней линии
     */
    function getCombination(n, type) {
        var randomEmpty = PsIntervals.isIn(type, [10, 12]);
        var bottomEmpty = PsIntervals.isIn(type, [20, 22]);
        
        var nums = [];
        for (var i = 1; i < n*n; i++) {
            nums.push(i);
        }
        if (randomEmpty) {
            nums.push(null);
        }
        nums.shuffle();
        
        function changeEven(arr) {
            var i,j;
            for (var k = 0; k < arr.length; k++) {
                if(arr[k]){
                    if(!i){
                        i=k;
                    } else if(!j){
                        j=k;
                    }else{
                        break;
                    }
                }
            }
            arr[i] = arr[i]+arr[j];
            arr[j] = arr[i]-arr[j];
            arr[i] = arr[i]-arr[j];
        }
        
        if(!randomEmpty){
            if(bottomEmpty){
                var changeIdx = PsUtil.nextInt(n*(n-1)+1, n*n);
                if (changeIdx==n*n){
                    nums.push(null);
                }else{
                    //Отними единицу, чтобы это бал индекс в массиве, а не на игровом поле
                    --changeIdx;
                    nums.push(nums[changeIdx]);
                    nums[changeIdx] = null;
                }
            } else {
                nums.push(null);
            }
        }
        
        switch (type%10) {
            case 1:
                if(!isEven(nums)) changeEven(nums);
                break;
            case 2:
                if (isEven(nums)) changeEven(nums);
                break;
        }
        
        return nums;
    }
    
    //Функции для вызова извне
    
    var hodes = 0;
    
    var onClick = function() {
        if($box.hasChild(':animated')) {
            return;//---
        }
        
        var ij = getCellInfo($(this));
        var moveTo = getCells4move(ij.i, ij.j);
        if(!moveTo) return;
        
        var ob = {};
        switch (moveTo.d) {
            case 'n':
                ob.top = '-=' + w;
                break;
            case 's':
                ob.top = '+=' + w;
                break;
            case 'w':
                ob.left = '-=' + w;
                break;
            case 'e':
                ob.left = '+=' + w;
                break;
        }
        
        moveTo.c.css(ob);
        if (listener) {
            listener.move.call(_this, ++hodes);
            checkFinish();
        }
        return;
        //Анимированное перемещение костяшек
        moveTo.c.animate(ob, 100, function() {
            if (listener) {
                listener.move.call(_this, ++hodes);
                checkFinish();
            }
        });
    };
    
    this.init = function(cols, type_or_set) {
        this.fill(cols, type_or_set);
    }
    
    var options = {};
    this.fill = function(n_or_set, type) {
        options.n_or_set = n_or_set;
        options.type = type;
        
        var set = PsIs.array(n_or_set) ? n_or_set : getCombination(n_or_set, PsIs.number(type) ? type : 1);
        n = Math.round(Math.sqrt(set.length));
        
        hodes = 0;
        $box.empty();
        
        for(var i = 1; i <= n; i++) {
            for(var j = 1; j <= n; j++) {
                var idx = (i-1)*n+j-1;
                if(!set[idx]) continue;
                
                var t = Math.round(w*(i-1));
                var l = Math.round(w*(j-1));
                
                $box.append($('<div>').addClass('cell').css('left', l).css('top', t).append(set[idx]));
            }
        }
        $box.width(n*w).height(n*w).disableSelection();
        
        setEnabled(true);
        checkFinish();
        return this;
    }
    
    this.reinit = function() {
        return this.init(options.n_or_set, options.type);
    }
    
    this.setListener = function(_listener) {
        listener = $.extend({
            move: function(hodes) {
            //Вызывается после хода
            },
            finish: function() {
            //Вызывается после завершения
            }
        }, _listener);
    }
    
    this.setEnabled = function(enabled) {
        setEnabled(enabled);
        return this;
    }
    
    //Если выставлено в true, то поле будет автоматически блокироваться,
    //когда расстановка на нём собрана.
    this.setDisableOnFinish = function(disableOnFinish) {
        doDisableOnFinish = disableOnFinish;
    }
    
    this.getInfo = function() {
        var array = getCurArray();
        var arrayWithEmpty = getCurArray(true);
        var eIJ = emptyCellIJ();
        
        return {
            arr: array,
            arre: arrayWithEmpty,
            inv: getInversions(array),
            even: isEven(arrayWithEmpty),
            K: eIJ.i,
            n: n,
            manh: PsMath.num2bounds((n-eIJ.i), [0, n])+PsMath.num2bounds((n-eIJ.j), [0,n])
        }
    }
}



/*
 * РЕБУСЫ
 */

function MathRebusPresentation($BODY) {
    var text = PsStrings.removeSpaces($BODY.html());
    
    var tokens = text.split('=');
    var left = tokens[0];
    var result = tokens[1];
    
    var lines = [];
    var operations = [];
    
    var line = '', i;
    for (i = 0; i < left.length; i++) {
        var ch = left.charAt(i);
        if (i==left.length-1) {
            line+=ch;
            lines.push(line);
            line = '';
        } else 
        if (PsArrays.inArray(ch, ['+', '-', '*', ':'])) {
            lines.push(line);
            line = '';
            operations.push(ch);
        } else {
            line+=ch;
        }
    }
    
    var maxlen = result.length;
    lines.walk(function(line) {
        maxlen = Math.max(maxlen, line.length);
    });
    
    function getTr(line) {
        var $tr = $('<tr>');
        for (i = 0; i < maxlen; i++) {
            var cnNum = line.length - maxlen + i;
            var $td = $('<td>').html(cnNum < 0 ? '' : line.charAt(cnNum));
            $tr.append($td);
        }
        return $tr;
    }
    
    var $table = $('<table>').addClass('ps-math-rebus');
    lines.walk(function(line) {
        $table.append(getTr(line));
    });
    $table.append(getTr(result).addClass('result'));
    
    var $TdTop = $('<td>').attr('rowspan', lines.length).html(''+operations.join(' ')).addClass('sign');
    var $TdBottom = $('<td>').addClass('first');
    
    $table.find('tr').first().prepend($TdTop);
    $table.find('tr').last().prepend($TdBottom);
    
    $table.insertAfter($BODY);
    $BODY.remove();
}

/*
 * КОНТРОЛЛЕР СКРЫТЫХ БЛОКОВ
 */
function PsHiddenBox($box) {
    var openText = $.trim($box.data('title'));

    if ($box.is('span')) {
        //INLINE
        $('<span>').addClass('ps-hidden-box-toggler').
        html('[' + (openText ? openText : 'показать') + ']').
        disableSelection().clickClbck(function() {
            this.remove();
            $('<span>').appendTo($box).unwrap().remove();
        }).insertBefore($box);
    } else {
        //BLOCK
        var $toggler = $('<div>').addClass('ps-hidden-box-toggler').
        html('[' + (openText ? openText : 'Показать') + ']').
        disableSelection().clickClbck(function() {
            this.hide();
            $('<div>').html('[Скрыть ⇑]').addClass('ps-hidden-box-toggler').clickClbck(function() {
                this.remove();
                $box.hide();
                $toggler.show();
            }).disableSelection().insertAfter($box.show());
        }).insertBefore($box);
    }
}

/*
 * CODEMIRROR
 */

function CodemirrorManager($textarea) {
    var type = $textarea.attr('codemirror');
    var name = $textarea.attr('name');
    
    //Будем вести хранилище в разрезе названий textarea
    var store = PsLocalStore.WIDGET('CodemirrorManager-' + name);
    
    var options = {
        mode: type,
        indentUnit: 4,
        undoDepth: 100,
        lineNumbers: false,
        lineWrapping: true,
        onChange: function(inst) {
            //При изменении данных будем их переносить в оригинальную textarea - это позволит без заморочек вызывать $.val()
            if(inst.save) inst.save();
        }
    };
    var CM = CodeMirror.fromTextArea($textarea.get(0), options);
    
    var $wrap = $(CM.getWrapperElement());
    var $scroll = $(CM.getScrollerElement());
    var origHeight = $scroll.css('height');
    var originalVal = $textarea.val();
    
    function winHeight() {
        return $(window).height();
    }
    
    /*
     * Расширение по всей высоте
     */
    var isExpanded = store.get('expanded', false);
    
    function setExpanded(expanded) {
        isExpanded = expanded;
        store.set('expanded', expanded);
        $scroll.css('height', isExpanded ? 'auto' : origHeight);
    }
    setExpanded(isExpanded);
    
    
    /*
     * Установка полноэкранного режима
     */
    var isFullScreen = false;
    
    function setFullScreen(full) {
        isFullScreen = full;
        if(full) {
            PsScrollManager.storeWndScroll();
            $scroll.css('height', winHeight() + "px");
        }else{
            setExpanded(isExpanded);
        }
        $wrap.toggleClass('CodeMirror-fullscreen', full);
        document.documentElement.style.overflow = full ? "hidden" : "";
        CM.refresh();
        
        if(!full) {
            PsScrollManager.restoreWndScroll();
        }
    }
    function toggleFullScreen() {
        setFullScreen(!isFullScreen);
    }
    function disableFullScreen() {
        setFullScreen(false);
    }
    
    /*
     * Форматирование
     */
    function format(){
        CodeMirror.commands["selectAll"](CM);
        CM.autoFormatRange(CM.getCursor(true), CM.getCursor(false));
    }
    
    /*
     * Отмена всех изменений
     */
    function undoAll() {
        while(CM.historySize().undo){
            CM.undo();
        }
    }
    
    /*
     * Показывать номера строк
     */
    
    var isLineNumbers = store.get('linenums', false);
    
    function setLineNumbers(show) {
        isLineNumbers = show;
        store.set('linenums', show);
        CM.setOption('lineNumbers', show);
    }
    setLineNumbers(isLineNumbers);
    
    function toggleLineNumbers() {
        setLineNumbers(!isLineNumbers);
    }
    
    /*
     * Горячие клавиши
     */
    
    CM.setOption('extraKeys', {
        "Ctrl-F11": toggleFullScreen,
        "Esc": disableFullScreen,
        "Ctrl-F9": format,
        "Ctrl-L": toggleLineNumbers
    });
    
    /*
     * Кнопки
     */
    var $btns = $('<div>').addClass('ps-codemirror-ctrl');
    
    $btns.append($('<button>').button({
        text: false,
        label: 'Отменить всё',
        icons: {
            primary: "ui-icon-arrowreturnthick-1-w"
        }
    }).clickClbck(undoAll));
    
    $btns.append($('<button>').button({
        text: false,
        label: 'Отменить',
        icons: {
            primary: "ui-icon-arrowreturn-1-w"
        }
    }).clickClbck(function() {
        CM.undo();
    }));
    
    $btns.append($('<button>').button({
        text: false,
        label: 'Восстановить',
        icons: {
            primary: "ui-icon-arrowreturn-1-e"
        }
    }).clickClbck(function(){
        CM.redo();
    }));
    
    $btns.append($('<button>').button({
        text: false,
        label: 'Восстановить всё',
        icons: {
            primary: "ui-icon-arrowreturnthick-1-e"
        }
    }).clickClbck(function(){
        while(CM.historySize().redo){
            CM.redo();
        }
    }));
    
    $btns.append($('<button>').button({
        text: false,
        label: 'Авто-высота',
        icons: {
            primary: "ui-icon-carat-2-n-s"
        }
    }).clickClbck(function(){
        setExpanded(!isExpanded);
    }));
    
    $btns.append($('<button>').button({
        text: false,
        label: 'Во весь экран (Ctrl-F11)',
        icons: {
            primary: "ui-icon-arrow-4-diag"
        }
    }).clickClbck(function() {
        setFullScreen(true);
    }));
    
    $btns.append($('<button>').button({
        text: false,
        label: 'Показывать номера строк (Ctrl-L)',
        icons: {
            primary: "ui-icon-grip-solid-vertical"
        }
    }).clickClbck(toggleLineNumbers));
    
    $btns.append($('<button>').button({
        text: false,
        label: 'Отформатировать (Ctrl-F9)',
        icons: {
            primary: "ui-icon-arrowreturnthick-1-e"
        }
    }).button({
        text: false,
        icons: {
            primary: "ui-icon-refresh"
        }
    }).clickClbck(format));
    
    $btns.insertBefore($textarea).buttonset();
    
    
    //Метод сбросит всю историю и установит начальное значение в textarea
    this.resetState = function() {
        undoAll();
        CM.setValue(originalVal);
        CM.save();
    };
}


/*
 * FORM LEGEND TOGGLER
 */
function FormLegendToggler($legend) {
    var id = $legend.extractParent('form').attr('id');
    var name = $.trim($legend.html());
    
    //Будем вести хранилище в разрезе названий textarea
    var store = PsLocalStore.WIDGET('FormLegendToggler-' + MD5(id+'-'+name));
    
    var isOpen = store.get('open', true);
    var $siblings = $legend.siblings();
    var $fieldset = $legend.parent('fieldset');
    
    function setOpen(open) {
        isOpen = open;
        $siblings.setVisible(open);
        store.set('open', open);
        $fieldset.toggleClass('closed', !open);
    }
    
    $legend.disableSelection().click(function(){
        setOpen(!isOpen);
    });
    setOpen(isOpen);
}

/*
 * TEXTAREA
 */
function PsTextareaManager($ta){
    //Codemirror
    if ($ta.is('[codemirror]')) {
        if (isDefined(window.CodeMirror)) {
            var CM = new CodemirrorManager($ta);
            FormHelper.bindOnReset($ta, CM.resetState);
        }
        return;//---
    }
    
    //Это - специальная textarea, создаваемая плагином CodeMirror, пропускаем
    if ($ta.is('.CodeMirror textarea')) {
        return;//---
    }
    
    //Ручное управление, пропускаем
    if ($ta.data('manual')) {
        return;//---
    }
    
    var ta = $ta[0];
    
    //MAXLEN
    var maxLen = strToInt($ta.attr('ml'));
    
    var $preview = $('<div>').addClass('ps-textarea-preview').hide().insertAfter($ta);
    var $maxlen = maxLen ? $('<div>').addClass('ps-textarea-maxlen').insertAfter($preview) : null;
    
    function updateLeftSymbols(filled) {
        if ($maxlen) {
            var left = maxLen - filled;
            var text = 'Символов осталось: ' + PsHtml.num2str(left);
            $maxlen.html(PsHtml.span(text, 'info '+(left < 0 ? 'err' : '')));
        }
    }
    
    //Модификация размеров текстового поля. Основано на использовании свойства textarea.scrollHeight - высота скролла.
    //Навеяно jquery.textarea-expander.js
    var minHeight = 100;
    var maxHeight = 300;
    
    function stateChanged() {
        //РАЗМЕРЫ ПОЛЯ ВВОДА
        var scrollHeight = ta.scrollHeight;
        if (scrollHeight > minHeight) {
            $ta.height(minHeight); //Сбрасываем размер поля, чтобы вычислить величину скролла, твк как мы могли и стереть текст
            scrollHeight = ta.scrollHeight;
        }
        var height = Math.max(minHeight, Math.min(scrollHeight, maxHeight));
        var showScroll = scrollHeight > height;
        $ta.css('overflow', showScroll ? 'auto' : 'hidden'); //Если максимальная высота поля превышена - ставим auto
        $ta.height(height);
        //Если мы показали скролл, то пролистаем в самый низ
        //http://stackoverflow.com/questions/9170670/how-do-i-set-textarea-scroll-bar-to-bottom-as-a-default
        if (showScroll) ta.scrollTop = scrollHeight;
        
        //ПРЕДПРОСМОТР
        var value = $.trim($ta.val());
        if(!value)
        {
            $preview.hide().empty();
            updateLeftSymbols(0);
        }
        else
        {
            $preview.html(value.htmlEntities()).show();
            if (value.hasJax()){
                MathJaxManager.updateFormules();
            }
            updateLeftSymbols(value.length);
        }
    }
    
    $ta.keyup(stateChanged).change(stateChanged).focus(stateChanged).blur(stateChanged);
    FormHelper.bindOnReset($ta, stateChanged);
    stateChanged();
}


/*
 * TABS
 */
function PsTabPanelManager($BODY) {
    var id = $BODY.attr('id');
    var store = id ? PsLocalStore.WIDGET('PsTabPanelManager-' + id) : null;
    var $form = $BODY.extractParent('form');
    
    var $tabs = $BODY.children('div');
    var $hrefs = $('<h3>').addClass('tab_items');
    var setFormAction = false;
    $tabs.each(function(i) {
        ++i;
        var $tab = $(this);
        var title = $tab.attr('title') ? $tab.attr('title') : 'Таб ' + i;
        var $a = crA('#'+title).clickClbck(function(title) {
            $tabs.hide();
            $tab.show();
            $hrefs.removeClass('cur');
            $a.addClass('cur');
            if (store) {
                store.set('tab', title);
            }
            if (setFormAction) {
                $form.addFormHidden('action', $tab.data('form-action'));
            }
        }).html(title);
        $hrefs.append($a)
        setFormAction = setFormAction || !!$tab.data('form-action');
    }).removeAttr('title');
    $hrefs.insertBefore($BODY);
    $hrefs = $hrefs.children('a');
    setFormAction = setFormAction && !$form.isEmptySet()  
    
    var state = store ? store.get('tab') : null;
    var initHref = state ? $hrefs.extractHrefsByAnchor(state) : null;
    initHref = isEmpty(initHref) ? $hrefs.first() : initHref;
    initHref.click();
    
    var controller = {
        setFirst: function() {
            $hrefs.first().click();
            return $BODY;
        },
        setLast: function() {
            $hrefs.last().click();
            return $BODY;
        }
    }
    
    $BODY.data('tabs', controller);
    
    PsUtil.scheduleDeferred(function() {
        $BODY.show();
    });
}

/*
 * ContentFlow
 */
/*
 * Flow Data View
<div class="ContentFlow" id="posts_flow">
    <div class="loadIndicator"><div class="indicator"></div></div>
    <div class="flow">

        <div class="item">
            <img class="content" src="resources/images/bp/kachek.jpg"/>
            <div class="caption">
                <span class="flow_caption"><a href="xxx.php" class="flow_href">Автор</a></span><br/>
                <span class="flow_caption">Автор</span><br/>
                <span class="flow_title">1985 - 1986</span><br/>
                <span class="flow_title"><a href="xxx.php">1985 - 1986</a></span>
            </div>
        </div>

    </div>
    <div class="globalCaption"></div>
    <div class="scrollbar">
        <div class="slider"><div class="position"></div></div>
    </div>
</div>

 *
 * converter - функция, принимающая на вход элемент переданного массива, и возвращающая объект:
 * {
 *  src - путь к картинке
 *  href - ссылка для картинки
 *  caption - заголовок (может быть ссылкой или текстом)
 *  title - название (может быть ссылкой или текстом)
 * }
 */
var PsContentFlow = {
    insts: {},
    uptimeDelay: 500,
    logger: PsLogger.inst().setTrace(),
    appendTo: function(data, converter, $target) {
        if(!$.isFunction(window.ContentFlow)) {
            this.logger.logInfo('ContentFlow отключён');
            return; //Библиотека не подключена
        }
        
        if (!data) {
            this.logger.logInfo('ContentFlow пропущен - не переданы данные');
            return;//---
        }
        
        var id = PsRand.pseudoId('cf');
        var $body = $('<div>').addClass('PsContentFlow').attr('id', id);
        var $flow = $('<div>').addClass('flow');
        $body.append($('<div>').addClass('loadIndicator').append($('<div>').addClass('indicator')));
        $body.append($flow);
        $body.append($('<div>').addClass('globalCaption'));
        $body.append($('<div>').addClass('scrollbar').append($('<div>').addClass('slider').append($('<div>').addClass('position'))));
        
        //Добавляем элементы. Проходим через $.each, так как мог быть передан и объект
        $.each(data, function(k, item) {
            var obj = converter(item);
            var $item = $('<div>').addClass('item');
            $item.append(crIMG(obj.src).attr('href', obj.href).addClass('content'));
            var $caption = $('<div>').addClass('caption');
            $caption.append($('<span>').addClass('flow_caption').append(obj.caption));
            if (obj.title) {
                $caption.append($('<br>'));
                $caption.append($('<span>').addClass('flow_title').append(obj.title));
            }
            $item.append($caption);
            $flow.append($item); //Добавляем к flow
        });
        
        $body.appendTo($target);
        
        /*
         * Дадим время, чтобы ContentFlow мог загрузить все свои темы.
         * Можно использовать ContentFlowGlobal.onloadInit.done, но не хотелось бы этого делать,
         * так как потом могут быть проблемы после обновления библиотеки
         */
        PsUtil.executeOnUptime(this.uptimeDelay, function() {
            var width = $body.show().outerWidth(true);
            var cf = new ContentFlow(id);
            cf.init();
            
            //Функция пересчёта состояния
            var resize = function() {
                if ($body.isVisible() && (width != $body.outerWidth(true))) {
                    //InfoBox.popupInfo('Resized');
                    width =  $body.outerWidth(true);
                    cf.resize();
                }
            }
            $('#'+id+':visible').livequery(resize);
            
            //Сохраняем
            PsContentFlow.insts[id] = {
                //cf: cf, // --- Пока не нужен
                resize: resize
            };
        });
        
        this.logger.logInfo('ContentFlow [{}] подключён', id);
    },
    resize: function() {
        $.each(this.insts, function(id, ob) {
            ob.resize();
        });
    }
}

/*
 * РЕДАКТИРУЕМЫЕ ТАБЛИЦЫ
 * 
 * options: {
 *      msg: 'Текст для подтверждения перед сохранением',
 *      ctxt: 'Контекст вызова',
 *      saver: function(controller, onDone) {
 *          //Функция, выполняющая сохранение
 *      }
 * }
 */
jQuery.fn.psEditableGrid = function(options) {
    if ($.isFunction(options)) {
        options = {
            msg: null,       //Текст подтверждения
            ctxt: null,      //Контукст вызова сейвера
            saver: options,  //Функция сохранения function(CONTROLLER, onDone)
            reload: true     //Перезагружать ли страницу после успешного сохранения грида
        }
    }
    
    options = options || {};
    
    var $table = $(this).extractTarget('table').addClass('editable');
    if ($table.isEmptySet()) return;//---
    
    var class_invalid = 'invalid';
    var data_long_text = 'text';
    //$table.find('td').removeClass('editable');
    //$table.find('tr[data-trid] td[data-tdid]').addClass('editable');
    
    var UTIL = {
        //Проверка типа
        isYn: function($td) {
            return $td.is('.yn');
        },
        isLongText: function($td) {
            return $td.is('.dialog');
        },
        isText: function($td) {
            return !this.isYn($td) && !this.isLongText($td);
        },
        
        //Проверка других параметров
        isNew: function ($td) {
            return $td.is('tr.new td');
        },
        hasId: function($td) {
            return !!$td.data('tdid');
        },
        hasInput: function($td) {
            return $td.hasChild('input');
        },
        
        //Загрузка значения + валидация столбца
        val: function($td) {
            if(!this.hasId($td)) return null;// У свойства нет ID ---
            var $input = $td.find('input');
            var val;
            
            if (this.isYn($td)) {
                var checked;
                if ($input.isEmptySet()) {
                    val = $.trim($td.html());
                    checked = !val || val=='false' || val=='0' ? false : true;
                } else {
                    checked = $input.isChecked();
                }
                return checked ? 1 : 0;
            }
        
            if(!$input.isEmptySet()) {
                val = $.trim($input.val());
            } else if (this.isLongText($td)) {
                val = $td.data(data_long_text);
            } else {
                val = $.trim($td.text());
            }
        
            $td.removeClass(class_invalid);
            
            if ($td.is('.number') && !PsIs.number(val)) {
                $td.addClass(class_invalid);
            }
            if ($td.is('.required') && PsIs.empty(val)) {
                $td.addClass(class_invalid);
            }
        
            return val;
        },
        
        //Загрузка оригинального значения
        origVal: function($td) {
            if($td.data('original-value-stored')) return $td.data('original-value');//---
            if(this.isNew($td)) return null;
            if(this.hasInput($td)) return null;
            $td.data('original-value-stored', true);
            var val = this.val($td);
            $td.data('original-value', val);
            return val;
        },
        
        updateState: function($td) {
            $td.toggleClass('modified', this.isNew($td) || (this.val($td)!=this.origVal($td)));
            this.val($td);
        }
    }
    
    var TEXT_DIALOG = PsDialog.register({
        id: 'EditableTableTdEditDialog',
        ctxt: this,
        build: function(DIALOG, whenDone) {
            DIALOG.div.
            //append($('<h5>').html('Редактирование комментария')).
            append($('<textarea>'));
            whenDone(DIALOG);
        },
        onShow: function(DIALOG) {
            DIALOG.div.find('textarea').val(DIALOG.data.td.data(data_long_text)).change().focus();
        },
        doAction: function(DIALOG) {
            var text = $.trim(DIALOG.div.find('textarea').val());
            DIALOG.close();
            DIALOG.data.td.data(data_long_text, text).attr('title', text).html(text ? '[текст]' : '[пусто]').toggleClass('gray', !text).addClass('modified');
            UTIL.updateState(DIALOG.data.td);
        },
        wnd: {
            title: 'Текст',
            buttons: 'Перенести в грид'
        }
    });
    
    var APPEND = {
        // # YES - NO
        yn: function($td) {
            if (UTIL.hasInput($td)) return;//---
            var checked = UTIL.val($td);
            var $input = $('<input>').attr('type', 'checkbox').setChecked(checked).
            change(function() {
                $td.toggleClass('modified', $(this).isChecked()!=checked);
            });
            $td.empty().append($input);
        },
        
        // # SHORT TEXT
        text: function($td) {
            if (UTIL.hasInput($td)) return;//---
            var tdWidth = $td.width();
            var $input = $('<input>').val(UTIL.val($td)).appendTo($td.empty()).select();
            var dx = $input.outerWidth(true) - $input.width();
            $input.width(tdWidth - dx);
            $input.blur(function() {
                UTIL.updateState($td);
            });
        },
        
        // # DIALOG
        longText: function($td) {
            var text = UTIL.val($td);
            $td.data(data_long_text, text).attr('title', text).html(text ? '[текст]' : '[пусто]').toggleClass('gray', !text);
            $td.click(function() {
                TEXT_DIALOG.open({
                    td: $td
                });
            });
        },

        autoAppend: function($td) {
            UTIL.origVal($td);

            if (UTIL.isText($td)) {
                this.text($td);
            } else
            if (UTIL.isLongText($td)) {
                this.longText($td);
            } else
            if (UTIL.isYn($td)) {
                this.yn($td);
            }
            
            UTIL.updateState($td);
        }
    }
    
    $table.find('td.editable').each(function() {
        var $td = $(this);

        if (UTIL.isLongText($td)) {
            $td.data(data_long_text, $.trim($td.text()));
        }
        
        APPEND.autoAppend($td);
        
        if (UTIL.isText($td)) {
            $td.addClass('text').click(function() {
                APPEND.autoAppend($td);
                $td.find('input').focus().blur(function() {
                    $td.html($.trim($(this).val()));
                    UTIL.updateState($td);
                });
            });
        }
    });
    
    //Подсчитаем кол-во столбцов в таблице, эмулируем клик по полям .text и установим одинаковую высоту столбцам
    var tdsCnt = $table.find('tr:has(td):first td').size();
    
    for(var i=1; i<=tdsCnt; i++) {
        var $tds = $table.find('td:nth-child('+i+')');
        var $texts = $tds.filter('.editable.text');
        $texts.sameWidth().click();
        $tds.sameHeight();
        $texts.find('input').blur();
    }
    
    //public
    var CONTROLLER = {
        //Получение моделей данных. Можно получить все данные, либо только модифицированные
        models: function(all) {
            var modified = [];
            $table.find('tr.new,tr:has(td)').each(function() {
                var $tr = $(this);
                if(!all && !$tr.hasChild('td.modified')) return;//---
                var model = {};
                $tr.children('td').each(function() {
                    var $td = $(this);
                    var tdid = $td.data('tdid');
                    if(!tdid) return;// У свойства нет ID ---
                    model[tdid] = UTIL.val($td);
                });
                modified.push(model);
            });
            return modified;
        }
    }
    
    /* Кнопки управления */
    var $CTRL = $table.next('div.ctrl:has(button)');
    if ($CTRL.isEmptySet()) return;//---
    var $buttons = $CTRL.find('button');
    
    var UM = new PsUpdateModel(null, function() {
        $buttons.uiButtonDisable();
    }, function() {
        $buttons.uiButtonEnable();
    });
    
    //Кнопка - сохранить страницу
    $buttons.filter('.save').button().click(function() {
        if(!$table.hasChild('td.modified')) {
            InfoBox.popupWarning('Нет изменений');
            return;//---
        }
        if ($table.hasChild('td.'+class_invalid)) {
            InfoBox.popupError('Требуется исправить ошибки');
            return;//---
        }
        if (!$.isFunction(options.saver)) {
            InfoBox.popupError('Не задана функция сохранения');
            return;//---
        }
        
        PsDialogs.confirm(options.msg, function() {
            UM.start();
            var onDone = PsUtil.once(function(err) {
                if (err && err!='OK') {
                    InfoBox.popupError('Ошибка сохранения: ' + err);
                    UM.stop();
                } else {
                    InfoBox.popupSuccess('Грид успешно сохранён');
                    if (options.reload) {
                        locationReload();
                    } else {
                        $table.find('td.modified').removeClass('modified');
                        UM.stop();
                    }
                }
            });
            
            //Вызываем сохранение
            options.saver.call(options.ctxt, CONTROLLER, onDone);
        });
        
    });
    
    //Кнопка - добавить новую строку в грид
    $buttons.filter('.add').button({
        text: false,
        icons: {
            primary: 'ui-icon-plus'
        }
    }).click(function() {
        $table.find('tr:last').clone(true, true).unbind().appendTo($table).
        addClass('new').find('td').unbind().empty().each(function() {
            var $td = $(this);
            $td.data(data_long_text, null);
            if(!$td.data('tdid') || $td.is('.noedit')) return;//---
            $td.addClass('editable');
            APPEND.autoAppend($td);
        }).end().find('input:first').focus();
        //Выбросим событие изменения
        $table.trigger(PsEvents.TABLE.modified);
    });
    
    //Кнопка - перезагрузить страницу
    $buttons.filter('.reload').button({
        text: false,
        icons: {
            primary: 'ui-icon-refresh'
        }
    }).click(function() {
        $buttons.uiButtonDisable();
        locationReload();
    });
}


/*
 * ВРЕМЕННЫЕ ШКАЛЫ
 */

/*
 * EVENT
 *

getID
isInstant
isImprecise
getStart
getEnd
getLatestStart
getEarliestEnd
getEventID
getText
getDescription
getImage
getLink
getIcon
getColor
getTextColor
getClassName
getTapeImage
getTapeRepeat
getTrackNum
getWikiURL
getWikiSection
setWikiInfo
fillDescription
fillWikiInfo
fillTime
fillInfoBubble

 */
var PsTimeLine = {
    logger: PsLogger.inst('PsTimeLine').setTrace()/*.disable()*/,
    
    //Константы вынесены сюда, так как на момент инициализации [Timeline.DateTime.] может быть не подключен
    DateTime: {
        MILLISECOND: 0,
        SECOND: 1,
        MINUTE: 2,
        HOUR: 3,
        DAY: 4,
        WEEK: 5,
        MONTH: 6,
        YEAR: 7,
        DECADE: 8,
        CENTURY: 9,
        MILLENNIUM: 10
    },
    
    TL: [],
    
    create: function(settings) {
        var options = {
            ctxt: null,     //Контекст вызова всех функций
            div: null,      //Контейнер
            bands: [],      //Массив с настройками шкал, Timeline.createBandInfo будет вызван внутри.
            data: {         //Дополнительные данные для постройки временной шкалы
                lident: null //Идентификатор строителя временной шкалы
            },
            tds: [],        //Кастомные столбцы таблиц function($td, tlEvent, CONTROLLER)
            preProcessData: function(jsonData) {
            //Вызывается для возможности обработать данные перед загрузкой в шкалу (например - можно проставить ссылки)
            },
            onHeaderClicked: function(clickEvent, tlEvent, CONTROLLER) {
            //Функция будет вызвана по клику на bubble
            }
        }
        $.extend(options, settings||{});
        
        var title = '(' + options.data.lident + ')';
        
        var logger = PsLogger.inst('PsTimeLine ' + title).setTrace();
        
        logger.logInfo('Строим шкалу.');
        
        //Див, в который будет построена временная шкала
        var $DIV = PsIs.string(options.div) ? $($.trim(options.div).ensureStartsWith('#')) : $(options.div);
        if ($DIV.isEmptySet()) {
            logger.logWarn('Не найден контейнер, пропускаем.');
            return null;//---
        }
        
        //Id дива
        var divId = $DIV.addClass('ps-tl').ensureIdIsSet('TL').attr('id');
        
        //Отлогируем параметры контейнера
        logger.logInfo('Контейнер id={}, height={}.', divId, $DIV.css('height'));
        
        var err2div = function(err) {
            logger.logError(err);
            $DIV.replaceWith(InfoBox.divError(err));
        }
        
        if (!PsUtil.hasGlobalObject('Timeline')) {
            err2div('Модуль для работы с временными шкалами отключён.');
            return null;//--- 
        }
        
        //Источник данных
        var eventSource = new Timeline.DefaultEventSource();
        
        //Параметры временных шкал
        var bands = options.bands;
        if (PsIs.empty(bands)) {
            //Зададим стандартные параметры
            bands = [{
                width:          "85%",
                intervalUnit:   Timeline.DateTime.DECADE,
                intervalPixels: 100
            },{
                overview:       true,
                width:          "15%",
                intervalUnit:   Timeline.DateTime.CENTURY,
                intervalPixels: 200
            }];
        }
        
        //BandInfo, созданное с помощью Timeline.createBandInfo
        var bandInfos = [];
        bands.walk(function(band, idx) {
            /*
             * Устанавливаем наш источник данных
             */
            band.eventSource = eventSource;
            /*
             * overview - признак предпросмотра (не показываются названия событий и по ним нельзя кликнуть).
             * Если данный признак не установлен, то ставим его самостоятельно для всех событий не главной (с индексом 0) шкалы.
             */
            band.overview = band.hasOwnProperty('overview') ? band.overview : idx>0;
            /*
             * Формируем объект и сохраняем в коллекцию
             */
            bandInfos.push(Timeline.createBandInfo(band));
        });
        
        bandInfos.walk(function(tlBand, idx) {
            if (idx > 0) {
                tlBand.syncWith = idx-1;
                /*
                 * highlight - признак подсвечивания в текущей шкале участка, соответствующего участку основной (с индексом 0) шкалы.
                 */
                tlBand.highlight = true;
            }
        });    
        
        //Основной объект - менеджер для работы с временнОй шкалой
        var TL = Timeline.create($DIV.get(0), bandInfos);
        
        //ФИЛЬТР
        var FILTER = {
            filtered: null,
            regExp: null,
            matchers: [],
            listeners: [],
            
            _matcher: function(evt) {
                var text = evt.getText();
                var take = !FILTER.regExp || FILTER.regExp.test(text);
                FILTER.matchers.walk(function(matcher) {
                    take = take && matcher.call(options.ctxt, evt);
                });
                return take;
            },
            _notifylistener: function(listener) {
                if ($.isFunction(listener) && PsIs.array(FILTER.filtered)) {
                    listener(FILTER.filtered);
                }
            },
            setText: function(text) {
                text = $.trim(text);
                this.regExp = text ? new RegExp(text, "i") : null;
                this.doApply();
            },
            
            addMatcher: function(matcher) {
                if(!$.isFunction(matcher)) return;//---
                if(FILTER.matchers.contains(matcher)) return;//---
                FILTER.matchers.push(matcher);
                FILTER.doApply();
            },
            removeMatcher: function(matcher) {
                if(!$.isFunction(matcher)) return;//---
                FILTER.matchers.removeValue(matcher);
                FILTER.doApply();
            },
            
            doApply: function() {
                var filterMatcher = FILTER._matcher;
                
                for (var i = 0; i < bandInfos.length; i++) {
                    TL.getBand(i).getEventPainter().setFilterMatcher(filterMatcher);
                }
                TL.paint();
                
                /*
                 * Нам нужно получить все видимые события. Хорошо бы их получить из eventSource,
                 * но такого метода я не нашёл - пробежимся по событиям сами.
                 */
                var iterator = eventSource.getAllEventIterator();
                
                FILTER.filtered = [];
                
                while (iterator.hasNext()) {
                    var evt = iterator.next();
                    if (filterMatcher(evt)) {
                        FILTER.filtered.push(evt);
                    }
                }
                
                FILTER.listeners.walk(FILTER._notifylistener);
            },
            addOnFilterListener: function(listener) {
                this.listeners.push(listener);
                this._notifylistener(listener);
            }
        }
        
        //КОНТЕЙНЕР С ТАБЛИЦЕЙ И ФИЛЬТРОМ
        var $BOX = $('<div>').addClass('ps-tl-controller').insertAfter($DIV);
        
        //ЗАГРУЗЧИК информации об элементах временной шкалы
        var DATALOADER = {
            CONTROLLER: null,
            DIV: null,
            
            loading: false,
            wait: null,
            loaded: {},
            show: function(evt, data, text) {
                data = data || {};
                data.eident = evt.custom.ident;
                var hash = PsObjects.toString(data);
                logger.logInfo('Поступила заявка на показ объекта [{}], параметры: [{}].', text, hash);
                this.wait = {
                    data: data,
                    text: text || evt.title,
                    hash: hash
                };
                this.doShow();
            },
            
            hide: function() {
                if (!this.CONTROLLER) {
                    return;//---
                }
                this.DIV.hide();
                $BOX.show();
            },
            
            doShow: function() {
                if (!this.CONTROLLER) {
                    return;//Загрузчик не готов
                }
                if (this.loading) {
                    return;//Сейчас загружаются данные
                }
                if (!this.wait) {
                    return;//Нечего показывать
                }
                
                this.loading = true;
                
                this.DIV.children().hide();
                this.DIV.show();
                this.CONTROLLER.closeBubbles();
                $BOX.hide();
                
                var objWait = this.wait;
                var objHas = this.loaded[objWait.hash];
                if (objHas) {
                    logger.logInfo('Объект [{}] показан.', objWait.text);
                    objHas.div.show();
                    this.wait = null;
                    this.loading = false;
                    return;//---
                }
                
                logger.logInfo('Начинаем загрузку объекта [{}].', objWait.text);
                
                var $loading = loadingMessageDiv(objWait.text).appendTo(this.DIV);
                
                var loadData = {};
                loadData['ctxt'] = this;
                loadData[defs.TIMELINE_LOADING_MARK] = 1;
                $.extend(loadData, options.data, objWait.data);
                
                AjaxExecutor.execute('TimeLine',
                    loadData, 
                    function(res) {
                        return {
                            ok: true,
                            ctt: res
                        }
                    }, function(err) {
                        return {
                            ok: false,
                            err: err
                        }
                    }, function(resp) {
                        var $head = $('<div>').addClass('post_head');
                        var $div = $('<div>').append($head);
                        
                        if (resp.ok) {
                            $head.html(objWait.text);
                            $div.append(resp.ctt);
                            logger.logInfo('Объект [{}] успешно загружен.', objWait.text);
                        
                        }
                        
                        if(!resp.ok) {
                            $head.html('Произошла ошибка');
                            $div.append(InfoBox.divError(resp.err));
                            logger.logInfo('Объект [{}] загружен с ошибкой: [{}].', objWait.text, resp.err);
                        }
                        
                        this.loaded[objWait.hash] = {
                            div: $div
                        }
                        
                        $head.append(crCloser(function(){
                            DATALOADER.hide();
                        }));
                        
                        $loading.remove();
                        $div.appendTo(this.DIV);
                        
                        this.loading = false;
                        this.doShow();
                    });
            },
            
            /*
             * Функция будет вызвана после отображения временной шкалы, когда всё будет готово для работы.
             * На вход будет передан CONTROLLER, который позволит управлять элементами отображения.
             */
            setReady: function(CONTROLLER) {
                this.CONTROLLER = CONTROLLER;
                this.DIV = $('<div>').addClass('ps-tl-data-holder').hide().insertAfter($BOX);
                this.doShow();
            }
        }
        
        //КОНТРОЛЛЕР
        var CONTROLLER = {
            //Бокс с фильтрами, таблицей событий и т.д. Ссылку дадим для возможности добавить пользовательские классы.
            BOX: $BOX,
            
            //Метод центрирует данные. Мы работаем в предположении, что все bind`ы связаны. Восновном так и будет.
            toDate: function(date) {
                var parsed = Timeline.DateTime.parseGregorianDateTime(date);
                if (PsIs.empty(parsed) || parsed=='Invalid Date') {
                    logger.logError('Ошибка позиционирования на дату [{}], обработанная дата: [{}].', date, parsed);
                    return;//---
                }
                logger.logTrace('Позиционируем на дату: ' + date);
                TL.getBand(0).setCenterVisibleDate(Timeline.DateTime.parseGregorianDateTime(date));
            },
            
            //Добавляет фильтр
            addMatcher: FILTER.addMatcher,
            
            //Удаляет фильтр
            removeMatcher: FILTER.removeMatcher,
            
            //Скрывает bubbles
            closeBubbles: function() {
                for (var i = 0; i < bandInfos.length; i++) {
                    TL.getBand(i).closeBubble();
                }
            },
            
            //Показывает содержимое элемента
            showItem: function(evt, data, text) {
                DATALOADER.show(evt, data, text);
            }
        }
        
        /*
         * Функция onEventsLoaded вызывается, когда события успешно загружены и див стал виден
         */
        
        var onEventsLoaded = function(jsonData) {
            logger.logInfo('Обрабатываем событие onEventsLoaded.');
            
            //Предобработаем загруженные данные
            options.preProcessData(jsonData);
            
            //Загрузим события во временную шкалу
            eventSource.loadJSON(jsonData, '.');
            
            //Сохраним ссылку на временную шкалу
            PsTimeLine.TL.push(TL);
            
            //Слушатель клика по заголовку
            var addClickListener = function(eId) {
                var event =  eventSource.getEvent(eId);
                if(!event) return; // --- Такого события вообще нет
                var eLink = $.trim(event.getLink());
                if(!eLink) return; // --- У события нет url
                
                logger.logInfo('Добавляем слушатель клика по заголовку события [{}], url: [{}].', event.getText(), eLink);
                
                var added = false;
                //Пробежимся по всем открытым bubbles (если их несколько) и подвяжемся на наш
                $('div.timeline-event-bubble-title a').each(function() {
                    if(added) return;//---
                    var $a = $(this);
                    var aLink = $.trim($a.attr('href'));
                    added = eLink==aLink;
                    logger.logInfo('Bubble Url: [{}]. Слушатель {}добавлен.', aLink, added ? '' : 'НЕ ');
                    if(!added) return;//---
                    $a.click(function(clickEvent) {
                        logger.logDebug('Произведён клик по заголовку события [{}]', event.getText());
                        CONTROLLER.closeBubbles();
                        //Обработчику будет передан именно event._obj - это наш исходный массив с данными события.
                        options.onHeaderClicked.call(options.ctxt, clickEvent, event._obj, CONTROLLER);
                    });
                });
            }
            for (var i = 0; i < bandInfos.length; i++) {
                TL.getBand(i).getEventPainter().addOnSelectListener(addClickListener);
            }
            
            /*
             * Строим таблицу событий и добавляем фильтр
             */
            //1. Фильтр
            $('<div>').addClass('filter').append($('<input>').keyup(function() {
                FILTER.setText($(this).val());
            }).attr('placeholder', 'Фильтр')).appendTo($BOX);
            
            
            //2. Таблица
            var $table = $('<table>').appendTo($BOX);
            jsonData.events.walk(function(event) {
                var $tr = $('<tr>').data('title', event.title);
                
                //<td>Название элемента, ссылка для позиционирования на дату</td>
                $('<td>').addClass('main').append(crA().html(event.title).clickClbck(function() {
                    CONTROLLER.toDate(event.start);
                })).appendTo($tr);
                
                //<td>Интервал дат</td>
                $('<td>').addClass('dates').html(event.custom.interval).appendTo($tr);
                
                //<td>Пользовательсткие</td>
                options.tds.walk(function(callback) {
                    if(!$.isFunction(callback)) return; //---
                    var $td = $('<td>');
                    callback.call(options.ctxt, $td, event, CONTROLLER);
                    $td.appendTo($tr);
                });
                
                $table.append($tr);
            });
            
            function colorTable() {
                $table.find('tr:not(.hidden)').removeClass('odd').filter('tr:even').addClass('odd');
            }
            
            FILTER.addOnFilterListener(function(vis) {
                $table.find('tr').addClass('hidden').each(function() {
                    var $tr = $(this);
                    if (vis.contains($tr.data('title'), function(text, evt){
                        return text==evt.getText();
                    })) {
                        $tr.removeClass('hidden');
                    }
                });
                colorTable();
            });
            colorTable();
            
            //Выполним layout
            TL.layout();
            
            //Определим центральное событие. На дату его начала будет позиционироваться шкала.
            var centralItem = PsArrays.centralItem(jsonData.events);
            var centralDate = centralItem ? centralItem.start : new Date();
            var centralTitle = centralItem ? centralItem.title : '';
            
            logger.logInfo('Дата позиционирования: {}{}.', centralDate, centralTitle ? ' ['+centralTitle+']': '');
            
            CONTROLLER.toDate(centralDate);
            
            //Оповестим загрузчик, что у нас всё готово к работе
            DATALOADER.setReady(CONTROLLER);
        }
        
        /*
         * Загружаем события

           TL.loadJSON(url, function(jsonData, url) {
               eventSource.loadJSON(jsonData, url);
           });
         */
        
        TL.showLoadingMessage();
        AjaxExecutor.execute('TimeLine',
            options.data, 
            function(jsonData) {
                var evtCnt = PsIs.array(jsonData.events) ? jsonData.events.length : 0;
                
                logger.logInfo('События успешно загружены, количество: {}. {}', evtCnt, evtCnt ? '' : 'Пропускаем дальнейшую обработку.');
                
                if(!evtCnt) return;//---
                
                PsJquery.executeOnElVisible('#'+divId, function() {
                    onEventsLoaded.call(PsTimeLine, jsonData);
                });
            }, function(err) {
                err2div('Ошибка загрузки данных для временной шкалы '+title+': ' + err);
            }, function() {
                TL.hideLoadingMessage();
            });
        
        return CONTROLLER;
    },
    
    resize: function() {
        return;//Сейчас без ресайза всё происходит симпатичнее
        this.logger.logInfo('Выполняем resize всех временных шкал.');
        this.TL.walk(function(TL) {
            TL.layout();
        });
    }
}

/*
 * TimePickers
 * (http://trentrichardson.com/examples/timepicker/)
 */
$.datepicker.setDefaults({
    showOn: 'both',
    buttonImageOnly: false,
    buttonImage: '/resources/images/icons/figure/calendar-blue.png',
    changeMonth: true,
    changeYear: true,
    yearRange: 'c-30:c+30',
    stepMonths: 1,
    dateFormat: 'dd-mm-yy'
});

$.timepicker.setDefaults({
    showTimezone: false,
    useLocalTimezone: false,
    defaultTimezone: PsTimeHelper.getDatepickerPresentation(-(new Date().getTimezoneOffset())*60),
    timeFormat: 'HH:mm',
    pickerTimeFormat: 'HH:mm z'
});


//Всегда работаем во временной зоне браузера
function PsDateTimePickerManager($field) {
    var elId = $field.ensureIdIsSet('DP').attr('id');
    
    PsJquery.executeOnElVisible('#'+elId, function() {
        var $picker = $('<input>').addClass('ps-datetime-picker-box').insertAfter($field);
        var $info = $('<span>').addClass('ps-datetime-picker-info').insertAfter($picker);
        
        //Функция вычисляет промежуток между прошлым и текущим состоянием
        var lastDelta = null;
        var lastEnabled = null;
        var updateInfo = function() {
            //Обработка редактируемости поля выбора даты
            if ($field.isEnabled() !== lastEnabled) {
                lastEnabled = $field.isEnabled();
                $picker.uiDatepickerSetEnabled(lastEnabled);
            }
            
            //Обновление информации о прошедшем интервале
            var val = $field.valEnsureIsNumber();
            if(!PsIs.number(val)) {
                $info.hide();
                return; //---
            }
            
            var delta = PsMath.round(new Date().getTime()/1000-val);
            if (lastDelta==delta) {
                return; //---
            }
            lastDelta = delta;
            
            var past = delta > 0;
            var formatted = PsTimeHelper.formatDHMS(delta);
            $info.
            html((past ? '$ назад' : 'через $').replace('$', formatted)).
            removeClass('past future').addClass(past ? 'past' : 'future').
            attr('title', (past ? 'Событие уже наступило' : 'Событие наступит в будущем')).
            show();
        }
        
        //Равнение на $field
        var sync2field = function() {
            var val = $field.valEnsureIsNumber();
            var date = null;
            if (PsIs.number(val)) {
                date = new Date();
                date.setTime(val*1000);
            }
            $picker.datetimepicker('setDate', date);
            updateInfo();
        }
        
        //Равнение на $picker
        var sync2picker = function() {
            var date = $picker.datetimepicker('getDate');
            var val = null;
            if (date && (date instanceof Date)) {
                val = Math.round(date.getTime()/1000);
            }
            $field.val(val).change();
        }
        
        /*
         * !!!ВАЖНО!!! На событие change подписывать нельзя, так как поля начнут взаимно переустанавливать друг-друга.
         */
        
        /*
         * Наконец создаём datetimepicker, после уже не получится передать onSelect.
         * Подвешиваем слушатели на все события, которые приведут к вызову sync2picker.
         */
        $picker.datetimepicker({
            onSelect: sync2picker
        });
        $picker.keyup(sync2picker);
        
        /*
         * Подвешиваем слушатели на все события, которые приведут к вызову sync2field.
         */
        $field.keyup(sync2field);
        $field.change(updateInfo);
        FormHelper.bindOnReset($field, sync2field);
        
        sync2field();
        
        PsGlobalInterval.subscribe(1000, updateInfo);
    });
}

/*
 * Навеяно: 
 * http://simile-widgets.googlecode.com/svn/timeline/tags/latest/src/webapp/examples/jfk/jfk.html
 *
 
    var regexes = [];
    var hasHighlights = false;
    for (var x = 1; x < tr.cells.length - 1; x++) {
        var input = tr.cells[x].firstChild;
        var text2 = cleanString(input.value);
        if (text2.length > 0) {
            hasHighlights = true;
            regexes.push(new RegExp(text2, "i"));
        } else {
            regexes.push(null);
        }
    }
    var highlightMatcher = hasHighlights ? function(evt) {
        var text = evt.getText();
        var description = evt.getDescription();
        for (var x = 0; x < regexes.length; x++) {
            var regex = regexes[x];
            if (regex != null && (regex.test(text) || regex.test(description))) {
                return x;
            }
        }
        return -1;
    } : null;
 */


/**
 * Класс, позволяющий осуществлять прокрутку страницы вверх/вниз
 * 
 * headerHeight - высота, которая не считается зоной прокрутки
 * 
 */
function PageScroller(headerSelector, carrierSelector) {
    if (this.added) return;//---
    this.added = true;
    
    if($(carrierSelector).isEmptySet()) return;//На случай ошибки
    
    var $bg = $('<div>').addClass('ps-page-scroller-bg');
    var $div = $('<div>').attr('id', 'ps-page-scroller').append($bg).appendToBody().disableSelection();
    
    //Минимальная прозрачность блока с фоном
    var minOpacity = $bg.cssDimension('opacity', 0.1);
    
    //Высота заголовка страницы
    var headerHeight = headerSelector ? $(headerSelector).outerHeight(true) : 0;
    
    //Глобальные переменные, которые можно использовать для управления видом страницы
    var hovered = false;
    var canScroll = false;
    var scrollTop = null;
    var canScrollDown = false;
    
    //Функции показа/скрытия левой панели
    var bgShown = false;
    var tryShowBg = function() {
        if (bgShown || !canScroll || !hovered) return;//---
        bgShown = true;
        $bg.stop(true, false).css('opacity', minOpacity).animate({
            opacity: 1
        }, 300);
    }
    
    var tryHideBg = function() {
        if (!bgShown || (canScroll && hovered)) return;//---
        bgShown = false;
        $bg.stop(true, false).animate({
            opacity: minOpacity
        }, 300);
    }
    
    //Основная задача метода - определить, можем ли мы прокручивать экран и в какую сторону.
    //В соответствии с этим мы показываем соответствующую стрелочку (или скрываем её совсем) и показываем/скрываем задний план.
    var recalcVis = function() {
        //Признак направления прокрутки
        var scrollTopBefore = scrollTop;
        scrollTop = $(window).scrollTop() > headerHeight;
        
        //Признак возможности прокрутки
        var canScrollBefore = canScroll;
        canScroll = canScrollDown || scrollTop;
        
        if (canScroll) {
            if (!canScrollBefore) {
                //Теперь можем скролить
                $div.addClass('scrollable');
            }
            if ((scrollTopBefore===null || scrollTopBefore) && !scrollTop) {
                //Теперь скролим вниз
                $div.removeClass('up').addClass('down');
            }
            if ((scrollTopBefore===null || !scrollTopBefore) && scrollTop) {
                //Теперь скролим вверх
                $div.removeClass('down').addClass('up');
            }
            tryShowBg();
        } else {
            if (canScrollBefore) {
                //Теперь не можем скролить
                $div.removeClass('scrollable up down');
            }
            tryHideBg();
        }
    
    }
    
    $div.click(function() {
        if(!canScroll) return;//---
        if (scrollTop) {
            PsScrollManager.storeWndScroll();
            PsScroll.scrollTop(0);
            canScrollDown = true;
            canScroll = canScrollDown || scrollTop
        } else {
            PsScrollManager.restoreWndScroll();
        }
    }).hover(function() {
        hovered = true;
        tryShowBg();
    }, function() {
        hovered = false;
        tryHideBg();
    });
    
    //Основная задача метода - вычислить размеры блока-подложки, при наведении на который можно осуществить прокрутку
    var onResize = function() {
        /*
         * Минимальная ширина блока прокрутки задаётся на css как стиль для .ps-page-scroller-bg.
         * Получать её будем именно здесь, когда страница уже загружена.
         */
        var boxWidth = $bg.width();
        var windowWidth = $(window).width();
        var carrierWidth = $(carrierSelector).width();
        var allowedWidth = Math.max(Math.round((windowWidth - carrierWidth) / 2), boxWidth);
        
        //Установим ширину блока-подложки
        $div.width(allowedWidth);
        
        recalcVis();
    }
    
    //Если пользователь сам прокрутил мышкой вверх, то мы забываем о том, где он кликнул по стрелке перехода в самый верх
    PsMouseWheelHelper.addListener(function(dy) {
        canScrollDown = canScrollDown && dy<0;
    });
    
    $(window).resize(onResize).scroll(recalcVis);
    
    //Пересчитаем размер полосы прокрутки
    onResize();
    
    //Ещё раз запустим обработку, так как прокрутка ведёт себя крайне странно в последних обновлениях firefox.
    PsUtil.scheduleDeferred(onResize, null, 1000);
}

/*
 * ИНДЕКСИРОВАННАЯ ТАБЛИЦА
 */
function PsIndexedGrid($TABLE) {
    var $THEAD = $TABLE.find('thead');
    var $TBODY = $TABLE.find('tbody');
    
    if ($THEAD.isEmptySet() || $TBODY.isEmptySet()) return;//---
    
    if(!$THEAD.hasChild('.tbl-idx')) {
        $THEAD.find('tr').prepend($('<th>').addClass('tbl-idx'));
    }
    
    var buildIndex = function() {
        $TBODY.find('tr:not(:has(.tbl-idx))').prepend($('<td>').addClass('tbl-idx'));
        $TBODY.find('tr td.tbl-idx').html(function(i) {
            return 1+i;
        });
    }

    //Подпишемся на событие сортировки или изменения таблицы, так как после нужно перестноить индексы
    $TABLE.on(PsEvents.TABLE.sorted, buildIndex);
    $TABLE.on(PsEvents.TABLE.modified, buildIndex);

    buildIndex();
}

/*
 * СОРТИРУЕМАЯ ТАБЛИЦА
 */
function PsSortableGrid($TABLE) {
    var $THEAD = $TABLE.find('thead');
    var $TBODY = $TABLE.find('tbody');
    
    if ($THEAD.isEmptySet() || $TBODY.isEmptySet()) return;//---
    var initSorters = function() {
        //Удалим имеющиеся сортеры
        $THEAD.find('th:has(a.sorter)').each(function() {
            var $th = $(this);
            $th.html($.trim($th.find('.toggler').remove().end().text()));
        });
        
        //Получим все строки
        var $trs = $TBODY.children('tr');
        
        if ($trs.isEmptySet()) return;//---
        
        //Пробежимся по сортируемым заголовкам и добавим сортеры
        $THEAD.find('th').each(function(idx) {
            var $TH = $(this);
            //Столбец не является сортируемым
            if ($TH.is('.nosort')) return;//---
            var TEXT = $.trim($TH.text());
            //У заголовка нет текста? Пропускаем!
            if(!TEXT) return;//---
            
            //Пробегаемся по всему столбцу и определяем тип сортировки
            var sortFunction = 0; //0 - не определена, 1 - число, 2 - строка
            $trs.each(function() {
                var text = $($(this).children('td').get(idx)).text();
                if (PsIs.number(text)) {
                    sortFunction = 1;
                    return true;//---
                }
                if(!PsIs.empty(text)) {
                    sortFunction = 2;
                    return false;//Мы нашли текст - всё, сортируем как текст
                }
                return true;//---
            });
            
            switch (sortFunction) {
                case 1:
                    //Число
                    sortFunction = function(t1, t2, sign) {
                        if(!t1) return -1;
                        if(!t2) return 1;
                        if(t1==t2) return 0;
                        return 1*t1<1*t2 ? sign : -sign;
                    }
                    break;
                
                case 2:
                    //Строка
                    sortFunction = function(t1, t2, sign) {
                        if(!t1) return -1;
                        if(!t2) return 1;
                        if(t1==t2) return 0;
                        return t1<t2 ? sign : -sign;
                    }
                    break;
                default:
                    return;//По этому столбцу нельзя сортировать
            
            }
            
            var $sorter = crA().addClass('sorter').html(TEXT+'&nbsp;'+PsHtml.span('—', 'toggler'));
            var doSort = function(sign) {
                //Установим тип сортировки у нашего сортера
                $sorter.data('sort-type', sign);
                //1=ASC, 0=NOSORT, -1=DESC
                $sorter.find('.toggler').html(sign == 0 ? '—' : (sign < 0 ? '∨' : '∧'));
                
                if (sign==0) return;//---
                
                //Отменим активные сортировщики
                $THEAD.find('th a.sorter.active').removeClass('active');
                //Сделаем наш тип сортировки - активным
                $sorter.addClass('active');
                
                //Не будем кешировать строки, чтобы не отметать возможность добавления их в рантайме
                $TBODY.children('tr').sort(function(tr1, tr2) {
                    var t1 = $.trim($($(tr1).children('td').get(idx)).text());
                    var t2 = $.trim($($(tr2).children('td').get(idx)).text());
                    return sortFunction(t1, t2, sign);
                }).each(function() {
                    $TBODY.prepend(this);
                });
                
                //Выбросим событие о том, что таблица была отсортирована
                $TABLE.trigger(PsEvents.TABLE.sorted);
            }
            
            $sorter.clickClbck(function() {
                var sign = $(this).data('sort-type');
                sign = PsIs.number(sign) && sign!=0 ? -1*sign : -1;
                doSort(sign);
            });
            
            $TH.empty().append($sorter);
            
            doSort(strToInt($TH.data('sort-type'), 0));
        
        });
    }
    
    initSorters();
    
    $TABLE.on(PsEvents.TABLE.modified, initSorters);
}


//Выполним всё то, что мы можем выполнить для всех страниц
$(function() {
    //РЕБУСЫ
    $('.ps-math-rebus-holder').livequery(function(){
        MathRebusPresentation($(this));
    });
    
    //СОРТИРОВКА+СОПОСТАВЛЕНИЕ
    $('.ps-sortable-compare').livequery(function(){
        PsSortableCompare($(this));
    });
    
    //ПРОГРЕСС
    $('.ps-progress').livequery(function() {
        $(this).psProgressbarUpdate();
    });
    
    //БЛОКИ СКРЫТОГО ТЕКСТА
    $('.ps-hidden-box').livequery(function() {
        new PsHiddenBox($(this));
    });

    
    //LEGEND EXPANDER
    $('legend.toggle').each(function() {
        new FormLegendToggler($(this));
    });
    
    //TEXTAREA
    $('textarea:visible').livequery(function() {
        var $ta = $(this);
        if(!$ta.data('ps-textarea-processed')) {
            $ta.data('ps-textarea-processed', true);
            new PsTextareaManager($(this));
        }
    });
    
    //TABS
    $('.ps-tabs').livequery(function(){
        new PsTabPanelManager($(this));
    });
    
    //TABLES
    $('table').livequery(function() {
        var $table = $(this);
        //Порядок не менять! Иначе при добавлении столбца в .indexed некорректно будет работать .sortable
        if ($table.is('.indexed')) {
            new PsIndexedGrid($table);
        }
        if ($table.is('.sortable')) {
            new PsSortableGrid($table);
        }
    });
    
    //CSS HINTS
    $('[data-hint][title]').livequery(function(){
        $(this).removeAttr('title');
    });
    
    //HOT KEYS
    PsJquery.on({
        item: 'a.ps-hotkey',
        click: function(e, $a) {
            e.preventDefault();
            PsHotKeysManager.process(getHrefAnchor($a));
        }
    });
    
    //ПОЛЕ ВЫБОРА ДАТЫ
    $('.ps-date-picker').datepicker();
    
    //ПОЛЕ ВЫБОРА ДАТЫ И ВРЕМЕНИ
    $('.ps-datetime-picker').each(function() {
        new PsDateTimePickerManager($(this));
    });
    
    //ЛЕЙБЛЫ
    $('label').live('click', function(e) {
        var $label = $(this);
        
        if (e.target!=this && $label.hasChild('.colorPicker-picker')) {
            //Мы кликнули не по лейблу, а по colorPicker - пропускаем обработку
            return;//---
        }
        
        var getTarget = function(items) {
            var $target = $label.find(items);/*Внутри label*/
            if ($target.isEmptySet()) {
                $target = $label.next(items);/*Следующий за label*/
            }
            if ($target.isEmptySet()) {
                $target = $label.siblings(items);/*Соседний с label*/
            }
            return $target;
        }
        
        var $cp = getTarget('.colorPicker-picker');
        if(!isEmpty($cp)) {
            $cp.click();
        } else {
            getTarget('input, textarea, select').focus();
        }
    }).disableSelection();

});