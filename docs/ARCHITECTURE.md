<!--
DOCS_METADATA:
  generated_at: 2026-02-19T08:31:16Z
  git_hash: 4b2b46b
  tool_version: 1.0.0
  source_command: /create-magento-documentation
-->

# System Architecture

<!-- AUTO-GENERATED:START - Do not edit manually -->

## Overview

`DpdConnect_Shipping` is a **single Magento 2 module** (not a full platform). It plugs into a host Magento 2 installation and adds DPD shipping capabilities.

```
Host Magento 2 installation
└── app/code/DpdConnect/Shipping/   ← this module
    OR installed via Composer as dpdconnect/magento2-shipping
```

## Module Boundaries

```
┌─────────────────────────────────────────────────────────────┐
│                    DpdConnect_Shipping                       │
│                                                             │
│  ┌─────────────┐   ┌──────────────┐   ┌────────────────┐   │
│  │  Carriers   │   │  Label Gen   │   │  Parcelshop    │   │
│  │ (9 classes) │   │  (sync/async)│   │  (Maps + API)  │   │
│  └──────┬──────┘   └──────┬───────┘   └───────┬────────┘   │
│         │                 │                    │            │
│  ┌──────▼─────────────────▼────────────────────▼─────────┐  │
│  │              DPDClient (JWT auth)                     │  │
│  │              DpdSettings (config)                     │  │
│  └──────────────────────────┬────────────────────────────┘  │
│                             │                               │
└─────────────────────────────│───────────────────────────────┘
                              │
                    ┌─────────▼──────────┐
                    │  dpdconnect/php-sdk │
                    └─────────┬──────────┘
                              │
                    ┌─────────▼──────────┐
                    │  DPD Connect API   │
                    │  (REST / JWT)      │
                    └────────────────────┘
```

## Magento Integration Points

| Integration point | Mechanism | Purpose |
|---|---|---|
| Checkout shipping rates | `Carrier::collectRates()` | Show DPD methods and prices at checkout |
| Order save | `Observer (sales_order_save_before)` | Transfer DPD data from quote to order |
| Shipment save guard | `Observer (sales_order_shipment_save_before)` | Block standard shipment for Fresh/Freeze |
| Config save | `Observer (admin_system_config_changed_section_dpdshipping)` | Validate credentials on config save |
| Order API | `Plugin (OrderRepositoryInterfacePlugin)` | Expose parcelshop_id in REST API |
| Checkout config | `CheckoutConfigProvider` | Pass DPD settings to checkout JS |
| REST endpoint | `webapi.xml` | Receive async label callbacks from DPD |
| Admin grid | `UI Component` | Batch and label grids in Sales menu |

## Data Flow Summary

```
CHECKOUT
Customer → selects DPD method → dpd_shipping_product / parcelshop saved to quote

ORDER PLACEMENT
Quote data → Observer → copied to sales_order

LABEL GENERATION
sales_order → OrderConvertService → DPD API payload
                                  → DPD API (sync or async)
                                  ← PDF label + parcel numbers
                                  → dpdconnect_shipping_label (DB)
                                  → sales_shipment_track (tracking numbers)

ASYNC CALLBACK
DPD API → POST /V1/dpd-shipping/callback → ApiCallback
        → fetch PDF from DPD → saveLabel → updateTracking → update batch job status
```

<!-- AUTO-GENERATED:END -->

<!-- MANUAL:START - Safe to edit, preserved on updates -->
<!-- MANUAL:END -->
