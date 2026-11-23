# GBU Smart Grievance Management System — Upgrade Notes

## Confirmed scope
- Retain the existing PHP + MySQL application.
- Use the professional light dashboard design.
- Add real-time notifications using a separate Socket.IO service.
- Add hostel grievances for the 18 official accommodation facilities listed in `database/migrations/001_add_hostel_directory.sql`.
- Keep academic, infrastructure/campus, IT/technical and anonymous grievance capabilities.
- **Mess Management is out of scope**: do not add a Mess Manager role, mess dashboard, mess category for new submissions, mess feedback module, or mess-specific tables.

## Hostel directory
The hostel directory contains 6 girls' hostels, 11 boys' hostels and 1 married research scholars' hostel. The names and official URLs were supplied by the project owner. The directory is stored in MySQL, not duplicated as hardcoded dropdown options.

## Backward-compatibility rules
1. Back up the current database before applying any migration.
2. Do not drop or rename existing tables or columns.
3. Preserve the legacy `grievances.hostel` text column while new code gradually uses `hostel_id`.
4. Keep historical grievance records, including any legacy category values such as `Mess`. Exclude Mess from new-submission options only.
5. Test migrations on a database copy before deployment.
6. Use existing grievance IDs and role/session behavior until replacement code is tested.

## First migration
Run `database/migrations/001_add_hostel_directory.sql` after making a database backup. It creates the hostels directory, seeds all 18 official entries, adds a nullable `grievances.hostel_id`, and backfills matches from existing hostel text. Review unmatched legacy values manually.

## Next implementation steps
1. Complete V1 security fixes (rotate any exposed DB credential, use a least-privilege DB account, environment configuration, replace email allowlist with database permissions).
2. Fix the `responses.remark` vs `response` column mismatch after inspecting every query that writes/reads responses.
3. Add department/officer assignments, grievance history, secure anonymous tracking, audit logs and event outbox through separate additive migrations.
4. Build hostel grievance forms and role-scoped hostel officer dashboard.
5. Add Socket.IO separately from `blockchain/server.js`, with PHP/MySQL as source of truth.
6. Add AI analysis asynchronously; validate AI suggestions and use parameterized SQL.

## Hostel categories and suggested issue types
- Maintenance
- Electricity
- Plumbing
- Water supply
- Cleanliness
- Internet / Wi-Fi
- Room facilities
- Security
- Other

These are proposed grievance types, not claims about official university policy.
