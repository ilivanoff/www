PsLocalStore.CLIENT = PsLocalStore.inst('client');

/*
 * ===============
 * Данные проектов
 * ===============
 */
var PsProjectData = {
    dataOb: null,
    rubrics: {}, //    type => [ob1, ob2]
    rubricsMap: {}, // type => {id=>ob}
    postsAsc: {}, //   type => [ob1, ob2] (asc)
    postsDesc: {}, //  type => [ob1, ob2] (desc)
    postsMap: {}, //   type => {id=>ob}
    inst: function() {
        if (this.dataOb) {
            return this;
        }
        this.dataOb = defs.structure;

        var process = function(item) {
            var $a = $(item.href);
            item.a = function() {
                return $a.clone();
            }
            item.url = $a.attr('href');
            item.name = $a.html();
            
            var inPath = false;
            
            if (item.url == defs.url) {
                item.inPath = true;
                item.inPathCh = null;
                inPath = true;
            }
            
            /*
             * Посты и рубрики. По умолчанию посты извлекаются отсортированными desc!
             * 
             * push/unshift - add an element to the end/beginning of an array
             * pop/shift - remove and return the last/first element of and array
             */
            var ptype = item.ptype, rid = item.rid, pid = item.pid;
            if (item.rubric) {
                this.rubrics[ptype] = this.rubrics[ptype] || [];
                this.rubrics[ptype].push(item);
                
                this.rubricsMap[ptype] = this.rubricsMap[ptype] || {};
                this.rubricsMap[ptype][rid] = item;
                
                item.postsAsc = [];
                item.postsDesc = [];
            }
            if (item.post) {
                //Посты потом всё равно будем сортировать, так как у нас идёт разбивка по рубрикам.
                this.postsAsc[ptype] = this.postsAsc[ptype] || [];
                this.postsAsc[ptype].push(item);

                this.postsDesc[ptype] = this.postsDesc[ptype] || [];
                this.postsDesc[ptype].push(item);
                
                this.postsMap[ptype] = this.postsMap[ptype] || {};
                this.postsMap[ptype][pid] = item;
                
                item.anons = [];
                //Рубрики может и не быть (журнал, например).
                if (rid) {
                    this.rubricsMap[ptype][rid].postsAsc.unshift(item); //Заполянем в обратном порядке, так как посты отсортированы desc
                    this.rubricsMap[ptype][rid].postsDesc.push(item);   //Складываем как есть, посты и так отсортированы desc c
                }
            }
            
            if (!item.chlist) {
                //Для ускорения работы
                return inPath;
            }
            
            item.chlist.walk(function(child) {
                if (process.call(PsProjectData, child)) {
                    inPath = true;
                    item.inPath = true;
                    item.inPathCh = child;
                }
                if (item.post) {
                    item.anons.push(child);
                }
            });
            
            return inPath;
        }
        
        process(this.dataOb);
        
        //Отсортируем посты asc и desc. Это прийдётся сделать, так как они у нас разбиты по рубрикам
        var postsSorterAsc = function(post1, post2) {
            var byDate = post1.pdate - post2.pdate;
            return byDate===0 ? post1.pid - post2.pid : byDate;
        };
        
        $.each(this.postsAsc, function(type, postsAsc) {
            postsAsc.sort(postsSorterAsc);
        });

        $.each(this.postsDesc, function(type, postsDesc) {
            postsDesc.sort(postsSorterAsc).reverse();
        });

        /*
        function expand(item, path) {
            path = path ? path : [];
            if (!item || !item.inPath) {
                return path;
            }
            path.push(item.url);
            expand(item.inPathCh, path);
            return path;
        }
        alert(expand(this.dataOb).join(' -> '));
         */
        
        return this;
    },
    
    data: function() {
        return this.inst().dataOb;
    },
    
    getRubrics: function(type) {
        return this.inst().rubrics[type] || [];
    },
    
    getPostsAsc: function(type) {
        return this.inst().postsAsc[type] || [];
    },
    
    getPostsDesc: function(type) {
        return this.inst().postsDesc[type] || [];
    },
    
    getPost: function(type, id) {
        if (PsIs.object(type) && type.ptype) {
            id = type.pid;
            type = type.ptype;
        }
        return PsObjects.getValue(this.inst().postsMap[type], id);
    },
    
    getRubric: function(type, id) {
        if (PsIs.object(type) && type.ptype) {
            id = type.rid;
            type = type.ptype;
        }
        return PsObjects.getValue(this.inst().rubricsMap[type], id);
    },
    
    _anonsList: function(type, id, list) {
        var post = this.getPost(type, id);
        var $list = $('<'+list+'>');
        if(!post) return $list;
        id = post.pid;
        type = post.ptype;
        post.anons.walk(function(sub) {
            $list.append($('<li>').append(sub.a()));
        });
        return $list;
    },
    
    anonsUl: function(type, id) {
        return this._anonsList(type, id, 'ul');
    },
    
    anonsOl: function(type, id) {
        return this._anonsList(type, id, 'ol');
    }
}

