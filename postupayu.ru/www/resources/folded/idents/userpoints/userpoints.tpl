<h4>История получения баллов</h4>

<p>
    За различные действия, выполняемые на сайте, Вам будут начисляться баллы. Их Вы можете использовать для участия в акциях. 
    Список акций можно посмотреть в разделе "акции" {gray}(ссылка &mdash; в строке навигации){/gray}.
</p>

<p>
    Что это за действия, я Вам, естесственно, не скажу;) Но учтите &mdash; все очки будут выдаваться 
    абсолютно бесплатно! Никаких SMS никуда отправлять не нужно.
</p>

{*
<div class="ps-user-points">
{foreach $points as $point}
{$describer=$point->getDescriber()}
<div class="point-box">
<span class="point-cnt">+{$point->getCnt()}</span>
<h4>{$describer->title()}</h4>
{$describer->content()}
<h6 class="date">{$point->getDtEvent()}</h6>
</div>
{/foreach}
</div>
*}
{$pointsGetted=0}
<table class="colored user_points">{*highlighted*}
    <thead>
        <tr>
            <th>Причина</th>
            <th>Кол-во баллов</th>
            <th>Дата получения</th>
        </tr>
    </thead>
    {if count($points)>0}
        <tbody>
            {foreach $points as $point}
                {$descr=$point->getDescriber()}
                {$pointsGetted=$pointsGetted+$point->getCnt()}
                <tr>
                    <td class="reason">
                        {$descr->title()}
                        {$content=$descr->content()}
                        {if !empty($descr)}
                            <div class="descr">{$content}</div>
                        {/if}
                    </td>
                    <td class="count">{$point->getCnt()}</td>
                    <td class="date">{$point->getDtEvent()}</td>
                </tr>
            {/foreach}
        </tbody>
    {/if}
    <tfoot>
        {if count($points)>0}
            <tr>
                <td>
                    <b>Всего:</b>
                </td>
                <td colspan="2">
                    {$pointsGetted}
                </td>
            </tr>
        {else}
            <tr class="empty">
                <td colspan="3">У Вас пока нет баллов</td>
            </tr>
        {/if}
    </tfoot>
</table>