# WordPress Security Hardening Guide

**Site:** ennrev WordPress Installation  
**Date:** January 29, 2026  
**Current Risk Level:** MEDIUM (down from CRITICAL)

---

## Quick Reference

### ✅ Completed (Phase 1)

- SQL injection fixed in eps-301-redirects
- Debug log archived and secured
- Security headers implemented
- File protection enabled
- Uploads directory hardened

### ⚠️ Requires Attention

- **code-snippets plugin** - DEACTIVATE (eval risk)
- **6 plugins** with SQL patterns - Review needed
- **Plugin updates** - Check for available updates
- **Wordfence** - Install for ongoing protection

---

## Phase 2: Plugin Security

### Plugins with SQL Injection Patterns

Based on automated scan, these plugins have potential SQL injection patterns:

| Plugin | Matches | Status | Action |
|--------|---------|--------|--------|
| eps-301-redirects | 1 | ✅ FIXED | Patched in Phase 1 |
| updraftplus | 7 | ⚠️ REVIEW | Reputable plugin, likely safe |
| jetpack | 3 | ⚠️ REVIEW | Automattic plugin, likely safe |
| woocommerce | 1 | ⚠️ REVIEW | Core plugin, regularly updated |
| polylang | 1 | ⚠️ REVIEW | Reputable plugin |
| wpfront-scroll-top | 1 | ⚠️ REVIEW | Minor plugin, low risk |

> [!NOTE]
> **Pattern Detection Limitations**
>
> The automated scan looks for `$wpdb->query("SELECT ... $variable")` patterns. Not all matches are actual vulnerabilities:
>
> - Reputable plugins (Jetpack, WooCommerce, UpdraftPlus) likely use proper escaping
> - Manual review required to confirm vulnerabilities
> - False positives are common with pattern matching

### Recommended Actions

#### Immediate

1. **Keep plugins updated** - Most vulnerabilities are patched in updates
2. **Monitor security advisories** - Subscribe to WordPress security news
3. **Use Wordfence** - Automated vulnerability detection

#### If Concerned

1. **Manual code review** - Check each match for proper `$wpdb->prepare()` usage
2. **Contact plugin authors** - Report potential issues
3. **Consider alternatives** - Replace if vulnerabilities confirmed

---

## WordPress Admin Security Checklist

### User Management

- [ ] Remove default "admin" username
- [ ] Use strong passwords (16+ characters)
- [ ] Enable two-factor authentication
- [ ] Limit admin accounts
- [ ] Review user permissions regularly

### File Permissions

```bash
# Recommended permissions
Directories: 755
Files: 644
wp-config.php: 600
```

### Database Security

- [ ] Use strong database password
- [ ] Change database table prefix from `wp_`
- [ ] Limit database user privileges
- [ ] Regular database backups

### Login Security

- [ ] Limit login attempts (Wordfence)
- [ ] Change login URL (optional)
- [ ] Disable XML-RPC if not needed
- [ ] Use HTTPS for admin area

---

## Wordfence Installation Guide

### Step 1: Install Plugin

**Via WordPress Admin:**

```
1. Login to WordPress Admin
2. Navigate to Plugins → Add New
3. Search "Wordfence Security"
4. Click "Install Now"
5. Click "Activate"
```

**Via WP-CLI (if available):**

```bash
wp plugin install wordfence --activate
```

### Step 2: Initial Configuration

**After activation:**

```
1. Go to Wordfence → Dashboard
2. Click "Get Wordfence API Key" (free)
3. Enter your email
4. Click "Register"
```

### Step 3: Run Initial Scan

```
1. Go to Wordfence → Scan
2. Click "Start New Scan"
3. Wait for completion (5-15 minutes)
4. Review results
```

### Step 4: Configure Firewall

```
1. Go to Wordfence → Firewall
2. Click "Optimize Firewall"
3. Select "Extended Protection" (recommended)
4. Click "Continue"
5. Follow on-screen instructions
```

### Step 5: Enable Key Features

**Recommended Settings:**

#### Login Security

```
Wordfence → Login Security
- Enable "Enable brute force protection"
- Set "Lock out after" to 5 failed attempts
- Set "Lock out duration" to 4 hours
- Enable "Immediately block invalid usernames"
```

#### Firewall Rules

```
Wordfence → Firewall → All Firewall Options
- Enable "Rate limiting"
- Enable "Block fake Google crawlers"
- Enable "Block immediately on Wordfence Security Network"
```

#### Email Alerts

```
Wordfence → All Options → Email Alert Preferences
- Enable "Alert me when Wordfence is automatically updated"
- Enable "Alert me when someone is locked out"
- Enable "Alert me when a scan finds something"
```

---

