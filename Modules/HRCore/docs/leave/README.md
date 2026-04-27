# HRCore Module Documentation

Welcome to the HRCore module documentation. This module provides comprehensive Human Resource management functionality for the ERP system.

## Documentation Structure

### Leave Management

The leave management system is a core component of HRCore, providing complete leave lifecycle management with 20 comprehensive leave types.

- **[Leave Management System Documentation](./LEAVE_MANAGEMENT.md)**
  - Complete technical documentation
  - Database schema with accurate field names
  - Features and functionality
  - Configuration options
  - Implementation details

- **[Leave API Reference](./LEAVE_API_REFERENCE.md)**
  - RESTful API endpoints
  - Request/response formats with all fields
  - Authentication details
  - Comprehensive leave type responses
  - Code examples

- **[Leave Administrator Guide](./LEAVE_ADMIN_GUIDE.md)**
  - Setup and configuration
  - 20 pre-configured leave types
  - Leave type view functionality
  - Balance management
  - Daily operations
  - Reports and analytics

- **[Leave User Guide](./LEAVE_USER_GUIDE.md)**
  - Employee instructions
  - Understanding all 20 leave types
  - How to request leave
  - Half-day and emergency features
  - Check balances
  - FAQs

- **[Leave Features Implemented](./LEAVE_FEATURES_IMPLEMENTED.md)**
  - Comprehensive feature list
  - Enhanced leave types with view offcanvas
  - Balance management system
  - API enhancements
  - Implementation status

### Other HR Components

Documentation for other HRCore components will be added here:

- Employee Management (Coming Soon)
- Attendance System (Coming Soon)
- Organization Structure (Coming Soon)
- Expense Management (Coming Soon)

## Quick Links

### For Developers
- [API Reference](./LEAVE_API_REFERENCE.md) - Integration guide
- [Database Schema](./LEAVE_MANAGEMENT.md#database-schema) - Table structures
- [Models](./LEAVE_MANAGEMENT.md#models) - Eloquent models

### For Administrators
- [Initial Setup](./LEAVE_ADMIN_GUIDE.md#initial-setup) - Getting started
- [Daily Operations](./LEAVE_ADMIN_GUIDE.md#daily-operations) - Routine tasks
- [Reports](./LEAVE_ADMIN_GUIDE.md#reports-and-analytics) - Analytics

### For End Users
- [User Guide](./LEAVE_USER_GUIDE.md) - Employee instructions
- [FAQs](./LEAVE_USER_GUIDE.md#faqs) - Common questions
- [Quick Reference](./LEAVE_USER_GUIDE.md#quick-reference-card) - Shortcuts

## Module Overview

HRCore is a comprehensive Human Resource management module that includes:

1. **Employee Management**
   - Employee profiles and records
   - Document management
   - Bank account details
   - Reporting structure

2. **Leave Management**
   - 20 comprehensive leave types (regular, parental, medical, legal, special)
   - Enhanced leave type configuration with view offcanvas
   - Balance tracking with real-time calculations
   - Multi-level approval workflows
   - Automatic accruals and carry forward
   - Leave encashment support
   - Half-day leave support
   - Emergency contact tracking
   - Travel abroad monitoring
   - Document attachments
   - Bulk balance operations
   - Comprehensive audit trails

3. **Attendance Management**
   - Check-in/out tracking
   - Multiple attendance modes
   - Shift management
   - Overtime calculation

4. **Organization Structure**
   - Departments
   - Designations
   - Teams
   - Hierarchical views

5. **Expense Management**
   - Expense requests
   - Approval workflows
   - Reimbursement tracking
   - Policy enforcement

## Architecture

The HRCore module follows the modular architecture pattern:

```
Modules/HRCore/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Web and API controllers
│   │   └── Middleware/       # Module-specific middleware
│   ├── Models/              # Eloquent models
│   ├── Services/            # Business logic services
│   └── Enums/               # Enum definitions
├── config/                  # Module configuration
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/            # Database seeders
├── docs/                    # Documentation
├── resources/
│   ├── lang/               # Translations
│   └── views/              # Blade templates
├── routes/                  # Route definitions
│   ├── api.php             # API routes
│   └── web.php             # Web routes
└── tests/                   # Module tests
```

## Contributing

When contributing to HRCore documentation:

1. Follow the existing documentation structure
2. Keep technical and user documentation separate
3. Include code examples where relevant
4. Update the table of contents
5. Test all code examples
6. Keep language clear and concise

## Version History

- **v2.5** - Added 20 comprehensive leave types, view offcanvas for leave types, enhanced documentation
- **v2.0** - Enhanced leave management with half-day support, emergency contacts, travel tracking
- **v1.5** - Added leave accruals and carry forward
- **v1.0** - Initial release with basic leave management

## Support

For support and questions:
- Technical issues: Create an issue in the repository
- Documentation improvements: Submit a pull request
- General questions: Contact the HR system administrator

---

*Generated with Laravel ERP System*
*Module: HRCore v2.0*