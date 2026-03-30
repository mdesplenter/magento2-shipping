<!--
DOCS_METADATA:
  generated_at: 2026-02-19T08:31:16Z
  git_hash: 4b2b46b
  tool_version: 1.0.0
  source_command: /create-magento-documentation
-->

# Module Class Reference

<!-- AUTO-GENERATED:START - Do not edit manually -->

## Services (`Services/`)

### `AuthenticationService`
**File:** `Services/AuthenticationService.php`

Validates DPD API credentials and checks JWT token validity.

| Method | Description |
|---|---|
| `authenticate(array $options, bool $ignoreCache)` | Tests user/password against DPD API; returns `true` if token valid |
| `isTokenValid(string $token)` | Decodes JWT payload, checks `exp` claim with 5-min buffer |

Called by `Observer/ConfigChanged.php` when admin saves the DPD configuration section.

---

### `BatchManager`
**File:** `Services/BatchManager.php`

Manages the async label batch lifecycle.

| Constant | Value |
|---|---|
| `STATUS_SUCCESS` | `'success'` |
| `STATUS_PARTIAL` | `'partial'` |
| `STATUS_FAILED` | `'failed'` |
| `STATUS_QUEUED` | `'queued'` |

| Method | Description |
|---|---|
| `createNewBatch()` | Creates a `dpdconnect_shipping_batch` record with status `queued` |
| `createNewJob($batch, $jobId)` | Creates a `dpdconnect_shipping_batch_job` record linked to a batch |
| `getJobById($jobId)` | Finds a batch job by DPD job ID |
| `getBatchById($batchId)` | Loads a batch by entity ID |
| `createBatchByOrders(array $orders, bool $includeReturn)` | Delegates to `ShipmentLabelService::generateLabelAsync()` |

---

### `ConfigurationValidator`
**File:** `Services/ConfigurationValidator.php`

Validates admin configuration field values against DPD API field length constraints.

| Method | Max length | Description |
|---|---|---|
| `validateISO2Code(string $code)` | 2 chars | Also verifies country exists in Magento |
| `validateName(string $name)` | 35 chars | |
| `validateZIPCode(string $zipcode)` | 9 chars | |
| `validateHouseNumber(string $hn)` | 8 chars | |
| `validateCity(string $city)` | 35 chars | |
| `validateEmail(string $email)` | 50 chars | |
| `validatePhoneNumber(string $phone)` | 30 chars | |

---

### `GoogleMaps`
**File:** `Services/GoogleMaps.php`

Geocodes addresses to lat/lng using the Google Maps Geocoding API.

| Method | Description |
|---|---|
| `getGoogleMapsCenter($postcode, $countryId)` | Geocode by postcode + country |
| `getGoogleMapsCenterByQuery($query)` | Geocode by free-text address |

Uses `Data::getGoogleServerApiKey()` for authentication.

---

### `ShipmentManager`
**File:** `Services/ShipmentManager.php`

Creates Magento shipments and attaches tracking numbers.

| Method | Description |
|---|---|
| `createShipment($order, $currentRow)` | Returns existing shipment if present; else creates new one. Filters items by `dpd_shipping_type` matching `currentRow['productType']`. |
| `addTrackingNumbersToShipment($shipment, $parcelNumbers)` | Attaches DPD parcel numbers as Magento tracking records with carrier title |
| `getCarrierCode($order)` | Extracts carrier code from `order->getShippingMethod()` (part before first `_`) |

---

## Helpers (`Helper/`)

### `DPDClient`
**File:** `Helper/DPDClient.php`

Factory that returns an authenticated DPD SDK client instance.

| Method | Description |
|---|---|
| `authenticate(?int $storeId)` | Builds `ClientBuilder`, injects cached JWT token, registers token update callback. JWT cached for 7200s per store. |

The client sends `webshopType`, `webshopVersion`, and `pluginVersion` as metadata with every request.

---

### `DpdSettings`
**File:** `Helper/DpdSettings.php`

Central repository of all config path constants and accessors.

Key constant groups:

| Group | Prefix |
|---|---|
| Account | `dpdshipping/account_settings/` |
| Shipping origin | `dpdshipping/shipping_origin/` |
| Store information | `dpdshipping/store_information/` |
| Advanced | `dpdshipping/advanced/` |
| Product attributes | `dpdshipping/product_attribute/` |
| API settings | `dpdshipping/api/` |
| Parcelshop maps | `carriers/dpdpickup/` |

Notable methods: `getDpdCarrierCustomerProductSettings()` — returns decoded JSON of per-product settings.

---

### `Data`
**File:** `Helper/Data.php`

Main label generation orchestrator.

