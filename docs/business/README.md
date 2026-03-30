<!--
DOCS_METADATA:
  generated_at: 2026-02-19T08:31:16Z
  git_hash: 4b2b46b
  tool_version: 1.0.0
  source_command: /create-magento-documentation
-->

# Business Logic Overview

<!-- AUTO-GENERATED:START - Do not edit manually -->

This module integrates the **DPD Connect shipping API** into a Magento 2 store. It handles the full lifecycle of a DPD shipment: carrier selection at checkout → label generation → tracking number attachment → async callback processing.

## Core Concepts

### The Unified Carrier (`dpd_dpd`)

The main carrier code `dpd` acts as a **single configurable carrier** that presents one or more DPD shipping products to the customer at checkout. Internally these products map to DPD API product codes (B2B, B2C, B2C6, etc.).

Legacy carriers (`dpdexpress10`, `dpdexpress12`, `dpdsaturday`, etc.) still exist for backwards compatibility but orders placed via those carriers are **automatically converted** to `dpd_dpd` on save.

### Two Checkout Modes

| Mode | Carrier | How it works |
|---|---|---|
| **Home delivery** | `dpd` | Customer selects DPD method; product code is stored on the quote |
| **Parcelshop** | `dpdpickup` | Customer picks a DPD parcelshop via Google Maps; shop data stored on quote |

### Label Generation Modes

| Mode | When | How |
|---|---|---|
| **Synchronous** | Default; small batches | Label is generated immediately and returned as PDF |
| **Asynchronous** | When batch exceeds threshold | Request queued at DPD; label delivered via webhook callback |

## Documents in This Section

| Document | Description |
|---|---|
| [SHIPPING.md](SHIPPING.md) | All carrier codes, product codes, rate types |
| [WORKFLOWS.md](WORKFLOWS.md) | End-to-end label generation workflows |
| [INTEGRATIONS.md](INTEGRATIONS.md) | DPD Connect API and Google Maps integration |
| [RULES.md](RULES.md) | All business rules in one reference |

<!-- AUTO-GENERATED:END -->

<!-- MANUAL:START - Safe to edit, preserved on updates -->
<!-- Add custom notes, team-specific information, or project-specific details below -->
<!-- MANUAL:END -->
