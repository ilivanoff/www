<h4><span>Утилиты</span></h4>

<p>
    В данном разделе собраны утилиты, написанные мною специально для Вас с целью дать инструмент для
    исследования того или иного вопроса.
</p>
<p>
    Все они открываются в отдельном окне, чтобы Вы могли получить к ним быстрый доступ в любой момент 
    времени. Например, Вы можете быстро вызвать калюкулятор, работая над занятием кружка.
</p>

{authed}
<p>
    В любой момент Вы можете добавить утилиту в избранное или убрать её отсюда.
</p>
{/authed}

{notauthed}
<p>
    Выполнив <a href="#Login">авторизацию</a> {gray}(или {page_href code=$smarty.const.PAGE_REGISTRATION}регистрацию{/page_href}){/gray},
    Вы сможете редактировать список предложенных здесь утилит, отмечая те из них, которые понравились Вам больше всего.
</p>
{/notauthed}

<p>
    Список всех доступных утилит находится <a href="#" pageIdent="">здесь</a>.
</p>


<h5 class="title">
    Список плагинов:
    {authed}
    {if count($pages)>1}
        <span class="tools-ctrl ps-ui-btn-small">
            <button class="sort">Сортировать</button>
            <button class="cancel">Отменить</button>
        </span>
    {/if}
    {/authed}
</h5>

{if empty($pages)}
    <div class="no_items">Не выбран ни один плагин</div>
{else}
    <div class="tools">
        {foreach $pages as $page}
            <div class="tool" data-type="{$page.type}" data-ident="{$page.ident}">
                <a href="#" pageIdent="{$page.url}" class="clickable tool_cover"><img src="{$page.cover}"/></a>
                <div class="tool-content">
                    <h4><a href="#" pageIdent="{$page.url}">{$page.name}</a></h4>
                    {text}{$page.descr}{/text}
                </div>
            </div>
        {/foreach}
    </div>
{/if}