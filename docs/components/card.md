# Card Component

## Overview

A flexible card container component with optional header, toolbar, footer, and color customization.

**Location**: `resources/components/card.vue`

**Type**: Simple Component

## Props

| Prop | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `title` | String | Yes | - | Card header title |
| `color` | String | No | - | Bootstrap color class for header (e.g., `cyan-2`, `green-10`) |

## Computed

| Property | Description |
|----------|-------------|
| `headerClass` | Builds CSS classes for header including color |
| `whiteTitle` | Auto-determines if title should be white based on color darkness |

## Slots

| Slot | Description |
|------|-------------|
| Default | Main card body content |
| `toolbar` | Toolbar buttons in header (right-aligned) |
| `footer` | Card footer content |

## Usage

### Basic Card

```vue path=null start=null
<card title="User Information">
    <p>Card content goes here...</p>
</card>
```

### Card with Color

```vue path=null start=null
<card title="Dashboard Statistics" color="teal-2">
    <p>Statistics content...</p>
</card>
```

### Card with Toolbar

```vue path=null start=null
<card title="Staff" color="teal-2">
    <template #toolbar>
        <div class="btn btn-sm btn-success" @click="addContact">
            <i class="fa fas fa-plus"></i> New
        </div>
    </template>
    
    <!-- Main content -->
    <vue-datatable>...</vue-datatable>
</card>
```

### Card with Footer

```vue path=null start=null
<card title="Report">
    <p>Report content...</p>
    
    <template #footer>
        <div class="btn btn-primary">Download PDF</div>
    </template>
</card>
```

### Card with Multiple Toolbar Buttons

```vue path=null start=null
<card title="Employees" color="blue-3">
    <template #toolbar>
        <div class="btn btn-sm btn-primary mr-1" @click="exportData">
            <i class="fas fa-download"></i> Export
        </div>
        <div class="btn btn-sm btn-success" @click="addEmployee">
            <i class="fas fa-plus"></i> Add
        </div>
    </template>
    
    <p>Employee list content...</p>
</card>
```

### Real Examples from OATERS

```blade path=/opt/public_html/theultragrey/oaters/Modules/Ruby/resources/views/contacts.blade.php start=19
<card title="{{trans('ruby::words.staff')}}" color="teal-2">
    <template #toolbar>
        <div class="btn btn-sm btn-success" title="{{trans('ruby::contacts.new')}}" @click="addContact"><i class="fa fas fa-plus align-middle"></i><span class="d-none d-sm-inline-block ml-2">{{trans('ruby::contacts.new')}}</span></div>
    </template>

    <vue-datafilter :cols="4" datatable-ref="contactsTable">
        <!-- filters -->
    </vue-datafilter>

    <x-ruby::tables.contacts/>
</card>
```

```blade path=/opt/public_html/theultragrey/oaters/Modules/Sapphire/resources/views/admin/dashboard.blade.php start=24
<card title="{{trans('sapphire::admin.tenants.new')}}" color="green-10">
    <vue-table id="latest-tenants" class="table-striped table-hover mb-0">
        <!-- table content -->
    </vue-table>
</card>
```

## Component Registration

```javascript path=null start=null
import {createApp} from "vue";
import Card from "@/components/card.vue";

let app = createApp({
    methods: {
        addItem() {
            console.log('Add item clicked');
        }
    }
});

app.component('Card', Card);
app.mount('#app');
```

## Features

- ✅ Automatic white text for dark colors (colors 1-5)
- ✅ Flexible layout with slots
- ✅ Bootstrap 4 card structure
- ✅ Toolbar with max-height constraint (28px)
- ✅ Optional header, body, and footer sections

## Color System

The component uses a numeric color naming convention:

| Color Range | Text Color | Use Case |
|-------------|------------|----------|
| `color-1` to `color-5` | White | Dark backgrounds |
| `color-6` to `color-10` | Black (default) | Light backgrounds |

### Examples

```vue path=null start=null
<!-- Dark header with white text -->
<card title="Dark Card" color="blue-2">...</card>

<!-- Light header with default text -->
<card title="Light Card" color="blue-8">...</card>
```

## Available Colors

Common Bootstrap color variants (with numeric suffixes 1-10):
- `grey-1` through `grey-10`
- `red-1` through `red-10`
- `orange-1` through `orange-10`
- `yellow-1` through `yellow-10`
- `green-1` through `green-10`
- `teal-1` through `teal-10`
- `cyan-1` through `cyan-10`
- `blue-1` through `blue-10`
- `purple-1` through `purple-10`
- `pink-1` through `pink-10`

## Styling

### Component Styles

```css path=null start=null
.btn-toolbar {
    max-height: 28px;
}
```

### Custom Styling

Override card styles in your application:

```css path=null start=null
.card {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.card-header {
    font-weight: bold;
    border-bottom: 2px solid;
}

.card-body {
    padding: 1.5rem;
}
```

## Best Practices

1. **Use toolbar slot for actions**: Keep header clean with toolbar slot
   ```vue path=null start=null
   <card title="Data">
       <template #toolbar>
           <button @click="refresh">Refresh</button>
       </template>
   </card>
   ```

2. **Color naming**: Use numeric suffixes (1-10) where lower = darker
   ```vue path=null start=null
   <card title="Important" color="red-3">...</card>
   ```

