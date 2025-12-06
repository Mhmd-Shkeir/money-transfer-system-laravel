# Refund Request System - Setup & Testing Guide

## Installation Steps

### 1. Run Migration
```bash
php artisan migrate
```
This creates the `refund_requests` table in your database.

### 2. Test the Feature

#### User Side (Request Refund):
1. Login as a regular user
2. Create a cash pickup transaction
   - Go to "Send Money"
   - Select any beneficiary
   - Choose "Cash Pickup" as payout method
   - Submit the form
3. Go to transaction history
4. Click on the pending cash pickup transaction
5. You should see a "Request Refund" button in the top-right
6. Click it to open the modal
7. Enter a reason (minimum 10 characters)
8. Submit the form
9. You should see a success message: "Your refund request has been submitted. Admins will review it shortly."

#### Admin Side (Review & Approve/Reject):
1. Login as an admin user
2. Go to Dashboard → Sidebar → "Refund Requests"
3. You'll see the "Pending" tab with your refund request
4. Click the "Review" button
5. You can see:
   - Full customer reason
   - Transaction details (amount, sender, recipient, etc.)
   - Action panel on the right

#### To Approve:
1. (Optional) Add admin notes
2. Click "Approve Refund" button
3. Confirm in the browser dialog
4. System will:
   - Mark refund as approved
   - Cancel the transaction
   - Refund the full amount to user's wallet
   - Send notification to user

#### To Reject:
1. Enter a rejection reason (required, min 10 chars)
2. Click "Reject Refund" button
3. Confirm in the browser dialog
4. System will:
   - Mark refund as rejected
   - Keep transaction status as pending
   - Send notification to user with your reason

### 3. Check Results

#### User Notifications:
- Approved: "Your refund request for transaction #X has been approved. $Y has been credited back to your wallet."
- Rejected: "Your refund request for transaction #X has been rejected. Reason: [admin's reason]"

#### View Resolved Requests:
1. Admin navigates to Refund Requests
2. Click "Resolved" tab
3. See all approved/rejected requests with status badges

## Important Notes

- **Refunds only for pending cash pickup transactions**: The system won't allow requests for:
  - Completed transactions
  - Cancelled transactions
  - Bank deposit or mobile wallet transactions
  
- **No duplicate requests**: If a user already has a pending refund request for a transaction, they can't create another one

- **Full refund**: When approved, the entire transaction amount (including fees) is refunded to the user's wallet

- **Transaction cancellation**: When a refund is approved, the original transaction is automatically marked as 'cancelled'

## File Structure

```
Created Files:
├── app/Models/RefundRequest.php
├── app/Http/Controllers/RefundRequestController.php
├── database/migrations/2025_11_28_create_refund_requests_table.php
├── resources/views/admin/refund_requests/
│   ├── index.blade.php
│   └── show.blade.php

Updated Files:
├── app/Models/Transaction.php (added refundRequests relationship)
├── resources/views/user/receipt.blade.php (added refund button & modal)
├── resources/views/partials/dashboard-sidebar.blade.php (added admin link)
├── routes/web.php (added routes)
```

## Troubleshooting

### Button doesn't appear on transaction:
- Verify transaction has status: `pending`
- Verify payout_method: `cash_pickup`
- Verify you're the transaction sender
- Refresh the page

### Migration fails:
- Ensure database is running
- Check migrations table exists
- Verify no existing `refund_requests` table

### Notifications not appearing:
- Check that NotificationController has `notifyAdmins()` and `notifyUser()` methods
- Review notification system configuration
- Check system logs

### Admin routes not accessible:
- Verify you're logged in as admin (role = 'admin')
- Verify admin middleware is active in routes/web.php
- Check route registration
