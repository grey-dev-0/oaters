# Tab Component Bundle

## Overview

The Tab component bundle provides a Bootstrap-styled tabbed interface with support for multiple tabs and panes. It consists of three components working together to create an accessible, interactive tab navigation system. The bundle uses Bootstrap's nav-tabs styling and fade animations for smooth transitions between tab content.

**Location**: `resources/components/tab/`

**Components**:
- `Tabs` - Container component that manages the tab state
- `Tab` - Individual tab button in the navigation
- `Pane` - Tab content container

## Architecture

```
┌──────────────────────────────────────────────────┐
│              Tabs                                │
│  (Manages active tab state)                      │
│                                                  │
│  ┌─────────────────────────────────────────────┐ │
│  │ Navigation (.nav.nav-tabs)                  │ │
│  │  ┌─────┐  ┌─────┐  ┌─────┐                 │ │
│  │  │ Tab │  │ Tab │  │ Tab │                 │ │
│  │  └─────┘  └─────┘  └─────┘                 │ │
│  └─────────────────────────────────────────────┘ │
│                                                  │
│  ┌─────────────────────────────────────────────┐ │
│  │ Content (.tab-content)                      │ │
│  │  ┌──────────────────────────────────────┐   │ │
│  │  │ Pane (active)                        │   │ │
│  │  │ {{ pane1Content }}                   │   │ │
│  │  └──────────────────────────────────────┘   │ │
│  │  ┌──────────────────────────────────────┐   │ │
│  │  │ Pane (hidden)                        │   │ │
│  │  │ {{ pane2Content }}                   │   │ │
│  │  └──────────────────────────────────────┘   │ │
│  └─────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────┘
```

## Component Registration

The Tab bundle is registered via an index file that exports a `load` function:

```javascript path=/opt/public_html/theultragrey/oaters/resources/components/tab/index.js start=1
import {defineAsyncComponent} from "vue";

function load(app){
    app.component('Tabs', defineAsyncComponent(() => import('./tabs.vue')));
    app.component('Tab', defineAsyncComponent(() => import('./tab.vue')));
    app.component('Pane', defineAsyncComponent(() => import('./pane.vue')));
}

export default {load};
```

**Usage in Entry Point**:

```javascript path=null start=null
// resources/js/app.js
import {createApp} from "vue";
import Tab from "../../components/tab";

let app = createApp({
    // ... app config
});

// Load the Tab bundle
Tab.load(app);

app.mount('#app');
```

## Components

### Tabs

The main container component that manages the active tab state and provides it to child Tab and Pane components via provide/inject.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `activeTab` | String | - | ID (with `#` prefix) of the active tab pane to display |

#### Slots

| Slot | Description |
|------|-------------|
| `navigation` | Tab button navigation (contains `<Tab>` components) |
| Default | Tab pane content (contains `<Pane>` components) |

#### Example Usage

```vue path=null start=null
<tabs active-tab="#tab-1">
  <template #navigation>
    <tab target="#tab-1">Overview</tab>
    <tab target="#tab-2">Details</tab>
    <tab target="#tab-3">Settings</tab>
  </template>
  
  <pane id="tab-1">
    Overview content here
  </pane>
  <pane id="tab-2">
    Details content here
  </pane>
  <pane id="tab-3">
    Settings content here
  </pane>
</tabs>
```

### Tab

Individual tab button component that renders as a navigation item. The component automatically highlights itself if its target matches the active tab.

#### Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| `target` | String | Yes | ID (with `#` prefix) of the pane this tab activates |

#### Slots

| Slot | Description |
|------|-------------|
| Default | Tab button text or content |

#### Computed Properties

| Property | Description |
|----------|-------------|
| `style` | Returns `nav-link` class, plus `active` if this tab is active |

#### Example Usage

**Simple Text Tab**:
```vue path=null start=null
<tab target="#overview">Overview</tab>
```

**Tab with Icon**:
```vue path=null start=null
<tab target="#profile">
  <i class="fas fa-user"></i> Profile
</tab>
```

**Tab with Badge**:
```vue path=null start=null
<tab target="#notifications">
  Notifications
  <span class="badge badge-danger">3</span>
</tab>
```

### Pane

Tab content container component that shows or hides based on whether it's the active pane. Uses Bootstrap's `tab-pane fade` classes for styling and animation.

#### Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| `id` | String | Yes | Unique ID for this pane (must match `#id` in Tab target) |

#### Slots

| Slot | Description |
|------|-------------|
| Default | Pane content |

#### Computed Properties

| Property | Description |
|----------|-------------|
| `style` | Returns `tab-pane fade` class, plus `show active` if this pane is active |

#### Example Usage

**Simple Pane**:
```vue path=null start=null
<pane id="overview">
  <p>Welcome to the overview!</p>
</pane>
```

