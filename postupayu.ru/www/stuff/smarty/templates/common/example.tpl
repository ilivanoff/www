<div class="ex" {if $id}id="{$id}"{/if}>
    <h4>Пример №{$num}</h4>
    {if isset($c_head)}
        <div class="ex_head">{$c_head}</div>
    {/if}
    <div class="ex_body_container">
        <div class="ex_body">
            {$c_body}

            {if isset($c_ans)}
                <p class="answer">Ответ:</p>
                {$c_ans}
            {/if}
        </div>
    </div>
</div>
