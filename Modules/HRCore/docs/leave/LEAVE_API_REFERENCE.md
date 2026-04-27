# Leave Management API Reference

## Authentication
All API endpoints require authentication using JWT tokens. Include the token in the Authorization header:
```
Authorization: Bearer {your-jwt-token}
```

## Base URL
```
https://your-domain.com/api/V1/leave
```

## Endpoints

### 1. Get Leave Types
Retrieve all active leave types with user's balance information.

**Endpoint:** `GET /types`

**Response:**
```json
{
  "statusCode": 200,
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "Annual Leave",
      "code": "AL",
      "isImgRequired": false,
      "availableBalance": 15.5,
      "isAccrualEnabled": true,
      "accrualFrequency": "monthly",
      "accrualRate": 1.25,
      "maxAccrualLimit": 30,
      "allowCarryForward": true,
      "maxCarryForward": 5,
      "carryForwardExpiryMonths": 3,
      "allowEncashment": true,
      "maxEncashmentDays": 10,
      "isCompOffType": false
    },
    {
      "id": 2,
      "name": "Sick Leave",
      "code": "SL",
      "isImgRequired": true,
      "availableBalance": 10,
      "isAccrualEnabled": false,
      "allowCarryForward": false,
      "allowEncashment": false,
      "isCompOffType": false
    }
  ]
}
```

### 2. Get Leave Balance
Get detailed leave balance for all leave types.

**Endpoint:** `GET /balance`

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| year | integer | No | Year to get balance for (default: current year) |

**Response:**
```json
{
  "statusCode": 200,
  "status": "success",
  "data": {
    "year": "2025",
    "balances": [
      {
        "leaveTypeId": 1,
        "leaveTypeName": "Annual Leave",
        "leaveTypeCode": "AL",
        "totalBalance": 15.5,
        "pendingLeaves": 2,
        "availableBalance": 13.5,
        "year": "2025",
        "isAccrualEnabled": true,
        "accrualRate": 1.75,
        "allowCarryForward": true
      },
      {
        "leaveTypeId": 2,
        "leaveTypeName": "Sick Leave",
        "leaveTypeCode": "SL",
        "totalBalance": 10,
        "pendingLeaves": 0,
        "availableBalance": 10,
        "year": "2025",
        "isAccrualEnabled": false,
        "accrualRate": 0,
        "allowCarryForward": false
      }
    ]
  }
}
```

### 3. Get Leave Requests
Retrieve paginated list of user's leave requests.

**Endpoint:** `GET /requests`

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| skip | integer | No | Number of records to skip (default: 0) |
| take | integer | No | Number of records to return (default: 10) |
| status | string | No | Filter by status (pending, approved, rejected, cancelled) |
| from_date | date | No | Filter requests from this date |
| to_date | date | No | Filter requests until this date |

**Response:**
```json
{
  "statusCode": 200,
  "status": "success",
  "data": {
    "totalCount": 25,
    "values": [
      {
        "id": 123,
        "fromDate": "2024-01-15",
        "toDate": "2024-01-17",
        "isHalfDay": false,
        "halfDayType": null,
        "totalDays": 3,
        "leaveType": "Annual Leave",
        "leaveTypeId": 1,
        "userNotes": "Family vacation",
        "approvalNotes": null,
        "status": "pending",
        "statusLabel": "Pending",
        "statusColor": "warning",
        "createdOn": "2024-01-10 10:30:00",
        "approvedOn": null,
        "approvedBy": null,
        "rejectedOn": null,
        "rejectedBy": null,
        "cancelledOn": null,
        "cancelReason": null,
        "emergencyContact": "John Doe",
        "emergencyPhone": "+1234567890",
        "isAbroad": true,
        "abroadLocation": "Paris, France",
        "hasDocument": true,
        "documentUrl": "https://domain.com/storage/leave_documents/doc123.pdf"
      }
    ]
  }
}
```

### 4. Get Leave Request Details
Get detailed information about a specific leave request.

**Endpoint:** `GET /request/{id}`