**Pane with Complex Content**:
```vue path=null start=null
<pane id="details">
  <div class="row">
    <div class="col-md-6">
      <h5>Details Section</h5>
      <p>Detailed information here</p>
    </div>
    <div class="col-md-6">
      <form @submit.prevent="submitForm">
        <!-- form fields -->
      </form>
    </div>
  </div>
</pane>
```

## Complete Example

Here's a complete example showing the Tab component in action:

### Blade View

```blade path=null start=null
<div class="card">
  <div class="card-body">
    <div id="app">
      <tabs active-tab="#overview">
        <template #navigation>
          <tab target="#overview">
            <i class="fas fa-eye"></i> Overview
          </tab>
          <tab target="#details">
            <i class="fas fa-info-circle"></i> Details
          </tab>
          <tab target="#settings">
            <i class="fas fa-cog"></i> Settings
          </tab>
        </template>

        <pane id="overview">
          <h5>Product Overview</h5>
          <p>{{ product.description }}</p>
          <p class="text-muted">Price: ${{ product.price }}</p>
        </pane>

        <pane id="details">
          <h5>Product Details</h5>
          <dl class="row">
            <dt class="col-sm-3">SKU</dt>
            <dd class="col-sm-9">{{ product.sku }}</dd>
            <dt class="col-sm-3">Category</dt>
            <dd class="col-sm-9">{{ product.category }}</dd>
            <dt class="col-sm-3">Stock</dt>
            <dd class="col-sm-9">{{ product.stock }} units</dd>
          </dl>
        </pane>

        <pane id="settings">
          <h5>Settings</h5>
          <form @submit.prevent="saveSettings">
            <div class="form-group">
              <label>Visibility</label>
              <select v-model="product.visibility" class="form-control">
                <option value="public">Public</option>
                <option value="private">Private</option>
                <option value="draft">Draft</option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
          </form>
        </pane>
      </tabs>
    </div>
  </div>
</div>
```

### JavaScript Entry Point

```javascript path=null start=null
import {createApp} from "vue";
import Tab from "../../components/tab";

let app = createApp({
    data() {
        return {
            product: {
                name: 'Sample Product',
                description: 'A great product',
                price: 99.99,
                sku: 'PROD-001',
                category: 'Electronics',
                stock: 50,
                visibility: 'public'
            }
        };
    },
    methods: {
        saveSettings() {
            alert('Settings saved!');
        }
    }
});

Tab.load(app);
app.mount('#app');
```

## Features

- ✅ Bootstrap-styled tab navigation
- ✅ Fade animation between tab transitions
- ✅ Automatic active state management via provide/inject
- ✅ Semantic HTML structure
- ✅ Supports icons and badges in tabs
- ✅ Responsive design
- ✅ Support for dynamic content

## Styling

The Tab component uses Bootstrap classes for styling. Customize appearance through:

1. **Bootstrap Tab Classes** (inherited from Tabs component):
   ```vue path=null start=null
   <tabs class="nav-tabs-custom">
   ```

2. **Bootstrap Utilities** (on pane content):
   ```vue path=null start=null
   <pane id="overview" class="p-4">
     <h5 class="mb-3">Overview</h5>
   </pane>
   ```

3. **Custom CSS**:
   ```scss path=null start=null
   .nav-tabs {
     border-bottom: 2px solid #dee2e6;
     
     .nav-link {
       color: #6c757d;
       border: none;
       
       &.active {
         color: #0d6efd;
         border-bottom: 3px solid #0d6efd;
       }
     }
   }
   ```

### Bootstrap Tab Classes

| Class | Effect |
|-------|--------|
| `nav-tabs` | Tab navigation styling (applied by Tabs component) |
| `nav-link` | Individual tab styling (applied by Tab component) |
| `active` | Active tab indicator (applied by Tab when active) |
| `tab-pane` | Tab content container (applied by Pane component) |
| `fade` | Fade animation on tab transition (applied by Pane component) |
| `show` | Display the tab content (applied by Pane when active) |

## Best Practices

1. **Always Provide activeTab Prop**: This controls which tab is displayed initially
   ```vue path=null start=null
   ✅ GOOD
   <tabs active-tab="#overview">
   ```

2. **Match Target to Pane ID**: Ensure Tab targets match Pane IDs with `#` prefix
   ```vue path=null start=null
   ✅ GOOD
   <tab target="#overview" />
   <pane id="overview" />
   
   ❌ BAD
   <tab target="overview" />
   <pane id="#overview" />
   ```

3. **Use Semantic IDs**: Choose meaningful, descriptive pane IDs
   ```vue path=null start=null
   ✅ GOOD
   <pane id="contact-information">
   
   ❌ BAD
   <pane id="tab1">
   ```

4. **Keep Icons Consistent**: Use Font Awesome or another icon library consistently
   ```vue path=null start=null
   <tab target="#settings">
     <i class="fas fa-cog"></i> Settings
   </tab>
   ```

