<h4>Добро пожаловать в <span>личный кабинет</span>.</h4>

<div class="office">

    {$user->getIdCard()}

    {*
    <ul class='controls'>
    <li class='avatar'><a href='#'>Аватар</a></li>
    <li class='edit'><a href='#'>Личные данные</a></li>
    <li class='pass'><a href='#'>Смена пароля</a></li>
    </ul>
    <div class="clearall"></div>
    *}

    {*
    <p>В данном разделе Вы можете изменить параметры, указанные Вами при регистрации.</p>
    <p>Всю информацию о Вашем аккаунте в рамках проекта Вы можете просмотреть, пользуясь ссылками на панели справа.</p>
    <p>Ниже приводится описание всех разделов, а также дополнительные возможности, доступные только из личного кабинета.</p>
    <div style='clear:both'></div>
    *}
    <!--
    -->
    {tool name='Аватар' img='avatar.png'}
    {tooldescr}
    В данном разделе Вы можете установить свой аватар.
    Загрузите два аватара, чтобы выбрать между ними.
    {/tooldescr}

    {toolbody}
    <div class="avatars">
        {foreach $avatars as $avId=>$avRel}
            <div id="{$avId}" class="avatar_holder">{img name=$avRel}</div>
        {/foreach}
    </div>

    <input id="file_upload" name="file_upload" type="file" />

    <div class="avatar-controls">
        <div><a class="main disabled" href="#upload">Загрузить аватар</a></div>
        <div><a class="main" href="#set">Установить аватар</a></div>
        <div><a class="main" href="#del">Удалить аватар</a></div>
    </div>
    {/toolbody}

    {/tool}

    {tool name='Личные данные' img='idcard.png'}
    {tooldescr}
    Если потребуется, вы можете изменить данные, указанные при регистрации.
    Также можете настроить свою визитную карточку, которая видна другим пользователям.
    {/tooldescr}

    {toolbody}
    {form form_id='RegEditForm'}
    {/toolbody}
    {/tool}

    {tool name='Временная зона' img='timezone.png'}
    {tooldescr}
    В данном разделе можно поменять временную зону, в которой Вы находитесь.
    Укажите её, пожалуйста, правильно. В противном случае даты на сайте будут отображаться некорректно.
    {/tooldescr}

    {toolbody}
    {form form_id='TzEditForm'}
    {/toolbody}
    {/tool}

    {tool name='Смена пароля' img='change_pass.png'}
    {tooldescr}
    Если потретубется, вы можете изменить свой пароль.
    Для этого нажмите <a href="#" class="tool_href">редактировать</a>.
    {/tooldescr}

    {toolbody}
    {form form_id='PassChangeForm'}
    {/toolbody}
    {/tool}
</div>