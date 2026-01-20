# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

Sales Management System - Project guidance for Claude Code (Updated: 2025-10-13)

---

## Project Overview

**PHP-based enterprise sales platform** with RBAC featuring:
- CRM & Project Management
- IT Service Ticket System (ITIL)
- Financial tracking
- Multi-team support
- Task & document collaboration

**Stack:** PHP 7.4+, MySQL/MariaDB, AdminLTE 3, Bootstrap 4, jQuery

---

## Quick Setup

```bash
composer install
mysql -u root -p sales_db < config/sales_db.sql
cp .env.example .env  # Configure DB_*, SECRET_KEY, ENCRYPTION_IV, BASE_URL
```

**Core Files:**
- `config/condb.php` - DB connection (PDO), encryption functions (encryptUserId/decryptUserId)
- `config/validation.php` - Input validation/sanitization, rate limiting, CSRF protection
- `config/env_loader.php` - Environment variable loader (loadEnv, getEnvVar)
- `include/Add_session.php` - Session check (MUST be first include on protected pages)
- `.env` - Environment config (SECRET_KEY, ENCRYPTION_IV, DB_*, BASE_URL) - NOT in repo

**Session Architecture:**
All protected pages follow a standard include pattern that establishes security and user context:
1. `Add_session.php` - Validates session, redirects if not authenticated
2. Sets global variables: `$role`, `$team_id`, `$user_id` from `$_SESSION`
3. Establishes permission flags: `$can_view_all`, `$can_view_team`, `$can_view_own`, `$can_view_financial`

---

## Database Schema (48 Tables)

### Key Tables

**Users & Teams:**
- `users` - User accounts (UUID PK, bcrypt passwords, 4 roles)
- `teams` - Department structure
- `user_teams` - Many-to-many mapping with primary flag
- `user_creation_logs` - Audit trail

**Customers & Products:**
- `customers` - Customer database
- `employees` - Employee records (bilingual)
- `products` - Product catalog with supplier/team
- `product_documents`, `product_images` - Product assets
- `suppliers` - Supplier management

**Projects:**
- `projects` - Main records with financial fields (sale_no_vat, cost_no_vat, gross_profit, potential)
- `project_customers` - M:M mapping
- `project_costs` - Cost breakdown
- `project_cost_summary` - Aggregated totals
- `project_payments` - Payment tracking
- `project_documents`, `project_images`, `document_links` - Project assets

**Project Team & Tasks:**
- `project_roles`, `project_members` - Team assignments
- `project_tasks` - Hierarchical tasks (status, progress, priority)
- `project_task_assignments` - Assignees
- `task_comments` - Comments & activity logs
- `task_comment_attachments`, `task_mentions`
- `project_discussions` - Chat-like board
- `project_discussion_attachments`, `project_discussion_mentions`

**Service Tickets:**
- `service_tickets` - ITIL tickets (auto TCK-YYYYMM-NNNN, SLA tracking)
- `service_ticket_*` - Comments, attachments, history, notifications, watchers, timeline, onsite
- `category`, `category_image` - 3-tier KB hierarchy

**Views:** `vw_service_tickets_alert`, `vw_service_tickets_full`, `vw_task_comments`

---

## RBAC - Role Hierarchy

### 1. Executive (Highest)
- **Access:** ALL data, all teams
- **Permissions:** Full system, user/team management, financial data
- **Team Switcher:** Default ALL, can filter to specific team

### 2. Sale Supervisor
- **Access:** Team data only
- **Permissions:** Manage team projects/finances, limited user mgmt
- **Team Switcher:** Can switch between assigned teams or ALL

### 3. Seller
- **Access:** Own data + shared projects
- **Permissions:** Create customers/projects, view own finances
- **Restrictions:** No team/user management

### 4. Engineer
- **Access:** Assigned projects/tasks only
- **Permissions:** Update tasks, service tickets, comments
- **CRITICAL:** **NO ACCESS to financial data** (sale_no_vat, cost_no_vat, gross_profit, potential)

### Permission Matrix

| Feature | Executive | Supervisor | Seller | Engineer |
|---------|:---------:|:----------:|:------:|:--------:|
| Account Management | ✅ Full | ⚠️ Team | ⚠️ Own | ❌ **BLOCKED** |
| View Projects | ✅ All | ⚠️ Team | ⚠️ Own | ⚠️ Assigned |
| **Financial Data** | ✅ All | ⚠️ Team | ⚠️ Own | ❌ **NONE** |
| Customer Mgmt | ✅ All | ⚠️ Team | ⚠️ Own | ❌ |
| Team Mgmt | ✅ | ❌ | ❌ | ❌ |
| Service Tickets | ✅ All | ⚠️ Team | ⚠️ Own | ⚠️ Assigned |