/*
 * ================
 * Контекст клиента
 * ================
 */
var ClientCtxt = {
    postId: defs.postId,
    rubricId: defs.rubricId,
    postType: defs.postType,
    
    isPostsListPage: defs.isPostsListPage,
    isRubricPage: defs.isRubricPage,
    isPostPage: defs.isPostPage,
    
    curPost: null,      //Текущий пост
    curRubric: null,    //Текущая рубрика
    allPostsAsc: null,  //Посты, соответствующие открытому типу поста (asc)
    allPostsDesc: null, //Все посты, соответствующие открытому типу поста (desc)
    pagePostsAsc: null, //Все посты, соответствующие открытой странице (все/в рубрике) (asc)
    pagePostsDesc: null //Посты, соответствующие открытой странице (все/в рубрике) (desc)
}

if (ClientCtxt.isPostPage) {
    ClientCtxt.curPost = PsProjectData.getPost(ClientCtxt.postType, ClientCtxt.postId);
}

if (ClientCtxt.isPostPage || ClientCtxt.isRubricPage) {
    ClientCtxt.curRubric = PsProjectData.getRubric(ClientCtxt.postType, ClientCtxt.rubricId);
}

if (ClientCtxt.postType) {
    ClientCtxt.allPostsAsc = PsProjectData.getPostsAsc(ClientCtxt.postType);
    ClientCtxt.allPostsDesc = PsProjectData.getPostsDesc(ClientCtxt.postType);
}

if (ClientCtxt.isPostsListPage) {
    ClientCtxt.pagePostsAsc = ClientCtxt.allPostsAsc;
    ClientCtxt.pagePostsDesc = ClientCtxt.allPostsDesc;
}

if (ClientCtxt.isRubricPage) {
    ClientCtxt.pagePostsAsc = ClientCtxt.curRubric.postsAsc;
    ClientCtxt.pagePostsDesc = ClientCtxt.curRubric.postsDesc;
}

/*
 * =========
 * Навигатор
 * =========
 */
