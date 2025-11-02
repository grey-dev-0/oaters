# Getting Started with OATERS Development

## Prerequisites

### System Requirements

- **PHP** 8.1 or higher
- **Node.js** 16+ and npm
- **Composer** 2.0 or higher
- **Git**
- A local development environment (Laravel Valet, Docker, or similar)

## Backend Setup

### 1. Clone and Install the Project

OATERS is available as a public repository on GitHub. Clone the repository and install dependencies:

```bash
# Clone the OATERS repository
git clone https://github.com/grey-dev-0/oaters.git my-oaters-project

# Navigate to the project directory
cd my-oaters-project

# Install PHP dependencies
composer install
```

This process will:
- Clone the OATERS repository from GitHub
- Install all PHP dependencies from `composer.json`
- Set up the Laravel module system
- Install required packages for multi-tenancy, permissions, localization, and more

### 2. Configure Environment

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

Edit `.env` and configure:
- `APP_NAME`, `APP_URL`
- Database credentials (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)
- Mail settings (optional)

### 3. Set Up Database

OATERS uses a sophisticated multi-tenancy architecture with separate databases:
- **Central Database**: Houses the super admin tenant and Sapphire module
- **Tenant Databases**: Each tenant has an isolated database with their own data

#### 3.1 Migrate and Seed Central Database

```bash
# Migrate the central database schema
php artisan migrate

# Seed central database (includes Sapphire module data)
php artisan db:seed --class=CentralAppSeeder
```

The `CentralAppSeeder` automatically:
- Migrates Sapphire module schema
- Seeds Sapphire module data (users, roles, permissions)
- Creates a test tenant database called "maxwell"
- Seeds the test tenant with sample data for all modules

#### Understanding the Architecture

**Central Database Structure:**
- System configuration and super admin data
- Sapphire module tables (tenants, users, roles, permissions)

**Tenant Database Structure ("maxwell" test tenant):**
- All module data specific to that tenant
- Shared entities (countries, colors, etc.)
- Sample data for testing and development

> **Note**: The test tenant "maxwell" is automatically created and seeded by `CentralAppSeeder`. For production use, you can create additional tenants as needed.

### 4. Start Laravel Development Server

```bash
# Start the PHP development server
php artisan serve
```

The application will be available at `http://127.0.0.1:8000`

## Frontend Setup

### Prerequisites

- Node.js 16+ and npm installed
- Backend server running (see Backend Setup above)

### Installation

```bash
# Install frontend dependencies
npm install

# Start the development server with hot module replacement
npm run dev
```

Keep both servers running:
- **Backend**: `php artisan serve` (port 8000)
- **Frontend**: `npm run dev` (port 5173 by default)

## Development Workflow

### 1. Start Development Server

```bash
npm run dev
```

This starts Vite with hot module replacement enabled.

### 2. Create a New Vue Component

**File**: `resources/components/MyNewComponent.vue`

```vue
<template>
  <div class="my-component">
    <h2>{{ title }}</h2>
    <button @click="count++">Count: {{ count }}</button>
  </div>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  title: String
})

const count = ref(0)
</script>

<style scoped>
.my-component {
  padding: 1rem;
  border: 1px solid #ccc;
}
</style>
```

### 3. Register Component in Entry Point

**File**: `resources/js/{moduleName}/{pageName}.js`

Example: `resources/js/ruby/employees.js`

```javascript
import { createApp } from 'vue'
import MyNewComponent from '@/components/MyNewComponent.vue'

const app = createApp({})
app.component('MyNewComponent', MyNewComponent)
app.mount('#app')
```

> **Note**: No changes to `vite.config.js` are needed. The glob pattern automatically discovers all files in `resources/js/*.js` and `resources/js/*/*.js` directories.

### 4. Use in Blade Template

**File**: `Modules/{ModuleName}/resources/views/{page}.blade.php`

Example: `Modules/Ruby/resources/views/employees/index.blade.php`

```blade
@extends('layouts.app')

@section('content')
    <div id="app">
        <my-new-component title="Hello World" />
    </div>
@endsection

@vite(['resources/js/ruby/employees.js'])
```

## Common Tasks

### Add Props to Component

```vue
<script setup>
const props = defineProps({
  title: {
    type: String,
    required: true
  },
  count: {
    type: Number,
    default: 0
  },
  items: Array
})
</script>
```

### Emit Events

```vue
<script setup>
const emit = defineEmits(['save', 'cancel'])

function handleSave() {
  emit('save', formData)
}
</script>
```

### Use Slots

```vue
<template>
  <div class="card">
    <div class="card-header">
      <slot name="header">Default Header</slot>
    </div>
    <div class="card-body">
      <slot />
    </div>
  </div>
</template>
```

### API Calls in Composable

```javascript
export function useData() {
  const data = ref(null)
  const loading = ref(false)
  const error = ref(null)

  const fetchData = async () => {
    loading.value = true
    try {
      const response = await fetch('/api/data')
      data.value = await response.json()
    } catch (err) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }

  return { data, loading, error, fetchData }
}
```

### Form Handling

```vue
<script setup>
import { ref } from 'vue'

const formData = ref({
  name: '',
  email: ''
})

async function handleSubmit() {
  const response = await fetch('/api/submit', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify(formData.value)
  })
  // Handle response
}
</script>

<template>
  <form @submit.prevent="handleSubmit">
    <input v-model="formData.name" placeholder="Name" />
    <input v-model="formData.email" type="email" placeholder="Email" />
    <button type="submit">Submit</button>
  </form>
</template>
```

## Debugging

### Vue DevTools

Install [Vue DevTools browser extension](https://devtools.vuejs.org/) to inspect components and state.

### Browser Console

```javascript
// Access app instance from console
const app = document.querySelector('#app').__vue__

// Inspect component data
app.$data
```

### Network Tab

Monitor API calls and network performance in browser DevTools.

## Production Build

```bash
npm run production
```

This creates optimized, minified assets ready for deployment.

## Troubleshooting

### Components Not Rendering

1. Check if component is registered in JavaScript entry point
2. Verify `@vite()` directive is in Blade template
3. Check browser console for errors

### Hot Module Replacement Not Working

1. Ensure `npm run dev` is running
2. Check Vite configuration
3. Try restarting the dev server

### Build Errors

1. Run `npm run lint` to check for syntax errors
2. Verify all imports are correct
3. Check component props and events
