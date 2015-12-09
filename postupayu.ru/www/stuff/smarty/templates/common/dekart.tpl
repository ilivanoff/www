<div class="DekartModul">
    <div class="DekartCtrl">
        <label class="max">Масштаб:
            <select>
                {section name='bar' loop='21' start='0'}
                    <option value="{$smarty.section.bar.index}">&nbsp;{$smarty.section.bar.index}&nbsp;</option>
                {/section}
            </select>
        </label>
        <label class="axes">Оси: <input type="checkbox"/></label>
        <label class="marks">Метки: <input type="checkbox"/></label>
        <label class="marksText">Подписи: <input type="checkbox"/></label>
        <label class="grid">Сетка: <input type="checkbox"/></label>

        {*ctrl_button action='save' title='Ход назад' gray='1'*}
        {ctrl_button action='rescale' name='scroll' title='Изменение масштаба скролом мыши' type='trigger'}
        {ctrl_button action='origin' name='pan' title='Перетаскивание начала координат' type='trigger'}
        {ctrl_button action='clear' name='clear3' title='Вернуть в исходное состояние' hoverable='1'}
        {ctrl_button action='legend' name='info16' title='Показать информацию' hoverable='1'}
    </div>
    <div class="Dekart noselect">
        <div class="Grid"></div>
    </div>
</div>