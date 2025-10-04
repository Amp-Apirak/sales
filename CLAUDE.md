# CLAUDE.md

This file provides comprehensive guidance to Claude Code when working with this sales management system. Last updated: 2025-10-04.

---

## Project Overview

**Sales Management System** - A comprehensive PHP-based enterprise sales and project management platform with role-based access control (RBAC), featuring:
- Customer Relationship Management (CRM)
- Project & Task Management with team collaboration
- IT Service Ticket System (ITIL-aligned)
- Financial tracking (sales, costs, profit analysis)
- Multi-team support with team switching
- Document & file management
- Real-time collaboration features

**Business Domain:** B2B sales operations with project-based delivery, support services, and team-based organizational structure.

---

## Development Setup

### Dependencies
- PHP 7.4+ with PDO MySQL extension
- MySQL/MariaDB database (InnoDB engine)
- Composer for dependency management
- Web server (Apache/Nginx) with mod_rewrite
- PHP extensions: openssl, fileinfo, mbstring

### Installation Commands
```bash
# Install PHP dependencies
composer install

# Import database schema (2979 lines)
mysql -u root -p sales_db < config/sales_db.sql

# Set up environment variables
cp .env.example .env
# Edit .env with your database credentials and encryption keys
```

### Database Configuration
Database connection settings in [config/condb.php](config/condb.php):
- Database name: `sales_db`
- Default dev credentials: root/1234 (change in production!)
- PDO with UTF-8 charset, exception mode
- Environment variables via `.env` file

### Environment Variables (.env)
Required variables:
```
DB_HOST=localhost
DB_NAME=sales_db
DB_USERNAME=root
DB_PASSWORD=your_password
SECRET_KEY=your_encryption_secret_key
ENCRYPTION_IV=16_character_iv
BASE_URL=http://localhost/sales/
```

---

## Complete Database Schema

### Users & Authentication

**`users`** - User accounts
- `user_id` (CHAR(36) PK) - UUID identifier
- `username` (VARCHAR(255)) - Login username (unique)
- `password` (VARCHAR(255)) - Bcrypt hashed password
- `email` (VARCHAR(255)) - Email address
- `role` (ENUM) - 'Executive', 'Sale Supervisor', 'Seller', 'Engineer'
- `first_name`, `last_name` (VARCHAR(255)) - Name fields
- `position`, `company`, `phone` (VARCHAR) - Contact info
- `profile_image` (VARCHAR(255)) - Profile picture path
- Audit: `created_at`, `created_by`

**`teams`** - Department/team structure
- `team_id` (CHAR(36) PK) - UUID
- `team_name` (VARCHAR(255)) - Team name
- `team_description` (MEDIUMTEXT) - Description
- `team_leader` (CHAR(36) FK→users) - Team leader reference
- Audit: `created_at`, `created_by`, `updated_at`, `updated_by`

**`user_teams`** - User-team many-to-many mapping
- `user_id` (CHAR(36) FK→users, PK)
- `team_id` (CHAR(36) FK→teams, PK)
- `is_primary` (TINYINT(1)) - Primary team flag
- Composite PK: (user_id, team_id)
- Supports multi-team membership

**`user_creation_logs`** - Audit trail for user actions
- `id` (CHAR(36) PK)
- `user_id` (CHAR(36)) - Actor
- `action_type` (VARCHAR(50)) - Action performed
- `action_details` (TEXT) - Detailed description
- `ip_address` (VARCHAR(45)), `user_agent` (TEXT) - Request info
- `created_at` (TIMESTAMP)

### Customers & Products

**`customers`** - Customer database
- `customer_id` (CHAR(36) PK)
- `customer_name`, `company` (VARCHAR(255))
- `position` (VARCHAR(255)) - Job title
- `address` (MEDIUMTEXT) - Full address
- `phone`, `office_phone`, `extension`, `email` (VARCHAR)
- `customers_image` (VARCHAR(255)) - Company logo
- `remark` (MEDIUMTEXT) - Notes
- Audit fields

**`employees`** - Employee records
- `id` (CHAR(36) PK)
- `first_name_th`, `last_name_th` (VARCHAR(255)) - Thai name
- `first_name_en`, `last_name_en` (VARCHAR(255)) - English name
- `nickname_th`, `nickname_en` (VARCHAR(50))
- `gender` (VARCHAR(10)), `birth_date` (DATE)
- `personal_email`, `company_email` (VARCHAR(255))
- `phone` (VARCHAR(20))
- `position`, `department` (VARCHAR)
- `team_id` (CHAR(36)) - Team reference
- `supervisor_id` (CHAR(36)) - Reporting manager
- `hire_date` (DATE)
- `profile_image` (VARCHAR(255))
- Audit fields

**`products`** - Product catalog
- `product_id` (CHAR(36) PK)
- `product_name` (VARCHAR(255))
- `product_description` (MEDIUMTEXT)
- `unit` (VARCHAR(50)) - Unit of measure
- `cost_price`, `selling_price` (DECIMAL(15,2))
- `supplier_id` (CHAR(36) FK→suppliers)
- `team_id` (CHAR(36) FK→teams) - Team ownership
- `main_image` (VARCHAR(255))
- Audit fields

**`product_documents`** - Product documentation
- `id` (CHAR(36) PK)
- `product_id` (CHAR(36) FK→products)
- `document_type` (ENUM) - 'presentation', 'specification', 'manual', 'other'
- `file_path`, `file_name`, `file_size` (VARCHAR/INT)
- Audit fields

**`product_images`** - Product image gallery
- `id`, `product_id`, `image_path`, audit fields

**`suppliers`** - Supplier management
- `supplier_id` (CHAR(36) PK)
- `supplier_name`, `contact_person` (VARCHAR(255))
- `phone`, `email`, `address` (VARCHAR/TEXT)
- Audit fields

### Projects & Financial Tracking

**`projects`** - Main project records
- `project_id` (CHAR(36) PK)
- `project_name` (VARCHAR(255))
- `start_date`, `end_date`, `sales_date` (DATE)
- `status` (VARCHAR(50))
- `contract_no` (VARCHAR(50))
- **Financial fields:**
  - `sale_no_vat`, `sale_vat` (DECIMAL(10,2)) - Sales amounts
  - `cost_no_vat`, `cost_vat` (DECIMAL(10,2)) - Cost amounts
  - `gross_profit` (DECIMAL(10,2)) - Calculated profit
  - `potential` (DECIMAL(5,2)) - Profit percentage
  - `es_sale_no_vat`, `es_cost_no_vat`, `es_gp_no_vat` (DECIMAL(10,2)) - Estimates
  - `vat` (DECIMAL(5,2)) - VAT rate
- **References:**
  - `customer_id` (CHAR(36) FK→customers)
  - `seller` (CHAR(36) FK→users) - Sales person
  - `product_id` (CHAR(36) FK→products)
- `remark` (TEXT)
- Audit fields

**`project_customers`** - Project-customer many-to-many
- `id` (CHAR(36) PK)
- `project_id`, `customer_id` - FKs

**`project_costs`** - Detailed cost breakdown
- `cost_id` (CHAR(36) PK)
- `project_id` (CHAR(36) FK→projects)
- `cost_category` (VARCHAR(100))
- `cost_description` (TEXT)
- `quantity`, `unit_price`, `total_price` (DECIMAL)
- `cost_date` (DATE)
- Audit fields

**`project_cost_summary`** - Aggregated costs
- `project_id` (CHAR(36) PK FK→projects)
- `total_cost` (DECIMAL(15,2))
- `total_items` (INT)
- `last_updated` (TIMESTAMP)

**`project_payments`** - Payment tracking
- `payment_id` (CHAR(36) PK)
- `project_id` (CHAR(36) FK→projects)
- `payment_date` (DATE)
- `amount` (DECIMAL(10,2))
- `payment_method`, `reference_no` (VARCHAR)
- `remark` (TEXT)
- Audit fields

**`project_documents`** - Project file storage
- `document_id` (CHAR(36) PK)
- `project_id`, `document_type`, `file_name`, `file_path`, `file_size` (BIGINT)
- `uploaded_by` (CHAR(36) FK→users)
- `uploaded_at` (TIMESTAMP)

**`project_images`** - Project image gallery
- `image_id`, `project_id`, `image_path`, `uploaded_by`, `uploaded_at`

**`document_links`** - External document links
- `id` (CHAR(36) PK)
- `project_id`, `link_url` (VARCHAR(500)), `link_title`, `description`
- `created_by`, `created_at`

### Project Team & Tasks

