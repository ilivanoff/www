{if $href}
    <a class="imaged clickable{if $hoverable} hoverable{/if}{if $class} {$class}{/if}" href="{$href}" {if $blank}target="_blank"{/if} {if $action}data-action="{$action}"{/if} title="{$title}">{img dir='icons/controls' name="`$name`.png"}{if $gray||$hoverable}{img dir='icons/controls' name="`$name`_gray.png" class='gray'}{/if}</a>
{else}
    <button class="imaged{if $hoverable} hoverable{/if}{if $class} {$class}{/if}" data-action="{$action}" type="button" title="{$title}" {if $popup}pageIdent="{$action}"{/if}>{img dir='icons/controls' name="`$name`.png"}{if $gray||$hoverable}{img dir='icons/controls' name="`$name`_gray.png" class='gray'}{/if}</button>
{/if}