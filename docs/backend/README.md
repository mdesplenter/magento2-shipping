<!--
DOCS_METADATA:
  generated_at: 2026-02-19T08:31:16Z
  git_hash: 4b2b46b
  tool_version: 1.0.0
  source_command: /create-magento-documentation
-->

# Backend Technical Overview

<!-- AUTO-GENERATED:START - Do not edit manually -->

## Module Identity

| Property | Value |
|---|---|
| Module name | `DpdConnect_Shipping` |
| Namespace | `DpdConnect\Shipping` |
| Setup version | `1.0.8` |
| Composer package | `dpdconnect/magento2-shipping` |
| PHP requirement | `>= 7.2` |
| Framework requirement | `magento/framework >= 103.0.6` |
| External SDK | `dpdconnect/php-sdk ^1.1` |

## Dependencies (Module Sequence)

```xml
<sequence>
    <module name="Magento_Backend"/>
    <module name="Magento_Quote"/>
    <module name="Magento_Checkout"/>
    <module name="Magento_Shipping"/>
</sequence>
```

## Directory Map

```
DpdConnect_Shipping/
├── Api/                    ApiCallbackInterface.php
├── Block/
│   ├── Adminhtml/
│   │   ├── Order/          CheckShipment, Packaging, Packaging/Grid, PrintShippingList
│   │   ├── System/Carrier/ DpdCustomerProductSettings
│   │   ├── System/Config/  Header, TestConnection
│   │   └── Tablerate/      Grid, TablerateExport, TablerateImport
│   ├── GoogleMaps.php
│   └── ParcelshopInfo.php
├── Config/Source/Settings/ ContentType, CustomsTerms, DaysInWeek, PrintFormat,
│                           ProductAttribute, RateType
├── Controller/
│   ├── Adminhtml/          Batch, BatchJob, Label, Order, Search, Shipping, Tablerate
│   ├── Carrier/Save.php
│   └── Parcelshops/        Index.php, Save.php
├── etc/                    di.xml, events.xml, webapi.xml, config.xml, module.xml,
│                           acl.xml, extension_attributes.xml, email_templates.xml,
│                           csp_whitelist.xml, frontend/di.xml, frontend/routes.xml,
│                           adminhtml/menu.xml, adminhtml/routes.xml, adminhtml/system.xml
├── Helper/
│   ├── Constants.php
│   ├── Data.php            Main label generation helper
│   ├── DPDClient.php       JWT-authenticated API client factory
│   ├── DpdCache.php        Cache wrapper
│   ├── DpdSettings.php     All config path constants + accessors
│   └── Services/
│       ├── OrderConvertService.php
│       ├── OrderService.php
│       └── ShipmentLabelService.php
├── Model/
│   ├── ApiCallback.php
│   ├── Batch.php / BatchJob.php / ShipmentLabel.php / Tablerate.php
│   ├── Carrier/            AbstractCarrier + 9 carrier classes
│   ├── CheckoutConfigProvider.php
│   ├── Config/Backend/     CustomerProduct.php
│   ├── Config/             Tablerate.php
│   ├── Mail/Template/      TransportBuilder.php
│   ├── ResourceModel/      Batch, BatchJob, ShipmentLabel, Tablerate (+ Collections)
│   └── Attribute/          Backend/ShippingDescription, Source/DefaultPackageTypeOptions,
│                           Source/ShippingType
├── Observer/               ConfigChanged, SalesOrderAddressSaveBefore,
│                           SalesOrderSaveBefore, SalesOrderShipmentSaveBefore
├── Plugin/Api/             OrderInterfacePlugin, OrderRepositoryInterfacePlugin
├── Services/               AuthenticationService, BatchManager, ConfigurationValidator,
│                           GoogleMaps, ShipmentManager
├── Setup/                  UpgradeData.php, UpgradeSchema.php
├── Ui/Component/           BatchJobProvider, BatchProvider, ShipmentLabelProvider,
│                           Listing/Column/ShipmentLabelActions,
│                           Shipping/CreateShipmentAction
└── ViewModel/              CheckShipment.php
```

## Documents in This Section

| Document | Description |
|---|---|
| [MODULES.md](MODULES.md) | Detailed class reference for all services, helpers, models |
| [OBSERVERS.md](OBSERVERS.md) | All 4 event observers |
| [PLUGINS.md](PLUGINS.md) | All 2 interceptor plugins |
| [API.md](API.md) | REST API endpoint (webhook callback) |
| [DATABASE.md](DATABASE.md) | Custom tables and schema modifications |

<!-- AUTO-GENERATED:END -->

<!-- MANUAL:START - Safe to edit, preserved on updates -->
<!-- MANUAL:END -->
