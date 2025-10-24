# Table Component Bundle

## Overview

The Table component bundle is a lightweight wrapper around standard HTML `<table>` elements, providing a simple and semantic approach to table rendering without external table library dependencies. Unlike the [DataTable](/components/datatable) bundle which integrates DataTables.net, this bundle focuses on clean markup and Bootstrap styling for straightforward tabular data display.

**Location**: `resources/components/table/`

**Components**:
- `VueTable` - Main table container component
- `Column` - Table header column component

**Key Difference from DataTable**: This is a simple HTML table wrapper without server-side processing, filtering, or sorting capabilities. Use this when you need basic table layouts; use DataTable for complex data management with server-side features.

## Architecture

```
┌──────────────────────────────────────┐
│         VueTable                     │
│    (HTML <table> wrapper)            │
│  ┌────────────────────────────────┐  │
│  │ <thead>                        │  │
│  │  <tr>                          │  │
│  │    <Column /> <Column /> ...   │  │
│  │  </tr>                         │  │
│  └────────────────────────────────┘  │
│  ┌────────────────────────────────┐  │
│  │ <tbody>                        │  │
│  │  <slot name="rows" />          │  │
│  │ (Table rows rendered here)     │  │
│  └────────────────────────────────┘  │
└──────────────────────────────────────┘
```

## Component Registration

The Table bundle is registered via an index file that exports a `load` function:

```javascript path=/opt/public_html/theultragrey/oaters/resources/components/table/index.js start=1
import {defineAsyncComponent} from "vue";

function load(app){
    app.component('VueTable', defineAsyncComponent(() => import('./table.vue')));
    app.component('Column', defineAsyncComponent(() => import('./column.vue')));
}

export default {load}
```

**Usage in Entry Point**:

```javascript path=null start=null
// resources/js/app.js
import {createApp} from "vue";
import Table from "../../components/table";

let app = createApp({
    // ... app config
});

// Load the Table bundle
Table.load(app);

app.mount('#app');
```

## Components

### VueTable

The main table component that wraps the HTML `<table>` element and manages table-level properties.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | String | - | ID attribute for the `<table>` element |
| `class` | String | - | Additional CSS classes to apply (combined with default `table` class) |
| `url` | String | - | Reserved for future use; currently not implemented |

#### Slots

| Slot | Description |
|------|-------------|
| Default | Table header content (typically `<Column>` components) |
| `rows` | Table body content (table rows rendered here) |

#### Example Usage

```vue path=null start=null
<vue-table id="users-table" class="table-striped table-hover">
  <column>Name</column>
  <column>Email</column>
  <column>Status</column>
  
  <template #rows>
    <tr v-for="user in users" :key="user.id">
      <td>{{ user.name }}</td>
      <td>{{ user.email }}</td>
      <td>{{ user.status }}</td>
    </tr>
  </template>
</vue-table>
```

### Column

Defines a table header column cell (`<th>`). This component is a simple wrapper for semantic HTML structure.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `data` | String | - | Reserved for future use; column data binding not currently implemented |
| `last` | Boolean | - | Reserved for future use; end-of-row indicator not currently used |

#### Slots

| Slot | Description |
|------|-------------|
| Default | Column header text content |

#### Example Usage

**Simple Column**:
```vue path=null start=null
<column>Name</column>
```

**Column with Icon**:
```vue path=null start=null
<column>
  <i class="fas fa-user"></i> Name
</column>
```

**Column with Slot Content**:
```vue path=null start=null
<column>
  <span class="text-muted">Last Updated</span>
</column>
```

## Complete Example

Here's a complete example showing the Table component in action:

### Blade View

```blade path=null start=null
<div class="card">
  <div class="card-header">
    <h5 class="card-title">Users List</h5>
  </div>
  <div class="card-body">
    <div id="app">
      <vue-table id="users-table" class="table-striped table-hover">
        <column>Name</column>
        <column>Email</column>
        <column>Department</column>
        <column>Status</column>
        
        <template #rows>
          <tr v-for="user in users" :key="user.id">
            <td><strong>{{ user.name }}</strong></td>
            <td>{{ user.email }}</td>
            <td>{{ user.department }}</td>
            <td>
              <span :class="['badge', user.active ? 'badge-success' : 'badge-secondary']">
                {{ user.active ? 'Active' : 'Inactive' }}
              </span>
            </td>
          </tr>
        </template>
      </vue-table>
    </div>
  </div>
</div>
```

### JavaScript Entry Point

```javascript path=null start=null
import {createApp} from "vue";
import Table from "../../components/table";

let app = createApp({
    data() {
        return {
            users: [
                { id: 1, name: 'John Doe', email: 'john@example.com', department: 'Engineering', active: true },
                { id: 2, name: 'Jane Smith', email: 'jane@example.com', department: 'Marketing', active: true },
                { id: 3, name: 'Bob Johnson', email: 'bob@example.com', department: 'Sales', active: false }
            ]
        };
    }
});

Table.load(app);
app.mount('#app');
```

## Features

- ✅ Lightweight, no external dependencies
- ✅ Semantic HTML structure
- ✅ Bootstrap 4/5 table classes compatible
- ✅ Responsive table support
- ✅ Simple and straightforward API
- ✅ Component-based column headers

## Styling

The Table component relies on Bootstrap's table classes. Apply styling through:

1. **Bootstrap Table Classes** (on VueTable):
   ```vue path=null start=null
   <vue-table class="table-striped table-hover table-sm">
   ```

2. **Bootstrap Utilities** (on rows and cells):
   ```vue path=null start=null
   <tr class="align-middle">
     <td class="text-center">{{ data }}</td>
   </tr>
   ```

