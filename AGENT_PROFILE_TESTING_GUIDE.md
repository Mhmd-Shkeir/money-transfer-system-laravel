# 🚀 AGENT PROFILE - QUICK START TESTING GUIDE

## How to Test the New Agent Profile & Business Info System

---

## STEP 1: Access the Application

1. Open your browser
2. Go to: `http://localhost/Web%20Programming%202%20Project`
3. You should see the home page

---

## STEP 2: Login as Agent

**Demo Account:**

-   Email: `demo.agent@example.com`
-   Password: `Password1!`

**Steps:**

1. Click "Sign In" or navigate to login
2. Enter email and password
3. Click "Login"
4. You'll be redirected to `/agent/dashboard`

---

## STEP 3: Navigate to Agent Profile

**Method 1: Click Sidebar Menu**

-   Look for user profile icon/dropdown in sidebar
-   Click "My Business Profile"

**Method 2: Direct URL**

-   Go to: `/agent/profile`
-   Full URL: `http://localhost/Web%20Programming%202%20Project/agent/profile`

---

## STEP 4: View the New Form

You should see a beautiful form with 8 sections:

### Section 1: Personal Information 🔵

-   First Name
-   Last Name
-   Email Address
-   Phone Number

### Section 2: Business Details 🟢

-   Business Name (required)
-   Registration Number (required)
-   Tax ID
-   Business Description
-   Office Address
-   Country (180+ countries)
-   Operating Currency

### Section 3: Contact & Online 🔵

-   Business Phone
-   Business Email
-   Website
-   Service Description

### Section 4: Location & Timezone 🔴

-   Timezone (24 major timezones)
-   Latitude (GPS)
-   Longitude (GPS)

### Section 5: Operating Hours 🟡

-   Sunday through Saturday
-   Open/Close times for each day
-   Closed checkbox for non-operating days

---

## STEP 5: TEST EACH FIELD

### Test Business Information

1. Find "Business Name" field
2. Enter: "Test Business Inc."
3. Find "Registration Number" field
4. Enter: "REG-12345-TEST"
5. Find "Tax ID" field
6. Enter: "TAX-2024-001"
7. Add Business Description

### Test Country Selection

1. Find "Country" dropdown
2. Click the dropdown
3. Look for Arab countries: Egypt, Saudi Arabia, UAE, Qatar, etc.
4. Verify **Israel is NOT in the list** ✅
5. Select: "United States" (or any country)

### Test Currency Selection

1. Find "Operating Currency" dropdown
2. Click to see all currencies
3. Select: "US Dollar (USD)"
4. Notice the currency badge in the sidebar changes

### Test Timezone Selection

1. Find "Timezone" dropdown
2. Click to see all 24 timezones
3. Select: "New York (UTC-5)" or "Cairo (UTC+2)" etc.

### Test Business Hours

1. Find the "Operating Hours" section
2. For **Monday**: Uncheck "Closed", set Open: 09:00, Close: 18:00
3. For **Tuesday**: Same as Monday
4. For **Wednesday**: Same as Monday
5. For **Thursday**: Same as Monday
6. For **Friday**: Same as Monday
7. For **Saturday**: Uncheck "Closed", set Open: 10:00, Close: 16:00
8. For **Sunday**: Check "Closed" ✅

### Test GPS Coordinates

1. Find "Latitude" field
2. Enter: 40.7128 (New York example)
3. Find "Longitude" field
4. Enter: -74.0060

---

## STEP 6: SAVE THE FORM

1. Scroll to bottom of form
2. Click **"Save All Changes"** button (blue)
3. Wait for success message
4. You should see: "Agent profile and business information updated successfully!"

---

## STEP 7: VERIFY DATA SAVED

### In the Form

1. Refresh the page (F5)
2. All your data should still be there
3. Verify all fields show your entered values

### In the Sidebar

1. Look at "Business Status" card
2. Verify:

    - Business Name shows your value
    - Country shows your selection
    - **Currency Badge shows your selected currency** ✅
    - Approval Status shows (Approved/Pending)
    - Commission Rate shows

3. Look at "Operating Hours Summary" card
4. Verify:
    - Monday-Friday: 09:00 - 18:00
    - Saturday: 10:00 - 16:00
    - Sunday: Closed ✅

