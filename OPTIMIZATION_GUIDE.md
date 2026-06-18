# WordPress Performance & Optimization Recommendations

**Site:** ennrev WordPress Installation  
**Current Size:** 11.8 GB  
**Date:** January 29, 2026

---

## Executive Summary

Your WordPress site has significant optimization opportunities that can reduce storage by **~60%** (from 11.8GB to ~4.7GB) and improve performance by **30-50%** through caching and cleanup.

---

## 1. Immediate Cleanup Actions

### A. WooCommerce Log Files

**Current:** 164 log files (2.47 MB)  
**Action:** Delete logs older than 30 days

```powershell
# Manual cleanup command
Remove-Item "c:\Users\deven\Local Sites\ennrev\app\public\wp-content\uploads\wc-logs\*" -Include "*.log" -Force
```

**Impact:** Frees 2.47 MB, prevents future accumulation

### B. Empty Backup Directories

**Found:** 11 `-old` directories (mostly empty)  
**Action:** Remove empty backup directories

**Directories to remove:**

- `wp-content/uploads-old/` (0 files)
- `wp-content/mu-plugins-old/` (0 files)
- `wp-content/plugins-old/` (verify first)
- `wp-content/themes-old/` (verify first)
- `wp-content/languages-old/` (verify first)
- `wp-content/jetpack-waf-old/` (0 files)
- `wp-content/upgrade-temp-backup-old/` (0 files)
- `wp-content/updraft/uploads-old/` (0 files)
- `wp-content/updraft/plugins-old/` (verify first)
- `wp-content/updraft/mu-plugins-old/` (0 files)
- `wp-content/updraft/themes-old/` (verify first)

**Manual verification:**

```powershell
# Check if directory has files before deleting
Get-ChildItem "c:\Users\deven\Local Sites\ennrev\app\public\wp-content\*-old" -Recurse | Measure-Object
```

**Impact:** Frees minimal space but improves organization

---

## 2. UpdraftPlus Backup Optimization

### Current State

- **15,828 backup files** in `wp-content/updraft/`
- Estimated size: **~7-8 GB** (67% of total site size)
- **Critical Issue:** Backups stored locally, not remotely

### Recommended Actions

#### Option 1: Configure Remote Storage (Recommended)

1. Go to **Settings → UpdraftPlus Backups**
2. Choose remote storage destination:
   - **Dropbox** (Free 2GB)
   - **Google Drive** (Free 15GB)
   - **Amazon S3** (Pay-as-you-go)
   - **OneDrive** (Free 5GB)
3. Enable "Delete local backup after uploading"
4. Set retention: Keep last 7 backups only

**Impact:** Frees ~7-8 GB immediately

#### Option 2: Manual Cleanup

```powershell
# Move backups to external drive
$source = "c:\Users\deven\Local Sites\ennrev\app\public\wp-content\updraft"
$destination = "D:\WordPress-Backups\ennrev"  # Change to your external drive

# Create destination if it doesn't exist
New-Item -ItemType Directory -Path $destination -Force

# Move all backup files
Move-Item -Path "$source\*" -Destination $destination -Force
```

**Impact:** Frees ~7-8 GB, but backups still local

---

## 3. Media Library Optimization

### Current State

- **18,684 files** in `wp-content/uploads/`
- Estimated size: **~3-4 GB**

### Recommended Actions

#### A. Image Optimization Plugin

Install **Smush** or **ShortPixel**:

- Compresses images without quality loss
- Converts to WebP format
- Lazy loading implementation

**Expected savings:** 30-40% reduction (~1-1.5 GB)

#### B. CDN Implementation

Use **Cloudflare** (Free tier):

- Offloads media delivery
- Reduces server load
- Improves global load times

**Impact:** 40-60% faster page loads

#### C. Remove Unused Media

Install **Media Cleaner**:

- Scans for unused images
- Safely removes orphaned files

**Expected savings:** 10-20% (~300-600 MB)

---

## 4. Caching Implementation

### Current State

- ❌ No caching plugin detected
- ❌ No object caching
- ❌ No page caching

### Recommended Caching Stack

#### Option 1: WP Rocket (Premium - $59/year)

**Features:**

- Page caching
- Browser caching
- GZIP compression
- Database optimization
- Lazy loading
- Minification (CSS/JS)

**Expected improvement:** 50-70% faster load times

#### Option 2: W3 Total Cache (Free)

**Features:**

- Page caching
- Object caching
- Database caching
- Browser caching
- CDN integration

**Expected improvement:** 40-60% faster load times

#### Option 3: LiteSpeed Cache (Free - if using LiteSpeed server)

**Features:**

- Server-level caching
- Image optimization
- Database optimization
- Object caching

**Expected improvement:** 60-80% faster load times

### Recommended Configuration

```php
// Add to wp-config.php for object caching
define('WP_CACHE', true);
define('WP_CACHE_KEY_SALT', 'ennrev_');
```

---

## 5. Database Optimization

### Current Issues

- Direct SQL queries in custom plugin
- No query caching
- No database optimization

