$(function() {

    /*
 * Менеджер управления строением атома.
 * 
 * Независимые функции:
 *   добавление/очистка содержимого  (addElectrons, addNuclons, clear)
 *   показать/скрыть информационную  (showInfo, hideInfo)
 */
    function AtomManager($field, R1, R2) {
        this.W = $field.width();
        this.H = $field.height();
    
        this.CX = Math.floor(this.W / 2);
        this.CY = Math.floor(this.H / 2);
        this.CORE = $field.children('.core');
        this.INFO = $field.children('.info');
    
        this.showInfo = function() {
            this.INFO.show();
        };
    
        this.hideInfo = function() {
            this.INFO.hide();
        };
    
        this.placeElectron = function(r, alpha) {
            var x = 0;
            var y = 0;
        
            while (alpha + 360 <= 360) {
                //Приводим углы к положительным значениям, пользуясь периодичностью
                alpha += 360;
            }
        
            x = r * PsMath.cosGrad(alpha);
            y = r * PsMath.sinGrad(alpha);
        
            var $div = $('<div>').addClass('el').appendTo($field).attr('title', 'Электрон');
        
            x -= $div.width()/2;
            y += $div.height()/2;
        
            x = this.CX + x;
            y = this.CY - y;

            x = Math.round(x);
            y = Math.round(y);

            $div.css('left', x).css('top', y);
        }
    
        this.addElectrons = function(cnt) {
            this.INFO.find('.els').html(cnt);
        
            if (cnt < 1) return;
            this.placeElectron(R1, 90);
        
            if (cnt < 2) return;
            this.placeElectron(R1, -90);
        
            if (cnt < 3) return;
        
            cnt = Math.min(cnt, 10);
            var delta = Math.floor(360/(cnt - 2));
            var initial = 0;
            switch (cnt) {
                case 5:
                    initial = 90;
                    break;
                case 6:
                    initial = 45;
                    break;
                case 7:
                    initial = 90;
                    break;
                case 9:
                    initial = 90;
                    break;
            }

            for (var i = 0; i < cnt - 2; i++) {
                this.placeElectron(R2, initial + delta * i);
            }
        }
    
        this.addNuclons = function (prCnt, nuCnt) {
            this.INFO.find('.prs').html(prCnt);
            this.INFO.find('.nes').html(nuCnt);

            var total = prCnt + nuCnt;

            while (prCnt > 0 || nuCnt > 0) {
                if(prCnt > 0) {
                    this.CORE.append($('<div>').addClass('pr').attr('title', 'Протон'));
                    --prCnt;
                }
                if(nuCnt > 0) {
                    this.CORE.append($('<div>').addClass('ne').attr('title', 'Нейтрон'));
                    --nuCnt;
                }
            }
        
        
            var wid = Math.ceil(Math.sqrt(total));
            var hei = Math.ceil(total/wid);
        
            var leftShift = wid * 15;
            var topShift =  hei * 16;
            this.CORE.css('left', this.CX-leftShift).css('top', this.CY-topShift).width(wid*30);
        }
    
        this.clear = function() {
            $field.find('.el, .pr, .ne').remove();
            this.hideInfo();
        }
    
        this.setAtom = function(num, mass) {
            this.clear();
            this.addElectrons(num);
            this.addNuclons(num, mass - num);
            this.showInfo();
        }
    }

    /*
 * Менеджер управления таблицей Менделеева.
 * 
 * Независимые функции:
 *   окраска элементов (selection)
 *   мета-информация   (meta)
 *   привязка/отвязка  (bind/unbind)
 */
    var MendeleevManager = function($table) {
        this.ELS  = $table.find('div.element');
        this.META = $table.find('td.meta');

        this.clearSelection = function() {
            this.ELS.removeClass('selected_green selected_red');
        }

        this.meta = function(info) {
            this.META.html(info ? info : '');
        }
    
        this.selectRed = function($el) {
            $el.addClass('selected_red');
        }
    
        this.selectGreen = function($el) {
            $el.addClass('selected_green');
        }
    
        this.bind = function(callback) {
            this.unbind();
            var _this = this;
            this.ELS.bind('click', function() {
                callback.call(_this, $(this));
            }).addClass('clickable');
        }
    
        this.unbind = function() {
            this.ELS.unbind('click').removeClass('clickable');
            this.META.unbind('click').removeClass('clickable');
        }
    
        this.bindMeta = function(callback) {
            var _this = this;
            this.META.bind('click', function() {
                callback.call(_this);
            }).addClass('clickable');
        }
    
        this.clear = function() {
            this.meta();
            this.clearSelection();
            this.unbind();
        }

        this.getEl = function(Sym) {
            return this.ELS.filter('.'+Sym);
        }

        this.getRandomEl = function() {
            return $(this.ELS.get(PsUtil.nextInt(0, this.ELS.size() - 1)));
        }
    
        this.ELS.each(function(){
            var $el = $(this);
            $el.data('sym', $el.find('.sym').html());
            $el.data('num', $el.find('.num').html());
            $el.data('name', $el.find('.name').html());
            $el.data('mass', $el.find('.mass').html());
        });
    }


    var AtomPlugin = {
        init: function() {
            var _this = this;
        
            this.BODY = $('.atom_plugin');
            this.TITLE = this.BODY.find('.title');
            this.ATOM_MANAGER = new AtomManager(this.BODY.find('.field'), 233, 277);
            this.MEND_MANAGER = new MendeleevManager(this.BODY.find('.Mendeleev'));
            this.title();
        
            new TabPanelController(this.BODY, function(tabName) {
                _this.tabClicked(tabName);
            }).callbackCall();
        },
    
        title: function(msg) {
            this.TITLE.html(msg ? msg : 'Атом').setVisibility(!!msg);
        },
    
        clear: function() {
            this.title();
            this.ATOM_MANAGER.clear();
            this.MEND_MANAGER.clear();
        },
    
        tabClicked: function(tabName) {
            this.clear();
            this['do_' + tabName].call(this);
        },
    
        setAtom: function($el) {
            var n = $el.data('n');
            var m = $el.data('m');
            var sym = $el.data('sym');
            var num = $el.data('num');
            var name = $el.data('name');
            var mass = $el.data('mass');
        
            this.ATOM_MANAGER.setAtom(n, m);
        
            this.title(name + ' (' + sym + ')');
        
            this.MEND_MANAGER.clearSelection();
            this.MEND_MANAGER.selectGreen($el);
            this.MEND_MANAGER.meta(
                '<b>' + name + ' (' + sym + ')</b> имеет порядковый номер ' + num + 
                ', поэтому его ядро содержит протонов: ' + num + 
                ', вокруг ядра вращается электронов: ' + num + 
                '. Относительная атомная масса равна ' + mass + 
                ', поэтому число нуклонов (нейтронов и протонов) в ядре равно ' + m + 
                ', откуда нейтронов в ядре: ' + m + ' &minus; ' + n + ' = ' + (m-n) + '.');
        },
    
        lastAtom: 'H',
        do_mend: function() {
            var _this = this;
            this.MEND_MANAGER.bind(function($el) {
                _this.lastAtom = $el.data('sym');
                _this.setAtom($el);
            });
            this.MEND_MANAGER.getEl(this.lastAtom).click();
        },
    
        do_tests: function() {
            var _this = this;
        
            this.clear();
        
            var $_el = this.MEND_MANAGER.getRandomEl();
            var n = $_el.data('n');
            var m = $_el.data('m');
        
            var el = PsRand.bool();
        
            if (el) {
                this.ATOM_MANAGER.addElectrons(n);
            } else {
                this.ATOM_MANAGER.addNuclons(n, m - n);
            }
        
            this.title('Элемент какого атома представлен ниже?');

            this.MEND_MANAGER.bind(function($el) {
                this.unbind();
                _this.setAtom($_el);
                if ($el.data('n') != $_el.data('n')) {
                    //Invalid
                    this.selectRed($el);
                }
                this.bindMeta(function() {
                    this.meta();
                    _this.do_tests();
                });
            });
        }
    }

    AtomPlugin.init();
});