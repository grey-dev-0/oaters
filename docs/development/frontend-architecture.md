# Frontend Architecture

## Overview

OATERS uses a sophisticated frontend architecture that combines Laravel Blade templates with Vue 3 components, orchestrated by Vite. This approach provides modularity, reusability, and clean separation of concerns while maintaining the benefits of both server-side and client-side rendering.

## Architecture Layers

```
┌───────────────────────────────────────────┐
│     Blade Templates (Module Views)        │
│     resources/views/*.blade.php           │
└────────────────┬──────────────────────────┘
                 │ (uses)
                 ↓
┌───────────────────────────────────────────┐
│  Business Components (Blade Components)   │
│  resources/views/components/*.blade.php   │
└────────────────┬──────────────────────────┘
                 │ (uses)
                 ↓
┌─────────────────────────────────────────────┐
│   Generic Vue Components                    │
│   resources/components/*.vue                │
└─────────────────────────────────────────────┘
```
## Layer Descriptions

### 1. Blade Templates (Page Layer)
- **Location**: `Modules/{ModuleName}/resources/views/`
- **Purpose**: Server-rendered HTML pages
- **Responsibility**: Page structure, layout, and SEO concerns
- **Examples**:
    - `resources/views/employees/index.blade.php`
    - `resources/views/departments/show.blade.php`

### 2. Business Components (Business Logic Layer)
- **Location**: `Modules/{ModuleName}/resources/views/components/`
- **Purpose**: Blade components that encapsulate business-specific logic
- **Responsibility**: Combining generic components into business workflows
- **Features**:
    - Use generic Vue components
    - Handle business-specific data transformations
    - Manage inter-component communication
- **Examples**:
    - `Modules/Ruby/resources/views/components/employee-form.blade.php`
    - `Modules/Ruby/resources/views/components/department-hierarchy.blade.php`

### 3. Generic Vue Components (Reusable Component Layer)
- **Location**: `resources/components/`
- **Purpose**: Reusable UI components used across all modules
- **Responsibility**: UI rendering and user interaction
- **Features**:
    - Module-agnostic
    - Highly reusable
    - Self-contained functionality
- **Examples**:
    - `resources/components/Button.vue`
    - `resources/components/Modal.vue`
    - `resources/components/DataTable.vue`
    - `resources/components/FormInput.vue`

## JavaScript Entry Points

### Purpose
Each Blade view has a corresponding JavaScript entry point that registers and initializes the Vue components used on that page.

### Location Pattern
- **Blade Template**: `Modules/{ModuleName}/resources/views/{page}.blade.php`
- **JavaScript Entry**: `resources/js/{moduleName}/{pageName}.js`

### Naming Convention
```
Blade View                                                    JavaScript Entry Point
─────────────────────────────────────────────────────         ──────────────────────────────────
Modules/{ModuleName}/resources/views/{page}.blade.php         resources/js/{moduleName}/{page}.js
```
### JavaScript Entry Point Structure

```javascript
// resources/js/ruby/employees.js
import { createApp } from 'vue'

// Import generic components
import EmployeeTable from '@/components/EmployeeTable.vue'
import FilterPanel from '@/components/FilterPanel.vue'
import PaginationControl from '@/components/PaginationControl.vue'

// Create and mount app
createApp({})
    .component('EmployeeTable', EmployeeTable)
    .component('FilterPanel', FilterPanel)
    .component('PaginationControl', PaginationControl)
    .mount('#app')
```
## Integration with Vite & Laravel Vite Plugin

### How Vite Orchestrates the Frontend

1. **Entry Point Resolution**:
    - Vite processes JavaScript entry points defined in `vite.config.js`
    - The Laravel Vite plugin automatically handles module paths

2. **Component Loading**:
    - Vue components are dynamically imported in JavaScript files
    - Vite bundles them together with their dependencies
    - Hot module replacement (HMR) enables live updates during development

3. **Asset Processing**:
    - SCSS/CSS is compiled and minified
    - Vue components are compiled to optimized JavaScript
    - Assets are fingerprinted in production

### Vite Configuration Example

**Automatic File Discovery**: OATERS uses `glob` to automatically discover all JavaScript entry points:

```javascript
// Automatically discovers files matching these patterns:
let files = globSync([
    "resources/js/*.js",           // Top-level JS files (common.js, login.js)
    "resources/js/*/*.js",         // Module JS files (ruby/employees.js, sapphire/tenants.js)
    "resources/scss/*.scss"        // SCSS files
]);
```

This means **no manual updates to vite.config.js** are needed when creating new entry points.

```javascript
// vite.config.js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        laravel({
            input, // Automatically populated via glob pattern
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    ...
})
```
### Laravel Blade Integration

In your Blade template, include the Vite directives:

```blade
<!DOCTYPE html>
<html>
<head>
    @vite(['resources/css/app.css'])
</head>
<body>
    <!-- Page content -->
    <div id="app">
        <!-- Vue components mount here -->
    </div>
    
    @vite(['resources/js/ruby/employees.js'])
</body>
</html>
```
## Data Flow

### Request → Response Cycle

```
1. User Request
   ↓
2. Laravel Route Handler
   ↓
3. Blade Template Rendering
   ├─ Server-side data passed to Blade
   ├─ Business components included with data
   └─ Generic components rendered as HTML
   ↓
4. JavaScript Entry Point Execution
   ├─ Vue app initialized
   ├─ Generic components hydrated/mounted
   └─ Event listeners attached
   ↓
5. Interactive Page Ready
```
### Server-Side Data Passing

