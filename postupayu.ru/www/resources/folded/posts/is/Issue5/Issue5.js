/*Вычисление чётности, сравнение первого и второго методов*/
$(function() {
    var $BODY = $('#pt-eveness');
    
    var defSize = 4;
    var defMode = 10;
    
    var controller = new PsPyatnashki($BODY);
    controller.init(defSize, defMode);
    controller.setDisableOnFinish(true);
    
    function arr2str(arr, mark) {
        mark = PsArrays.toArray(mark);
        var str = '';
        arr.walk(function(num) {
            str += str ? ' ' : '';
            str += PsHtml.span(mark.contains(num) ? '<b>.</b>'.replace('.', num) : num, 'num');
        });
        return str;
    }
        
    function descr(info) {
        return PsHtml.div(info, 'descr');
    }
        
    var TP = new TabPanelController($BODY);
    TP.setUpdaters({
        m1: function($m) {
            //1 метод
            var $ol, i, desc, shifts, sum, chet;
        
            var info = controller.getInfo();
            var n = info.n;
        
            var ARR = info.arre.walk(function(num) {
                return num ? num : '&mdash;';
            });
        
            $ol = $('<ol>').attr('start', '0');
            $m.empty().append($ol);
        
            $ol.append($('<li>').html(descr('Выписываем текущую расстановку: ') + arr2str(ARR)));
        
            shifts = 0;
            for (i = 1; i < n*n; i++) {
                var tmp = ARR[i-1];
                if (tmp==i) continue; //Чисто на своём месте
            
                //Меняем
                ARR[ARR.indexOf(i)] = tmp;
                ARR[i-1] = i;
            
                desc = PsStrings.replaceOneByOne('Переставляем {} и {}:', '{}', [i, tmp]);
            
                $ol.append($('<li>').html(descr(desc) + arr2str(ARR, [i, tmp])));
                ++shifts;
            }
        
            sum = shifts+info.manh;
            chet = !(sum%2);
            desc = 'Кол-во перестановок: ' + shifts+ ', Манхэттенское расстояние для пустой ячейки: ' + info.manh + ', сумма:  ' +sum;
            PsHtml.div$(desc, 'final').insertBefore($ol);
            PsHtml.div$('Расстановка является ' + (chet ? 'чётной' : 'нечётной'), chet ? 'res chet' : 'res nechet').insertBefore($ol);
        },
        
        m2: function($m) {
            //2 метод
            var $ol, i, desc, shifts, sum, chet;
        
            var info = controller.getInfo();
            var n = info.n;
        
            var ARR = info.arre.walk(function(num) {
                return num ? num : '&mdash;';
            });

            $ol = $('<ol>').attr('start', '0');
            $m.empty().append($ol);
        
            var goalArr = [];
            for (i = 1; i < n*n; i++) {
                goalArr.push(i);
            }
            goalArr.push('&mdash;');

            $ol.append($('<li>').html(descr('Выписываем текущую и собранную расстановки:')+arr2str(ARR)+'<br>'+arr2str(goalArr)));
        
            var chains = [];
            var chain;
            shifts = 0;
            for (i = 1; i < n*n; i++) {
                chain = [i];
                while(goalArr.indexOf(i)!=ARR.indexOf(i)) {
                    var changeWith = goalArr[ARR.indexOf(i)];
                    var curIpos = ARR.indexOf(i);
                    ARR[ARR.indexOf(changeWith)] = i;
                    ARR[curIpos] = changeWith;
                    chain.push(changeWith);
                
                    desc = PsStrings.replaceOneByOne('Под {} находится {}, переставляем {} и {}:', '{}', [i, changeWith, i, changeWith]);
                
                    $ol.append($('<li>').html(descr(desc)+arr2str(ARR, [i, changeWith])+'<br>'+arr2str(goalArr)));
                    ++shifts;
                }
            
                chains.push(chain);
            }
        
            desc = 'Короткая запись: ';
            var marked = [];
            chains.walk(function(chain) {
                marked.walk(function(num){
                    chain.removeValue(num);
                }, true);
                if(chain.length==0) return;
                desc+='('+chain.join(', ')+')';
                marked.push(chain);
            });
        
            $ol.children('li').last().append(PsHtml.div$(desc, 'chain'));
        
            sum = shifts+info.manh;
            chet = !(sum%2);
            desc = 'Кол-во перестановок: ' + shifts+ ', Манхэттенское расстояние для пустой ячейки: ' + info.manh + ', сумма: ' + sum;
            PsHtml.div$(desc, 'final').insertBefore($ol);
            PsHtml.div$('Расстановка является ' + (chet ? 'чётной' : 'нечётной'), chet ? 'res chet' : 'res nechet').insertBefore($ol);

        }
    });
    
    function updateMethInfo() {
        TP.updateTab();
    }
    
    new ButtonsController($BODY, {
        on_dice: function() {
            controller.reinit();
            updateMethInfo();
        }
    });
    
    controller.setListener({
        move: function() {
            updateMethInfo();
        }
    });
    
    var $select = $BODY.find('.field_size select').val(defSize).change(function(){
        var cnt = strToInt($select.val());
        controller.init(cnt, defMode);
        updateMethInfo();
    });
    updateMethInfo();
});