**`project_roles`** - Project role definitions
- `role_id` (CHAR(36) PK)
- `role_name` (VARCHAR(100)) - e.g., 'Project Manager', 'Developer'
- `role_description` (TEXT)
- `permissions` (JSON) - Role permissions
- Audit fields

**`project_members`** - Project team assignments
- `member_id` (CHAR(36) PK)
- `project_id` (CHAR(36) FK→projects)
- `user_id` (CHAR(36) FK→users)
- `role_id` (CHAR(36) FK→project_roles)
- `is_active` (TINYINT(1))
- `joined_date`, `left_date` (TIMESTAMP)
- `remark` (TEXT)
- Audit fields

**`project_tasks`** - Task management
- `task_id` (CHAR(36) PK)
- `project_id` (CHAR(36) FK→projects)
- `parent_task_id` (CHAR(36) FK→project_tasks) - For subtasks
- `task_name` (VARCHAR(255))
- `description` (TEXT)
- `start_date`, `end_date` (DATE)
- `status` (ENUM) - 'Pending', 'In Progress', 'Completed', 'Cancelled'
- `progress` (DECIMAL(5,2)) - Progress percentage (0-100)
- `priority` (ENUM) - 'Low', 'Medium', 'High', 'Urgent'
- `task_order` (INT) - Display order
- `task_level` (INT) - Hierarchy depth
- Audit fields

**`project_task_assignments`** - Task assignees
- `assignment_id` (CHAR(36) PK)
- `task_id` (CHAR(36) FK→project_tasks)
- `user_id` (CHAR(36) FK→users)
- `assigned_at`, `assigned_by`

**`task_comments`** - Task activity & comments
- `comment_id` (CHAR(36) PK)
- `task_id` (CHAR(36) FK→project_tasks)
- `project_id` (CHAR(36) FK→projects)
- `user_id` (CHAR(36) FK→users)
- `comment_text` (TEXT)
- `comment_type` (ENUM) - 'comment', 'status_change', 'progress_update', 'file_upload'
- `old_value`, `new_value` (VARCHAR(255)) - For change tracking
- `is_edited`, `is_deleted` (TINYINT(1)) - Edit/soft delete flags
- `created_at`, `updated_at`

**`task_comment_attachments`** - File attachments on comments
- `attachment_id` (CHAR(36) PK)
- `comment_id` (CHAR(36) FK→task_comments)
- `task_id` (CHAR(36) FK→project_tasks)
- `file_name`, `file_path`, `file_size` (BIGINT), `file_extension`
- `uploaded_by`, `uploaded_at`

**`task_mentions`** - User mentions in comments
- `mention_id` (CHAR(36) PK)
- `comment_id` (CHAR(36) FK→task_comments)
- `task_id` (CHAR(36) FK→project_tasks)
- `mentioned_user_id` (CHAR(36) FK→users)
- `created_at`

### Service Ticketing System

**`service_tickets`** - IT service ticket system
- `ticket_id` (CHAR(36) PK)
- `ticket_no` (VARCHAR(50)) - Auto-generated: TCK-YYYYMM-NNNN
- `project_id` (CHAR(36) FK→projects)
- `ticket_type` (ENUM) - 'Incident', 'Service', 'Change'
- `subject` (VARCHAR(150))
- `description` (TEXT)
- `status` (ENUM) - 'Draft', 'New', 'On Process', 'Pending', 'Waiting for Approval', 'Scheduled', 'Resolved', 'Resolved Pending', 'Containment', 'Closed', 'Canceled'
- `priority` (ENUM) - 'Critical', 'High', 'Medium', 'Low'
- `urgency` (ENUM) - 'High', 'Medium', 'Low'
- `impact` (VARCHAR(100))
- **Categorization:**
  - `service_category`, `category`, `sub_category` (VARCHAR(255))
- **Assignments:**
  - `job_owner` (CHAR(36) FK→users) - Assignee
  - `reporter` (CHAR(36) FK→users) - Reporter
  - `source` (VARCHAR(100)) - Reporting channel
- **SLA Management:**
  - `sla_target` (INT) - Target hours
  - `sla_deadline` (DATETIME) - Auto-calculated deadline
  - `sla_status` (ENUM) - 'Within SLA', 'Near SLA', 'Overdue'
- **Timeline:**
  - `start_at`, `due_at`, `resolved_at`, `closed_at` (DATETIME)
- `channel` (ENUM) - 'Onsite', 'Remote', 'Office'
- `deleted_at` (DATETIME) - Soft delete
- Audit fields

**`service_ticket_attachments`** - Ticket file attachments
- `attachment_id`, `ticket_id`, file details, `uploaded_by`, `uploaded_at`

**`service_ticket_comments`** - Ticket comments
- `comment_id`, `ticket_id`, `comment_text`
- `is_internal` (TINYINT(1)) - Internal note flag
- `created_by`, `created_at`

**`service_ticket_history`** - Ticket change history
- `history_id`, `ticket_id`, `field_changed`, `old_value`, `new_value`
- `changed_by`, `changed_at`

**`service_ticket_notifications`** - Notification queue
- `notification_id`, `ticket_id`, `user_id`, `notification_type`
- `is_read` (TINYINT(1))
- `sent_at`

**`service_ticket_onsite`** - Onsite service details
- `onsite_id`, `ticket_id`, `location` (TEXT)
- `scheduled_date`, `actual_date` (DATETIME)
- `notes` (TEXT)

**`service_ticket_timeline`** - Timeline events
- `timeline_id`, `ticket_id`, `event_type`, `event_description`, `event_time`
- `created_by`

**`service_ticket_watchers`** - Ticket watchers
- `watcher_id`, `ticket_id`, `user_id`
- `added_by`, `added_at`

### Service Categories

**`category`** - Service category hierarchy
- `id` (CHAR(36) PK)
- `service_category`, `category`, `sub_category` (VARCHAR(255)) - 3-tier hierarchy
- `problems`, `cases`, `resolve` (TEXT) - Knowledge base
- `image_id` (CHAR(36))
- `created_by`, audit fields
- Trigger: `before_insert_category` - Auto UUID generation

**`category_image`** - Category images
- `id`, `category_id`, file details, `created_by`, audit fields
- Trigger: `before_insert_category_image` - Auto UUID

### Views

**`vw_service_tickets_alert`** - Service alert aggregation view
**`vw_service_tickets_full`** - Full ticket details with joins
**`vw_task_comments`** - Aggregated task activity view

---

## User Roles & Permissions (RBAC)

### Role Hierarchy

#### 1. Executive (Highest Authority)
**Permissions:**
- Full system access
- View ALL data across all teams
- Manage users and teams
- Access all financial data
- Override any permissions
- Team switching (view specific team or ALL teams)

**Data Visibility:**
- All projects, customers, employees
- All financial metrics (sales, costs, profits)
- All teams and members
- Complete system analytics

**Access Control Logic:**
```sql
-- Default: See everything
WHERE 1=1
-- If team selected via switcher:
WHERE seller IN (SELECT user_id FROM user_teams WHERE team_id = :selected_team)
```

#### 2. Sale Supervisor
**Permissions:**
- View and manage team data
- Create/edit projects for team members
- Access financial data for team projects
- Team switching if member of multiple teams
- Limited user management (team level)

**Data Visibility:**
- Projects from assigned teams
- Team members' customers
- Team financial metrics
- Own and shared projects

**Restrictions:**
- Cannot access other teams' data (unless member)
- No global user management

**Access Control Logic:**
```sql
-- Single team:
WHERE seller IN (SELECT user_id FROM user_teams WHERE team_id = :team_id)
-- Multiple teams (ALL mode):
WHERE seller IN (SELECT user_id FROM user_teams WHERE team_id IN (:team_ids))
```

#### 3. Seller
**Permissions:**
- View and manage own data
- Create customers and projects
- View own financial data
- Access shared projects (via project_members)

**Data Visibility:**
- Own projects and customers only
- Projects shared with them
- Own sales metrics
- Team information (read-only)

**Restrictions:**
- Cannot view other sellers' data
- No team management
- No user management

**Access Control Logic:**
```sql
WHERE seller = :user_id
OR project_id IN (SELECT project_id FROM project_members WHERE user_id = :user_id)
```

#### 4. Engineer
**Permissions:**
- View assigned projects and tasks
- Access service tickets
- Update task progress
- Add comments and files

**Data Visibility:**
- Assigned projects and tasks only
- Technical documentation
- Service tickets assigned to them
- **NO ACCESS to financial data**

**Restrictions:**
- **Cannot view sales, costs, or profit data**
- Cannot view other engineers' work
- Limited project visibility
- No customer management
- No financial reports

