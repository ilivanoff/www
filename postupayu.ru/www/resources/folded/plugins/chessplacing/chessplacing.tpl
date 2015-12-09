<div class="chessplacing">

    <!--    <div class='mode'><a href='#' class='independence'>независимость</a>&nbsp;&nbsp;&nbsp;<a href='#' class='dominance'>доминирование</a></div>-->
    <div class="mode">
        <a href="#"></a>
    </div>
    <div class="info"></div>
    <div class="figures_left chessfigs_small"></div>

    {chessboard}.{/chessboard}

    <!--    <div class='mode'><a href='#' class='dominance'>доминирование</a></div>-->

    <table class="figs">
        <tbody>
            <tr class="chessfigs">
                <td data-f="R" class="R"><span class="wr"></span></td>
                <td data-f="K" class="K"><span class="wk"></span></td>
                <td data-f="Q" class="Q"><span class="wq"></span></td>
                <td data-f="B" class="B"><span class="wb"></span></td>
                <td data-f="N" class="N"><span class="wn"></span></td>
            </tr>
        </tbody>
    </table>

    <div class="centered_block controls">
        {ctrl_button action='back' title='Ход назад' gray='1'}
        {ctrl_button action='roll' title='Начать с начала' gray='1'}
        {ctrl_button action='clear' title='Отменить все ходы' gray='1'}
    </div>
</div>