var PsNavigation = {
    mapUl: null,
    navigationUl: null,
    
    init: function() {
        //СТРОКА НАВИГАЦИИ (есть на каждой странице, но всёже проверку выполним)
        var $navUl = $('ul.navigation');
        if(!$navUl.isEmptySet()) {
            $navUl.replaceWith(this.navigation());
            //Выровняем выпадающие меню по вершнему элементу
            $navUl.find('div.popup').each(function(){
                var itemWidth = $(this).width();
                var parentWidth = $(this).parents('li:first').width()+18+20;
                if (parentWidth > itemWidth) {
                    $(this).width(parentWidth);
                }
            });
        }
        
        //КАРТА САЙТА (есть только на странице "карта")
        var $mapUl = $('ul.sitemap');
        if(!$mapUl.isEmptySet()) {
            $mapUl.replaceWith(this.map());
            //Отключим кнопку открытия карты
            $('#navigation_bar>a.map').disable();
        }
        
        //РУБРИКИ С ПРАВОЙ СТОРОНЫ
        ['bp', 'tr'].walk(function(type) {
            var $rubUl = $('ul.ps-rubrics-'+type);
            if ($rubUl.isEmptySet()) return;
            var $ul = PsNavigation.rubricsUl(type, defs.PAGE_RS_MAX_LESSONS);
            if ($ul.hasChild('li')) {
                $rubUl.replaceWith($ul);
            }else{
                $rubUl.prev('h3').remove();
                $rubUl.remove();
            }
        });
        
        //АНОНСЫ ВЫПУСКОВ ЖУРНАЛА
        ['is', 'tr'].walk(function(type) {
            var cl = '.'+type+'-anons-placeholder';
            var doReplace = function() {
                var $div = $(this);
                $div.replaceWith(PsProjectData.anonsOl(type, $div.data('id')).addClass('anons'));
            }
            $(cl).each(doReplace);
            $(cl).livequery(doReplace);
        });
    
    //СОДЕРЖАНИЕ УРОКОВ
    //$('.tr-anons-placeholder') - будет обработан в 
    },
    
    /*
     * НАВИГАЦИЯ
     */
    navigation: function() {
        //Специальный контейнер для отображения элементов не как ссылок
        function crSpan(item, text) {
            return PsHtml.span$(text ? text : item.name, 'nav');
        }
        
        //Строит список потомков для данного элемента (выпадение вправо)
        function getChildsUl(item, level) {
            if(!item.chlist) return null;
            var $ul = $('<ul>').addClass('l'+level);
            if (item.chanons) {
                $ul.addClass('anchor');
            }
            var $li = null;
            item.chlist.walk(function(child) {
                if (child.inPath) {
                    //Элемент входит в путь. Если для него есть placeholder - отметим
                    if(item.chplaceholder && item.chlist.length > 1) {
                        $li = $('<li>').addClass('selected').appendTo($ul);
                        $li.append(crSpan(child));
                    }
                    return;//---
                }
                var $chUl = getChildsUl(child, level+1);
                $li = $('<li>').appendTo($ul);
                if ($chUl){
                    $li.append($('<div>').addClass('popupCarrier').append($chUl));
                }
                //Проверка - нужно ли показывать "стрелочку" (задний план) для элемента списка
                if (child.nobg) {
                    $li.addClass('nobg');
                }
                $li.append(child.a());
            });
            return $li ? $ul : null;
        }
        
        var $NAV_UL = $('<ul>').addClass('navigation');
        
        //Регистрирует ПОТОМКА на верхнем уровне строки навигации (выпадение вниз)
        function registerPathItem(item, force) {
            var child = item.inPathCh;
            var $UL;
            if (child) {
                var hasAfterChild = child.inPathCh || (child.chlist && child.chname);
                $UL = getChildsUl(item, 1);
                var $LI = $('<li>').appendTo($NAV_UL).
                append($('<div>').addClass('popup').append($UL));
                if (hasAfterChild){
                    $LI.append(child.a());
                    registerPathItem(child, false);
                }else{
                    $LI.append(crSpan(child));
                }
                return;//---
            }
            if (item.chlist && item.chname) {
                $UL = getChildsUl(item, 1);
                $('<li>').appendTo($NAV_UL).
                append($('<div>').addClass('popup').append($UL)).
                append(crSpan(item, item.chname));
                return;//---
            } 
            if (force) {
                $UL = getChildsUl(item, 1);
                $('<li>').appendTo($NAV_UL).
                append($('<div>').addClass('popup').append($UL)).
                append(crSpan(item));
                return;//---
            }
        }        
        
        registerPathItem(PsProjectData.data(), true);
        
        return $NAV_UL;
    },
    
    /*
     * ПРАВАЯ ЧАСТЬ НАВИГАЦИИ
     */
    rubricsUl: function(type, max) {
        var $ul = $('<ul>').addClass('ps-rubrics');
        $.each(PsProjectData.getRubrics(type), function(n, rubric) {
            $ul.append($('<li>').addClass('l1').append(rubric.a()).append($('<span>').addClass('rub-posts-cnt').html('('+rubric.postsDesc.length+')')));
            var cur = 0;
            $.each(rubric.postsDesc, function(n, post) {
                var $li = $('<li>').addClass('l2');
                if (max && (++cur > max)) {
                    $ul.append($li.addClass('gray').html('...'));
                    return false;
                } else {
                    $ul.append($li.append(post.a()));
                }
                return true;
            });
        });
        return $ul;
    },
    
    /*
     * КАРТА САЙТА
     */
    map: function() {
        var $map = $('<ul>').addClass('sitemap');
        function addChild(item, level) {
            var $a = item.a();
            var $li = $('<li>').addClass('level'+level).append($a);
            $map.append($li);
            if (item.chlist) {
                if (item.chanons) {
                    //Наведение на такие ссылки будет обработано в addSiteMapListener
                    $a.addClass('expandable').data('item', item).removeAttr('title');
                } else {
                    item.chlist.walk(function(sub) {
                        addChild(sub, level+1);
                    });
                }
            }
        }
        
        PsProjectData.data().chlist.walk(function(root) {
            addChild(root, 1);
        });
        
        this.addSiteMapListener();
        
        return $map;
    },
    
    //Добавим слушатель наведения на пост для открытия анонса.
    //Добавлять будем через $(...).on(...) для ускорения работы.
    mapListenerAdded: false,
    addSiteMapListener: function() {
        if(this.mapListenerAdded) return;
        this.mapListenerAdded = true;
        
        var fixHolder = false;
        var $holder, timer, $expandedHref;
        var holderShow = function(e, $a) {
            //Мы уже кликнули по разделу поста и загружаем, новую навигаю открывать незачем
            if (fixHolder) return;//---
            holderHide();
            var $ul = PsProjectData.anonsUl($a.data('item'));
            $holder = $('<div>').addClass('ps-anons-holder').appendTo('body').append($ul);
            $holder.hover(timer.stop, timer.start);
            $holder.calculatePosition($a);
            $holder.show();
            $expandedHref = $a.addClass('expanded');
        }
        var holderHide = function() {
            if (fixHolder) return;//---
            if (timer) {
                timer.stop();
            }
            if ($holder) {
                $holder.remove();
                $holder = null;
            }
            if ($expandedHref) {
                $expandedHref.removeClass('expanded');
                $expandedHref = null;
            }
        }
        timer = new PsTimerAdapter(holderHide, 200);
        
        PsJquery.on({
            item: 'ul.sitemap a.expandable',
            mouseenter: holderShow,
            mouseleave: timer.start
        });
        
        PsJquery.on({
            item: 'ul.sitemap a, div.ps-anons-holder a',
            click: function(e, $a) {
                if (fixHolder) {
                    //Мы уже загружаем раздел поста. Запретим переход по другим ссылкам анонса.
                    e.preventDefault();
                    return;//---
                }
                var processed = PsIdentPagesManager.processIpHrefClick(e, $a);
                if(!processed && $a.is('div.ps-anons-holder a')) {
                    //Мы кликнули на анонсе и собираемся переходить по ссылке на другую страницу
                    //Отметим наш выбор, запретив прятать anons-holder и убрав признак .expandable у всех ссылок, кроме ссылки выбранного поста
                    fixHolder = true;
                    $('ul.sitemap a.expandable').not('.expanded').removeClass('expandable');
                    
                    $a.addClass('loading');
                    $holder.addClass('loading');
                } else {
                    holderHide();
                }
            }
        });
    
    }
}

