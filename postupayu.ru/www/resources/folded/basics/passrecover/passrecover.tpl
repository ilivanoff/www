{*Получение кода для восстановления пароля*}
{if $mode=='get'}
    <h4>Не можете авторизоваться? Пройдите процедуру <span>восстановления пароля</span>.</h4>
    <p>
        Введите электронный адрес, указанный Вами при регистрации. На него будет отправлено письмо
        с сылкой на страницу смены пароля.
    </p>

    {form form_id='RecoverGetCodeForm'}
{/if}

{*Использование кода восстановления пароля*}
{if $mode=='use'}
    <h4>Установка нового пароля.</h4>
    {if $error}
        <div class="info_box err">
            Код восстановления <b>{$code}</b> не может быть использован:
            <br>
            {$error}.
        </div>
        <p>
            Просьба повторно пройти процедуру {page_href code=$smarty.const.PAGE_PASS_REMIND}восстановления пароля{/page_href}.
        </p>
    {else}
        <p>
            Введите новый пароль, а также его подтверждение.
        </p>
        {form form_id='RecoverUseCodeForm' code=$code}
    {/if}
{/if}
