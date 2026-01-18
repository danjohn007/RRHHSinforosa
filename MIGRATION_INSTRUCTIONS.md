# Employee Improvements Migration Instructions

## Overview
This migration adds employee code (codigo_empleado), branch assignment (sucursal_id), and shift assignment (turno_id) to the employees table.

## Pre-Migration Steps

1. **Backup your database** before running this migration:
   ```bash
   mysqldump -u username -p rrhh_sinforosa > backup_$(date +%Y%m%d_%H%M%S).sql
   ```

2. **Ensure you have sucursales and turnos tables** in your database with active records.

## Migration Steps

1. Run the migration SQL script:
   ```bash
   mysql -u username -p rrhh_sinforosa < migration_employee_improvements.sql
   ```

2. The migration will:
   - Add `codigo_empleado` VARCHAR(6) field (6-digit employee code)
   - Add `sucursal_id` INT field (branch assignment)
   - Add `turno_id` INT field (shift assignment)
   - Add foreign keys for sucursal_id and turno_id
   - Add index for codigo_empleado for fast lookups
   - Generate codigo_empleado values for existing employees (format: 183XXX)

## Post-Migration Steps

1. **Verify the migration**:
   ```sql
   SELECT id, numero_empleado, codigo_empleado, sucursal_id, turno_id 
   FROM empleados 
   LIMIT 10;
   ```

2. **Update existing employee records**:
   - Assign branches (sucursal_id) to employees if not already set
   - Assign shifts (turno_id) to employees if not already set
   - Verify codigo_empleado values are correct (6 digits)

3. **Create uploads directory** for employee documents:
   ```bash
   mkdir -p uploads/documentos_empleados
   chmod 755 uploads/documentos_empleados
   ```

## Employee Code Format

The system uses a 6-digit employee code format:
- Default format: `183XXX` where XXX is a sequential number
- Example: `183001`, `183002`, `183758`
- The prefix `183` can be customized by modifying the migration script

## Troubleshooting

### Issue: Foreign key constraint fails
- **Cause**: Referenced tables (sucursales or turnos) don't exist
- **Solution**: Create the required tables before running this migration

### Issue: Duplicate codigo_empleado
- **Cause**: Multiple employees generated with same code
- **Solution**: Manually update duplicate codes to unique values

### Issue: NULL values in codigo_empleado
- **Cause**: Generation logic failed for some records
- **Solution**: Run manual UPDATE query to generate codes for affected records

## Rollback (if needed)

If you need to rollback this migration:

```sql
USE rrhh_sinforosa;

-- Remove foreign keys
ALTER TABLE empleados DROP FOREIGN KEY fk_empleado_sucursal;
ALTER TABLE empleados DROP FOREIGN KEY fk_empleado_turno;

-- Remove indexes
ALTER TABLE empleados DROP INDEX idx_codigo_empleado;

-- Remove columns
ALTER TABLE empleados DROP COLUMN codigo_empleado;
ALTER TABLE empleados DROP COLUMN sucursal_id;
ALTER TABLE empleados DROP COLUMN turno_id;
```

## New Features Enabled

After this migration, the following features are enabled:

1. **Employee Code System**: 6-digit codes for attendance tracking
2. **Branch Assignment**: Employees can be assigned to specific branches
3. **Shift Assignment**: Employees can be assigned to specific work shifts
4. **Enhanced Edit Form**: All employee fields can be edited
5. **Document Upload**: Multiple documents can be uploaded per employee
6. **Logo in Documents**: System logo appears in work certificates and recommendation letters
7. **Employee Code Display**: 6-digit code shown in employee profile

## Support

If you encounter any issues during migration, please:
1. Check the error logs
2. Verify database permissions
3. Ensure all required tables exist
4. Contact the development team with the error message
