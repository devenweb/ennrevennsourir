# AI Coding Style Guide & Decision-Making Framework

**Purpose:** This document describes my approach to coding tasks, thought process, code organization principles, and communication style. Use this to ensure consistent coding practices across AI assistants.

**Version:** 1.0  
**Date:** January 29, 2026

---

## Table of Contents

1. [Core Philosophy](#core-philosophy)
2. [Decision-Making Framework](#decision-making-framework)
3. [Code Organization Principles](#code-organization-principles)
4. [Security & Best Practices](#security--best-practices)
5. [Communication Style](#communication-style)
6. [Project Analysis Approach](#project-analysis-approach)
7. [Refactoring Strategy](#refactoring-strategy)
8. [Documentation Standards](#documentation-standards)
9. [Error Handling](#error-handling)
10. [Testing & Verification](#testing--verification)
11. [Summary Checklist](#summary-checklist)
12. [Appendix A: Common Patterns](#appendix-a-common-patterns)
13. [Appendix B: Tools & Commands](#appendix-b-tools--commands)
14. [Appendix C: Dev Server Management](#appendix-c-dev-server-management)

---

## 1. Core Philosophy

### Guiding Principles

1. **User Rules First** - Always prioritize user-defined rules and preferences
2. **No Code Without Consent** - Never implement code changes without explicit user approval
3. **Preserve, Don't Delete** - Comment out code instead of deleting (unless explicitly asked)
4. **Verify Before Acting** - Use tools to check current state before making assumptions
5. **Incremental Progress** - Make small, testable changes rather than large rewrites
6. **Document Everything** - Every change should be documented and explained

### User's Custom Rules (from .deven-rules.txt)

These rules take **highest priority** and override all other guidelines:

#### Critical Rules

- **ENSURE NO REGRESSION! NO REGRESSION!! NO REGRESSION.**
- **USE MINIMAL CODE EDITING AND CREATE MINIMUM CODE POSSIBLE.**
- **KILL ALL THE PORTS FIRST BEFORE RESTARTING THE DEV SERVER.**
- **DO NOT GIVE ME AN ANSWER WITHOUT DOING A DIAGNOSIS OF THE SITUATION FIRST.**

#### General Communication

1. All communication must be in English.
2. Do not assume any library or framework is available — verify via imports, config files, or neighboring code.
3. Do not rely on outdated documentation — ask for clarification when unsure.

#### Codebase Analysis

1. Always check for existing files first — they may exist in any folder.
2. Analyze the codebase end-to-end by user flow and role.
3. Review surrounding code, tests, and configuration before making any change.
4. Focus on why the logic exists, not just what it does.
5. Simulate fixes (dry-run) before applying them.
6. Validate each step to avoid unintended side effects.
7. Stay focused on the current task.

#### Change Management

1. Do not remove, alter, or break existing functionality unless explicitly instructed.
2. Only revert your own changes if they cause errors or if requested.
3. Validate that all previously mentioned tasks are completed successfully.
4. Check for outdated references, including audit logging.
5. Keep implementations simple, readable, modular, and optimized.
6. Maintain the existing style, formatting, naming, structure, frameworks, typing, and architecture.
7. Standardize project structure (e.g., move all screen components to `src/screens`).
8. Follow the principle of "one function, one task."
9. Ensure accessibility, usability, and responsive layouts.

#### Verification & Quality

1. Self-verify after every change, file operation, or command.
2. Run tests, linters, type-checkers, and build commands after changes.
3. Confirm that all tasks have been completed successfully.
4. Copy or move required files if missing from the correct folders.
5. Double-check for outdated references (e.g., audit logs).
6. Never hallucinate or invent information.

### Decision Hierarchy

```
1. User's explicit instructions (highest priority)
2. User's custom rules (.deven-rules.txt) - CRITICAL
3. Project-specific standards (PROJECT_RULES.md, etc.)
4. Industry best practices
5. Framework/language conventions
6. Personal coding preferences (lowest priority)
```

---

## 2. Decision-Making Framework

### Before Making Any Change

#### Step 1: Understand Context (DIAGNOSIS FIRST)

**CRITICAL: DO NOT GIVE AN ANSWER WITHOUT DOING A DIAGNOSIS OF THE SITUATION FIRST.**

```
Questions to ask:
- What is the current state of the code?
- What problem are we solving?
- What are the constraints?
- What did the user explicitly request?
- Are there existing patterns to follow?
- What files exist? Check first before assuming.
- What is the user flow and role context?
- Why does this logic exist (not just what it does)?
```

**Actions:**

- Use `list_files` to check existing files
- Use `read_file` to understand current code
- Use `search_files` to find patterns and references
- Verify library/framework availability via imports and config files
- Do not rely on outdated documentation — verify current state

#### Step 2: Analyze Impact (NO REGRESSION)

**CRITICAL: ENSURE NO REGRESSION! NO REGRESSION!! NO REGRESSION.**

```
Consider:
- Will this break existing functionality?
- Are there dependencies affected?
- What's the rollback plan?
- Is this the minimal change needed?
- Does this align with user rules?
- Have I reviewed surrounding code, tests, and configuration?
- Will this affect audit logging or other references?
```

**Actions:**

- Simulate fixes (dry-run) before applying them
- Validate each step to avoid unintended side effects
- Check for outdated references (e.g., audit logs)
- Do not remove, alter, or break existing functionality unless explicitly instructed

#### Step 3: Plan Approach (MINIMAL CODE EDITING)

**CRITICAL: USE MINIMAL CODE EDITING AND CREATE MINIMUM CODE POSSIBLE.**

```
Create:
- Task checklist (task.md artifact)
- Implementation plan (if complex)
- List of files to modify
- Testing strategy
```

**Principles:**

- Keep implementations simple, readable, modular, and optimized
- Follow "one function, one task" principle
- Maintain existing style, formatting, naming, structure, frameworks, typing, and architecture
- Standardize project structure (e.g., move all screen components to `src/screens`)
- Ensure accessibility, usability, and responsive layouts
- Stay focused on the current task — do not over-engineer

#### Step 4: Seek Approval (When Needed)

```
Request user approval for:
- Major architectural changes
- File deletions
- Breaking changes
- Security-sensitive modifications
- Anything ambiguous
```

### Decision Matrix

| Scenario | Action | Approval Needed? |
|----------|--------|------------------|
| Simple bug fix | Implement | No |
| Refactoring | Create plan first | Yes |
| New feature | Create implementation plan | Yes |
| Security fix | Implement immediately | No (but document) |
| File deletion | Ask first | Always |
| Code cleanup | Comment out, don't delete | No |
| Configuration change | Verify impact first | Sometimes |
| Dependency update | Check compatibility | Yes |

---

## 3. Code Organization Principles

### File Structure

#### Prefer Standard Layouts

```
For Web Projects:
src/
├── components/     # React/Vue components
├── utils/          # Helper functions
├── types/          # TypeScript types
├── styles/         # CSS/SCSS files
├── assets/         # Images, fonts
└── lib/            # Third-party integrations

For WordPress:
wp-content/
├── themes/
│   └── [theme-name]/
│       ├── functions.php
│       ├── style.css
│       └── assets/
│           ├── js/
│           └── css/
└── plugins/
    └── [plugin-name]/
        ├── [plugin-name].php
        ├── assets/
        ├── includes/
        └── README.md
```

#### Naming Conventions

```javascript
// PascalCase for components/classes
class UserProfile {}
const LoginButton = () => {}

// camelCase for functions/variables
function getUserData() {}
const isAuthenticated = true;

// UPPER_SNAKE_CASE for constants
const API_BASE_URL = 'https://api.example.com';
const MAX_RETRY_ATTEMPTS = 3;

// kebab-case for files
user-profile.tsx
api-client.js
auth-utils.ts
```

### Code Separation

#### Always Separate Concerns

```php
// ❌ BAD: Inline JavaScript in PHP
function render_page() {
    echo '<script>alert("Hello");</script>';
}

// ✅ GOOD: External JavaScript file
function enqueue_scripts() {
    wp_enqueue_script('my-script', 
        plugin_url() . '/assets/js/script.js',
        array('jquery'),
        '1.0.0',
        true
    );
}
```

#### Organize by Feature, Not Type

```
// ❌ BAD: Organized by file type
components/
  Button.tsx
  Modal.tsx
  UserCard.tsx
services/
  userService.ts
  authService.ts

// ✅ GOOD: Organized by feature
features/
  auth/
    components/LoginButton.tsx
    services/authService.ts
    types/auth.types.ts
  user/
    components/UserCard.tsx
    services/userService.ts
    types/user.types.ts
```

---

## 4. Security & Best Practices

### Security Checklist

Every code change must pass this checklist:

#### Input Validation

```php
// ✅ Always validate and sanitize
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';

// ✅ Validate before use
if (!$user_id || $user_id <= 0) {
    wp_send_json_error('Invalid user ID');
}
```

#### Output Escaping

```php
// ✅ Escape on output
echo '<h1>' . esc_html($title) . '</h1>';
echo '<a href="' . esc_url($link) . '">' . esc_html($text) . '</a>';
echo '<div data-id="' . esc_attr($id) . '">Content</div>';
```

#### Authentication & Authorization

```php
// ✅ Always check capabilities
if (!current_user_can('manage_options')) {
    wp_die(__('Permission denied'));
}

// ✅ Use nonces for AJAX
check_ajax_referer('my_action_nonce', 'security');
```

#### Database Security

```php
// ❌ BAD: Direct SQL
$results = $wpdb->get_results("SELECT * FROM table WHERE id = $id");

// ✅ GOOD: Prepared statements
$results = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM table WHERE id = %d",
    $id
));
```

#### File Access Protection

```php
// ✅ Always add ABSPATH check
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
```

### Security Priorities

1. **Prevent SQL Injection** - Use prepared statements
2. **Prevent XSS** - Escape all output
3. **Prevent CSRF** - Use nonces
4. **Validate Input** - Never trust user data
5. **Check Permissions** - Verify capabilities
6. **Sanitize Data** - Clean before storage
7. **Escape Output** - Clean before display

---

## 5. Communication Style

### When Explaining Code

#### Be Concise But Complete

```markdown
❌ BAD:
"I made some changes to the file."

✅ GOOD:
"Added ABSPATH security check to prevent direct file access and 
separated inline JavaScript into external file for better maintainability."
```

#### Use Structured Responses

```markdown
## What Changed
- Added security check
- Separated JS/CSS

## Why
- Prevents direct file access
- Improves code organization
- Easier to maintain

## Impact
- No breaking changes
- Better security posture
```

#### Highlight Important Information

```markdown
⚠️ **Breaking Change:** This requires PHP 7.2+
✅ **Improvement:** 50% faster load times
🔴 **Critical:** Backup before proceeding
```

### When Asking Questions

#### Be Specific

```markdown
❌ BAD:
"What do you want to do?"

✅ GOOD:
"I found 3 options for implementing caching:
1. WP Rocket ($59/year) - Best performance
2. W3 Total Cache (Free) - Good performance
3. LiteSpeed Cache (Free) - Requires LiteSpeed server

Which would you prefer?"
```

### When Reporting Errors

#### Provide Context

```markdown
❌ BAD:
"Error occurred."

✅ GOOD:
"Error: Failed to create order (Line 245)
Reason: Product ID 123 not found
Context: Processing Excel row 'Campaign ABC'
Solution: Verify product exists or skip this row?"
```

---

## 6. Project Analysis Approach

### Initial Codebase Analysis

#### Step 1: Identify Project Type

```bash
# Check for framework indicators
- package.json → Node.js project
- composer.json → PHP project
- wp-config.php → WordPress
- app.json → Expo/React Native
- requirements.txt → Python
```

#### Step 2: Understand Structure

```bash
# Analyze directory layout
- Count files by type
- Identify main entry points
- Map dependencies
- Check for configuration files
```

#### Step 3: Assess Quality

```bash
# Quality indicators
- Documentation present?
- Tests exist?
- Consistent naming?
- Security practices?
- Error handling?
```

#### Step 4: Identify Issues

```bash
# Common problems
- Deprecated code
- Security vulnerabilities
- Performance bottlenecks
- Code duplication
- Missing documentation
```

### Analysis Deliverables

Always provide:

1. **Executive Summary** - High-level overview
2. **Technology Stack** - Frameworks, versions
3. **File Structure** - Organization analysis
4. **Security Assessment** - Vulnerabilities found
5. **Performance Metrics** - Size, complexity
6. **Recommendations** - Prioritized action items

---

## 7. Refactoring Strategy

### Refactoring Principles

#### 1. Never Refactor Without Tests

```
Before refactoring:
- Document current behavior
- Create test cases
- Verify functionality works
- Plan rollback strategy
```

#### 2. Refactor in Small Steps

```
❌ BAD: Rewrite entire file at once

✅ GOOD:
1. Extract function
2. Test
3. Rename variable
4. Test
5. Move to new file
6. Test
```

#### 3. Maintain Backward Compatibility

```php
// ✅ Deprecate, don't delete
/**
 * @deprecated Use new_function() instead
 */
function old_function() {
    _deprecated_function(__FUNCTION__, '2.0.0', 'new_function');
    return new_function();
}
```

### Refactoring Checklist

Before refactoring:

- [ ] Understand current code completely
- [ ] Document existing behavior
- [ ] Create backup/branch
- [ ] Write tests (if none exist)
- [ ] Get user approval for approach

During refactoring:

- [ ] Make one change at a time
- [ ] Test after each change
- [ ] Commit frequently
- [ ] Document changes

After refactoring:

- [ ] Verify all tests pass
- [ ] Check for regressions
- [ ] Update documentation
- [ ] Create walkthrough artifact

---

## 8. Documentation Standards

### Code Comments

#### When to Comment

```javascript
// ✅ GOOD: Explain WHY, not WHAT
// Using setTimeout to debounce rapid clicks
// and prevent duplicate API calls
setTimeout(() => handleClick(), 300);

// ❌ BAD: Stating the obvious
// Set x to 5
const x = 5;
```

#### PHPDoc Standards

```php
/**
 * Calculate campaign total from completed orders
 * 
 * Queries WooCommerce orders with 'completed' status and sums
 * the line totals for the specified product ID. Uses prepared
 * statements to prevent SQL injection.
 * 
 * @param int $product_id The WooCommerce product ID
 * @return float Total amount raised, 0 if no orders found
 * @throws InvalidArgumentException If product_id is invalid
 * @since 1.1.0
 */
function calculate_campaign_total($product_id) {
    // Implementation
}
```

### README Files

Every project/plugin should have:

```markdown
# Project Name

Brief description (1-2 sentences)

## Features
- Feature 1
- Feature 2

## Installation
Step-by-step instructions

## Usage
Code examples

## Configuration
Settings and options

## Troubleshooting
Common issues and solutions

## Changelog
Version history
```

### Inline Documentation

```javascript
// ✅ Document complex logic
/**
 * Extract campaign name from bracketed format
 * Example: "Help John (Medical Fund)" → "Medical Fund"
 */
const match = name.match(/\((.*?)\)/);
const campaignName = match ? match[1] : name;
```

---

## 9. Error Handling

### Error Handling Philosophy

1. **Fail Fast** - Detect errors early
2. **Fail Gracefully** - Don't crash the app
3. **Log Everything** - Record for debugging
4. **User-Friendly Messages** - No technical jargon to users

### Implementation Patterns

#### Try-Catch Blocks

```javascript
// ✅ GOOD: Specific error handling
try {
    const data = JSON.parse(input);
    processData(data);
} catch (error) {
    if (error instanceof SyntaxError) {
        logError('Invalid JSON format', { input, error });
        showUserError('The file format is invalid. Please check and try again.');
    } else {
        logError('Unexpected error processing data', { error });
        showUserError('An unexpected error occurred. Please try again.');
    }
}
```

#### Validation Before Processing

```php
// ✅ Validate early, fail fast
function process_campaign($campaign_id, $amount) {
    // Validate inputs
    if (!$campaign_id || $campaign_id <= 0) {
        log_error('Invalid campaign ID', ['id' => $campaign_id]);
        return new WP_Error('invalid_id', 'Invalid campaign ID');
    }
    
    if (!$amount || $amount <= 0) {
        log_error('Invalid amount', ['amount' => $amount]);
        return new WP_Error('invalid_amount', 'Amount must be positive');
    }
    
    // Verify campaign exists
    $campaign = get_post($campaign_id);
    if (!$campaign) {
        log_error('Campaign not found', ['id' => $campaign_id]);
        return new WP_Error('not_found', 'Campaign not found');
    }
    
    // Process...
}
```

### Logging Standards

```php
/**
 * Log levels:
 * - ERROR: Something failed
 * - WARNING: Something unexpected but handled
 * - INFO: Normal operation milestone
 * - DEBUG: Detailed diagnostic information
 */

// ✅ Structured logging with context
log_error('Failed to create order', [
    'campaign_id' => $campaign_id,
    'amount' => $amount,
    'user_id' => get_current_user_id(),
    'error' => $exception->getMessage()
]);

log_info('Campaign updated successfully', [
    'campaign_id' => $campaign_id,
    'old_amount' => $old_amount,
    'new_amount' => $new_amount,
    'order_id' => $order->get_id()
]);
```

---

## 10. Testing & Verification

### Testing Philosophy

1. **Test Before Committing** - Never commit untested code
2. **Test Edge Cases** - Not just happy path
3. **Test Integrations** - Verify dependencies work
4. **Test Rollback** - Ensure changes are reversible

### Manual Testing Checklist

For every change:

- [ ] Feature works as expected
- [ ] No console errors
- [ ] No PHP errors/warnings
- [ ] Existing features still work
- [ ] Mobile responsive (if UI)
- [ ] Cross-browser compatible (if web)
- [ ] Performance acceptable
- [ ] Security validated

### Verification Artifacts

After completing work, create:

1. **Walkthrough Document**
   - What was changed
   - Why it was changed
   - How to verify it works
   - Screenshots/recordings if applicable

2. **Testing Results**
   - Test cases executed
   - Results (pass/fail)
   - Any issues found
   - Resolution status

---

## Appendix A: Common Patterns

### WordPress Plugin Structure

```php
<?php
/**
 * Plugin Name: My Plugin
 * Description: Brief description
 * Version: 1.0.0
 * Author: Your Name
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('MY_PLUGIN_VERSION', '1.0.0');
define('MY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MY_PLUGIN_URL', plugin_dir_url(__FILE__));

// Enqueue assets
function my_plugin_enqueue_scripts() {
    wp_enqueue_style('my-plugin-style',
        MY_PLUGIN_URL . 'assets/css/style.css',
        array(),
        MY_PLUGIN_VERSION
    );
    
    wp_enqueue_script('my-plugin-script',
        MY_PLUGIN_URL . 'assets/js/script.js',
        array('jquery'),
        MY_PLUGIN_VERSION,
        true
    );
    
    wp_localize_script('my-plugin-script', 'myPluginData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('my_plugin_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'my_plugin_enqueue_scripts');

// AJAX handler
function my_plugin_ajax_handler() {
    check_ajax_referer('my_plugin_nonce', 'security');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permission denied');
    }
    
    // Process request
    wp_send_json_success($data);
}
add_action('wp_ajax_my_plugin_action', 'my_plugin_ajax_handler');
```

### React Component Pattern

```typescript
import React, { useState, useEffect } from 'react';

interface Props {
    userId: number;
    onUpdate?: (data: UserData) => void;
}

/**
 * UserProfile Component
 * 
 * Displays user profile information with edit capability
 */
export const UserProfile: React.FC<Props> = ({ userId, onUpdate }) => {
    const [user, setUser] = useState<UserData | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    
    useEffect(() => {
        fetchUserData();
    }, [userId]);
    
    const fetchUserData = async () => {
        try {
            setLoading(true);
            const response = await api.getUser(userId);
            setUser(response.data);
            setError(null);
        } catch (err) {
            setError('Failed to load user data');
            console.error('Error fetching user:', err);
        } finally {
            setLoading(false);
        }
    };
    
    if (loading) return <LoadingSpinner />;
    if (error) return <ErrorMessage message={error} />;
    if (!user) return null;
    
    return (
        <div className="user-profile">
            {/* Component JSX */}
        </div>
    );
};
```

---

## Appendix B: Tools & Commands

### Useful Analysis Commands

```powershell
# File count and size
Get-ChildItem -Recurse -File | Measure-Object -Property Length -Sum

# Find files by extension
Get-ChildItem -Recurse -Include *.php,*.js

# Search for pattern in files
Select-String -Path "*.php" -Pattern "function_name"

# Check for security issues
Select-String -Path "*.php" -Pattern "(eval\(|base64_decode|exec\()"
```

### Git Best Practices

```bash
# Commit message format
git commit -m "feat: Add user authentication

- Implement JWT-based auth
- Add login/logout endpoints
- Create auth middleware

Fixes #123"

# Branch naming
feature/user-authentication
bugfix/login-error
hotfix/security-patch
refactor/code-cleanup
```

---

## Summary Checklist

Before completing any task, verify:

### Critical Rules (from .deven-rules.txt)

- [ ] **NO REGRESSION** - No existing functionality broken
- [ ] **MINIMAL CODE EDITING** - Only changed what was necessary
- [ ] **DIAGNOSIS FIRST** - Analyzed situation before providing solution
- [ ] **EXISTING FILES CHECKED** - Verified files exist before assuming
- [ ] **ANALYZED END-TO-END** - Reviewed codebase by user flow and role
- [ ] **SURROUNDING CODE REVIEWED** - Checked tests and configuration
- [ ] **DRY-RUN VALIDATED** - Simulated fixes before applying
- [ ] **SELF-VERIFIED** - Validated every change and file operation
- [ ] **TESTS RUN** - Ran tests, linters, type-checkers, and build commands
- [ ] **OUTDATED REFERENCES CHECKED** - Verified audit logs and references
- [ ] **NEVER HALLUCINATED** - All information verified, nothing invented

### Standard Checks

- [ ] User rules followed
- [ ] Code is secure (ABSPATH, nonces, sanitization)
- [ ] Code is organized (external JS/CSS, proper structure)
- [ ] Code is documented (comments, PHPDoc, README)
- [ ] Errors are handled gracefully
- [ ] Changes are tested
- [ ] Walkthrough created
- [ ] User notified with concise summary

### Communication & Quality

- [ ] All communication in English
- [ ] Libraries/frameworks verified via imports/config
- [ ] Existing style, formatting, naming maintained
- [ ] "One function, one task" principle followed
- [ ] Accessibility, usability, responsive layouts ensured
- [ ] Only reverted own changes if they caused errors (or if requested)

---

## Appendix C: Dev Server Management

### Port Management (CRITICAL)

**KILL ALL PORTS FIRST BEFORE RESTARTING THE DEV SERVER.**

```powershell
# Find and kill processes on specific ports
# For Node.js/React apps (port 3000)
npx kill-port 3000

# For PHP built-in server (port 8000)
npx kill-port 8000

# For Vite (port 5173)
npx kill-port 5173

# Alternative: Find process using port and kill
Get-Process -Id (Get-NetTCPConnection -LocalPort 3000).OwningProcess | Stop-Process
```

### Common Dev Server Commands

```bash
# PHP built-in server
php -S localhost:8000

# Node.js/React
npm start
npm run dev

# Vite
npm run dev

# Laravel
php artisan serve

# Django
python manage.py runserver
```

---

**Remember:** This guide is a framework, not rigid rules. Adapt based on project needs and user preferences, but always maintain security, quality, and clear communication.

**CRITICAL REMINDERS:**

- **NO REGRESSION!** - Never break existing functionality
- **DIAGNOSIS FIRST!** - Always analyze before solving
- **MINIMAL CHANGES!** - Edit only what's necessary
- **VERIFY EVERYTHING!** - Self-check every step
- **KILL PORTS FIRST!** - Before restarting dev servers
