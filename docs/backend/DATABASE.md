<!--
DOCS_METADATA:
  generated_at: 2026-02-19T08:31:16Z
  git_hash: 4b2b46b
  tool_version: 1.0.0
  source_command: /create-magento-documentation
-->

# Database Schema

<!-- AUTO-GENERATED:START - Do not edit manually -->

This module creates **3 new tables** and modifies **2 existing Magento tables**. All schema changes are applied via `Setup/UpgradeSchema.php` and `Setup/UpgradeData.php`.

## New Tables

### `dpdconnect_shipping_label`
Stores generated DPD shipping and return labels.
Added in setup version `1.0.3`.

| Column | Type | Nullable | Description |
|---|---|---|---|
| `entity_id` | INT UNSIGNED PK | No | Auto-increment |
| `created_at` | TIMESTAMP | No | Record creation time |
| `updated_at` | TIMESTAMP | No | Last update time |
| `order_id` | INT UNSIGNED | No | Magento order entity_id |
| `order_increment_id` | VARCHAR(32) | No | Magento order increment_id |
| `shipment_id` | INT UNSIGNED | No | Magento shipment entity_id |
| `shipment_increment_id` | VARCHAR(32) | No | Magento shipment increment_id |
| `carrier_code` | VARCHAR(32) | No | Full shipping method string (e.g. `dpd_dpd`) |
| `mps_id` | VARCHAR(255) | No | DPD MPS shipment identifier |
| `label_numbers` | TEXT | No | Serialized array of parcel numbers + weights |
| `label` | BLOB (32MB) | No | PDF binary data (empty if stored as file) |
| `label_path` | TEXT | Yes | File path if `save_label_file = 1` |
| `is_return` | INT(1) | No | `1` = return label, `0` = shipping label |

---

### `dpdconnect_shipping_batch`
Groups async label generation jobs.
Added in setup version `1.0.4`.

| Column | Type | Nullable | Description |
|---|---|---|---|
| `entity_id` | INT UNSIGNED PK | No | Auto-increment |
| `created_at` | TIMESTAMP | No | Record creation time |
| `updated_at` | TIMESTAMP | No | Last update time |
| `status` | VARCHAR(50) | Yes | `queued` / `success` / `partial` / `failed` |

---

### `dpdconnect_shipping_batch_job`
Individual jobs within an async batch. Each job = one DPD shipment request.
Added in setup version `1.0.4`.

| Column | Type | Nullable | Description |
|---|---|---|---|
| `entity_id` | INT UNSIGNED PK | No | Auto-increment |
| `batch_id` | INT UNSIGNED | No | FK → `dpdconnect_shipping_batch.entity_id` (CASCADE) |
| `created_at` | TIMESTAMP | No | Record creation time |
| `updated_at` | TIMESTAMP | No | Last update time |
| `order_id` | INT UNSIGNED | Yes | FK → `sales_order.entity_id` (CASCADE) |
| `order_increment_id` | VARCHAR(32) | No | Order increment ID |
| `shipment_id` | INT UNSIGNED | Yes | FK → `sales_shipment.entity_id` (CASCADE) |
| `shipment_increment_id` | VARCHAR(32) | No | Shipment increment ID |
| `job_id` | VARCHAR(100) | Yes | DPD job identifier (from async response) |
| `error_message` | TEXT | Yes | Error message from DPD callback |
| `type` | VARCHAR(50) | Yes | Default: `regular` |
| `status` | VARCHAR(50) | Yes | Default: `queued` → `success` or `failed` |

**Foreign keys:**
- `batch_id` → `dpdconnect_shipping_batch.entity_id` ON DELETE CASCADE
- `shipment_id` → `sales_shipment.entity_id` ON DELETE CASCADE
- `order_id` → `sales_order.entity_id` ON DELETE CASCADE

---

### `dpdconnect_shipping_tablerate`
Custom shipping rate table, one row per rate entry per carrier method.
Added in setup version `1.0.2`.

| Column | Type | Nullable | Description |
|---|---|---|---|
| `pk` | INT UNSIGNED PK | No | Auto-increment |
| `shipping_method` | VARCHAR(150) | No | e.g. `dpd_dpd` or `dpdsaturday` |
| `website_id` | INT | No | Magento website ID |
| `dest_country_id` | VARCHAR(4) | No | ISO 2/3 country code, `'0'` = all |
| `dest_region_id` | INT | No | Magento region ID, `0` = all |
| `dest_zip` | VARCHAR(10) | No | Postal code, `'*'` = wildcard |
| `condition_name` | VARCHAR(30) | No | `package_weight`, `package_value`, `package_qty` |
| `condition_value` | DECIMAL(12,4) | No | Upper bound for this condition |
| `price` | DECIMAL(12,4) | No | Shipping price |
| `cost` | DECIMAL(12,4) | No | Shipping cost |

**Unique index:** `(shipping_method, website_id, dest_country_id, dest_region_id, dest_zip, condition_name, condition_value)`

---

## Modified Tables

### `quote`
Columns added in setup version `1.0.1` and `1.0.8`.

| Column | Type | Description |
|---|---|---|
| `dpd_parcelshop_id` | TEXT NULL | Selected parcelshop ID |
| `dpd_parcelshop_name` | TEXT NULL | Parcelshop company name |
| `dpd_parcelshop_street` | TEXT NULL | Parcelshop street |
| `dpd_parcelshop_house_number` | TEXT NULL | Parcelshop house number |
| `dpd_parcelshop_zip_code` | TEXT NULL | Parcelshop postal code |
| `dpd_parcelshop_city` | TEXT NULL | Parcelshop city |
| `dpd_parcelshop_country` | TEXT NULL | Parcelshop ISO2 country code |
| `dpd_shipping_product` | TEXT NULL | Selected DPD product code (for unified `dpd` carrier) |

---

### `sales_order`
Columns added via `SalesSetup` in setup version `1.0.1` and `1.0.8` (EAV-style, flat-order compatible).

| Attribute | Type | Description |
|---|---|---|
| `dpd_parcelshop_id` | varchar | Selected parcelshop ID |
| `dpd_parcelshop_name` | varchar | Parcelshop company name |
| `dpd_parcelshop_street` | varchar | Parcelshop street |
| `dpd_parcelshop_house_number` | varchar | Parcelshop house number |
| `dpd_parcelshop_zip_code` | varchar | Parcelshop postal code |
| `dpd_parcelshop_city` | varchar | Parcelshop city |
| `dpd_parcelshop_country` | varchar | Parcelshop ISO2 country code |
| `dpd_shipping_product` | varchar | Selected DPD product code |

---

## Custom Product EAV Attributes

Added via `Setup/UpgradeData.php`. All attributes belong to attribute group **"DPD Product Attributes"**.

| Attribute code | Type | Input | Description | Added in |
|---|---|---|---|---|
| `hs_code` | varchar | text | Harmonized System Code for customs | v1.0.0 |
| `export_description` | varchar | text | Export description for customs declarations | v1.0.0 |
| `age_check` | int | boolean | Require age verification at delivery | v1.0.6 |
| `dpd_shipping_type` | varchar | select | `default` / `fresh` / `freeze` | v1.0.7 |
| `dpd_fresh_description` | varchar | text | Goods description for Fresh/Freeze shipments | v1.0.7 |

<!-- AUTO-GENERATED:END -->

<!-- MANUAL:START - Safe to edit, preserved on updates -->
<!-- MANUAL:END -->
