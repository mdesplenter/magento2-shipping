<!--
DOCS_METADATA:
  generated_at: 2026-02-19T08:31:16Z
  git_hash: 4b2b46b
  tool_version: 1.0.0
  source_command: /create-magento-documentation
-->

# Frontend Templates

<!-- AUTO-GENERATED:START - Do not edit manually -->

## Frontend Templates (`view/frontend/templates/`)

| Template | Block class | Purpose |
|---|---|---|
| `checkout/shipping/googlemaps.phtml` | `Block/GoogleMaps.php` | Renders the Google Maps container for parcelshop selection |
| `checkout/shipping/parcelshop-info.phtml` | `Block/ParcelshopInfo.php` | Displays the selected parcelshop address + opening hours summary |
| `checkout/shipping/parcelshop-marker.phtml` | Template (inline) | HTML for Google Maps info window popup on a parcelshop marker |
| `checkout/shipping/parcelshop-opening-hours.phtml` | Template (inline) | Opening hours row, used inside the marker popup |

### `parcelshop-info.phtml`
Renders after a parcelshop is selected. Shows:
- Parcelshop name
- Street + house number
- Postal code + city
- Country name (resolved via `Block/ParcelshopInfo::getCountry($code)`)
- Opening hours table (rendered by `ParcelshopInfo::getOpeningHoursHtml()`)

**Opening hours logic** (in `Block/ParcelshopInfo.php`):
- `00:00–00:00` morning AND afternoon = **Closed**
- `00:00` or `00:01` to `23:59` = **Whole day** (parcel locker)
- Only afternoon or morning closed = shows one time slot
- Both slots present = shows two columns (morning / afternoon)

## Admin Templates (`view/adminhtml/templates/`)

| Template | Purpose |
|---|---|
| `order/checkshipment.phtml` | "Create DPD Shipment" button panel on order view |
| `order/packaging/grid.phtml` | Items grid inside the packaging popup |
| `order/packaging/popup.phtml` | Packaging popup wrapper |
| `order/packaging/popup_content.phtml` | Packaging popup body — parcels, weight, package type, Fresh/Freeze fields |
| `printshippinglist.phtml` | Printable shipping list for selected orders |
| `system/carrier/dpd-customer-product-settings.phtml` | Customer products JSON config UI |
| `system/config/header.phtml` | DPD logo header in Stores → Configuration → DPD |
| `system/config/testconnection.phtml` | "Test Connection" button in account settings |

## Email Templates

| Template ID | File | Purpose |
|---|---|---|
| `dpdshipping_advanced_email_return_label_template` | `view/adminhtml/email/return_labels.html` | Email sent to customer with return label PDF attached |

Registered in `etc/email_templates.xml`.

<!-- AUTO-GENERATED:END -->

<!-- MANUAL:START - Safe to edit, preserved on updates -->
<!-- MANUAL:END -->
