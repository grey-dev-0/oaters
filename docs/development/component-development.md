# Component Development Guide

## Overview

This guide covers how to develop components in OATERS, from generic Vue components to business-specific Blade components.

## Component Documentation

Detailed documentation for all generic Vue components is available:

### Simple Components
- [Alert](../components/alert.md) - Dismissible notification alerts
- [Autocomplete](../components/autocomplete.md) - AJAX-powered autocomplete input
- [Avatar](../components/avatar.md) - User avatar with image/initials fallback
- [Card](../components/card.md) - Card container with header and footer
- [Chart](../components/chart.md) - Chart.js integration with date filtering
- [Counter](../components/counter.md) - Dashboard statistics counter
- [Loader](../components/loader.md) - Hybrid loading spinner (Blade + Vue)
- [Modal](../components/modal.md) - Bootstrap 4 modal dialogs
- [OrgChart](../components/org-chart.md) - Organization hierarchy visualization

### Component Bundles
- [Breadcrumb](../components/breadcrumb.md) - Breadcrumb navigation trail
- [DataTable](../components/datatable.md) - Complete table component suite with filtering and pagination
- [Form](../components/form.md) - Comprehensive form building system with validation and field management
- [List](../components/list.md) - Bootstrap-styled list with collapsible items
- [Navbar](../components/navbar.md) - Navigation bar component
- [Tab](../components/tab.md) - Tabbed interface with fade animations
- [Table](../components/table.md) - Simple HTML table wrapper
- [Timeline](../components/timeline.md) - Visual timeline for chronological events

## Generic Vue Components

Generic Vue components are reusable UI components located in `resources/components/`. They should have no module-specific logic and be usable across all modules.

### Creating a Generic Component

**File**: `resources/components/Button.vue`

```vue
<template>
  <button 
    :class="['btn', `btn-${variant}`, { disabled: isDisabled }]"
    :disabled="isDisabled"
    @click="handleClick"
  >
    <slot />
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (v) => ['primary', 'secondary', 'danger', 'success'].includes(v)
  },
  disabled: {
    type: Boolean,
    default: false
  },
  loading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['click'])

const isDisabled = computed(() => props.disabled || props.loading)

function handleClick(event) {
  if (!isDisabled.value) {
    emit('click', event)
  }
}
</script>

<style scoped>
.btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 0.25rem;
  cursor: pointer;
  font-size: 1rem;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-primary {
  background-color: #007bff;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background-color: #0056b3;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
}

.btn-danger {
  background-color: #dc3545;
  color: white;
}

.btn-success {
  background-color: #28a745;
  color: white;
}
</style>
```
### Component Best Practices

**1. Use Composition API**
```vue
<script setup>
import { ref, computed, onMounted } from 'vue'

const count = ref(0)
const doubled = computed(() => count.value * 2)
</script>
```
**2. Define Props with Validation**
```vue
<script setup>
const props = defineProps({
  items: {
    type: Array,
    required: true,
    validator: (arr) => Array.isArray(arr)
  },
  loading: {
    type: Boolean,
    default: false
  }
})
</script>
```
**3. Emit Events Explicitly**
```vue
<script setup>
const emit = defineEmits(['update', 'delete', 'select'])

function updateItem(item) {
  emit('update', item)
}
</script>
```
**4. Use Slots for Flexibility**
```vue
<template>
  <div class="card">
    <div class="card-header">
      <slot name="header" :title="title" />
    </div>
    <div class="card-body">
      <slot :data="data" />
    </div>
    <div class="card-footer">
      <slot name="footer" />
    </div>
  </div>
</template>
```
**5. Keep Styling Scoped**
```vue
<style scoped>
/* This CSS only applies to this component */
.card {
  border: 1px solid #ddd;
}
</style>
```
## Business Components (Blade Components)

Business components are Blade components that combine generic Vue components with business logic. They're module-specific and can access server-side data.

### Example: Department Form Component

Here's a real example from OATERS that demonstrates how business components combine generic Vue components (Form, Field, Autocomplete, Select2) with module-specific logic:

**File**: `Modules/Ruby/resources/views/components/modals/department-form.blade.php`

