<h4>Новости портала</h4>

<p>
    Данный раздел поможет Вам всегда оставаться в курсе последних событий проекта &mdash; 
    когда были опубликованы те или иные заметки/уроки/выпуски журнала. Прямо из данного 
    раздела можно переходить непосредственно к самим постам.
</p>

<h2>Показано новостей: {ldelim}<span id="news_cnt"></span>{rdelim}</h2>

{*<div id="ps-news-datepicker" class="ps-centered-datepicker"></div>*}

{$line.line}

{if $line.has_more}
    <div id="load_news">
        <button>Загрузить ещё новости</button>
    </div>
{/if}