```blade
<!-- Blade template: resources/views/employees/index.blade.php -->
<div id="app">
    <x-ruby::employee-table 
        :employees="$employees"
        :departments="$departments"
        :filters="$filters"
    />
</div>

@vite(['resources/js/ruby/employees.js'])
```
```javascript
// JavaScript: Make server data accessible to Vue
window.pageData = {
    employees: @json($employees),
    departments: @json($departments),
}
```
```vue
<!-- Vue component: resources/components/EmployeeTable.vue -->
<script setup>
import { ref, onMounted } from 'vue'

const employees = ref([])

onMounted(() => {
    employees.value = window.pageData.employees
})
</script>
```
## Component Communication

### Props (Parent → Child)
```blade
<!-- Business component passes props to generic components -->
<x-data-table 
    :data="$tableData"
    :columns="$columns"
    :sortable="true"
/>
```
### Events (Child → Parent)
```vue
<!-- Generic component emits events -->
<script setup>
const emit = defineEmits(['row-selected', 'sort', 'filter'])

function selectRow(row) {
    emit('row-selected', row)
}
</script>

<!-- Business component listens to events -->
<DataTable 
    :data="employees"
    @row-selected="handleRowSelect"
    @sort="handleSort"
/>
```
### Slots (Flexible Content)
```vue
<!-- Generic component provides slots for business logic -->
<template>
    <div class="card">
        <div class="card-header">
            <slot name="header" />
        </div>
        <div class="card-body">
            <slot />
        </div>
    </div>
</template>

<!-- Business component fills slots -->
<Card>
    <template #header>
        <h3>{{ title }}</h3>
    </template>
    <DataTable :data="data" />
</Card>
```
## Asset Organization

```
resources/
├── components/                    # Generic Vue components
│   ├── Button.vue
│   ├── Modal.vue
│   ├── DataTable.vue
│   ├── FormInput.vue
│   └── ...
├── css/
│   ├── app.css
│   └── variables.css
└── js/
    ├── ruby/                       # Ruby module entry points
    │   ├── dashboard.js
    │   ├── employees.js
    │   ├── departments.js
    │   ├── attendance.js
    │   ├── contacts.js
    │   └── structure.js
    ├── sapphire/                   # Sapphire module entry points
    │   ├── dashboard.js
    │   ├── tenants.js

Modules/Ruby/resources/
├── views/
│   ├── employees/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── show.blade.php
│   └── components/
│       ├── employee-form.blade.php
│       └── department-selector.blade.php
└── css/
    └── module.css

Modules/Sapphire/resources/
├── views/
│   ├── auth/
│   │   ├── login.blade.php
│   │   └── register.blade.php
│   └── ...
└── css/
```
## Development Workflow

### During Development

1. **Start Dev Server**:
```bash
   npm run dev
```
This starts Vite with HMR enabled

2. **Component Changes**:
    - Modify a Vue component → Instant browser update
    - Modify a Blade template → Page reloads

3. **Hot Module Replacement**:
    - Vue component state is preserved
    - Only changed modules are reloaded
    - No full page refresh required

### For Production

1. **Build Assets**:
```bash
   npm run production
```
2. **Output**:
    - Minified JavaScript bundles
    - Compiled and minified CSS
    - Fingerprinted asset filenames
    - Source maps (optional)

## Best Practices

1. **Keep Generic Components Generic**
    - No business logic
    - No module-specific imports
    - Accept data via props

2. **Business Logic in Blade Components**
    - Data transformation
    - Conditional rendering
    - Module-specific styling

3. **Page-Specific Setup in JavaScript**
    - Component registration
    - Event listeners
    - Initial state setup

4. **Shared Utilities in Composables**
    - API calls (via `resources/js/composables/`)
    - Form validation logic
    - State management helpers

5. **Proper Naming**
    - Vue components: PascalCase (`DataTable.vue`)
    - JavaScript files: kebab-case or camelCase
    - Blade components: kebab-case

## Troubleshooting

### Components Not Showing

1. Check if component is registered in JavaScript entry point
2. Verify component path in import statement
3. Ensure Vite entry point is included in Blade template

### Hot Module Replacement Not Working

1. Ensure `npm run dev` is running
2. Check `vite.config.js` configuration
3. Verify Blade template includes correct `@vite()` directive

### Build Errors

1. Check JavaScript syntax with `npm run lint`
2. Verify all component imports are correct
3. Run `npm run build` locally to reproduce production issues
## Independent Applications Architecture

Unlike traditional SPAs (Single Page Applications), OATERS treats each Blade view as an **independent frontend application**:

- **No global app.js**: Each page loads only what it needs
- **Isolated Vue applications**: Multiple Vue apps can coexist without conflicts  
- **Page-specific bundles**: Better performance through code splitting
- **Server-side coordination**: Communication between pages happens via Laravel routes
- **Independent entry points**: Each Blade view has its own corresponding JavaScript entry point

### How It Works

When a user navigates to a Blade view, Vite loads the corresponding JavaScript entry point which:
1. Creates a new Vue application instance
2. Registers only the components needed for that page
3. Mounts the app to the page's DOM
4. Runs independently until the user navigates to another page

This approach provides better performance, modularity, and eliminates common SPA complexity like global state management and routing.