| Method | Description |
|---|---|
| `generateShippingLabel($order, $shipment, $parcels, $isReturn)` | Sync label generation; handles both batch-row mode and single/multi-package mode |
| `generateShippingLabelAsync($orders)` | Async label generation; creates batch + jobs |
| `isDPDOrder($order)` | Returns `true` if shipping method starts with `dpd` |
| `hasDpdFreshProducts($order)` | Returns `true` if any item has `dpd_shipping_type` = `fresh` or `freeze` |
| `combinePDFFiles(array $pdfFiles)` | Merges multiple PDF binary strings into one using Zend_Pdf |

---

### `Helper/Services/OrderService`
**File:** `Helper/Services/OrderService.php`

Detects order type for a given order/shipment context.

| Method | Returns true when |
|---|---|
| `isDPDPickupOrder()` | Shipping method is parcelshop, OR product type is `parcelshop` via DPD API |
| `isDPDPredictOrder()` | Method is `dpdpredict_dpdpredict`, OR product code in `[B2B MSG option, B2C, B2C6]` |
| `isDPDSaturdayOrder()` | Method is `dpdsaturday_dpdsaturday`, OR product code in `[B2C6, 6]` |
| `isDPDClassicSaturdayOrder()` | Method is `dpdclassicsaturday_dpdclassicsaturday`, OR code in `[6]` |
| `isDPDGuarantee18Order()` | Method is `dpdguarantee18_dpdguarantee18`, OR code in `[PM2]` |
| `isDPDExpress12Order()` | Method is `dpdexpress12_dpdexpress12`, OR code in `[AM2]` |
| `isDPDExpress10Order()` | Method is `dpdexpress10_dpdexpress10`, OR code in `[AM1]` |
| `isDPDClassicOrder()` | Method is `dpdclassic_dpdclassic`, OR code in `[B2B]` |
| `isAgeCheckOrder()` | Any visible order item has `age_check = true` |

---

### `Helper/Services/OrderConvertService`
**File:** `Helper/Services/OrderConvertService.php`

Converts a Magento order into the DPD API shipment payload array.

| Method | Description |
|---|---|
| `convert($order, $shipment, $isReturn, $packages)` | Full payload builder — sender, receiver, product, parcels, customs, notifications |
| `getReceiverData($order)` | Returns billing address (parcelshop) or shipping address (other) |
| `getOrderWeight($order)` | Converts to kg if lbs, multiplies by 100, defaults to 600 if 0 |
| `addParcels($order, $shipment, $isReturn, $parcelAmount)` | Builds parcel array with customerReferences, weight, volume |
| `addParcelsFromPackages($order, $shipment, $packages)` | Builds parcel array from Magento packages (multi-parcel) |
| `addCustomsLines($order)` | Builds customs line items from order items (HS code, country of manufacture, value) |

---

### `Helper/Services/ShipmentLabelService`
**File:** `Helper/Services/ShipmentLabelService.php`

Handles actual DPD API calls for label creation and saves results.

| Method | Description |
|---|---|
| `generateLabel($order, $isReturn, $shipment, $parcels, $includeReturn)` | Sync: create + save label for 1–N identical parcels |
| `generateLabelMultiPackage($order, $isReturn, $shipment, $packages, $includeReturn)` | Sync: create + save label with package-level parcel data |
| `generateLabelAsync($orders, $includeReturn)` | Async: create request with `callbackURI`, returns job array |
| `saveLabel($order, $shipment, $identifier, $parcelNumbers, $labelData, $isReturn)` | Persists to `dpdconnect_shipping_label` (DB or file) |
| `getLabel($parcelNumber)` | Fetches label PDF from DPD API by parcel number (used in callback) |

---

## Models

| Model | Table | Purpose |
|---|---|---|
| `Model/Batch.php` | `dpdconnect_shipping_batch` | Async batch group |
| `Model/BatchJob.php` | `dpdconnect_shipping_batch_job` | Individual async job |
| `Model/ShipmentLabel.php` | `dpdconnect_shipping_label` | Stored label PDF |
| `Model/Tablerate.php` | `dpdconnect_shipping_tablerate` | Rate lookup |
| `Model/ApiCallback.php` | — | Processes DPD webhook callback |
| `Model/CheckoutConfigProvider.php` | — | Provides DPD config to checkout JS |

## UI Components

| Component | Grid | Description |
|---|---|---|
| `BatchProvider` | `dpdconnect_shipping_shipment_batch_grid` | Batch list (Sales → DPD → Batches) |
| `BatchJobProvider` | `dpdconnect_shipping_shipment_batchjob_grid` | Job list per batch |
| `ShipmentLabelProvider` | `dpdconnect_shipping_shipment_label_grid` | Label archive (Sales → DPD → Labels) |
| `CreateShipmentAction` | `sales_order_grid` | Adds "DPD - Create shipment(s)" mass action |

<!-- AUTO-GENERATED:END -->

<!-- MANUAL:START - Safe to edit, preserved on updates -->
<!-- MANUAL:END -->
