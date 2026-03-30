<!--
DOCS_METADATA:
  generated_at: 2026-02-19T08:31:16Z
  git_hash: 4b2b46b
  tool_version: 1.0.0
  source_command: /create-magento-documentation
-->

# DpdConnect_Shipping — Documentation

<!-- AUTO-GENERATED:START - Do not edit manually -->

**Module:** `dpdconnect/magento2-shipping` v1.0.8
**Type:** Magento 2 shipping module
**Integration:** DPD Connect API + Google Maps Geocoding API

## Start Here

| Audience | Document |
|---|---|
| Everyone | This file |
| Backend developers | [backend/README.md](backend/README.md) |
| Frontend developers | [frontend/README.md](frontend/README.md) |
| Support / Eerste Lijn | [support/README.md](support/README.md) — especially [support/TRIAGE.md](support/TRIAGE.md) |

## What This Module Does

This module integrates DPD parcel shipping into a Magento 2 store. It provides:

- **9 DPD carrier methods** for various service levels (Classic, Express, Saturday, Parcelshop, Predict, etc.)
- **Label generation** — synchronous for small batches, asynchronous (via webhook) for large batches
- **Parcelshop pickup** — Google Maps widget at checkout for customers to pick a DPD pickup point
- **Return labels** — generated alongside shipping labels or on demand
- **DPD Fresh/Freeze** — temperature-controlled shipments with expiration date per parcel
- **Customs support** — HS codes, export descriptions, consignor data for international shipments
- **Age check** — product-level flag that triggers DPD age verification at delivery

## Documentation Map

### Business Logic
| Document | Description |
|---|---|
| [business/SHIPPING.md](business/SHIPPING.md) | All carriers, product codes, rate types |
| [business/WORKFLOWS.md](business/WORKFLOWS.md) | End-to-end workflows: checkout → label → tracking |
| [business/INTEGRATIONS.md](business/INTEGRATIONS.md) | DPD API and Google Maps integration details |
| [business/RULES.md](business/RULES.md) | All business rules with code references |

### Backend
| Document | Description |
|---|---|
| [backend/MODULES.md](backend/MODULES.md) | All classes: services, helpers, models |
| [backend/OBSERVERS.md](backend/OBSERVERS.md) | 4 event observers |
| [backend/PLUGINS.md](backend/PLUGINS.md) | 2 interceptor plugins |
| [backend/API.md](backend/API.md) | REST webhook endpoint |
| [backend/DATABASE.md](backend/DATABASE.md) | Custom tables + schema changes |

### Frontend
| Document | Description |
|---|---|
| [frontend/COMPONENTS.md](frontend/COMPONENTS.md) | JS components and checkout integration |
| [frontend/TEMPLATES.md](frontend/TEMPLATES.md) | Phtml templates (frontend + admin) |

### Support
| Document | Description |
|---|---|
| [support/TRIAGE.md](support/TRIAGE.md) | **Decision tree for ticket triage** |
| [support/CONFIGURATION.md](support/CONFIGURATION.md) | All admin settings reference |
| [support/TROUBLESHOOTING.md](support/TROUBLESHOOTING.md) | Common issues and fixes |

### Diagrams
| Document | Description |
|---|---|
| [diagrams/ARCHITECTURE.md](diagrams/ARCHITECTURE.md) | Module architecture + sequence diagrams |
| [diagrams/FLOWS.md](diagrams/FLOWS.md) | Business flow diagrams |
| [diagrams/DATABASE.md](diagrams/DATABASE.md) | ERD for all custom tables |

## Key Files Quick Reference

| What you need | File |
|---|---|
| All config paths | `Helper/DpdSettings.php` |
| Label generation entry point | `Helper/Data.php` |
| DPD API client factory | `Helper/DPDClient.php` |
| Order → DPD payload conversion | `Helper/Services/OrderConvertService.php` |
| Async webhook handler | `Model/ApiCallback.php` |
| Database schema | `Setup/UpgradeSchema.php` |
| Admin menu | `etc/adminhtml/menu.xml` |

<!-- AUTO-GENERATED:END -->

<!-- MANUAL:START - Safe to edit, preserved on updates -->
<!-- MANUAL:END -->
