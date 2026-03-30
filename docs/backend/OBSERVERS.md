<!--
DOCS_METADATA:
  generated_at: 2026-02-19T08:31:16Z
  git_hash: 4b2b46b
  tool_version: 1.0.0
  source_command: /create-magento-documentation
-->

# Event Observers

<!-- AUTO-GENERATED:START - Do not edit manually -->

Registered in `etc/events.xml`.

## Overview

| Observer class | Event | Area | Purpose |
|---|---|---|---|
| `SalesOrderSaveBefore` | `sales_order_save_before` | Global | Transfers DPD data from quote to order |
| `SalesOrderAddressSaveBefore` | `sales_order_address_save_before` | Global | Handles address changes for existing DPD orders |
| `SalesOrderShipmentSaveBefore` | `sales_order_shipment_save_before` | Global | Blocks standard shipment for Fresh/Freeze orders |
| `ConfigChanged` | `admin_system_config_changed_section_dpdshipping` | Adminhtml | Validates credentials when DPD config is saved |

---

## `SalesOrderSaveBefore`
**File:** `Observer/SalesOrderSaveBefore.php`
**Event:** `sales_order_save_before`

This is the most critical observer. It runs in two modes:

### Frontend mode
Triggered when a customer places an order.

1. Skip if shipping method is not `dpd_dpd` or `dpdpickup_dpdpickup`.
2. Load the quote by `order->getQuoteId()`.
3. **If `dpdpickup_dpdpickup`:** Copy 7 parcelshop fields from quote → order.
4. **If `dpd_dpd`:** Copy `dpd_shipping_product` from quote → order.

### Admin mode
Triggered when admin creates or edits an order.

1. Skip if shipping method does not start with `dpd_`.
2. Skip if shipping method is already `dpd_dpd`.
3. **Otherwise:** Convert method to `dpd_dpd` and store the old method suffix as `dpd_shipping_product`.

> **Why this matters:** Legacy carriers created before v1.0.8 (e.g. `dpdsaturday_dpdsaturday`) continue to work — they are transparently unified under `dpd_dpd` with the product code stored separately.

---

## `SalesOrderAddressSaveBefore`
**File:** `Observer/SalesOrderAddressSaveBefore.php`
**Event:** `sales_order_address_save_before`

Handles the case where an admin edits a shipping address on an existing DPD parcelshop order. Ensures the parcelshop data remains consistent with any address changes.

---

## `SalesOrderShipmentSaveBefore`
**File:** `Observer/SalesOrderShipmentSaveBefore.php`
**Event:** `sales_order_shipment_save_before`

Guards DPD Fresh/Freeze shipments from being created via the standard Magento UI.

**Logic:**
1. Skip if area is frontend.
2. Skip if not a DPD order (`Helper/Data::isDPDOrder()`).
3. Skip if current URL contains `dpd_shipping` (already in the DPD shipment UI).
4. If `Helper/Data::hasDpdFreshProducts()` is `true` → throw `\Exception`.

**Error message:**
> "This order has DPD Fresh/Freeze products, shipments can only be made through the order overview or the packages screen."

---

## `ConfigChanged`
**File:** `Observer/ConfigChanged.php`
**Event:** `admin_system_config_changed_section_dpdshipping`

Runs every time the admin saves the **Stores → Configuration → DPD** section.

**Steps:**
1. Read `account_settings`, `store_information`, `shipping_origin` from POST params.
2. **Validate credentials:** Call `AuthenticationService::authenticate()`. Save `dpdshipping/account_settings/valid_account` = 1 (success) or 0 (failure). Show admin success/error message.
3. **Validate shipping origin fields:** name, country (ISO2), zip, city, house number — via `ConfigurationValidator`.
4. **Validate store information fields:** same fields plus email and phone.

> Values that use "Use Website" / "Use Default" are resolved from the `inherit` key of the POST data.

<!-- AUTO-GENERATED:END -->

<!-- MANUAL:START - Safe to edit, preserved on updates -->
<!-- MANUAL:END -->
