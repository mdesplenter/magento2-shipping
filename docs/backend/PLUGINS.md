<!--
DOCS_METADATA:
  generated_at: 2026-02-19T08:31:16Z
  git_hash: 4b2b46b
  tool_version: 1.0.0
  source_command: /create-magento-documentation
-->

# Interceptor Plugins

<!-- AUTO-GENERATED:START - Do not edit manually -->

Registered in `etc/di.xml`.

## Overview

| Plugin | Target | Type | Purpose |
|---|---|---|---|
| `OrderInterfacePlugin` | `Magento\Sales\Api\Data\OrderInterface` | `after` | Initialises extension attributes on Order data objects |
| `OrderRepositoryInterfacePlugin` | `Magento\Sales\Api\OrderRepositoryInterface` | `after` | Populates `dpd_parcelshop_id` on orders returned via the REST API |

---

## `OrderInterfacePlugin`
**File:** `Plugin/Api/Data/OrderInterfacePlugin.php`
**DI name:** `initExtensionAttributes`
**Target:** `Magento\Sales\Api\Data\OrderInterface`

Ensures the `ExtensionAttributes` object is initialised on Order objects so that the `dpd_parcelshop_id` extension attribute can be read and set without null pointer errors.

---

## `OrderRepositoryInterfacePlugin`
**File:** `Plugin/Api/OrderRepositoryInterfacePlugin.php`
**DI name:** `DpdConnectShippingOrderRepositoryInterface`
**Target:** `Magento\Sales\Api\OrderRepositoryInterface`

Injects the `dpd_parcelshop_id` value into the Order's extension attributes when an order is loaded via the REST API (`GET /V1/orders/:id`).

**Extension attributes declared in:** `etc/extension_attributes.xml`

```xml
<extension_attributes for="Magento\Sales\Api\Data\OrderInterface">
    <attribute code="dpd_parcelshop_id" type="string"/>
</extension_attributes>
```

This makes `dpd_parcelshop_id` visible in the Magento Order API response, enabling third-party systems to read which parcelshop was selected.

<!-- AUTO-GENERATED:END -->

<!-- MANUAL:START - Safe to edit, preserved on updates -->
<!-- MANUAL:END -->
