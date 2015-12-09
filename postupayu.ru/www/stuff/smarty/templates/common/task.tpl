<div class="task_container{if $sub_task} sub{/if}">
    {if !$sub_task && $task_num}
        <h4>{$task_num}</h4>
    {/if}
    <div class="task">
        <div class="task_ctt">
            {$body}
            <div class="clearall"></div>
            {if $from}
                <h6>{$from}</h6>
            {/if}

            {if isset($c_hint) || isset($c_solut) || isset($c_ans) || isset($c_proof)}
                <div class="answer_block">

                    {if isset($c_hint)}
                        <p><a href="#" class="prompt ctrl">Подсказка</a></p>
                        <div class='ctrl'>
                            {$c_hint}
                            <div class="clearall"></div>
                        </div>
                    {/if}

                    {if isset($c_solut)}
                        <p><a href="#" class="solution ctrl">Решение</a></p>
                        <div class="ctrl">
                            {$c_solut}
                            <div class="clearall"></div>
                        </div>
                    {/if}

                    {if isset($c_ans)}
                        <p><a href="#" class="answer ctrl">Ответ</a></p>
                        <div class="ctrl">
                            {$c_ans}
                            <div class="clearall"></div>
                        </div>
                    {/if}

                    {if isset($c_proof)}
                        <p><a href="#" class="answer ctrl">Доказательство</a></p>
                        <div class="ctrl">
                            {$c_proof}
                            <div class="clearall"></div>
                        </div>
                    {/if}

                </div>
            {/if}
        </div>
    </div>
    <div class="clearall"></div>
</div>