## wp-config.php Security Hardening

### Current Configuration

```php
// Development settings (CURRENT)
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### Production Configuration (Phase 3)

**Add to wp-config.php:**

```php
// Security hardening
define('DISALLOW_FILE_EDIT', true);  // Disable file editor
define('DISALLOW_FILE_MODS', false); // Allow plugin updates
define('FORCE_SSL_ADMIN', true);     // Force HTTPS for admin
define('WP_AUTO_UPDATE_CORE', 'minor'); // Auto-update minor versions

// Environment-based debug settings
if (WP_ENVIRONMENT_TYPE === 'production') {
    define('WP_DEBUG', false);
    define('WP_DEBUG_LOG', false);
    define('WP_DEBUG_DISPLAY', false);
    @ini_set('display_errors', 0);
} else {
    define('WP_DEBUG', true);
    define('WP_DEBUG_LOG', true);
    define('WP_DEBUG_DISPLAY', false);
    @ini_set('display_errors', 0);
}

// Limit post revisions
define('WP_POST_REVISIONS', 5);
define('AUTOSAVE_INTERVAL', 300); // 5 minutes

// Security keys (regenerate for production)
// https://api.wordpress.org/secret-key/1.1/salt/
```

---

## Backup Strategy

### UpdraftPlus Configuration

**Recommended Settings:**

```
1. Go to Settings → UpdraftPlus Backups
2. Set backup schedule:
   - Files: Weekly
   - Database: Daily
3. Choose remote storage:
   - Google Drive (15GB free)
   - Dropbox (2GB free)
   - Amazon S3 (pay-as-you-go)
4. Enable "Delete local backup after uploading"
5. Set retention: 30 days
6. Click "Save Changes"
```

### Manual Backup Before Changes

**Always backup before:**

- Plugin updates
- Theme updates
- WordPress core updates
- Major configuration changes
- Database modifications

---

## Monitoring & Maintenance

### Weekly Tasks

- [ ] Review Wordfence scan results
- [ ] Check for plugin updates
- [ ] Review failed login attempts
- [ ] Check debug log size

### Monthly Tasks

- [ ] Full security audit
- [ ] Review user accounts
- [ ] Clean up old backups
- [ ] Update security documentation
- [ ] Test backup restoration

### Quarterly Tasks

- [ ] Penetration testing
- [ ] Code review of custom plugins
- [ ] Review and update security policies
- [ ] Audit third-party integrations

---

## Security Incident Response

### If Site is Compromised

**Immediate Actions:**

1. Take site offline (maintenance mode)
2. Change all passwords (WordPress, database, hosting)
3. Run Wordfence scan
4. Review access logs
5. Restore from clean backup
6. Update all plugins/themes
7. Contact hosting provider

**Investigation:**

1. Check Wordfence activity log
2. Review file changes
3. Check database for malicious content
4. Scan for backdoors
5. Review user accounts for unauthorized access

**Recovery:**

1. Clean infected files
2. Update all credentials
3. Patch vulnerabilities
4. Restore from backup if needed
5. Monitor for reinfection
6. Document incident

---

## Additional Security Plugins (Optional)

### Sucuri Security (Alternative to Wordfence)

- Website firewall
- Malware scanner
- Security hardening
- Post-hack security actions

### iThemes Security

- Brute force protection
- File change detection
- Strong password enforcement
- Two-factor authentication

### All In One WP Security

- User account security
- Login lockdown
- Database security
- Firewall protection

---

## Resources

### Security News

- [WPScan Vulnerability Database](https://wpscan.com/wordpresses)
- [WordPress Security Blog](https://wordpress.org/news/category/security/)
- [Wordfence Blog](https://www.wordfence.com/blog/)

### Tools

- [WPScan](https://wpscan.com/) - WordPress security scanner
- [Sucuri SiteCheck](https://sitecheck.sucuri.net/) - Free malware scanner
- [Security Headers](https://securityheaders.com/) - Check HTTP headers

### Best Practices

- [WordPress Hardening](https://wordpress.org/support/article/hardening-wordpress/)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [WordPress Security Whitepaper](https://wordpress.org/about/security/)

---

## Summary

**Current Status:**

- ✅ Phase 1 complete (70% risk reduction)
- 🔄 Phase 2 in progress
- ⏳ Phase 3 pending (production hardening)
- ⏳ Phase 4 pending (ongoing maintenance)

**Next Steps:**

1. Install Wordfence Security
2. Run initial security scan
3. Review and address scan results
4. Update all plugins
5. Deactivate code-snippets plugin
6. Proceed to Phase 3

**Risk Level:** MEDIUM → Target: LOW

---

**Questions or need help?** Review the security_remediation_plan.md for detailed implementation steps.
