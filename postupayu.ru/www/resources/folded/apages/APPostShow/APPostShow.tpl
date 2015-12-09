{hidden text='Показать настройки времени' toggle='1'}
<h3>
    Время базы: {UtilsBean::inst()->getDbNow()}
</h3>
<h3>
    Время зоны: {DatesTools::inst()->uts2dateInCurTZ($uts_db)} ({PsTimeZone::inst()->getCurrentDateTimeZone()->getName()})
</h3>
<h4>
    Uts php: {$uts_php}
</h4>
<h4>
    Uts db:&nbsp;&nbsp;&nbsp;{$uts_db}
</h4>
{/hidden}

<div id="APPostsTabs" class="ps-tabs">
    {foreach $data as $postsTitle=>$postsArr}
        <div title="{$postsTitle}">
            {foreach $postsArr as $type=>$posts}

                {if count($posts)==0}
                    {continue}
                {/if}

                {if $type=='hidden'}
                    <h2 class="gray">Скрытые посты ({count($posts)}):</h2>
                {/if}

                {if $type=='ready'}
                    <h2 class="green">Готовые к показу ({count($posts)}):</h2>
                {/if}

                {if $type=='shown'}
                    <h2>Показанные посты ({count($posts)}):</h2>
                {/if}

                <ol class="posts">
                    {foreach $posts as $post}
                        <li>
                            {img post=$post dim='64x64' class="cover"}
                            [{$post->getId()}] <b>{$post->getName()}</b> <small>{gray}[{$post->getIdent()}]{/gray}</small>

                            <table class="post_settings visible" data-type="{$post->getPostType()}">
                                <tr>
                                    <td>
                                        Показан:
                                    </td>
                                    <td>
                                        <input type="checkbox" name="b_shown" {if $post->isShow()}checked="checked"{/if}/>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Дата публикации:
                                    </td>
                                    <td>
                                        <input type="text" name="dt_pub" class="ps-datetime-picker" value="{$post->getDtPublication()}" />
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                    </td>
                                    <td>
                                        {psctrl id=$post->getId() confirm='Сохранить'}
                                    </td>
                                </tr>

                            </table>

                        </li>
                    {/foreach}
                </ol>

            {/foreach}
        </div>
    {/foreach}
</div>