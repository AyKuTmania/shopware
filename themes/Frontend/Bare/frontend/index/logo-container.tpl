<div class="logo-main block-group" role="banner">

    {* Main shop logo *}
    {block name='frontend_index_logo'}
        <div class="logo--shop block">
            {s name="IndexLinkDefault" namespace="frontend/index/index" assign="snippetIndexLinkDefault"}{/s}
            <a class="logo--link" href="{url controller='index'}" title="{"{config name=shopName}"|escapeHtml} - {$snippetIndexLinkDefault|escape}">

                {* Check that every is not the same *}
                {if [$theme.desktopLogo,$theme.tabletLandscapeLogo,$theme.tabletLogo,$theme.mobileLogo]|array_filter|array_unique|count === 1}
                    <img src="{link file=$theme.mobileLogo}" alt="{"{config name=shopName}"|escapeHtml} - {$snippetIndexLinkDefault|escape}" />
                {else}
                    <picture>
                        {if $theme.desktopLogo && $theme.desktopLogo !== $theme.tabletLandscapeLogo}
                            <source srcset="{link file=$theme.desktopLogo}" media="(min-width: 78.75em)">
                        {/if}
                        {if $theme.tabletLandscapeLogo && $theme.tabletLandscapeLogo !== $theme.tabletLogo}
                            <source srcset="{link file=$theme.tabletLandscapeLogo}" media="(min-width: 64em)">
                        {/if}
                        {if $theme.tabletLogo && $theme.tabletLogo !== $theme.mobileLogo}
                            <source srcset="{link file=$theme.tabletLogo}" media="(min-width: 48em)">
                        {/if}

                        <img src="{link file=$theme.mobileLogo}" alt="{"{config name=shopName}"|escapeHtml} - {$snippetIndexLinkDefault|escape}" />
                    </picture>
                {/if}
            </a>
        </div>
    {/block}

    {* Support Info *}
    {block name='frontend_index_logo_supportinfo'}
        {if $theme.checkoutHeader && {controllerName|lower} === 'checkout' && {controllerAction|lower} !== 'cart'}
            <div class="logo--supportinfo block">
                {s name='RegisterSupportInfo' namespace='frontend/register/index'}{/s}
            </div>
        {/if}
    {/block}

    {* Trusted Shops *}
    {block name='frontend_index_logo_trusted_shops'}{/block}
</div>
