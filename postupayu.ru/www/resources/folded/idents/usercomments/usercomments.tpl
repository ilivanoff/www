<h4>Новые ответы на Ваши сообщения</h4>
<p>
    В данном разделе Вы можете оперативно видеть те Ваши сообщения, которые были прокомментированы
    другими участниками проекта.
</p>
<br/>

<div id="user_posts_comments">
    <h2 class="upc_cnt">
        Всего ответов {ldelim}<span></span>{rdelim}:
    </h2>
    {foreach $trees as $postsItems}
        {foreach $postsItems as $pItem}
            {$post=$pItem->getPost()}
            <div class="user_post_comments">
                {post_href post=$post}
                {img post=$post dim='96x' class='cover'}
                {/post_href}

                <div class="upc_content">
                    <h3 class="upc_post_name">{$post->getName()}</h3>
                    {foreach $pItem->getItems() as $cItem}
                        <div class="upc_comments">
                            {$cItem->discHtml()}
                            <div class="upc_controls">
                                {ctrl_button href=$cItem->commentUrl() blank='1' name='next' title='Перейти к комментарию'}
                            </div>
                        </div>
                    {/foreach}
                </div>
                <div class="clearall"></div>
            </div>

        {/foreach}
    {/foreach}
</div>