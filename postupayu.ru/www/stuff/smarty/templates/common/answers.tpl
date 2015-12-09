<p><strong>Варианты ответов:</strong></p>
<ul class="answers">
    {foreach $answers as $val}
        {$content=$val[0]}
        {$valid=$val[1]}
        <li><label><input type="checkbox"{if $valid} class="correct"{/if}/> {$content}</label></li>
            {/foreach}
</ul>