/*
$(function() {
    PsNavigation.map().clone(true, true).prependTo('#content');
    PsNavigation.map().clone().prependTo('#content');
    PsNavigation.navigation().clone().prependTo('#content');
    });
*/

//(function($) {
/*
 * =====================
 * Идентифицируемые окна
 * =====================
 */
var PsIdentPagesManager = {
    pages: {},       //Зарегистрированные страницы
    page: null,      //Текущая страница
    processors: new ObjectsStore(),  //Зарегистрированные процессоры для страницы
    showStack: [],   //Стек открытия страниц
    dflt: 'content', //Id дефолтной страницы, на месте которой показываются загружаемые
    
    logger: PsLogger.inst('PsIdentPagesManager').setDebug()/*.disable()*/,
    
    hasPage: function(ident) {
        return this.pages.hasOwnProperty(ident);
    },
    
    isDflt: function(ident) {
        return this.dflt == ident;
    },
    
    isCurrent: function(ident) {
        return this.page ? this.page.equals(ident) : ident==this.dflt;
    },
    
    register: function(processor) {
        this.logger.logInfo('Зарегистирован процессор: '+PsObjects.keys2array(processor));
        for (var ident in processor) {
            this.processors.putToArray(ident, processor[ident]);
        }
    },
    
    init: function() {
        
        var openers = {};
        $('a.ip-opener').each(function(){
            var $a = $(this);
            var ident = getHrefAnchor($a);
            var title = $a.attr('title');
            
            //Определим cover src, так как его больше неоткуда взять
            var $img = $a.children('img');
            var src = $img.isEmptySet() ? $a.backgroundImageUrl() : $img.attr('src');
            
            openers[ident] = {
                ident: ident,
                title: title,
                src: src
            };
            $a.clickClbck(function() {
                PsIdentPagesManager.openPage(ident, title);
            //PsIdentPagesManager.openPage(pageIdent, pageTitle, 'Search!');
            });
        });
        
        PsHotKeysManager.addListener('Ctrl+Alt+W', {
            f: function() {
                PsDialog.register({
                    id: 'IdentPagesDialog',
                    build: function(DIALOG, whenDone) {
                        $.each(openers, function(ident, ob) {
                            var $img = crIMG(ob.src, ob.ident);
                            var $a = crA().append($img).append(ob.title).
                            clickClbck(function() {
                                DIALOG.close();
                                PsIdentPagesManager.openPage(ob.ident, ob.title);
                            });
                            DIALOG.div.append($('<div>').append($a));
                        });
                        whenDone(DIALOG);
                    },
                    wnd: {
                        title: 'Загружаемые окна',
                        width: null,
                        minWidth: 300
                    }
                }).toggle();
            },
            descr: 'Загружаемые окна'
        });
        
        PsHotKeysManager.addListener('Ctrl+Alt+M', {
            f: function() {
                var ident = 'sitemap';
                var ob = openers[ident];
                if (PsIdentPagesManager.isCurrent(ident)) {
                    if (PsScroll.isScrolling()) {
                        //Мы сейчас выполняем скроллинг - пропускаем.
                        return;//---
                    }
                    PsIdentPagesManager.hideAll();
                    PsScrollManager.restoreWndScroll();
                } else {
                    PsScrollManager.storeWndScroll();
                    PsIdentPagesManager.openPage(ob.ident, ob.title);
                }
            },
            descr: 'Карта сайта'
        });
        
        //Регистрируем обработчик клика по ссылке внутри идентифицируемой страницы
        //Так как при повешании $(item).on - item уже должен быть добавлен на страницу, мы добавим слушатель на body
        PsJquery.on({
            ctxt: this,
            item: '.ps-ipage-content a[href]',
            click: this.processIpHrefClick
        });
        
        /*
         * Во время инициализации мы восстанавливаем предыдущую открытую страницу.
         * Сохраняем предыдущую страницу в тот момет, когда пользователь нажал F5.
         */
        PsHotKeysManager.addListener('F5', {
            ctxt: this,
            f: this.storeState
        });

        //Восстанавливаем состояние
        this.restoreState();
    },
    
    storeState: function() {
        //Если текущая страница - не загружаемая, или она была показана с ошибкой - не сохраняем состояние
        var curpage = this.page;
        if(!curpage || !curpage.data.ok) return;//--
        
        PsLocalStore.CLIENT.set('last_ident_page', {
            ident: curpage.ident,
            title: curpage.title
        });
    },
    
    restoreState: function() {
        var lastPage = PsLocalStore.CLIENT.get('last_ident_page');
        if(!lastPage) return;//---
        //Сразу после восстановления страницы - стираем информацию о ней.
        PsLocalStore.CLIENT.remove('last_ident_page');
        //Стартуем отложенный режим
        PsUtil.scheduleDeferred(function() {
            /*
             * Открываем страницу в отложенном режиме, так как сначала должны 
             * зарегистироваться плагины.
             * 
             * Тут мы убиваем двух зайцев сразу - если, например,
             * была открыта страница ЛК, а теперь пользователь разлогинен,
             * то мы не будем пытаться повторно открыть эту страницу, так как
             * для неё обработчик уже не будет зарегистрирован.
             */
            if(!this.processors.has(lastPage.ident)) return;//---
            this.openPage(lastPage.ident, lastPage.title);
        }, this);
    },
    
    openPage: function(ident, title, force) {
        if (this.isDflt(ident)) {
            this.showStack.push({
                ident: ident,
                force: false
            });
            this.doShow();
            return;//---
        }
        
        if(!this.hasPage(ident)) {
            var logger = this.logger;
            var processors = this.processors.get(ident);

            if (!processors) {
                InfoBox.popupError("Не зарегистрирован обработчик для '"+title+"' ("+ident+")");
                return;//---
            }
            
            logger.logInfo('Регистрируем страницу {}.', ident);
            
            this.pages[ident] = {
                ident: ident,
                title: title,
                adds: 0,
                shows: 0,
                div: null,
                /*
                 * Признак принудительной перезагрузки.
                 * Если пришла команда на перезагрузку страницы, но страница сейчас не открыта,
                 * то мы должны принудительно перезагрузить её при следующем открытии.
                 */
                reload: false,
                //Данные, устанавливаемые после загрузки содержимого страницы
                data: {
                    ctt: null,
                    jsp: null,
                    ok: null
                },
                //Некоторые страницы сами умеют грузить данные
                load: function(onLoadDone) {
                    var loaded = false;
                    processors.walk(function(processor) {
                        if (loaded) return; //Уже загружаем ---
                        if(!$.isFunction(processor.load)) return;//Данный процессор не умеет загружать страницу---
                        loaded = true;
                        processor.load.call(processor, onLoadDone);
                    });
                    return loaded;
                },
                //Есть возможность отработать на события добавления/до-показа/после_показа
                fire: function(eventName) {
                    logger.logTrace('Поступило событие {}->{}.', eventName, ident);
                    processors.walk(function(processor) {
                        var method = processor['on'+eventName.firstCharToUpper()];
                        if(!$.isFunction(method)) return;//Процессор не случает данное событие
                        logger.logDebug('Отравляем событие {}->{}.', eventName, ident);
                        //При вызове метода мы передадим не все параметры, а только некоторые
                        method.call(processor, {
                            ident: this.ident,
                            adds: this.adds,
                            shows: this.shows,
                            div: this.div,
                            js: this.data.jsp
                        });
                    }, false, this);
                },
                //Аналог equals
                equals: function(other) {
                    if (PsIs.string(other)) return this.ident==other;
                    if (PsIs.object(other)) return this.ident==other.ident;
                    return false;
                }
            }
        }
        
        var page = this.pages[ident];
        
        this.showStack.push({
            ident: ident,
            force: force || page.reload
        });
        
        //Сбросим признак принудительной перезагрузки
        page.reload = false;
        
        this.doShow();
    },
    
    openDefaultPage: function() {
        this.openPage(this.dflt);
    },
    
    hideAll: function() {
        this.openDefaultPage();
    },
    
    resetPage: function(ident) {
        var page = this.pages[ident];
        this.logger.logInfo('Поступил запрос на сброс страницы [{}]', ident);
        if(!page || page.data.ok === false) return; //Если страницы ещё нет или она загрузилась с ошибкой - ничего не делаем
        //Установим признак необходимости перезагрузки
        page.reload = true;
    },
    
    reloadPage: function(ident) {
        var page = this.pages[ident];
        this.logger.logInfo('Поступил запрос на перезагрузку страницы [{}]', ident);
        if(!page || page.data.ok === false) return; //Если страницы ещё нет или она загрузилась с ошибкой - ничего не делаем
        //Установим признак необходимости перезагрузки
        page.reload = true;
        //Если сейчас открыта обновлённая страница - откроем её заново
        if (this.isCurrent(page)) {
            this.openPage(ident, page.title);
        }
    },
    
    inProgress: false,
    doShow: function() {
        if (this.inProgress || this.showStack.length==0) return;//---
        var logger = this.logger;
        
        var rules = this.showStack.shift();
        var ident = rules.ident;
        var force = rules.force;
        var page = this.pages[ident];
        
        //У дефолтной страницы нет объекта page, на этом основана логика.
        if (page && !force && this.isCurrent(page)) {
            logger.logTrace('Страница {} является текущей.', ident);
            //this.openDefaultPage();
            return;//---
        }
        
        logger.logDebug('Открываем страницу {}.', ident);
        
        this.inProgress = true;
        this.closerHide();

        $('#leftPanel').children().hide();
        
        if (!page) {
            this.doShowImpl(null);
            return;//---
        }
        
        //Если страница однажды загрузилась некорректно, больше её не перезагружаем
        if (page.data.ok === false) {
            this.doShowImpl(page);
            return;//---
        }
        
        //Страница уже добавлена и перезагружать её не нужно
        if (page.adds>0 && !force) {
            this.doShowImpl(page);
            return;//---
        }

        var secundomer = new PsSecundomer(true);
        logger.logInfo('Загружаем страницу {}...', ident);

        //Загружаем/перезагружаем страницу
        var onLoadDone = PsUtil.once(function(ctt, jsp, error) {
            logger.logInfo('Страница {} загружена за {} секунд.', ident, secundomer.stop());
            this.onPageLoaded(page, ctt, jsp, error);
            this.progressHide();
            this.doShowImpl(page);
        }, this);
        
        if (page.div) page.div.remove();
            
        this.progressShow(page.title);
        
        try {
            if (page.load(onLoadDone)) {
                //Страница самостоятельно загрузила данные...
                logger.logInfo('Страница {} самостоятельно загрузила данные.', ident);
                return;//---
            }
        } catch(e) {
            //Мы пытались загрузить содержимое и получили ошибку
            logger.logError('Эксепшн во время загрузки страницы {}: {}.', ident, e);
            onLoadDone(e, null, true);
            return;//---
        }
        
        //Страница не грузит данные сама для себя, полезем за данными на сервер.
        logger.logInfo('Страница {} будет загружена с сервера.', ident);

        var request = {};
        request[defs.IDENT_PAGE_PARAM] = ident;
        request.ctxt = this;
                
        AjaxExecutor.execute('IdentPages', 
            request, 
            function(resp) {
                onLoadDone(resp['ctt'], resp['jsp'], false);
            },
            function(err) {
                onLoadDone(InfoBox.divError(err), null, true);
            });
    },
    
    onPageLoaded: function(page, ctt, jsp, error) {
        page.data = {
            ctt: ctt,
            jsp: jsp,
            ok: !error
        }
        //Наша задача обернуть возвращённое содержимое в див. Если вернулся див, то он и должен быть использован.
        
        var $div = $('<div>').append(ctt);
        var $divChild = $div.children();
        
        page.div = $divChild.size()==1 && $divChild.is('div') ? $divChild : $div;
        page.div.addClass('ps-ipage-content').hide().appendTo('#leftPanel');
        
        if (error) return;//---
        
        ++page.adds;
        page.shows=1;
        page.fire('Add');
    },
    
    /**
     * Метод показывает страницу, при этом:
     * 1. Если page==null, то это - дефолтная страница
     * 2. Если page!=null, то это - успешно загруженная не дефолтная страница
     */
    doShowImpl: function(page) {
        var ident = page ? page.ident : this.dflt;
        
        if (page && page.data.ok) {
            page.fire('BeforeShow');
        }
        
        if (page) {
            page.div.show();
        } else {
            //Показываем див дефолтной страницы
            $('#leftPanel>#'+ident).show();
        }
        
        if (page && page.data.ok) {
            page.fire('AfterShow');
            ++page.shows;
        }
        
        if (page) {
            this.closerShow();
        }
        
        this.page = page;
        this.inProgress = false;
        this.doShow();
    },
    
    closer: null,
    closerHide: function() {
        if (this.closer) {
            this.closer.remove();
            this.closer = null;
        }
    },
    
    closerShow: function() {
        this.closerHide();
        
        var _this = this;
        this.closer = crCloser(function() {
            _this.hideAll();
        }, 'ipcloser').prependTo('#leftPanel');
    },
    
    
    progress: null,
    progressHide: function() {
        if (this.progress) {
            this.progress.remove();
            this.progress = null;
        }
    },
    progressShow: function(title){
        this.progressHide();
        this.progress = loadingMessageDiv(title).appendTo('#leftPanel');
    },
    
    /*
     * Обработаем клик по ссылке на идентифицируемой странице.
     * Возможно наша задача сведётся к тому, чтобы просто её скрыть.
     * Скрывать будем, если ссылка является обычной (есть что-то помимо якоря) 
     * и указывает НА ЭТУ ЖЕ страницу.
     * 
     * Возвращаем true, если мы обработали эту ссылку, иначе - false и ссылка будет обработана
     * стандартным образом.
     */
    processIpHrefClick: function(event, $a) {
        if(!PsUrl.isUsualHref($a)) return false;// Ссылка только с якорем
        if (PsUrl.isUsualHref2AnotherPage($a)) return false;// Обычная ссылка, но на другую страницу
        
        //По клику мы должны остаться на этой странице
        var hasAnchor = !!getHrefAnchor($a);
        PsIdentPagesManager.hideAll();//this
        if (hasAnchor) {
        //Переходим по якорю
        } else {
            //Сбросим якорь и покажем страницу под идентифицируемой
            event.preventDefault();
            if (window.location.hash) {
                window.location.hash = '';
            }
            PsScroll.jumpTop();
        }
        return true;
    }
}