```blade path=/opt/public_html/theultragrey/oaters/Modules/Ruby/resources/views/components/modals/department-form.blade.php start=1
@php
$edit ??= false;
@endphp

<modal id="{{$id}}" ref="{{$ref}}" static size="lg" color="{{$color}}">
    <template #header>{{$title}}@if($edit) - @{{ openDepartment.name }}@endif</template>
    <vue-form id="{{$id}}-form" ref="{{$ref}}Form" large="3" ajax action="{{url('r/departments/'.($edit? 'update' : 'create'))}}">
        @if($edit)
            <input type="hidden" name="id" :value="openDepartment.id">
        @endif
        <vue-field name="en[name]" type="text" id="{{($edit)? 'e_' : ''}}name-en">{{trans('common::words.name')}} ({{trans('common::words.english')}})</vue-field>
        <vue-field name="ar[name]" type="text" id="{{($edit)? 'e_' : ''}}name-ar">{{trans('common::words.name')}} ({{trans('common::words.arabic')}})</vue-field>
        <vue-field name="manager_id" type="autocomplete" id="{{($edit)? 'e_' : ''}}manager-id" url="{{route('ruby::contacts.search')}}">{{trans('ruby::departments.head')}}</vue-field>
        <vue-field name="contact_id" type="select2" multiple id="{{($edit)? 'e_' : ''}}contact-id" url="{{route('ruby::contacts.search')}}">
            {{trans('ruby::words.staff')}}
        </vue-field>
    </vue-form>
    <template #footer>
        <div class="btn btn-outline-secondary" data-dismiss="modal">Cancel</div>
        <div class="btn btn-{{$color}} text-white" @click="submit('{{$ref}}')">Save</div>
    </template>
</modal>
```

### Using Business Components in Views

This component is used in the departments list page:

**File**: `Modules/Ruby/resources/views/departments.blade.php`

```blade path=/opt/public_html/theultragrey/oaters/Modules/Ruby/resources/views/departments.blade.php start=48
<x-ruby::modals.department-form ref="createDepartment" id="add-department" color="green-2" title="{{trans('ruby::departments.new')}}"/>
<x-ruby::modals.department-form ref="updateDepartment" id="edit-department" color="blue-3" title="{{trans('ruby::departments.edit')}}" :edit="true"/>
```

### Key Features Demonstrated

This real example shows:

1. **Combining Generic Components**: Uses `VueForm`, `VueField`, `Modal`, `Autocomplete`, and `Select2` components
2. **Dynamic Properties**: Props like `$edit`, `$id`, `$ref`, `$color`, and `$title` allow component reuse for create/edit operations
3. **AJAX Integration**: Form submission via AJAX to Laravel routes
4. **Server-Side Data**: Autocomplete and select2 fields fetch data from server endpoints
5. **Internationalization**: Uses `trans()` helpers for multi-language support
6. **Blade Syntax**: Mixes Blade directives with Vue template syntax seamlessly
## JavaScript Entry Points for Blade Views

Each Blade view requires a corresponding JavaScript entry point to register and initialize Vue components used on that page. These files are automatically discovered by Vite using glob patterns.

### Common Helper Script

The `resources/js/common.js` helper script provides centralized management of common libraries and components used across all pages. It exports:

- `load(app)` - Registers common components (Alert, Avatar, Card, VLoader) and navigation (Breadcrumb, Navbar)
- `loadBundles(app, bundles)` - Registers component bundles (Form, DataTable, Tab, etc.)
- `loadComponents(app, components)` - Dynamically registers individual simple components
- `jQuery` - Pre-configured jQuery with CSRF token support
- `bootbox` - Modal library for alerts and confirmations

**Key Benefits:**
- Bootstrap and jQuery pre-loaded for all pages
- CSRF tokens automatically configured for AJAX requests
- Common components available globally
- Consistent component registration across modules

### File Structure

**Location**: `resources/js/{moduleName}/{pageName}.js` corresponds to `Modules/{ModuleName}/resources/views/{page-name}.blade.php`

**Example**: `resources/js/ruby/employees.js` for `Modules/Ruby/resources/views/employees.blade.php`

```javascript path=null start=null
import { createApp } from 'vue'
import common, { jQuery, bootbox } from '@/common'
import Form from '@/components/form'
import DataTable from '@/components/datatable'

// Create app instance
const app = createApp({})

// Load common libraries and base components (Alert, Avatar, Card, VLoader, etc.)
common.load(app)

// Load component bundles needed for this Blade view
common.loadBundles(app, [Form, DataTable])

// Register simple components (non-bundled) used on this page
common.loadComponents(app, {
  Chart: 'chart',
  OrgChart: 'org-chart'
})

// Mount to #app element in the Blade template
app.mount('#app')
```

> **Note**: No changes to `vite.config.js` are needed. The glob pattern automatically discovers all files matching `resources/js/*.js` and `resources/js/*/*.js`.

## Styling

### Global Styles

**File**: `resources/css/app.css`

```css
/* Global variables */
:root {
    --color-primary: #007bff;
    --color-secondary: #6c757d;
    --color-danger: #dc3545;
    --spacing-unit: 0.5rem;
}

/* Global utilities */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1rem;
}

.d-flex {
    display: flex;
}

.gap-1 {
    gap: var(--spacing-unit);
}
```
### Module Styles

**File**: `Modules/Ruby/resources/css/module.css`

```css
/* Ruby-specific styles */
.employee-form {
    max-width: 600px;
}

.department-selector {
    border: 1px solid var(--color-primary);
}
```
### Component Scoped Styles

Always use scoped styles in Vue components:

```vue
<style scoped>
.button {
    /* Only applies to this component */
    padding: 0.5rem 1rem;
}
</style>
```