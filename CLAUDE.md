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
| Account Management | ✅ Full | ⚠️ Team | ❌ | ❌ |
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
if ($role !== 'Executive') {
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

**Version:** 2.0 | **Updated:** 2025-10-13 | **DB:** 48 tables | **PHP:** 7.4+ | **Stack:** XAMPP/LAMP
