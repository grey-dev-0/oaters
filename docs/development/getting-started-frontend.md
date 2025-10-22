# Frontend Development

## Getting Started

### Prerequisites

- Node.js 16+ and npm
- A running OATERS installation

### Installation

```bash
# Install dependencies
npm install

# Build development assets
npm run dev
```
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

**File**: `Modules/Ruby/resources/js/module/page.js`

```javascript
import { createApp } from 'vue'
import MyNewComponent from '@/components/MyNewComponent.vue'

const app = createApp({})
app.component('MyNewComponent', MyNewComponent)
app.mount('#app')
```
### 4. Use in Blade Template

**File**: `Modules/ModuleName/resources/views/page.blade.php`

```blade
@extends('layouts.app')

@section('content')
    <div id="app">
        <my-new-component title="Hello World" />
    </div>
@endsection

@vite(['resources/js/module/page.js'])
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