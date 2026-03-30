<!--
DOCS_METADATA:
  generated_at: 2026-02-19T08:31:16Z
  git_hash: 4b2b46b
  tool_version: 1.0.0
  source_command: /create-magento-documentation
-->

# Configuration Reference

All DPD settings are under **Stores → Configuration → Sales → DPD Parcelservice**.
All carrier-specific settings are under **Stores → Configuration → Sales → Shipping Methods**.

<!-- AUTO-GENERATED:START - Do not edit manually -->

## Account Settings

Admin path: **DPD Parcelservice → Account Settings**

| Setting | Config path | Default | Required | Support changeable? | Notes |
|---|---|---|---|---|---|
| Username | `dpdshipping/account_settings/username` | — | Yes | Yes | DPD Connect API username |
| Password | `dpdshipping/account_settings/password` | — | Yes | Yes | Stored encrypted. Use "Test Connection" to validate |
| Depot | `dpdshipping/account_settings/depot` | — | Yes | Yes | DPD depot number from your DPD contract |
| Print format | `dpdshipping/account_settings/print_format` | `A4` | Yes | Yes | A4 or A6 |

## Shipping Origin

Admin path: **DPD Parcelservice → Shipping Origin**
Used as the `sender` address on every DPD shipment.

| Setting | Config path | Max length | Required |
|---|---|---|---|
| Name | `dpdshipping/shipping_origin/name1` | 35 chars | Yes |
| Street | `dpdshipping/shipping_origin/street` | — | Yes |
| House number | `dpdshipping/shipping_origin/house_number` | 8 chars | Yes |
| Postal code | `dpdshipping/shipping_origin/zip_code` | 9 chars | Yes |
| City | `dpdshipping/shipping_origin/city` | 35 chars | Yes |
| Country | `dpdshipping/shipping_origin/country` | 2 chars (ISO2) | Yes |

> These length limits are DPD API requirements. Saving the configuration validates these values automatically and shows error messages if they exceed the limits.

## Store Information

Admin path: **DPD Parcelservice → Store Information**
Used as the `consignor` in customs declarations and the `sender.email`/`sender.phone`.

| Setting | Config path | Max length | Notes |
|---|---|---|---|
| Name | `dpdshipping/store_information/name` | 35 chars | |
| Street | `dpdshipping/store_information/street` | — | |
| House number | `dpdshipping/store_information/house_number` | 8 chars | |
| Postal code | `dpdshipping/store_information/zip_code` | 9 chars | |
| City | `dpdshipping/store_information/city` | 35 chars | |
| Country | `dpdshipping/store_information/country` | 2 chars | |
| Phone | `dpdshipping/store_information/phone` | 30 chars | |
| Email | `dpdshipping/store_information/email` | 50 chars | |
| VAT number | `dpdshipping/store_information/vat_number` | — | Included in sender data |
| EORI number | `dpdshipping/store_information/eori` | — | Required for non-EU export |
| SPRN | `dpdshipping/store_information/sprn` | — | DPD-specific reference |
| Customs terms | `dpdshipping/store_information/customs_terms` | — | Default: `DAPNP` |

## Advanced Settings

Admin path: **DPD Parcelservice → Advanced**

| Setting | Config path | Default | Support changeable? | Notes |
|---|---|---|---|---|
| Default content type | `dpdshipping/advanced/customs_content_type` | `D` (Documents) | Yes | For non-domestic/non-EU shipments |
| Include return label | `dpdshipping/advanced/include_return_label` | `0` | Yes | Auto-generate return label with every shipping label |
| Email return label | `dpdshipping/advanced/email_return_label` | `0` | Yes | Email return label PDF to customer |
| Return label email template | `dpdshipping/advanced/email_return_label_template` | Default | Yes | Visible only when email_return_label = 1 |
| Picqer mode | `dpdshipping/advanced/picqer_mode` | `0` | Yes | Stores parcelshop name in company field (for Picqer WMS) |
| Save labels as file | `dpdshipping/advanced/save_label_file` | `0` | Yes | Write PDF to disk instead of storing in DB |
| Label directory | `dpdshipping/advanced/label_path` | `var/dpd_labels/` | Yes | Only visible when save_label_file = 1 |
| Print phone number | `dpdshipping/advanced/print_phone_number` | `0` | Yes | Include phone on label |
| Print order ID | `dpdshipping/advanced/print_order_id` | `0` | Yes | Include Magento order ID as customer reference |
| Send shipment confirmation email | `dpdshipping/advanced/send_confirm_email` | `0` | Yes | Triggers Magento shipment notification email |
| Default package type | `dpdshipping/advanced/default_package_type` | `100050050` | Yes | DPD parcel volume identifier |