**Access Control Logic:**
```sql
WHERE project_id IN (
    SELECT project_id FROM project_members WHERE user_id = :user_id
)
-- Financial data hidden in UI layer
```

### Permission Matrix

| Feature | Executive | Sale Supervisor | Seller | Engineer |
|---------|:---------:|:---------------:|:------:|:--------:|
| **Account Management** | ✅ Full | ⚠️ Team Only | ❌ No | ❌ No |
| **View All Projects** | ✅ Yes | ⚠️ Team | ⚠️ Own | ⚠️ Assigned |
| **Financial Data** | ✅ All | ⚠️ Team | ⚠️ Own | ❌ **None** |
| **Customer Management** | ✅ All | ⚠️ Team | ⚠️ Own | ❌ No |
| **Team Management** | ✅ Yes | ❌ No | ❌ No | ❌ No |
| **Product Management** | ✅ Yes | ✅ Yes | ✅ Yes | ❌ No |
| **Service Tickets** | ✅ All | ⚠️ Team | ⚠️ Own | ⚠️ Assigned |
| **Project Tasks** | ✅ All | ⚠️ Team | ⚠️ Own/Shared | ⚠️ Assigned |
| **Reports/Analytics** | ✅ All | ⚠️ Team | ⚠️ Own | ⚠️ Limited |
| **User Management** | ✅ Yes | ❌ No | ❌ No | ❌ No |

### Team-Based Access Control

**Multi-Team Membership:**
- Users can belong to multiple teams via `user_teams` table
- One team designated as primary (`is_primary = 1`)
- Team switcher in navbar for users with >1 team
- "ALL" mode shows combined data from all user's teams

**Team Switcher Behavior:**
- Executive: Default = ALL teams, can filter to specific team
- Sale Supervisor: Can switch between assigned teams or view ALL
- Seller/Engineer: Single team (no switcher if only 1 team)

**Auto-Reset Feature:**
- JavaScript listener resets to 'ALL' when navigating between modules
- Preserves selection within same module
- Opt-out: Add `data-no-reset-team="true"` to navigation links

---

## Core Features & Workflows

### 1. Authentication & Session Management

**Login Flow ([login.php](login.php)):**
1. User enters username/password
2. **Rate limiting:** 5 attempts per 15 minutes
3. **Progressive blocking:** 1min → 3min → 5min → 7min → 9min → 15min
4. Input validation (username: 3-50 alphanumeric, password: 6+ chars)
5. Database query: `SELECT * FROM users WHERE username = :username`
6. Password verification: `password_verify($input, $hash_from_db)`
7. On success:
   - Query user's teams: `SELECT * FROM user_teams WHERE user_id = :user_id`
   - Determine active team:
     - 1 team: Set that team as active
     - >1 teams: Set 'ALL' mode
   - Initialize session with user data
8. Redirect to dashboard with SweetAlert welcome

**Session Variables:**
```php
$_SESSION['user_id']      // User UUID
$_SESSION['username']     // Login username
$_SESSION['role']         // User role (enum)
$_SESSION['first_name']   // User first name
$_SESSION['last_name']    // User last name
$_SESSION['profile_image']// Profile picture path
$_SESSION['team_id']      // Active team UUID or 'ALL'
$_SESSION['team_name']    // Active team name or 'All Teams'
$_SESSION['team_ids']     // Array of all team UUIDs
$_SESSION['user_teams']   // Full team data array
```

**Session Validation ([include/Add_session.php](include/Add_session.php)):**
- Included on every protected page
- Checks for `role`, `team_id`, `user_id` in session
- Auto-redirects to login if not authenticated
- Extracts session vars into PHP variables

**Team Switching ([switch_team.php](switch_team.php)):**
- AJAX endpoint: `POST /switch_team.php`
- Parameters: `team_id` (UUID or 'ALL')
- Validates team membership
- Updates session: `$_SESSION['team_id']`, `$_SESSION['team_name']`
- Returns JSON success/error
- Triggers page reload to refresh data

**Logout ([logout.php](logout.php)):**
```php
session_start();
session_unset();
session_destroy();
// SweetAlert success → redirect to login
```

### 2. Dashboard & Analytics ([index.php](index.php))

**Metrics Display (Role-Based):**
- **Team Statistics:** Total teams, team members (filtered by role)
- **Product Count:** Total products in system
- **Project Statistics:**
  - Total projects (filtered by date range and role)
  - Date range picker (default: current year)
  - Custom date filtering
- **Financial Metrics (Hidden for Engineers):**
  - Total Sales (excluding VAT)
  - Total Costs (excluding VAT)
  - Gross Profit: `Sale - Cost`
  - Profit Percentage: `(Gross Profit / Sale) × 100`
  - Tooltips explain calculations

**Filtering:**
- Date range: Custom period selection
- Team filter (Executive only)
- User/Seller filter (Executive/Supervisor)
- Real-time AJAX updates

**Visual Elements:**
- Info boxes with color coding
- FontAwesome icons
- Responsive cards
- AdminLTE components

### 3. Project Management ([pages/project/](pages/project/))

**Project Lifecycle:**

1. **Creation ([project.php](pages/project/project.php)):**
   - Project details: name, dates, contract number
   - Customer selection/quick-add
   - Product selection
   - Seller assignment (auto for non-Executive)
   - Financial estimates: sale/cost/profit
   - Status setting
   - File upload support

2. **Cost Management ([tab_cost/](pages/project/tab_cost/)):**
   - Add cost items by category
   - Quantity × Unit Price = Total
   - Auto-updates `project_cost_summary`
   - Cost breakdown reports
   - Cost history tracking
   - Print/export capability

3. **Payment Tracking ([tab_payment/](pages/project/tab_payment/)):**
   - Record payments with dates
   - Payment methods (Cash, Transfer, Check, etc.)
   - Reference numbers
   - Payment history
   - Total paid vs project value

4. **Document Management ([tab_document/](pages/project/tab_document/)):**
   - Multi-file upload
   - Document categorization
   - File preview/download
   - Upload audit trail (who, when)
   - Supported formats: PDF, Office docs, images

5. **Image Gallery ([tab_image/](pages/project/tab_image/)):**
   - Multiple image upload
   - Thumbnail display
   - Lightbox viewer
   - Image metadata

6. **External Links ([tab_linkdocument/](pages/project/tab_linkdocument/)):**
   - Link external documents (Google Drive, SharePoint, etc.)
   - Link title and description
   - Quick access buttons

7. **Project Team ([project_member/](pages/project/project_member/)):**
   - Add users to project
   - Assign project roles (from `project_roles` table)
   - Active/inactive status
   - Join/leave dates
   - Project sharing mechanism

8. **Task Management ([management/](pages/project/management/)):**
   - **Task Board ([project_management.php](pages/project/management/project_management.php)):**
     - Hierarchical task structure (parent/subtasks)
     - Drag-and-drop reordering
     - Task status workflow
     - Progress tracking (0-100%)
     - Priority levels with color coding

   - **Task Details ([task_detail.php](pages/project/management/task_detail.php)):**
     - Full task information
     - Assignee management
     - Timeline (start/end dates)
     - Description with rich text
     - Comment system with real-time loading

   - **Comments & Activity ([get_task_comments.php](pages/project/management/get_task_comments.php)):**
     - User comments with avatars
     - System activity logs (status changes, progress updates)
     - File attachments with icons
     - @mentions support
     - Edit/delete own comments
     - Timestamp with "time ago" display
     - HTML rendering for formatted comments

   - **File Attachments:**
     - Upload files with comments
     - File type icons (PDF, Excel, Word, Image, etc.)
     - File size display
     - Secure download with access control
     - Multiple files per comment

**Financial Calculations:**
```php
// Sales
$sale_no_vat = [base amount]
$sale_vat = $sale_no_vat * (1 + $vat_rate)

// Costs
$cost_no_vat = SUM(project_costs.total_price)
$cost_vat = $cost_no_vat * (1 + $vat_rate)

// Profit
$gross_profit = $sale_no_vat - $cost_no_vat
$potential = ($gross_profit / $sale_no_vat) * 100

// Engineer Role: Hide ALL financial fields in UI
```

**Search & Filters:**
- Search: Project name, customer name
- Filter: Product, status, creator, customer, year, team
- Role-based data filtering
- Team switcher integration
- Export: Excel, PDF, Print

### 4. Customer Management ([pages/customer/](pages/customer/))

**Customer Features:**
- **CRUD Operations:**
  - Create customer records
  - Edit customer information
  - View customer details with project history
  - Delete customers (check for project dependencies)

