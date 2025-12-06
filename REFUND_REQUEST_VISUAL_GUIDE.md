# Refund Request System - Visual Guide & Quick Reference

## 🎯 Feature Overview

Users can request refunds on pending cash pickup transactions. Admins review and approve/reject these requests.

---

## 📋 User Journey

### Step 1: Create Transaction
```
User Dashboard
  ↓
"Send Money" Button
  ↓
Fill Form → Choose "Cash Pickup" → Create Transaction
  ↓
Status: PENDING ✓
```

### Step 2: Request Refund
```
Transaction Receipt Page
  ↓
"Request Refund" Button (visible if pending + cash_pickup + user is sender)
  ↓
Modal Opens
  ↓
Enter Reason (min 10 chars)
  ↓
Click "Submit Refund Request"
  ↓
Success: "Your refund request has been submitted"
```

### Step 3: Await Admin Decision
```
RefundRequest Created with Status: PENDING
  ↓
Admin Notified
  ↓
User Waits...
```

### Step 4a: If Approved ✓
```
Admin Clicks "Approve Refund"
  ↓
Transaction → Status: CANCELLED
  ↓
User Wallet → +$[full amount]
  ↓
User Notification: "Your refund has been approved"
```

### Step 4b: If Rejected ✗
```
Admin Enters Rejection Reason
  ↓
Admin Clicks "Reject Refund"
  ↓
Transaction → Status: STILL PENDING
  ↓
User Notification: "Your refund was rejected. Reason: [reason]"
```

---

## 🛠️ Admin Dashboard

### Location
```
Admin Sidebar → "Refund Requests"
```

### Two Tabs

#### Tab 1: PENDING REQUESTS
```
┌─────────────────────────────────────────────────┐
│ Pending Requests              [Count: 5 badge]   │
├─────────────────────────────────────────────────┤
│ #ID │ User      │ Ref Code │ Amount │ Review    │
├─────────────────────────────────────────────────┤
│ #15 │ John Doe  │ ABC1234  │ $50    │ [Review]  │
│ #16 │ Jane Smith│ XYZ5678  │ $75    │ [Review]  │
└─────────────────────────────────────────────────┘
[Pagination: 1 2 3 ...]
```

#### Tab 2: RESOLVED
```
┌────────────────────────────────────────────────┐
│ Resolved Requests          [Count: 23 badge]    │
├────────────────────────────────────────────────┤
│ #ID │ User     │ Ref Code │ Status │ Reviewed  │
├────────────────────────────────────────────────┤
│ #10 │ Alex J.  │ DEF1234  │ ✓ APPROVED │ 2d ago  │
│ #11 │ Bob S.   │ GHI5678  │ ✗ REJECTED │ 1d ago  │
└────────────────────────────────────────────────┘
[Pagination: 1 2 3 ...]
```

---

## 📝 Admin Review Page

### Layout
```
┌─────────────────────────┐     ┌──────────────────┐
│ REFUND REQUEST INFO     │     │ DECISION PANEL   │
│                         │     │                  │
│ Status: PENDING ⚠       │     │ Alert Box        │
│ Requested: 2 hours ago  │     │                  │
│                         │     │ [Approve Form]   │
│ ─────────────────────   │     │  └─ Notes        │
│ Customer Reason:        │     │  └─ [Approve]   │
│ "I changed my mind..."  │     │                  │
│                         │     │ [Reject Form]    │
│ ─────────────────────   │     │  └─ Reason*     │
│ Admin Notes:            │     │  └─ [Reject]    │
│ (none yet)              │     └──────────────────┘
│                         │
│ ─────────────────────   │
│ TRANSACTION DETAILS     │
│                         │
│ Ref: TRANS001234        │
│ Sender: John Doe        │
│ Recipient: Jane Smith   │
│ Amount: $500.00         │
│ Fee: $12.50             │
│ Total: $512.50          │
│ Method: Cash Pickup     │
│ Status: Pending         │
└─────────────────────────┘
```

---

## 🔐 Security Checks

### User Can Request If:
```
✓ Logged in as user
✓ Own the transaction
✓ Transaction status = PENDING
✓ Payout method = CASH_PICKUP
✓ No pending refund request already exists
✓ Reason is 10-1000 characters
```

### Admin Can Approve If:
```
✓ Logged in as admin
✓ Request status = PENDING
✓ Transaction still exists
✓ Optional: Add admin notes
```

### Admin Can Reject If:
```
✓ Logged in as admin
✓ Request status = PENDING
✓ Required: Provide reason (10-500 chars)
```

