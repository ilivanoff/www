<h2>Сообщения<span class="mcount"></span>:</h2>
<ul class="discussion">
    {foreach $msgs as $msg}
        <li class="msg root">
            <div class="comment">
                <div class="comment-content">
                    <div class="meta">
                        <span class="author">{$msg->getUserName()}</span>
                        <span class="date">{$msg->getDtEvent()}</span>
                    </div>
                    {if $msg->getContacts()}
                        <div><i>Контакты:</i> {$msg->getContacts()}</div>
                    {/if}
                    <h4>{$msg->getTheme()}</h4>
                    <div class="comment-text">{$msg->getContent()}</div>
                    {psctrl id=$msg->getId() delete='Удалить'}
                </div>        
            </div>
        </li>
    {/foreach}
</ul>