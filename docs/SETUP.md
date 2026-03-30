<!--
DOCS_METADATA:
  generated_at: 2026-02-19T08:31:16Z
  git_hash: 4b2b46b
  tool_version: 1.0.0
  source_command: /create-magento-documentation
-->

# Development Setup

<!-- AUTO-GENERATED:START - Do not edit manually -->

## Requirements

| Dependency | Version |
|---|---|
| PHP | ≥ 7.2 |
| Magento Framework | ≥ 103.0.6 (Magento 2.3+) |
| `dpdconnect/php-sdk` | ^1.1 (auto-installed via Composer) |

## Installation in a Magento Store

```bash
# Install via Composer
composer require dpdconnect/magento2-shipping

# Enable the module
php bin/magento module:enable DpdConnect_Shipping

# Run upgrade (creates DB tables, adds EAV attributes)
php bin/magento setup:upgrade

# Compile DI
php bin/magento setup:di:compile

# Deploy static assets
php bin/magento setup:static-content:deploy -f

# Flush cache
php bin/magento cache:flush
```

## Working on the Module Itself

This repository contains only the module code, not a full Magento installation.

To develop and test changes:

1. Set up a local Magento 2 installation (≥ 2.3).
2. Add a local path repository in the Magento `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "/path/to/your/magento2-shipping"
        }
    ]
}
```

3. Require the package:

```bash
composer require dpdconnect/magento2-shipping:@dev
```

4. Or symlink the module directory directly into `app/code/DpdConnect/Shipping/`.

## Development Commands

```bash
# Flush cache (most common during development)
php bin/magento cache:flush

# Recompile DI after changing di.xml, plugins, or preferences
php bin/magento setup:di:compile

# Rerun setup:upgrade after changing Setup/ files
php bin/magento setup:upgrade

# Redeploy static assets after changing view/*/web/ files
php bin/magento setup:static-content:deploy -f

# Reindex (rarely needed for this module)
php bin/magento indexer:reindex

# Check module status
php bin/magento module:status DpdConnect_Shipping

# Run PHP linter (uses overtrue/phplint from require-dev)
vendor/bin/phplint .
```

## Configuration After Install

1. Go to **Stores → Configuration → Sales → DPD Parcelservice**.
2. Fill in **Account Settings**: username, password, depot, print format.
3. Fill in **Shipping Origin**: your warehouse address (sent as sender on every label).
4. Fill in **Store Information**: store contact details (used for customs and notifications).
5. Click **Test Connection** to verify credentials.
6. Go to **Stores → Configuration → Sales → Shipping Methods**.
7. Enable the DPD carrier(s) you want to use.
8. For Parcelshop pickup: configure Google Maps API keys.

## Async Webhook Setup

For async label generation, DPD needs to be able to call back to your store:

```
POST https://your-store.com/rest/default/V1/dpd-shipping/callback
```

Ensure this URL is:
- Publicly reachable from the internet (not behind VPN/firewall)
- Not blocked by WAF rules for POST requests
- Returns HTTP 200

Test with:
```bash
curl -X POST https://your-store.com/rest/default/V1/dpd-shipping/callback \
  -H "Content-Type: application/json" \
  -d '{"test": true}'
```

## Sensitive Configuration in `env.php`

Credentials can be stored in `env.php` instead of the database. They **must be encrypted**:

```bash
bin/magento config:sensitive:set dpdshipping/account_settings/username "yourusername"
bin/magento config:sensitive:set dpdshipping/account_settings/password "yourpassword"
```

Do not store plain-text credentials in `env.php`.

<!-- AUTO-GENERATED:END -->

<!-- MANUAL:START - Safe to edit, preserved on updates -->
<!-- MANUAL:END -->
