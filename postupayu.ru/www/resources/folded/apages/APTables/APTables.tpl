{if $mode=='list'}
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

    {*Табы со скоупами*}
    <div id="APTables-tab" class="ps-tabs">
        {$configuredProp=PsTableColumnProps::TABLE_CONFIGURED()}

        {*Данные*}
        {foreach $data as $type => $content}
            {$isIni=ends_with($type, '.ini')}
            <div title="{$type}" class="tab" data-type="{$type}">
                {if $isIni}
                    <textarea codemirror="scheme">{$content}</textarea>
                {else}

                    {foreach $content as $tableName => $table}
                        {$selected=$table->isEditable()}
                        <div class="table{if $selected} selected was-selected{/if}" data-name="{$table->getName()}">
                            <input type="checkbox" value={$configuredProp->name()} class="selecttable" {if $selected}checked="checked"{/if} title="{$configuredProp->getLongText()}" />

                            <div class="content">

                                <div class="table-controller">
                                    <a href="{AP_APTables::urlTableRows($table)}">{$table->getName()}</a>
                                    <span class="up-down">
                                        <span class="down">&dArr;</span>
                                        <span class="up">&uArr;</span>
                                    </span>
                                </div>

                                <div class="if-selected">

                                    {*Настройки самой таблицы*}
                                    <div class="table-settings">
                                        {foreach $table->getAllowedTableProperties() as $propId=>$prop}
                                            {if $prop!=$configuredProp}
                                                <div title="{$prop->getLongText()}">
                                                    <label>
                                                        <input type="checkbox" value="{$propId}" {if $table->isProperty($propId)}checked="checked"{/if} /> {$prop->getShortText()}
                                                    </label>
                                                </div>
                                            {/if}
                                        {/foreach}
                                    </div>


                                    <table class="colored database table-col-props">
                                        <thead>
                                            <tr>
                                                <th class="fetched"></th>
                                                {foreach $table->getColumnsConfigurable() as $id=>$col}
                                                    <th>{$id}</th>
                                                {/foreach}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {foreach $table->getAllowedColumnProperties() as $propId=>$prop}
                                                <tr data-type="{$propId}">
                                                    <td><span title="{$prop->getLongText()}">{$prop->getShortText()}</span></td>
                                                    {foreach $table->getColumnsConfigurable() as $id=>$col}
                                                        <td>
                                                            <input type="checkbox" value="{$id}" {if $col->isProperty($propId)}checked="checked"{/if} />
                                                        </td>
                                                    {/foreach}
                                                </tr>
                                            {/foreach}
                                        </tbody>
                                    </table>

                                    {if $table->isEditable()}
                                        <div class="table-control">
                                            <a href="{AP_APRecEdit::urlTableRows($table)}" target="_blank">редактировать</a>
                                        </div>
                                    {/if}

                                    {* Зависимые группы кеширования *}
                                    {* Для нас наличие триггеров на таблице является признаком того, что она может содержать зависимые группы кешей *}
                                    {if $table->hasTriggers()}
                                        <div title="Триггеры таблицы" class="table-entitys">
                                            <img src="/resources/images/timeline/dull-green-circle.png"/>
                                            {foreach $table->getTriggers() as $trigger}
                                                &nbsp;&nbsp;{$trigger}
                                            {/foreach}
                                            {*<img src="/resources/images/timeline/gray-circle.png"/>*}
                                        </div>
                                    {/if}

                                    {* Зависимые группы кеширования *}
                                    {* Для нас наличие триггеров на таблице является признаком того, что она может содержать зависимые группы кешей *}
                                    {if $table->hasFoldings()}
                                        <div title="Фолдинги таблицы" class="table-entitys">
                                            <img src="/resources/images/timeline/gray-circle.png"/>
                                            {foreach $table->getFoldings() as $folding}
                                                &nbsp;&nbsp;{$folding->getUnique()} ({$folding->getEntityName()})</li>
                                            {/foreach}
                                        </div>
                                    {/if}
                                </div>

                            </div>

                        </div>
                    {/foreach}

                {/if}

            </div>
        {/foreach}
    </div>

    <div class="controls">
        <button>Сохранить</button>
        <button>Перезагрузить</button>
    </div>
{/if}


{if $mode=='view'}
    <h3>{$table->getName()}</h3>
    <table class="colored database">
        <thead>
            <tr>
                {foreach $table->getColumns() as $id=>$col}
                    <th {if $col->isPk()}class="fetched"{/if}>{$id}</th>
                {/foreach}
            </tr>
        </thead>
        <tbody>
            {foreach $rows as $row}
                <tr>
                    {foreach $table->getColumns() as $id=>$col}
                        <td>{$col->safe4show($row[$id])}</td>
                    {/foreach}
                </tr>
            {/foreach}
        </tbody>
    </table>
    {if $table->isEditable()}
        <div class="table-control">
            <a href="{AP_APRecEdit::urlTableRows($table)}" target="_blank">редактировать</a>
        </div>
    {/if}
    <br/><br/>
{/if}