**Response:**
```json
{
  "statusCode": 200,
  "status": "success",
  "data": {
    "id": 123,
    "fromDate": "2024-01-15",
    "toDate": "2024-01-17",
    "isHalfDay": false,
    "halfDayType": null,
    "halfDayDisplay": "",
    "totalDays": 3,
    "leaveType": {
      "id": 1,
      "name": "Annual Leave",
      "code": "AL"
    },
    "userNotes": "Family vacation",
    "approvalNotes": null,
    "status": "pending",
    "statusLabel": "Pending",
    "statusBadge": "<span class=\"badge bg-label-warning\">Pending</span>",
    "emergencyContact": "John Doe",
    "emergencyPhone": "+1234567890",
    "isAbroad": true,
    "abroadLocation": "Paris, France",
    "document": "https://domain.com/storage/leave_documents/doc123.pdf",
    "createdAt": "2024-01-10 10:30:00",
    "approvedBy": null,
    "approvedAt": null,
    "rejectedBy": null,
    "rejectedAt": null,
    "cancelledAt": null,
    "cancelReason": null
  }
}
```

### 5. Create Leave Request
Submit a new leave request.

**Endpoint:** `POST /request`

**Request Body:**
```json
{
  "fromDate": "2024-01-15",
  "toDate": "2024-01-17",
  "leaveType": 1,
  "comments": "Family vacation to Paris",
  "isHalfDay": false,
  "halfDayType": null,
  "emergencyContact": "John Doe",
  "emergencyPhone": "+1234567890",
  "isAbroad": true,
  "abroadLocation": "Paris, France"
}
```

**Half-Day Request Example:**
```json
{
  "fromDate": "2024-01-15",
  "toDate": "2024-01-15",
  "leaveType": 1,
  "comments": "Doctor appointment",
  "isHalfDay": true,
  "halfDayType": "first_half"
}
```

**Validation Rules:**
- `fromDate` - required, date format (YYYY-MM-DD)
- `toDate` - required, date format, must be on or after fromDate
- `leaveType` - required, valid leave type ID
- `comments` - required, string, max 500 characters
- `isHalfDay` - optional, boolean (Note: Half-day leaves must have same fromDate and toDate)
- `halfDayType` - required if isHalfDay is true, values: "first_half" or "second_half"
- `emergencyContact` - optional, string, max 100 characters
- `emergencyPhone` - optional, string, max 50 characters
- `isAbroad` - optional, boolean
- `abroadLocation` - required if isAbroad is true, string, max 200 characters

**Important Notes:**
- Half-day leaves can only be applied for a single day (fromDate must equal toDate)
- The system validates leave balance before creating the request
- Overlapping leave requests are not allowed

**Success Response:**
```json
{
  "statusCode": 200,
  "status": "success",
  "data": {
    "message": "Leave request created successfully",
    "leaveRequestId": 124
  }
}
```

**Error Response:**
```json
{
  "statusCode": 400,
  "status": "failed",
  "data": "Insufficient leave balance. Available: 2 days, Requested: 3 days"
}
```

### 6. Update Leave Request
Update a pending or draft leave request.

**Endpoint:** `PUT /request/{id}`

**Request Body:**
```json
{
  "fromDate": "2024-01-16",
  "toDate": "2024-01-18",
  "comments": "Updated: Family vacation to Paris",
  "emergencyContact": "Jane Doe",
  "emergencyPhone": "+0987654321",
  "isAbroad": true,
  "abroadLocation": "Lyon, France"
}
```

**Note:** Only pending or draft requests can be updated. All fields are optional.

**Response:**
```json
{
  "statusCode": 200,
  "status": "success",
  "data": "Leave request updated successfully"
}
```

### 7. Upload Leave Document
Upload supporting document for a leave request.

**Endpoint:** `POST /upload-document`

**Request Body (multipart/form-data):**
- `file` - required, file (pdf, jpg, jpeg, png), max 2MB
- `leaveRequestId` - optional, specific leave request ID

**Response:**
```json
{
  "statusCode": 200,
  "status": "success",
  "data": {
    "message": "Document uploaded successfully",
    "documentUrl": "https://domain.com/storage/leave_documents/doc124.pdf"
  }
}
```

