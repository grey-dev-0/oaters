# Navbar Component Bundle

## Overview

A responsive navigation bar component bundle that provides Bootstrap 4-compatible navigation with support for dropdowns, scheme variants, and customizable branding. Consists of two components working together to create flexible navigation structures.

**Location**: `resources/components/navbar/`

**Components**:
- `Navbar` - Main navigation container
- `NavItem` - Individual navigation item (link or dropdown)

**Type**: Component Bundle

## Component Registration

The navbar bundle exports a `load` function to register both components:

```javascript path=null start=null
import Navbar from '@/components/navbar/navbar.vue';
import NavItem from '@/components/navbar/item.vue';

function load(app){
    app.component('Navbar', Navbar);
    app.component('NavItem', NavItem);
}

export default {load}
```

Or load via common loader:

```javascript path=null start=null
import Navbar from '../../components/navbar';

common.loadBundles(app, [Navbar]);
```

## Navbar Component

The main navigation container component that wraps the navigation structure.

### Props

| Prop | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `brand` | String | No | - | Brand name/logo text displayed on the left |
| `home` | String | No | - | URL for the brand link |
| `scheme` | String | No | `'light'` | Bootstrap navbar scheme: `light` or `dark` |
| `bgColor` | String | No | `'light'` | Background color class (e.g., `red-4`, `blue-2`) |

### Slots

| Slot | Description |
|------|-------------|
| Default | Left-aligned navigation items |
| `right` | Right-aligned navigation items |

### Usage

**Basic Navbar**

```vue path=null start=null
<navbar 
    brand="My App" 
    home="/"
    scheme="light"
    bg-color="light"
>
    <nav-item url="/about">About</nav-item>
    <nav-item url="/services">Services</nav-item>
    <nav-item url="/contact">Contact</nav-item>
</navbar>
```

**Dark Navbar with Color**

```vue path=null start=null
<navbar 
    brand="Ruby" 
    :home="baseUrl"
    scheme="dark" 
    bg-color="red-4"
>
    <!-- Left items -->
    <nav-item>
        <template #label>Organization</template>
        <nav-item in-dropdown url="/departments">Departments</nav-item>
        <nav-item in-dropdown url="/staff">Staff</nav-item>
    </nav-item>
    
    <!-- Right items -->
    <template #right>
        <nav-item>
            <template #label>User Name</template>
            <nav-item in-dropdown url="/profile">Profile</nav-item>
            <nav-item in-dropdown url="/logout">Logout</nav-item>
        </nav-item>
    </template>
</navbar>
```

### Real Example from OATERS

```blade path=/opt/public_html/theultragrey/oaters/Modules/Ruby/resources/views/master.blade.php start=17
<navbar brand="Ruby" home="{{url('r')}}" scheme="dark" bg-color="red-4">
    <nav-item @if(Route::is(['ruby::departments.*', 'ruby::contacts.*', 'ruby::structure.*'])) active @endif>
        <template #label>{{trans('common::words.organization')}}</template>
        <nav-item @if(Route::is('ruby::departments.index')) active @endif in-dropdown url="{{url('r/departments')}}">{{trans('common::words.departments')}}</nav-item>
        <nav-item @if(Route::is('ruby::contacts.index')) active @endif in-dropdown url="{{url('r/contacts')}}">{{trans('ruby::words.staff')}}</nav-item>
        <nav-item @if(Route::is('ruby::contacts.structure')) active @endif in-dropdown url="{{route('ruby::contacts.structure')}}">{{trans('common::words.structure')}}</nav-item>
    </nav-item>
    <nav-item>
        <template #label>{{trans('common::words.personnel')}}</template>
        <nav-item in-dropdown url="{{url('r/attendance')}}">{{trans('ruby::words.attendance')}}</nav-item>
        <nav-item in-dropdown url="{{url('r/notices')}}">{{trans('ruby::words.notices')}}</nav-item>
        <nav-item in-dropdown url="{{url('r/leaves')}}">{{trans('ruby::words.leaves')}}</nav-item>
        <nav-item in-dropdown url="{{url('r/visas')}}">{{trans('ruby::words.visas')}}</nav-item>
    </nav-item>
    <template #right>
        <nav-item>
            <template #label>{{auth()->user()->contact->name}}</template>
            <nav-item in-dropdown url="{{url('s/logout')}}">{{trans('sapphire::admin.login.logout')}}</nav-item>
        </nav-item>
    </template>
</navbar>
```

