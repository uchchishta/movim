<section>
    {$url = $contact->getPhoto('s')}
    <header class="big"
        {if="$url"}
            style="background-image: linear-gradient(to bottom, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 100%), url('{$contact->getPhoto('xxl')}');"
        {else}
            style="background-color: rgba(62,81,181,1);"
        {/if}
        >
        <ul class="list middle">
            <li>
                {if="$url"}
                    <span class="primary icon bubble color
                        {if="$roster && $roster->presence"}status {$roster->presence->presencekey}{/if}
                    ">
                        <img src="{$url}">
                    </span>
                {else}
                    <span class="primary icon bubble color {$contact->id|stringToColor}
                        {if="$roster && $roster->presence"}status {$roster->presence->presencekey}{/if}
                    ">
                        <i class="zmdi zmdi-account"></i>
                    </span>
                {/if}
                {if="!$contact->isMe()"}
                    <span class="control icon active" onclick="ContactActions_ajaxChat('{$contact->id}')">
                        <i class="zmdi zmdi-comment-text-alt"></i>
                    </span>
                {/if}
                <span class="control icon active" onclick="MovimUtils.reload('{$c->route('contact', $contact->id)}')">
                    <i class="zmdi zmdi-account"></i>
                </span>
                <p>{$contact->truename}</p>
                <p>{$contact->id}</p>
            </li>
        </ul>
    </header>

    <ul class="list middle">
        {if="$caps"}
            <li class="block">
                <span class="primary icon gray">
                    <i class="zmdi
                        {if="$caps->type == 'handheld' || $caps->type == 'phone'"}
                            zmdi-smartphone-android
                        {elseif="$caps->type == 'bot'"}
                            zmdi-memory
                        {elseif="$caps->type == 'web'"}
                            zmdi-globe-alt
                        {else}
                            zmdi-laptop
                        {/if}
                    ">
                    </i>
                </span>
                <p class="normal line">
                    {$caps->name}
                </p>
                <p class="line">
                    {if="isset($clienttype[$caps->type])"}
                        {$clienttype[$caps->type]}
                    {/if}
                </p>
            </li>
        {/if}

        {if="$contact->fn != null"}
        <li>
            <span class="primary icon gray">{$contact->fn|firstLetterCapitalize}</span>
            <p>{$c->__('general.name')}</p>
            <p>{$contact->fn}</p>
        </li>
        {/if}

        {if="$contact->nickname != null"}
        <li>
            <span class="primary icon gray">{$contact->nickname|firstLetterCapitalize}</span>
            <p>{$c->__('general.nickname')}</p>
            <p>{$contact->nickname}</p>
        </li>
        {/if}

        {if="$contact->url != null"}
        <li>
            <span class="primary icon gray"><i class="zmdi zmdi-link"></i></span>
            <p>{$c->__('general.website')}</p>
            <p>
                {if="filter_var($contact->url, FILTER_VALIDATE_URL)"}
                    <a href="{$contact->url}" target="_blank">{$contact->url}</a>
                {else}
                    {$contact->url}
                {/if}
            </p>
        </li>
        {/if}

        {if="$contact->email != null"}
        <li>
            <span class="primary icon gray"><i class="zmdi zmdi-email"></i></span>
            <p>{$c->__('general.email')}</p>
            <p><a href="mailto:{$contact->email}">{$contact->email}</a></p>
        </li>
        {/if}

        {if="$contact->description != null && trim($contact->description) != ''"}
        <li>
            <span class="primary icon gray"><i class="zmdi zmdi-format-align-justify"></i></span>
            <p>{$c->__('general.about')}</p>
            <p class="all">{$contact->description|nl2br}</p>
        </li>
        {/if}

        {if="strtotime($contact->date) != 0"}
        <li class="block">
            <span class="primary icon gray"><i class="zmdi zmdi-cake"></i></span>
            <p>{$c->__('general.date_of_birth')}</p>
            <p>{$contact->date|strtotime|prepareDate:false}</p>
        </li>
        {/if}
    </ul>

    <br />
</section>
