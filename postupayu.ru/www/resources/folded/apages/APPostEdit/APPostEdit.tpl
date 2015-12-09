<div id="APPostsTabs" class="ps-tabs">
    {foreach $data as $params}
        <div title="{$params.title}">

            <ol class="posts">
                {foreach $params.templates as $tpl}
                    {$ident=$tpl->getNameNoExt()}
                    {$post=$tpl->getData('post')}
                    <li>
                        {img type=$params.type ident=$ident dim='64x64' class="cover"}

                        <table class="post_settings dbedit" data-type="{$params.type}">
                            <tr>
                                <td>
                                    Id:
                                </td>
                                <td>
                                    {if $post}
                                        {$post->getId()}
                                    {else}
                                        {gray}Не загеристрирован{/gray}
                                    {/if}                            
                                </td>
                            </tr>

                            {if $params.rubrics}
                                <tr>
                                    <td>
                                        Рубрика:
                                    </td>
                                    <td>
                                        <select name="id_rubric">
                                            {foreach $params.rubrics as $id=>$rubric}
                                                <option value="{$id}" {if $post && $post->getRubricId()==$id}selected="selected"{/if}>{$rubric->getName()}</option>
                                            {/foreach}
                                        </select>
                                    </td>
                                </tr>
                            {/if}

                            <tr>
                                <td>
                                    Название:
                                </td>
                                <td>
                                    <input type="text" name="name" value="{if $post}{$post->getName()}{/if}"/>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    Шаблон:
                                </td>
                                <td>
                                    {$ident}
                                </td>
                            </tr>

                            <tr>
                                <td>
                                </td>
                                <td>
                                    {psctrl id=$ident confirm='Сохранить'}
                                </td>
                            </tr>

                        </table>
                        <div class="clearall"></div>
                    </li>
                {/foreach}
            </ol>
        </div>
    {/foreach}
</div>