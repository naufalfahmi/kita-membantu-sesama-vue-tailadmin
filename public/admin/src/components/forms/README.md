AsyncSearchableSelect

Usage:

<AsyncSearchableSelect
  v-model="selectedId"
  fetch-url="/admin/api/donatur"
  placeholder="Donatur"
/>

Notes:
- Component performs server-side search and pagination using `?search=...&per_page=...&page=...`.
- Debounced search (300ms) and infinite-scroll support.
- If `modelValue` is a non-empty id, the component will attempt to fetch `/admin/api/{resource}/{id}` to populate the label.
- Add a top "All" option by setting `:include-all="true"` and customize label with `all-label` (useful for filters where empty value means all). 
- Keep `per_page` reasonable (default 20).
