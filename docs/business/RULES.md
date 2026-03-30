<!--
DOCS_METADATA:
  generated_at: 2026-02-19T08:31:16Z
  git_hash: 4b2b46b
  tool_version: 1.0.0
  source_command: /create-magento-documentation
-->

# Business Rules Reference

All rules below are verified against source code. Every rule has a direct code reference.

<!-- AUTO-GENERATED:START - Do not edit manually -->

## Carrier & Order Rules

| ID | Rule | Condition | Action | File | Verified |
|---|---|---|---|---|---|
| CR-001 | Legacy admin carrier conversion | Admin saves order with `dpd_*` method (not `dpd_dpd`) | Method converted to `dpd_dpd`; old method segment stored as `dpd_shipping_product` | `Observer/SalesOrderSaveBefore.php:62-76` | ✓ |
| CR-002 | Parcelshop data transfer | Frontend order placed with `dpdpickup_dpdpickup` | 7 parcelshop fields copied from quote → order | `Observer/SalesOrderSaveBefore.php:97-110` | ✓ |
| CR-003 | Product code transfer | Frontend order placed with `dpd_dpd` | `dpd_shipping_product` copied from quote → order | `Observer/SalesOrderSaveBefore.php:112-114` | ✓ |
| CR-004 | Saturday window restriction | `dpdsaturday` carrier rate collection | Carrier returns `false` (hidden) outside configured day+time window | `Model/Carrier/DpdSaturday.php:97-119` | ✓ |
| CR-005 | No duplicate shipment in batch | Mass-action processes order that already has a shipment | Returns existing first shipment instead of creating a new one | `Services/ShipmentManager.php:62-64` | ✓ |

## Label Generation Rules

| ID | Rule | Condition | Action | File | Verified |
|---|---|---|---|---|---|
| LR-001 | Fresh/Freeze blocks standard shipment | Admin creates shipment via standard Magento UI for order with Fresh/Freeze products | Exception thrown; must use DPD packaging popup | `Observer/SalesOrderShipmentSaveBefore.php:71-74` | ✓ |
| LR-002 | Default weight fallback | Order total weight = 0 | Weight sent to DPD API defaults to `600` (= 6 kg in 100g units) | `Helper/Services/OrderConvertService.php:157` | ✓ |
| LR-003 | Weight unit conversion | Magento weight unit is `lbs` | Weight multiplied by `0.45359237` before conversion to DPD units | `Helper/Services/OrderConvertService.php:149` | ✓ |
| LR-004 | Parcelshop uses billing address | Order shipping method is `dpdpickup_dpdpickup` OR old `dpdpickup` | Billing address used as recipient, not shipping address | `Helper/Services/OrderConvertService.php:118-120` | ✓ |
| LR-005 | Predict/Saturday notification | Order is Predict (`B2B MSG option`, `B2C`, `B2C6`) or Saturday (`B2C6`, `6`) | Email notification added to DPD shipment payload | `Helper/Services/OrderConvertService.php:317-323` | ✓ |
| LR-006 | Parcelshop notification | Order is parcelshop pickup | Parcelshop notification + parcelshopId added to DPD payload | `Helper/Services/OrderConvertService.php:325-333` | ✓ |
| LR-007 | Age check applies if any item requires it | Any visible order item has `age_check = true` | `ageCheck: true` sent in DPD product payload | `Helper/Services/OrderService.php:88-96` | ✓ |
| LR-008 | Product code `B2C6`/`6` is both Saturday AND Predict | Shipping product code is `B2C6` or `6` | Both `saturdayDelivery: true` AND `homeDelivery: true` set, email notification added | `Helper/Services/OrderService.php:108,147` | ✓ |

## Authentication & API Rules

| ID | Rule | Condition | Action | File | Verified |
|---|---|---|---|---|---|
| AR-001 | JWT token caching | After successful auth | Token cached in Magento cache for 7200 seconds (2 hours) per store | `Helper/DPDClient.php:110-112` | ✓ |
| AR-002 | JWT expiry buffer | Token validation | Token considered expired 5 minutes before actual `exp` claim | `Services/AuthenticationService.php:106` | ✓ |
| AR-003 | Credential validation on config save | Admin saves DPD configuration section | Credentials tested against DPD API; success/fail message shown; `valid_account` flag saved | `Observer/ConfigChanged.php:69-81` | ✓ |

## Validation Rules (Config)

Applied when admin saves the DPD configuration section. Errors shown as admin notices.

| ID | Field | Rule | Max Length | File | Verified |
|---|---|---|---|---|---|
| VR-001 | Country | Must be valid ISO 2-letter code | 2 | `Services/ConfigurationValidator.php:23-29` | ✓ |
| VR-002 | Name | Must not exceed max length | 35 chars | `Services/ConfigurationValidator.php:33-35` | ✓ |
| VR-003 | Zip code | Must not exceed max length | 9 chars | `Services/ConfigurationValidator.php:43-45` | ✓ |
| VR-004 | House number | Must not exceed max length | 8 chars | `Services/ConfigurationValidator.php:52-54` | ✓ |
| VR-005 | City | Must not exceed max length | 35 chars | `Services/ConfigurationValidator.php:62-64` | ✓ |
| VR-006 | Email | Must not exceed max length | 50 chars | `Services/ConfigurationValidator.php:72-74` | ✓ |
| VR-007 | Phone number | Must not exceed max length | 30 chars | `Services/ConfigurationValidator.php:81-83` | ✓ |

These limits come from DPD API field constraints, not Magento.

<!-- AUTO-GENERATED:END -->

<!-- MANUAL:START - Safe to edit, preserved on updates -->
<!-- MANUAL:END -->
