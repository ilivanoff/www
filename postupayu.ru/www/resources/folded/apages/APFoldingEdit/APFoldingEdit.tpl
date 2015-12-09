{* ALL FOLDINGS *}
{if $mode=='list'}
    <ul class="ap-navigation">
        {foreach $foldings as $folding}
            <li><a href="{AP_APFoldingEdit::urlFoldingEntitys($folding)}">{$folding->getEntityName()} ({$folding->getFoldingGroup()})</a></li>
        {/foreach}
    </ul>
{/if}


{* CONTENT *}
{if $mode=='content'}
    <div class="ps-tabs" id="ps-folding-edit-tabs">
        <div title="Сущности">
            <ul class="ap-navigation">
                {foreach $folding->getAccessibleFoldedEntitys() as $entity}
                    <li class="level2 f-entity">
                        <a href="{AP_APFoldingEdit::urlFoldingEdit($entity)}">{$entity->getIdent()}</a>
                    </li>
                {/foreach}
            </ul>
        </div>

        <div title="Создание">
            {form form_id='AdminFoldingCreateForm'}
        </div>

        <div title="Загрузка">
            {form form_id='AdminFoldingUploadForm'}
        </div>

        {if $folding->hasLists()}
            <div title="Списки">
                <ul class="ap-navigation lists" data-unique="{$folding->getUnique()}">
                    {foreach $folding->getLists() as $list}
                        <li><a href="{AP_APFoldingEdit::urlFoldingListEdit($folding, $list)}">{$list}</a></li>
                    {/foreach}
                </ul>
            </div>
        {/if}

    </div>

    <br/>
{/if}


{*LIST EDIT*}
{if $mode=='list_edit'}
    <ul class="sortable-list-content" data-unique="{$folding->getUnique()}" data-list={$list}>
        {foreach $listIdents as $ident=>$state}
            <li>
                <input type="checkbox" {if $state.i}checked="checked"{/if} title="Включать ли сущность в список"/>
                <span>{$ident}</span>
                <input type="checkbox" {if $state.m}checked="checked"{/if} title="Отмечена ли сущность"/>
            </li>
        {/foreach}
    </ul>
    <button id="save-list">Сохранить</button>
{/if}


{*INFO TEMPLATES LIST*}
{if $mode=='tpls_list'}
    <ul class="sections">
        {foreach $tplsList as $tpl}
            <li class="level2"><a href="{AP_APFoldingEdit::urlFoldingTplInfoEdit($entity, $tpl)}">{$tpl->getInfoRelPath()}</a></li>
        {/foreach}
    </ul>
{/if}


{*INFO TEMPLATE EDIT*}
{if $mode=='tpl_edit'}
    {form form_id='AdminFoldingInfoTplEditForm'}
    <h5>Параметры для шаблона:</h5>
    <div  id="tpl-smarty-params">
        <input type="text" /> {ctrl_button action='accept' name='accept' title='Применить параметры'}
    </div>
    <h5>Предпросмотр:</h5>
    {$content}
    <br/><br/>
{/if}



{* EDIT *}
{if $mode=='edit'}
    <div class="separetedtb">{$info} <span class="controls">{$patterns} {$sprite} {$download}</span></div>

    {$error}

    {form form_id='AdminFoldingEditForm'}

    {if $content}
        <hr/>

        <div class="ap_controls">
            <a href="#">Переключить вид</a>
        </div>

        <div class="preview">
            {$content}
        </div>

        <div class="ap_controls">
            <a href="#">Переключить вид</a>
        </div>
    {/if}
{/if}