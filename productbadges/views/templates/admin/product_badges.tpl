<div class="panel">
    <h3>Badges del producto</h3>
    {if $badges}
        <div class="badges-list">
            {foreach from=$badges item=badge}
                <span class="badge"
                      style="background: {$badge.color_bg}; color: {$badge.color_text};">
                    {$badge.name}
                </span>
            {/foreach}
        </div>
    {else}
        <p>No hay badges asignadas</p>
    {/if}
</div>