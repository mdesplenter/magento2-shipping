<!--
DOCS_METADATA:
  generated_at: 2026-02-19T08:31:16Z
  git_hash: 4b2b46b
  tool_version: 1.0.0
  source_command: /create-magento-documentation
-->

# Business Workflows

<!-- AUTO-GENERATED:START - Do not edit manually -->

## 1. Checkout Flow (Customer)

```
Customer selects DPD shipping method
        │
        ▼
[dpd carrier] ──────────── collectRates() evaluates enabled products
        │                   First enabled product pre-selected in session
        │
        ├── [Home delivery] JS component shown; customer can switch product
        │
        └── [dpdpickup]     Google Maps parcelshop picker shown
                │
                ▼
          Customer selects parcelshop
                │
                ▼
          POST /dpd/parcelshops/save
          → saves 7 fields to quote (id, name, street, housenr, zip, city, country)
```

## 2. Order Placement

```
Magento fires event: sales_order_save_before
        │
        ▼
Observer\SalesOrderSaveBefore::execute()
        │
        ├── [FRONTEND] shipping method = dpd_dpd?
        │       └── copy dpd_shipping_product from quote → order
        │
        ├── [FRONTEND] shipping method = dpdpickup_dpdpickup?
        │       └── copy 7 parcelshop fields from quote → order
        │
        └── [ADMINHTML] shipping method starts with "dpd_" but is NOT "dpd_dpd"?
                └── convert to "dpd_dpd", store old method-part as dpd_shipping_product
```

## 3. Synchronous Label Generation

Triggered from:
- **Order view** → "Create DPD Shipment" button
- **Order grid** mass action → "DPD - Create shipment(s)"

```
Admin clicks "Create DPD Shipment"
        │
        ▼
Controller\Adminhtml\Order\CreateShipment (or Shipping\CreateShipment)
        │
        ▼
Helper\Data::generateShippingLabel($order, $shipment, $parcels)
        │
        ├── ShipmentManager::createShipment()     ← creates Magento shipment if none exists
        │
        ├── OrderConvertService::convert()         ← builds DPD API payload
        │       ├── sender from dpdshipping/shipping_origin config
        │       ├── receiver from shipping address (or billing for parcelshop)
        │       ├── productCode from getProductCode()
        │       ├── parcels (weight, volume, customerReferences)
        │       ├── customs lines (HS code, origin country, value per item)
        │       └── notifications for Predict/Saturday/Parcelshop orders
        │
        ├── DPDClient::authenticate()              ← JWT auth (cached 2h)
        │
        ├── $client->getShipment()->create($request)   ← POST to DPD API
        │
        ├── ShipmentLabelService::saveLabel()      ← PDF stored in DB (or disk)
        │
        ├── ShipmentManager::addTrackingNumbersToShipment()
        │
        └── [if send_confirm_email=1] ShipmentNotifier::notify($shipment)
```

## 4. Asynchronous Label Generation

Triggered when batch size exceeds `dpdshipping/api/async_threshold` and `async_enabled = 1`.

```
Admin selects N orders (N > threshold) → mass action
        │
        ▼
Helper\Data::generateShippingLabelAsync($orders)
        │
        ├── ShipmentLabelService::generateLabelAsync()
        │       ├── For each order: OrderConvertService::convert()
        │       └── DPD API: $client->getShipment()->createAsync($request)
        │               └── callbackURI = /rest/default/V1/dpd-shipping/callback
        │
        ├── BatchManager::createNewBatch()         ← status: queued
        └── BatchManager::createNewJob($batch, $jobId) per DPD job
```

**DPD calls back** asynchronously for each job:

```
POST /V1/dpd-shipping/callback  ← called by DPD servers (anonymous, no Magento auth)
        │
        ▼
Model\ApiCallback::sendCallback()
        │
        ├── Parse: orderId, jobId, parcelNumber, shipmentIdentifier, errors
        │
        ├── [errors present] → mark batch + job as FAILED
        │
        └── [success]
                ├── ShipmentLabelService::getLabel($parcelNumber) ← fetch PDF from DPD
                ├── ShipmentLabelService::saveLabel()
                ├── ShipmentManager::addTrackingNumbersToShipment()
                └── mark batch + job as SUCCESS
```

## 5. Return Label Generation

```
Admin clicks "Print Return Label" on order view
        │
        ▼
Controller\Adminhtml\Order\PrintReturnLabel
        │
        ▼
Helper\Data::generateShippingLabel($order, null, 1, $isReturn = true)
        │
        └── OrderConvertService::convert($order, ..., $isReturn = true)
                └── productCode = "RETURN"
```

The return label PDF is stored separately with `is_return = 1` in `dpdconnect_shipping_label`.

Optionally emailed to customer if `dpdshipping/advanced/email_return_label = 1`.

## 6. Multi-Parcel Label Generation

When the admin uses the **Packages** popup on the shipment creation screen:

```
Admin defines packages (qty + weight each) → Save
        │
        ▼
$shipment->getPackages()  ← non-empty array
        │
        ▼
ShipmentLabelService::generateLabelMultiPackage($order, ..., $packages)
        │
        └── OrderConvertService::addParcelsFromPackages()
                └── One parcel entry per package in DPD API payload
```

## 7. DPD Fresh / Freeze Shipment

Products with `dpd_shipping_type = fresh` or `freeze` are **blocked** from standard shipment creation:

```
Admin tries to create shipment via standard Magento UI
        │
        ▼
Observer\SalesOrderShipmentSaveBefore::execute()
        │
        ├── [Not admin area] → skip
        ├── [Not a DPD order] → skip
        ├── [URL contains 'dpd_shipping'] → skip (already using DPD UI)
        │
        └── [hasDpdFreshProducts() = true]
                └── throw Exception:
                    "This order has DPD Fresh/Freeze products, shipments can only be
                     made through the order overview or the packages screen."
```

Fresh/Freeze orders use the packaging popup to supply:
- `expirationDate` — per-parcel goods expiration date
- `description` — goods description

These are passed as `goodsExpirationDate` and `goodsDescription` in the DPD API parcel payload.

<!-- AUTO-GENERATED:END -->

<!-- MANUAL:START - Safe to edit, preserved on updates -->
<!-- MANUAL:END -->
