{if $mode=='folders'}
    {$empty=empty($folders)}

    {if not $empty}
        <ul class="sections">
            <li class="level1"><a href="{AP_APLogs::urlFolders()}">Логи {if $num}[{$num}]{/if}</a></li>
            {foreach $folders as $folder}
                <li class="level2"><a href="{AP_APLogs::urlFolder($folder->getName())}">{$folder->getName()}</a></li>
            {/foreach}
        </ul>
    {/if}

    <div class="ctrl align-center">
        {if $enabled}
            <button class="off">Отключить</button>
        {else}
            <button class="on">Включить</button>
            {if not $empty}
                <button class="reset">Очистить</button>
            {/if}
        {/if}
        <button class="reload">Перезагрузить</button>
    </div>
{/if}

{if $mode=='files'}
    <ul class="sections">
        <li class="level1"><a href="{AP_APLogs::urlFolders()}">Логи {if $num}[{$num}]{/if}</a></li>
        <li class="level2"><a href="{AP_APLogs::urlFolder($folder)}">{$folder}</a></li>
        {foreach $files as $file}
            <li class="level3"><a href="{AP_APLogs::urlFile($folder, $file->getName())}">{$file->getNameNoExt()}</a></li>
        {/foreach}
    </ul>
{/if}

{if $mode=='file'}
    <ul class="sections">
        <li class="level1"><a href="{AP_APLogs::urlFolders()}">Логи {if $num}[{$num}]{/if}</a></li>
        <li class="level2"><a href="{AP_APLogs::urlFolder($folder)}">{$folder}</a></li>
        {foreach $files as $ifile}
            <li class="level3" id="{$ifile->getName()}">
                {if $ifile->getName()==$file->getName()}
                    <a href="{AP_APLogs::urlFolder($folder)}" class="current">{$ifile->getNameNoExt()}</b></a>
                    <table class="logs">
                        <tbody>
                            {foreach $file->getFileLines() as $line}
                                <tr>
                                    <td>
                                        {nl2br(html_4show($line))}
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                {else}
                    <a href="{AP_APLogs::urlFile($folder, $ifile->getName())}">{$ifile->getNameNoExt()}</a>
                {/if}
            </li>
        {/foreach}
    </ul>
{/if}