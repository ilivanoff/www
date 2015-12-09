<div id="print_setting">
    <div class="ctrl"><input id="p_tasks" type="checkbox" value="" /> <label for="p_tasks">Печатать ответы к задачам</label></div>
    <div class="ctrl"><input id="p_comments" type="checkbox" value="" /> <label for="p_comments">Печатать комментарии</label></div>
    <div class="comments_type ctrl sub">
        <div class="sub2">
            <input id="comments_full" type="radio" name="comments_type" value="full" /><label for="comments_full">полный вид</label>
        </div>
        <div class="sub2">
            <input id="comments_short" type="radio" name="comments_type" value="short" /><label for="comments_short">короткий вид</label>
        </div>
    </div>
</div>

<div id="print_variant">
    {$prinvView}
</div>