## NavItem Component

Individual navigation items that can be either simple links or dropdown triggers.

### Props

| Prop | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `url` | String | No | - | Link URL (if not provided, creates a dropdown) |
| `inDropdown` | Boolean | No | `false` | Renders as dropdown item instead of nav-item |
| `active` | Boolean | No | `false` | Marks item as active/selected |

### Slots

| Slot | Description |
|------|-------------|
| Default | Item label/content |
| `label` | Dropdown label (only when creating dropdown) |

### Usage Patterns

**Simple Link Item**

```vue path=null start=null
<nav-item url="/about">About Us</nav-item>
```

**Active Link Item**

```vue path=null start=null
<nav-item url="/services" :active="true">Services</nav-item>
```

**Dropdown Item**

```vue path=null start=null
<nav-item>
    <template #label>Products</template>
    <nav-item in-dropdown url="/products/electronics">Electronics</nav-item>
    <nav-item in-dropdown url="/products/software">Software</nav-item>
    <nav-item in-dropdown url="/products/services">Services</nav-item>
</nav-item>
```

**Dropdown with Active Item**

```vue path=null start=null
<nav-item :active="isProductsActive">
    <template #label>Products</template>
    <nav-item in-dropdown url="/products/new" :active="currentPage === 'new'">New</nav-item>
    <nav-item in-dropdown url="/products/featured" :active="currentPage === 'featured'">Featured</nav-item>
</nav-item>
```

## Features

- ✅ Responsive Bootstrap 4 navbar
- ✅ Support for dark and light schemes
- ✅ Dropdown menus with nested items
- ✅ Active state highlighting
- ✅ Left and right-aligned items
- ✅ Customizable brand and colors
- ✅ Mobile-friendly toggler button
- ✅ Automatic dropdown background color adjustment

## Architecture

### Responsive Behavior

- **Desktop** (≥768px): Full navigation displayed, toggler hidden
- **Mobile** (<768px): Navigation collapsed, toggler button visible
- Collapse ID: `collapsible-navbar`

### Color Scheme

The navbar supports two schemes:

| Scheme | Description |
|--------|-------------|
| `light` | Light background with dark text |
| `dark` | Dark background with light text |

### Dropdown Background Color

When using non-light background colors, dropdown menus automatically get a lighter shade:
- `red-4` background → `red-10` dropdown background
- Pattern: Takes color name and replaces numeric suffix with `-10`

## Styling

### Component Styles

Built-in Bootstrap 4 navbar classes:
- `.navbar` - Main container
- `.navbar-expand-md` - Breakpoint for toggler
- `.navbar-light` / `.navbar-dark` - Scheme variants
- `.navbar-brand` - Brand text
- `.navbar-nav` - Navigation list
- `.nav-item` - Individual items
- `.dropdown-menu` - Dropdown container

### Custom Background Colors

Uses Bootstrap color utilities:
- `bg-red-4` - Red background
- `bg-blue-2` - Blue background
- `bg-grey-8` - Grey background
- etc.

## Common Patterns

### Module Navigation with Dropdowns

```vue path=null start=null
<navbar brand="Module Name" :home="baseUrl" scheme="dark" bg-color="blue-4">
    <nav-item>
        <template #label>Section 1</template>
        <nav-item in-dropdown :url="route1">Item 1</nav-item>
        <nav-item in-dropdown :url="route2">Item 2</nav-item>
    </nav-item>
    
    <nav-item>
        <template #label>Section 2</template>
        <nav-item in-dropdown :url="route3">Item 3</nav-item>
        <nav-item in-dropdown :url="route4">Item 4</nav-item>
    </nav-item>
    
    <template #right>
        <nav-item>
            <template #label>{{ currentUser }}</template>
            <nav-item in-dropdown url="/profile">Profile</nav-item>
            <nav-item in-dropdown url="/settings">Settings</nav-item>
            <nav-item in-dropdown url="/logout">Logout</nav-item>
        </nav-item>
    </template>
</navbar>
```

### Dynamic Active State

```javascript path=null start=null
export default {
    data() {
        return {
            currentRoute: ''
        }
    },
    methods: {
        isActive(routeName) {
            return this.currentRoute === routeName;
        }
    },
    mounted() {
        // Track current route
        this.currentRoute = this.$route.name;
    }
}
```

