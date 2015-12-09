<div id="admin_panel">
    <ul>
        <li class="confirmable"><a href="#toggledev" class="reload">{devmode}DEVMODE{/devmode}{production}{green}PRODUCTION{/green}{/production}</a></li>
        <li><hr /></li>

        <li class="MathJaxIndicator"></li>
        <li><hr /></li>
        <li class="SpeedTest"><a href="#">Протестировать скорость</a></li>

        <li><hr /></li>

        <li class="confirmable"><a href="#ccache">Очистка кешей</a></li>
        <li><hr /></li>

        {if $smarty.const.NORMALIZE_PAGE}
            <li class="active">Нормализация страниц включена</li>
        {else}
            <li class="inactive">Нормализация страниц отключена</li>
        {/if}

        {if PsDefines::getReplaceFormulesType() == PsDefines::F_REPLACE_NONE}
            <li class="inactive">
                Формулы не заменяются
            </li>
        {else}
            <li class="active">
                Замена формул на картинки: {PsDefines::getReplaceFormulesType()}
            </li>
        {/if}

        <li><hr /></li>
    </ul>
</div>