### 8. Cancel Leave Request
Cancel a pending or approved leave request.

**Endpoint:** `POST /cancel`

**Request Body:**
```json
{
  "leaveRequestId": 123,
  "reason": "Change of plans"
}
```

**Response:**
```json
{
  "statusCode": 200,
  "status": "success",
  "data": "Leave request cancelled successfully"
}
```

## Error Codes

| Status Code | Description |
|-------------|-------------|
| 200 | Success |
| 400 | Bad Request - Validation error or business rule violation |
| 401 | Unauthorized - Invalid or missing authentication |
| 403 | Forbidden - User doesn't have permission |
| 404 | Not Found - Resource not found |
| 422 | Unprocessable Entity - Validation errors |
| 500 | Internal Server Error |

## Common Error Messages

1. **Insufficient Balance**
   ```json
   {
     "statusCode": 400,
     "status": "failed",
     "data": "Insufficient leave balance. Available: X days, Requested: Y days"
   }
   ```

2. **Overlapping Leave**
   ```json
   {
     "statusCode": 400,
     "status": "failed",
     "data": "You already have a leave request for the selected dates"
   }
   ```

3. **Validation Error**
   ```json
   {
     "statusCode": 422,
     "status": "failed",
     "data": "The from date must be a date before or equal to to date"
   }
   ```

## Working Days Calculation

The system automatically calculates working days by:
1. Excluding weekends (Saturday and Sunday)
2. Excluding active holidays configured in the system
3. Half-day requests count as 0.5 days

Example:
- Request from Monday to Friday = 5 working days
- Request from Thursday to Tuesday = 4 working days (excluding weekend)
- Request including a public holiday = Working days minus holiday count
- Half-day request = 0.5 days

The calculation considers:
- Active holidays from the holidays table
- Only holidays with status = 'active' are excluded
- Holidays falling on weekends are not double-counted

## Rate Limiting

API endpoints are rate-limited to prevent abuse:
- 60 requests per minute per user
- 1000 requests per hour per user

## Webhooks (Future Enhancement)

Webhook notifications for leave events:
- Leave request created
- Leave request approved
- Leave request rejected
- Leave request cancelled

## SDK Examples

### JavaScript/Axios
```javascript
// Get leave balance
const getLeaveBalance = async () => {
  try {
    const response = await axios.get('/api/V1/leave/balance', {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });
    return response.data.data;
  } catch (error) {
    console.error('Error fetching leave balance:', error);
  }
};

// Create leave request
const createLeaveRequest = async (leaveData) => {
  try {
    const response = await axios.post('/api/V1/leave/request', leaveData, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });
    return response.data;
  } catch (error) {
    console.error('Error creating leave request:', error.response.data);
  }
};
```

### PHP/Guzzle
```php
use GuzzleHttp\Client;

$client = new Client([
    'base_uri' => 'https://your-domain.com/api/V1/',
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ]
]);

// Get leave types
$response = $client->get('leave/types');
$leaveTypes = json_decode($response->getBody(), true);

// Create leave request
$response = $client->post('leave/request', [
    'json' => [
        'fromDate' => '2024-01-15',
        'toDate' => '2024-01-17',
        'leaveType' => 1,
        'comments' => 'Family vacation',
        'isAbroad' => true,
        'abroadLocation' => 'Paris, France'
    ]
]);
```

## Testing

### Postman Collection
A Postman collection is available with all endpoints pre-configured:
1. Import the collection from `Modules/HRCore/docs/postman/Leave_Management_API.json`
2. Set up environment variables:
   - `base_url`: Your API base URL
   - `auth_token`: Your JWT token

### Test Scenarios
1. **Create Leave Request**
   - Valid request with all fields
   - Half-day request
   - Request with insufficient balance
   - Overlapping dates

2. **Update Leave Request**
   - Update pending request
   - Try updating approved request (should fail)

3. **Cancel Leave Request**
   - Cancel pending request
   - Cancel approved request

## Support

For API support or bug reports:
- Email: api-support@your-domain.com
- Documentation: https://docs.your-domain.com/api/leave
- Issue Tracker: https://github.com/your-org/erp/issues