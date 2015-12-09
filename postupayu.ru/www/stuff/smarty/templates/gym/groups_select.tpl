<select class='gym_groups'>
    <option value=''>-- Не выбрано--</option>
{foreach from=$gym_groups item=gr}
    <option class='gym_gr' value='{$gr->getId()}'>{$gr->getName()}</option>
{/foreach}
</select>