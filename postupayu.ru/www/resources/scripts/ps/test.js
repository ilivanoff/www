/*
1/sin(x+0.015)

calcInfo={dx: 0.001, evals: 205336}

Решений: 33

    [-80, -78.554816339745]
    [-78.5548163397447, -75.4132236861552]
    [-75.4132236861549, -72.2716310325654]
    [-72.2716310325651, -69.1300383789755]
    [-69.1300383789753, -65.9884457253858]
    [-65.9884457253856, -62.846853071796]
    [-62.8468530717957, -59.7052604182063]
    [-59.705260418206, -56.5636677646164]
    [-56.5636677646161, -53.4220751110266]
    [-53.4220751110264, -50.2804824574368]
    [-50.2804824574366, -47.138889803847]
    [-47.1388898038468, -43.9972971502573]
    [-43.997297150257, -40.8557044966674]
    [-40.8557044966671, -37.7141118430776]
    [-37.7141118430774, -34.5725191894878]
    [-34.5725191894876, -31.4309265358981]
    [-31.4309265358978, -28.2893338823082]
    [-28.2893338823079, -25.1477412287184]
    [-25.1477412287181, -22.0061485751287]
    [-22.0061485751284, -18.8645559215389]
    [-18.8645559215387, -15.7229632679491]
    [-15.7229632679488, -12.5813706143593]
    [-12.581370614359, -9.4397779607695]
    [-9.4397779607692, -6.2981853071798]
    [-6.2981853071794, -3.15659265359]
    [-3.1565926535896, -0.0150000000001]
    [-0.0149999999999, 3.1265926535897]
    [3.12659265359, 6.2681853071794]
    [6.2681853071798, 9.4097779607692]
    [9.4097779607695, 12.551370614359]
    [12.5513706143593, 15.6929632679488]
    [15.6929632679491, 18.8345559215387]
    [18.8345559215389, 20]
*/


$(function() {
    new PsIntervalAdapter(function() {
        $('#my-date-picker').datepicker("destroy").datepicker({
            changeMonth: 1,
            changeYear: 1,
            stepMonths: 1,
            /*numberOfMonths: 3,*/
            showButtonPanel: true,
            minDate: '20-10-2013',
            maxDate: '22-11-2013',
            beforeShowDay: function(date) {
                consoleLog(date);
                return [PsRand.bool(), '', PsRand.string(6)];
            }
        });
    }, 1000, true).start().stop();
});

$(function(){
    PsTimeLine.create({
        div: '#ps-timeline',
        bands: [{
            width:          "85%",
            intervalUnit:   PsTimeLine.DateTime.DECADE,
            intervalPixels: 100
        },{
            overview:       true,
            width:          "15%",
            intervalUnit:   PsTimeLine.DateTime.CENTURY,
            intervalPixels: 200
        }],
        data: {
            lident: 'poets'
        }
    });
});

$(function() {
    var STORE = PsLocalStore.inst('Test');
    var show = STORE.get('ShowRp', true);
    var set = function() {
        STORE.set('ShowRp', show);
        $('#mainPanel').toggleClass('RpHide', !show);
    }
    PsHotKeysManager.addListener('Ctrl+Alt+F', {
        f: function() {
            show = !show;
            set();
        },
        descr: 'Показать/скрыть правую панель',
        enableInInput: true,
        stopPropagate: true
    });
    set();
});

