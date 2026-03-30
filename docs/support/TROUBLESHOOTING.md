<!--
DOCS_METADATA:
  generated_at: 2026-02-19T08:31:16Z
  git_hash: 4b2b46b
  tool_version: 1.0.0
  source_command: /create-magento-documentation
-->

# Troubleshooting Guide

<!-- AUTO-GENERATED:START - Do not edit manually -->

## Label Generation Errors

### "Bad credentials" / credentials not valid

**Symptom:** Admin flash message "Your credentials are NOT valid!" after saving config, or "Bad credentials" when printing a label.

**Cause:** Username/password combination is incorrect, or the password was set via `env.php` without encryption.

**Solution:**
1. Go to **Stores → Config → DPD → Account Settings**.
2. Re-enter username and password and click **Save Config**.
3. Use the **Test Connection** button.
4. If credentials are correct but stored in `env.php`, re-set the password using:
   ```bash
   bin/magento config:sensitive:set dpdshipping/account_settings/password "yourpassword"
   ```
   The password must be encrypted when stored in `env.php`.

**Escalate?** No — support can fix this.

---

### "This order has DPD Fresh/Freeze products, shipments can only be made through the order overview or the packages screen."

**Symptom:** Exception when trying to create a shipment via the standard Magento shipment UI.

**Cause:** Order contains products with `dpd_shipping_type = fresh` or `freeze`. This is enforced by `Observer/SalesOrderShipmentSaveBefore`.

**Solution:** Create the shipment via **Sales → Orders → [order] → Create DPD Shipment** or use the **Packages** popup.

**Escalate?** No — this is by design.

---

### DPD API error message shown in admin

**Symptom:** Admin error message like "[DPD-Shipment] Error occurred" or a specific DPD error.

**Cause:** The DPD API returned an error. Common reasons: invalid address data, missing HS code for international shipment, depot mismatch.

**Solution:**
1. Check the exact error message in the admin flash notification.
2. Verify the shipping origin and store information fields are complete and valid (see [CONFIGURATION.md](CONFIGURATION.md)).
3. For international orders: check that products have `hs_code` set.
4. If error mentions address: check customer shipping address format.

**Escalate?** If the address data appears correct and the error persists, escalate.

---

### Labels not appearing after async batch

**Symptom:** Batch was created, jobs show `queued` but never update to `success`.

**Cause options:**
1. The callback webhook URL is not publicly reachable from DPD servers.
2. Magento is returning an error on the callback endpoint.

**Solution:**
1. Check batch status: **Sales → DPD → Batches**.
2. Verify webhook is reachable: `POST https://your-store.com/rest/default/V1/dpd-shipping/callback` should return HTTP 200.
3. Check `var/log/exception.log` for errors during the callback.
4. If the store is behind a firewall or VPN, DPD's servers cannot call back.

**Escalate?** Yes — if the endpoint is unreachable or throwing errors.

---

### "DPD - There are no shipping labels generated."

**Symptom:** Mass action returns this flash message with no labels downloaded.

**Cause:** No DPD orders were selected, or selected orders are not DPD shipping method orders.

**Solution:** Verify the selected orders have a `dpd*` shipping method. Filter the orders grid by shipping method.

**Escalate?** No.

---

## Checkout / Parcelshop Issues

### Parcelshop map not loading

**Symptom:** Google Maps widget does not appear at checkout when DPD Pickup is selected.

**Cause options:**
1. Google Maps client API key not configured.
2. API key missing Maps JavaScript API permission.
3. CSP (Content Security Policy) blocking Google Maps.

**Solution:**
1. Go to **Stores → Config → Shipping Methods → DPD Pickup → Google Maps API Key**.
2. Ensure the key has **Maps JavaScript API** and **Geocoding API** enabled in Google Cloud Console.
3. Check browser console for CSP errors.

**Escalate?** If key is correct and CSP is not the issue, escalate.

---

### Parcelshop search returns "No address found"

**Symptom:** Customer types postcode + country but gets "No address found" error.

**Cause:** Google Maps server-side API key is not configured or invalid.

**Solution:** Configure the Google Maps server key (`carriers/dpdpickup/google_maps_api_server`). This key must have **Geocoding API** enabled.

**Escalate?** No — support can configure the key.

---

### Saturday delivery not showing at checkout

**Symptom:** DPD Saturday carrier is enabled but does not appear at checkout.

**Cause:** Current date/time is outside the configured display window.

**Solution:**
1. Go to **Stores → Config → Shipping Methods → DPD Saturday**.
2. Check "Shown from day/time" and "Shown till day/time".
3. The default values (Monday 00:00 → Monday 00:00) mean it **never shows**.
4. Set a proper window, e.g. Monday 00:00 → Thursday 17:00.

**Escalate?** No.

---

## Account / Authentication Issues

### JWT token errors in logs

**Symptom:** `var/log/exception.log` shows JWT authentication failures.

**Cause:** Cached JWT token has expired or is invalid, and the re-authentication is failing.

**Solution:**
1. Flush Magento cache: **System → Cache Management → Flush Magento Cache**.
2. This clears the cached JWT token (`dpdconnect_jwt_token_*`).
3. The next API call will re-authenticate automatically.

**Escalate?** If flush does not resolve the issue, check credentials and escalate.

---

### Config validation errors on save ("value for X in group Y is NOT valid")

**Symptom:** After saving DPD configuration, error messages appear about field values being invalid.

**Cause:** One or more address fields exceed DPD API length limits (see [CONFIGURATION.md](CONFIGURATION.md)).

**Solution:** Shorten the offending field value to within the limit. The error message identifies which field and which group (Shipping Origin or Store Information).

**Escalate?** No — support can fix the field values.

<!-- AUTO-GENERATED:END -->

<!-- MANUAL:START - Safe to edit, preserved on updates -->
<!-- MANUAL:END -->
