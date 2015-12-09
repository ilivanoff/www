<h3>Расчёт ООФ на заданном интервале</h3>

<div>
    <input type="text" required="required" placeholder="Введите функцию" name="func"/>
</div>

<div>
    <input type="text" placeholder="Значение в"  name="checkX" class="number"/> = <span></span>
</div>

{* 
pattern="^-?\d+$"
(^\s*[0]\s*$)|(^\s*-?[1-9]\d*(\.\d+)?\s*$)
*}
<div>
    <input type="text" required="required" placeholder="От" name="fromX" class="number"/>
    &mdash;
    <input type="text" required="required" placeholder="До" name="toX" class="number"/>
</div>

<div class="buttons">
    <button>Рассчитать</button>
    <button>Очистить</button>
</div>

<div class="use-cache">
    <label><input type="checkbox" name="useCache" checked="checked" /> Использовать кеш</label>
</div>

<div class="progress">
    {progress title='Выполнено' total='100' current='0'}
</div>

<div class="results">
</div>