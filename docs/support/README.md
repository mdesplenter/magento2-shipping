<!--
DOCS_METADATA:
  generated_at: 2026-02-19T08:31:16Z
  git_hash: 4b2b46b
  tool_version: 1.0.0
  source_command: /create-magento-documentation
-->

# Support Quick Reference

<!-- AUTO-GENERATED:START - Do not edit manually -->

## First-Line Support Checklist

When a DPD-related ticket arrives, start here:

1. **Is it a label issue?** → See [TRIAGE.md](TRIAGE.md)
2. **Is it a configuration question?** → See [CONFIGURATION.md](CONFIGURATION.md)
3. **Is it a known error?** → See [TROUBLESHOOTING.md](TROUBLESHOOTING.md)

## What First-Line CAN Do

| Task | How |
|---|---|
| Verify DPD credentials are valid | Admin → Stores → Config → DPD → "Test Connection" button |
| Check batch/async job status | Admin → Sales → DPD → Batches |
| Download a specific label | Admin → Sales → DPD → Labels → Download |
| Retry a failed batch | Admin → Sales → DPD → Batches → Retry |
| Check if order has a DPD label | Admin → Sales → DPD → Labels → filter by order ID |
| Flush cache | Admin → System → Cache Management → Flush Magento Cache |
| Check shipping method on an order | Admin → Sales → Orders → view order → Shipping Method column |

## What Requires Developer Escalation

- Any PHP exception in `var/log/`
- Label data not appearing after async callback (potential webhook reachability issue)
- Parcelshop map not loading (Google Maps API key issue)
- Customer product settings not saving correctly
- Database errors on any DPD table
- Callback URL returning errors

## Key Admin Paths

| Function | Admin path |
|---|---|
| DPD Configuration | Stores → Configuration → Sales → DPD Parcelservice |
| Carrier settings | Stores → Configuration → Sales → Shipping Methods |
| Batch monitor | Sales → DPD → Batches |
| Label archive | Sales → DPD → Labels |
| Flush cache | System → Cache Management |

## Log Files

| File | What it contains |
|---|---|
| `var/log/system.log` | General Magento errors |
| `var/log/exception.log` | PHP exceptions |
| `var/log/debug.log` | Debug-level entries (if enabled) |

DPD-specific errors are not written to a separate log file — they appear in `exception.log` or as admin flash messages.

<!-- AUTO-GENERATED:END -->

<!-- MANUAL:START - Safe to edit, preserved on updates -->
<!-- MANUAL:END -->
