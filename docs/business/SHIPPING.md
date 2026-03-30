<!--
DOCS_METADATA:
  generated_at: 2026-02-19T08:31:16Z
  git_hash: 4b2b46b
  tool_version: 1.0.0
  source_command: /create-magento-documentation
-->

# Shipping Methods, Carriers & Product Codes

<!-- AUTO-GENERATED:START - Do not edit manually -->

## Carriers

The module registers **9 carriers** in Magento. Only `dpd` is enabled by default; all others are disabled by default.

| Carrier Code | Class | Default Status | Description |
|---|---|---|---|
| `dpd` | `Model/Carrier/Dpd.php` | **Active** | Unified carrier — exposes configured customer products |
| `dpdpickup` | `Model/Carrier/Dpdpickup.php` | Disabled | Parcelshop pickup with Google Maps selector |
| `dpdpredict` | `Model/Carrier/Dpdpredict.php` | Disabled | Legacy predict carrier (home delivery + notification) |
| `dpdsaturday` | `Model/Carrier/DpdSaturday.php` | Disabled | Saturday delivery with configurable display window |
| `dpdclassic` | `Model/Carrier/DpdClassic.php` | Disabled | Legacy classic carrier |
| `dpdclassicsaturday` | `Model/Carrier/DpdClassicSaturday.php` | Disabled | Legacy classic Saturday |
| `dpdguarantee18` | `Model/Carrier/DpdGuarantee18.php` | Disabled | Guaranteed delivery before 18:00 |
| `dpdexpress12` | `Model/Carrier/DpdExpress12.php` | Disabled | Express delivery before 12:00 |
| `dpdexpress10` | `Model/Carrier/DpdExpress10.php` | Disabled | Express delivery before 10:00 |

> All carriers extend `Model/Carrier/AbstractCarrier.php` and implement `Magento\Shipping\Model\Carrier\CarrierInterface`.

## DPD API Product Codes

When creating a shipment via the DPD Connect API, a `productCode` must be sent. The module maps Magento carrier/product selections to these codes:

| Product Code | Description | Triggered by |
|---|---|---|
| `RETURN` | Return shipment label | `$isReturn = true` in any label call |
| `CL` | Classic (default fallback) | Legacy `dpdclassic_dpdclassic` or unknown carrier |
| `E10` | Express before 10:00 | Legacy `dpdexpress10_dpdexpress10` |
| `E12` | Express before 12:00 | Legacy `dpdexpress12_dpdexpress12` |
| `E18` | Guarantee before 18:00 | Legacy `dpdguarantee18_dpdguarantee18` |
| `B2C` | B2C home delivery (code `6`) | Customer product code `6` |
| `B2B` | B2B Classic | `dpd_dpd` + customer product `B2B` |
| `B2C6` / `6` | Saturday + Predict | `dpd_dpd` + customer product `B2C6` or `6` |
| `PM2` | Guarantee 18:00 | `dpd_dpd` + customer product `PM2` |
| `AM2` | Express 12:00 | `dpd_dpd` + customer product `AM2` |
| `AM1` | Express 10:00 | `dpd_dpd` + customer product `AM1` |
| `default` | Uses customer product as-is | `dpd_dpd` + any other product code |

Source: `Helper/Services/OrderConvertService.php` — `getProductCode()` method.

## How the Unified DPD Carrier Works

The `dpd` carrier reads its available products from the admin setting **Stores → Configuration → Sales → Shipping Methods → DPD → Customer Products** (`carriers/dpd/customer_products`, stored as JSON).

### Frontend behaviour
1. Module evaluates which products are enabled and country-allowed for the current cart.
2. The **first enabled product** is pre-selected if no DPD product is in the session.
3. Only **one rate** is shown to the customer (the pre-selected product's price).
4. The JS component at checkout allows the customer to switch products.

### Admin behaviour
1. In the admin order creation/edit screen, **each enabled product** is shown as a separate shipping method option.
2. Method label uses the product's configured `title`.

## Rate Types

Each carrier (and each customer product within the `dpd` carrier) supports two rate modes:

### Flat rate
- Price is taken directly from the product's `price` setting.
- Free shipping rules from Magento are respected.

### Table rate
- Rates are stored in `dpdconnect_shipping_tablerate`.
- Rate lookup key: `shipping_method + website_id + dest_country_id + dest_region_id + dest_zip + condition_name + condition_value`.
- Default condition: `package_weight`.
- Supports `package_weight`, `package_value`, `package_qty` conditions.
- Import/export via **Stores → Configuration → Sales → Shipping Methods → [carrier] → Import/Export CSV**.

## Saturday Carrier — Time Window Logic

The `dpdsaturday` carrier has a unique visibility rule: it **only shows at checkout within a configured day/time window**.

| Config field | Path | Description |
|---|---|---|
| Shown from day | `carriers/dpdsaturday/shown_from_day` | Day of week (1=Mon … 7=Sun) |
| Shown from time | `carriers/dpdsaturday/shown_from_day_time` | e.g. `10:00` |
| Shown till day | `carriers/dpdsaturday/shown_till_day` | Day of week |
| Shown till time | `carriers/dpdsaturday/shown_till_day_time` | e.g. `15:00` |

Source: `Model/Carrier/DpdSaturday.php` — `collectRates()`.

## Parcelshop Carrier

When `dpdpickup` is selected:
1. A Google Maps widget is shown at checkout.
2. The customer searches by postcode and country (geocoded via Google Maps API).
3. DPD API returns up to `map_max_shops` nearby parcelshops.
4. On selection, parcelshop data is saved to the **quote** (7 fields).
5. On order placement, parcelshop data is copied from quote to **order** by `Observer/SalesOrderSaveBefore.php`.
6. When creating the label, **billing address** (not shipping address) is used as the recipient.
7. The parcelshop ID is added to the DPD API payload as `product.parcelshopId`.

<!-- AUTO-GENERATED:END -->

<!-- MANUAL:START - Safe to edit, preserved on updates -->
<!-- MANUAL:END -->
