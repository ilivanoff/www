$(function() {
    var GalleryAction = function(dataObj, onOk, onErr, onFinal) {
        AdminAjaxExecutor.executePost('GalleryAction', dataObj, onOk, onErr, onFinal);
    }
    
    //Создание новой галереи
    var $GALL_TABS = $('.ps-gallery-menu');
    
    
    function PsGalleryCleareController($TAB) {
        var $BUTTONS = $TAB.find('.ps-gallery-buttons button');
        
        $BUTTONS.filter('.save').button({
            icons: {
                primary: 'ui-icon-disk'
            }
        }).click(function() {
            var gallery = $TAB.find('input[name="gallery"]').val();
            var name = $TAB.find('input[name="name"]').val();
            
            if (PsIs.empty(gallery)) {
                InfoBox.popupError('Не указана директория галереи');
                return;//---
            }

            $BUTTONS.uiButtonDisable();

            GalleryAction({
                action: 'creategall',
                gallery: gallery,
                name: name
            }, function() {
                InfoBox.popupSuccess('Галлерея успешно создана');
                locationReload();
            }, function(err){
                InfoBox.popupError(err);
                $BUTTONS.uiButtonEnable();
            });
            
        });
    }
    
    if (!$GALL_TABS.isEmptySet()) {
        new PsGalleryCleareController($GALL_TABS.children('.create-new'));
    }
    
    //Редактирование конкретной галереи
    var $GALLS_TABS = $('#ps-gallery-img-menu');
    var GALLERY = $GALLS_TABS.data('name');
    
    var $TAB_EDIT = $GALLS_TABS.children('.ps-gallery-controller');
    var $TAB_ADD_WEB_IMG = $GALLS_TABS.children('.web-img-add');
    var $TAB_ADD_FILE_IMG = $GALLS_TABS.children('.file-img-add');
    
    var onImageAdd = PsUtil.once(function() {
        $TAB_EDIT.prepend(InfoBox.divWarning('В галерею добавлены новые картинки, нужно перезагрузить страницу!'));
    });

    function PsGalleryController($TAB) {
        var $GALLERY = $TAB.children('.gallery');
        var $IMAGES = $TAB.children('.images');
        
        var $IMG_LIS, $IMG_INC_CBOXES, $IMG;
        
        //Выделить/снять выделение
        var $selectAll = $TAB.find('.select-all label').disableSelection().children('input:checkbox');
        var recalSelectAllState = function() {
            if(!$IMG) return;//Элементы ещё не загружались
            var total = $IMG.size();
            var checked = $IMG_INC_CBOXES.filter(':checked').size();
            $selectAll.setChecked(checked==total).siblings('span.count').html(checked+'/'+total);
        }
        
        //Ссылки на элементы, содержащие картинки. Они могут измениться после удаления эдемента из дерева
        var treeUpdated = function() {
            $IMG_LIS = $IMAGES.children('li.image');
            $IMG_INC_CBOXES = $IMG_LIS.find('.path :checkbox');
            $IMG = $IMG_LIS.find('img.preview');
            recalSelectAllState();
        }
        treeUpdated();
        
        //Добавим все необходимые слушатели
        $selectAll.change(function() {
            $IMG_INC_CBOXES.setChecked($(this).isChecked()).change();
        });
        
        //Повесим слушатель на изменение состояния чекбокса для каждой картинки
        $IMG_INC_CBOXES.change(recalSelectAllState);
        
        //Переключение размеров картинок:
        var $sizeHrefs = $TAB.find('.hg-self.fixed a').html(function() {
            return getHrefAnchor(this);
        });
        new HrefsGroup($sizeHrefs, 'self', {
            id: 'GalleryManager',
            callback: function(state) {
                $IMG.width(state);
            }
        }).callbackCall();
        
        //Сортировка
        $IMAGES.sortable({
            axis: 'y',
            placeholder: 'placeholder',
            handle: '.image-holder',
            start: function(event, ui) {
                ui.placeholder.height(ui.item.height());
                ui.item.addClass('move');
            },
            stop: function(event, ui) {
                ui.item.removeClass('move');
            }
        });
        
        //Включение/отключение картинки
        $IMG_LIS.each(function() {
            var $LI = $(this);
            var file = $LI.data('name');
            var isWeb = $LI.is('.web');
            var $path = $LI.find('.path').disableSelection();
            $path.find(':checkbox').change(function() {
                $LI.toggleClass('excluded', !$(this).isChecked());
            }).change();
            $LI.find('.imgctrl a.remove').clickClbck(function() {
                var $a = this;
                PsDialogs.confirm('Подтвердите удаление картинки', function() {
                    $a.remove('imgdel');
                    GalleryAction({
                        action: 'imgdel',
                        file: file,
                        web: isWeb ? 1 : 0,
                        gallery: GALLERY
                    },  function() {
                        InfoBox.popupSuccess('Картинка успешно удалена');
                        $LI.remove();
                        treeUpdated();
                    }, 'Удаление картинки');
                });
            });
        });
        
        //Проставим размеры
        $IMG.each(function() {
            var $img = $(this)
            PsResources.getImgSize($img.attr('src'), function(wh) {
                $img.next('div').html(wh ? wh.w+'x'+wh.h : PsHtml.span('Ошибка', 'error'));
            });
        });
        
        //Кнопки
        var $BUTTONS = $TAB.find('.ps-gallery-buttons button');
        
        $BUTTONS.filter('.save').button({
            icons: {
                primary: 'ui-icon-disk'
            }
        }).click(function() {
            $BUTTONS.uiButtonDisable();
            
            var images = [];
            //Получаем список дочерних элементов заново - порядок мог измениться
            $IMAGES.children('.image').each(function() {
                var $div = $(this);
                var file = $div.data('name');
                var name = $div.find('input[name="name"]').val();
                var descr = $div.find('input[name="descr"]').val();
                var show = $div.find('input[name="show"]').isChecked();
                var web  = $div.is('.web');
                images.push({
                    file: file,
                    name: name,
                    descr: descr,
                    show: show ? 1 : 0,
                    web: web ? 1 : 0
                });
            });
            
            GalleryAction({
                action: 'save',
                name: $GALLERY.find('[name="galname"]').val(),
                images: images,
                gallery: GALLERY
            }, function() {
                InfoBox.popupSuccess('Галлерея успешно сохранена');
                locationReload();
            }, function(err){
                InfoBox.popupError(err);
                $BUTTONS.uiButtonEnable();
            });
            
        });
        
        $BUTTONS.filter('.reload').button({
            text: false,
            icons: {
                primary: 'ui-icon-refresh'
            }
        }).click(function() {
            $BUTTONS.uiButtonDisable();
            locationReload();
        });
    }
    
    
    //Закладка добавления внешних картинок в галерею
    function PsGalleryAddWebImgController($TAB) {
        var $inputPath = $TAB.find('input.img');
        var $inputName = $TAB.find('input[name="name"]');
        var $inputDescr = $TAB.find('input[name="descr"]');
        
        var $state = $TAB.find('.state>span');
        var $img =   $TAB.find('img');
        var inpSrc = function() {
            return $.trim($inputPath.val());
        }
        var stateUpdate = function(msg, type) {//0 - info, 1 - success, 2 - error
            msg = $.trim(msg);
            $state.html(msg ? msg : '&nbsp;').removeClass('gray error success');
            if (msg) $state.addClass(type==0 ? 'gray' : (type==1 ? 'green' : 'error'));
        }
        $inputPath.keyup(function() {
            var src = inpSrc();
            $img.attr('src', src);
            if(!src) {
                stateUpdate();
                return;//---
            }
            stateUpdate('Загружаем картинку...', 0);
            PsResources.getImgSize(src, function(wh) {
                if (src!=inpSrc()) return;//Уже загружают другую
                if (wh) {
                    stateUpdate('Загружено успешно ['+wh.w+'x'+wh.h+']', 1);
                } else {
                    stateUpdate('Ошибка загрузки', 2);
                }
            });
        });
        
        //Кнопка сохранить
        var $BUTTONS = $TAB.find('.ps-gallery-buttons button');
        $BUTTONS.filter('.save').button({
            icons: {
                primary: 'ui-icon-disk'
            }
        }).click(function() {
            $BUTTONS.uiButtonDisable();
            
            var img = {};
            img.file = inpSrc();
            img.name = $inputName.val();
            img.descr = $inputDescr.val();
            
            GalleryAction({
                action: 'imgadd',
                img: img,
                gallery: GALLERY
            }, function() {
                InfoBox.popupSuccess('Картинка успешно добавлена');
                $inputPath.val('').keyup();
                $inputName.val('');
                $inputDescr.val('');
                onImageAdd();
            }, 'Добавление картинки', 
            function() {
                $BUTTONS.uiButtonEnable();
            });
        });
    
    }
    
    
    function PsGalleryAddFileImgController($TAB) {
        $TAB.find('#file_upload').psUploadify({
            postData: {
                gallery: GALLERY,
                type: 'GalleryImg'
            },
            onUploadComplete: onImageAdd
        });
    }
    
    if (!$GALLS_TABS.isEmptySet()) {
        new PsGalleryController($TAB_EDIT);
        new PsGalleryAddWebImgController($TAB_ADD_WEB_IMG);
        new PsGalleryAddFileImgController($TAB_ADD_FILE_IMG);
    }
});
