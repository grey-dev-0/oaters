# DataTable Component Bundle

## Overview

The DataTable component bundle is a sophisticated set of Vue components that integrate [DataTables.net](https://datatables.net/) with Vue 3, providing server-side data processing, filtering, sorting, and pagination. It consists of four components that work together to create powerful, filterable data tables.

**Location**: `resources/components/datatable/`

**Components**:
- `VueDatatable` - Main table component
- `DtColumn` - Column definition component
- `VueDatafilter` - Filter container component
- `DtFilter` - Individual filter input component

## Architecture

```
┌─────────────────────────────────────┐
│       VueDatafilter                 │
│  (Filter Container)                 │
│  ┌───────────┬───────────┬────────┐ │
│  │ DtFilter  │ DtFilter  │ ...    │ │
│  └───────────┴───────────┴────────┘ │
└─────────────────────────────────────┘
         ↓ (filters data)
┌─────────────────────────────────────┐
│       VueDatatable                  │
│  (Table with Server-Side Data)      │
│  ┌─────────────────────────────────┐│
│  │ DtColumn │ DtColumn │ DtColumn ││
│  └─────────────────────────────────┘│
└─────────────────────────────────────┘
```

## Component Registration

The DataTable bundle is registered via an index file that exports a `load` function:

```javascript path=null start=null
// resources/components/datatable/index.js
import DtColumn from "./column.vue";
import VueDatatable from "./datatable.vue";
import {defineAsyncComponent} from "vue";

function load(app){
    app.component('DtColumn', DtColumn);
    app.component('VueDatatable', VueDatatable);
    app.component('VueDatafilter', defineAsyncComponent(() => import('./datafilter.vue')));
    app.component('DtFilter', defineAsyncComponent(() => import('./filter-input.vue')));
}

export default {load}
```

**Usage in Entry Point**:

```javascript path=null start=null
// resources/js/ruby/contacts.js
import {createApp} from "vue";
import Datatable from "../../components/datatable";

let app = createApp({
    // ... app config
});

// Load the DataTable bundle
Datatable.load(app);

app.mount('#app');
```

## Components

### VueDatatable

The main table component that wraps DataTables.net functionality and handles server-side data loading.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `datatableId` | String | - | Unique ID for the table element |
| `deferred` | Boolean | `false` | Whether to defer initialization |
| `url` | String | `window.location.href` | Server endpoint for data |
| `sort` | Array | `[[0, 'asc']]` | Default sort order `[columnIndex, direction]` |
| `dom` | String | Bootstrap layout | DataTables DOM positioning string |
| `ajaxComplete` | Function | `null` | Callback after AJAX request completes |
| `ajaxData` | Object | `{}` | Additional data to send with AJAX request |
| `renderActions` | Function | - | Custom function to render action buttons |

#### Methods

| Method | Description |
|--------|-------------|
| `init()` | Initialize or reinitialize the DataTable |
| `getRef()` | Get the component's ref name from parent |

#### Events

| Event | Payload | Description |
|-------|---------|-------------|
| `initialized` | `{ref: string}` | Emitted when table is initialized |

#### Example Usage

```vue path=null start=null
<vue-datatable 
    datatable-id="contacts-table" 
    ref="contactsTable" 
    :sort="[[5, 'desc']]"
>
    <dt-column name="name" data="name">Name</dt-column>
    <dt-column name="email" data="email">Email</dt-column>
    <dt-column name="created_at" data="created_at">Created</dt-column>
</vue-datatable>
```

### DtColumn

Defines a table column with its properties and rendering behavior.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `data` | String \| Function | - | Data source or accessor function |
| `render` | String \| Function | - | Custom render function for cell content |
| `name` | String | `undefined` | Column name for filtering/sorting |
| `searchable` | Boolean | `true` | Whether column is searchable |
| `orderable` | Boolean | `true` | Whether column is sortable |
| `visible` | Boolean | `true` | Whether column is visible |
| `width` | String | `undefined` | Column width (e.g., "100px", "20%") |
| `defaultContent` | String | - | Default content if data is null |
| `className` | String | - | CSS class for the column cells |

#### Slots

| Slot | Description |
|------|-------------|
| Default | Column header text |
| `actions` | Action buttons template (hidden, cloned for each row) |

#### Example Usage

**Simple Column**:
```vue path=null start=null
<dt-column name="name" data="name">Name</dt-column>
```

**Column with Custom Renderer**:
```vue path=null start=null
<dt-column name="amount" :data="renderAmount">Amount</dt-column>
```

**Actions Column**:
```vue path=null start=null
<dt-column :orderable="false" :searchable="false" class-name="nowrap" :data="null">
    Actions
    <template #actions>
        <div class="btn btn-sm btn-outline-primary edit" title="Edit">
            <i class="fas fa-pen"></i>
        </div>
        <div class="btn btn-sm btn-outline-danger delete" title="Delete">
            <i class="fas fa-trash-alt"></i>
        </div>
    </template>
</dt-column>
```

### VueDatafilter

Container component for filter inputs that manages filter state and communicates with the DataTable.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `cols` | Number | `5` | Number of columns in the filter grid |
| `datatableRef` | String | - | Reference to the target DataTable component |
| `color` | String | `'grey-10'` | Background color class |

#### Data

| Property | Type | Description |
|----------|------|-------------|
| `filters` | Object | Current filter values keyed by field name |
| `defaults` | Object | Default filter values to apply on init |

#### Methods

| Method | Parameters | Description |
|--------|-----------|-------------|
| `filter(field, value)` | `field`: String, `value`: Any | Apply filter to specified column |
| `clear()` | - | Clear all filters and reset to defaults |
| `setDefaults()` | - | Apply default filters |

#### Events Listened

| Event | Description |
|-------|-------------|
| `initialized` | When DataTable is ready, apply default filters |

#### Events Emitted

| Event | Payload | Description |
|-------|---------|-------------|
| `clear` | `{ref: string}` | Emitted when filters are cleared |

#### Example Usage

```vue path=null start=null
<vue-datafilter :cols="4" datatable-ref="contactsTable">
    <dt-filter name="name" type="text" label="Name"></dt-filter>
    <dt-filter name="email" type="text" label="Email"></dt-filter>
    <dt-filter name="role" type="select2" multiple :values="roles" label="Role"></dt-filter>
    <dt-filter name="created_at" type="date" label="Created At"></dt-filter>
</vue-datafilter>
```

### DtFilter

Individual filter input component supporting multiple input types (text, select, select2, date).

#### Props

| Prop | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `name` | String | Yes | - | Field name to filter on |
| `label` | String | Yes | - | Display label for the filter |
| `type` | String | Yes | - | Input type: `text`, `select`, `select2`, `date` |
| `multiple` | Boolean | No | `false` | Allow multiple selections (for select/select2) |
| `values` | Object | No | - | Options for select/select2 `{id: 'Label'}` |
| `options` | Object | No | - | Additional options (e.g., `{opens: 'left'}` for date) |
| `default` | String | No | - | Default value for the filter |

#### Supported Types

| Type | Description | Dependencies |
|------|-------------|--------------|
| `text` | Standard text input with debounced search | - |
| `select` | Native HTML select dropdown | - |
| `select2` | Enhanced select with search | [Select2](https://select2.org/) |
| `date` | Date range picker | [daterangepicker](https://www.daterangepicker.com/) |

#### Example Usage

**Text Filter**:
```vue path=null start=null
<dt-filter name="name" type="text" label="Name"></dt-filter>
```

**Select2 Filter (Single)**:
```vue path=null start=null
<dt-filter 
    name="department" 
    type="select2" 
    :values="departments" 
    label="Department"
></dt-filter>
```

**Select2 Filter (Multiple)**:
```vue path=null start=null
<dt-filter 
    name="roles" 
    type="select2" 
    multiple 
    :values="roles" 
    label="Roles"
></dt-filter>
```

**Date Filter**:
```vue path=null start=null
<dt-filter 
    name="created_at" 
    type="date" 
    label="Created At"
    :options="{opens: 'left'}"
></dt-filter>
```

**Filter with Default Value**:
```vue path=null start=null
<dt-filter 
    name="status" 
    type="select" 
    :values="{active: 'Active', inactive: 'Inactive'}" 
    label="Status"
    default="active"
></dt-filter>
```

## Complete Example

Here's a complete example from the OATERS codebase showing how all components work together:

### Blade View

```blade path=/opt/public_html/theultragrey/oaters/Modules/Ruby/resources/views/contacts.blade.php start=24
<vue-datafilter :cols="4" datatable-ref="contactsTable">
    <dt-filter name="name" type="text" label="{{trans('common::words.name')}}"></dt-filter>
    <dt-filter name="emails.address" type="text" label="{{trans('common::words.email')}}"></dt-filter>
    <dt-filter name="phones.number" type="text" label="{{trans('common::words.phone')}}"></dt-filter>
    <dt-filter name="roles" type="select2" multiple :values="roles" label="{{trans('common::words.role')}}"></dt-filter>
    <dt-filter name="job" type="text" label="{{trans('ruby::contacts.job')}}"></dt-filter>
    <dt-filter name="departments" type="select2" multiple :values="departments" label="{{trans('common::words.departments')}}"></dt-filter>
    <dt-filter name="applicant.recruited_at" type="date" label="{{trans('ruby::applicants.recruited_at')}}"></dt-filter>
</vue-datafilter>

<x-ruby::tables.contacts/>
```

### JavaScript Entry Point

```javascript path=/opt/public_html/theultragrey/oaters/resources/js/ruby/contacts.js start=1
import {createApp} from "vue";
import common, {jQuery as $} from "../common.js";
import Datatable from "../../components/datatable";
import select2 from 'select2';
import 'select2/dist/css/select2.min.css';
import 'select2-theme-bootstrap4/dist/select2-bootstrap.min.css';
import 'daterangepicker';
import 'daterangepicker/daterangepicker.css';
select2();

let app = createApp({
    data(){
        return {
            roles, departments, locale
        };
    },
    methods: {
        renderDepartments(row){
            let departments = [], i;
            for(i in row.departments)
                departments.push(`<p class="m-0 text-primary">${row.departments[i].name}</p>`);
            for(i in row.managed_departments)
                departments.push(`<p class="m-0 text-info">${row.managed_departments[i].name}</p>`);
            return departments.length? departments.join('') : `<small class="text-muted">${window.locale.common.unassigned}</small>`;
        }
    },
    computed: {
        dataTable(){
            return this.$refs.contactsTable.dataTable;
        }
    }
});

common.load(app);
Datatable.load(app);
app.mount('#app');
```

## Server-Side Integration

The DataTable component expects server responses in DataTables.net format:

### Request Parameters

The component sends POST requests with these parameters:
- `draw` - Draw counter for synchronization
- `start` - Pagination start index
- `length` - Number of records per page
- `order` - Array of sort orders
- `columns` - Array of column definitions
- `search` - Global search value
- `columns[i][search][value]` - Individual column searches

### Response Format

```json
{
    "draw": 1,
    "recordsTotal": 1000,
    "recordsFiltered": 50,
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "created_at": "2024-01-15"
        },
        // ... more rows
    ]
}
```

## Dependencies

- [DataTables.net](https://datatables.net/) - jQuery plugin for enhanced tables
- [DataTables Bootstrap 4](https://datatables.net/manual/styling/bootstrap4) - Bootstrap 4 styling
- [Select2](https://select2.org/) (optional) - For enhanced select filters
- [daterangepicker](https://www.daterangepicker.com/) (optional) - For date filters
- [mitt](https://github.com/developit/mitt) - Event emitter for component communication

## Styling

The DataTable bundle includes scoped styling for:
- Table container layout
- Cell padding and alignment
- Pagination controls
- Button sizing
- Responsive behavior

Custom styles can be added to override defaults:

```scss path=null start=null
.dt-container {
    // Custom table container styles
    
    td.custom-cell {
        // Custom cell styles
    }
}
```

## Best Practices

1. **Always Provide a Ref**: Reference the DataTable component to access its API
   ```vue path=null start=null
   <vue-datatable ref="myTable" ...>
   ```

2. **Use Named Columns for Filtering**: Include the `name` prop on columns you want to filter
   ```vue path=null start=null
   <dt-column name="email" data="email">Email</dt-column>
   ```

3. **Optimize Rendering**: Use render functions for computed values instead of processing in the template
   ```javascript path=null start=null
   renderAmount(row) {
       return `$${parseFloat(row.amount).toFixed(2)}`;
   }
   ```

4. **Action Buttons**: Use the `actions` slot with `data-id` attributes for row identification
   ```vue path=null start=null
   <template #actions>
       <div class="btn btn-sm edit" data-id="id"><i class="fas fa-pen"></i></div>
   </template>
   ```

5. **Filter Grid Layout**: Set `cols` to match your design (typically 3-5 columns)
   ```vue path=null start=null
   <vue-datafilter :cols="4" datatable-ref="myTable">
   ```

6. **Import Required Libraries**: When using select2 or daterangepicker filters, import their CSS
   ```javascript path=null start=null
   import 'select2/dist/css/select2.min.css';
   import 'daterangepicker/daterangepicker.css';
   ```

## Troubleshooting

### Filters Not Working
- Ensure `datatableRef` matches the DataTable's ref name
- Verify column `name` props match filter `name` props
- Check that select2/daterangepicker are initialized

### Table Not Initializing
- Verify the server endpoint returns valid DataTables format
- Check browser console for JavaScript errors
- Ensure all required dependencies are imported

### Actions Not Rendering
- Make sure `renderActions` is called or default rendering is used
- Verify action buttons have proper event listeners attached
- Check that `data-id` attributes are set correctly

## Advanced Usage

### Custom AJAX Data

Pass additional parameters with each request:

```vue path=null start=null
<vue-datatable 
    :ajax-data="getAjaxData"
    ...>
</vue-datatable>
```

```javascript path=null start=null
methods: {
    getAjaxData(data) {
        data.custom_param = this.customValue;
        return data;
    }
}
```

### Manual Table Refresh

Access the DataTable API directly:

```javascript path=null start=null
// Reload data
this.$refs.myTable.dataTable.ajax.reload();

// Get selected row data
let rowData = this.$refs.myTable.dataTable.row('.selected').data();
```

### Deferred Initialization

Initialize the table programmatically:

```vue path=null start=null
<vue-datatable ref="myTable" deferred>
    <!-- columns -->
</vue-datatable>
```

```javascript path=null start=null
mounted() {
    // Initialize when ready
    this.$refs.myTable.init();
}
```
