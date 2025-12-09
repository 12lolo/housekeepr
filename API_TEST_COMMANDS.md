# Quick API Test Commands

## Test the API is deployed and accessible

### 1. Health Check
```bash
curl https://housekeepr.nl/up
```

### 2. Test Login Endpoint (should return error message)
```bash
curl -X POST https://housekeepr.nl/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test","password":"test"}'
```

### 3. Test Protected Endpoint (should return 401)
```bash
curl https://housekeepr.nl/api/bookings
```

### 4. Test User Endpoint (should return 401)
```bash
curl https://housekeepr.nl/api/user
```

## Login with Real Credentials

### Step 1: Get a token (replace with real credentials)
```bash
curl -X POST https://housekeepr.nl/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"your-email@example.com","password":"your-password"}'
```

### Step 2: Save the token
```bash
# Copy the token from the response above
TOKEN="paste_your_token_here"
```

### Step 3: Use the token to access protected endpoints
```bash
# Get user info
curl -H "Authorization: Bearer $TOKEN" https://housekeepr.nl/api/user

# Get bookings
curl -H "Authorization: Bearer $TOKEN" https://housekeepr.nl/api/bookings

# Get cleaning tasks
curl -H "Authorization: Bearer $TOKEN" https://housekeepr.nl/api/cleaning-tasks
```

## Create a Booking via API

```bash
curl -X POST https://housekeepr.nl/api/bookings \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "room_id": 1,
    "check_in_datetime": "2025-12-15 14:00:00",
    "notes": "Created via API"
  }'
```

## Start a Cleaning Task (for cleaners)

```bash
# Start task
curl -X POST https://housekeepr.nl/api/cleaning-tasks/1/start \
  -H "Authorization: Bearer $TOKEN"

# Complete task
curl -X POST https://housekeepr.nl/api/cleaning-tasks/1/complete \
  -H "Authorization: Bearer $TOKEN"
```

## Quick One-Liner Test

Test all endpoints quickly:
```bash
echo "Health:" && curl -s -o /dev/null -w "%{http_code}\n" https://housekeepr.nl/up && \
echo "Login:" && curl -s -o /dev/null -w "%{http_code}\n" -X POST https://housekeepr.nl/api/login && \
echo "Bookings (no auth):" && curl -s -o /dev/null -w "%{http_code}\n" https://housekeepr.nl/api/bookings && \
echo "User (no auth):" && curl -s -o /dev/null -w "%{http_code}\n" https://housekeepr.nl/api/user
```

