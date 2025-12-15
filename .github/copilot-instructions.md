# GitHub Copilot Instructions - KMS (Kita Membantu Sesama)

## Architecture Overview

**Hybrid Laravel + Vue SPA**: Backend API (Laravel 12) serves JSON to a Vue 3 SPA admin panel (`public/admin/`). Frontend routes are managed by Vue Router at `/admin/*`, while Laravel handles auth and API endpoints under `/admin/api/*`.

**Critical routing split**:
- Public routes: `/admin/signin`, `/admin/signup` serve the Vue SPA (no auth)
- Protected admin routes: `/admin/{any}` require `auth` middleware
- API routes: `/admin/api/*` use `web` + `auth` middleware, return JSON

**Key design decisions**:
- UUIDs for all resource IDs (models use `HasUuids` trait)
- Soft deletes everywhere (`SoftDeletes` trait)
- Audit trails via `created_by`, `updated_by`, `deleted_by` foreign keys to `users` table
- Permission-based menu filtering via `MenuService::getFilteredMenu()` using Spatie Laravel Permission

## Development Workflow

**Frontend build** (required after Vue changes):
```bash
npm run build
```

**Backend setup**:
```bash
composer install
php artisan migrate --seed
php artisan serve  # Serves on http://localhost:8000
```

**Full dev environment** (from root):
```bash
composer dev  # Runs server + queue + logs + vite concurrently
```

## Code Patterns

### Backend: Resource CRUD Controllers

All controllers follow this standard structure (see `TipeDonaturController`, `TipeAbsensiController`):

```php
public function index(Request $request) {
    $query = Model::with(['creator', 'updater']);
    if ($request->filled('search')) {
        $query->where('field', 'like', "%{$search}%");
    }
    $items = $query->orderBy('created_at', 'desc')->paginate($perPage);
    return response()->json([
        'success' => true,
        'data' => $items->items(),
        'pagination' => [/* current_page, last_page, per_page, total */]
    ]);
}

public function store(Request $request) {
    $data = $request->only([...]);
    $data['created_by'] = auth()->id();
    Model::create($data);
    return response()->json(['success' => true, 'message' => '...'], 201);
}
```

**Validation errors return**:
```json
{"success": false, "message": "Validation failed", "errors": {"field": ["error message"]}}
```

### Frontend: AG Grid List Views

Standard pattern in `public/admin/src/views/Administrasi/*.vue`:

1. **Import AG Grid**:
```typescript
import { AgGridVue } from 'ag-grid-vue3'
import 'ag-grid-community/styles/ag-grid.css'
import 'ag-grid-community/styles/ag-theme-alpine.css'
```

2. **CSRF token helper**:
```typescript
const getCsrfToken = (): string => {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
}
```

3. **Fetch pattern** (with debounced search):
```typescript
const fetchData = async () => {
  const url = `/admin/api/resource${search ? '?search=' + search : ''}`
  const response = await fetch(url, {
    headers: {
      'X-CSRF-TOKEN': getCsrfToken(),
      'X-Requested-With': 'XMLHttpRequest'
    },
    credentials: 'same-origin'
  })
  const result = await response.json()
  if (result.success) rowData.value = result.data
}

watch(filterField, () => {
  clearTimeout(filterTimeout)
  filterTimeout = setTimeout(fetchData, 500)
})
```

4. **Action buttons** (edit/delete in cell renderer):
```typescript
cellRenderer: (params: any) => {
  const div = document.createElement('div')
  div.className = 'flex items-center gap-3'
  
  const editBtn = document.createElement('button')
  editBtn.className = 'flex items-center justify-center w-8 h-8 rounded-lg text-brand-500 hover:bg-brand-50...'
  editBtn.onclick = () => router.push(`/path/${params.data.id}/edit`)
  
  div.appendChild(editBtn)
  // Similar for deleteBtn
  return div
}
```

### Frontend: Form Views

Pattern in `*Form.vue` files:

