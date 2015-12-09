{*<div class="ans_holder">*}
{*Вынесено сюда, так как данный html возвращается в ответ на ajax-запрос добавления ответа*}
<h3>
    Вы ответили:
</h3>

<div class="answered">
    {$ans->getAnswer()}
    {psctrl delete='Удалить' id=$ans->getId()}
</div>

<div class="text">
    Вы можете изменить свой ответ, для этого...
</div>
{*</div>*}