---

## Core Features

### Authentication & Session

**Login Flow (login.php):**
- Rate limiting: 5 attempts/15min, progressive blocking (15s → 30s → 45s → 60s)
- Validation: username 3-50 chars (alphanumeric + ._@-), password 6+ chars
- Password: `password_verify()` with bcrypt
- Session init: Query user_teams → set team_id ('ALL' if >1 team, single team UUID if 1 team)
- Multi-team support: Users can belong to multiple teams via `user_teams` junction table

**Session Variables:**
```php
$_SESSION['user_id']      // UUID (CHAR(36))
$_SESSION['username']     // Username string
$_SESSION['role']         // Executive|Sale Supervisor|Seller|Engineer
$_SESSION['team_id']      // Active team UUID or 'ALL' (for multi-team users)
$_SESSION['team_ids']     // Array of all team UUIDs user belongs to
$_SESSION['user_teams']   // Full team data array (team_id, team_name, is_primary)
$_SESSION['first_name']   // User first name
$_SESSION['last_name']    // User last name
$_SESSION['profile_image']// Profile image path
$_SESSION['csrf_token']   // CSRF protection token (when generated)
```

**Team Switching (switch_team.php):**
- POST endpoint with `team_id` param
- Validates membership, updates session, triggers reload

### Dashboard (index.php)

**Metrics (Role-Based):**
- Team stats, product count, project count
- **Financial (hidden for Engineers):** Sales, costs, gross profit, profit %
- Date range filtering, team/user filters

### Project Management (pages/project/)

**Lifecycle:**
1. **Creation** - Details, customer, product, seller, finances
2. **Cost Management (tab_cost/)** - Add items, auto-update summary
3. **Payments (tab_payment/)** - Track payments vs project value
4. **Documents (tab_document/)** - Multi-file upload, categorization
5. **Images (tab_image/)** - Gallery with lightbox
6. **Links (tab_linkdocument/)** - External docs (Drive, SharePoint)
7. **Team (project_member/)** - Assign users with roles
8. **Tasks (management/)**
   - Hierarchical structure, drag-drop, status/progress
   - Comments with attachments, @mentions
   - File uploads, activity logs
9. **Discussion (discussion/)** - Chat board, 5 files/msg (10MB), auto-refresh

**Financial Calc:**
```php
$sale_vat = $sale_no_vat * (1 + $vat_rate);
$cost_no_vat = SUM(project_costs.total_price);
$gross_profit = $sale_no_vat - $cost_no_vat;
$potential = ($gross_profit / $sale_no_vat) * 100;

// CRITICAL: Hide for Engineers
$can_view_financial = ($role !== 'Engineer');
```

### Service Tickets (pages/service/)

**Features:**
- Auto ticket #: TCK-YYYYMM-NNNN
- Types: Incident, Service, Change
- SLA tracking: Green/Yellow/Red status
- 3-tier categorization, watchers, timeline
- Status workflow: Draft → New → On Process → ... → Closed

### Other Modules

- **Customers (pages/customer/)** - CRUD, bulk import, project history
- **Categories (pages/category/)** - 3-tier KB (problems, cases, resolve)
- **Employees (pages/setting/employees/)** - Bilingual records, bulk import
- **Products (pages/setting/product/)** - Catalog, docs, images
- **Teams (pages/setting/team/)** - Executive only
- **Accounts (pages/account/)** - User CRUD, team assignment, bulk import

---

## Security

### Critical Rules

1. **SQL Injection:** ALWAYS use PDO prepared statements
```php
$stmt = $condb->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute([':username' => $username]);
```

2. **XSS Prevention:** Escape all output
```php
echo escapeOutput($user_input); // htmlspecialchars ENT_QUOTES
```

3. **Password:** Bcrypt hashing
```php
$hash = password_hash($password, PASSWORD_DEFAULT);
password_verify($input, $hash);
```

4. **File Upload:** Validate type, size, sanitize filename
```php
validateUploadedFile($_FILES['file'], $allowed_types, $max_size);
sanitizeFilename($filename);
```

5. **CSRF:** Token functions exist (not universally implemented)
```php
generateCSRFToken();
validateCSRFToken($_POST['csrf_token']);
```

6. **Encryption:** AES-256-CBC for IDs
```php
encryptUserId($user_id);
decryptUserId($encrypted);
```

