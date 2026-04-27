# Leave Management Administrator Guide

## Quick Start

This guide helps administrators set up and manage the leave management system effectively.

## Initial Setup

### 1. Configure Leave Types

Navigate to **Human Resources > Leave Management > Leave Types** and set up your organization's leave categories.

#### Basic Leave Type Setup
1. Click **"Add Leave Type"**
2. Fill in the basic information:
   - **Name**: e.g., "Annual Leave", "Sick Leave"
   - **Code**: Short code like "AL", "SL" (must be unique)
   - **Description**: Optional notes about the leave type
   - **Proof Required**: Toggle if medical certificates or documents are needed
   - **Compensatory Off Type**: Mark if this is a comp-off leave type
   - **Status**: Set to Active

#### Advanced Settings

**Accrual Configuration:**
- **Enable Accrual**: Turn on for leaves that accumulate over time
- **Frequency**: Monthly, Quarterly, or Yearly
- **Accrual Rate**: Number of days earned per period
- **Maximum Limit**: Cap on total accrued leaves

**Carry Forward Rules:**
- **Allow Carry Forward**: Enable to let unused leaves roll over
- **Maximum Days**: Limit on carried forward leaves
- **Expiry Period**: Months before carried leaves expire

**Encashment Options:**
- **Allow Encashment**: Let employees convert leaves to cash
- **Maximum Encashment Days**: Limit on cashable leaves

#### Pre-configured Leave Types
The system comes with 20 comprehensive leave types:

**Regular Leaves:**
- Casual Leave (CL) - Short-term personal leave
- Earned/Annual Leave (EL) - Regular vacation leave with accrual
- Sick Leave (SL) - Medical leave for illness

**Parental Leaves:**
- Maternity Leave (ML) - 182 days for expecting mothers
- Paternity Leave (PL) - 15 days for new fathers
- Adoption Leave (ADL) - 12 weeks for adoptive parents
- Childcare Leave (CCL) - For child emergencies

**Special Leaves:**
- Bereavement Leave (BL) - For family loss
- Marriage Leave (MRL) - For own/family marriage
- Study Leave (STL) - For exams and education

**And more including:** Compensatory Off, Work From Home, Leave Without Pay, Sabbatical, Medical Leave, Emergency Leave, Quarantine Leave, Jury Duty, Voting Leave, Birthday Leave, and Relocation Leave.

#### Viewing Leave Type Details
1. In the Leave Types list, click the dropdown in the Actions column
2. Select **"View"** to open the detailed view offcanvas
3. The view shows all settings including:
   - Basic information and status
   - Accrual settings and frequency
   - Carry forward configuration
   - Encashment rules
   - Special type flags
   - Audit trail (created/updated by and when)

### 2. Set Employee Leave Balances

#### Initial Balance Setup
1. Go to **Human Resources > Leave Management > Balance Management**
2. Click on an employee's **View Details** button
3. For each leave type, click **"Set Initial"** if no balance exists
4. Enter the entitled leaves amount
5. Click **"Set Balance"**

#### Bulk Initial Balance Setup
For multiple employees:
1. Go to **Human Resources > Leave Management > Balance Management**
2. Click **Actions > Bulk Set Initial Balance**
3. Select the year and leave type
4. Enter default days to apply
5. Select employees from the list
6. Click **"Set Balance"**

#### Adjust Existing Balance
1. Navigate to employee's leave balance page
2. Click **"Adjust"** next to the leave type
3. Choose adjustment type (Add/Deduct)
4. Enter number of days and reason
5. Click **"Adjust Balance"**

### 3. Configure Approval Workflow

Set up who approves leave requests:

1. Navigate to **Settings > Approval Workflows**
2. Select **"Leave Request"** workflow
3. Configure approval levels:
   - Level 1: Direct Manager
   - Level 2: Department Head (for leaves > 5 days)
   - Level 3: HR Manager (for special cases)

### 4. Set Working Days and Holidays

Configure your organization's calendar:

1. **Working Days**: Go to **Settings > Working Days**
   - Define weekends (default: Saturday, Sunday)
   - Set working hours

2. **Holidays**: Go to **HR Settings > Holidays**
   - Add public holidays
   - Set location-specific holidays if needed

## Daily Operations

### Managing Leave Requests

#### View Pending Requests
1. Navigate to **Leave Management**
2. Use filters:
   - Status: Pending
   - Date Range: Current month
   - Department: Your team

#### Review and Approve
1. Click on a leave request to view details
2. Check:
   - Employee's leave balance
   - Team calendar for conflicts
   - Supporting documents (if required)
3. Actions:
   - **Approve**: Confirms the leave
   - **Reject**: Provide reason
   - **Request Info**: Ask for clarification

#### Bulk Actions
Select multiple requests and:
- Approve all
- Export to Excel
- Send reminders

### Leave Balance Management

#### View Team Balances
1. Go to **Reports > Leave Balance Report**
2. Filter by:
   - Department
   - Leave Type
   - Date Range

#### Manual Adjustments
When needed (e.g., compensation, errors):
1. Select employee
2. Click **"Adjust Balance"**
3. Enter:
   - Adjustment type (Add/Deduct)
   - Number of days
   - Reason for audit trail

#### Year-End Processing
1. Run **Year-End Leave Processing**:
   - Calculates carry forward
   - Expires old carried leaves
   - Generates encashment report