3. **Footer for actions**: Use footer slot for form buttons or actions
   ```vue path=null start=null
   <card title="Form">
       <form>...</form>
       <template #footer>
           <button type="submit">Save</button>
       </template>
   </card>
   ```

4. **Semantic titles**: Use descriptive, user-friendly titles
   ```vue path=null start=null
   <card title="Recent Activity">...</card>
   ```

5. **Consistent colors**: Maintain color consistency across similar card types
   ```vue path=null start=null
   <!-- All data tables use teal -->
   <card title="Users" color="teal-2">...</card>
   <card title="Products" color="teal-2">...</card>
   ```

## Common Patterns

### Data Table Card

```vue path=null start=null
<card title="Employees" color="blue-3">
    <template #toolbar>
        <div class="btn btn-sm btn-success" @click="addEmployee">
            <i class="fas fa-plus"></i> Add Employee
        </div>
    </template>
    
    <vue-datatable ref="employeeTable">
        <dt-column name="name" data="name">Name</dt-column>
        <dt-column name="email" data="email">Email</dt-column>
        <dt-column name="department" data="department">Department</dt-column>
    </vue-datatable>
</card>
```

### Form Card

```vue path=null start=null
<card title="Edit Profile" color="green-3">
    <form @submit.prevent="saveProfile">
        <div class="form-group">
            <label>Name</label>
            <input v-model="profile.name" class="form-control">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input v-model="profile.email" class="form-control">
        </div>
    </form>
    
    <template #footer>
        <button type="button" class="btn btn-secondary" @click="cancel">
            Cancel
        </button>
        <button type="submit" class="btn btn-primary" @click="saveProfile">
            Save Changes
        </button>
    </template>
</card>
```

### Statistics Card

```vue path=null start=null
<card title="Total Sales" color="cyan-2">
    <h2 class="text-center mb-0">${{ totalSales }}</h2>
    <p class="text-center text-muted">This Month</p>
</card>
```

### Collapsible Card

```vue path=null start=null
<card title="Advanced Settings" color="grey-8">
    <template #toolbar>
        <div class="btn btn-sm btn-outline-secondary" @click="toggleCollapse">
            <i :class="collapsed ? 'fas fa-chevron-down' : 'fas fa-chevron-up'"></i>
        </div>
    </template>
    
    <div v-show="!collapsed">
        <p>Advanced settings content...</p>
    </div>
</card>
```

### Loading State Card

```vue path=null start=null
<card title="Dashboard Data" color="purple-3">
    <div v-if="loading" class="text-center py-5">
        <i class="fas fa-spinner fa-spin fa-3x"></i>
        <p class="mt-3">Loading...</p>
    </div>
    <div v-else>
        <p>{{ data }}</p>
    </div>
</card>
```

### Multi-section Card

```vue path=null start=null
<card title="User Profile" color="blue-4">
    <div class="card-section">
        <h5>Personal Information</h5>
        <p>Name: {{ user.name }}</p>
        <p>Email: {{ user.email }}</p>
    </div>
    
    <hr>
    
    <div class="card-section">
        <h5>Account Settings</h5>
        <p>Status: {{ user.status }}</p>
        <p>Role: {{ user.role }}</p>
    </div>
    
    <template #footer>
        <div class="btn btn-primary" @click="editProfile">Edit Profile</div>
    </template>
</card>
```

## Layout Patterns

### Card Grid

```vue path=null start=null
<div class="row">
    <div class="col-md-6">
        <card title="Card 1" color="blue-3">
            <p>Content 1</p>
        </card>
    </div>
    <div class="col-md-6">
        <card title="Card 2" color="green-3">
            <p>Content 2</p>
        </card>
    </div>
</div>
```

### Full Width Card

```vue path=null start=null
<div class="container-fluid">
    <card title="Full Width Dashboard" color="grey-8">
        <p>Full width content...</p>
    </card>
</div>
```

### Stacked Cards

```vue path=null start=null
<div class="card-stack">
    <card title="Recent Activity" color="cyan-8">
        <p>Activity feed...</p>
    </card>
    
    <card title="Notifications" color="orange-8" class="mt-3">
        <p>Notification list...</p>
    </card>
    
    <card title="Tasks" color="green-8" class="mt-3">
        <p>Task list...</p>
    </card>
</div>
```

## Troubleshooting

### Header styles not applying
- Confirm color class exists in your CSS (e.g., `bg-cyan-2`)
- Check if custom CSS is overriding card styles
- Verify Bootstrap 4 is loaded

### White title not showing
- Verify you're using colors 1-5 (darker colors)
- Check that color naming follows the pattern: `color-1` to `color-10`
- Ensure no CSS is overriding text color

### Toolbar buttons overflowing
- Check `max-height: 28px` constraint on `.btn-toolbar`
- Use `btn-sm` class for toolbar buttons
- Consider reducing number of toolbar buttons

### Footer not displaying
- Verify you're using the `#footer` slot
- Check that slot content is not empty
- Ensure no CSS is hiding the footer

### Card not responsive
- Wrap cards in Bootstrap grid columns
- Use responsive column classes (e.g., `col-md-6`)
- Test on different screen sizes