$(function() {
    $('.testmethods').each(function(){
        var $BODY = $(this);
        
        function disableAll() {
            $BODY.find('input').disable();
            $BODY.find('button').uiButtonDisable();
        }
        
        function enableAll() {
            $BODY.find('input').enable();
            $BODY.find('button').uiButtonEnable();
        }
        
        $BODY.children('li').each(function(){
            var $li = $(this);
            $li.find('button').button().click(function() {
                var $btn = $(this);
                
                if ($btn.is('.do')) {
                    disableAll();
                    
                    var data = {
                        method: $li.data('name'),
                        params: []
                    };
                    var br = false;
                    $li.find('input, select').each(function(){
                        var $input = $(this);
                        var val = $input.val();
                        br = br || $.trim(val)=='';
                        if(br) return; //---
                        data.params.push(val);
                    });
                    
                    AjaxExecutor.execute('TestAction', data, function(res) {
                        InfoBox.popupSuccess(res);
                    }, data.method, function() {
                        enableAll();
                    });
                }
                
                if ($btn.is('.clear')) {
                    $li.find('input, select').val('');
                }
            });
        });
    });
});


$(function() {
    var boxes = [];
    $('.demo-head').each(function(i) {
        var num = i+1;
        var id = 'l'+num;
        var $box = $(this);
        var title = $box.html();
        boxes.push({
            id: id,
            title: title
        });
        $box.prepend(PsHtml.span$(num+'. ')).attr('id', id);
        $box.toggleClass('first', num==1);
    });
    
    var $contents = $('.contents').hide();
    boxes.walk(function(ob) {
        var $a = crA('#'+ob.id, ob.title).html(ob.title);
        $contents.append($a);
    });
    $('.contents-toggler').disableSelection().click(function(){
        $contents.toggleVisibility();
    });
});


/*
 * КНОПКИ
 */
$(function() {
    var $buttons = $('.ps-ui-buttons').children();
    $buttons.first().
    button({
        label: 'Обычная кнопка',/*Какой текст показывать (если нет, то будет взять из value или html)*/
        text: true, /*Вообще - показываем ли текст, или только картинку*/
        icons: {
            primary: "ui-icon-gear",
            secondary: "ui-icon-triangle-1-s"
        }
    });
    
    var $set = $buttons.last();
    $set.children().
    first().button({
        text: false,
        icons: {
            primary: "ui-icon-carat-1-n"
        }
    }).
    next().button({
        text: false,
        icons: {
            primary: "ui-icon-carat-1-s"
        }
    }).
    next().button({
        text: false,
        icons: {
            primary: "ui-icon-carat-1-nw"
        }
    });
    $set.children().click(function() {
        InfoBox.popupInfo($(this).uiButtonIcons().primary);
    });
    $set.buttonset();
//$('.ps-ui-buttons').buttonset();
})

/*
 * Тест загрузки TeX-формул
 */
$(function() {
    return;
    var ta = new PsTimerAdapter(function(){
        var hash = 'f8301e0a9828ac6535195d672057c46c';
        MathJaxManager.decodeJax(hash, function(tex){
            alert(tex);
        });
    });
    ta.start(2000);
});


$(function() {
    //Аккордион
    $( "#accordion" ).accordion({
        collapsible: true,
        autoHeight: true,
        //event: "mouseover",
        icons: {
            "header": "ui-icon-plus", 
            "headerSelected": "ui-icon-minus"
        },
        header: '>div>h3',
        fillSpace: false
    });
    
    $( "#accordion" ).find(':checkbox').checkBoxesGroup();
    
    //Прогресс
    var seconds = 20;
    PsGlobalInterval.subscribe4Nseconds(seconds, function(last, past) {
        $('#progress-demo').psProgressbarUpdate(seconds, past);
    });
    
    //Прокрутка
    PageScroller(null, '#carrier');
});


/*
 * Сортируемый список
 */
$(function() {
    var $sortableUl = $('#ps-sortable');
    $sortableUl.children('li').sameHeight();
        
    $sortableUl.sortable({
        axis: 'y',
        distance: 5,
        placeholder: "placeholder",
        start: function(event, ui) {
            ui.placeholder.height(ui.item.height());
            ui.item.addClass('move');
        },
        stop: function(event, ui) {
            ui.item.removeClass('move');
        },
        change: function() {
            InfoBox.popupInfo('Sorted');
        }
    });

});
