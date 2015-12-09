{$error}

{*Ошибки*}
{if $errors}
    <div class="info_box err">
        {foreach $errors as $type => $content}
            {if !empty($content)}
                <h5>Ошибки {$type}.ini</h5>
                <ol>
                    {foreach $content as $error}
                        <li>{$error}</li>
                    {/foreach}
                </ol>
            {/if}
        {/foreach}
    </div>
{/if}

{* ALL TABLES *}
{if $mode=='list'}
    <ul class="ap-navigation">
        {foreach $tables as $table}
            <li>
                <a href="{AP_APRecEdit::urlTableRows($table)}" {if $table->hasModified()}class="red"{/if}>{$table->getName()}</a>
                {if $table->hasModified('F')}
                    {img dir='icons/figure' name='file-plus.png' title='Есть только в файле'}
                {/if}
                {if $table->hasModified('M')}
                    {img dir='icons/figure' name='edit.png' title='Ecть модифицированные данные'}
                {/if}
                {if $table->hasModified('D')}
                    {img dir='icons/figure' name='db-plus.png' title='Есть только в базе'}
                {/if}
                <a href="{AP_APRecEdit::urlTableSql($table)}" class="small">inserts</a>
                <a href="{AP_APRecEdit::urlTableArr($table)}" class="small">data</a>
            </li>
        {/foreach}
    </ul>
    <div class="add-ctrls"><a href="{AP_APRecEdit::urlInserts()}">Все inserts</a></div>
{/if}


{* INSERTS *}
{if $mode=='inserts'}
    {foreach $tables as $table}
        <h5>{$table->getName()}</h5>
        {foreach $table->exportDataAsInserts() as $insert}
            <div class="sql">{html_4show($insert)};</div>
        {/foreach}
        <br/>
    {/foreach}
{/if}


{* TABLE ARR *}
{if $mode=='arr'}
    {foreach $table->getDataFromFile() as $data}
        <pre>{print_r($data, true)}</pre>
        <br/>
    {/foreach}
{/if}


{* TABLE SQL *}
{if $mode=='sql'}
    {foreach $table->exportDataAsInserts() as $insert}
        <div class="sql">{html_4show($insert)};</div>
    {/foreach}
{/if}


