{$user->getAvatarImg(PsUser::ID_CARD_AVATAR_DIM)}
<div class="content_carrier">
    <div class="content">
        <div class="user_name">{$user->getName()}{if $user->isAuthorised()}{page_href code=$smarty.const.PAGE_OFFICE class='current'}(Вы){/page_href}{/if}</div>        <div class="block">
            <div class="regdate">Дата регистрации:</div>
            {$user->getDtReg()}
        </div>

        {if !isEmpty($user->getAbout())}
            <div class="block">
                <div class="about">О себе:</div>
                {$user->getAbout()}
            </div>
        {/if}

        {if !isEmpty($user->getContacts())}
            <div class="block">
                <div class="contacts">Контакты:</div>
                {$user->getContacts()}
            </div>
        {/if}

        {if !isEmpty($user->getMsg())}
            <div class="message">
                {$user->getMsg()}
            </div>
        {/if}

    </div>
    <div class="clearall"></div>
</div>