### Access Control

**Page-level:**
```php
// Example 1: Executive only
if ($role !== 'Executive') {
    header("Location: " . BASE_URL . "index.php");
    exit();
}

// Example 2: Multiple roles (exclude specific roles)
if (!in_array($role, ['Executive', 'Account Management', 'Sale Supervisor', 'Seller'])) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}
```

**Data-level:**
```php
if ($role === 'Executive') {
    // No filter if team_id === 'ALL'
} elseif ($role === 'Sale Supervisor') {
    $sql .= " WHERE seller IN (SELECT user_id FROM user_teams WHERE team_id = :team_id)";
} else {
    $sql .= " WHERE seller = :user_id";
}
```

---

## API Endpoints

### Auth
- `POST /login.php` - Authentication
- `POST /switch_team.php` - Team switching (params: team_id)

### Projects
- `GET /pages/project/management/get_tasks.php?project_id={id}`
- `GET /pages/project/management/get_task_comments.php?task_id={id}`
- `POST /pages/project/management/post_comment.php` - Add comment + files
- `POST /pages/project/discussion/post_discussion.php` - Post message + attachments
- `GET /pages/project/tab_cost/get_costs.php?project_id={id}`

**Response Format:**
```json
{
  "success": true/false,
  "message": "...",
  "data": {...}
}
```

---

## Development Commands

### Database Setup
```bash
# Import database schema
mysql -u root -p sales_db < config/sales_db.sql

# Or restore with custom credentials
mysql -u your_username -p your_database < config/sales_db.sql
```

### Composer Dependencies
```bash
# Install dependencies (PHPSpreadsheet for Excel import/export)
composer install

# Update dependencies
composer update
```

### Running in Development (XAMPP/LAMP)
```bash
# Ensure Apache and MySQL are running
# Access via: http://localhost/sales/
# Or via configured BASE_URL in .env
```

### Testing Database Connection
```bash
# Test MySQL connection
mysql -u root -p -e "SELECT VERSION();"

# Check if database exists
mysql -u root -p -e "SHOW DATABASES LIKE 'sales_db';"
```

---

## Common Tasks

### New Page Template

```php
<?php
include('../../include/Add_session.php');
include('../../config/condb.php');

// Role check
if ($role !== 'Executive') {
    header("Location: " . BASE_URL . "index.php");
    exit();
}

include('../../include/Header.php');
include('../../include/Navbar.php');
?>

<!-- Content here -->

<?php include('../../include/Footer.php'); ?>
```

### Generate UUID (for new records)

```php
// Standard UUID v4 generation
function generateUUID() {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // version 4
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // UUID variant
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

$new_id = generateUUID(); // Use for user_id, team_id, project_id, etc.
```

### Role-Based Query

```php
$sql = "SELECT * FROM projects WHERE 1=1";
$params = [];

if ($role === 'Executive') {
    if ($team_id !== 'ALL') {
        $sql .= " AND seller IN (SELECT user_id FROM user_teams WHERE team_id = :team_id)";
        $params[':team_id'] = $team_id;
    }
} elseif ($role === 'Sale Supervisor') {
    $sql .= " AND seller IN (SELECT user_id FROM user_teams WHERE team_id = :team_id)";
    $params[':team_id'] = $team_id;
} else {
    $sql .= " AND seller = :user_id";
    $params[':user_id'] = $user_id;
}

$stmt = $condb->prepare($sql);
$stmt->execute($params);
```

### File Upload

```php
$allowed = ['image/jpeg', 'image/png', 'application/pdf'];
$validation = validateUploadedFile($_FILES['file'], $allowed, 5242880);
if (!$validation['valid']) die(json_encode(['success' => false]));

$safe = sanitizeFilename($_FILES['file']['name']);
$unique = generateUUID() . '.' . pathinfo($safe, PATHINFO_EXTENSION);
$path = __DIR__ . '/../../uploads/documents/' . $unique;

if (move_uploaded_file($_FILES['file']['tmp_name'], $path)) {
    // Save to DB
}
```

### DataTables

```javascript
$('#table').DataTable({
    "responsive": true,
    "buttons": ["copy", "csv", "excel", "pdf", "print"],
    "pageLength": 25,
    "order": [[0, "desc"]]
});
```

### SweetAlert

```javascript
Swal.fire({
    title: 'Confirm?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes'
}).then((result) => {
    if (result.isConfirmed) {
        // Execute action
    }
});
```

### Select2 Dropdown Enhancement

