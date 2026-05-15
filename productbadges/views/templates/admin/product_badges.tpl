<script>
    var adminProductBadgesUrl = '{$admin_product_badges_url|escape:'javascript' nofilter}';
</script>
<div class="panel">
    <h3>Badges del producto</h3>



    <div id="assigned-badges">
        {if $badges}
            <div class="badges-list">
                {foreach from=$badges item=badge}
                    <span class="badge"
                          style="background: {$badge.background_color}; color: {$badge.text_color}; padding:5px; margin:3px; display:inline-block; border-radius:4px;">
                        
                        {$badge.name}

                        <button type="button"
                                class="remove-badge"
                                data-id-product="{$id_product}"
                                data-id-badge="{$badge.id_badge}"
                                style="margin-left:5px; background:none; border:none; color:#fff; cursor:pointer;">
                            ✕
                        </button>

                    </span>
                {/foreach}
            </div>
        {else}
            <p id="no-badges-msg">No hay badges asignadas</p>
        {/if}
    </div>

    <hr>


    <div class="add-badges-section">

        <label><strong>Añadir badges al producto</strong></label>

        <select id="badge-selector" multiple style="width:100%; min-height:120px;">
            {foreach from=$all_badges item=badge}
                <option value="{$badge.id_badge}">
                    {$badge.name}
                </option>
            {/foreach}
        </select>

        <br><br>

        <button type="button"
                id="add-badges"
                class="btn btn-primary"
                data-id-product="{$id_product}">
            Añadir seleccionadas
        </button>

    </div>
</div>