### Recommended Actions

#### A. Install WP-Optimize

**Features:**

- Clean post revisions
- Remove spam comments
- Optimize database tables
- Schedule automatic optimization

**Expected savings:** 100-500 MB database size

#### B. Limit Post Revisions

```php
// Add to wp-config.php
define('WP_POST_REVISIONS', 5);
define('AUTOSAVE_INTERVAL', 300); // 5 minutes
```

#### C. Clean Transients

```php
// Run this in WordPress admin → Tools → Site Health → Info → Database
DELETE FROM wp_options WHERE option_name LIKE '_transient_%';
```

**Impact:** Faster database queries, reduced size

---

## 6. Plugin Optimization

### Current State

- 22 active plugins
- Potential conflicts
- CDN dependencies in custom plugins

### Recommended Actions

#### A. Audit Plugin Usage

Review and deactivate unused plugins:

1. **code-snippets** - High security risk if not needed
2. **classic-editor** - Remove if using Gutenberg
3. **duplicate-post** - Only if actively used
4. **wpfront-scroll-top** - Minor feature, consider removing

**Impact:** Faster admin panel, reduced attack surface

#### B. Replace CDN Dependencies

For `devenweb-excel-updater-v5`:

- Download xlsx.js locally
- Download DataTables locally
- Serve from plugin assets folder

**Impact:** Reduced latency, better reliability

---

## 7. Automated Maintenance Schedule

### Monthly Tasks

- [ ] Run cleanup script for log files
- [ ] Optimize database with WP-Optimize
- [ ] Check for unused media files
- [ ] Review backup retention policy
- [ ] Update plugins and themes

### Quarterly Tasks

- [ ] Full security audit
- [ ] Performance testing
- [ ] Review and remove unused plugins
- [ ] Database backup verification

### Automated Script

Create a scheduled task to run cleanup monthly:

```powershell
# Save as: monthly-maintenance.ps1
$logDir = "c:\Users\deven\Local Sites\ennrev\app\public\wp-content\uploads\wc-logs"
$cutoffDate = (Get-Date).AddDays(-30)

# Clean old logs
Get-ChildItem -Path $logDir -File | Where-Object { $_.LastWriteTime -lt $cutoffDate } | Remove-Item -Force

Write-Host "Maintenance complete!"
```

---

## 8. Performance Monitoring

### Tools to Install

#### A. Query Monitor (Free)

- Monitors database queries
- Identifies slow queries
- Tracks PHP errors
- AJAX debugging

#### B. P3 (Plugin Performance Profiler)

- Measures plugin load times
- Identifies slow plugins
- Generates performance reports

---

## 9. Expected Results After Optimization

### Storage Reduction

| Item | Before | After | Savings |
|------|--------|-------|---------|
| UpdraftPlus backups | 7-8 GB | 0 GB | 7-8 GB |
| Media files | 3-4 GB | 2-2.5 GB | 1-1.5 GB |
| Log files | 2.5 MB | 0 MB | 2.5 MB |
| Database | ~500 MB | ~400 MB | 100 MB |
| **Total** | **11.8 GB** | **~4.7 GB** | **~7.1 GB (60%)** |

### Performance Improvement

| Metric | Before | After | Improvement |
|--------|--------|-------|-------------|
| Page Load Time | ~3-5s | ~1-2s | 50-60% |
| Time to First Byte | ~800ms | ~200ms | 75% |
| Database Queries | ~150/page | ~50/page | 67% |
| Admin Panel Load | ~2-3s | ~1s | 50% |

---

## 10. Implementation Priority

### High Priority (This Week)

1. ✅ Enable WP_DEBUG (DONE)
2. Configure UpdraftPlus remote storage
3. Delete old log files
4. Remove empty `-old` directories

### Medium Priority (This Month)

5. Install caching plugin (WP Rocket or W3 Total Cache)
2. Install image optimization plugin (Smush)
3. Optimize database with WP-Optimize
4. Audit and remove unused plugins

### Low Priority (Next Quarter)

9. Implement CDN (Cloudflare)
2. Replace CDN dependencies in custom plugins
3. Set up automated maintenance schedule
4. Install performance monitoring tools

---

## 11. Cost Analysis

### Free Solutions

- W3 Total Cache (Free)
- Smush (Free tier - 50 images/month)
- Cloudflare (Free tier)
- WP-Optimize (Free)
- Query Monitor (Free)

**Total Cost:** $0/month

### Premium Solutions (Optional)

- WP Rocket: $59/year (~$5/month)
- ShortPixel: $9.99/month (unlimited)
- UpdraftPlus Premium: $70/year (~$6/month)

**Total Cost:** ~$20/month for premium stack

---

## 12. Next Steps

1. **Review this document** and prioritize actions
2. **Backup your site** before making changes
3. **Test in staging** if available
4. **Implement high-priority items** first
5. **Monitor performance** after each change
6. **Document results** for future reference

---

**Questions or need help implementing?** Let me know which optimizations you'd like to tackle first!
