<div class="covers exercises_covers">
    {foreach $exes as $ex}
        {$exId = $ex->getId()}
        {$exName = $ex->getName()}
        {$exClass = GymManager::getInstance()->getClass($ex)}
        {$exSrc = GymManager::getInstance()->relCoverPathSmall($exId)}
        <a href="#{$exId}" title="{$exName}" class="{$exClass}">
            <span class="name">{$exName}</span>
            <img src="{$exSrc}" alt="{$exName}"/>
        </a>
    {/foreach}
    <div class="clearall"></div>
</div>