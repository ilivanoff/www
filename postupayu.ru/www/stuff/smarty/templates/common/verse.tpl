{if $b_double}
    <table class="verse double">
        <tbody>
            {if $c_name}
                <tr>
                    <td colspan="2" class="name">
                        {$c_name}
                    </td>
                </tr>
            {/if}

            {foreach $verses as $ver}
                <tr>
                    <td class="content">
                        <div class="ctt">
                            {$ver[0]}
                        </div>
                    </td>
                    <td class="content">
                        {if $ver[1]}
                            <div class="ctt">
                                {$ver[1]}
                            </div>
                        {/if}
                    </td>
                </tr>
            {/foreach}

            {if $c_year}
                <tr>
                    <td colspan="2" class="year">
                        {$c_year}
                    </td>
                </tr>
            {/if}
        </tbody>
    </table>
{else}
    <table class="verse">
        <tbody>
            {if $c_name}
                <tr>
                    <td class="name">
                        {$c_name}
                    </td>
                </tr>
            {/if}
            <tr>
                <td class="content">
                    <div class="ctt">
                        {$c_body}
                    </div>
                </td>
            </tr>
            {if $c_year}
                <tr>
                    <td class="year">
                        {$c_year}
                    </td>
                </tr>
            {/if}
        </tbody>
    </table>
{/if}