```javascript
// Initialize Select2 for searchable dropdowns
$('.select2').select2({
    theme: 'bootstrap4',
    placeholder: 'Select an option',
    allowClear: true
});
```

---

## Frontend Libraries

**AdminLTE 3** - Main admin template framework
- Bootstrap 4 based
- Located in `/AdminLTE/` directory
- Custom theme modifications in `/assets/css/`

**Key JavaScript Libraries:**
- **jQuery 3.x** - DOM manipulation, AJAX
- **DataTables** - Table enhancement with sorting, filtering, export (located in `/assets/plugins/datatables`)
- **Select2** - Enhanced select boxes with search (located in `/assets/plugins/select2`)
- **SweetAlert2** - Modern alert/modal dialogs
- **Chart.js** - Dashboard charts and graphs
- **Moment.js** - Date/time manipulation
- **DateRangePicker** - Date range selection for filters
- **Ekko Lightbox** - Image gallery lightbox

**CSS Frameworks:**
- **Bootstrap 4.6** - Grid, components, utilities
- **Font Awesome 6** - Icon library
- **Google Fonts** - Poppins font family

---

## File Structure (Simplified)

```
/sales/
├── config/          # condb.php, validation.php, sales_db.sql, env_loader.php
├── include/         # Header, Footer, Navbar, Add_session
├── pages/
│   ├── account/     # User management
│   ├── customer/    # CRM
│   ├── project/     # Projects + tabs (cost, payment, document, image, linkdocument,
│   │                #   project_member, management, discussion, report)
│   ├── service/     # Service tickets
│   ├── category/    # Service categories
│   ├── setting/     # employees, product, suppliers, team
│   ├── profile/     # User profile
│   └── inventory, pipline, claims/
├── assets/          # css, js, img, plugins (datatables, select2, sweetalert2, etc.)
├── uploads/         # profile_images, product_*, project_*, task_*, discussion_*, ticket_*
├── AdminLTE/        # Theme assets
├── vendor/          # Composer deps
├── .env             # Environment config (SECRET_KEY, DB_*, BASE_URL)
├── index.php        # Dashboard
├── login.php, logout.php, switch_team.php
└── CLAUDE.md, README.md
```

---

## Critical Notes

### 🚨 CRITICAL: Engineer Financial Data Restrictions
Engineers must **NEVER** see or access financial data. This is enforced at multiple levels:

**Backend Data Filtering:**
```php
// At query level - exclude financial columns for Engineers
if ($role === 'Engineer') {
    $sql = "SELECT project_id, project_name, status, created_at
            FROM projects"; // NO financial fields
} else {
    $sql = "SELECT project_id, project_name, status, created_at,
            sale_no_vat, cost_no_vat, gross_profit, potential
            FROM projects";
}
```

**Frontend Display Control:**
```php
// In index.php and all project pages
$can_view_financial = ($role !== 'Engineer');

// Hide UI elements
<?php if ($can_view_financial): ?>
    <div class="info-box">
        <span>Sale: <?php echo number_format($sale_no_vat, 2); ?></span>
    </div>
<?php endif; ?>
```

**Protected Fields List:**
- `sale_no_vat` - Sale amount without VAT
- `sale_vat` - Sale amount with VAT
- `cost_no_vat` - Cost amount without VAT
- `gross_profit` - Calculated profit (sale - cost)
- `potential` - Profit margin percentage
- `project_costs.*` - All cost breakdown data
- `project_payments.*` - Payment tracking data

### Team Filtering
```php
// Respect active team
$team_id = $_SESSION['team_id']; // UUID or 'ALL'
```

### Security Checklist
- ✅ Use PDO prepared statements (NEVER concatenate SQL)
- ✅ Escape output with `escapeOutput()`
- ✅ Validate files before upload
- ✅ Check `$_SESSION['role']` for auth (NOT `$_POST`)
- ✅ Log errors, show generic messages to users

---

## TODO (Security Enhancements)

**High Priority:**
1. Universal CSRF tokens
2. Security headers (CSP, X-Frame-Options, HSTS)
3. Password policy (complexity, history, expiration)
4. HTTPS enforcement
5. Session timeout
6. Comprehensive audit logging

**Medium:** 2FA, email notifications, RESTful API, WebSocket

**Low:** PWA, dark mode, i18n, advanced search

---

## Troubleshooting

