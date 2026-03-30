<!--
DOCS_METADATA:
  generated_at: 2026-02-19T08:31:16Z
  git_hash: 4b2b46b
  tool_version: 1.0.0
  source_command: /create-magento-documentation
-->

# Frontend JS Components

<!-- AUTO-GENERATED:START - Do not edit manually -->

All JS files live under `view/frontend/web/js/`.

## Component Map

| File | Type | Purpose |
|---|---|---|
| `view/checkout/shipping/dpd.js` | KnockoutJS component | Main DPD shipping method selector at checkout |
| `view/checkout/shipping/parcelshop.js` | KnockoutJS component | DPD Parcelshop method — triggers map and stores selection |
| `view/onestepcheckout/shipping/parcelshop.js` | KnockoutJS component | Parcelshop for Onestepcheckout compatibility |
| `view/ShippingInfo.js` | KnockoutJS component | Displays selected parcelshop info summary |
| `view/shipping-rates-validation.js` | Mixin | Shipping rate validation rules integration |
| `model/set-shipping-information-mixin.js` | Mixin | Extends Magento's set-shipping-information to include DPD parcelshop data |
| `model/shipping-rates-validation-rules.js` | Model | Validation rules for DPD shipping methods |
| `model/shipping-rates-validator.js` | Model | Rate validator |
| `view/aheadworks-onestepcheckout-shipping-method-mixin.js` | Mixin | Compatibility with Aheadworks One Step Checkout |
| `view/mageplaza-osc-shipping-mixin.js` | Mixin | Compatibility with Mageplaza One Step Checkout |

## `requirejs-config.js` Mixins

```js
// view/frontend/requirejs-config.js
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-shipping-information': {
                'DpdConnect_Shipping/js/model/set-shipping-information-mixin': true
            },
            // ... OSC compatibility mixins
        }
    }
};
```

The `set-shipping-information-mixin` ensures parcelshop data (id, name, address fields) is included in the checkout shipping information payload sent to Magento.

## Checkout Config Provider

**File:** `Model/CheckoutConfigProvider.php`

Exposes DPD configuration to the checkout JavaScript under the key `dpdConfig`:

```js
// Available via window.checkoutConfig.dpdConfig
{
    useDpdMapsKey:     true/false,
    googleMapsApiKey:  "AIzaSy...",
    mapWidth:          796,
    mapHeight:         430,
    maxShops:          20,
    saveUrl:           "/dpd/parcelshops/save",
    searchUrl:         "/dpd/parcelshops/index",
    carrierSaveUrl:    "/dpd/carrier/save",
    customerProducts:  { /* product settings object */ }
}
```

## Parcelshop Search Flow

```
1. Customer selects "DPD Pickup" at checkout
2. parcelshop.js component activates
3. Customer types postcode / address → triggers AJAX to /dpd/parcelshops/index
4. Controller geocodes via Google Maps → fetches parcelshops from DPD API
5. Map rendered with parcelshop markers (Google Maps JS API, client key)
6. Customer clicks a marker → selects parcelshop
7. AJAX POST to /dpd/parcelshops/save → saves 7 fields to quote
8. ShippingInfo.js component renders selected parcelshop address summary
```

## Templates Referenced by JS

| JS Component | Template |
|---|---|
| `ShippingInfo.js` | `view/frontend/web/template/ShippingInfo.html` |
| Checkout shipping methods | `view/frontend/web/template/checkout/shipping/parcelshops.html` |
| Onestepcheckout | `view/frontend/web/template/checkout/onestepcheckout/shipping-method.html` |

<!-- AUTO-GENERATED:END -->

<!-- MANUAL:START - Safe to edit, preserved on updates -->
<!-- MANUAL:END -->
