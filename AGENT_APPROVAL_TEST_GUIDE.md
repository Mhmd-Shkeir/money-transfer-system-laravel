# Agent Approval & Rejection Testing Guide

## Quick Start: Test the Approve/Reject Buttons

### Step 1: Ensure Admin Account Exists & is Logged In

**Admin Login Credentials (Example):**
```
Email: admin@example.com
Password: password
Role: admin
```

If you don't have an admin account, create one:
```bash
php artisan tinker
```

Then paste:
```php
$admin = App\Models\User::firstOrCreate(
    ['email' => 'admin@example.com'],
    [
        'first_name' => 'Admin',
        'last_name' => 'User',
        'password_hash' => bcrypt('password'),
        'role' => 'admin',
        'status' => 'active'
    ]
);
echo "Admin created: {$admin->email}";
exit;
```

### Step 2: Login as Admin

1. Go to: `http://127.0.0.1:8000/login`
2. Enter:
   - **Email:** `admin@example.com`
   - **Password:** `password`
3. Click **Login**
4. You should be redirected to `/admin/dashboard`

### Step 3: View Pending Agent Approvals

On the Admin Dashboard (`http://127.0.0.1:8000/admin/dashboard`):
- Look for the **"Pending Agent Approvals"** section on the left side
- You should see a card with pending agent(s)

#### Example Pending Agent Card:
```
┌─────────────────────────────────┐
│ Test Agent Store                │
│ Test Agent                      │
│ 123 Test Street, Test City      │
│                                 │
│ [Pending Approval] Applied: ... │
│                                 │
│ [✓ Approve] [✕ Reject] [...] │
└─────────────────────────────────┘
```

### Step 4: Click "Approve" Button

1. On the pending agent card, click the **green "✓ Approve"** button
2. Expected behavior:
   - Button becomes disabled (grayed out)
   - A success alert appears: `"Request processed successfully"`
   - Page refreshes automatically
   - Agent card disappears from "Pending Agent Approvals"
   - Agent's status is now **Active** and role is **Agent**

### Step 5: Click "Reject" Button (to test rejection)

1. Create another pending agent (see Step 6 below)
2. On the new pending agent card, click the **red "✕ Reject"** button
3. Expected behavior:
   - Button becomes disabled
   - A success alert appears: `"Request processed successfully"`
   - Page refreshes
   - Agent card disappears
   - Agent's status is now **Rejected** and role remains **User**
   - Agent receives rejection email notification

---

## Create Test Pending Agents

### Quick Method: Run PHP Script

The project includes `test_agent.php` to quickly create a test pending agent:

```bash
php test_agent.php
```

Output should be:
```
✓ Created/verified test agent:
  User ID: 2
  User Email: testagent@example.com
  Agent Profile ID: 1
  Is Approved: pending (0)

Now visit http://127.0.0.1:8000/admin/dashboard and click Approve/Reject
```

### Manual Method: Create Multiple Test Agents

To create multiple agents for thorough testing, run:

```bash
php artisan tinker
```

Then paste this code for each agent:

```php
$user = App\Models\User::create([
    'first_name' => 'Agent',
    'last_name' => 'Two',
    'email' => 'agent2@example.com',
    'password_hash' => bcrypt('password'),
    'role' => 'user',
    'status' => 'active'
]);

App\Models\AgentProfile::create([
    'user_id' => $user->id,
    'is_approved' => 0,
    'store_name' => 'Agent Two Store',
    'address' => '456 Another Street, Another City',
    'commission_rate' => 2.50,
    'total_transactions' => 0
]);

echo "Agent 2 created: {$user->email}";
exit;
```

---

## What Happens When Admin Approves/Rejects

### On Approval:

**Database Changes:**
- `agent_profiles.is_approved` → `1` (approved)
- `users.role` → `'agent'` (promoted to agent)
- `users.status` → `'active'` (activated)

**Email Sent:**
- To: Agent's email
- Subject: Agent Application Approved
- Body: Congratulation message with agent details

**UI Changes:**
- Agent card removed from "Pending Agent Approvals"
- Page auto-refreshes
- Success message shown

### On Rejection:

**Database Changes:**
- `agent_profiles.is_approved` → `-1` (rejected)
- `users.role` → `'user'` (stays as regular user)
- `users.status` → `'rejected'` (marked rejected)

**Email Sent:**
- To: Agent's email
- Subject: Agent Application Rejected
- Body: Rejection message explaining the decision

**UI Changes:**
- Agent card removed from pending list
- Page auto-refreshes
- Success message shown

---

## Test Scenarios

### Scenario 1: Approve a Single Agent
1. Login as admin
2. View dashboard → see 1 pending agent
3. Click "✓ Approve"
4. Agent disappears, notification sent
5. Verify agent can now login as agent role

### Scenario 2: Reject a Single Agent
1. Create a pending agent
2. Login as admin
3. Click "✕ Reject"
4. Agent disappears, rejection email sent
5. Verify agent cannot access agent features

### Scenario 3: Multiple Pending Agents
1. Create 3 pending agents via `test_agent.php` (or duplicate script)
2. Login as admin
3. See 3 pending agent cards
4. Approve 1, Reject 1, leave 1 pending
5. Verify correct agents update/disappear

### Scenario 4: Email Notification Check
1. Approve an agent
2. Check Laravel log: `storage/logs/laravel.log`
3. Look for lines like: `Mail sent to testagent@example.com`
4. (If using Gmail SMTP, check spam folder)

---

## Troubleshooting

### Issue: "No pending agent applications" shows

**Solution:** Create a test agent first
```bash
php test_agent.php
```

### Issue: Button doesn't respond when clicked

**Check:**
1. Are you logged in as admin? (top-right corner should show admin menu)
2. Is JavaScript enabled? (F12 → Console, look for errors)
3. Is the server running? (check terminal for "Server running")

**Fix:** Refresh the page (Ctrl+Shift+R) and try again

### Issue: Alert says "Error: ..." 

**Check:**
1. Open DevTools (F12)
2. Go to Network tab
3. Click button again
4. Find the POST request to `/admin/agents/{id}/approve` or `/reject`
5. Check Response → copy error message
6. Check server logs: `storage/logs/laravel.log`

### Issue: Agent card doesn't disappear after approval

**Check:**
- Did you see the success alert?
- Is page auto-refreshing? (watch for loading spinner)
- If no refresh, manually refresh: F5

---

## Quick Reference: Button Behavior

| Action | Button Color | Disabled After | Status | Email |
|--------|--------------|-----------------|--------|-------|
| **Approve** | Green ✓ | Yes | `is_approved=1` | AgentApproved |
| **Reject** | Red ✕ | Yes | `is_approved=-1` | AgentRejected |

---

## File Locations

- **Admin Controller:** `app/Http/Controllers/AdminAgentController.php`
- **Routes:** `routes/web.php` (look for `admin.agents.approve` / `admin.agents.reject`)
- **Dashboard View:** `resources/views/admin/dashboard.blade.php`
- **Email Templates:** `resources/views/emails/agent_approved.blade.php` and `agent_rejected.blade.php`
- **Database Logs:** `storage/logs/laravel.log`

---

## Success Checklist ✓

- [x] Admin can login
- [x] Pending agents display on dashboard
- [x] Approve button works (agent moves to active)
- [x] Reject button works (agent marked as rejected)
- [x] Emails sent on approval/rejection
- [x] Page refreshes after action
- [x] Agent card disappears from pending list

**You're ready to test!** 🚀