- **Login fails:** Check rate limit, credentials, session cookies
- **Team switcher hidden:** User needs >1 team in user_teams
- **Engineer sees financial:** BUG! Check `$role !== 'Engineer'` condition
- **Engineer accesses restricted pages:** Check role permission array in `!in_array($role, [...])` - Engineer must be EXCLUDED
- **Upload fails:** Verify size (<5MB), type whitelist, permissions (755/644)
- **DB connection:** Check .env, MySQL service, test with `mysql -u root -p`

---

## Glossary

- **RBAC** - Role-Based Access Control
- **UUID** - CHAR(36) identifier
- **SLA** - Service Level Agreement
- **CSRF** - Cross-Site Request Forgery
- **XSS** - Cross-Site Scripting
- **PDO** - PHP Data Objects

---

## Important Architecture Notes

### Multi-file UUID Function Pattern
The `generateUUID()` function is **not centralized** - it's duplicated across many files. When creating new records, either:
1. Copy the function from an existing file (e.g., `pages/setting/team/add_team.php:46-52`)
2. Or define it locally in your file

```php
function generateUUID() {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
```

### Database Character Set
- Connection uses `utf8mb4` with `utf8mb4_unicode_ci` collation for full emoji support
- Set via: `SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci` in `condb.php:24`

### File Upload Limits
- Default max: 5MB (5242880 bytes)
- Configured per-endpoint via `validateUploadedFile($file, $types, $size)`
- Discussion attachments: Max 5 files/message, 10MB each
- MIME type validation enforced (not just extension checking)

### AJAX Response Pattern
All AJAX endpoints return consistent JSON structure:
```php
header('Content-Type: application/json');
echo json_encode([
    'success' => true|false,
    'message' => 'User-friendly message',
    'data' => [...], // Optional payload
    'error' => '...' // Optional error details
]);
```

### Validation Functions Available
From `config/validation.php`:
- `sanitizeInput($data)` - Strip whitespace, slashes
- `escapeOutput($data)` - XSS protection via htmlspecialchars
- `validateEmail($email)` - Format + length
- `validatePhone($phone)` - 9-15 digits
- `validatePassword($pass)` - 6-255 chars
- `validateText($text, $min, $max, $name)` - General text
- `validateNumber($num, $min, $max, $name)` - Numeric
- `validateUsername($user)` - 3-50 chars, alphanumeric+._@-
- `validateUploadedFile($file, $types, $size)` - Complete file validation
- `checkRateLimit($id, $max, $window)` - Progressive rate limiting
- `generateCSRFToken()` / `validateCSRFToken($token)` - CSRF protection
- `sanitizeFilename($name)` - Safe filename (alphanumeric+._-, max 100 chars)

---

## Security Fix Log

### 2025-10-16: RBAC Access Control Fix (Account & Customer Management)
**Issue:** Users could bypass RBAC by accessing pages via direct URL with encrypted IDs

**Account Management Files Fixed:**
1. **`pages/account/edit_account.php`** (Line 13)
   - **Problem:** Included 'Engineer' in allowed roles array
   - **Fix:** Removed 'Engineer' from `!in_array($role, [...])` check
   - **Before:** `['Executive', 'Account Management', 'Sale Supervisor', 'Seller', 'Engineer']`
   - **After:** `['Executive', 'Account Management', 'Sale Supervisor', 'Seller']`