- **Customer Information:**
  - Customer name and company
  - Position/job title
  - Full address
  - Phone, office phone, extension
  - Email
  - Company logo/image
  - Team association
  - Remarks/notes

- **Customer View ([view_customer.php](pages/customer/view_customer.php)):**
  - Customer details
  - Related projects list
  - Project count and value
  - Recent activity

- **Bulk Operations:**
  - Excel import with template
  - Batch customer creation
  - Data validation on import

- **Access Control:**
  - Executive: All customers
  - Sale Supervisor: Team customers
  - Seller: Own customers
  - Engineer: No access

### 5. Service Ticket System ([pages/service/](pages/service/))

**Ticket Dashboard ([service.php](pages/service/service.php)):**
- **Metrics Cards:**
  - Total tickets
  - By status: New, On Process, Pending, Resolved, Closed, Cancelled
  - SLA overdue count (red alert)
  - Color-coded status indicators

**Ticket Management:**
- **Creation:**
  - Auto-generate ticket number: `TCK-YYYYMM-NNNN`
  - Select ticket type: Incident, Service, Change
  - Subject and detailed description
  - Priority: Critical, High, Medium, Low
  - Urgency: High, Medium, Low
  - Impact assessment
  - 3-tier categorization: Service Category → Category → Sub-Category
  - Source tracking (Email, Call Center, Portal, Phone, etc.)

- **SLA Management:**
  - Set SLA target (hours)
  - Auto-calculate deadline: `start_time + sla_target hours`
  - Real-time SLA status:
    - Within SLA: Green
    - Near SLA: Yellow (< 20% remaining)
    - Overdue: Red
  - SLA breach alerts

- **Assignment & Ownership:**
  - Job Owner: Responsible person (required)
  - Reporter: Who reported the issue
  - Watchers: Additional users to notify
  - Related project linkage

- **Status Workflow:**
  ```
  Draft → New → On Process → Pending →
  Waiting for Approval → Scheduled → Resolved →
  Resolved Pending → Closed

  Can be Canceled at any stage
  ```

- **Timeline Tracking:**
  - Start time
  - Due time
  - Resolved time
  - Closed time
  - All changes logged to history

- **Service Channel:**
  - Onsite: Schedule location and visit
  - Remote: Remote support session
  - Office: In-office service

- **Supporting Features:**
  - File attachments
  - Comment system (public and internal notes)
  - Change history log
  - Notifications to stakeholders
  - Timeline events
  - Watchers management

### 6. Service Categories ([pages/category/](pages/category/))

**Category Structure:**
- 3-tier hierarchy:
  1. Service Category (top level)
  2. Category (middle level)
  3. Sub-Category (detailed level)

**Knowledge Base Fields:**
- **Problems:** Common problems for this category
- **Cases:** Use case examples
- **Resolve:** Standard resolution steps
- **Images:** Visual guides and diagrams

**Usage:**
- Used in service ticket creation for categorization
- Helps standardize ticket routing
- Knowledge base for support staff
- Training resource for engineers

**Access:**
- Main listing: Engineer role only ([category.php](pages/category/category.php))
- Management: Admin access ([index.php](pages/category/index.php))

### 7. Employee Management ([pages/setting/employees/](pages/setting/employees/))

**Employee Records:**
- Bilingual names (Thai and English)
- Nicknames in both languages
- Gender and birth date
- Contact: Personal email, company email, phone
- Organization: Position, department, team
- Supervisor hierarchy
- Hire date
- Profile image
- Address

**Features:**
- Employee listing with search/filter
- Detail view
- Bulk import from Excel
- Template generation
- Duplicate checking (name, email)
- Integration with user accounts

### 8. Product Management ([pages/setting/product/](pages/setting/product/))

**Product Catalog:**
- Product name and description
- Unit of measure
- Cost price and selling price
- Supplier association
- Team ownership
- Main product image

**Product Documentation:**
- Document types:
  - Presentation (sales materials)
  - Specification (technical specs)
  - Manual (user guides)
  - Other
- Multi-file support
- File categorization
- Version tracking

**Product Images:**
- Main image
- Additional images (gallery)
- Image management

**Features:**
- Product CRUD operations
- Search and filter
- Document upload/management
- Image gallery
- Supplier linking

### 9. Team Management ([pages/setting/team/](pages/setting/team/))

**Team Structure:**
- Team name and description
- Team leader assignment
- Team member management
- Primary team designation

**Features:**
- Create/edit teams
- Assign team leaders
- Manage team membership
- Track team projects and metrics

**Access:**
- Executive only
- Critical for organizational structure

### 10. User Account Management ([pages/account/](pages/account/))

**User Management:**
- **CRUD Operations:**
  - Create users with role assignment
  - Edit user details
  - View user profiles
  - Deactivate/activate users

- **User Information:**
  - Username (unique, 3-50 chars)
  - Password (hashed with bcrypt)
  - Email
  - Name: First and last
  - Position and company
  - Phone
  - Profile image
  - Role assignment

- **Team Assignment:**
  - Assign to multiple teams
  - Set primary team
  - Team membership management

- **Bulk Operations:**
  - Excel import
  - Template generation
  - Batch user creation

- **Search & Filter:**
  - Search: Name, username, email, phone
  - Filter: Company, team, role, position
  - Advanced dropdown filters

**Access Control:**
- Executive: Full access
- Sale Supervisor: View team members
- Others: Redirected to dashboard

---

## File Structure & Organization