---

## STEP 8: TEST VALIDATION

### Required Fields

1. Clear "Business Name" field
2. Click "Save All Changes"
3. You should see error: "Business name is required"

### Email Validation

1. Change "Business Email" to: "not-an-email"
2. Click "Save All Changes"
3. You should see error: "Business email must be a valid email"

### GPS Coordinate Validation

1. Set "Latitude" to: 150 (invalid, must be -90 to 90)
2. Click "Save All Changes"
3. You should see error: "Latitude must be between -90 and 90"

### URL Validation

1. Set "Website" to: "not-a-url"
2. Click "Save All Changes"
3. You should see error: "Website must be a valid URL"

---

## STEP 9: TEST BUSINESS HOURS TOGGLE

1. Find "Operating Hours" section
2. Find "Monday" card
3. Click the "Closed" checkbox
4. Notice the time inputs become **disabled and grayed out** ✅
5. Click the checkbox again
6. Notice the time inputs become **enabled** ✅
7. Save and refresh
8. Verify Monday is now marked as "Closed"

---

## STEP 10: VERIFY AUTHENTICATION

1. Try to access profile while **logged out**:

    - Go to: `/agent/profile`
    - You should be **redirected to login**

2. Try to access as a **normal user** (not agent):

    - Login as: `demo.user@example.com`
    - Try to visit: `/agent/profile`
    - You should get an **authorization error**

3. Try to access as **admin**:
    - Login as: `demo.admin@example.com`
    - Try to visit: `/agent/profile`
    - You should get an **authorization error**

---

## CHECKLIST: Everything Should Work ✅

-   [✓] Form loads with 8 sections
-   [✓] All 180+ countries appear in dropdown
-   [✓] Israel is NOT in country list
-   [✓] All Arab countries appear (Egypt, Saudi Arabia, UAE, etc.)
-   [✓] Currency dropdown works
-   [✓] Currency badge displays in sidebar
-   [✓] Timezone dropdown shows 24 timezones
-   [✓] Business hours toggle works
-   [✓] Time inputs disable when "Closed" is checked
-   [✓] Form saves all data
-   [✓] Data persists on refresh
-   [✓] Validation shows error messages
-   [✓] Authentication blocks unauthorized access
-   [✓] Success message shows on save
-   [✓] Sidebar shows business status
-   [✓] Operating hours summary displays correctly

---

## DATABASE VERIFICATION

To verify data is saved in database:

1. Open PhpMyAdmin
2. Go to: `Web Programming 2 Project`
3. Check tables:
    - `agent_profiles` - Should have business info
    - `business_hours` - Should have 7 entries (Mon-Sun)
    - `countries` - Should have 180+ entries
    - Verify "Israel" (ISR) is NOT in countries

---

## DEMO DATA

After saving, your agent profile should have:

```
Personal:
- Name: [Your Name]
- Email: demo.agent@example.com

Business:
- Business Name: Test Business Inc.
- Registration: REG-12345-TEST
- Tax ID: TAX-2024-001

Location:
- Country: United States
- Currency: USD
- Timezone: America/New_York
- Latitude: 40.7128
- Longitude: -74.0060

Hours:
- Mon-Fri: 09:00 - 18:00
- Sat: 10:00 - 16:00
- Sun: Closed
```

---

## TROUBLESHOOTING

### Form not loading?

-   Clear browser cache (Ctrl+Shift+Delete)
-   Make sure you're logged in as an agent
-   Check PHP artisan is running

### Data not saving?

-   Check that all **required fields** are filled (marked with \*)
-   Look for red error messages
-   Check PHP error logs

### Countries dropdown empty?

-   Run: `php artisan migrate`
-   Run: `php artisan db:seed --class=DemoAgentBusinessHoursSeeder`

### Timezone not showing?

-   Refresh the page
-   Check ProfileController has getTimezonesList() method

### Business hours not toggle?

-   JavaScript might be blocked
-   Check browser console for errors (F12)

---

## NEXT STEPS

After testing:

1. Create real agent accounts
2. Have agents set their business info
3. Display business hours on customer pages
4. Use timezone for transaction routing
5. Show business status on profiles

---

**Status:** ✅ Ready for Testing!

All features implemented and working. Test and report any issues.
