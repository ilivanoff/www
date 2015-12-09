<div id="carrier">
    <div id="mainPanel">
        <div id="leftPanel">
            <div id="content">
                {$content}
                {*task_test*}
            </div>
        </div>
        <div id="rightPanel">
            {text}
            {section start='1' loop=$cnt name='bar'}
                {$idx=$smarty.section.bar.index}
                <a href="test.php?num={$idx}">Тестовая страница {$idx}</a>
            {/section}
            {/text}


            <br/>
            <h4>Специальные страницы</h4>
            {text}
            <a href="test.php?pagetype=patterns">Шаблоны</a>
            <a href="test.php?pagetype=testmethods">Тестовые методы</a>
            <a href="test.php?pagetype=smarty">Функции Smarty</a>
            {/text}


            <br/>
            <h4>Картинки</h4>
            {text}
            <a href="test.php?pagetype=doubleimg">Дублирующиеся картинки</a>
            <a href="test.php?pagetype=imgbysize">Картинки по весу</a>
            <a href="test.php?pagetype=formules">Формулы</a>
            {/text}


            {foreach $processors as $proc}
                <br/>
                <h4>{$proc->postsTitle()}:</h4>
                {text}
                {foreach $proc->getPosts() as $post}
                    <a href="test.php?type={$post->getPostType()}&post_id={$post->getId()}">{$post->getName()}</a>
                {/foreach}
                {/text}
            {/foreach}

        </div>
        <div style="clear:both"></div>
    </div>
</div>