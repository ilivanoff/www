<div {$class}>
    {foreach $actions as $action=>$name}
        <a href="#{$id}" class="{$action}">{$name}</a>
    {/foreach}
    {*if $psctrl_edit}<a href="#" class="edit">Редактировать</a>{/if}
    {if $psctrl_copy}<a href="#" class="copy">Копировать</a>{/if}
    {if $psctrl_confirm}<a href="#" class="confirm">Подтвердить</a>{/if}
    {if $psctrl_view}<a href="#" class="view">Просмотр</a>{/if}
    {if $psctrl_user}<a href="#" class="user">Пользователь</a>{/if}
    {if $psctrl_reply}<a href="#" class="reply">Ответить</a>{/if}
    {if $psctrl_delete}<a href="#" class="delete">Удалить</a>{/if*}
</div>
