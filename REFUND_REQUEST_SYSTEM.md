# Refund Request System Implementation

## Overview
A complete refund request system has been implemented allowing users to request refunds on pending cash pickup transactions, with admins able to review and approve/reject these requests.

## Database

### Migration: `2025_11_28_create_refund_requests_table.php`
Creates the `refund_requests` table with the following columns:
- `id` - Primary key
- `transaction_id` - Foreign key to transactions table (cascade delete)
- `user_id` - Foreign key to users table (sender requesting refund)
- `reason` - Text field for customer's reason for refund
- `status` - Enum: `pending`, `approved`, `rejected`, `cancelled`
- `admin_notes` - Optional text field for admin's decision notes
- `reviewed_by` - Foreign key to users table (admin who reviewed)
- `reviewed_at` - Timestamp of when admin reviewed
- `created_at`, `updated_at` - Standard timestamps

## Models

### RefundRequest Model
Located at `app/Models/RefundRequest.php`

**Relationships:**
- `transaction()` - Belongs to Transaction model
- `user()` - Belongs to User model (the sender requesting refund)
- `reviewer()` - Belongs to User model (the admin who reviewed)

**Fillable Fields:** transaction_id, user_id, reason, status, admin_notes, reviewed_by, reviewed_at

### Transaction Model (Updated)
Added relationship:
- `refundRequests()` - Has many RefundRequest model

## Controller

### RefundRequestController
Located at `app/Http/Controllers/RefundRequestController.php`

**Methods:**

1. **store(Request $request)** - Route: `POST /refund-requests`
   - Validates refund request from user
   - Verifies transaction is pending and cash_pickup method
   - Checks user is the sender
   - Prevents duplicate pending requests
   - Creates RefundRequest record
   - Notifies admins

2. **adminIndex()** - Route: `GET /admin/refund-requests`
   - Shows paginated pending refund requests (15 per page)
   - Shows resolved requests (approved/rejected)
   - Admins only (via middleware)

3. **show($id)** - Route: `GET /admin/refund-requests/{id}`
   - Shows detailed view of a specific refund request
   - Displays transaction details, customer reason, admin notes
   - Admins only

4. **approve(Request $request, $id)** - Route: `POST /admin/refund-requests/{id}/approve`
   - Admin approves a refund request
   - Sets transaction status to 'cancelled'
   - Refunds full amount to user's wallet
   - Records admin notes and timestamp
   - Notifies user via notification system

5. **reject(Request $request, $id)** - Route: `POST /admin/refund-requests/{id}/reject`
   - Admin rejects a refund request with required reason
   - Records admin notes and timestamp
   - Notifies user with rejection reason

## Views

### User Views

#### `resources/views/user/receipt.blade.php` (Updated)
Added "Request Refund" button that appears only for:
- Pending transactions
- Cash pickup payout method
- User is the sender

Added refund request modal with:
- Transaction reference display
- Reason textarea (10-1000 characters)
- Info alert about refund eligibility
- Submit button

### Admin Views

#### `resources/views/admin/refund_requests/index.blade.php`
Two-tab interface:
1. **Pending Tab**
   - List of all pending refund requests
   - Shows: User, Transaction ref, Amount, Reason snippet, Request date
   - "Review" button for each request
   - Pagination

2. **Resolved Tab**
   - List of approved/rejected refund requests
   - Shows: User, Transaction ref, Amount, Status badge, Reviewed by, Reviewed date

#### `resources/views/admin/refund_requests/show.blade.php`
Detailed review interface with:
- **Left Column:**
  - Refund request info (status, dates)
  - Full customer reason
  - Admin notes (if any)
  - Reviewer info (if reviewed)
  - Complete transaction details

- **Right Column (Action Panel):**
  - Alert message showing pending status
  - Approve form with optional admin notes
  - Reject form with required rejection reason
  - Confirmation dialogs

## Routes

**User Routes** (under `middleware('auth')`):
```
POST /refund-requests                    - RefundRequestController@store
```

**Admin Routes** (under `middleware(['auth', 'role:admin'])`):
```
GET  /admin/refund-requests              - RefundRequestController@adminIndex
GET  /admin/refund-requests/{id}         - RefundRequestController@show
POST /admin/refund-requests/{id}/approve - RefundRequestController@approve
POST /admin/refund-requests/{id}/reject  - RefundRequestController@reject
```

## Sidebar Navigation (Updated)
Added "Refund Requests" link to admin dashboard sidebar with undo-2 icon, visible only for admin users.

## Workflow

### User Flow:
1. User creates a cash pickup transaction → Status: `pending`
2. User views transaction receipt
3. If transaction is pending and cash_pickup:
   - "Request Refund" button appears
4. User clicks button, modal opens
5. User enters reason and submits
6. System creates RefundRequest with status: `pending`
7. Admins are notified

### Admin Flow:
1. Admin navigates to Refund Requests dashboard
2. Views list of pending refund requests
3. Clicks "Review" on a request
4. Reviews customer reason and transaction details
5. Either:
   - **Approve:** 
     - Transaction status → `cancelled`
     - Full amount refunded to user wallet
     - Admin notes optional
     - User receives notification
   - **Reject:**
     - Requires admin to provide rejection reason
     - User receives notification with reason

## Validations

### User Refund Request:
- Transaction ID must exist
- Reason: required, min 10 chars, max 1000 chars
- Transaction must have status: `pending`
- Transaction must have payout_method: `cash_pickup`
- User must be the sender
- No duplicate pending requests for same transaction

### Admin Approval:
- Admin notes: optional, max 500 chars

### Admin Rejection:
- Admin notes: required, min 10 chars, max 500 chars

## Notifications
- **When refund request created:** Admins are notified
- **When refund approved:** User receives notification with message about refund
- **When refund rejected:** User receives notification with rejection reason

## Database Transactions
- Approval process uses database transaction to ensure atomic operations:
  - Update RefundRequest status
  - Update Transaction status
  - Update User wallet balance
  - All succeed or all fail

## Error Handling
- Try-catch blocks for notification failures
- Validation errors shown to user
- Refund failures logged and returned to admin
- Prevents duplicate requests
- Ensures only valid transactions can be refunded

## Security
- Admin routes protected by middleware: `role:admin`
- User routes protected by middleware: `auth`
- Users can only create requests for their own transactions
- Only admins can approve/reject
- Authorization checks in controller methods