3. **Custom CSS** (in component or stylesheet):
   ```scss path=null start=null
   #users-table {
     th { background-color: #f8f9fa; }
     td { padding: 1rem; }
   }
   ```

### Available Bootstrap Classes

| Class | Effect |
|-------|--------|
| `table-striped` | Alternating row colors |
| `table-hover` | Highlight row on hover |
| `table-bordered` | Add borders to all cells |
| `table-borderless` | Remove all borders |
| `table-sm` | Reduce padding for compact display |
| `table-dark` | Dark background with light text |

## Best Practices

1. **Use Semantic Markup**: Always use `<th>` for headers via `<Column>` and `<td>` for data cells

   ```vue path=null start=null
   ✅ GOOD
   <vue-table>
     <column>Name</column>
     <template #rows>
       <tr v-for="item in items">
         <td>{{ item.name }}</td>
       </tr>
     </template>
   </vue-table>
   ```

2. **Set a Table ID**: Helps with styling and JavaScript targeting
   ```vue path=null start=null
   <vue-table id="products-table">
   ```

3. **Use Bootstrap Classes for Styling**: Consistent with the application design system
   ```vue path=null start=null
   <vue-table class="table-striped table-hover">
   ```

4. **Provide Alternative Text for Icons**: Ensure accessibility
   ```vue path=null start=null
   <column>
     <i class="fas fa-envelope"></i>
     <span class="d-none d-md-inline">Email</span>
   </column>
   ```

5. **Keep Rows Simple**: For complex row content, consider using a component
   ```vue path=null start=null
   ✅ GOOD
   <template #rows>
     <user-table-row v-for="user in users" :key="user.id" :user="user" />
   </template>
   ```

## Common Patterns

### Action Buttons in Rows

```vue path=null start=null
<vue-table>
  <column>Name</column>
  <column>Email</column>
  <column style="width: 150px;">Actions</column>
  
  <template #rows>
    <tr v-for="user in users" :key="user.id">
      <td>{{ user.name }}</td>
      <td>{{ user.email }}</td>
      <td>
        <button class="btn btn-sm btn-primary" @click="editUser(user)">
          <i class="fas fa-edit"></i> Edit
        </button>
        <button class="btn btn-sm btn-danger" @click="deleteUser(user)">
          <i class="fas fa-trash"></i> Delete
        </button>
      </td>
    </tr>
  </template>
</vue-table>
```

### Empty State

```vue path=null start=null
<vue-table>
  <column>Name</column>
  <column>Email</column>
  
  <template #rows>
    <tr v-if="users.length === 0">
      <td :colspan="2" class="text-center text-muted py-5">
        <p>No users found</p>
        <button class="btn btn-primary btn-sm">Create User</button>
      </td>
    </tr>
    <tr v-for="user in users" :key="user.id">
      <td>{{ user.name }}</td>
      <td>{{ user.email }}</td>
    </tr>
  </template>
</vue-table>
```

### Sortable Columns (Manual)

```vue path=null start=null
<script>
export default {
  data() {
    return {
      users: [],
      sortField: 'name',
      sortOrder: 'asc'
    };
  },
  computed: {
    sortedUsers() {
      return [...this.users].sort((a, b) => {
        const aVal = a[this.sortField];
        const bVal = b[this.sortField];
        return this.sortOrder === 'asc' ? 
          aVal.localeCompare(bVal) : 
          bVal.localeCompare(aVal);
      });
    }
  },
  methods: {
    toggleSort(field) {
      if (this.sortField === field) {
        this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
      } else {
        this.sortField = field;
        this.sortOrder = 'asc';
      }
    }
  }
};
</script>

<template>
  <vue-table>
    <column @click="toggleSort('name')">
      Name
      <i v-if="sortField === 'name'" 
         :class="['fas', sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down']">
      </i>
    </column>
    
    <template #rows>
      <tr v-for="user in sortedUsers" :key="user.id">
        <td>{{ user.name }}</td>
      </tr>
    </template>
  </vue-table>
</template>
```

### Responsive Table

```vue path=null start=null
<div class="table-responsive">
  <vue-table class="table-sm">
    <column>Name</column>
    <column>Email</column>
    <column>Phone</column>
    <column>Status</column>
    
    <template #rows>
      <tr v-for="contact in contacts" :key="contact.id">
        <td data-label="Name">{{ contact.name }}</td>
        <td data-label="Email">{{ contact.email }}</td>
        <td data-label="Phone">{{ contact.phone }}</td>
        <td data-label="Status">{{ contact.status }}</td>
      </tr>
    </template>
  </vue-table>
</div>
```

## Troubleshooting

### Table Not Displaying

- Verify `VueTable` and `Column` are properly registered via the bundle's `load()` function
- Check that the component names match: `vue-table` and `column` (case-insensitive)
- Ensure the rows slot has content or the table body will appear empty

### Styling Not Applied

- Confirm Bootstrap CSS is included in the page
- Check that Bootstrap classes are spelled correctly
- Verify no conflicting CSS is overriding table styles
- Use browser DevTools to inspect computed styles

### Columns Misaligned

- Ensure the same number of `<Column>` components as `<td>` cells in rows
- Use `colspan` for merged cells if needed
- Check that no CSS is setting fixed widths on columns

## Browser Compatibility

| Browser | Support |
|---------|---------|
| Chrome | ✅ Full support |
| Firefox | ✅ Full support |
| Safari | ✅ Full support |
| Edge | ✅ Full support |
| IE 11 | ⚠️ Requires Vue 3 polyfills |

## Dependencies

- Vue 3
- Bootstrap 4 or 5 (for default styling)

## See Also

- [DataTable](/components/datatable) - For tables with server-side processing and advanced features
- [Card](/components/card) - For data containers and card layouts
- [Loader](/components/loader) - For loading states while fetching table data