---

## 📊 Database Relationships

```
Users
  ├── sent Transactions
  ├── requested RefundRequests
  └── reviewed RefundRequests (as admin)

Transactions
  ├── RefundRequests (many)
  └── Sender (User)

RefundRequests
  ├── Transaction
  ├── User (sender)
  ├── Reviewer (admin)
  └── Status: pending → approved/rejected → (closed)
```

---

## 🔔 Notification Events

### Event 1: User Creates Request
```
WHO: Admins
TITLE: "New Refund Request"
MESSAGE: "User John Doe requested refund for transaction #123"
ACTION: Link to admin review page
```

### Event 2: Admin Approves
```
WHO: User
TITLE: "Your Refund Request Approved"
MESSAGE: "Your refund for transaction #123 has been approved. $512.50 has been credited to your wallet."
ACTION: Link to dashboard
```

### Event 3: Admin Rejects
```
WHO: User
TITLE: "Your Refund Request Rejected"
MESSAGE: "Your refund for transaction #123 was rejected. Reason: Transaction already partially processed"
ACTION: Link to support
```

---

## 💾 What Happens Behind the Scenes

### When User Requests:
```
1. Validate input (reason length, etc.)
2. Check transaction (pending? cash_pickup? user's own?)
3. Check no duplicate pending request exists
4. Create RefundRequest record
5. Send admin notification
6. Return success message
```

### When Admin Approves:
```
1. Validate admin notes (if provided)
2. Start database transaction
3.   Update RefundRequest: status = 'approved'
4.   Update RefundRequest: reviewed_by, reviewed_at
5.   Update Transaction: status = 'cancelled'
6.   Update User: balance += refund_amount
7. Commit database transaction
8. Send user notification
9. Return success message
```

### When Admin Rejects:
```
1. Validate admin notes (required)
2. Update RefundRequest: status = 'rejected'
3. Update RefundRequest: reviewed_by, reviewed_at, admin_notes
4. Send user notification with reason
5. Return success message
```

---

## 📂 File Structure

```
Web Programming 2 Project/
├── app/
│   ├── Models/
│   │   ├── RefundRequest.php ..................... (NEW)
│   │   └── Transaction.php ....................... (MODIFIED)
│   └── Http/
│       └── Controllers/
│           └── RefundRequestController.php ....... (NEW)
│
├── database/
│   └── migrations/
│       └── 2025_11_28_create_refund_requests_table.php (NEW)
│
├── resources/
│   └── views/
│       ├── user/
│       │   └── receipt.blade.php ................. (MODIFIED)
│       ├── admin/
│       │   └── refund_requests/
│       │       ├── index.blade.php ............... (NEW)
│       │       └── show.blade.php ................ (NEW)
│       └── partials/
│           └── dashboard-sidebar.blade.php ....... (MODIFIED)
│
├── routes/
│   └── web.php ................................... (MODIFIED)
│
└── Documentation/
    ├── REFUND_REQUEST_SYSTEM.md .................. (NEW)
    ├── REFUND_REQUEST_SETUP.md ................... (NEW)
    ├── REFUND_IMPLEMENTATION_COMPLETE.md ........ (NEW)
    └── REFUND_REQUEST_VISUAL_GUIDE.md ........... (NEW - this file)
```

---

## 🚀 Quick Start Checklist

- [ ] Run migration: `php artisan migrate`
- [ ] Clear cache: `php artisan cache:clear`
- [ ] Create test cash pickup transaction
- [ ] Request refund from user account
- [ ] Login as admin
- [ ] Navigate to Refund Requests
- [ ] Review and approve/reject
- [ ] Verify wallet updated (if approved)
- [ ] Check notifications sent

---

## ❓ FAQ

**Q: Can I request a refund for a completed transaction?**
A: No, only pending transactions with cash_pickup method.

**Q: Can I request a refund multiple times?**
A: No, only one pending refund request per transaction.

**Q: What happens to fees?**
A: Entire amount including fees is refunded.

**Q: Can I cancel my refund request?**
A: Currently no, but admin can reject it.

**Q: Will the recipient be notified?**
A: The transaction is cancelled, so they won't receive funds.

**Q: How long does approval take?**
A: Depends on admin response time.

**Q: Can I see reason why refund was rejected?**
A: Yes, via notification and your transaction history.

---

## 📞 Support

For issues or questions, refer to:
- `REFUND_REQUEST_SYSTEM.md` - Technical details
- `REFUND_REQUEST_SETUP.md` - Setup and testing
- Admin should check notification system logs