```
/sales/
├── config/
│   ├── condb.php                   # Database connection, encryption functions
│   ├── sales_db.sql                # Complete schema (2979 lines, 48 tables)
│   ├── validation.php              # Input validation & sanitization
│   └── env_loader.php              # Environment variable loader
│
├── include/
│   ├── Header.php                  # HTML head, CSS includes, meta tags
│   ├── Footer.php                  # Scripts, closing tags
│   ├── Navbar.php                  # Top nav, sidebar, team switcher
│   └── Add_session.php             # Session check (required on all pages)
│
├── pages/
│   ├── account/                    # User management (Executive/Supervisor)
│   │   ├── account.php             # User listing with filters
│   │   ├── add_account.php         # Create user form
│   │   ├── edit_account.php        # Edit user
│   │   ├── view_account.php        # User profile view
│   │   ├── import_account.php      # Bulk Excel import
│   │   └── generate_template.php   # Excel template generator
│   │
│   ├── customer/                   # Customer/CRM module
│   │   ├── customer.php            # Customer listing
│   │   ├── add_customer.php        # Create customer
│   │   ├── edit_customer.php       # Edit customer
│   │   ├── view_customer.php       # Customer details + projects
│   │   ├── delete_customer.php     # Delete customer
│   │   └── import_customer.php     # Bulk import
│   │
│   ├── project/                    # Project management
│   │   ├── project.php             # Project listing (main)
│   │   ├── delete_project.php      # Delete project
│   │   ├── import_project.php      # Bulk import
│   │   ├── save_customer_ajax.php  # Quick customer add
│   │   │
│   │   ├── tab_cost/               # Cost management tab
│   │   │   ├── index.php           # Cost listing
│   │   │   ├── save_cost.php       # Add cost
│   │   │   ├── edit_cost.php       # Edit cost
│   │   │   ├── delete_cost.php     # Delete cost
│   │   │   ├── get_costs.php       # AJAX: Fetch costs
│   │   │   └── cost_viewprint.php  # Print cost report
│   │   │
│   │   ├── tab_payment/            # Payment tracking tab
│   │   │   ├── index.php           # Payment listing
│   │   │   ├── save_payment.php    # Record payment
│   │   │   └── delete_payment.php  # Delete payment
│   │   │
│   │   ├── tab_document/           # Document management tab
│   │   │   ├── index.php           # Document listing
│   │   │   ├── document.php        # Document viewer
│   │   │   ├── upload_document.php # Upload files
│   │   │   ├── view_document.php   # Preview/download
│   │   │   └── delete_document.php # Delete document
│   │   │
│   │   ├── tab_image/              # Image gallery tab
│   │   │   ├── index.php           # Image grid
│   │   │   ├── image.php           # Image viewer
│   │   │   ├── upload_images.php   # Upload images
│   │   │   └── delete_image.php    # Delete image
│   │   │
│   │   ├── tab_linkdocument/       # External links tab
│   │   │   ├── index.php           # Link listing
│   │   │   ├── save_document_link.php    # Add link
│   │   │   ├── get_document_links.php    # AJAX: Fetch links
│   │   │   ├── get_document_link_details.php # Link details
│   │   │   └── delete_document_link.php  # Delete link
│   │   │
│   │   ├── project_member/         # Project team tab
│   │   │   ├── index.php           # Member listing
│   │   │   ├── add_member.php      # Add team member
│   │   │   ├── edit_member.php     # Edit member role
│   │   │   └── delete_member.php   # Remove member
│   │   │
│   │   ├── management/             # Task management
│   │   │   ├── project_management.php    # Task board (main view)
│   │   │   ├── tab_management.php        # Task tab content
│   │   │   ├── task_detail.php           # Task detail page
│   │   │   ├── save_task.php             # Create task (POST)
│   │   │   ├── edit_task.php             # Edit task (form)
│   │   │   ├── update_task.php           # Update task (POST)
│   │   │   ├── delete_task.php           # Delete task
│   │   │   ├── get_tasks.php             # AJAX: Fetch task tree
│   │   │   ├── update_task_position.php  # Drag-drop reorder
│   │   │   ├── get_task_comments.php     # AJAX: Load comments/activity
│   │   │   ├── post_comment.php          # Add comment (POST)
│   │   │   ├── edit_comment.php          # Edit comment
│   │   │   ├── delete_comment.php        # Soft delete comment
│   │   │   ├── download_attachment.php   # Download task file
│   │   │   └── README_TASK_COMMENTS.md   # Task system docs
│   │   │
│   │   └── report/                 # Project reports
│   │       └── sale_price.php      # Sales price report
│   │
│   ├── service/                    # IT service tickets
│   │   ├── service.php             # Ticket dashboard & listing
│   │   └── index.php               # Module index
│   │
│   ├── category/                   # Service categories & KB
│   │   ├── category.php            # Category listing (Engineer)
│   │   ├── index.php               # Category management
│   │   ├── edit_category.php       # Edit category
│   │   ├── upload_image.php        # Upload category images
│   │   └── delete_image.php        # Delete images
│   │
│   ├── setting/                    # System settings
│   │   ├── employees/              # Employee management
│   │   │   ├── employees.php       # Employee listing
│   │   │   ├── view_employees.php  # Employee details
│   │   │   ├── import_employees.php# Bulk import
│   │   │   ├── generate_template.php # Excel template
│   │   │   └── check_duplicate.php # Duplicate validation
│   │   │
│   │   ├── product/                # Product catalog
│   │   │   ├── product.php         # Product listing
│   │   │   ├── add_product.php     # Create product
│   │   │   ├── edit_product.php    # Edit product
│   │   │   ├── delete_product.php  # Delete product
│   │   │   ├── upload_document.php # Product docs
│   │   │   └── delete_document.php # Delete doc
│   │   │
│   │   ├── suppliers/              # Supplier management
│   │   │   ├── supplier.php        # Supplier listing
│   │   │   ├── add_supplier.php    # Create supplier
│   │   │   ├── edit_supplier.php   # Edit supplier
│   │   │   └── import_supplier.php # Bulk import
│   │   │
│   │   └── team/                   # Team management (Executive)
│   │       ├── team.php            # Team listing
│   │       ├── add_team.php        # Create team
│   │       └── edit_team.php       # Edit team
│   │
│   ├── profile/                    # User profile
│   │   ├── profile.php             # Profile page
│   │   └── recover.php             # Password recovery
│   │
│   ├── inventory/                  # Inventory module
│   │   └── inventory.php           # Stock management
│   │
│   ├── pipline/                    # Sales pipeline
│   │   └── index.php               # Pipeline view
│   │
│   └── claims/                     # Claims management
│       ├── claims.php              # Claims listing
│       └── add_claim.php           # Create claim
│
├── assets/                         # Frontend assets
│   ├── css/                        # Custom stylesheets
│   ├── js/                         # Custom JavaScript
│   ├── img/                        # Images
│   └── plugins/                    # Third-party libraries
│       ├── datatables/             # DataTables
│       ├── select2/                # Select2
│       ├── sweetalert2/            # SweetAlert2
│       ├── fontawesome-free/       # Icons
│       ├── daterangepicker/        # Date picker
│       ├── summernote/             # WYSIWYG editor
│       └── toastr/                 # Toast notifications
│
├── uploads/                        # User uploads
│   ├── profile_images/             # User avatars
│   ├── product_images/             # Product photos
│   ├── product_documents/          # Product docs
│   ├── project_documents/          # Project files
│   ├── project_images/             # Project photos
│   ├── task_attachments/           # Task files
│   └── ticket_attachments/         # Service ticket files
│
├── AdminLTE/                       # AdminLTE theme
│   ├── dist/                       # Compiled assets
│   └── plugins/                    # AdminLTE plugins
│
├── vendor/                         # Composer dependencies
├── logs/                           # Application logs
│
├── .env                            # Environment config (NOT in repo)
├── .env.example                    # Example env file
├── .gitignore                      # Git ignore rules
├── composer.json                   # PHP dependencies
├── index.php                       # Dashboard (main page)
├── login.php                       # Login page
├── logout.php                      # Logout handler
├── switch_team.php                 # Team switching AJAX
├── css_dashboard.php               # Dashboard styles
├── 404.php                         # 404 error page
├── CLAUDE.md                       # This file
└── README.md                       # Project README
```

---

## API/AJAX Endpoints

### Authentication
- `POST /login.php` - User authentication
- `POST /logout.php` - Session destruction
- `POST /switch_team.php` - Team switching
  - Params: `team_id` (UUID or 'ALL')
  - Response: JSON success/error

### Project Management

**Task APIs:**
- `GET /pages/project/management/get_tasks.php?project_id={id}`
  - Returns: Task tree with assignments, status, progress

- `GET /pages/project/management/get_task_comments.php?task_id={id}`
  - Returns: HTML rendered comments with activity log

- `POST /pages/project/management/save_task.php`
  - Create task
  - Params: project_id, task_name, description, dates, priority, status, etc.

- `POST /pages/project/management/update_task.php`
  - Update task fields
  - Logs changes to task_comments

- `POST /pages/project/management/update_task_position.php`
  - Drag-and-drop reordering
  - Params: task_id, new_order, new_parent_id

- `POST /pages/project/management/post_comment.php`
  - Add comment with optional file attachments
  - Supports @mentions

- `POST /pages/project/management/edit_comment.php`
  - Edit comment text
  - Sets is_edited flag

- `DELETE /pages/project/management/delete_comment.php`
  - Soft delete (is_deleted = 1)

- `GET /pages/project/management/download_attachment.php?id={attachment_id}`
  - Secure file download with access check

**Project Data APIs:**
- `GET /pages/project/tab_cost/get_costs.php?project_id={id}`
  - Fetch cost breakdown

- `GET /pages/project/tab_cost/get_cost_details.php?cost_id={id}`
  - Cost item details for editing

- `POST /pages/project/tab_cost/save_cost.php`
  - Add cost item, updates summary

- `POST /pages/project/tab_payment/save_payment.php`
  - Record payment

- `GET /pages/project/tab_linkdocument/get_document_links.php?project_id={id}`
  - Fetch external links

- `POST /pages/project/tab_linkdocument/save_document_link.php`
  - Add external link

- `POST /pages/project/tab_document/upload_document.php`
  - Multi-file upload

- `POST /pages/project/tab_image/upload_images.php`
  - Image upload

- `POST /pages/project/save_customer_ajax.php`
  - Quick-add customer from project form

### Common Response Format
Most AJAX endpoints return JSON:
```json
{
  "success": true/false,
  "message": "Success or error message",
  "data": { ... },
  "errors": [ ... ]
}
```

Some endpoints (like `get_task_comments.php`) return rendered HTML for direct DOM injection.

---

## Security Implementation

### 1. SQL Injection Prevention
**All queries use PDO prepared statements:**
```php
// Named parameters (preferred)
$stmt = $condb->prepare("SELECT * FROM users WHERE username = :username");
$stmt->bindParam(':username', $username);
$stmt->execute();

// Positional parameters
$stmt = $condb->prepare("SELECT * FROM projects WHERE project_id = ?");
$stmt->execute([$project_id]);

// Never use string concatenation in SQL
```

### 2. Password Security
**Hashing:**
```php
// On registration/update
$hashed = password_hash($password, PASSWORD_DEFAULT); // bcrypt, cost 10

// On login
if (password_verify($input_password, $stored_hash)) {
    // Authenticated
}
```

### 3. Authentication Rate Limiting
**Rate Limit Implementation ([config/validation.php](config/validation.php)):**
```php
checkRateLimit($identifier, $max_attempts = 5, $time_window = 900);
// 5 attempts per 15 minutes (900 seconds)
// Progressive blocking: 1min → 3min → 5min → 7min → 9min → 15min
```

