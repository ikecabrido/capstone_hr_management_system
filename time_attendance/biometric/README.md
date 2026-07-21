# Biometric System - Implementation Guide & Reference

**Status**: Architecture & Foundation Phase  
**Capstone Project**: HR Time & Attendance System - School Management Integration

---

## 📋 QUICK START

### What's Ready Now?
✅ Database schema  
✅ Core models & services  
✅ REST API endpoints  
✅ Configuration system  
✅ Logging infrastructure  
✅ Device adapter pattern  

### What to Do Next?
1. **Database Setup**: Run `biometric_architecture.sql` to create tables
2. **Test API**: Use the endpoints to manage devices
3. **Integration**: When NGTeco arrives, implement the NGTecoAdapter

---

## 🗂️ PROJECT STRUCTURE

```
biometric/
├── api/                    # REST API endpoints
│   ├── device_api.php     # Device CRUD operations
│   ├── enrollment_api.php # [TO CREATE]
│   └── sync_api.php       # [TO CREATE]
│
├── config/                 # Configuration
│   └── biometric_config.php # All settings & constants
│
├── controllers/            # Business logic
│   ├── DeviceController.php # [TO CREATE]
│   └── EnrollmentController.php # [TO CREATE]
│
├── models/                 # Data models
│   ├── BiometricDevice.php       ✅ READY
│   ├── BiometricEnrollment.php   ✅ READY
│   ├── BiometricLog.php          ✅ READY
│   └── BiometricVerification.php # [TO CREATE]
│
├── services/              # Core services
│   ├── BiometricService.php      ✅ READY (main sync service)
│   ├── MatchingService.php       # [TO CREATE]
│   ├── VerificationService.php   # [TO CREATE]
│   └── adapters/
│       └── GenericBiometricAdapter.php ✅ READY (with NGTeco template)
│
├── views/                 # UI templates
│   ├── dashboard.php      # [TO CREATE]
│   ├── device_management.php # [TO CREATE]
│   └── employee_enrollment.php # [TO CREATE]
│
├── logs/                  # Log storage
│   └── biometric_sync.log # Auto-created
│
├── BIOMETRIC_IMPLEMENTATION_GUIDE.md # Full documentation
├── README.md             # This file
└── index.php            # [TO CREATE - Main dashboard]
```

---

## 🚀 CURRENT FILES & WHAT THEY DO

### 1. **Models** (Data Access Layer)

#### `BiometricDevice.php`
- Manages biometric device CRUD operations
- Methods: `create()`, `getAll()`, `getById()`, `update()`, `delete()`
- Tracks device status, capacity, enrollment counts
- Tests device connectivity

#### `BiometricEnrollment.php`
- Manages employee biometric enrollments
- Methods: `create()`, `getEmployeeEnrollments()`, `isEnrolled()`
- Tracks enrollment status (pending/enrolled/rejected/expired)
- Gets statistics on total enrollments

#### `BiometricLog.php`
- Stores raw attendance logs from device
- Methods: `create()`, `getUnprocessed()`, `matchEmployee()`, `verify()`
- Detects duplicates within time window
- Tracks log processing status

### 2. **Services** (Business Logic)

#### `BiometricService.php` - **MAIN SERVICE**
Core service that orchestrates the entire system:

```php
// Main functions:
$service = new BiometricService($db);

// Sync device data
$result = $service->syncDevice($device_id);
// Returns: { success, message, processed, failed }

// Verify individual log
$service->verifyLog($log_id);
// Matches employee, checks duplicates, creates attendance record

// Get statistics
$stats = $service->getStatistics();
```

**How it works:**
1. Connects to device via adapter
2. Fetches new logs since last sync
3. Processes each log (match employee)
4. Checks for duplicates
5. Creates attendance records
6. Records sync operation
7. Handles exceptions

#### `GenericBiometricAdapter.php` - Device Communication
Adapter pattern for device-specific implementations:

```php
// Generic adapter (for development)
$adapter = new GenericBiometricAdapter($device);
$logs = $adapter->getNewLogs($since_timestamp);

// NGTeco adapter (TEMPLATE - implement when hardware arrives)
class NGTecoAdapter extends BiometricDeviceAdapter {
    // Implement device-specific methods
}
```

### 3. **Configuration** (`biometric_config.php`)

All settings in one place:

```php
// Device settings
BIOMETRIC_DEVICE_TYPE = 'ngteco'
BIOMETRIC_DEVICE_IP = '192.168.1.100'
BIOMETRIC_DEVICE_PORT = 4370

// Sync settings
BIOMETRIC_SYNC_INTERVAL = 600  // seconds
BIOMETRIC_SYNC_BATCH_SIZE = 100

// Enrollment settings
BIOMETRIC_MAX_RETRY_ATTEMPTS = 3
BIOMETRIC_MIN_CONFIDENCE = 80

// Logging
BIOMETRIC_DEBUG_MODE = true
BIOMETRIC_LOG_FILE = 'logs/biometric_sync.log'
```

### 4. **API** (`device_api.php`)

REST endpoints for device management:

```
GET  /api/device_api.php?action=list
     Returns: All devices

POST /api/device_api.php?action=create
     Body: { device_name, location, ip_address, ... }
     Returns: Created device

GET  /api/device_api.php?action=detail&id=1
     Returns: Device details + performance stats

POST /api/device_api.php?action=update&id=1
     Body: { status, location, notes, ... }
     Returns: Update confirmation

POST /api/device_api.php?action=sync&device_id=1
     Returns: Sync results { success, processed, failed }

POST /api/device_api.php?action=test_connection?ip=192.168.1.100
     Returns: Connection test result
```

---

## 📊 DATABASE SCHEMA OVERVIEW

### Key Tables

**biometric_devices** - Device information
- id, device_name, device_type, ip_address, status, location
- enrolled_count, last_sync, capacity

**biometric_enrollments** - Employee fingerprint mappings
- id, employee_id, device_id, biometric_id, enrollment_status
- quality_score, enrollment_date, verification_count

**biometric_logs** - Raw attendance from device
- id, device_id, biometric_id, log_datetime, punch_type
- is_matched, match_confidence, status (pending/processed/verified)

**biometric_verification** - Processed & verified records
- id, employee_id, device_id, verification_datetime
- verification_status (success/failed/duplicate)

**biometric_attendance_link** - Link to existing attendance system
- biometric_verification_id, employee_id, attendance_type
- Bridges biometric system with existing time_attendance module

**biometric_exceptions** - Error tracking for admin review
- id, employee_id, device_id, exception_type, severity
- resolution_status, description

**biometric_sync_logs** - Sync operation audit trail
- device_id, sync_type, records_synced, records_failed, status

---

## 🔄 DATA FLOW DIAGRAM

```
┌─────────────────────────────────────────────────────┐
│  BIOMETRIC DEVICE (NGTeco - when arrives)          │
└──────────────────┬──────────────────────────────────┘
                   │
        (Ethernet Connection)
                   │
┌──────────────────▼──────────────────────────────────┐
│  BiometricService::syncDevice()                     │
│  - Connects via adapter                            │
│  - Fetches new logs                                │
└──────────────────┬──────────────────────────────────┘
                   │
        Inserts raw logs
                   │
┌──────────────────▼──────────────────────────────────┐
│  biometric_logs table (status=pending)              │
└──────────────────┬──────────────────────────────────┘
                   │
        BiometricService::verifyLog()
                   │
         ┌─────────┴─────────┐
         │                   │
    Match Employee?     Check Duplicates?
         │                   │
         ▼                   ▼
    ┌──────────────────────────────┐
    │  biometric_verification      │
    └──────────────────┬───────────┘
                       │
        Create attendance record
                       │
    ┌──────────────────▼───────────┐
    │  biometric_attendance_link    │
    └──────────────────────────────┘
                       │
         Links to existing attendance system
                       │
         Dashboard shows unified records
```

---

## 🛠️ HOW TO USE (DEVELOPMENT PHASE)

### 1. Run Database Setup
```bash
# Open MySQL client and run:
mysql -u root -p capstone_hr_management_system < biometric_architecture.sql
```

### 2. Test API Endpoints

**List devices:**
```bash
curl "http://localhost/capstone_hr_management_system/biometric/api/device_api.php?action=list"
```

**Create device:**
```bash
curl -X POST "http://localhost/capstone_hr_management_system/biometric/api/device_api.php?action=create" \
  -H "Content-Type: application/json" \
  -d '{
    "device_name": "Main Gate Device",
    "device_type": "fingerprint",
    "manufacturer": "NGTeco",
    "model": "UF100",
    "ip_address": "192.168.1.100",
    "location": "Main Gate",
    "capacity": 100
  }'
```

**Test device connection:**
```bash
curl "http://localhost/capstone_hr_management_system/biometric/api/device_api.php?action=test_connection&ip=192.168.1.100&port=4370"
```

### 3. Check Logs
```bash
tail -f biometric/logs/biometric_sync.log
```

---

## 🎯 NEXT STEPS (What to Build)

### Before Hardware Arrives
- [ ] Enrollment UI (employee_enrollment.php)
- [ ] Device management UI (device_management.php)
- [ ] Sync logs viewer (sync_logs.php)
- [ ] Exception management UI (exceptions.php)
- [ ] Create main dashboard (index.php)
- [ ] Build additional controllers/services