/*Проверка второго правила определения решаемости с для поля 4x4*/
$(function(){
    
    var $BODY = $('#pt-method');
    
    var controller = new PsPyatnashki($BODY);
    controller.setDisableOnFinish(true);

    var $info = $BODY.children('.info');
    
    function updateMethInfo() {
        var info = controller.getInfo();
            
        var K = info.K;
        var inv = info.inv;
        var N = inv.length;
        var KpN = K+N;
        var even = info.even;

        $info.find('.K').html(K);
        $info.find('.N').html(N);
        $info.find('.KPN').html(KpN);
        
        $info.find('.res').html(even ? 'Решение есть' : 'Решения нет').removeClass('red green').addClass(even ? 'green' : 'red');

        var invs = '';
        inv.walk(function(item) {
            invs+=invs ? ', ' : '';
            invs+='<b>'+item[0]+'</b>'+item[1];
        });
        $info.find('.pt-inversions').html(invs);
    }
    
    new ButtonsController($BODY, {
        on_dice: function() {
            controller.reinit();
            updateMethInfo();
        }
    });
    
    controller.init(4, 10);
    controller.setListener({
        move: function() {
            updateMethInfo();
        }
    });

    updateMethInfo();
});


/*Проверка второго правила определения решаемости с для поля nxn*/
$(function(){
    
    var $BODY = $('#pt-method2');
    
    var controller = new PsPyatnashki($BODY);
    controller.setDisableOnFinish(true);

    var $info = $BODY.children('.info');
    
    function updateMethInfo() {
        var info = controller.getInfo();
        
        var K = info.K;
        var inv = info.inv;
        var N = inv.length;
        var M = info.manh;
        var KpN = K+N;
        var has = !(KpN%2);
        var even = info.even;

        $info.find('.res-inv').html(has ? 'решение есть' : 'решения нет').removeClass('red green').addClass(has ? 'green' : 'red');
        $info.find('.res-even').html(even ? 'решение есть' : 'решения нет').removeClass('red green').addClass(even ? 'green' : 'red');
        $info.find('.res-conf').html(has==even? 'нет' : 'есть').removeClass('red green').addClass(has==even ? 'green' : 'red');
        
        
        var $m1 = $BODY.find('.m1');
        $m1.find('.K').html(K);
        $m1.find('.N').html(N);
        $m1.find('.KPN').html(KpN);
        
        var invs = '';
        inv.walk(function(item) {
            invs+=invs ? ', ' : '';
            invs+='<b>'+item[0]+'</b>'+item[1];
        });
        $m1.find('.pt-inversions').html(invs);


        var $m2 = $BODY.find('.m2');
        $m2.find('.M').html(M);
        
        var $ol = $m2.find('ol').empty();
        
        function arr2str(arr, mark) {
            mark = PsArrays.toArray(mark);
            var str = '';
            arr.walk(function(num) {
                str += str ? ' ' : '';
                str += PsHtml.span(mark.contains(num) ? '<b>.</b>'.replace('.', num) : num, 'num');
            });
            return str;
        }

        var shifts = 0;
        var ARR = info.arre.walk(function(num) {
            return num ? num : '&mdash;';
        });
        $ol.append($('<li>').html(arr2str(ARR)));

        for(var i = 1; i < ARR.length; i++) {
            var tmp = ARR[i-1];
            if (tmp==i) continue; //Число на своём месте
            
            //Меняем
            ARR[ARR.indexOf(i)] = tmp;
            ARR[i-1] = i;
            
            $ol.append($('<li>').html(arr2str(ARR, [i, tmp])));
            ++shifts;
        }
        
        $m2.find('.N').html(shifts);
        $m2.find('.MPN').html(M+shifts);

        
    //        var invs = '';
    //        inv.walk(function(item) {
    //            invs+=invs ? ', ' : '';
    //            invs+='<b>'+item[0]+'</b>'+item[1];
    //        });
    //        $m1.find('.pt-inversions').html(invs);
    }

    var mode = 10;
    var size = 4;

    new ButtonsController($BODY, {
        on_dice: function() {
            mode=10;
            controller.init(size, mode);
            updateMethInfo();
        },
        on_dice2: function() {
            mode=20;
            controller.init(size, mode);
            updateMethInfo();
        }
    });
    
    controller.init(size, mode);
    controller.setListener({
        move: function() {
            updateMethInfo();
        }
    });
    
    var $select = $BODY.find('.field_size select').val(4).change(function(){
        size = strToInt($select.val());
        controller.init(size, mode);
        updateMethInfo();
    });
    
    
    $BODY.children('h5').click(function(){
        $(this).toggleClass('closed').next().toggleVisibility();
    }).disableSelection().click();
    
    updateMethInfo();
/*
    $('#href_lloyd').clickClbck(function(){
        controller.init([1,2,3,4,5,6,7,8,9,10,11,12,13,15,14,null]);
        updateMethInfo();
    });
    */
});


