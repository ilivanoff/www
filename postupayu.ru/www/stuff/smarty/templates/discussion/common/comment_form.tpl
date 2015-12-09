<li class="discussion-form">
    {$avatar}
    <div class="comment">
        <div class="comment-content">
            <span class="form_closer"></span>

            {authed}
            <div class="form-tools">
                {ctrl_button action='formula' title='Справка по работе с формулами' type='popup'}
                {ctrl_button action='fullscreen' title='Редактировать в отдельном окне' hoverable='0'}
                {ctrl_button action='show_parent' title='Показывать родительский комментарий' type='trigger'}
                <!--<input type="checkbox" value="1" class="process_formules" title="Обрабатывать формулы по мере набора"/>-->
            </div>
            {if $themed}
                <div>
                    <input type="text" placeholder="Введите тему..." class="theme"/>
                </div>
            {/if}
            <textarea cols="" rows=""></textarea>
            <div>
                <input class="button" type="submit" value="Сохранить"/>
            </div>
            {/authed}

            {notauthed}
            <p class="first">Для добавления комментариев необходимо <a href="#Login">автризоваться</a>.</p>
            <p>Если у Вас ещё нет учётной записи, то сначала необходимо {page_href code=$smarty.const.PAGE_REGISTRATION}зарегистрироваться{/page_href}.</p>
            {/notauthed}

        </div>
    </div>
</li>