/*
 * Секундомеры акций
 */
PsJquery.executeOnElVisible('.ps-stock-timer', function($div) {
    PsGlobalInterval.subscribe4Nseconds($div.html(), function(left) {
        $div.html(PsTimeHelper.formatDHMS(left));
    }, function() {
        $div.extractParent('.stock_body').addClass('past');
        $div.replaceWith($('<span>').html('Акция завершилась'));
    });
});


/*
 * Альтернативный вид постов на блоге/кружке.
 */
var PsShowcasesViewController = {
    //Процессоры, выполняющие обработку данного плагина
    processors: {},
    
    //Метод регистрирует обработчик для представления
    register: function(processors) {
        $.extend(PsShowcasesViewController.processors, processors);
    },
    
    //Инициализация менеджера
    init: function() {
        var $controls = $('div.ps-showcases-ctrl-panel');
        if ($controls.isEmptySet()) return; //---
        
        var $WRAPPER = $('div.showcases_list').wrap($('<div>')).parent('div');
        
        var updateModel = new PsUpdateModel();
        
        var $hrefs = $controls.children('a').clickClbck(function(type) {
            type = type=='list' ? 'showcases_list' : type;
            if (this.hasClass('current') || updateModel.isStarted()) {
                return;//---
            }
            
            $hrefs.removeClass('current');
            this.addClass('current');
            
            var $childs = $WRAPPER.children().hide();
            var $child = $childs.filter('.'+type);
            if(!$child.isEmptySet()) {
                $child.show();
                return;//---
            }
            
            var $contentHolder = this.next('.hidden-box');
            var $divs = $contentHolder.children('div').hide();
            
            var $contentDiv = $divs.filter('div:first');
            var $pluginsDiv = $divs.filter('.ps-showcases-view-plugins');
            
            var $loading = loadingMessageDiv('Построение списка');
            var $plugHrefs = $pluginsDiv.children('a');
            var pluginsStoreKey = 'posts-view-'+type+'-plugin';
            var $titleH1 = $('<div>').addClass('post_head').html(this.data('hint')).append($pluginsDiv).hide();
            $('<div>').addClass(type).append($titleH1).append($loading).append($contentDiv).appendTo($WRAPPER);
            
            //Мы всё переместили из служебного дива, больше он нам не нужен
            $contentHolder.remove();
            
            //Стартуем updateModel и начинаем выполнение
            updateModel.start();
            
            /*
             * Функция обратного вызова, которая:
             * спрячет прогресс, покажет ошибку (ели она произошла), покажет див с содержимым,
             * инициализирует последний открытый плагин (если он был)
             */
            var onDone = function(error) {
                $loading.remove();
                
                if (error) {
                    $contentDiv.empty().append(InfoBox.divError(error));
                }
                
                $titleH1.show();
                $contentDiv.show();
                
                if(!error && !$plugHrefs.isEmptySet()) {
                    $pluginsDiv.show();
                    $plugHrefs.extractHrefsByAnchor(PsLocalStore.CLIENT.get(pluginsStoreKey)).click();
                }
                
                updateModel.stopDeferred();
            }
            
            //ПЛАГИНЫ
            //Контроллер, оповещаемый об изменении состояния плагина (onInit, onShow, onHide)
            var plugins = {
                __doCall: function(meth, plType) {
                    //onInitType
                    var customMethod = 'on'+meth.firstCharToUpper() + plType.firstCharToUpper();
                    if (this.hasOwnProperty(customMethod)) {
                        this[customMethod].call();
                        return true;//---
                    }
                    //onInit(type)
                    var commonMethod = 'on'+meth.firstCharToUpper();
                    if (this.hasOwnProperty(commonMethod)) {
                        this[commonMethod].call();
                        return true;//---
                    }
                    
                    return false;
                }
            }
            
            var initedPlugins = {};
            
            $plugHrefs.clickClbck(function(plAnchor) {
                var oldPl = getHrefAnchor($plugHrefs.filter('.current'));
                var newPl = oldPl==plAnchor ? null : plAnchor;
                $plugHrefs.removeClass('current');
                if (oldPl) {
                    PsLocalStore.CLIENT.remove(pluginsStoreKey);
                    plugins.__doCall('hide', oldPl);
                }
                if (newPl) {
                    PsLocalStore.CLIENT.put(pluginsStoreKey, newPl);
                    if(!initedPlugins[newPl]) {
                        initedPlugins[newPl] = true;
                        plugins.__doCall('init', newPl);
                    }
                    if (plugins.__doCall('show', newPl)) {
                        this.addClass('current');
                    } else {
                        InfoBox.popupError('Плагин ' + newPl + ' не реализован');
                    }
                }
            });
                
            //Непосредственно выполнение команды
            if (PsShowcasesViewController.processors.hasOwnProperty(type)) {
                var scItemJsParams = PsFoldingManager.FOLDING('sc', type).panel('SCCONTROLS');
                PsShowcasesViewController.processors[type].call(null, $contentDiv, onDone, plugins, scItemJsParams);
            } else {
                onDone('Обработчик для плагина ['+type+'] не добавлен');
            }
        });
        
        $hrefs.first().click();
    }
}

