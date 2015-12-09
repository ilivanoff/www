{if $mode=='list'}
    <div class="ps-tabs ps-gallery-menu">
        <div title="Список">
            {if empty($galls)}
                <div class="no_items">Нет галерей</div>
            {else}
                <div class="controls">
                    <a href="{AP_APGalleries::urlLazy()}">список</a>
                </div>
                <ul class="sections all">
                    {foreach $galls as $name=>$gall}
                        <li class="level2"><a href="{AP_APGalleries::urlGall($name)}">{$name} {gray}({$gall->getName()}, {$gall->getCount()}){/gray}</a></li>
                    {/foreach}
                </ul>
            {/if}
        </div>
        <div title="Создание" class="create-new">
            <table class="ps-gal-settings">
                <tr>
                    <td class="fetched">Директория:</td>
                    <td><input type="text" name="gallery" value=""/></td>
                </tr>
                <tr>
                    <td>Название:</td>
                    <td><input type="text" name="name" value=""/></td>
                </tr>
            </table>
            <div class="ps-gallery-buttons">
                <button class="save">Сохранить</button>
            </div>
        </div>
    </div>
{/if}



{if $mode=='lazy'}
    <ol>
        {foreach $galls as $gall}
            <li>
                {$gall->getName()}
                {gallery name=$gall->getDir() lazy=1}
                <br/>
            </li>
        {/foreach}
    </ol>
{/if}

{if $mode=='gall'}
    <div id="ps-gallery-img-menu" class="ps-tabs" data-name="{$info->getDir()}">
        <div title="Редактирование" class="ps-gallery-controller">

            <div class="gallery">
                <table class="ps-gal-settings">
                    <tr>
                        <td class="fetched">Название:</td>
                        <td><input type="text" value="{$info->getName()}" name="galname"/></td>
                    </tr>
                </table>
            </div>

            {*Для кнопок изменения размеров используем фиксированную позицию*}
            <div class="hg-self fixed" title="Размеры картинок">
                <a href="#100"></a>
                <a href="#150"></a>
                <a href="#200"></a>
                <a href="#250"></a>
            </div>

            {*Для отображение информации о картинке мы используем таблицу, чтобы потом легко масштабировать её, меняя размеры картинок*}
            <div class="select-all">
                <label><input type="checkbox" /> выделить всё <span class="count gray"></span></label>
            </div>

            <ul class="images">
                {foreach $info->getAllGalleryItems() as $img}
                    {$web=$img->isWeb()}
                    {$file=$img->getFile()}
                    <li class="image {if $web}web{else}local{/if}" data-name="{$file}">
                        <div class="imgctrl">
                            <a href="{$img->getRelPath()}" target="_blank" class="nobg open"></a>
                            <a href="#" class="nobg remove"></a>
                        </div>
                        <table class="image-table">
                            <tr>
                                <td class="image-holder fetched">
                                    <img src="{$img->getRelPath()}" class="preview"/>
                                    <div>Загрузка...</div>
                                    {*
                                    Вычисляем на js
                                    <div>
                                    {$img->di()->getImageAdapter()->getWidth()}x{$img->di()->getImageAdapter()->getHeight()},
                                    {$img->di()->getSize()|fsize:true}
                                    </div>
                                    *}
                                </td>
                                <td>
                                    <div class="info">
                                        <table class="ps-gal-settings">
                                            <tr>
                                                <td class="fetched">Название:</td>
                                                <td><input type="text" name="name" value="{$img->getName()}"/></td>
                                            </tr>
                                            <tr>
                                                <td>Описание:</td>
                                                <td><input type="text" name="descr" value="{$img->getDescr()}"/></td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="path">
                                    <label><input type="checkbox" {if $img->isShow()}checked="checked"{/if} name="show"/> {$file}</label>
                                </td>
                            </tr>
                        </table>

                    </li>
                {/foreach}
            </ul>

            <div class="ps-gallery-buttons">
                <button class="save">Сохранить</button>
                <button class="reload">Перезагрузить</button>
            </div>

            {gallery name=$info->getDir() lazy=1}
        </div>

        <div title="Загрузить web" class="web-img-add">
            <input type="text" name="img" class="img" placeholder="Путь к внешней картинке" />
            <div class="state"><span>&nbsp;</span></div>
            <img/>

            <table class="ps-gal-settings">
                <tr>
                    <td class="fetched">Название:</td>
                    <td><input type="text" name="name" value=""/></td>
                </tr>
                <tr>
                    <td>Описание:</td>
                    <td><input type="text" name="descr" value=""/></td>
                </tr>
            </table>

            <div class="ps-gallery-buttons">
                <button class="save">Сохранить</button>
            </div>
        </div>

        <div title="Загрузить файл" class="file-img-add">
            <input id="file_upload" name="file_upload" type="file" />
        </div>
    </div>
{/if}