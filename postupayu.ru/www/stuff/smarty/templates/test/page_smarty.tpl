<h1>Блочные функции [{count($blocks)}]</h1>
<ol>
    {foreach $blocks as $bl}
        <li>
            {*<pre>{$bl.comment}</pre>*}
            {ldelim}{$bl.name}{rdelim} ... {ldelim}/{$bl.name}{rdelim}
        </li>
    {/foreach}
</ol>

<h1>Функции [{count($functions)}]</h1>
<ol>
    {foreach $functions as $bl}
        <li>
            {*<pre>{$bl.comment}</pre>*}
            {ldelim}{$bl.name}{rdelim}
        </li>
    {/foreach}
</ol>

<h1>Модификаторы [{count($modifiers)}]</h1>
<ol>
    {foreach $modifiers as $bl}
        <li>
            <pre>{$bl.comment}</pre>
            {ldelim}...|{$bl.name}{rdelim}
        </li>
    {/foreach}
</ol>
