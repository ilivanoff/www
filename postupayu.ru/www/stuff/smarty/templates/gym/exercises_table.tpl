<table class="exercises">
    <thead>
        <tr>
            <th class="col1">Название</th>
            <th><span>Целевая группа мышц</span></th>
        </tr>
    </thead>
    <tbody>
        {foreach $exes as $ex}
            {$exId = $ex->getId()}
            {$exName = $ex->getName()}
            {$exClass = GymManager::getInstance()->getClass($ex)}
            <tr class="{$exClass}">
                <td>
                    <a href="#{$exId}" class="gym_ex">
                        {imgp dir="GymExercises/$exId" name="cover.jpg"}.{/imgp} {$exName}
                    </a>
                    <a href="#" class="add">{img dir='icons' name='add.png'}</a>
                    <a href="#" class="del">{img dir='icons' name='delete.png'}</a>
                </td>
                <td>
                    {foreach $ex->getGroups() as $gr}
                        <a href="#{$gr->getId()}" class="gym_gr">{$gr->getName()}</a>
                    {/foreach}
                </td>
            </tr>
        {/foreach}
    </tbody>
</table>

