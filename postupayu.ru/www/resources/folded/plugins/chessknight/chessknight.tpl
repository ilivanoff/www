<div class="chessknight">

    <div class="ctrl">
        <div class="info_msg"></div>
    </div>

    {chessboard}.{/chessboard}

    <div class="centered_block controls">
        {ctrl_button action='back' title='Ход назад' gray='1'}
        {ctrl_button action='roll' title='Начать с начала' gray='1'}
        {ctrl_button action='replay' title='Повторить' gray='1'}
        {ctrl_button action='rewind' title='Перемотать' gray='1'}
    </div>

    {if count($solutions)>0}
        <div class="answers">
            <select>
                {foreach $solutions as $sol}
                    <option value="{$sol->getAnswer()}" {if $sol->isSystem()}class="system"{/if}>Решение {$sol@index+1}</option>
                {/foreach}
            </select>
            <a href="#">{img dir='icons/cn' name='play.png'}</a>
        </div>
    {/if}

</div>