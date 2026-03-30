<!--
DOCS_METADATA:
  generated_at: 2026-02-19T08:31:16Z
  git_hash: 4b2b46b
  tool_version: 1.0.0
  source_command: /create-magento-documentation
-->

# External Integrations

<!-- AUTO-GENERATED:START - Do not edit manually -->

## 1. DPD Connect API

The module communicates with the **DPD Connect REST API** via the `dpdconnect/php-sdk` package.

### Authentication

| Property | Detail |
|---|---|
| Method | JWT (Bearer token) |
| Credentials | Username + password (stored encrypted in Magento config) |
| Token TTL | 7200 seconds (2 hours) cached in Magento cache |
| Expiry buffer | Token considered expired 5 minutes early to avoid races |
| Config path | `dpdshipping/account_settings/username` / `password` |
| Sensitive | Yes — marked as sensitive in `etc/di.xml` (not exported in config dumps) |

Authentication is handled by `Helper/DPDClient.php`. The JWT token is cached under the key `dpdconnect_jwt_token_{storeId}`. A callback is registered with the SDK so the token is refreshed automatically and the new value re-cached.

### API Operations Used

| Operation | SDK call | When |
|---|---|---|
| Create shipment (sync) | `$client->getShipment()->create($request)` | Single label or small batch |
| Create shipment (async) | `$client->getShipment()->createAsync($request)` | Large batch (> async_threshold) |
| Get parcel label | `$client->getParcel()->getLabel($parcelNumber)` | After async callback |
| List parcelshops | `$client->getParcelshop()->getList($coordinates)` | Checkout parcelshop search |
| List products | `$client->getProduct()->getList()` | Carrier type detection (pickup check) |
| Get JWT token | `$client->getAuthentication()` | Auth flow |

### Shipment Request Payload

```json
{
  "printOptions": {
    "printerLanguage": "PDF",
    "paperFormat": "A4",
    "verticalOffset": 0,
    "horizontalOffset": 0
  },
  "createLabel": true,
  "shipments": [
    {
      "orderId": "000000123",
      "sendingDepot": "522",
      "sender": {
        "name1": "My Store",
        "street": "Mainstreet",
        "housenumber": "1",
        "country": "NL",
        "postalcode": "1234AB",
        "city": "Amsterdam",
        "phoneNumber": "+31612345678",
        "email": "store@example.com",
        "commercialAddress": true,
        "vatnumber": "NL123456789B01",
        "eorinumber": "NL123456789",
        "sprn": ""
      },
      "receiver": {
        "name1": "John Doe",
        "street": "Customer Street 1",
        "postalcode": "5678CD",
        "city": "Rotterdam",
        "country": "NL",
        "phoneNumber": "0612345678",
        "email": "customer@example.com",
        "commercialAddress": false
      },
      "product": {
        "productCode": "B2C",
        "saturdayDelivery": false,
        "homeDelivery": false,
        "ageCheck": false
      },
      "parcels": [
        {
          "customerReferences": ["000000123", "", "", "12"],
          "weight": 1500,
          "volume": "100050050",
          "returns": false
        }
      ],
      "customs": {
        "terms": "DAPNP",
        "totalCurrency": "EUR",
        "totalAmount": 49.95,
        "customsLines": [
          {
            "description": "T-shirt",
            "harmonizedSystemCode": "6109100010",
            "originCountry": "NL",
            "quantity": 2,
            "netWeight": 300,
            "grossWeight": 300,
            "totalAmount": 49.95
          }
        ],
        "consignor": { "...": "same as sender" },
        "consignee": { "...": "same as receiver" }
      },
      "notifications": [
        {
          "subject": "predict",
          "channel": "EMAIL",
          "value": "customer@example.com"
        }
      ]
    }
  ]
}
```

> **Note on weight:** DPD API uses units of 100g. So 1 kg = `100`, 1.5 kg = `150`. Magento weight is multiplied by 100 after optional lbs→kg conversion. If weight = 0, the module defaults to `600` (6 kg).

### Async Request Additions

```json
{
  "callbackURI": "https://your-store.com/rest/default/V1/dpd-shipping/callback",
  "label": {
    "printOptions": { "..." : "..." },
    "createLabel": true,
    "shipments": [ "..." ]
  }
}
```

### Callback Payload (received FROM DPD)

```json
{
  "jobid": "abc-123-def",
  "shipment": {
    "orderId": "000000123",
    "product": {
      "productCode": "B2C"
    },
    "trackingInfo": {
      "parcelNumbers": ["01234567890"],
      "shipmentIdentifier": "MPS-987654"
    },
    "parcels": [
      {
        "customerReferences": ["000000123", "", "", "12"]
      }
    ]
  },
  "error": []
}
```

### API Endpoint Configuration

By default the endpoint is determined by the `dpdconnect/php-sdk`. An override can be set at **Stores → Configuration → Sales → DPD → API Settings → Endpoint**. Leave empty in production.

---

## 2. Google Maps Geocoding API

Used exclusively for the **DPD Parcelshop** carrier to find parcelshops near a customer's address.

### Configuration

| Setting | Admin Path | Config Path |
|---|---|---|
| Use DPD key | Shipping Methods → DPD Pickup → Use DPD Maps Key | `carriers/dpdpickup/use_dpd_maps_key` |
| Client API key | Shipping Methods → DPD Pickup → Google Maps API Key (client) | `carriers/dpdpickup/google_maps_api_client` |
| Server API key | Shipping Methods → DPD Pickup → Google Maps API Key (server) | (via `Data::getGoogleServerApiKey()`) |

The client key is used in the **frontend JavaScript** (map rendering). The server key is used in **backend geocoding** calls.

### Geocoding Flow

```
Customer enters postcode + country OR free-text address at checkout
        │
        ▼
AJAX POST /dpd/parcelshops/index
        │
        ▼
Controller\Parcelshops\Index::execute()
        │
        ├── Call Google Maps Geocode API:
        │   https://maps.googleapis.com/maps/api/geocode/json
        │   ?key={serverKey}&components=country:NL|postal_code:1234AB&sensor=false
        │
        ├── Extract lat/lng from response
        │
        └── Call DPD API: $client->getParcelshop()->getList($coordinates)
                └── Returns array of parcelshops with opening hours, address, lat/lng
```

### What Is Stored

On parcelshop selection:

| Quote field | Order field | Description |
|---|---|---|
| `dpd_parcelshop_id` | `dpd_parcelshop_id` | Parcelshop identifier (sent in DPD API as `parcelshopId`) |
| `dpd_parcelshop_name` | `dpd_parcelshop_name` | Company/shop name |
| `dpd_parcelshop_street` | `dpd_parcelshop_street` | Street |
| `dpd_parcelshop_house_number` | `dpd_parcelshop_house_number` | House number |
| `dpd_parcelshop_zip_code` | `dpd_parcelshop_zip_code` | Postal code |
| `dpd_parcelshop_city` | `dpd_parcelshop_city` | City |
| `dpd_parcelshop_country` | `dpd_parcelshop_country` | ISO 2 country code |

<!-- AUTO-GENERATED:END -->

<!-- MANUAL:START - Safe to edit, preserved on updates -->
<!-- MANUAL:END -->