var toolsManager = {
    init: function(){
        $('.tool:has(.tool_body)').livequery(function(){
            var $toolDiv = $(this);
            var $toolBody = $toolDiv.find('.tool_body');
            var uqId = 'tool_uq_'+PsStrings.trim($toolDiv.find('.tool_name').text());
            $toolDiv.find('a.tool_href').click(function() {
                var visible = $toolBody.toggleVisibility().isVisible();
                PsLocalStore.CLIENT.set(uqId, visible ? 1 : null);
                return false;
            });
            $toolBody.setVisible(PsLocalStore.CLIENT.has(uqId));
        });
    }
}

var RightPanelController = {
    inst: null,
    init: function() {
        if(this.inst) return;//---
        //НАЧАЛО реализации
        var INST = new function() {
            var $carrier = $('#carrier');
            
            var setVis = function(vis){
                $carrier.toggleClass('RpHide', !vis);
                PsLocalStore.CLIENT.set('RpHide', vis ? null : 1);
                RightPanelController.onResize();
            }
            
            $carrier.find('#rightPanel a.RpHide').clickClbck(function() {
                setVis(false);
            });
            
            $carrier.find('#navigation_bar>span.controls a.RpShow').clickClbck(function() {
                setVis(true);
            });
            
            var isHide = PsLocalStore.CLIENT.has('RpHide');
            setVis(!isHide);
            
            this.setVis = setVis;
            this.toggleVis = function() {
                setVis($carrier.hasClass('RpHide'));
            }
        }
        //ОКОНЧАНИЕ реализации
        this.inst = INST;
        
        PsHotKeysManager.addListener('Ctrl+Alt+F', {
            f: INST.toggleVis,
            descr: 'Показать/скрыть правую панель',
            enableInInput: true,
            stopPropagate: true
        });
    },
    
    setVis: function(vis) {
        this.inst.setVis(vis);
    },
    
    onResize: function() {
        //При изменении размеров окна - отресайзим все ContentFlow
        PsContentFlow.resize();
        PsTimeLine.resize();
    }
}

