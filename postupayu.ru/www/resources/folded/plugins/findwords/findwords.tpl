<table class='findwords'>
    <tbody>
        {foreach from=$findwords item=word}
        <tr class='word'>
            <td class='col1'></td>
            <td class='col2'><input type='text' ans='{$word}'/></td>
            <td class='col3'><span class='ans'>?</span>{img dir='icons' name='wanswer.png'}</td>
        </tr>
        {/foreach}
        <tr class='ctrl'>
            <td></td>
            <td></td>
            <td><a href='#'>Смешать</a></td>
        </tr>
    </tbody>
</table>