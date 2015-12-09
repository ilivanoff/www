<table class="chessboard {if $small}smallchessboard chessfigs_small{else}chessfigs{/if}">
    <tbody>
        {section name=rows start=10 loop=10 max=10 step=-1}
            <tr>
                {section name=cols start=0 loop=10 max=10 step=1}
                    {chess_fugure x=$smarty.section.cols.index y=$smarty.section.rows.index}
                {/section}        
            </tr>        
        {/section}
    </tbody>
</table>