2. **`pages/account/add_account.php`** (Line 15)
   - **Problem:** Included 'Engineer' and 'Seller' in allowed roles
   - **Fix:** Removed both 'Engineer' and 'Seller' (Sellers shouldn't create accounts)
   - **Before:** `['Executive', 'Account Management', 'Sale Supervisor', 'Seller']`
   - **After:** `['Executive', 'Account Management', 'Sale Supervisor']`

3. **`pages/account/view_account.php`** (Line 111-119)
   - **Problem:** Missing explicit Seller and Engineer role checks
   - **Fix:** Added explicit checks for Seller (own data only) and Engineer (blocked)
   - **Impact:** Seller can view own profile, Engineer completely blocked

**Already Protected:**
- `pages/account/account.php` - Line 6 blocks non-authorized roles

**Customer Management Files Fixed:**
4. **`pages/customer/view_customer.php`** (Line 1-99)
   - **Problem:** NO access control - anyone logged in could view any customer
   - **Fix:** Added complete RBAC validation matching `customer.php` logic
   - **Impact:**
     - Executive: View all (or filtered by team switcher)
     - Account Mgmt/Supervisor: View team customers only
     - Seller/Engineer: View own created customers only

5. **`pages/customer/edit_customer.php`** (Line 20-108)
   - **Problem:** NO access control - anyone could edit any customer
   - **Fix:** Added complete RBAC validation before allowing edit
   - **Impact:** Same access rules as view, prevents unauthorized modifications

**RBAC Compliance:** ✅ All account management pages now properly enforce role restrictions

**Testing Checklist:**
```bash
# Account Management - As Engineer role:
1. Access account.php → Redirect to index.php
2. Access add_account.php → Redirect to index.php
3. Access edit_account.php?user_id=X → Redirect to index.php
4. Access view_account.php?id=X → Error message + redirect to account.php

# Account Management - As Seller role:
1. Access account.php → Redirect to index.php
2. Access add_account.php → Redirect to index.php
3. Access edit_account.php?user_id=own → Can edit own profile
4. Access edit_account.php?user_id=other → Error message + redirect
5. Access view_account.php?id=own → Can view own profile
6. Access view_account.php?id=other → Error message + redirect

# Customer Management - As Engineer role:
1. Access view_customer.php?id=own_customer → Can view
2. Access view_customer.php?id=other_customer → Error + redirect to customer.php
3. Access edit_customer.php?customer_id=own → Can edit
4. Access edit_customer.php?customer_id=other → Error + redirect to customer.php

# Customer Management - As Seller role:
1. Access view_customer.php?id=own_customer → Can view
2. Access view_customer.php?id=other_customer → Error + redirect to customer.php
3. Access edit_customer.php?customer_id=own → Can edit
4. Access edit_customer.php?customer_id=other → Error + redirect to customer.php

# Customer Management - As Sale Supervisor:
1. Access view_customer.php?id=team_customer → Can view
2. Access view_customer.php?id=other_team_customer → Error + redirect
3. Access edit_customer.php?customer_id=team → Can edit
4. Access edit_customer.php?customer_id=other_team → Error + redirect
```

### 2025-10-16: Service Ticket Date/Time Fields Not Saving Fix
**Issue:** Date/time fields "กำหนดเริ่มดำเนินการ (วันเวลา)" and "กำหนดแล้วเสร็จ (วันเวลา)" in service ticket creation form were not saving to database

**Root Cause:**
- Form displays dates using Tempus Dominus datetimepicker with Thai format: `DD/MM/YYYY HH:mm`
- MySQL columns `start_at` and `due_at` expect format: `YYYY-MM-DD HH:mm:ss`
- API endpoint received dates but didn't convert format before database insertion

**Files Fixed:**
1. **`pages/service/api/create_ticket.php`** (Lines 74-91)
   - **Problem:** Received `$_POST['start_at']` and `$_POST['due_at']` in Thai format (DD/MM/YYYY HH:mm) and passed directly to database without conversion
   - **Fix:** Added PHP DateTime conversion logic after receiving POST data
   - **Code Added:**
   ```php
   // แปลงรูปแบบวันที่จาก DD/MM/YYYY HH:mm เป็น YYYY-MM-DD HH:mm:ss สำหรับ MySQL
   if ($start_at) {
       $dt = DateTime::createFromFormat('d/m/Y H:i', $start_at);
       if ($dt !== false) {
           $start_at = $dt->format('Y-m-d H:i:s');
       } else {
           $start_at = null; // ถ้าแปลงไม่ได้ให้เป็น null
       }
   }

   if ($due_at) {
       $dt = DateTime::createFromFormat('d/m/Y H:i', $due_at);
       if ($dt !== false) {
           $due_at = $dt->format('Y-m-d H:i:s');
       } else {
           $due_at = null; // ถ้าแปลงไม่ได้ให้เป็น null
       }
   }
   ```
   - **Impact:** Dates now properly convert from display format to MySQL DATETIME format before insertion

2. **`pages/service/api/update_ticket.php`** (Lines 124-138)
   - **Problem:** Used `strtotime()` to parse date/time, which cannot correctly handle Thai format `DD/MM/YYYY HH:mm`
   - **Fix:** Changed to use `DateTime::createFromFormat()` with fallback to `strtotime()` for backward compatibility
   - **Code Modified:**
   ```php
   if (in_array($field, ['start_at', 'due_at'], true)) {
       if ($value === '') {
           $value = null;
       } else {
           // แปลงรูปแบบวันที่จาก DD/MM/YYYY HH:mm เป็น YYYY-MM-DD HH:mm:ss สำหรับ MySQL
           $dt = DateTime::createFromFormat('d/m/Y H:i', $value);
           if ($dt !== false) {
               $value = $dt->format('Y-m-d H:i:s');
           } else {
               // ถ้าแปลงไม่ได้ ลองใช้ strtotime เผื่อเป็นรูปแบบอื่น
               $timestamp = strtotime($value);
               $value = $timestamp ? date('Y-m-d H:i:s', $timestamp) : null;
           }
       }
   }
   ```
   - **Impact:** Both creating and editing tickets now properly save date/time fields

**Related Files (unchanged):**
- `pages/service/add_account.php` - Create ticket form with datetimepicker using format `DD/MM/YYYY HH:mm` (Lines 655-680, 858-879)
- `pages/service/edit_ticket.php` - Edit ticket form with datetimepicker using format `DD/MM/YYYY HH:mm` (Lines 672-696, 892-912)
- Database columns: `service_tickets.start_at`, `service_tickets.due_at` (type: DATETIME)

**Date Format Pattern:**
- **User Input (Display):** `DD/MM/YYYY HH:mm` (e.g., `25/10/2025 14:30`)
- **PHP DateTime Parse:** `d/m/Y H:i`
- **MySQL Storage:** `YYYY-MM-DD HH:mm:ss` (e.g., `2025-10-25 14:30:00`)

### 2025-10-16: Service Ticket Onsite Travel Mode Display Fix
**Issue:** In `view_ticket.php`, the "ยานพาหนะ" (Vehicle) field under "รายละเอียดการเดินทาง Onsite" section displayed raw database values (e.g., `personal_car`) instead of user-friendly Thai labels

**Root Cause:**
- The system stored travel mode as English keys (`personal_car`, `company_car`, etc.) in database
- Display directly showed these values without translation to Thai labels

**File Fixed:**
1. **`pages/service/view_ticket.php`** (Lines 136-153, 991)
   - **Problem:** Displayed `$onsite['travel_mode']` raw value (e.g., `personal_car`)
   - **Fix:** Added `getTravelModeLabel()` function to map database values to Thai labels
   - **Function Added:**
   ```php
   function getTravelModeLabel($mode) {
       $labels = [
           'personal_car' => 'รถส่วนตัว',
           'company_car' => 'รถบริษัท',
           'taxi' => 'แท็กซี่ / รถรับจ้าง',
           'electric_train' => 'รถไฟฟ้า (BTS/MRT)',
           'bus' => 'รถโดยสารประจำทาง',
           'van' => 'รถตู้โดยสาร',
           'train' => 'รถไฟ',
           'boat' => 'เรือโดยสาร',
           'plane' => 'เครื่องบิน',
           'others_mileage' => 'อื่นๆ (ต้องบันทึกเลขไมล์)',
           'others' => 'อื่นๆ (ไม่ต้องบันทึกเลขไมล์)'
       ];
       return $labels[$mode] ?? $mode;
   }
   ```
   - **Display Changed From:** `<?php echo htmlspecialchars($onsite['travel_mode'] ?? '-'); ?>`
   - **Display Changed To:** `<?php echo htmlspecialchars(getTravelModeLabel($onsite['travel_mode'] ?? '')); ?>`
   - **Impact:** Users now see readable Thai labels instead of technical database values

**Travel Mode Mapping:**
| Database Value | Thai Label |
|----------------|------------|
| personal_car | รถส่วนตัว |
| company_car | รถบริษัท |
| taxi | แท็กซี่ / รถรับจ้าง |
| electric_train | รถไฟฟ้า (BTS/MRT) |
| bus | รถโดยสารประจำทาง |
| van | รถตู้โดยสาร |
| train | รถไฟ |
| boat | เรือโดยสาร |
| plane | เครื่องบิน |
| others_mileage | อื่นๆ (ต้องบันทึกเลขไมล์) |
| others | อื่นๆ (ไม่ต้องบันทึกเลขไมล์) |

---

### 2025-10-17: Service Ticket Activity Log Relative Time Fix
**Issue:** เวลาใน Activity Log & Comments ค้างอยู่ที่ `เมื่อสักครู่` แม้เวลาจะผ่านไป เนื่องจากค่าจากฐานข้อมูลถูกตีความว่าเป็นอนาคต (timezone mismatch)

**File Fixed:**
1. **`pages/service/api/get_ticket_feed.php`**
   - แก้ฟังก์ชัน `timeAgo()` ให้ใช้ `DateTimeImmutable` พร้อมตั้งค่า `Asia/Bangkok` และ fallback เป็นเวลาจริง (`d/m/Y H:i`) เมื่อตรวจพบว่าค่า `created_at` อยู่ในอนาคต เพื่อไม่ให้ผลลัพธ์ค้างเป็น `เมื่อสักครู่`

**Impact:** เวลาแสดงแบบ relative จะอัปเดตถูกต้อง และถ้าเวลาที่ดึงมาอยู่ในอนาคตจะแสดงเวลาเต็มแทนการแปลเป็น “เมื่อสักครู่”

---

### 2025-10-17: Service Management Metric Filter & Reset
**Issue:** การ์ดสถานะบนหน้า Service Management ไม่ได้ผูกกับตารางผลลัพธ์ และไม่มีปุ่มรีเซ็ตตัวกรอง (ต้องการ default = Job Owner เป็นผู้ใช้ปัจจุบัน)

**Files Updated:**
1. **`pages/service/service.php`**
   - เพิ่มตัวแปร `$filterSlaStatus` และ hidden input `sla_status` เพื่อให้การ์ด “Overdue SLA” กรองตาม `sla_status`
   - กำหนดเมทริกการ์ดให้มี `data-filter` พร้อม CSS/JS รองรับการคลิกหรือกดคีย์บอร์ด แล้ว submit ฟอร์มด้วยสถานะที่เลือก (ยังคง Job Owner เดิม)
   - ใส่ปุ่ม `รีเซ็ต` ในฟอร์มค้นหา เรียกใช้ JS เพื่อคืนค่าตัวกรองทั้งหมดเป็นค่าเริ่มต้น (Job Owner = ผู้ใช้งานระบบ, ฟิลด์อื่นว่าง)
   - ไฮไลต์การ์ดที่ถูกเลือกอยู่ปัจจุบันเพื่อให้เห็นสถานะการกรองชัดเจน
2. **`pages/service/service2.php`**
   - เพิ่มตัวแปร/ฟิลด์ `sla_status` และ metadata ให้การ์ดในมุมมอง classic ทำงานเหมือนหน้า modern
   - เพิ่มปุ่ม `รีเซ็ต` และสคริปต์บังคับให้ตัวกรองกลับค่า default พร้อมรองรับการคลิกการ์ดเพื่อกรองด้วยสถานะ/SLA

**Impact:** ผู้ใช้คลิกการ์ดเพื่อกรองสถานะได้ทันที ตาราง Service Ticket Overview จะตามสถานะที่เลือก และสามารถรีเซ็ตกลับสู่มุมมองค่าเริ่มต้นได้รวดเร็ว

---

### 2025-10-18: Service Ticket Overview Responsive Layout
**Issue:** ตาราง Service Ticket Overview เลยออกนอกกรอบเมื่อย่อหน้าจอ (mid / small viewport) ทำให้ใช้งานลำบากบนโน้ตบุ๊ก/แท็บเล็ต

**File Updated:**
1. **`pages/service/service.php`**
   - ครอบตารางด้วย `.service-table-wrapper` เพื่อให้ Bootstrap/Custom CSS จัดการ overflow ได้เนียนตา และเปิดใช้ momentum scroll บนอุปกรณ์สัมผัส
   - เพิ่ม `data-title` ให้ `<td>` แต่ละคอลัมน์ พร้อม media query ลดความกว้าง `colgroup` สำหรับหน้าจอ < 1400px
   - สร้าง mobile card layout (≤ 992px): ซ่อน `<thead>`, ปรับ `<tr>` เป็นบล็อกโค้งมน, ลด padding, ตัดเส้นขอบซ้ำซ้อน และปรับ `ticket-actions` ให้เป็นพื้นหลังอ่อนอ่านง่าย
   - ปรับ `details-grid` ในคอลัมน์รายละเอียด ให้จัดเป็น auto-fit grid ที่ยืดหยุ่นตามความกว้างหน้าจอเล็ก
   - ปรับแต่ง breakpoint ≤ 992px เพิ่มเติม: คลาย `white-space` ของป้ายสถานะให้ห่อคำ, จัด header ticket เป็นคอลัมน์ และเพิ่ม margin/padding ใหม่ให้โซน `SLA & Action` มีกรอบมนพร้อมพื้นหลังอ่อน ไม่ชิดขอบ

**Impact:** มุมมอง Service Ticket ยืดหยุ่นกับทุกขนาดหน้าจอ ตัดการ overflow และอ่านง่ายขึ้นบนมือถือ/แท็บเล็ต โดยยังคงการทำงานของ DataTables (ค้นหา/จัดหน้า) ครบถ้วน

---

**Version:** 2.0.3 | **Updated:** 2025-10-18 | **DB:** 48 tables | **PHP:** 7.4+ | **Stack:** XAMPP/LAMP