2. Review and approve adjustments
3. Notify employees of new balances

## Reports and Analytics

### Standard Reports

#### 1. Leave Balance Report
Shows current balances for all employees:
- Total entitled
- Used leaves
- Pending requests
- Available balance

#### 2. Leave Usage Report
Analyzes leave patterns:
- Department-wise usage
- Leave type distribution
- Seasonal trends
- Absenteeism patterns

#### 3. Leave History Report
Complete audit trail:
- All leave transactions
- Approval/rejection history
- Balance adjustments
- Document attachments

### Custom Reports

Create custom reports using the report builder:
1. Select data points
2. Add filters
3. Choose visualization
4. Schedule automated delivery

### Dashboard Widgets

Add to your admin dashboard:
- Pending approval count
- Team on leave today
- Leave trends graph
- Low balance alerts

## Team Calendar

### View Team Availability
1. Navigate to **Team Calendar**
2. View modes:
   - Month view
   - Week view
   - List view

### Calendar Features
- Color-coded by leave type
- Hover for employee details
- Export to external calendar
- Print team schedule

## Notifications

### Email Notifications

Configure when emails are sent:
- New leave request submitted
- Leave approved/rejected
- Low balance warning
- Document upload reminder

### System Alerts

Set up dashboard alerts for:
- Pending approvals > 48 hours
- Overlapping leave requests
- Policy violations
- System errors

## Common Scenarios

### 1. Emergency Leave Request

When an employee needs immediate leave:
1. Admin can create leave on behalf
2. Mark as "Emergency Leave"
3. Process approval post-facto
4. Update records with documents later

### 2. Leave Cancellation

For approved leaves that need cancellation:
1. Employee submits cancellation request
2. Admin reviews impact
3. Approves cancellation
4. System restores leave balance

### 3. Negative Balance

When exceptional leave is needed:
1. Enable "Allow Negative Balance" for leave type
2. Set maximum negative limit
3. Create recovery plan
4. Monitor balance restoration

### 4. Compensatory Off

For overtime compensation:
1. Create "Comp Off" leave type
2. Admin adds comp off days
3. Set expiry period
4. Employee uses like regular leave

## Best Practices

### 1. Regular Maintenance
- Review leave policies quarterly
- Audit leave balances monthly
- Update holiday calendar yearly
- Archive old leave records

### 2. Communication
- Announce policy changes in advance
- Send balance statements quarterly
- Remind about expiring leaves
- Publish team calendar

### 3. Compliance
- Maintain accurate records
- Follow labor law requirements
- Document all adjustments
- Regular compliance audits

### 4. User Training
- Conduct orientation for new employees
- Create leave policy handbook
- Regular refresher sessions
- Maintain FAQ document

## Troubleshooting

### Common Issues

#### 1. Balance Mismatch
**Problem**: Employee claims different balance
**Solution**:
- Check adjustment history
- Verify accrual calculations
- Review approved leaves
- Reconcile with payroll

#### 2. Approval Delays
**Problem**: Leaves pending too long
**Solution**:
- Check approver availability
- Verify workflow configuration
- Set up delegation rules
- Enable auto-escalation

#### 3. Document Issues
**Problem**: Can't view uploaded documents
**Solution**:
- Check file permissions
- Verify storage configuration
- Clear browser cache
- Contact IT support

#### 4. Report Discrepancies
**Problem**: Reports showing incorrect data
**Solution**:
- Refresh report cache
- Check filter settings
- Verify date ranges
- Rebuild report indexes

## Security and Compliance

### Access Control
- Role-based permissions
- Department-wise restrictions
- Audit trail for all actions
- Data encryption

### Data Privacy
- Limit access to sensitive info
- Regular access reviews
- Comply with data protection laws
- Secure document storage

### Backup and Recovery
- Daily automated backups
- Test restore procedures
- Document recovery process
- Maintain backup logs

## Integration Points

### Payroll System
- Automatic leave deduction
- Encashment processing
- Attendance reconciliation
- Salary impact calculation

### HRIS
- Employee master sync
- Organization hierarchy
- Department updates
- Role assignments

### Calendar Systems
- Google Calendar sync
- Outlook integration
- Team calendar feeds
- Mobile app sync

## Advanced Features

### Policy Engine
Create complex leave policies:
- Conditional approvals
- Blackout periods
- Minimum team strength
- Sequential leave limits

### Analytics Dashboard
- Predictive absence patterns
- Department comparisons
- Cost impact analysis
- Productivity correlation

### Mobile Approval
- Approve via mobile app
- Push notifications
- Offline capability
- Biometric authentication

## Support Resources

### Documentation
- User manuals
- Video tutorials
- Policy templates
- Best practices guide

### Getting Help
- In-app help system
- IT helpdesk tickets
- HR support team
- Vendor support (for technical issues)

### Training Resources
- Admin certification program
- Webinar series
- Knowledge base articles
- Community forum

## Appendix

### Glossary
- **Accrual**: Automatic leave accumulation
- **Carry Forward**: Unused leave transfer to next period
- **Encashment**: Converting leave to monetary compensation
- **Comp Off**: Compensatory time off for overtime
- **LOP**: Loss of Pay (unpaid leave)

### Quick Reference
- Maximum file upload: 2MB
- Supported formats: PDF, JPG, PNG
- Minimum leave: 0.5 days (half day)
- Maximum future request: 1 year
- Backdated limit: 30 days (configurable)