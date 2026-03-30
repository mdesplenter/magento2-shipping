<!--
DOCS_METADATA:
  generated_at: 2026-02-19T08:31:16Z
  git_hash: 4b2b46b
  tool_version: 1.0.0
  source_command: /create-magento-documentation
-->

# Business Flow Diagrams

<!-- AUTO-GENERATED:START - Do not edit manually -->

## Checkout: Carrier Selection

```mermaid
flowchart TD
    A[Customer reaches shipping step] --> B{Which DPD carrier?}

    B -->|dpd carrier| C[collectRates called]
    C --> D{Customer products\nconfigured?}
    D -->|None enabled| E[Carrier hidden]
    D -->|1+ enabled| F[Rate shown\nfirst product pre-selected]
    F --> G[JS component allows\nproduct switching]
    G --> H[Selected product\nsaved to quote.dpd_shipping_product]

    B -->|dpdpickup carrier| I[Parcelshop map shown]
    I --> J[Customer searches\npostcode + country]
    J --> K[Google Maps geocode\n→ DPD API parcelshop list]
    K --> L[Customer selects shop]
    L --> M[POST /dpd/parcelshops/save\n→ 7 fields saved to quote]

    B -->|dpdsaturday| N{Within\ndisplay window?}
    N -->|No| O[Carrier hidden]
    N -->|Yes| P[Rate shown]
```

## Order Placement: Data Transfer

```mermaid
flowchart TD
    A[Order placed] --> B[Event: sales_order_save_before]
    B --> C{Area code?}

    C -->|Frontend| D{Shipping method?}
    D -->|dpd_dpd| E[Copy dpd_shipping_product\nfrom quote → order]
    D -->|dpdpickup_dpdpickup| F[Copy 7 parcelshop fields\nfrom quote → order]
    D -->|Other| G[Skip]

    C -->|Adminhtml| H{Method starts\nwith dpd_?}
    H -->|No| G
    H -->|Yes, already dpd_dpd| G
    H -->|Yes, legacy code| I[Convert to dpd_dpd\nStore suffix as dpd_shipping_product]
```

## Label Generation Decision

```mermaid
flowchart TD
    A[Admin requests label] --> B{Async enabled\nAND batch > threshold?}

    B -->|Yes| C[ASYNC PATH]
    B -->|No| D[SYNC PATH]

    C --> C1[Build payloads for all orders]
    C1 --> C2[POST /shipment/async to DPD\nwith callbackURI]
    C2 --> C3[Create batch + jobs in DB]
    C3 --> C4[Admin sees batch in\nSales → DPD → Batches]
    C4 --> C5[DPD calls back per job\nPOST /V1/dpd-shipping/callback]
    C5 --> C6[Label saved + tracking attached]

    D --> D1{Has packages\ndefined?}
    D1 -->|Yes - multi-parcel| D2[generateLabelMultiPackage\nOne parcel per package]
    D1 -->|No| D3{Has shipmentRows\nbatch data?}
    D3 -->|Yes| D4[Create shipment per row\ngenerateLabel per row]
    D3 -->|No| D5[generateLabel\nSingle or N parcels]

    D2 --> D6[POST /shipment to DPD\nSync response]
    D4 --> D6
    D5 --> D6
    D6 --> D7[Save label + tracking\nOptional: send confirm email]
```

## Fresh/Freeze Shipment Guard

```mermaid
flowchart TD
    A[Event: sales_order_shipment_save_before] --> B{Area = frontend?}
    B -->|Yes| Z[Skip]
    B -->|No| C{Is DPD order?}
    C -->|No| Z
    C -->|Yes| D{URL contains\ndpd_shipping?}
    D -->|Yes - already in DPD UI| Z
    D -->|No - standard Magento UI| E{hasDpdFreshProducts?}
    E -->|No| Z
    E -->|Yes| F[Throw Exception\nMust use DPD packaging popup]
```

<!-- AUTO-GENERATED:END -->

<!-- MANUAL:START - Safe to edit, preserved on updates -->
<!-- MANUAL:END -->