### When Hardware Arrives
1. Get NGTeco documentation
2. Implement `NGTecoAdapter` with actual device API
3. Test sync operations
4. Validate enrollment process
5. Test attendance record creation

### After Testing
1. Integrate with existing time_attendance dashboard
2. Create unified attendance view
3. Deploy to production server
4. User training & documentation

---

## 🔧 CUSTOMIZATION

### Change Device Type
```php
// config/biometric_config.php
define('BIOMETRIC_DEVICE_TYPE', 'ngteco'); // or 'zkteco', 'morpho', etc.
```

### Adjust Sync Interval
```php
// config/biometric_config.php
define('BIOMETRIC_SYNC_INTERVAL', 300); // 5 minutes instead of 10
```

### Set Quality Threshold
```php
// config/biometric_config.php
define('BIOMETRIC_MIN_QUALITY_SCORE', 85); // Stricter quality check
```

### Customize Log Output
```php
// config/biometric_config.php
define('BIOMETRIC_LOG_LEVEL', 'DEBUG'); // More detailed logging
```

---

## 🐛 TROUBLESHOOTING

### Device Connection Issues
```bash
# Check if device is reachable
ping 192.168.1.100

# Test port connectivity
telnet 192.168.1.100 4370

# Check logs
tail -f biometric/logs/biometric_sync.log
```

### Enrollment Not Working
- Verify employee is active in database
- Check if device has available capacity
- Look for quality score failures in logs

### Duplicate Detection
- Adjust `BIOMETRIC_DUPLICATE_WINDOW` if too strict
- Current default: 5 minutes

### Sync Not Happening
- Ensure cron job is configured (when deployed)
- Check device last_sync timestamp
- Verify device status is 'active'

---

## 📚 FILE REFERENCE

| File | Purpose | Status |
|------|---------|--------|
| models/BiometricDevice.php | Device management | ✅ Ready |
| models/BiometricEnrollment.php | Enrollment management | ✅ Ready |
| models/BiometricLog.php | Log storage | ✅ Ready |
| services/BiometricService.php | Core sync logic | ✅ Ready |
| services/adapters/GenericBiometricAdapter.php | Device abstraction | ✅ Ready |
| config/biometric_config.php | Configuration | ✅ Ready |
| api/device_api.php | Device API | ✅ Ready |
| api/enrollment_api.php | Enrollment API | ⏳ To Create |
| api/sync_api.php | Sync API | ⏳ To Create |
| views/device_management.php | Device UI | ⏳ To Create |
| views/employee_enrollment.php | Enrollment UI | ⏳ To Create |
| views/dashboard.php | Main dashboard | ⏳ To Create |

---

## 💡 ARCHITECTURE HIGHLIGHTS

### Adapter Pattern
- Supports multiple device types (NGTeco, ZKTeco, Morpho, Hikvision)
- Easy to add new device support
- Device-specific code isolated in adapters

### Separation of Concerns
- **Models**: Database access
- **Services**: Business logic
- **Controllers**: Request handling
- **API**: External integration
- **Config**: Centralized settings

### Error Handling
- All errors logged to biometric_sync.log
- Exception table tracks issues for admin review
- Graceful degradation on device errors

### Security
- Role-based access control (time/HR admin only)
- Biometric data stored securely
- All operations audited
- Audit trail in sync_logs table

---

## 🎓 CAPSTONE PROJECT BENEFITS

This architecture demonstrates:

1. **Professional System Design**
   - Clear separation of concerns
   - Extensible adapter pattern
   - Robust error handling

2. **Database Design**
   - Normalized schema
   - Proper relationships
   - Audit trail tables

3. **Hardware Integration**
   - Device abstraction layer
   - Multiple device support
   - Ready for hardware implementation

4. **Software Engineering**
   - Configuration management
   - Logging infrastructure
   - API design
   - Model-View-Controller pattern

5. **Scalability**
   - Batch processing
   - Multiple device support
   - Database-driven configuration

Even without physical hardware, demonstrating this complete architecture is impressive for a capstone project!

---

## 📞 SUPPORT

### When NGTeco Hardware Arrives
1. Get device documentation
2. Implement NGTecoAdapter methods
3. Update BIOMETRIC_DEVICE_IP in config
4. Test sync operation

### Integration with Existing System
- BiometricService links to attendance_logs table
- biometric_attendance_link bridges the two systems
- Dashboard can query both systems

---

**Last Updated**: 2024-07-12  
**Version**: 1.0 (Architecture & Foundation)
