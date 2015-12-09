{*
Футер в popup окнах всегда расположен внизу страницы. Источник:
http://matthewjamestaylor.com/blog/keeping-footers-at-the-bottom-of-the-page
*}
<div id="footer">
    <p>
        &copy; Иванов Илья, {$smarty.const.COPY_DATE_FROM} &mdash; {$smarty.const.COPY_DATE_TO} | <a href="http://{PSHOST}" {if $_blank}target='_blank'{/if}>{PSHOST}</a>
        <br />
        Использование материалов допускается только с сохранением рабочей ссылки на сайт.
    </p>
</div>