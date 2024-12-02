<div id="lipscore-rating-wrapper">
    <div id="lipscore-rating"
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
        data-ls-readonly="true"
    ></div>
</div>