5. **Avoid Too Many Tabs**: Keep to 3-5 tabs for mobile-friendly interfaces
   ```vue path=null start=null
   ✅ GOOD - 3 tabs
   <tab target="#general">General</tab>
   <tab target="#advanced">Advanced</tab>
   <tab target="#help">Help</tab>
   ```

## Common Patterns

### Lazy-Loaded Content

```vue path=null start=null
<script>
export default {
  data() {
    return {
      activeTab: '#overview',
      loadedTabs: new Set()
    };
  },
  watch: {
    activeTab(newVal) {
      this.loadedTabs.add(newVal);
    }
  },
  computed: {
    isLoaded(paneId) {
      return this.loadedTabs.has(paneId);
    }
  }
};
</script>

<template>
  <tabs :active-tab="activeTab" @change="activeTab = $event.target">
    <template #navigation>
      <tab target="#overview">Overview</tab>
      <tab target="#analytics">Analytics</tab>
    </template>

    <pane id="overview">
      <p>Always visible</p>
    </pane>

    <pane v-if="isLoaded('#analytics')" id="analytics">
      <chart :data="analyticsData" />
    </pane>
  </tabs>
</template>
```

### Tab with Forms

```vue path=null start=null
<tabs active-tab="#basic-info">
  <template #navigation>
    <tab target="#basic-info">Basic Information</tab>
    <tab target="#advanced">Advanced Settings</tab>
    <tab target="#preview">Preview</tab>
  </template>

  <pane id="basic-info">
    <form class="needs-validation" novalidate>
      <div class="form-group">
        <label for="name">Name</label>
        <input id="name" v-model="form.name" type="text" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" v-model="form.description" class="form-control" rows="3"></textarea>
      </div>
    </form>
  </pane>

  <pane id="advanced">
    <div class="form-group">
      <label for="visibility">Visibility</label>
      <select id="visibility" v-model="form.visibility" class="form-control">
        <option value="public">Public</option>
        <option value="private">Private</option>
      </select>
    </div>
  </pane>

  <pane id="preview">
    <div class="alert alert-info">
      <h5>{{ form.name }}</h5>
      <p>{{ form.description }}</p>
    </div>
  </pane>
</tabs>
```

### Vertical Tabs

```vue path=null start=null
<div class="row">
  <div class="col-md-3">
    <div class="nav flex-column nav-tabs" role="tablist">
      <!-- Navigation rendered vertically -->
    </div>
  </div>
  <div class="col-md-9">
    <tabs active-tab="#section1">
      <template #navigation>
        <!-- Tabs rendered here but positioned absolutely -->
      </template>
      <!-- Pane content -->
    </tabs>
  </div>
</div>
```

### Tabs with Dynamic Content

```vue path=null start=null
<script>
export default {
  data() {
    return {
      activeTab: '#user-1',
      users: [
        { id: 1, name: 'John', email: 'john@example.com' },
        { id: 2, name: 'Jane', email: 'jane@example.com' },
        { id: 3, name: 'Bob', email: 'bob@example.com' }
      ]
    };
  }
};
</script>

<template>
  <tabs :active-tab="activeTab">
    <template #navigation>
      <tab v-for="user in users" :key="user.id" :target="'#user-' + user.id">
        {{ user.name }}
      </tab>
    </template>

    <pane v-for="user in users" :key="user.id" :id="'user-' + user.id">
      <h5>{{ user.name }}</h5>
      <p>Email: {{ user.email }}</p>
    </pane>
  </tabs>
</template>
```

## Troubleshooting

### Tabs Not Working/Showing

- Verify `Tabs`, `Tab`, and `Pane` are properly registered via the bundle's `load()` function
- Check that component names match (case-insensitive)
- Ensure the `activeTab` prop is set on the Tabs component
- Verify there are matching Pane components with IDs for each Tab

### Active Tab Not Updating

- Ensure Tab `target` props have `#` prefix: `target="#tab-id"` not `target="tab-id"`
- Verify Pane `id` props match Tab targets (without `#` in pane): `id="tab-id"`
- Check browser console for JavaScript errors
- Confirm provide/inject is working by inspecting Vue DevTools

### Styling Not Applied

- Verify Bootstrap CSS is included in the page
- Check that `nav-tabs` class is present on navigation container
- Ensure `tab-pane` class is present on content panes
- Look for conflicting CSS rules

### Content Not Showing in Panes

- Verify pane ID matches the tab target (ID should not have `#`, target should)
- Check that pane ID is unique; duplicate IDs can cause issues
- Ensure `activeTab` is set to a valid pane ID

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
- Bootstrap 4 or 5 (for styling)
- Font Awesome (optional, for icons)

## See Also

- [Modal](/components/modal) - For tab-like dialog windows
- [Card](/components/card) - For content containers
- [Navbar](/components/navbar) - For navigation with similar styling