### 4. Input Validation & Sanitization
**Validation Functions ([config/validation.php](config/validation.php)):**
- `sanitizeInput($data)` - Trim, strip slashes
- `validateEmail($email)` - Filter + length check
- `validatePhone($phone)` - Numeric, 9-15 digits
- `validatePassword($password)` - 6-255 chars
- `validateUsername($username)` - 3-50 chars, alphanumeric + `_-`
- `validateText($text, $min, $max, $field)` - Length validation
- `validateNumber($number, $min, $max, $field)` - Numeric range
- `validateUploadedFile($file, $allowedTypes, $maxSize)` - File upload security
- `sanitizeFilename($filename)` - Prevent path traversal

**Always sanitize input, escape output:**
```php
// Input
$input = sanitizeInput($_POST['field']);

// Output
echo escapeOutput($data); // htmlspecialchars with ENT_QUOTES
```

### 5. XSS Prevention
**Output escaping:**
```php
// Function: escapeOutput($data)
htmlspecialchars($data, ENT_QUOTES, 'UTF-8')

// Use in views:
<p><?php echo escapeOutput($user_input); ?></p>
```

### 6. CSRF Protection
**Token functions ([config/validation.php](config/validation.php)):**
```php
// Generate token (on form display)
$csrf_token = generateCSRFToken(); // bin2hex(random_bytes(32))

// Validate token (on form submission)
if (!validateCSRFToken($_POST['csrf_token'])) {
    die('CSRF validation failed');
}
```

**Usage in forms:**
```html
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
    ...
</form>
```

### 7. File Upload Security
**Validation ([config/validation.php](config/validation.php)):**
```php
validateUploadedFile($file, $allowedTypes, $maxSize = 5242880);
// Checks:
// 1. Upload errors
// 2. File size (default 5MB)
// 3. Extension whitelist
// 4. MIME type verification (finfo_file)
// 5. Prevents file spoofing
```

**Filename sanitization:**
```php
sanitizeFilename($filename);
// - Removes special chars
// - Prevents path traversal (strips leading dots)
// - Limits to 100 chars
// - Preserves extension
```

**Upload directories:**
- All uploads in `/uploads/` directory
- Organized by type (profile_images, product_documents, etc.)
- Random/UUID filenames to prevent guessing
- Proper directory permissions (755 for dirs, 644 for files)

### 8. Session Security
**Configuration:**
```php
session_start();
// On login success: Regenerate session ID
session_regenerate_id(true);
```

**Session validation:**
- Check on every request ([include/Add_session.php](include/Add_session.php))
- Required variables: `user_id`, `role`, `team_id`
- Auto-redirect to login if missing
- Session timeout handling

### 9. Access Control Enforcement
**Page-level protection:**
```php
// Example: Account management (Executive/Supervisor only)
if (!isset($_SESSION['role']) ||
    ($_SESSION['role'] != 'Executive' && $_SESSION['role'] != 'Sale Supervisor')) {
    header("Location: " . BASE_URL . "index.php");
    exit();
}
```

**Data-level filtering:**
```php
// Query filtering based on role
if ($role === 'Executive') {
    // No filter - see all
} elseif ($role === 'Sale Supervisor') {
    $sql .= " WHERE seller IN (SELECT user_id FROM user_teams WHERE team_id = :team_id)";
} else { // Seller/Engineer
    $sql .= " WHERE seller = :user_id";
}
```

**Financial data protection:**
```php
// Engineers cannot see financial data
$can_view_financial = ($role !== 'Engineer');

if ($can_view_financial) {
    // Show sales, costs, profits
} else {
    // Hide completely
}
```

### 10. Encryption
**ID Encryption ([config/condb.php](config/condb.php)):**
```php
// Encrypt (for URLs)
function encryptUserId($user_id) {
    $secret_key = getEnvVar('SECRET_KEY', 'your_secret_key');
    $iv = getEnvVar('ENCRYPTION_IV', '1234567890123456');
    return base64_encode(
        openssl_encrypt($user_id, "aes-256-cbc", $secret_key, 0, $iv)
    );
}

// Decrypt
function decryptUserId($encrypted_user_id) {
    $secret_key = getEnvVar('SECRET_KEY', 'your_secret_key');
    $iv = getEnvVar('ENCRYPTION_IV', '1234567890123456');
    return openssl_decrypt(
        base64_decode($encrypted_user_id), "aes-256-cbc", $secret_key, 0, $iv
    );
}

// Usage: Obfuscate sensitive IDs in URLs
```

### 11. Environment Variables
**Loader ([config/env_loader.php](config/env_loader.php)):**
```php
loadEnv(__DIR__ . '/../.env'); // Parse .env file
$db_password = getEnvVar('DB_PASSWORD', 'default'); // Get with fallback
```

**Security:**
- `.env` file NOT in version control (in `.gitignore`)
- Contains sensitive credentials
- Fallback defaults for development only

### Security Recommendations (TODO)
1. **Implement CSRF tokens universally** (functions exist, not used everywhere)
2. **Add security headers:**
   - Content-Security-Policy
   - X-Frame-Options: DENY
   - X-Content-Type-Options: nosniff
   - Strict-Transport-Security (HTTPS)
3. **Stronger password policy:**
   - Complexity requirements (uppercase, lowercase, number, symbol)
   - Password history (prevent reuse)
   - Password expiration
4. **Two-Factor Authentication (2FA)** for Executive role
5. **HTTPS enforcement** in production
6. **Session timeout** after inactivity
7. **Audit logging** for sensitive operations
8. **Database backups** and encryption at rest
9. **Regular security audits** (OWASP Top 10)
10. **Input validation on client-side** (in addition to server-side)

---

## Common Development Tasks

### Adding a New Page

1. **Create PHP file** in appropriate `pages/[module]/` directory
2. **Include session check** at top:
   ```php
   <?php
   include('../../include/Add_session.php');
   include('../../config/condb.php');
   ?>
   ```
3. **Add role-based access control** if needed:
   ```php
   if ($role !== 'Executive') {
       header("Location: " . BASE_URL . "index.php");
       exit();
   }
   ```
4. **Include shared components:**
   ```php
   include('../../include/Header.php');
   include('../../include/Navbar.php');
   // Your content here
   include('../../include/Footer.php');
   ```
5. **Use BASE_URL** for paths:
   ```php
   <a href="<?php echo BASE_URL; ?>pages/customer/customer.php">Customers</a>
   ```

### Database Queries

**Always use prepared statements:**
```php
// SELECT
$stmt = $condb->prepare("SELECT * FROM projects WHERE project_id = :id");
$stmt->execute([':id' => $project_id]);
$project = $stmt->fetch();

// INSERT
$stmt = $condb->prepare("
    INSERT INTO customers (customer_id, customer_name, created_by, created_at)
    VALUES (:id, :name, :user, NOW())
");
$stmt->execute([
    ':id' => $uuid,
    ':name' => $customer_name,
    ':user' => $user_id
]);

// UPDATE
$stmt = $condb->prepare("UPDATE projects SET status = :status WHERE project_id = :id");
$stmt->execute([':status' => 'Completed', ':id' => $project_id]);

// DELETE
$stmt = $condb->prepare("DELETE FROM project_costs WHERE cost_id = :id");
$stmt->execute([':id' => $cost_id]);
```

**Connection available as:** `$condb` (after including `config/condb.php`)

### Implementing Role-Based Data Filtering

**Example: Projects listing**
```php
$sql = "SELECT * FROM projects WHERE 1=1";
$params = [];

if ($role === 'Executive') {
    // If team selected via switcher
    if ($team_id !== 'ALL') {
        $sql .= " AND seller IN (SELECT user_id FROM user_teams WHERE team_id = :team_id)";
        $params[':team_id'] = $team_id;
    }
    // Else: No filter, see all
} elseif ($role === 'Sale Supervisor') {
    // See team data
    if ($team_id === 'ALL') {
        // Multiple teams
        $team_ids = $_SESSION['team_ids'];
        $placeholders = implode(',', array_fill(0, count($team_ids), '?'));
        $sql .= " AND seller IN (SELECT user_id FROM user_teams WHERE team_id IN ($placeholders))";
        $params = array_merge($params, $team_ids);
    } else {
        // Single team
        $sql .= " AND seller IN (SELECT user_id FROM user_teams WHERE team_id = :team_id)";
        $params[':team_id'] = $team_id;
    }
} else {
    // Seller/Engineer: Own data only
    $sql .= " AND seller = :user_id";
    $params[':user_id'] = $user_id;
}

$stmt = $condb->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();
```

