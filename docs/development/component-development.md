# Component Development Guide

## Overview

This guide covers how to develop components in OATERS, from generic Vue components to business-specific Blade components.

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

### Creating a Business Component

**File**: `Modules/Ruby/resources/views/components/employee-form.blade.php`

```blade
@props(['employee' => null, 'departments' => [], 'action' => 'create'])

<form 
    action="{{ $action === 'create' 
        ? route('ruby.employees.store') 
        : route('ruby.employees.update', $employee) }}"
    method="POST"
    id="employee-form"
    class="employee-form"
>
    @csrf
    @if($action === 'update')
        @method('PUT')
    @endif

    <div class="form-group">
        <form-input 
            name="first_name"
            type="text"
            label="First Name"
            :value="@json($employee?->first_name ?? '')"
            required
        />
    </div>

    <div class="form-group">
        <form-input 
            name="last_name"
            type="text"
            label="Last Name"
            :value="@json($employee?->last_name ?? '')"
            required
        />
    </div>

    <div class="form-group">
        <select-input 
            name="department_id"
            label="Department"
            :options="{{ json_encode($departments->pluck('name', 'id')) }}"
            :value="@json($employee?->department_id ?? '')"
            required
        />
    </div>

    <div class="form-group">
        <button type="submit" variant="primary">
            {{ $action === 'create' ? 'Create Employee' : 'Update Employee' }}
        </button>
    </div>
</form>

<script setup>
// Optional: Any component-specific logic here
console.log('Employee form mounted')
</script>
```
### Using Business Components

**In a Blade Template**:
```blade
<!-- Modules/Ruby/resources/views/employees/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Create Employee</h1>
        
        @include('ruby::components.employee-form', [
            'departments' => $departments,
            'action' => 'create',
        ])
    </div>
@endsection

@vite(['resources/js/ruby/employees.js'])
```
## Vue Composables

Composables are reusable Vue 3 logic modules that can be shared across components.

### Creating a Composable

**File**: `resources/js/composables/useEmployee.js`

```javascript
import { ref, computed, onMounted } from 'vue'

export function useEmployee(employeeId = null) {
    const employee = ref(null)
    const loading = ref(false)
    const error = ref(null)

    // Fetch employee data
    const fetchEmployee = async () => {
        if (!employeeId) return

        loading.value = true
        error.value = null

        try {
            const response = await fetch(`/api/employees/${employeeId}`)
            if (!response.ok) throw new Error('Failed to fetch employee')
            employee.value = await response.json()
        } catch (err) {
            error.value = err.message
        } finally {
            loading.value = false
        }
    }

    // Update employee
    const updateEmployee = async (data) => {
        loading.value = true
        error.value = null

        try {
            const response = await fetch(`/api/employees/${employee.value.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(data)
            })
            if (!response.ok) throw new Error('Failed to update employee')
            employee.value = await response.json()
            return true
        } catch (err) {
            error.value = err.message
            return false
        } finally {
            loading.value = false
        }
    }

    // Delete employee
    const deleteEmployee = async () => {
        if (!confirm('Are you sure?')) return false

        loading.value = true
        error.value = null

        try {
            const response = await fetch(`/api/employees/${employee.value.id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            })
            if (!response.ok) throw new Error('Failed to delete employee')
            return true
        } catch (err) {
            error.value = err.message
            return false
        } finally {
            loading.value = false
        }
    }

    // Computed properties
    const fullName = computed(() => 
        employee.value ? `${employee.value.first_name} ${employee.value.last_name}` : ''
    )

    const isLoading = computed(() => loading.value)

    onMounted(() => {
        fetchEmployee()
    })

    return {
        employee,
        loading,
        error,
        fullName,
        isLoading,
        fetchEmployee,
        updateEmployee,
        deleteEmployee,
    }
}
```
### Using Composables in Components

```vue
<script setup>
import { useEmployee } from '@/composables/useEmployee.js'

const props = defineProps({
  employeeId: Number
})

const { employee, loading, error, fullName, updateEmployee } = useEmployee(props.employeeId)

async function handleUpdate() {
    const success = await updateEmployee({
        first_name: 'John',
        last_name: 'Doe'
    })
    if (success) {
        alert('Employee updated!')
    }
}
</script>

<template>
    <div v-if="loading">Loading...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else>
        <h2>{{ fullName }}</h2>
        <button @click="handleUpdate">Update</button>
    </div>
</template>
```
## Module-Specific JavaScript Entry Points

Each page needs a JavaScript entry point to register Vue components. These files are automatically discovered by Vite using glob patterns.

### File Structure

**Location**: `resources/js/{moduleName}/{pageName}.js`

**Example**: `resources/js/ruby/employees.js`

```javascript
import { createApp } from 'vue'
import EmployeeTable from '@/components/EmployeeTable.vue'
import FilterPanel from '@/components/FilterPanel.vue'
import PaginationControl from '@/components/PaginationControl.vue'

// Create app instance
const app = createApp({})

// Register components globally for this page
app.component('EmployeeTable', EmployeeTable)
app.component('FilterPanel', FilterPanel)
app.component('PaginationControl', PaginationControl)

// Mount to #app element
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