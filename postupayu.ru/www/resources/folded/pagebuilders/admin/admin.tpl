{if $authed}
    <!--PAGE START-->
    <div id="carrier">
        <!--ЗАГОЛОВОК-->

        <div id="header">
            <!--логотип-->
            {page_href code=$smarty.const.PAGE_ADMIN class="logo"}
            <img src="admin/resources/images/tools.png" alt="Панель администратора"/>
            {/page_href}

            <!--ссылки-->
            {$pagesLayout}

            <!--логотип-->
            <div class="adminControls">
                {page_href blank=1}.{/page_href}
                <a href="#" class="edit">Редактировать</a>
                <a href="#" class="logout">Выход</a>
            </div>

            <div style="clear:both"></div>
        </div>
        {AdminPageNavigation::inst()->html()}
        <div id="adminPageContent" class="{$page->getPageIdent()}">
            <div class="adminPageContainer">
                {$content}
            </div>
        </div>
    </div>
{else}
    {literal}
        <style>
            body {
                background-color: #F0F0F0
            }
        </style>
    {/literal}
    <div id="carrier" style="width: 200px; margin: 0px auto">
        {form form_id='AdminLoginForm'}
    </div>
{/if}