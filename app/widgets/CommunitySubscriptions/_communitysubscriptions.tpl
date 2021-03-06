{if="$subscriptions == null"}
    <ul class="thick">
        <div class="placeholder icon bookmark">
            <h1>{$c->__('communitysubscriptions.empty_title')}</h1>
            <h4>{$c->__('communitysubscriptions.empty_text1')} {$c->__('communitysubscriptions.empty_text2')}</h4>
        </li>
    </ul>
{else}
    <ul class="list middle flex active all">
        {loop="$subscriptions"}
            {if="$c->checkNewServer($value)"}
                <li class="subheader block large" onclick="MovimUtils.redirect('{$c->route('community', $value->server)}')">
                    <span class="control icon gray"><i class="zmdi zmdi-chevron-right"></i></span>
                    <p>
                        {$value->server}
                    </p>
                </li>
            {/if}
            <li
                class="block"
                onclick="MovimUtils.redirect('{$c->route('community', [$value->server, $value->node])}')"
                title="{$value->server} - {$value->node}"
            >
                <span class="primary icon bubble color {$value->node|stringToColor}">
                    {$value->node|firstLetterCapitalize}
                </span>
                <span class="control icon gray">
                    <i class="zmdi zmdi-chevron-right"></i>
                </span>
                <p class="line normal">
                    {if="$value->info && $value->info->name"}
                        {$value->info->name}
                    {else}
                        {$value->node}
                    {/if}
                </p>
                {if="$value->info && $value->info->description"}
                    <p class="line">{$value->info->description|strip_tags}</p>
                {/if}
            </li>
        {/loop}
    </ul>
{/if}
