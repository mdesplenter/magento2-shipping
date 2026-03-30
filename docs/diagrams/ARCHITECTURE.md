<!--
DOCS_METADATA:
  generated_at: 2026-02-19T08:31:16Z
  git_hash: 4b2b46b
  tool_version: 1.0.0
  source_command: /create-magento-documentation
-->

# Architecture Diagrams

<!-- AUTO-GENERATED:START - Do not edit manually -->

## Module Component Overview

```mermaid
flowchart TB
    subgraph "Magento Frontend"
        CH[Checkout]
        JS[JS Components\nparcelshop.js / dpd.js]
        CH --> JS
    end

    subgraph "Magento Backend"
        CTRL[Controllers\nAdminhtml / Frontend]
        OBS[Observers\n4 event listeners]
        PLUG[Plugins\n2 interceptors]
        PROV[CheckoutConfigProvider]
    end

    subgraph "DpdConnect_Shipping Core"
        DATA[Helper/Data\nLabel orchestrator]
        CLIENT[Helper/DPDClient\nJWT auth factory]
        SETTINGS[Helper/DpdSettings\nConfig accessor]
        CONV[OrderConvertService\nPayload builder]
        LABEL[ShipmentLabelService\nAPI + save]
        SHIP[ShipmentManager\nMagento shipments]
        BATCH[BatchManager\nAsync job tracker]
        AUTH[AuthenticationService\nCredential validation]
    end

    subgraph "External"
        DPD[DPD Connect API]
        GMAPS[Google Maps API]
    end

    subgraph "Database"
        DB1[(dpdconnect_shipping_label)]
        DB2[(dpdconnect_shipping_batch)]
        DB3[(dpdconnect_shipping_batch_job)]
        DB4[(dpdconnect_shipping_tablerate)]
        DB5[(quote / sales_order\nextended columns)]
    end

    JS -->|AJAX| CTRL
    CH -->|checkout config| PROV
    CTRL --> DATA
    OBS --> DATA
    PLUG --> DATA

    DATA --> CONV
    DATA --> LABEL
    DATA --> SHIP
    DATA --> BATCH

    LABEL --> CLIENT
    AUTH --> CLIENT
    CLIENT --> DPD
    LABEL --> DB1
    BATCH --> DB2
    BATCH --> DB3
    SETTINGS --> DB4
    OBS --> DB5

    JS -->|geocode| GMAPS
    CTRL -->|parcelshop list| DPD

    DPD -->|async callback POST /V1/dpd-shipping/callback| CTRL
```

## Request Flow: Sync Label Generation

```mermaid
sequenceDiagram
    participant A as Admin Browser
    participant C as Controller
    participant D as Helper/Data
    participant OC as OrderConvertService
    participant LS as ShipmentLabelService
    participant SDK as DPD PHP SDK
    participant API as DPD Connect API
    participant DB as Database

    A->>C: Click "Create DPD Shipment"
    C->>D: generateShippingLabel($order)
    D->>OC: convert($order, $shipment)
    OC-->>D: shipment payload array
    D->>LS: generateLabel($order, ...)
    LS->>SDK: authenticate()
    SDK->>API: POST /authenticate (JWT)
    API-->>SDK: JWT token
    SDK-->>LS: Client instance
    LS->>API: POST /shipment (create)
    API-->>LS: label PDF (base64) + parcel numbers
    LS->>DB: saveLabel() → dpdconnect_shipping_label
    LS-->>D: label array
    D->>DB: addTrackingNumbersToShipment()
    D-->>C: PDF binary array
    C-->>A: Download PDF
```

## Request Flow: Async Label Generation

```mermaid
sequenceDiagram
    participant A as Admin Browser
    participant C as Controller
    participant D as Helper/Data
    participant LS as ShipmentLabelService
    participant API as DPD Connect API
    participant DB as Database
    participant CB as POST /V1/callback

    A->>C: Mass action (N > threshold orders)
    C->>D: generateShippingLabelAsync($orders)
    D->>LS: generateLabelAsync($orders)
    LS->>API: POST /shipment/async (callbackURI set)
    API-->>LS: jobIds array
    LS-->>D: jobs
    D->>DB: createNewBatch() + createNewJob() per job
    D-->>C: jobs info
    C-->>A: "Batch created" flash message

    Note over API,CB: DPD processes asynchronously

    API->>CB: POST /V1/dpd-shipping/callback (per job)
    CB->>API: getParcel()->getLabel($parcelNumber)
    API-->>CB: PDF binary
    CB->>DB: saveLabel()
    CB->>DB: addTrackingNumber()
    CB->>DB: Update batch_job status = success
    CB->>DB: Update batch status = success
```

<!-- AUTO-GENERATED:END -->

<!-- MANUAL:START - Safe to edit, preserved on updates -->
<!-- MANUAL:END -->
