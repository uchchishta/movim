<ul class="list middle divided spaced">
{loop="$post->links"}
    <li>
        <span class="primary icon gray">
            {if="$value->logo"}
                <img src="{$value->logo}"/>
            {else}
                <i class="zmdi zmdi-link"></i>
            {/if}
        </span>
        <p class="normal line">
            <a target="_blank" href="{$value->href}" title="{$value->href}">
                {if="isset($value->title)"}
                    {$value->title}
                {else}
                    {$value->href}
                {/if}
            </a>
        </p>
        {if="$value->description"}
            <p title="{$value->description}">{$value->description}</p>
        {else}
            <p>{$value->url.host}</p>
        {/if}
    </li>
{/loop}

{loop="$post->files"}
    <li>
        <span class="primary icon gray">
            <span class="zmdi zmdi-attachment-alt"></span>
        </span>
        <p class="normal line">
            <a
                href="{$value->href}"
                class="enclosure"
                {if="isset($value->type)"}
                    type="{$value->type}"
                {/if}
                target="_blank">
                {$value->href|urldecode}
            </a>
        </p>
    </li>
{/loop}
</ul>
