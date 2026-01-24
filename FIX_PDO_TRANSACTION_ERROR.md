# Fix: PDO Transaction Rollback Error

## Issue
Fatal error occurred in employee import functionality:
```
PHP Fatal error: Uncaught PDOException: There is no active transaction
in EmpleadosController.php:942
Stack trace: PDO->rollBack()
```

## Root Cause
The `rollBack()` method was being called unconditionally in the catch block, even when:
1. No transaction had been started (exception before `beginTransaction()`)
2. Transaction had already been committed/rolled back
3. Transaction was implicitly committed by `LOCK TABLES` statement

## Solution (Commit 2df98f7)

### 1. Added Transaction State Tracking
```php
$db = null;
$transactionStarted = false;

try {
    $db = Database::getInstance()->getConnection();
    $db->beginTransaction();
    $transactionStarted = true;  // Track transaction state
    // ... rest of code
```

### 2. Safe Rollback with State Check
```php
} catch (Exception $e) {
    if ($db && $transactionStarted) {
        try {
            $db->rollBack();
        } catch (Exception $rollbackException) {
            error_log('Error al hacer rollback: ' . $rollbackException->getMessage());
        }
    }
    echo json_encode(['success' => false, 'message' => 'Error al procesar importación: ' . $e->getMessage()]);
}
```

### 3. Replaced LOCK TABLES with SELECT FOR UPDATE
**Problem:** `LOCK TABLES` causes implicit transaction commit in MySQL, breaking the transaction flow.

**Before:**
```php
$db->exec("LOCK TABLES empleados WRITE");
$lockAcquired = true;
$stmt = $db->query("SELECT MAX(...) FROM empleados");
// ... later ...
$db->exec("UNLOCK TABLES");
```

**After:**
```php
$stmt = $db->query("SELECT MAX(...) FROM empleados FOR UPDATE");
```

**Benefits:**
- `SELECT FOR UPDATE` is transaction-safe (no implicit commit)
- Automatically released when transaction commits/rolls back
- Proper row-level locking instead of table-level
- No need for manual unlock logic

## Impact
- ✅ Employee import now works correctly even when errors occur
- ✅ Proper transaction handling prevents database inconsistencies
- ✅ Better concurrency with row-level locking
- ✅ No more fatal PDO exceptions on rollback

## Testing
- PHP syntax validation: ✅ Passed
- Transaction flow tested for both success and error cases

## Files Modified
- `app/controllers/EmpleadosController.php` - importar() method (lines 802-943)

---
**Date:** 2026-01-24
**Commit:** 2df98f7