```vue path=null start=null
<navbar brand="App">
    <nav-item url="/dashboard" :active="isActive('dashboard')">Dashboard</nav-item>
    <nav-item :active="isActive('users')">
        <template #label>Users</template>
        <nav-item in-dropdown url="/users" :active="isActive('users')">All Users</nav-item>
        <nav-item in-dropdown url="/users/create" :active="isActive('create-user')">Add User</nav-item>
    </nav-item>
</navbar>
```

### Multi-level Navigation

```vue path=null start=null
<navbar brand="Complex App" scheme="dark" bg-color="purple-3">
    <!-- Dropdown with nested structure -->
    <nav-item>
        <template #label>Configuration</template>
        <nav-item in-dropdown url="/config/general">General Settings</nav-item>
        <nav-item in-dropdown url="/config/users">User Management</nav-item>
        <nav-item in-dropdown url="/config/roles">Roles & Permissions</nav-item>
        <nav-item in-dropdown url="/config/security">Security</nav-item>
    </nav-item>
    
    <!-- Another dropdown -->
    <nav-item>
        <template #label>Reports</template>
        <nav-item in-dropdown url="/reports/sales">Sales Report</nav-item>
        <nav-item in-dropdown url="/reports/analytics">Analytics</nav-item>
        <nav-item in-dropdown url="/reports/export">Export Data</nav-item>
    </nav-item>
</navbar>
```

## Best Practices

1. **Always provide brand and home**: Makes navigation consistent
   ```vue path=null start=null
   <navbar brand="My App" home="/"></navbar>
   ```

2. **Use semantic structure**: Organize items logically
   ```vue path=null start=null
   <!-- ✓ Good - grouped by function -->
   <nav-item><template #label>Admin</template>...</nav-item>
   <nav-item><template #label>User</template>...</nav-item>
   
   <!-- ✗ Bad - random order -->
   <nav-item><template #label>Random</template>...</nav-item>
   <nav-item><template #label>Order</template>...</nav-item>
   ```

3. **Use right slot for user menu**: Standard UX pattern
   ```vue path=null start=null
   <template #right>
       <nav-item>
           <template #label>{{ username }}</template>
           <nav-item in-dropdown url="/profile">Profile</nav-item>
           <nav-item in-dropdown url="/logout">Logout</nav-item>
       </nav-item>
   </template>
   ```

4. **Mark current page as active**: Helps user orientation
   ```vue path=null start=null
   <nav-item :active="currentPage === 'about'" url="/about">About</nav-item>
   ```

5. **Use scheme matching content**: Dark scheme for light backgrounds, vice versa
   ```vue path=null start=null
   <!-- Dark navbar on light background -->
   <navbar scheme="dark" bg-color="grey-10"></navbar>
   
   <!-- Light navbar on dark background -->
   <navbar scheme="light" bg-color="blue-2"></navbar>
   ```

## Troubleshooting

### Navbar not responsive
- Verify Bootstrap JavaScript is loaded
- Check that toggler button is present
- Ensure `data-toggle="collapse"` and `data-target` are correct

### Dropdown not working
- Verify item has no `url` prop to create dropdown
- Use `<template #label>` for dropdown title
- Check child items have `in-dropdown` prop

### Colors not applying
- Verify color class exists in your CSS framework
- Check `bgColor` prop uses correct format (e.g., `red-4`)
- Ensure Bootstrap utilities are loaded

### Active state not showing
- Verify `active` prop is set on correct item
- Check CSS doesn't override active styling
- Use `:active` binding for dynamic active states

### Mobile toggler issues
- Check viewport meta tag is present
- Verify collapse ID matches `data-target`
- Ensure Bootstrap JS is loaded before Vue app

## Browser Compatibility

- ✅ Chrome/Edge (all versions)
- ✅ Firefox (all versions)
- ✅ Safari (all versions)
- ✅ IE 11 (with polyfills)

## Dependencies

- Bootstrap 4+ (CSS classes and JavaScript)
- jQuery (for Bootstrap JavaScript functionality)
- Vue 3

## See Also

- [Breadcrumb Component](./breadcrumb.md) - For page navigation trails
- Bootstrap 4 Navbar Documentation
