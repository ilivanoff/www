<div class="pyatnashki_plugin">
    {*
    <div class="centered_block controls">
    {ctrl_button action='dice' title='Смешать'}
    &nbsp;
    <label class="cnt">Ширина поля:
    <select>
    {section name='bar' loop='9' start='2'}
    <option value="{$smarty.section.bar.index}">&nbsp;{$smarty.section.bar.index}&nbsp;</option>
    {/section}
    </select>
    </label>
    &nbsp;
    Время: <span class="time">0</span>
    &nbsp;
    Ходов: <span class="hodes">0</span>
    </div>
    *}
    <div class="centered_block controls">
        Время: <span class="time">0</span>
        &nbsp;
        Ходов: <span class="hodes">0</span>
    </div>

    <div class="ps-pyatnashki">
        <div class="field"></div>
    </div>

    <div class="info centered_block"></div>

    <div class="centered_block controls">
        {ctrl_button action='dice' title='Смешать'}
        &nbsp;
        <label class="cnt">Поле:
            <select>
                {section name='bar' loop='9' start='2'}
                    <option value="{$smarty.section.bar.index}">&nbsp;{$smarty.section.bar.index}x{$smarty.section.bar.index}&nbsp;&nbsp;</option>
                {/section}
            </select>
        </label>
    </div>

</div>