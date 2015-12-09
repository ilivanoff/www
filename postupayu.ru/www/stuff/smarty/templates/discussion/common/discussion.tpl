<div>
    <h2>Сообщения<span class="mcount"></span>:</h2>

    <div class="discussion-ctrl">
        {ctrl_button action='add_comment' title='Написать'}
        {ctrl_button action='simple_view' title='Упрощённый вид дерева сообщений' type='trigger'}
        {ctrl_button action='highlight' title='Подсветка родительских сообщений' type='trigger'}
    </div>

    <ul class="discussion default" {$data}>
        {$tree}
        {if $has_more}
            <li class="load"><button>Загрузить сообщения...</button></li>
        {/if}
        {$form}
    </ul>
</div>