### Working with UUID Primary Keys

**Generate UUID:**
```php
// PHP UUID generation (requires extension or function)
function generateUUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

// Or use database trigger (already set up in schema)
$uuid = generateUUID();
```

**Note:** Database has triggers for auto UUID generation on some tables (e.g., `category`, `category_image`)

### File Uploads

**Example: Upload with validation**
```php
// Validate file
$allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
$max_size = 5242880; // 5MB

$validation = validateUploadedFile($_FILES['file'], $allowed_types, $max_size);
if (!$validation['valid']) {
    die(json_encode(['success' => false, 'message' => $validation['message']]));
}

// Sanitize filename
$original_filename = $_FILES['file']['name'];
$safe_filename = sanitizeFilename($original_filename);

// Generate unique filename
$file_extension = pathinfo($safe_filename, PATHINFO_EXTENSION);
$unique_filename = generateUUID() . '.' . $file_extension;

// Upload path
$upload_dir = __DIR__ . '/../../uploads/project_documents/';
$upload_path = $upload_dir . $unique_filename;

// Move file
if (move_uploaded_file($_FILES['file']['tmp_name'], $upload_path)) {
    // Save to database
    $stmt = $condb->prepare("
        INSERT INTO project_documents
        (document_id, project_id, file_name, file_path, file_size, uploaded_by, uploaded_at)
        VALUES (:id, :project, :name, :path, :size, :user, NOW())
    ");
    $stmt->execute([
        ':id' => generateUUID(),
        ':project' => $project_id,
        ':name' => $original_filename,
        ':path' => 'uploads/project_documents/' . $unique_filename,
        ':size' => $_FILES['file']['size'],
        ':user' => $_SESSION['user_id']
    ]);

    echo json_encode(['success' => true, 'message' => 'File uploaded']);
} else {
    echo json_encode(['success' => false, 'message' => 'Upload failed']);
}
```

### AJAX Requests

**Example: Fetch task comments**
```javascript
function loadComments(taskId) {
    $.ajax({
        url: 'get_task_comments.php',
        type: 'GET',
        data: { task_id: taskId },
        success: function(html) {
            $('#comments-container').html(html);
        },
        error: function(xhr, status, error) {
            console.error('Failed to load comments:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load comments'
            });
        }
    });
}
```

**Example: Post comment with file**
```javascript
function postComment() {
    const formData = new FormData();
    formData.append('task_id', taskId);
    formData.append('comment_text', $('#comment-text').val());

    // Add file if selected
    if ($('#file-input')[0].files.length > 0) {
        formData.append('attachment', $('#file-input')[0].files[0]);
    }

    $.ajax({
        url: 'post_comment.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                $('#comment-text').val('');
                $('#file-input').val('');
                loadComments(taskId); // Reload comments
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message,
                    timer: 1500
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message
                });
            }
        }
    });
}
```

### Using DataTables

**Basic initialization:**
```javascript
$(document).ready(function() {
    $('#example1').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
        "pageLength": 25,
        "order": [[0, "desc"]] // Sort by first column descending
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
});
```

**With server-side processing (for large datasets):**
```javascript
$('#example2').DataTable({
    "processing": true,
    "serverSide": true,
    "ajax": {
        "url": "get_data.php",
        "type": "POST",
        "data": function(d) {
            d.team_id = $('#team-filter').val();
            d.status = $('#status-filter').val();
        }
    },
    "columns": [
        { "data": "project_name" },
        { "data": "customer_name" },
        { "data": "status" },
        { "data": "sale_no_vat" }
    ]
});
```

### Using Select2

**Basic usage:**
```javascript
$('.select2').select2({
    theme: 'bootstrap4',
    width: '100%'
});
```

**AJAX data source:**
```javascript
$('#customer-select').select2({
    theme: 'bootstrap4',
    ajax: {
        url: 'search_customers.php',
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                q: params.term, // Search term
                page: params.page || 1
            };
        },
        processResults: function(data, params) {
            params.page = params.page || 1;
            return {
                results: data.items,
                pagination: {
                    more: (params.page * 30) < data.total_count
                }
            };
        }
    },
    minimumInputLength: 2,
    placeholder: 'Search customer...'
});
```

### SweetAlert2 Notifications

**Success message:**
```javascript
Swal.fire({
    icon: 'success',
    title: 'Success!',
    text: 'Project created successfully',
    timer: 2000,
    showConfirmButton: false
});
```

**Confirmation dialog:**
```javascript
Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'Cancel'
}).then((result) => {
    if (result.isConfirmed) {
        // Proceed with deletion
        $.ajax({
            url: 'delete_project.php',
            type: 'POST',
            data: { project_id: projectId },
            success: function() {
                Swal.fire('Deleted!', 'Project has been deleted.', 'success');
                location.reload();
            }
        });
    }
});
```

---

## Technology Stack

### Backend
- **PHP 7.4+** - Server-side scripting
- **PDO** - Database abstraction (prepared statements)
- **Composer** - Dependency management
- **OpenSSL** - Encryption (AES-256-CBC)

### Database
- **MySQL/MariaDB** - Relational database
- **InnoDB Engine** - Transactional support
- **UTF-8 charset** - Unicode support
- **UUID Primary Keys** - CHAR(36) identifiers

### Frontend Framework
- **AdminLTE 3.x** - Admin dashboard template
- **Bootstrap 4** - Responsive CSS framework
- **jQuery 3.x** - JavaScript library
- **Font Awesome 5** - Icon library

### JavaScript Libraries
- **DataTables** - Enhanced table functionality
- **Select2** - Advanced select dropdowns
- **SweetAlert2** - Beautiful alerts
- **DateRangePicker** - Date selection
- **Summernote** - WYSIWYG editor
- **Toastr** - Toast notifications
- **Chart.js** - Data visualization (dashboard)
- **OverlayScrollbars** - Custom scrollbars

### Authentication & Security
- **Session-based** authentication
- **Bcrypt** password hashing
- **AES-256-CBC** encryption
- **PDO Prepared Statements** - SQL injection prevention
- **CSRF tokens** - Cross-site request forgery protection
- **Input validation** - XSS prevention

### Development Tools
- **Composer** - PHP dependency management
- **Git** - Version control
- **.env** - Environment configuration

---

## Business Logic & Workflows

### Project Financial Calculations

```php
// Sales Calculation
$sale_no_vat = [base amount entered by user];
$sale_vat = $sale_no_vat * (1 + ($vat_rate / 100));

// Cost Calculation
$cost_no_vat = SUM(project_costs.total_price); // From cost items
$cost_vat = $cost_no_vat * (1 + ($vat_rate / 100));

// Profit Calculation
$gross_profit = $sale_no_vat - $cost_no_vat;
$potential = ($sale_no_vat > 0) ? ($gross_profit / $sale_no_vat) * 100 : 0;

// Role-Based Visibility
if ($role === 'Engineer') {
    // Hide ALL financial fields in UI
} else {
    // Show based on data access permissions
}
```

### Service Ticket SLA Calculation

```php
// On ticket creation/update
$start_time = new DateTime($ticket['start_at']);
$sla_target_hours = $ticket['sla_target']; // e.g., 24 hours

$sla_deadline = clone $start_time;
$sla_deadline->add(new DateInterval("PT{$sla_target_hours}H"));

// Update ticket
$ticket['sla_deadline'] = $sla_deadline->format('Y-m-d H:i:s');

// Calculate SLA status
$now = new DateTime();
$time_remaining = $sla_deadline->getTimestamp() - $now->getTimestamp();
$total_sla_time = $sla_target_hours * 3600; // Convert to seconds

if ($now > $sla_deadline) {
    $sla_status = 'Overdue'; // Red
} elseif ($time_remaining < ($total_sla_time * 0.2)) {
    $sla_status = 'Near SLA'; // Yellow (< 20% remaining)
} else {
    $sla_status = 'Within SLA'; // Green
}
```

### Task Progress Tracking