/*
 * Размещение цифр до суммы 17
 */
$(function() {
    var $BODY = $('.put17');
    var $NUMS = $BODY.children('.nums');
    var $SELECTS = $BODY.children('.selects');

    for (var i = 1; i <= 9; i++) {
        var $select = $('<select>').addClass('s'+i);
        for (var j = 0; j <= 9; j++) {
            $select.append($('<option>').html('&nbsp;x&nbsp;'.replace('x', j==0?'&mdash;':j)).val(j)).addClass('n'+i);
        }
        $SELECTS.append($select);
    }
    var $combos = $SELECTS.children('select');
    
    for (var k = 9; k >= 1; k--) {
        $NUMS.prepend($('<span>').addClass('n'+k).html(k));
    }
    
    var CONTROLS = new ButtonsController($BODY);
    
    $NUMS = $NUMS.children('span');
    
    /*
     * Карта, из каких комбо складывается сумма
     */
    var selMap = {
        1: [1,2,4,6],
        2: [1,3,5,7],
        3: [6,7,8,9]
    }
        
    function updateNums() {
        $NUMS.removeClass('taken');
        
        var done = true;
        for (var summNum in selMap) {
            var summ = 0;
            selMap[summNum].walk(function(i) {
                var num = strToInt($combos.filter('.n'+i).val());
                if(!num) return;
                $NUMS.filter('.n'+num).addClass('taken');
                summ+=num;
            });
            done = done && summ==17;
            $SELECTS.children('.sum'+summNum).children('span').html(summ);
        }
        $SELECTS.toggleClass('done', done);
    }
    
    $combos.change(function(){
        var $select = $(this);
        var num = strToInt($select.val());
        var has = !!num;
        $select.toggleClass('done', has);
        if (has) {
            /*
             * Пользователь выбрал значение, отличное от пустого. Нужно удалить его в других комбо.
             * Также будем считать количество пустых комбо, и если он остался один, заполним его.
             */
            var empty$combo;
            var notTakenNums = [1,2,3,4,5,6,7,8,9].removeValue(num);
            $combos.not($select).each(function(){
                var $_select = $(this);
                var _num = strToInt($_select.val());
                if (_num && _num==num){
                    _num = 0;
                    $_select.removeClass('done').val(_num);
                }
                if (_num) {
                    notTakenNums.removeValue(_num);
                }else{
                    empty$combo = $_select;
                }
            });
            
            if (empty$combo && notTakenNums.length==1) {
                empty$combo.val(notTakenNums[0]).addClass('done');
            }
        }
        
        updateNums();
    });
    
    
    CONTROLS.setCallbacks({
        on_clear: function(){
            $combos.each(function(){
                $(this).val(0).removeClass('done');
            });
            updateNums();
        },
        on_dice: function() {
            var nums = [1,2,3,4,5,6,7,8,9].shuffle();
            $combos.each(function(i) {
                $(this).val(nums[i]).addClass('done');
            });
            updateNums();
        }
    });
});

//Разное
$(function(){
    //Сортировка для задачи о покупках женщины
    //Изначально элементы расставлены правильно и мы перенесём их в ответ перед сортировкой
    var $sortable = $('#task-sort');
    $('#task-sort-ans').append($('<ol>').append($sortable.children().clone()));
    $sortable.children().shuffle();
    $sortable.sortable({
        axis: 'y'
    });
})