{* ROWS *}
{if $mode=='rows'}
    <h3>{$table->getName()}</h3>
    <div class="separetedtb">
        {$table->getComment()}
    </div>

    {if $table->hasFoldings()}
        <div class="dep-foldings">
            Фолдинги:
            {foreach $table->getFoldings() as $folding}
                <a href="{AP_APFoldingEdit::url($folding->getFoldingType(), $folding->getFoldingSubType())}" target="_blank">{$folding->getEntityName()}</a>{if not $folding@last},{/if}
            {/foreach}
        </div>
    {/if}

    <table class="colored database highlighted" data-table="{$table->getName()}">
        <thead>
            <tr>
                {foreach $table->getColumns() as $id=>$col}
                    {if $col->isPk()}
                        <th class="fetched"></th>
                    {else}
                        {if $col->isVisibleInTable()}
                            <th>{$id}</th>
                        {/if}
                    {/if}
                {/foreach}
                {if $table->hasFoldings()}
                    <th class="fetched"></th>
                {/if}
                <th class="fetched"></th>
                <th class="fetched"></th>
            </tr>
        </thead>
        <tbody>
            {foreach $rows as $row}
                <tr>
                    {foreach $table->getColumns() as $id=>$col}
                        {if $col->isVisibleInTable()}
                            <td>{$col->safe4show($row[$id])}</td>
                        {/if}
                    {/foreach}
                    {*
                    <td class="img-ctrls">
                    <a href="{AP_APRecEdit::urlRecEdit($table, $row)}" class="clickable"><img src="/resources/images/icons/figure/edit.png"/></a>&nbsp;
                    <a href="{AP_APRecEdit::urlRecDelete($table, $row)}" class="clickable"><img src="/resources/images/icons/figure/delete.png"/></a>
                    </td>
                    *}
                    {if $table->hasFoldings()}
                        <td class="td-ctrl create">
                            {$foldingEnt=$table->getFoldingEntity4DbRec($row, true)}
                            {if $foldingEnt}
                                <a href="{AP_APFoldingEdit::urlFoldingEdit($foldingEnt)}" title="Редартирование фолдинга" target="_blank">фол</a>
                            {else}
                                &mdash;
                            {/if}
                        </td>
                    {/if}
                    <td class="td-ctrl edit">
                        <a href="{AP_APRecEdit::urlRecEdit($table, $row)}" title="Редартирование записи БД">ред</a>
                    </td>
                    <td class="td-ctrl delete">
                        <a href="{AP_APRecEdit::urlRecDelete($table, $row)}" title="Удаление записи из БД">уд</a>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
    <h5 class="align-right">
        <a href="{$addurl}" class="clickable">
            <img src="/resources/images/icons/figure/add.png"/>
        </a>
    </h5>

    {if !empty($modified['D'])}
        <h3>
            <img src="/resources/images/icons/figure/db-plus.png"/>
            Новые записи в таблице:
        </h3>
        <ol class="diffs">
            {foreach $modified['D'] as $rowId=>$row}
                <li>
                    <h5>Данные:</h5>
                    <table class="colored database">
                        <thead>
                            <tr>
                                {foreach $table->getTake4ExportColumns() as $id=>$col}
                                    {if $col->isPk()}
                                        <th class="fetched"></th>
                                    {else}
                                        <th>{$id}</th>
                                    {/if}
                                {/foreach}
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                {foreach $table->getTake4ExportColumns() as $id=>$col}
                                    <td>{$col->safe4show($row['ROW'][$id])}</td>
                                {/foreach}
                            </tr>
                        </tbody>
                    </table>

                    <h5>Запрос на вставку:</h5>
                    <div class="sql">{html_4show($row['SQLI'])}</div>

                    <h5>Запрос на удаление:</h5>
                    <div class="sql">{html_4show($row['SQL'])}</div>
                    <div class="rec-control">
                        {psctrl confirm='Удалить строку из базы' id=$rowId}
                    </div>

                    {hidden toggle=1 text='Массив'}
                    <h5>Массив:</h5>
                    <pre>{print_r($row['ROW'], true)}</pre>
                    {/hidden}
                </li>
            {/foreach}
        </ol>
    {/if}

    {if !empty($modified['M'])}
        <h3>
            <img src="/resources/images/icons/figure/edit.png" title="Запись была модифицирована"/>
            Модифицированные записи:
        </h3>
        <ol class="diffs">
            {foreach $modified['M'] as $rowId=>$row}
                <li>
                    <h5>Изменения:</h5>
                    <table class="colored database">
                        <thead>
                            <tr>
                                <th class="fetched"></th>
                                {foreach $table->getTake4ExportColumns() as $id=>$col}
                                    {if $col->isPk()}
                                        <th class="fetched"></th>
                                    {else}
                                        <th>{$id}</th>
                                    {/if}
                                {/foreach}
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b>База</b></td>
                                {foreach $table->getTake4ExportColumns() as $id=>$col}
                                    <td>{$col->safe4show($row['DROW'][$id])}</td>
                                {/foreach}
                            </tr>
                            <tr>
                                <td><b>Файл</b></td>
                                {foreach $table->getTake4ExportColumns() as $id=>$col}
                                    <td>{$col->safe4show($row['FROW'][$id])}</td>
                                {/foreach}
                            </tr>
                        </tbody>
                    </table>

                    <h5>Запрос для обновления до варианта из файла:</h5>
                    <div class="sql">{html_4show($row['SQL'])}</div>
                    <div class="rec-control">
                        {psctrl confirm='Принять вариант из файла' id=$rowId}
                    </div>

                    {hidden toggle=1 text='Сравнение массивов'}
                    <h5>Данные из базы:</h5>
                    <pre>{print_r($row['DROW'], true)}</pre>

                    <h5>Данные из файла:</h5>
                    <pre>{print_r($row['FROW'], true)}</pre>
                    {/hidden}
                </li>
            {/foreach}
        </ol>
    {/if}

    {if !empty($modified['F'])}
        <h3>
            <img src="/resources/images/icons/figure/file-plus.png"/>
            Новые записи из файла:
        </h3>
        <ol class="diffs">
            {foreach $modified['F'] as $rowId=>$row}
                <li>
                    <h5>Данные:</h5>
                    <table class="colored database">
                        <thead>
                            <tr>
                                {foreach $table->getTake4ExportColumns() as $id=>$col}
                                    {if $col->isPk()}
                                        <th class="fetched"></th>
                                    {else}
                                        <th>{$id}</th>
                                    {/if}
                                {/foreach}
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                {foreach $table->getTake4ExportColumns() as $id=>$col}
                                    <td>{$col->safe4show($row['ROW'][$id])}</td>
                                {/foreach}
                            </tr>
                        </tbody>
                    </table>

                    <h5>Запрос:</h5>
                    <div class="sql">{html_4show($row['SQL'])}</div>
                    <div class="rec-control">
                        {psctrl confirm='Вставить строку в базу' id=$rowId}
                    </div>

                    {hidden toggle=1 text='Массив'}
                    <h5>Массив:</h5>
                    <pre>{print_r($row['ROW'], true)}</pre>
                    {/hidden}
                </li>
            {/foreach}
        </ol>
    {/if}

    {if $table->hasModified()}
        {*Кнопка экспорта в файл*}
        <div class="controls">
            <button class="export" title="В файл {$table->getName()} будет выгружено текущее состояние таблицы">Экспортировать в файл</button>
            <button class="import" title="В таблицу {$table->getName()} будет выгружено состояние из файла">Откатиться до файла</button>
        </div>
    {/if}
    <br/><br/><br/>
{/if}


{* CREATE/EDIT/DELETE *}
{if in_array($mode, array('add', 'edit', 'delete'))}
    <h3 class="header">
        <img src="{"/resources/images/icons/figure/`$mode`.png"}"/> {$table->getName()}
    </h3>
    {form form_id='RecEditForm'}
{/if}