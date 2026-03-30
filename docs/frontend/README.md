<!--
DOCS_METADATA:
  generated_at: 2026-02-19T08:31:16Z
  git_hash: 4b2b46b
  tool_version: 1.0.0
  source_command: /create-magento-documentation
-->

# Frontend Overview

<!-- AUTO-GENERATED:START - Do not edit manually -->

The frontend of this module consists of:
- **Checkout integration** — injects DPD shipping method components and the parcelshop map
- **JavaScript components** — RequireJS/KnockoutJS components for the Luma/standard checkout
- **Templates** — Phtml templates for parcelshop display and the Google Maps widget

All frontend assets are in `view/frontend/`.

## Documents in This Section

| Document | Description |
|---|---|
| [COMPONENTS.md](COMPONENTS.md) | JS components and checkout integration |
| [TEMPLATES.md](TEMPLATES.md) | Phtml templates |

## Layout Injection Points

| Layout handle | What is injected |
|---|---|
| `checkout_index_index` | DPD shipping components (via `view/frontend/layout/checkout_index_index.xml`) |
| `onestepcheckout_index_index` | DPD components for Onestepcheckout compatibility |

<!-- AUTO-GENERATED:END -->

<!-- MANUAL:START - Safe to edit, preserved on updates -->
<!-- MANUAL:END -->
