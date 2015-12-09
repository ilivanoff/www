<h4 class="first">Как работать с формулами</h4>
<p>
    Для работы с формулами в проекте используется макро-язык <strong>LaTeX</strong>.
    {*
    </p>

    <p>
    *}
    Чтобы вставить формулу в текст сообщения, необходимо оформить её набором
    специальных команд. Например, формула для нормального распределения в LaTeX будет выглядеть так:
</p>

<div class="latex">
    {literal}
        \frac{1}{\sigma\sqrt{2\pi}}\exp\left(-\frac{(x-\mu)^2}{2\sigma^2}\right)
    {/literal}
</div>

<p>А отображаться будет так:
    $$\frac{1}{\sigma\sqrt{2\pi}}\exp\left(-\frac{(x-\mu)^2}{2\sigma^2}\right)$$
</p>

<p>
    Посмотреть исходный код формулы можно, кликнув по формуле правой кнопкой мыши и выбрав
    {ppimgp name='MathJaxMenu.gif'}Show Source{/ppimgp}.
    Далее можно просто скопировать исходник формулы и использовать его как шаблон для своей формулы.
</p>

<p>
    Чтобы формула начала обрабатываться, её необходимо обрамить символами
    {nobr}<strong>&#092;(</strong> ... <strong>&#092;)</strong>{/nobr}
    или
    {nobr}<strong>&#092;[</strong> ... <strong>&#092;]</strong>{/nobr}.
    Формула, обрамлённая по первому стилю, будет отображаться как внутристрочная, по второму - как
    блочная. Рассмотрим пример:
</p>

<p>
    Когда \{a \ne 0\}, уравнение \{ax^2 + bx + c = 0\} имеет два решения:
</p>
$$x = {-b \pm \sqrt{b^2-4ac} \over 2a}$$

<p>
    На языке LaTeX это выражение имеет вид:
</p>

{literal}
    <div class="latex">
        Когда <b>&#092;(</b> a &#092;ne 0 <b>&#092;)</b>, уравнение
        <b>&#092;(</b> ax^2 + bx + c = 0 <b>&#092;)</b> имеет два решения:
        <b>&#092;[</b> x = {-b \pm \sqrt{b^2-4ac} \over 2a} <b>&#092;]</b>
    </div>
{/literal}
<p>
    Скопируйте этот пример и вставьте в поле ниже. Вводимый текст будет обработан
    автоматически. Подробную документацию по макро-языку LaTeX можно посмотреть
    {doc_href doc='LaTeX.pdf' text='здесь' title='Документация по LaTeX'}.
    Также примеры можно посмотреть
    <a href="http://ru.wikipedia.org/wiki/Википедия:Примеры_оформления_формул" target="_blank">на википедии</a>
    или воспользоваться <a href="http://www.sciweavers.org/free-online-latex-equation-editor" target="_blank">онлайн-конструктором</a>.
</p>

<form action="" id="formulaTest">
    <fieldset>
        <legend>Введите формулу</legend>
        <div class="textarea_tools">
            <button title="Блочная формула">[...]</button>
            <button title="Внутристрочная формула">(...)</button>
        </div>
        <textarea name="formula" cols="20" rows="110">{$formula}</textarea>
        <p>
            <!--<input class="button" type="submit" value="Проверить"/>-->
            <input class="button" type="reset" value="Очистить"/>
        </p>
    </fieldset>
</form>