## API Settings

Admin path: **DPD Parcelservice → API Settings**

| Setting | Config path | Default | Notes |
|---|---|---|---|
| Enable async requests | `dpdshipping/api/async_enabled` | `0` | Enable async label generation for large batches |
| Async threshold | `dpdshipping/api/async_threshold` | `10` | Number of orders above which async mode activates |
| Endpoint override | `dpdshipping/api/endpoint` | — | Leave empty in production. Only for development/testing |

## Product Attribute Mapping

Admin path: **DPD Parcelservice → Product Attributes**
Maps which Magento product attribute code contains each value.

| Setting | Config path | Default attribute code |
|---|---|---|
| HS Code attribute | `dpdshipping/product_attribute/hs_code` | `hs_code` |
| Length attribute | `dpdshipping/product_attribute/product_length` | `ts_dimensions_length` |
| Width attribute | `dpdshipping/product_attribute/product_width` | `ts_dimensions_width` |
| Height attribute | `dpdshipping/product_attribute/product_height` | `ts_dimensions_height` |
| Depth attribute | `dpdshipping/product_attribute/product_depth` | `ts_dimensions_depth` |

## Carrier: DPD (Unified)

Admin path: **Shipping Methods → DPD**

| Setting | Config path | Default |
|---|---|---|
| Enabled | `carriers/dpd/active` | `1` |
| Rate type | `carriers/dpd/rate_type` | `flat` |
| Price | `carriers/dpd/price` | `6.95` |
| Title | `carriers/dpd/title` | `DPD` |
| Sort order | `carriers/dpd/sort_order` | `0` |
| Ship to applicable countries | `carriers/dpd/sallowspecific` | `0` (all) |
| Customer products (JSON) | `carriers/dpd/customer_products` | — |

## Carrier: DPD Pickup (Parcelshop)

Admin path: **Shipping Methods → DPD Pickup**

| Setting | Config path | Default |
|---|---|---|
| Enabled | `carriers/dpdpickup/active` | `0` |
| Use DPD Maps key | `carriers/dpdpickup/use_dpd_maps_key` | `1` |
| Google Maps API key (client) | `carriers/dpdpickup/google_maps_api_client` | — |
| Map width | `carriers/dpdpickup/map_width` | `796` |
| Map height | `carriers/dpdpickup/map_height` | `430` |
| Max parcelshops shown | `carriers/dpdpickup/map_max_shops` | `20` |

## Carrier: DPD Saturday

Admin path: **Shipping Methods → DPD Saturday**

| Setting | Config path | Default |
|---|---|---|
| Enabled | `carriers/dpdsaturday/active` | `0` |
| Shown from day | `carriers/dpdsaturday/shown_from_day` | `1` (Monday) |
| Shown from time | `carriers/dpdsaturday/shown_from_day_time` | `00:00` |
| Shown till day | `carriers/dpdsaturday/shown_till_day` | `1` (Monday) |
| Shown till time | `carriers/dpdsaturday/shown_till_day_time` | `00:00` |

> **Note:** With default values both from and till are Monday 00:00 — the carrier will never show. Configure a proper window, e.g. Monday 00:00 → Thursday 17:00.

<!-- AUTO-GENERATED:END -->

<!-- MANUAL:START - Safe to edit, preserved on updates -->
<!-- MANUAL:END -->
