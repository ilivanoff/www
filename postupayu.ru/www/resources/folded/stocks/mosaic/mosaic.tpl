{if $showcase_mode}
    <p>
        Вашему вниманию будет предложена картинка, которую пользователи открывают совместно, используя 
        свои баллы. После открытия картинки Вам нужно будет ответить на вопрос...
    </p>
    <p>
        Среди тех, кто откроет наибольшее количество ячеек и даст правильный ответ, будет разыгран приз.
    </p>

    {h5}Баллы:{/h5}
    <p>
        В данной акции один балл приравнивается одной ячейке, которую Вы можете открыть на картинке.
    </p>

    {hidden text='Текущее состояние' toggle=1}
    {h5}Текущее состояние:{/h5}
    <a href="#" pageIdent="{$stock->popup()}"><img class="smalled" src="{$info->getRelPath()}" alt="" /></a>

    {progress current=$info->getOwnedCellsCnt() total=$info->getTotalCellsCnt() title='Открыто'}
    {/hidden}

{else}

    <div class="ps-mosaic-img" style="width: {$info->getWidth()}px">
        <div class="quest">
            {$stock->getMosaicTask()}
        </div>

        {* КАРТИНКА *}
        <div class="holder" style="width: {$info->getWidth()}px; height: {$info->getHeight()}px">
            {*authed}
            {foreach $info.ownedc as $cells}
            {foreach $cells as $cell}
            <div {$cell.style} class="bind"></div>
            {/foreach}
            {/foreach}
            {/authed*}
            <img src="{$info->getRelPath()}" class="mosaic" alt="" usemap="#mosaicmap"/>
            <map id="mosaicmap">
                {$info->getHtmlAreas()}
            </map>
        </div>

        {progress current=$info->getOwnedCellsCnt() total=$info->getTotalCellsCnt() title='Открыто'}

        {authed}
        {$openedCnt=$info->getUserOwnedCellsCnt()}
        {$canOpenCnt=$info->getUserCanOpenCellsCnt()}
        <div class="ctrl text">
            <div>Вами открыто ячеек: {$openedCnt}{if $openedCnt>0} {gray}(<a href="#showBinds">показать</a>){/gray}{/if}</div>
            {if $active && $info->hasNotOwnedCells()}
                <div>Можете открыть ячеек: {$canOpenCnt}{if $canOpenCnt>0} {gray}(<a href="#showCells">открыть</a>){/gray}{/if}</div>
            {/if}
        </div>
        {/authed}


        {if $active}
            {authed}
            {* == ФОРМА ВВОДА ОТВЕТА == *}

            <div class="ans_holder">
                {$info->getUserAnswerHtml()}
            </div>

            <div class="text">
                Максимально короткий и чёткий ответ впишите в поле снизу. Среди тех, кто откроет наибольшее количество ячеек и правильно ответит на 
                поставленный вопрос, будет разыгран специальный приз. Что именно это будет за приз, я сообщу позднее.
                Удачи!;)
            </div>

            {form form_id='MosaicAnswerForm'}

            {if $info->hasStatistic()}
                {$showFoot=false}
                <h3>Статистика:</h3>
                <table class="colored stat">
                    <thead>
                        <tr>
                            <th>
                                Кол-во ячеек открыто
                            </th>
                            <th>
                                Кол-во пользователей
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $info->getStatistic() as $cnt=>$users}
                            <tr{if $openedCnt eq $cnt} class="user" title="Ваша группа"{/if}>
                                <td>
                                    {$cnt}
                                </td>
                                <td>
                                    {$users}
                                </td>
                            </tr>
                            {if $users@index==5}
                                {$showFoot=true}
                                {break}
                            {/if}
                        {/foreach}
                    </tbody>
                    {if $showFoot}
                        <tfoot>
                            <tr class="centered">
                                <td colspan="2">
                                    ...
                                </td>
                            </tr>
                        </tfoot>
                    {/if}
                </table>
            {/if}
            {/authed}

            {notauthed}
            {* == ПРАВИЛА АКЦИИ == *}

            <br/>
            <h2>Правила:</h2>

            <p>
                В разгадывании данной задачки принимают участие только {page_href code=$smarty.const.PAGE_REGISTRATION blank='1'}зарегистрированные{/page_href}
                пользователи. Правила очень просты &mdash; за различные действия, выполняемые на проекте, Вы получаете баллы, которые можете 
                использовать для открытия ячеек данной картинки. 1 балл = 1 ячейка, всё просто:)
            </p>

            <p>
                Сразу предупреждаю &mdash; никаких SMS`ок никуда отправлять не нужно! Баллы будут Вам доставаться именно за действия. А вот какие это 
                действия, я Вам не скажу (по, надеюсь, понятным причинам:) Картинка разгадывается совместно, открытые Вами ячейки видны другим пользователям и наоборот.
            </p>

            <p>
                После того, как Вы решите, что знаете ответ на вопрос &mdash; вписывайте его в форму {gray}(появится после регистрации){/gray}.
                По окончании конкурса, среди тех, кто откроет наибольшее количество ячеек и даст правильный ответ, будет разыгран специальный приз.
            </p>

            <p>
                Итак &mdash; {page_href code=$smarty.const.PAGE_REGISTRATION blank='1'}регистрируйтесь{/page_href}, принимайте активное участие в жизни сайта,
                получайте баллы, открывайте ячейки, разгадывайте загадку и получайте приз. Удачи!:)
            </p>
            {/notauthed}

        {else}
            {if $winner}
                {* == АКЦИЯ ЗАВЕРШИЛАСЬ, ОТВЕТ ПОБЕДИТЕЛЯ == *}

                <h2>Результаты акции:</h2>

                <p>
                    Итак, как я и обещал, среди тех, кто набрал наибольшее количество баллов и 
                    дал правильный ответ, случайным образом был выбран победитель.
                    Им стал пользователь <b>«{$winner->getUserName()}»</b>, уникальный номер:
                    <b>{$winner->getUserId()}</b>.
                    {*todo - показывать при наведении*}
                    {*hidden block=1 text='показать карточку'}
                    {UserGuiManager::getInstance()->getIdCard($winner->getUserId())}
                    {/hidden*}
                </p>

                <p>
                    Искренне поздравляем нашего победителя и желаем новых достижений, успехов и 
                    покорённых вершин!
                    В подарок от меня он получает вот этот замечательный <i>приз</i>.
                </p>

                <p>
                    Огромное спасибо всем, кто принял участие в этой акции! Не расстраивайтесь, если
                    победить не удалось. Попытайте удачу в новых акциях, которые обязательно будут 
                    проводиться на нашем сайте.
                </p>

                <p>
                    Ну а теперь пришла пора дать ответ на нашу задачку. Вот ответ, который был признан победным:
                </p>

                <div class="ans_winner">
                    {$winner->getAnswer()}
                </div>

                {$stock->getMosaicAnswer()}
            {else}
                {* == АКЦИЯ ЗАВЕРШИЛАСЬ, ИДЁТ ОПРЕДЕЛЕНИЕ ПОБЕДИТЕЛЯ == *}
                {notice}Акция завершилась, идёт определение победителя...{/notice}
            {/if}
        {/if}
    </div>

{/if}