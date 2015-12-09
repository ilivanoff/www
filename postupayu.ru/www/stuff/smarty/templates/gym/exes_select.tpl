<select class="gym_exercises">
    <option value="">-- Не выбрано--</option>
    {foreach $gym_groups as $gr}
        <option class="gym_gr" value="">{$gr->getName()}</option>
        {foreach $gr->getExercises() as $ex}
            <option class="gym_ex" value="{$ex->getId()}">{$ex->getName()}</option>
        {/foreach}
    {/foreach}
</select>