function CalculatePageSwitcher() {
    var $BOX = $('.ps-switcher');
    var $BOXWidth = $BOX.width();
    
    var max = $BOX.data('max');
    var cur = $BOX.data('cur');
    var url = $BOX.data('url');
    
    function newBox() {
        return $('<div>').addClass('box').appendTo($BOX);
    }
    
    function newIem(i) {
        return i==cur ? PsHtml.span$(i) : crA(PsUrl.addParams(url, defs.PAGING_PARAM+'='+i)).html(i);
    }
    
    var $curBox = newBox();
    var curWidth = 0;
    for(var i = 1; i <= max; i++) {
        var $a = newIem(i);
        $BOX.append($a);
        var aWidth = $a.outerWidth(true);
        if (curWidth + aWidth <= $BOXWidth) {
            $curBox.append($a);
            curWidth+=aWidth;
        } else {
            $curBox = newBox();
            $curBox.append($a);
            curWidth=aWidth;
        }
    }
}

//Код, выполняемый после загрузки страницы.
$(function() {
    PsNavigation.init();
    
    PsShowcasesViewController.init();
    
    toolsManager.init();
    
    RightPanelController.init();
    
    CalculatePageSwitcher();
    
    PsIdentPagesManager.init();
    
    new PageScroller('#header', '#carrier');
    
    $('#rightPanel h3:first').addClass('first');
});

//})(jQuery);

/*
$(function() {
    var converter = function(ob) {
        return {
            src: '/autogen/images/250x/folding/posts/bp/BP_SP2_zrenie.png',
            href: 'xxx.php',
            caption: 'Заголовок '+ob,
            title: 'Название '+ob
        }
    }
    PsContentFlow.appendTo([1,2,3], converter, '.xxxx');
});
 */