{if $product_badges && $product_badges|count > 0}
    {foreach from=$product_badges item=badge}
        <div class="product-badges-container position-{$badge.position|escape:'htmlall':'UTF-8'}">
            <span class="product-badge-label"
                  style="background-color:{$badge.background_color|escape:'htmlall':'UTF-8'}; color:{$badge.text_color|escape:'htmlall':'UTF-8'};">
                {$badge.name|escape:'htmlall':'UTF-8'}
            </span>
        </div>
    {/foreach}
{/if}
