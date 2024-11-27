<div class="card card-block" id="lipscore-ratings-and-form">

    <h2>{l s='Product reviews' mod='lipscore_prestashop'}</h2>

    <div id="lipscore-review-list"
         data-ls-product-name="{$name}"
         data-ls-brand="{$brand}"
         data-ls-product-id="{$id}"
         data-ls-gtin="{$gtin}"
         data-ls-product-url="{$url}"
         data-ls-availability="{$stock}"
         data-ls-category="{$category}"
         data-ls-mpn="{$mpn}"
         data-ls-sku="{$sku}"
         data-ls-image-url="{$imageUrl}"

            {if $showPrice}
                data-ls-price-currency="{$currency}"
                data-ls-price="{$price}"
            {/if}

         data-ls-description="{$description}"
         data-ls-variant-id="{$variantId}"
         data-ls-variant-name="{$variantName}"
         data-ls-page-size="10">
    </div>
</div>