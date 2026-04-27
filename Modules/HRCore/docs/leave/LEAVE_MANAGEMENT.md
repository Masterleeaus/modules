# Leave Management System Documentation

## Table of Contents
1. [Overview](#overview)
2. [Features](#features)
3. [Database Schema](#database-schema)
4. [Models](#models)
5. [Controllers](#controllers)
6. [API Endpoints](#api-endpoints)
7. [Views and UI](#views-and-ui)
8. [Leave Balance Calculation](#leave-balance-calculation)
9. [Approval Workflow](#approval-workflow)
10. [Configuration](#configuration)
11. [Usage Examples](#usage-examples)

## Overview

The Leave Management System in HRCore provides comprehensive functionality for managing employee leaves, including leave requests, approvals, balance tracking, and various leave policies. The system supports half-day leaves, emergency contacts, travel tracking, and automatic leave accruals.

## Features

### Core Features
- **Leave Request Management**: Create, view, edit, and cancel leave requests
- **Multiple Leave Types**: Support for different leave categories (Annual, Sick, Emergency, etc.)
- **Leave Balance Tracking**: Real-time balance calculation with pending leaves consideration
- **Half-Day Leave Support**: Option to request half-day leaves (first half/second half)
- **Document Attachments**: Upload supporting documents for leave requests
- **Emergency Contact Information**: Capture emergency contact details during leave
- **Travel Tracking**: Track if employee is traveling abroad during leave
- **Leave Accruals**: Automatic leave balance accumulation based on configured rules
- **Carry Forward**: Support for carrying forward unused leaves to next period
- **Leave Encashment**: Option to encash unused leaves (if enabled)
- **Compensatory Off**: Support for comp-off leaves

### Administrative Features
- **Approval Workflow**: Multi-level approval system integration
- **Bulk Actions**: Approve/reject multiple leave requests
- **Leave Reports**: Comprehensive reporting and analytics
- **Leave Calendar**: Visual representation of team leaves
- **Leave History**: Complete audit trail of all leave transactions

## Database Schema

### leave_types
```sql
CREATE TABLE `leave_types` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL UNIQUE,
  `notes` text,
  `is_proof_required` boolean DEFAULT false,
  `status` enum('active','inactive') DEFAULT 'active',
  `is_accrual_enabled` boolean DEFAULT false,
  `accrual_frequency` enum('monthly','quarterly','yearly') DEFAULT 'monthly',
  `accrual_rate` decimal(5,2) DEFAULT 0,
  `max_accrual_limit` decimal(5,2),
  `allow_carry_forward` boolean DEFAULT false,
  `max_carry_forward` decimal(5,2),
  `carry_forward_expiry_months` int,
  `allow_encashment` boolean DEFAULT false,
  `max_encashment_days` decimal(5,2),
  `is_comp_off_type` boolean DEFAULT false,
  `created_by_id` bigint UNSIGNED,
  `updated_by_id` bigint UNSIGNED,
  `created_at` timestamp,
  `updated_at` timestamp,
  `deleted_at` timestamp,
  PRIMARY KEY (`id`)
);
```

### leave_requests
```sql
CREATE TABLE `leave_requests` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `leave_type_id` bigint UNSIGNED NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `is_half_day` boolean DEFAULT false,
  `half_day_type` enum('first_half','second_half'),
  `total_days` decimal(4,2) DEFAULT 1,
  `user_notes` text,
  `approval_notes` text,
  `document` varchar(255),
  `status` varchar(50) DEFAULT 'pending',
  `approved_by_id` bigint UNSIGNED,
  `approved_at` timestamp,
  `rejected_by_id` bigint UNSIGNED,
  `rejected_at` timestamp,
  `cancelled_by_id` bigint UNSIGNED,
  `cancelled_at` timestamp,
  `cancel_reason` text,
  `emergency_contact` varchar(100),
  `emergency_phone` varchar(50),
  `is_abroad` boolean DEFAULT false,
  `abroad_location` varchar(200),
  `is_comp_off` boolean DEFAULT false,
  `comp_off_date` date,
  `created_by_id` bigint UNSIGNED,
  `updated_by_id` bigint UNSIGNED,
  `created_at` timestamp,
  `updated_at` timestamp,
  `deleted_at` timestamp,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `leave_type_id` (`leave_type_id`),
  KEY `status` (`status`),
  KEY `from_date` (`from_date`),
  KEY `to_date` (`to_date`)
);
```

### user_available_leaves
```sql
CREATE TABLE `user_available_leaves` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `leave_type_id` bigint UNSIGNED NOT NULL,
  `year` int NOT NULL,
  `entitled_leaves` decimal(5,2) DEFAULT 0,
  `carried_forward_leaves` decimal(5,2) DEFAULT 0,
  `additional_leaves` decimal(5,2) DEFAULT 0,
  `used_leaves` decimal(5,2) DEFAULT 0,
  `available_leaves` decimal(5,2) DEFAULT 0,
  `carry_forward_expiry_date` date,
  `created_by_id` bigint UNSIGNED,
  `updated_by_id` bigint UNSIGNED,
  `created_at` timestamp,
  `updated_at` timestamp,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_leave_year` (`user_id`, `leave_type_id`, `year`)
);
```

### leave_accruals
```sql
CREATE TABLE `leave_accruals` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `leave_type_id` bigint UNSIGNED NOT NULL,
  `accrual_date` date NOT NULL,
  `accrued_days` decimal(5,2) NOT NULL,
  `balance_before` decimal(5,2) DEFAULT 0,
  `balance_after` decimal(5,2) DEFAULT 0,
  `notes` text,
  `created_at` timestamp,
  `updated_at` timestamp,
  PRIMARY KEY (`id`),
  KEY `user_leave` (`user_id`, `leave_type_id`),
  KEY `accrual_date` (`accrual_date`)
);
```

## Models

### LeaveType Model
Location: `Modules/HRCore/app/Models/LeaveType.php`

```php
class LeaveType extends Model
{
    // Key relationships
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
    
    public function userAvailableLeaves()
    {
        return $this->hasMany(UserAvailableLeave::class);
    }
    
    // Key methods
    public function getAccrualDays(): float
    {
        // Calculate accrual based on frequency and rate
    }
}
```

### LeaveRequest Model
Location: `Modules/HRCore/app/Models/LeaveRequest.php`

```php
class LeaveRequest extends Model
{
    // Key relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
    
    // Key methods
    public function calculateTotalDays(): float
    {
        // Calculate working days between dates
    }
    
    public function hasOverlappingLeave(): bool
    {
        // Check for date conflicts
    }
    
    public function getHalfDayDisplayAttribute(): string
    {
        // Format half-day display text
    }
}
```

## Controllers

### Web Controllers

#### LeaveController
Location: `Modules/HRCore/app/Http/Controllers/LeaveController.php`

**Key Methods:**
- `index()` - Display leave requests listing
- `indexAjax()` - DataTable AJAX endpoint
- `create()` - Show leave request form
- `store()` - Process new leave request
- `show()` - Display leave request details
- `actionAjax()` - Handle approve/reject/cancel actions

### API Controllers

#### API LeaveController
Location: `Modules/HRCore/app/Http/Controllers/Api/LeaveController.php`

**Key Methods:**
- `getLeaveTypes()` - Get available leave types with balances
- `getLeaveBalance()` - Get user's leave balances
- `getLeaveRequests()` - Get paginated leave requests
- `createLeaveRequest()` - Submit new leave request
- `updateLeaveRequest()` - Update pending request
- `cancelLeaveRequest()` - Cancel leave request
- `uploadLeaveDocument()` - Upload supporting document

## API Endpoints

### Leave Types
```
GET /api/V1/leave/types
Response: {
    "status": "success",
    "data": [{
        "id": 1,
        "name": "Annual Leave",
        "code": "AL",
        "isImgRequired": false,
        "availableBalance": 15.5,
        "isAccrualEnabled": true,
        "accrualFrequency": "monthly",
        "accrualRate": 1.25
    }]
}
```

### Leave Balance
```
GET /api/V1/leave/balance
Response: {
    "status": "success",
    "data": [{
        "leaveTypeId": 1,
        "leaveTypeName": "Annual Leave",
        "totalBalance": 15.5,
        "pendingLeaves": 2,
        "availableBalance": 13.5
    }]
}
```

### Create Leave Request
```
POST /api/V1/leave/request
Body: {
    "fromDate": "2024-01-15",
    "toDate": "2024-01-17",
    "leaveType": 1,
    "comments": "Family vacation",
    "isHalfDay": false,
    "emergencyContact": "John Doe",
    "emergencyPhone": "+1234567890",
    "isAbroad": true,
    "abroadLocation": "Paris, France"
}
```

### Get Leave Requests
```
GET /api/V1/leave/requests?skip=0&take=10&status=pending
Response: {
    "status": "success",
    "data": {
        "totalCount": 25,
        "values": [{
            "id": 1,
            "fromDate": "2024-01-15",
            "toDate": "2024-01-17",
            "totalDays": 3,
            "leaveType": "Annual Leave",
            "status": "pending",
            "userNotes": "Family vacation",
            "hasDocument": true
        }]
    }
}
```

## Views and UI

### Main Views

#### Leave Index Page
Location: `Modules/HRCore/resources/views/leave/index.blade.php`
- DataTable listing with filters
- Quick stats cards
- Action buttons for view/edit/delete

#### Leave Request Form
Location: `Modules/HRCore/resources/views/leave/_form.blade.php`
- Date range picker with half-day option
- Leave type selection with balance display
- Emergency contact fields
- Travel information section
- Document upload

#### Leave Details Offcanvas
Location: `Modules/HRCore/resources/views/leave/_leave_request_details.blade.php`
- Complete leave request information
- Approval/rejection buttons (based on permissions)
- Status history

### JavaScript
Location: `resources/assets/js/app/hrcore-leaves.js`
- DataTable initialization
- AJAX form submissions
- Dynamic UI updates
- Leave balance calculations

## Leave Balance Calculation

### Balance Components
1. **Entitled Leaves**: Base allocation for the year
2. **Carried Forward**: Previous year's unused leaves
3. **Accrued Leaves**: Accumulated through accrual rules
4. **Used Leaves**: Approved leave requests
5. **Pending Leaves**: Requests awaiting approval

### Calculation Formula
```
Available Balance = Entitled + Carried Forward + Accrued - Used - Pending
```

### Accrual Processing
- Runs based on configured frequency (monthly/quarterly/yearly)
- Considers maximum accrual limit
- Tracks accrual history in `leave_accruals` table

## Approval Workflow

### Integration with Approval System
The leave management system integrates with the central approval workflow:

1. **Submission**: Leave request triggers approval workflow
2. **Routing**: Based on configured approval rules
3. **Actions**: Approve/Reject with comments
4. **Notifications**: Email/system notifications at each step
5. **Completion**: Updates leave balance upon approval

### Status Flow
```
Draft → Pending → Approved/Rejected → Cancelled (if needed)
```

## Configuration

### Leave Type Settings
- **Basic Settings**: Name, code, description
- **Policy Settings**: Proof requirement, half-day allowance
- **Accrual Settings**: Enable/disable, frequency, rate
- **Carry Forward**: Enable/disable, maximum days, expiry
- **Encashment**: Enable/disable, maximum days

### System Settings
- **Working Days**: Configure weekends and holidays
- **Notification Settings**: Email templates and triggers
- **Approval Levels**: Configure approval hierarchy

## Usage Examples

### Employee Actions

#### Requesting Leave
1. Navigate to Leave Management
2. Click "Request Leave"
3. Select dates and leave type
4. Fill emergency contact if traveling
5. Upload documents if required
6. Submit for approval

#### Checking Balance
1. View balance in employee dashboard
2. Check detailed balance in leave section
3. Review accrual history

### Manager Actions

#### Approving Leave
1. Navigate to "My Approvals"
2. Review leave request details
3. Check team calendar for conflicts
4. Approve/Reject with comments

#### Team Overview
1. View team leave calendar
2. Check leave patterns and trends
3. Export leave reports

### Admin Actions

#### Configure Leave Types
1. Navigate to Settings > Leave Types
2. Add/Edit leave type
3. Configure accrual rules
4. Set carry forward policies

#### Manage Leave Balances
1. View employee leave balances
2. Make manual adjustments
3. Process year-end carry forward

## Best Practices

1. **Regular Balance Updates**: Run accrual process monthly
2. **Document Requirements**: Clearly define when documents are needed
3. **Approval Timelines**: Set SLAs for leave approvals
4. **Holiday Planning**: Update holiday calendar annually
5. **Balance Reconciliation**: Audit leave balances quarterly

## Troubleshooting

### Common Issues

1. **Balance Mismatch**
   - Check accrual processing status
   - Verify manual adjustments
   - Review calculation logs

2. **Approval Workflow Issues**
   - Verify approval rules configuration
   - Check user permissions
   - Review workflow logs

3. **Document Upload Failures**
   - Check file size limits
   - Verify allowed file types
   - Check storage permissions

## Future Enhancements

1. **Mobile App Integration**: Native mobile support
2. **Advanced Analytics**: Predictive leave patterns
3. **Integration with Payroll**: Automatic leave deduction
4. **Leave Planning Tools**: Team capacity planning
5. **Automated Policies**: Rule-based auto-approval