<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RoleColumn;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $roles = Role::withCount('columns')->withCount('users')
                ->with('columns')
                ->orderBy('id')
                ->get()
                ->map(function ($role) {
                    $role->columns_data = $role->columns->map(function ($col) {
                        return [
                            'column_name' => $col->column_name,
                            'column_type' => $col->column_type,
                            'column_length' => $col->column_length,
                            'column_label' => $col->column_label,
                            'is_primary' => (bool) $col->is_primary,
                            'is_unique' => (bool) $col->is_unique,
                            'is_nullable' => (bool) $col->is_nullable,
                            'is_unsigned' => (bool) $col->is_unsigned,
                            'is_auto_increment' => (bool) $col->is_auto_increment,
                            'is_foreign' => (bool) $col->is_foreign,
                            'references_table' => $col->references_table,
                            'references_column' => $col->references_column,
                            'on_delete' => $col->on_delete,
                            'on_update' => $col->on_update,
                            'options' => is_array($col->options) ? implode(', ', $col->options) : null,
                        ];
                    });
                    $role->makeHidden('columns');
                    return $role;
                });

            return response()->json(['data' => $roles]);
        }

        $roles = Role::withCount('columns')->orderBy('id')->get();
        $stats = [
            'total' => $roles->count(),
            'system' => $roles->where('is_system', true)->count(),
            'custom' => $roles->where('is_system', false)->count(),
        ];

        return view('cms.pengguna.roles.index', compact('roles', 'stats'));
    }

    public function create()
    {
        $columnTypes = $this->getColumnTypes();
        $existingRoles = Role::with('columns')->get();
        $menuPermissions = $this->getMenuDefinitions();
        $templatesJson = $existingRoles->keyBy('name')->map(function ($role) {
            return $role->columns->map(function ($column) {
                return [
                    'name' => $column->column_name,
                    'type' => $column->column_type,
                    'length' => $column->column_length,
                    'label' => $column->column_label,
                    'nullable' => (bool) $column->is_nullable,
                    'unique' => (bool) $column->is_unique,
                    'options' => is_array($column->options) ? implode(',', $column->options) : '',
                    'primary' => (bool) $column->is_primary,
                    'foreign' => (bool) $column->is_foreign,
                    'references_table' => $column->references_table,
                    'references_column' => $column->references_column,
                    'on_delete' => $column->on_delete,
                    'on_update' => $column->on_update,
                    'unsigned' => (bool) $column->is_unsigned,
                    'auto_increment' => (bool) $column->is_auto_increment,
                ];
            })->values();
        })->toJson();
        $unsignedTypes = $this->supportsUnsignedTypes();
        $integerTypes = ['tinyint', 'smallint', 'mediumint', 'int', 'integer', 'bigint'];
        return view('cms.pengguna.roles.create', compact('columnTypes', 'templatesJson', 'menuPermissions', 'unsignedTypes', 'integerTypes'));
    }

    public function store(Request $request)
    {
        // Validate all column inputs BEFORE saving — catch enum options errors early
        $columnsInput = $request->input('columns', []);
        foreach ($columnsInput as $idx => $col) {
            $type = $col['column_type'] ?? '';
            $options = trim($col['options'] ?? '');

            // ENUM/SET: values must be comma-separated WITHOUT spaces
            if (in_array($type, ['enum', 'set']) && $options !== '') {
                $parts = explode(',', $options);
                foreach ($parts as $i => $part) {
                    $clean = trim($part);
                    if ($clean === '') {
                        return redirect()->back()->withInput()->with('error', $this->t('cms.roles.column_enum_space_empty', $idx + 1));
                    }
                    if ($clean !== $part) {
                        return redirect()->back()->withInput()->with('error',
                            str_replace([':part', ':clean'], [$part, $clean], $this->t('cms.roles.column_enum_space_in_value', $idx + 1)));
                    }
                    if (!preg_match('/^[a-zA-Z0-9_]+$/', $clean)) {
                        return redirect()->back()->withInput()->with('error',
                            str_replace(':value', $clean, $this->t('cms.roles.column_enum_invalid_char', $idx + 1)));
                    }
                }
            }

            // Column name: no spaces allowed
            $colName = trim($col['column_name'] ?? '');
            if ($colName === '') {
                return redirect()->back()->withInput()->with('error', $this->t('cms.roles.column_name_empty', $idx + 1));
            }
            if (str_contains($colName, ' ')) {
                return redirect()->back()->withInput()->with('error',
                    str_replace(':name', $colName, $this->t('cms.roles.column_name_has_space', $idx + 1)));
            }
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $colName)) {
                return redirect()->back()->withInput()->with('error',
                    str_replace(':name', $colName, $this->t('cms.roles.column_name_invalid_pattern', $idx + 1)));
            }

            // Type-specific validation
            if ($type === 'enum' && empty(trim($col['options'] ?? ''))) {
                return redirect()->back()->withInput()->with('error', $this->t('cms.roles.column_enum_required', $idx + 1));
            }
            if ($type === 'set' && empty(trim($col['options'] ?? ''))) {
                return redirect()->back()->withInput()->with('error', $this->t('cms.roles.column_set_required', $idx + 1));
            }

            // Check enum values for duplicates
            if (in_array($type, ['enum', 'set']) && $options !== '') {
                $vals = array_map('trim', explode(',', $options));
                $uniq = array_unique($vals);
                if (count($vals) !== count($uniq)) {
                    $dupes = array_diff_key($vals, $uniq);
                    return redirect()->back()->withInput()->with('error',
                        str_replace(':values', implode("', '", $dupes), $this->t('cms.roles.column_enum_duplicate', $idx + 1)));
                }
            }
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9_]+$/', Rule::unique('roles', 'name')],
            'label' => ['required', 'string', 'max:100'],
            'is_system' => ['required', 'boolean'],
            'table_name' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_]+$/', Rule::unique('roles', 'table_name')],
            'relation_name' => ['required', 'string', 'max:100', 'regex:/^[a-z][a-zA-Z0-9]*$/', Rule::unique('roles', 'relation_name')],
            'description' => ['nullable', 'string'],
        ], [
            'name.regex' => __('cms.roles.validation_name_regex'),
            'name.unique' => __('cms.roles.validation_name_unique'),
            'table_name.regex' => __('cms.roles.validation_table_name_regex'),
            'table_name.unique' => __('cms.roles.validation_table_name_unique'),
            'table_name.required' => __('cms.roles.validation_table_name_required'),
            'relation_name.regex' => __('cms.roles.validation_relation_name_regex'),
            'relation_name.unique' => __('cms.roles.validation_relation_name_unique'),
            'relation_name.required' => __('cms.roles.validation_relation_name_required'),
        ]);

        DB::transaction(function () use ($request, $data) {
            $role = Role::create($data);

            // Create table automatically
            $this->createRoleTable($data['table_name']);

            // Create default columns
            $this->generateDefaultColumns($role, $request->input('columns', []));

            // Generate model file
            $this->generateRoleModel($data['relation_name'], $data['table_name']);

            // Sync permissions
            $this->syncPermissions($role, $request->input('permissions', []));
        });

        return redirect()
            ->route('cms.pengguna.roles.index')
            ->with('success', __('cms.roles.created_successfully'));
    }

    public function edit(Role $role)
    {
        $columnTypes = $this->getColumnTypes();
        $menuPermissions = $this->getMenuDefinitions();
        $unsignedTypes = $this->supportsUnsignedTypes();
        $integerTypes = ['tinyint', 'smallint', 'mediumint', 'int', 'integer', 'bigint'];

        $role->load('columns', 'permissions');

        // All DB tables for FK dropdown
        $skipTables = [
            'migrations', 'password_reset_tokens', 'personal_access_tokens',
            'failed_jobs', 'job_batches', 'sessions', 'cache', 'notifications',
        ];
        $dbTables = collect(Schema::getTables())
            ->pluck('name')
            ->reject(fn($t) => in_array($t, $skipTables) || str_starts_with($t, '_'))
            ->sort()
            ->values()
            ->map(fn($t) => ['name' => $t])
            ->all();

        // Pre-load columns for all tables referenced by existing FK columns
        $dbColumnsByTable = [];
        foreach ($role->columns->where('is_foreign', true)->pluck('references_table')->filter() as $refTable) {
            if (!isset($dbColumnsByTable[$refTable]) && Schema::hasTable($refTable)) {
                $dbColumnsByTable[$refTable] = collect(Schema::getColumns($refTable))
                    ->map(fn($col) => [
                        'name' => $col['name'],
                        'type' => $col['type_name'] ?? $col['type'],
                    ])->values()->all();
            }
        }

        return view('cms.pengguna.roles.edit', compact('role', 'columnTypes', 'menuPermissions', 'unsignedTypes', 'integerTypes', 'dbTables', 'dbColumnsByTable'));
    }

    /**
     * Manually trigger column sync for a role (called from UI button).
     * Returns JSON for AJAX requests, redirect otherwise.
     */
    public function triggerSync(Role $role, Request $request)
    {
        $this->syncTableColumnsToRole($role);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('cms.roles.columns_synced'),
                'columns_count' => $role->fresh()->columns->count(),
            ]);
        }

        return redirect()
            ->route('cms.pengguna.roles.edit', $role)
            ->with('success', __('cms.roles.columns_synced'));
    }

    public function update(Request $request, Role $role)
    {
        // Validate all column inputs BEFORE syncing — catch enum options errors early
        $columnsInput = $request->input('columns', []);
        foreach ($columnsInput as $idx => $col) {
            $type = $col['column_type'] ?? '';
            $options = trim($col['options'] ?? '');

            // ENUM/SET: values must be comma-separated WITHOUT spaces
            if (in_array($type, ['enum', 'set']) && $options !== '') {
                $parts = explode(',', $options);
                foreach ($parts as $i => $part) {
                    $clean = trim($part);
                    if ($clean === '') {
                        return redirect()->back()->withInput()->with('error', $this->t('cms.roles.column_enum_space_empty', $idx + 1));
                    }
                    if ($clean !== $part) {
                        return redirect()->back()->withInput()->with('error',
                            str_replace([':part', ':clean'], [$part, $clean], $this->t('cms.roles.column_enum_space_in_value', $idx + 1)));
                    }
                    if (!preg_match('/^[a-zA-Z0-9_]+$/', $clean)) {
                        return redirect()->back()->withInput()->with('error',
                            str_replace(':value', $clean, $this->t('cms.roles.column_enum_invalid_char', $idx + 1)));
                    }
                }
            }

            // Column name: no spaces allowed
            $colName = trim($col['column_name'] ?? '');
            if ($colName === '') {
                return redirect()->back()->withInput()->with('error', $this->t('cms.roles.column_name_empty', $idx + 1));
            }
            if (str_contains($colName, ' ')) {
                return redirect()->back()->withInput()->with('error',
                    str_replace(':name', $colName, $this->t('cms.roles.column_name_has_space', $idx + 1)));
            }
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $colName)) {
                return redirect()->back()->withInput()->with('error',
                    str_replace(':name', $colName, $this->t('cms.roles.column_name_invalid_pattern', $idx + 1)));
            }

            // Type-specific validation
            if ($type === 'enum' && empty(trim($col['options'] ?? ''))) {
                return redirect()->back()->withInput()->with('error', $this->t('cms.roles.column_enum_required', $idx + 1));
            }
            if ($type === 'set' && empty(trim($col['options'] ?? ''))) {
                return redirect()->back()->withInput()->with('error', $this->t('cms.roles.column_set_required', $idx + 1));
            }

            // Check enum values for duplicates
            if (in_array($type, ['enum', 'set']) && $options !== '') {
                $vals = array_map('trim', explode(',', $options));
                $uniq = array_unique($vals);
                if (count($vals) !== count($uniq)) {
                    $dupes = array_diff_key($vals, $uniq);
                    return redirect()->back()->withInput()->with('error',
                        str_replace(':values', implode("', '", $dupes), $this->t('cms.roles.column_enum_duplicate', $idx + 1)));
                }
            }
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9_]+$/', Rule::unique('roles', 'name')->ignore($role->id)],
            'label' => ['required', 'string', 'max:100'],
            'is_system' => ['required', 'boolean'],
            'table_name' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_]+$/', Rule::unique('roles', 'table_name')->ignore($role->id)],
            'relation_name' => ['required', 'string', 'max:100', 'regex:/^[a-z][a-zA-Z0-9]*$/', Rule::unique('roles', 'relation_name')->ignore($role->id)],
            'description' => ['nullable', 'string'],
        ], [
            'name.regex' => __('cms.roles.validation_name_regex'),
            'name.unique' => __('cms.roles.validation_name_unique'),
            'table_name.regex' => __('cms.roles.validation_table_name_regex'),
            'table_name.unique' => __('cms.roles.validation_table_name_unique'),
            'table_name.required' => __('cms.roles.validation_table_name_required'),
            'relation_name.regex' => __('cms.roles.validation_relation_name_regex'),
            'relation_name.unique' => __('cms.roles.validation_relation_name_unique'),
            'relation_name.required' => __('cms.roles.validation_relation_name_required'),
        ]);

        $oldTableName = $role->table_name;
        $role->update($data);

        // Rename table if table_name changed (DDL - outside transaction)
        if ($oldTableName !== $data['table_name'] && Schema::hasTable($oldTableName)) {
            Schema::rename($oldTableName, $data['table_name']);
        }

        // Sync columns (contains DDL - must run outside DB::transaction)
        try {
            $this->syncColumns($role, $request->input('columns', []));
        } catch (\Throwable $e) {
            $msg = $this->formatMysqlErrorMessage($e);
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $msg);
        }

        // Update model file if relation_name changed
        $this->generateRoleModel($data['relation_name'], $data['table_name']);

        // Sync permissions
        $this->syncPermissions($role, $request->input('permissions', []));

        return redirect()
            ->route('cms.pengguna.roles.index')
            ->with('success', __('cms.roles.updated_successfully'));
    }

    /**
     * Get available menu definitions for permission management.
     */
    private function getMenuDefinitions(): array
    {
        return [
            'dashboard' => [
                'label' => 'Dashboard',
                'icon' => 'home',
            ],
            'cms' => [
                'label' => 'CMS',
                'children' => [
                    'cms.features' => 'Manajemen Fitur',
                    'cms.footer' => 'Footer',
                    'cms.disclaimer' => 'Disclaimer',
                ],
            ],
            'pengguna' => [
                'label' => 'Pengguna',
                'children' => [
                    'pengguna.users' => 'Data Pengguna',
                    'pengguna.roles' => 'Role Management',
                ],
            ],
        ];
    }

    /**
     * Sync role permissions from request input.
     */
    private function syncPermissions(Role $role, array $permissionsInput): void
    {
        $existingKeys = $role->permissions()->pluck('menu_key')->toArray();
        $updatedKeys = [];

        foreach ($permissionsInput as $menuKey => $canAccess) {
            $canAccessBool = in_array($canAccess, [true, 1, '1', 'on'], true);

            $perm = RolePermission::where('role_id', $role->id)
                ->where('menu_key', $menuKey)
                ->first();

            if ($perm) {
                $perm->update(['can_access' => $canAccessBool]);
            } else {
                RolePermission::create([
                    'role_id' => $role->id,
                    'menu_key' => $menuKey,
                    'can_access' => $canAccessBool,
                ]);
            }

            $updatedKeys[] = $menuKey;
        }

        // Delete removed permissions
        $toDelete = array_diff($existingKeys, $updatedKeys);
        if (!empty($toDelete)) {
            RolePermission::where('role_id', $role->id)
                ->whereIn('menu_key', $toDelete)
                ->delete();
        }
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return redirect()
                ->route('cms.pengguna.roles.index')
                ->with('error', __('cms.roles.cannot_delete_system'));
        }

        $userCount = $role->users()->count();
        if ($userCount > 0) {
            return redirect()
                ->route('cms.pengguna.roles.index')
                ->with('error', __('cms.roles.cannot_delete_has_users', ['count' => $userCount]));
        }

        // Drop table
        if (Schema::hasTable($role->table_name)) {
            Schema::dropIfExists($role->table_name);
        }

        // Delete model file
        $modelPath = app_path('Models/' . ucfirst($role->relation_name) . '.php');
        if (file_exists($modelPath)) {
            unlink($modelPath);
        }

        $role->delete();

        return redirect()
            ->route('cms.pengguna.roles.index')
            ->with('success', __('cms.roles.deleted_successfully'));
    }

    /**
     * Return all database table names (excluding internal/system tables).
     * Used for FK dropdown in role column definition.
     */
    public function getTables()
    {
        $skipTables = [
            'migrations', 'password_reset_tokens', 'personal_access_tokens',
            'failed_jobs', 'job_batches', 'sessions', 'cache', 'notifications',
        ];

        $tables = collect(Schema::getTables())
            ->pluck('name')
            ->reject(fn($t) => in_array($t, $skipTables) || str_starts_with($t, '_'))
            ->sort()
            ->values()
            ->map(fn($t) => ['name' => $t])
            ->all();

        return response()->json($tables);
    }

    /**
     * Return all column names for a given table.
     * Used for FK dropdown in role column definition.
     */
    public function getTableColumns(string $table)
    {
        if (!Schema::hasTable($table)) {
            return response()->json(['error' => 'Table not found'], 404);
        }

        $columns = collect(Schema::getColumns($table))
            ->map(fn($col) => [
                'name' => $col['name'],
                'type' => $col['type_name'] ?? $col['type'],
            ])
            ->values()
            ->all();

        return response()->json($columns);
    }

    /**
     * Sync existing DB table columns to role_columns for a given role.
     * Stores EXACT MySQL DATA_TYPE from INFORMATION_SCHEMA (varchar, int, datetime, enum, etc.)
     * to match phpMyAdmin exactly.
     */
    private function syncTableColumnsToRole(Role $role): void
    {
        if (!Schema::hasTable($role->table_name)) {
            return;
        }

        $connectionName = DB::getDefaultConnection();
        $dbName = DB::connection($connectionName)->getDatabaseName();

        $columns = DB::connection($connectionName)->select("
            SELECT
                COLUMN_NAME,
                DATA_TYPE,
                CHARACTER_MAXIMUM_LENGTH,
                NUMERIC_PRECISION,
                NUMERIC_SCALE,
                IS_NULLABLE,
                COLUMN_TYPE,
                EXTRA,
                COLUMN_DEFAULT,
                COLUMN_COMMENT
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
            ORDER BY ORDINAL_POSITION
        ", [$dbName, $role->table_name]);

        // Get unique index columns for this table
        $uniqueIndexColumns = DB::connection($connectionName)->select("
            SELECT COLUMN_NAME
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND NON_UNIQUE = 0 AND INDEX_NAME != 'PRIMARY'
        ", [$dbName, $role->table_name]);
        $uniqueColumns = array_column($uniqueIndexColumns, 'COLUMN_NAME');

        // Get primary key columns
        $primaryKeyColumns = DB::connection($connectionName)->select("
            SELECT COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = 'PRIMARY'
        ", [$dbName, $role->table_name]);
        $primaryColumns = array_column($primaryKeyColumns, 'COLUMN_NAME');

        // Get foreign key metadata using TABLE_CONSTRAINTS for reliability
        $fkQuery = "
            SELECT
                kcu.COLUMN_NAME,
                kcu.REFERENCED_TABLE_NAME,
                kcu.REFERENCED_COLUMN_NAME,
                tc.CONSTRAINT_NAME,
                rc.UPDATE_RULE,
                rc.DELETE_RULE
            FROM information_schema.KEY_COLUMN_USAGE kcu
            JOIN information_schema.TABLE_CONSTRAINTS tc
                ON tc.CONSTRAINT_SCHEMA = kcu.CONSTRAINT_SCHEMA
                AND tc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME
                AND tc.TABLE_NAME = kcu.TABLE_NAME
                AND tc.CONSTRAINT_TYPE = 'FOREIGN KEY'
            JOIN information_schema.REFERENTIAL_CONSTRAINTS rc
                ON rc.CONSTRAINT_SCHEMA = kcu.CONSTRAINT_SCHEMA
                AND rc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME
            WHERE kcu.TABLE_SCHEMA = DATABASE()
              AND kcu.TABLE_NAME = ?
              AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
        ";

        $foreignKeys = DB::connection($connectionName)->select($fkQuery, [$role->table_name]);

        $foreignMap = [];
        foreach ($foreignKeys as $fk) {
            $fkArr = (array) $fk;
            $foreignMap[$fkArr['COLUMN_NAME']] = [
                'references_table' => $fkArr['REFERENCED_TABLE_NAME'],
                'references_column' => $fkArr['REFERENCED_COLUMN_NAME'],
                'on_update' => strtolower($fkArr['UPDATE_RULE'] ?? 'no action'),
                'on_delete' => strtolower($fkArr['DELETE_RULE'] ?? 'no action'),
            ];
        }

        $sortOrder = 0;

        foreach ($columns as $column) {
            $columnName = $column->COLUMN_NAME;

            // Store EXACT MySQL DATA_TYPE (varchar, int, datetime, enum, etc.)
            $exactDbType = strtolower($column->DATA_TYPE);

            // Determine length based on type
            // Only types that actually accept a length parameter in MySQL should have a length value.
            // Types like TEXT, BLOB, DATE, DATETIME, FLOAT, DOUBLE do NOT take a length.
            $typesWithoutLength = ['text', 'longtext', 'mediumtext', 'tinytext', 'blob', 'longblob', 'mediumblob', 'date', 'datetime', 'timestamp', 'time', 'float', 'double', 'enum', 'set'];
            $columnLength = null;

            if (!in_array($exactDbType, $typesWithoutLength)) {
                if ($column->CHARACTER_MAXIMUM_LENGTH !== null) {
                    $columnLength = (int) $column->CHARACTER_MAXIMUM_LENGTH;
                } elseif (in_array($exactDbType, ['decimal', 'numeric']) && $column->NUMERIC_PRECISION !== null) {
                    $columnLength = (int) $column->NUMERIC_PRECISION;
                }

                // For types like int(11), varchar(255), extract from COLUMN_TYPE if length still null
                if ($columnLength === null && $column->COLUMN_TYPE) {
                    if (preg_match('/\((\d+)(?:,(\d+))?\)/', $column->COLUMN_TYPE, $matches)) {
                        $columnLength = (int) $matches[1];
                    }
                }
            }

            $isNullable = $column->IS_NULLABLE === 'YES';
            $defaultValue = $column->COLUMN_DEFAULT;
            $isPrimary = in_array($columnName, $primaryColumns);
            $isForeign = array_key_exists($columnName, $foreignMap);
            $columnTypeRaw = strtolower((string) ($column->COLUMN_TYPE ?? ''));
            $isUnsigned = str_contains($columnTypeRaw, 'unsigned');

            // AUTO_INCREMENT must follow DB metadata strictly.
            // Do NOT infer from primary key/type, because PK numeric column can be non-auto-increment.
            $extra = strtolower((string) ($column->EXTRA ?? ''));
            $isAutoIncrement = str_contains($extra, 'auto_increment');

            // Extract enum options from COLUMN_TYPE (e.g., enum('a','b','c'))
            $options = null;
            if (in_array($exactDbType, ['enum', 'set']) && $column->COLUMN_TYPE) {
                $options = $this->extractEnumOptions($column->COLUMN_TYPE);
            }

            $exists = RoleColumn::where('role_id', $role->id)
                ->where('column_name', $columnName)
                ->first();

            $data = [
                'column_name' => $columnName,
                'column_type' => $exactDbType, // EXACT MySQL type: varchar, int, datetime, enum, etc.
                'column_label' => ucwords(str_replace('_', ' ', $columnName)),
                'column_length' => $columnLength,
                'is_nullable' => $isNullable,
                'is_unique' => in_array($columnName, $uniqueColumns),
                'is_primary' => $isPrimary,
                'is_foreign' => $isForeign,
                'references_table' => $isForeign ? $foreignMap[$columnName]['references_table'] : null,
                'references_column' => $isForeign ? $foreignMap[$columnName]['references_column'] : null,
                'on_delete' => $isForeign ? $foreignMap[$columnName]['on_delete'] : null,
                'on_update' => $isForeign ? $foreignMap[$columnName]['on_update'] : null,
                'is_unsigned' => $isUnsigned,
                'is_auto_increment' => $isAutoIncrement,
                'default_value' => $defaultValue,
                'options' => $options,
                'sort_order' => $sortOrder++,
            ];

            if ($exists) {
                $exists->update($data);
            } else {
                RoleColumn::create(['role_id' => $role->id, ...$data]);
            }
        }

        // Regenerate model so fillable stays in sync
        $this->generateRoleModel($role->relation_name, $role->table_name);
    }

    private function extractEnumOptions(string $columnType): array
    {
        if (preg_match("/^enum\\((.*)\\)$/i", $columnType, $matches) || preg_match("/^set\\((.*)\\)$/i", $columnType, $matches)) {
            $options = [];
            preg_match_all("/'((?:[^'\\\\]|\\\\.)*)'/", $matches[1], $values);
            foreach ($values[1] as $value) {
                $options[] = stripslashes($value);
            }
            return $options;
        }
        return [];
    }

    private function mapDbTypeToRoleType(string $dbType): string
    {
        return match (strtolower($dbType)) {
            'varchar', 'char', 'text', 'longtext', 'mediumtext', 'tinytext' => 'string',
            'int', 'bigint', 'smallint', 'tinyint', 'mediumint', 'integer' => 'integer',
            'decimal', 'float', 'double', 'numeric' => 'decimal',
            'date' => 'date',
            'datetime', 'timestamp' => 'datetime',
            'time' => 'time',
            'enum', 'set' => 'enum',
            'boolean', 'tinyint(1)' => 'boolean',
            'blob', 'longblob', 'mediumblob' => 'file',
            default => 'string',
        };
    }

    private function extractColumnLength(?string $dbType): ?int
    {
        if (!$dbType) return null;
        if (preg_match('/\((\d+)\)/', $dbType, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    /**
     * Get available column types — matches EXACT MySQL DATA_TYPE names
     * (varchar, int, datetime, enum, etc.) to match phpMyAdmin exactly.
     */
    private function getColumnTypes(): array
    {
        return [
            'varchar'    => 'VARCHAR',
            'char'       => 'CHAR',
            'text'       => 'TEXT',
            'longtext'   => 'LONGTEXT',
            'mediumtext' => 'MEDIUMTEXT',
            'tinytext'   => 'TINYTEXT',
            'int'        => 'INT',
            'bigint'     => 'BIGINT',
            'smallint'   => 'SMALLINT',
            'tinyint'    => 'TINYINT',
            'mediumint'  => 'MEDIUMINT',
            'decimal'    => 'DECIMAL',
            'float'      => 'FLOAT',
            'double'     => 'DOUBLE',
            'date'       => 'DATE',
            'datetime'   => 'DATETIME',
            'timestamp'  => 'TIMESTAMP',
            'time'       => 'TIME',
            'enum'       => 'ENUM',
            'set'        => 'SET',
            'boolean'    => 'BOOLEAN',
            'blob'       => 'BLOB',
            'longblob'   => 'LONGBLOB',
            'mediumblob' => 'MEDIUMBLOB',
        ];
    }

    /**
     * Create the role profile table dynamically.
     */
    private function createRoleTable(string $tableName): void
    {
        if (Schema::hasTable($tableName)) {
            return;
        }

        Schema::create($tableName, function ($table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Generate default columns for a role.
     */
    private function generateDefaultColumns(Role $role, array $columnsInput): void
    {
        // If columns provided from form, use them
        if (!empty($columnsInput)) {
            $this->syncColumns($role, $columnsInput);
            return;
        }

        // Otherwise, create minimal default columns
        $defaults = [
            ['column_name' => 'nomor_kartu_identitas', 'column_type' => 'string', 'column_length' => 25, 'column_label' => 'Nomor Kartu Identitas', 'is_nullable' => true, 'is_unique' => true],
            ['column_name' => 'alamat', 'column_type' => 'text', 'column_length' => null, 'column_label' => 'Alamat', 'is_nullable' => true, 'is_unique' => false],
            ['column_name' => 'nomor_whatsapp', 'column_type' => 'string', 'column_length' => 20, 'column_label' => 'Nomor WhatsApp', 'is_nullable' => true, 'is_unique' => true],
        ];

        foreach ($defaults as $index => $col) {
            $column = RoleColumn::create([
                'role_id' => $role->id,
                'sort_order' => $index,
                ...$col,
            ]);

            $this->addColumnToTable($role->table_name, $column);
        }
    }

    /**
     * Sync columns for a role.
     */
    private function syncColumns(Role $role, array $columnsInput): void
    {
        $role->load('columns');
        $existingIds = $role->columns->pluck('id')->toArray();
        $updatedIds = [];

        $columnErrors = [];

        foreach ($columnsInput as $index => $input) {
            $toBool = static fn($v): bool => in_array($v, [true, 1, '1', 'on', 'true', 'yes'], true);

            $columnName = $input['column_name'] ?? ('column_index_' . $index);

            try {
                $columnData = [
                    'column_name' => $input['column_name'],
                    'column_type' => $input['column_type'],
                    'column_label' => $input['column_label'],
                    'column_length' => $input['column_length'] != '' ? (int) $input['column_length'] : null,
                    'is_nullable' => $toBool($input['is_nullable'] ?? '0'),
                    'is_unique' => $toBool($input['is_unique'] ?? '0'),
                    'is_primary' => $toBool($input['is_primary'] ?? '0'),
                    'is_foreign' => $toBool($input['is_foreign'] ?? '0'),
                    'references_table' => $input['references_table'] ?: null,
                    'references_column' => $input['references_column'] ?: null,
                    'on_delete' => ($input['on_delete'] ?? '') !== '' ? $input['on_delete'] : null,
                    'on_update' => ($input['on_update'] ?? '') !== '' ? $input['on_update'] : null,
                    'is_unsigned' => $toBool($input['is_unsigned'] ?? '0'),
                    'is_auto_increment' => $toBool($input['is_auto_increment'] ?? '0'),
                    'default_value' => $input['default_value'] ?? null,
                    'options' => !empty($input['options']) ? explode(',', $input['options']) : null,
                    'sort_order' => $index,
                ];

                if (!empty($input['id'])) {
                    // Update existing — apply in-memory first, try SQL, then persist
                    $column = RoleColumn::find($input['id']);
                    if ($column) {
                        $oldName = $column->column_name;
                        foreach ($columnData as $k => $v) {
                            $column->{$k} = $v;
                        }
                        try {
                            $this->alterColumn($role->table_name, $oldName, $column);
                        } catch (\Throwable $e) {
                            $column->refresh();
                            throw $e;
                        }
                        $column->save();
                        $updatedIds[] = $column->id;
                    }
                } else {
                    // Create new — build object in-memory, try SQL, then persist
                    $column = new RoleColumn(['role_id' => $role->id, ...$columnData]);
                    try {
                        $this->addColumnToTable($role->table_name, $column);
                    } catch (\Throwable $e) {
                        throw $e;
                    }
                    $column->save();
                    $updatedIds[] = $column->id;
                }
            } catch (\Throwable $e) {
                $columnErrors[] = $this->formatColumnErrorMessage($columnName, $e);
            }
        }

        // If any error happened during update/create phase, abort before destructive delete.
        if (!empty($columnErrors)) {
            throw new \RuntimeException(implode("\n", $columnErrors));
        }

        // Delete removed columns only when sync phase is fully successful.
        $toDelete = array_diff($existingIds, $updatedIds);

        foreach ($toDelete as $id) {
            $column = RoleColumn::find($id);
            if (!$column) {
                continue;
            }

            try {
                $this->dropColumnFromTable($role->table_name, $column->column_name);
                $column->delete();
            } catch (\Throwable $e) {
                $columnErrors[] = $this->formatColumnErrorMessage($column->column_name, $e);
            }
        }

        if (!empty($columnErrors)) {
            throw new \RuntimeException(implode("\n", $columnErrors));
        }

        // Reorder table columns physically to match sort_order
        try {
            $this->reorderTableColumns($role->table_name, $role);
        } catch (\Throwable $e) {
            $columnErrors[] = $this->formatColumnErrorMessage('(reorder)', $e);
        }

        if (!empty($columnErrors)) {
            throw new \RuntimeException(implode("\n", $columnErrors));
        }
    }

    /**
     * Add a column to the database table.
     */
    private function addColumnToTable(string $tableName, RoleColumn $column): void
    {
        if (!Schema::hasTable($tableName)) {
            return;
        }

        if (Schema::hasColumn($tableName, $column->column_name)) {
            return;
        }

        // Use raw SQL for column definition + FK constraint (Laravel Blueprint drops ON UPDATE/DELETE)
        $sqlTail = $this->buildColumnSqlTail($column);

        try {
            DB::statement("ALTER TABLE `{$tableName}` ADD COLUMN `{$column->column_name}` {$sqlTail}");
        } catch (\Throwable $e) {
            Log::error('Failed to add column', [
                'table' => $tableName,
                'column' => $column->column_name,
                'sql_tail' => $sqlTail,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        // Add FK constraint separately so ON UPDATE/DELETE are preserved
        if ($column->is_foreign && $column->references_table && $column->references_column) {
            $this->addForeignKeyConstraint($tableName, $column);
        }
    }

    /**
     * Drop a column from the database table.
     */
    private function dropColumnFromTable(string $tableName, string $columnName): void
    {
        if (!Schema::hasTable($tableName)) {
            return;
        }

        if (!Schema::hasColumn($tableName, $columnName)) {
            return;
        }

        // Drop FK constraint first before dropping column
        $this->dropForeignKeyForColumn($tableName, $columnName);

        Schema::table($tableName, function ($table) use ($columnName) {
            $table->dropColumn($columnName);
        });
    }

    /**
     * Alter an existing column in the database table.
     */
    private function alterColumn(string $tableName, string $oldName, RoleColumn $column): void
    {
        if (!Schema::hasTable($tableName)) {
            return;
        }

        if (!Schema::hasColumn($tableName, $oldName)) {
            $this->addColumnToTable($tableName, $column);
            return;
        }

        // For simple rename or type change, we need to use raw SQL for MySQL
        $this->modifyColumn($tableName, $oldName, $column);
    }

    /**
     * Define column structure for Blueprint using EXACT MySQL types.
     */
    private function defineColumn($table, RoleColumn $column, string $action = 'add')
    {
        $method = $action === 'add' ? 'addColumn' : 'column';
        $type = strtolower($column->column_type);

        $col = match ($type) {
            'varchar'  => $table->string($column->column_name, $column->column_length ?? 255),
            'char'     => $table->char($column->column_name, $column->column_length ?? 255),
            'text'     => $table->text($column->column_name),
            'longtext' => $table->longText($column->column_name),
            'mediumtext' => $table->mediumText($column->column_name),
            'tinytext' => $table->tinyText($column->column_name),
            'int'      => $table->integer($column->column_name, $column->is_auto_increment, $column->is_unsigned),
            'bigint'   => $table->bigInteger($column->column_name, $column->is_auto_increment, $column->is_unsigned),
            'smallint' => $table->smallInteger($column->column_name, $column->is_auto_increment, $column->is_unsigned),
            'tinyint'  => $table->tinyInteger($column->column_name, $column->is_auto_increment, $column->is_unsigned),
            'mediumint' => $table->mediumInteger($column->column_name, $column->is_auto_increment, $column->is_unsigned),
            'decimal'  => $table->decimal($column->column_name, $column->column_length ?? 10, 2),
            'float'    => $table->float($column->column_name),
            'double'   => $table->double($column->column_name),
            'date'     => $table->date($column->column_name),
            'datetime' => $table->dateTime($column->column_name),
            'timestamp'=> $table->timestamp($column->column_name),
            'time'     => $table->time($column->column_name),
            'enum'     => $table->enum($column->column_name, $column->options ?? ['option_1']),
            'set'      => $table->set($column->column_name, $column->options ?? ['option_1']),
            'boolean'  => $table->boolean($column->column_name),
            'blob'     => $table->binary($column->column_name),
            'longblob' => $table->binary($column->column_name),
            'mediumblob' => $table->binary($column->column_name),
            // Backwards compatibility with old mapped types
            'string'   => $table->string($column->column_name, $column->column_length ?? 255),
            'integer'  => $table->integer($column->column_name, $column->is_auto_increment, $column->is_unsigned),
            'file'     => $table->string($column->column_name, 255),
            default    => $table->string($column->column_name, 255),
        };

        if ($column->is_nullable) {
            $col->nullable();
        }

        if ($column->default_value !== null && $column->default_value !== '') {
            $col->default($column->default_value);
        }

        if ($column->is_unique) {
            $table->unique($column->column_name);
        }

        if ($column->is_primary) {
            $table->primary($column->column_name);
        }

        if ($column->is_foreign && $column->references_table && $column->references_column) {
            $fk = $table->foreign($column->column_name)
                ->references($column->references_column)
                ->on($column->references_table);

            if ($column->on_delete) {
                $fk->onDelete($column->on_delete);
            }

            if ($column->on_update) {
                $fk->onUpdate($column->on_update);
            }
        }

        return $col;
    }

    /**
     * Modify column using raw SQL for complex changes.
     * Uses EXACT MySQL types from role_columns.
     */
    private function buildColumnSqlTail(RoleColumn $column): string
    {
        $integerTypes = ['tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'integer'];
        $typeName = strtolower((string) $column->column_type);

        $type = $this->buildMysqlTypeDefinition($column);
        $nullable = $column->is_nullable ? "NULL" : "NOT NULL";
        $default = '';
        $autoIncrement = '';

        if ($column->default_value !== null && $column->default_value !== '') {
            $escapedDefault = str_replace("'", "''", (string) $column->default_value);
            $default = "DEFAULT '{$escapedDefault}'";
        }

        if ($column->default_value === null && $column->is_nullable) {
            $default = "DEFAULT NULL";
        }

        if ($column->is_auto_increment && in_array($typeName, $integerTypes, true)) {
            $autoIncrement = "AUTO_INCREMENT";
            $default = '';
            $nullable = "NOT NULL";
        }

        return trim(preg_replace('/\s+/', ' ', implode(' ', array_filter([$type, $nullable, $default, $autoIncrement]))));
    }

    private function modifyColumn(string $tableName, string $oldName, RoleColumn $column): void
    {
        $this->validateMysqlColumnRules($column);

        // Drop existing FK constraint for this column before modifying (FK is separate from column definition)
        $this->dropForeignKeyForColumn($tableName, $column->column_name);

        $sqlTail = $this->buildColumnSqlTail($column);

        try {
            if ($oldName !== $column->column_name) {
                DB::statement("ALTER TABLE `{$tableName}` CHANGE `{$oldName}` `{$column->column_name}` {$sqlTail}");
            } else {
                DB::statement("ALTER TABLE `{$tableName}` MODIFY COLUMN `{$column->column_name}` {$sqlTail}");
            }
        } catch (\Throwable $e) {
            Log::error('Failed to alter role column', [
                'table' => $tableName,
                'old_name' => $oldName,
                'new_name' => $column->column_name,
                'type' => $column->column_type,
                'is_unsigned' => (bool) $column->is_unsigned,
                'is_nullable' => (bool) $column->is_nullable,
                'is_auto_increment' => (bool) $column->is_auto_increment,
                'default_value' => $column->default_value,
                'sql_tail' => $sqlTail,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }

        // Re-add FK constraint after column modification if this column has FK
        if ($column->is_foreign && $column->references_table && $column->references_column) {
            $this->addForeignKeyConstraint($tableName, $column);
        }
    }

    /**
     * Drop any existing FK constraint on a column so column can be modified without conflict.
     * Uses TABLE_CONSTRAINTS to find the actual constraint name regardless of Laravel naming convention.
     */
    private function dropForeignKeyForColumn(string $tableName, string $columnName): void
    {
        $constraints = DB::select("
            SELECT tc.CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS tc
            JOIN information_schema.KEY_COLUMN_USAGE kcu
                ON kcu.CONSTRAINT_SCHEMA = tc.CONSTRAINT_SCHEMA
                AND kcu.CONSTRAINT_NAME = tc.CONSTRAINT_NAME
                AND kcu.TABLE_NAME = tc.TABLE_NAME
            WHERE tc.TABLE_SCHEMA = DATABASE()
              AND tc.TABLE_NAME = ?
              AND kcu.COLUMN_NAME = ?
              AND tc.CONSTRAINT_TYPE = 'FOREIGN KEY'
        ", [$tableName, $columnName]);

        foreach ($constraints as $c) {
            try {
                DB::statement("ALTER TABLE `{$tableName}` DROP FOREIGN KEY `{$c->CONSTRAINT_NAME}`");
                Log::info('FK dropped', ['constraint' => $c->CONSTRAINT_NAME, 'table' => $tableName, 'column' => $columnName]);
            } catch (\Throwable $e) {
                Log::warning('Failed to drop FK', [
                    'constraint' => $c->CONSTRAINT_NAME,
                    'table' => $tableName,
                    'column' => $columnName,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Add FK constraint for a column using raw SQL so ON UPDATE/DELETE are preserved.
     */
    private function addForeignKeyConstraint(string $tableName, RoleColumn $column): void
    {
        $constraintName = "fk_{$tableName}_{$column->column_name}";
        $onDelete = $column->on_delete ? strtoupper(str_replace(' ', '_', $column->on_delete)) : 'NO ACTION';
        $onUpdate = $column->on_update ? strtoupper(str_replace(' ', '_', $column->on_update)) : 'NO ACTION';

        $sql = "ALTER TABLE `{$tableName}` ADD CONSTRAINT `{$constraintName}` FOREIGN KEY (`{$column->column_name}`) REFERENCES `{$column->references_table}`(`{$column->references_column}`) ON DELETE {$onDelete} ON UPDATE {$onUpdate}";

        try {
            DB::statement($sql);
        } catch (\Throwable $e) {
            // If constraint name already exists, try with a unique suffix
            if (str_contains($e->getMessage(), 'Duplicate foreign key constraint name')) {
                $constraintNameAlt = $constraintName . '_' . time();
                $sqlAlt = "ALTER TABLE `{$tableName}` ADD CONSTRAINT `{$constraintNameAlt}` FOREIGN KEY (`{$column->column_name}`) REFERENCES `{$column->references_table}`(`{$column->references_column}`) ON DELETE {$onDelete} ON UPDATE {$onUpdate}";
                try {
                    DB::statement($sqlAlt);
                    Log::info('FK added with alt name', ['constraint' => $constraintNameAlt]);
                    return;
                } catch (\Throwable $e2) {
                    Log::error('Failed to add FK (alt name)', ['error' => $e2->getMessage(), 'sql' => $sqlAlt]);
                    throw $e2;
                }
            }
            Log::error('Failed to add FK constraint', [
                'table' => $tableName,
                'column' => $column->column_name,
                'on_update' => $onUpdate,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function reorderTableColumns(string $tableName, Role $role): void
    {
        $columns = $role->columns()->orderBy('sort_order')->get();
        if ($columns->count() <= 1) return;

        foreach ($columns as $i => $column) {
            if (!Schema::hasColumn($tableName, $column->column_name)) continue;

            $sqlTail = $this->buildColumnSqlTail($column);

            try {
                if ($i === 0) {
                    DB::statement("ALTER TABLE `{$tableName}` MODIFY COLUMN `{$column->column_name}` {$sqlTail} FIRST");
                } else {
                    $prevCol = $columns[$i - 1];
                    DB::statement("ALTER TABLE `{$tableName}` MODIFY COLUMN `{$column->column_name}` {$sqlTail} AFTER `{$prevCol->column_name}`");
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to reorder column', [
                    'table' => $tableName,
                    'column' => $column->column_name,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        }
    }

    private function validateMysqlColumnRules(RoleColumn $column): void
    {
        $type = strtolower((string) $column->column_type);
        $unsignedSupported = $this->supportsUnsigned($type);
        $integerTypes = ['tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'integer'];

        if ($column->is_unsigned && !$unsignedSupported) {
            throw new \InvalidArgumentException(__('cms.roles.error_unsigned_not_supported', ['column' => $column->column_name, 'type' => $type]));
        }

        if ($column->is_auto_increment && !in_array($type, $integerTypes, true)) {
            throw new \InvalidArgumentException(__('cms.roles.error_auto_increment_integer_only', ['column' => $column->column_name]));
        }

        if ($column->is_auto_increment && $column->is_nullable) {
            throw new \InvalidArgumentException(__('cms.roles.error_auto_increment_not_null', ['column' => $column->column_name]));
        }
    }

    /**
     * Short-hand for translating a role error message with :index replacement.
     */
    private function t(string $key, int|string $index): string
    {
        return str_replace(':index', $index, __($key));
    }

    private function formatMysqlErrorMessage(\Throwable $e): string
    {
        $base = $e->getMessage();

        if ($e instanceof QueryException && isset($e->errorInfo[1])) {
            $mysqlCode = (int) $e->errorInfo[1];
            $friendly = $this->mapMysqlErrorCodeToFriendlyMessage($mysqlCode, null, $base);
            return __('cms.roles.error_mysql_prefix', ['code' => $mysqlCode, 'message' => $friendly]);
        }

        if (str_contains($base, 'SQLSTATE')) {
            return __('cms.roles.error_mysql_prefix', ['code' => '?', 'message' => $base]);
        }

        return $base;
    }

    private function formatColumnErrorMessage(string $columnName, \Throwable $e): string
    {
        $raw = $e->getMessage();

        if ($e instanceof QueryException && isset($e->errorInfo[1])) {
            $mysqlCode = (int) $e->errorInfo[1];
            $friendly = $this->mapMysqlErrorCodeToFriendlyMessage($mysqlCode, $columnName, $raw);
            return __('cms.roles.error_column_prefix', ['column' => $columnName, 'code' => $mysqlCode, 'message' => $friendly]);
        }

        if (str_starts_with($raw, __('cms.roles.column_name_prefix', ['column' => $columnName])) || str_starts_with($raw, "Column '{$columnName}':")) {
            return $raw;
        }

        return __('cms.roles.error_column_prefix', ['column' => $columnName, 'code' => '?', 'message' => $raw]);
    }

    private function mapMysqlErrorCodeToFriendlyMessage(int $code, ?string $columnName, string $raw): string
    {
        return match ($code) {
            1064 => __('cms.roles.mysql_1064'),
            1264 => __('cms.roles.mysql_1264'),
            1366 => __('cms.roles.mysql_1366'),
            1364 => __('cms.roles.mysql_1364'),
            1048 => __('cms.roles.mysql_1048'),
            1062 => __('cms.roles.mysql_1062'),
            1452 => __('cms.roles.mysql_1452'),
            1451 => __('cms.roles.mysql_1451'),
            1075 => __('cms.roles.mysql_1075'),
            1171 => __('cms.roles.mysql_1171'),
            default => $raw,
        };
    }

    /**
     * Build exact MySQL type definition string (e.g., varchar(255), int(11), decimal(10,2)).
     */
    private function buildMysqlTypeDefinition(RoleColumn $column): string
    {
        $type = strtolower((string) $column->column_type);
        $length = $column->column_length;
        $unsigned = $this->supportsUnsigned($type) && $column->is_unsigned ? ' UNSIGNED' : '';

        return match ($type) {
            'varchar'    => "VARCHAR(" . ($length ?: 255) . ")",
            'char'       => "CHAR(" . ($length ?: 255) . ")",
            'text'       => "TEXT",
            'longtext'   => "LONGTEXT",
            'mediumtext' => "MEDIUMTEXT",
            'tinytext'   => "TINYTEXT",
            'int', 'integer' => "INT" . ($length ? "({$length})" : "") . $unsigned,
            'bigint'     => "BIGINT" . ($length ? "({$length})" : "") . $unsigned,
            'smallint'   => "SMALLINT" . ($length ? "({$length})" : "") . $unsigned,
            'tinyint'    => "TINYINT" . ($length ? "({$length})" : "") . $unsigned,
            'mediumint'  => "MEDIUMINT" . ($length ? "({$length})" : "") . $unsigned,
            'decimal'    => "DECIMAL(" . ($length ?: 10) . ",2)" . $unsigned,
            'float'      => "FLOAT" . $unsigned,
            'double'     => "DOUBLE" . $unsigned,
            'date'       => "DATE",
            'datetime'   => "DATETIME",
            'timestamp'  => "TIMESTAMP",
            'time'       => "TIME",
            'enum'       => $column->options
                ? "ENUM(" . implode(',', array_map(fn($o) => "'" . str_replace("'", "\\'", (string) $o) . "'", $column->options)) . ")"
                : "ENUM('option_1')",
            'set'        => $column->options
                ? "SET(" . implode(',', array_map(fn($o) => "'" . str_replace("'", "\\'", (string) $o) . "'", $column->options)) . ")"
                : "SET('option_1')",
            'boolean'    => "TINYINT(1)" . ($column->is_unsigned ? " UNSIGNED" : ""),
            'blob'       => "BLOB",
            'longblob'   => "LONGBLOB",
            'mediumblob' => "MEDIUMBLOB",
            // Backwards compatibility
            'string'     => "VARCHAR(" . ($length ?: 255) . ")",
            'file'       => "VARCHAR(255)",
            default      => "VARCHAR(255)",
        };
    }

    private function supportsUnsignedTypes(): array
    {
        return ['tinyint', 'smallint', 'mediumint', 'int', 'integer', 'bigint', 'decimal', 'float', 'double', 'boolean'];
    }

    private function supportsUnsigned(string $type): bool
    {
        return in_array(strtolower($type), $this->supportsUnsignedTypes(), true);
    }

    /**
     * Generate model file for the role.
     */
    private function generateRoleModel(string $relationName, string $tableName): void
    {
        $modelName = ucfirst($relationName);
        $modelPath = app_path("Models/{$modelName}.php");

        // Get columns from role_columns table
        $role = Role::where('relation_name', $relationName)->first();

        // Build fillable dynamically from all role_columns.
        $fillable = ['user_id'];

        if ($role) {
            $columns = $role->columns->pluck('column_name')->toArray();
            foreach ($columns as $col) {
                $fillable[] = $col;
            }
        }

        $fillable = array_values(array_unique($fillable));
        $fillableQuoted = array_map(fn($f) => "'{$f}'", $fillable);
        $fillableStr = implode(",\n        ", $fillableQuoted);

        $content = <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class {$modelName} extends Model
{
    protected \$table = '{$tableName}';

    protected \$fillable = [
        {$fillableStr}
    ];

    public function user()
    {
        return \$this->belongsTo(User::class);
    }
}
PHP;

        file_put_contents($modelPath, $content);
    }
}