```php
// When updating task progress
$old_progress = $task['progress'];
$new_progress = $_POST['progress']; // 0-100

if ($old_progress != $new_progress) {
    // Update task
    $stmt = $condb->prepare("UPDATE project_tasks SET progress = :progress WHERE task_id = :id");
    $stmt->execute([':progress' => $new_progress, ':id' => $task_id]);

    // Log to task_comments
    $stmt = $condb->prepare("
        INSERT INTO task_comments
        (comment_id, task_id, project_id, user_id, comment_type, old_value, new_value, created_at)
        VALUES (:id, :task, :project, :user, 'progress_update', :old, :new, NOW())
    ");
    $stmt->execute([
        ':id' => generateUUID(),
        ':task' => $task_id,
        ':project' => $project_id,
        ':user' => $_SESSION['user_id'],
        ':old' => $old_progress,
        ':new' => $new_progress
    ]);

    // Auto-complete if progress = 100
    if ($new_progress == 100 && $task['status'] != 'Completed') {
        $stmt = $condb->prepare("UPDATE project_tasks SET status = 'Completed' WHERE task_id = :id");
        $stmt->execute([':id' => $task_id]);
    }
}
```

### Team Data Filtering Flow

```
User Login
    ├─> Query user_teams: Get all teams for user
    ├─> Count teams
    │   ├─> 1 team: Set session team_id = that team
    │   └─> >1 teams: Set session team_id = 'ALL'
    └─> Set session team_ids = array of all team UUIDs

Page Load
    ├─> Extract session: $role, $team_id, $user_id
    ├─> Build SQL query based on role:
    │   ├─> Executive:
    │   │   ├─> If team_id == 'ALL': No filter (see all)
    │   │   └─> If team_id == UUID: Filter to that team
    │   ├─> Sale Supervisor:
    │   │   ├─> If team_id == 'ALL': Filter to user's teams (IN clause)
    │   │   └─> If team_id == UUID: Filter to that team
    │   └─> Seller/Engineer:
    │       └─> Filter to user_id (own data only)
    └─> Execute filtered query

Team Switch (AJAX)
    ├─> User clicks team in dropdown
    ├─> POST to switch_team.php with team_id
    ├─> Validate team membership
    ├─> Update session variables
    └─> Page reload → Data re-filtered
```

---

## System Architecture Summary

### Multi-Tier Architecture

```
Presentation Layer (Frontend)
    ├─> AdminLTE 3.x + Bootstrap 4
    ├─> jQuery + DataTables + Select2
    ├─> SweetAlert2 + DateRangePicker
    └─> Responsive, mobile-friendly UI

Application Layer (PHP Backend)
    ├─> Session management (authentication)
    ├─> Role-based access control (RBAC)
    ├─> Business logic (calculations, workflows)
    ├─> Input validation & sanitization
    ├─> File upload handling
    └─> AJAX endpoints

Data Layer (Database)
    ├─> MySQL/MariaDB with InnoDB
    ├─> 48 tables with UUID PKs
    ├─> Foreign key relationships
    ├─> Triggers for auto UUID generation
    └─> Views for reporting

Security Layer
    ├─> Prepared statements (SQL injection prevention)
    ├─> Password hashing (bcrypt)
    ├─> Input validation (XSS prevention)
    ├─> CSRF tokens
    ├─> File upload validation
    ├─> Session security
    ├─> Encryption (AES-256-CBC)
    └─> Role-based data filtering
```

### Key Design Patterns

1. **Session-Based Authentication**
   - All pages protected by session check
   - Session variables store user context
   - Auto-redirect to login if not authenticated

2. **Role-Based Access Control (RBAC)**
   - 4 roles: Executive, Sale Supervisor, Seller, Engineer
   - Permission checked at page and data level
   - Financial data hidden for Engineers

3. **Multi-Team Architecture**
   - Many-to-many user-team relationship
   - Primary team designation
   - Team switcher for multi-team users
   - Data filtered by active team

4. **UUID Primary Keys**
   - CHAR(36) identifiers
   - Prevents ID enumeration attacks
   - Database triggers for auto-generation

5. **Audit Trail**
   - created_at, created_by, updated_at, updated_by
   - Activity logs (user_creation_logs, task_comments, service_ticket_history)
   - Soft deletes (deleted_at, is_deleted)

6. **Modular Structure**
   - Shared components (Header, Footer, Navbar, Add_session)
   - Module-based organization (pages/[module]/)
   - Separation of concerns

---

## Important Development Notes

### Financial Data Access

**CRITICAL:** Engineers MUST NOT see financial data. Always check:
```php
$can_view_financial = ($role !== 'Engineer');

if (!$can_view_financial) {
    // Hide sale_no_vat, cost_no_vat, gross_profit, potential
}
```

### Team Filtering

Always respect the active team from session:
```php
$team_id = $_SESSION['team_id']; // Can be UUID or 'ALL'

if ($role === 'Executive' && $team_id === 'ALL') {
    // No team filter
} elseif ($role === 'Sale Supervisor') {
    if ($team_id === 'ALL') {
        // Filter to user's teams
        $team_ids = $_SESSION['team_ids'];
    } else {
        // Filter to selected team
    }
}
```

### File Uploads

Always validate files before accepting:
```php
$validation = validateUploadedFile($_FILES['file'], $allowed_types, $max_size);
if (!$validation['valid']) {
    // Reject upload
}
```

### Database Queries

NEVER concatenate user input into SQL:
```php
// WRONG:
$sql = "SELECT * FROM users WHERE username = '" . $_POST['username'] . "'";

// CORRECT:
$stmt = $condb->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute([':username' => $_POST['username']]);
```

### Session Variables

Never trust client-side data for authorization:
```php
// WRONG:
if ($_POST['role'] === 'Executive') { ... }

// CORRECT:
if ($_SESSION['role'] === 'Executive') { ... }
```

### Error Handling

Show generic errors to users, log details:
```php
try {
    // Database operation
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
```

---

## Future Enhancements (Recommendations)

### High Priority
1. **Universal CSRF Implementation** - Add tokens to all forms
2. **Security Headers** - CSP, X-Frame-Options, HSTS
3. **Password Policy** - Complexity, history, expiration
4. **HTTPS Enforcement** - Redirect HTTP to HTTPS
5. **Session Timeout** - Auto-logout after inactivity
6. **Audit Logging** - Comprehensive activity logs

### Medium Priority
7. **Two-Factor Authentication (2FA)** - For Executive accounts
8. **Email Notifications** - Ticket assignments, task mentions, etc.
9. **RESTful API** - For mobile app or third-party integration
10. **WebSocket** - Real-time updates for comments/tickets
11. **Advanced Reporting** - Custom reports, dashboards, charts
12. **Calendar Integration** - Task deadlines, project timelines

### Low Priority
13. **Progressive Web App (PWA)** - Offline capability
14. **Dark Mode** - UI theme toggle
15. **Multi-Language** - i18n support (Thai/English)
16. **Export Options** - More formats (XML, JSON)
17. **Advanced Search** - Full-text search across modules
18. **Kanban Board** - Alternative task view

### Code Quality
19. **MVC Framework** - Refactor to Laravel or similar
20. **Unit Tests** - PHPUnit for business logic
21. **Code Documentation** - PHPDoc comments
22. **Performance Optimization** - Query optimization, caching
23. **Database Indexing** - Review and optimize indexes
24. **Asset Minification** - Minify CSS/JS for production

---

## Troubleshooting Common Issues

### Login Issues
- **Rate limited:** Wait for block period to expire
- **Incorrect credentials:** Verify username/password
- **Session issues:** Clear browser cookies, restart session

### Team Switcher Not Showing
- User must belong to >1 team in `user_teams` table
- Check session: `$_SESSION['user_teams']` should have multiple entries

### Financial Data Visible to Engineer
- BUG! Check role condition: `$role !== 'Engineer'`
- Financial fields should be hidden in UI

### File Upload Fails
- Check file size (max 5MB default)
- Verify file type in whitelist
- Check directory permissions (755 for uploads/)
- Ensure `file_uploads = On` in php.ini

### Database Connection Fails
- Verify .env credentials
- Check MySQL service is running
- Test connection: `mysql -u root -p`

### Task Comments Not Loading
- Check AJAX endpoint: `get_task_comments.php`
- Verify task_id parameter
- Check browser console for errors
- Ensure user has access to project

---

## Glossary

- **RBAC** - Role-Based Access Control
- **UUID** - Universally Unique Identifier (CHAR(36) format)
- **SLA** - Service Level Agreement (time-based targets)
- **VAT** - Value Added Tax
- **CSRF** - Cross-Site Request Forgery
- **XSS** - Cross-Site Scripting
- **PDO** - PHP Data Objects (database abstraction)
- **ITIL** - IT Infrastructure Library (service management framework)
- **CRM** - Customer Relationship Management
- **WYSIWYG** - What You See Is What You Get (editor)

---

**Document Version:** 2.0
**Last Updated:** 2025-10-04
**Database Schema Version:** Based on sales_db.sql (2979 lines, 48 tables)
**System Status:** Production-ready with recommended security enhancements