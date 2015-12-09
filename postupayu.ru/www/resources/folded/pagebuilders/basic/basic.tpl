<div id="carrier">
    <div id="CLIENT">
        <div id="header">
            <a href="index.php">{img name='oracle.png' alt=$host class='logo'}</a>
            {*<a href="index.php" class="logo">{PSHOST ucf=1}</a>*}
            <ul class="links">
                {header_links}
            </ul>
            <div style="clear:both"></div>
        </div>
        <div id="navigation_bar">
            {ipagehref item='sitemap'}.{/ipagehref}
            <ul class="navigation">
                <li class="gray">Построение навигации</li>
            </ul>
            {*navigation_bar*}
            <span class="controls">
                {'CONTROLHREFS'|ippanel}
                <a href="#RpShow" class="RpShow" title="Показать правую панель">{sprite name='maximize' nc=1}</a>
            </span>
        </div>
        <div id="mainPanel">
            <div id="leftPanel">
                <div id="content">
                    {$content}
                </div>
            </div>
            <div id="rightPanel">
                <a href="#RpHide" class="RpHide" title="Скрыть правую панель">{sprite name='minimize' nc=1}</a>
                {'RCOLUMN'|cbpanel}
            </div>
            <div style="clear:both"></div>
        </div>
        {page_footer}
    </div>
</div>