```typescript
const formData = reactive({ nama: '', ... })
const formErrors = reactive({ nama: '', ... })

const handleSave = async () => {
  const url = isEditMode.value ? `/admin/api/resource/${id}` : '/admin/api/resource'
  const method = isEditMode.value ? 'PUT' : 'POST'
  
  const response = await fetch(url, {
    method,
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': await getCsrfToken()
    },
    body: JSON.stringify(payload)
  })
  
  const result = await response.json()
  if (result.success) {
    toast.success('...')
    router.push('/list-page')
  } else if (result.errors) {
    // Map errors to formErrors reactive object
    formErrors.nama = result.errors.nama?.[0]
  }
}

// Clear errors on input change
watch(() => formData.nama, () => { formErrors.nama = '' })
```

**Input with validation styling**:
```vue
<input
  v-model="formData.nama"
  :class="formErrors.nama 
    ? 'border-red-500 focus:border-red-500' 
    : 'border-gray-300 focus:border-brand-300'"
/>
<p v-if="formErrors.nama" class="mt-1 text-xs text-red-500">{{ formErrors.nama }}</p>
```

## Database Conventions

**All tables** (see migrations in `database/migrations/`):
- Use UUID primary keys
- Include `created_by`, `updated_by`, `deleted_by` foreign keys to `users.id`
- Include `timestamps()` and `softDeletes()`
- Add composite indexes like `idx_{table}_{field}_deleted` for soft delete queries

**Example migration**:
```php
Schema::create('table_name', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('field');
    $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
    $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
    $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamps();
    $table->softDeletes();
    $table->index(['field', 'deleted_at'], 'idx_table_field_deleted');
});
```

**Model boilerplate**:
```php
use HasFactory, HasUuids, SoftDeletes;

protected $fillable = ['field', 'created_by', 'updated_by', 'deleted_by'];

public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
public function deleter(): BelongsTo { return $this->belongsTo(User::class, 'deleted_by'); }
```

## Menu & Permissions

**Dynamic menu** (`app/Services/MenuService.php`):
- `getMenuConfig()`: Static menu structure with permission mappings
- `getFilteredMenu()`: Filters menu items based on authenticated user permissions using `$user->can('permission')`
- Menu returned via `/admin/api/menu` endpoint

**Adding new menu items**:
1. Add to `MenuService::getMenuConfig()` with `permission` key
2. Create matching permission in database via seeder
3. Frontend fetches filtered menu on auth

## Integration Points

**CSRF Protection**: All POST/PUT/DELETE requests must include `X-CSRF-TOKEN` header. Token available from:
- Meta tag: `<meta name="csrf-token" content="...">`
- API endpoint: `GET /admin/api/csrf-token`

**Toast notifications**: Use `vue-toastification` in frontend:
```typescript
import { useToast } from 'vue-toastification'
const toast = useToast()
toast.success('Message')
toast.error('Error')
```

**Vue Router base path**: Set to `/admin/` in `public/admin/src/router/index.ts` to match Laravel catch-all route

## Component Library

**TailAdmin Vue components** in `public/admin/src/components/`:
- `layout/AdminLayout.vue`: Main admin wrapper
- `common/PageBreadcrumb.vue`: Breadcrumb navigation
- `common/ConfirmModal.vue`: Delete confirmation modal

**Grid theming**: Use `.ag-theme-alpine` with custom CSS variables for dark mode (see `<style>` blocks in grid views)

## Adding New CRUD Resources

1. **Backend**: Create migration (UUID + audit fields), model (HasUuids + SoftDeletes + creator/updater relations), controller (standard CRUD pattern)
2. **Routes**: Add to `routes/web.php` under `admin/api` protected group: `Route::apiResource('resource', ResourceController::class)`
3. **Frontend**: 
   - Add routes to `public/admin/src/router/index.ts` (list, new, edit)
   - Create list view with AG Grid + search filter
   - Create form view with validation error handling
4. **Menu**: Add to `MenuService` with appropriate permission
5. **Build**: Run `npm run build` from `public/admin/`
