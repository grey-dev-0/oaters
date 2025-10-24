# Breadcrumb Component Bundle

## Overview

A Bootstrap 4-compatible breadcrumb navigation component bundle that displays the current page location in a hierarchical navigation structure. Provides visual context for users and improves navigation between related pages.

**Location**: `resources/components/breadcrumb/`

**Components**:
- `Breadcrumb` - Main breadcrumb container
- `BcItem` - Individual breadcrumb item

**Type**: Component Bundle

## Component Registration

The breadcrumb bundle exports a `load` function to register both components:

```javascript path=null start=null
import Breadcrumb from '@/components/breadcrumb/breadcrumb.vue';
import BcItem from '@/components/breadcrumb/item.vue';

function load(app){
    app.component('Breadcrumb', Breadcrumb);
    app.component('BcItem', BcItem);
}

export default {load}
```

Or load via common loader:

```javascript path=null start=null
import Breadcrumbs from '../../components/breadcrumb';

common.loadBundles(app, [Breadcrumbs]);
```

## Breadcrumb Component

The main container component that wraps the breadcrumb items.

### Props

None - the component is a simple container.

### Slots

| Slot | Description |
|------|-------------|
| Default | Breadcrumb items (`bc-item` components) |

### Usage

**Basic Breadcrumb**

```vue path=null start=null
<breadcrumb>
    <bc-item url="/">Home</bc-item>
    <bc-item url="/products">Products</bc-item>
    <bc-item active>Electronics</bc-item>
</breadcrumb>
```

**Module Breadcrumb**

```vue path=null start=null
<breadcrumb>
    <bc-item :url="moduleHome">Module Name</bc-item>
    <bc-item :url="sectionUrl">Section</bc-item>
    <bc-item active>Current Page</bc-item>
</breadcrumb>
```

### Real Example from OATERS

```blade path=/opt/public_html/theultragrey/oaters/Modules/Ruby/resources/views/contacts.blade.php start=9
<breadcrumb>
    <bc-item url="{{url('r')}}">Ruby</bc-item>
    <bc-item active>{{trans('ruby::words.staff')}}</bc-item>
</breadcrumb>
```

### Styling

Built-in Bootstrap 4 breadcrumb styles:
- `.breadcrumb` - Main container with light background
- `.breadcrumb-item` - Individual items
- `.breadcrumb-item.active` - Active/current item
- Auto-generated separators between items

## BcItem Component

Individual breadcrumb items that can be either links or static text.

### Props

| Prop | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `url` | String | No | - | Link URL (if not provided, renders as text/static) |
| `active` | Boolean | No | `false` | Marks item as active/current (typically last item) |

### Slots

| Slot | Description |
|------|-------------|
| Default | Item label/text content |

### Usage Patterns

**Link Item**

```vue path=null start=null
<bc-item url="/home">Home</bc-item>
```

**Active/Current Item**

```vue path=null start=null
<bc-item active>Current Page</bc-item>
```

**Text Item (no link)**

```vue path=null start=null
<bc-item>Static Text</bc-item>
```

**With Dynamic Content**

```vue path=null start=null
<bc-item :url="itemUrl">{{ itemLabel }}</bc-item>
```

## Features

- ✅ Bootstrap 4 breadcrumb styling
- ✅ Simple link or static text items
- ✅ Active state for current page
- ✅ Clean, semantic HTML
- ✅ Auto-generated item separators
- ✅ Flexible slot-based content
- ✅ Responsive design

## Architecture

### Structure

```
<div class="col-12">
    <ul class="breadcrumb bg-white">
        <!-- BcItem components rendered here -->
    </ul>
</div>
```

### Item Rendering

- **With URL**: Renders as `<a href="...">` link
- **Without URL**: Renders as plain text or slot content
- **Active**: Adds `.active` class to disable link styling

### Automatic Separators

Bootstrap 4 automatically adds CSS-generated separators (typically ">") between breadcrumb items via the `::before` pseudo-element.

## Common Patterns

### Navigation Hierarchy

```vue path=null start=null
<breadcrumb>
    <bc-item url="/">Home</bc-item>
    <bc-item url="/admin">Admin</bc-item>
    <bc-item url="/admin/users">Users</bc-item>
    <bc-item url="/admin/users/123">John Doe</bc-item>
    <bc-item active>Edit Profile</bc-item>
</breadcrumb>
```

### Module Navigation

```vue path=null start=null
<breadcrumb>
    <bc-item :url="moduleHome">{{ moduleName }}</bc-item>
    <bc-item :url="sectionUrl">{{ sectionName }}</bc-item>
    <bc-item :url="subsectionUrl">{{ subsectionName }}</bc-item>
    <bc-item active>{{ currentPageTitle }}</bc-item>
</breadcrumb>
```

### E-commerce Product Pages

```vue path=null start=null
<breadcrumb>
    <bc-item url="/shop">Shop</bc-item>
    <bc-item url="/shop/electronics">Electronics</bc-item>
    <bc-item url="/shop/electronics/computers">Computers</bc-item>
    <bc-item url="/shop/electronics/computers/laptops">Laptops</bc-item>
    <bc-item active>MacBook Pro 16"</bc-item>
</breadcrumb>
```

### Dynamic Breadcrumb from Route

```javascript path=null start=null
export default {
    data() {
        return {
            breadcrumbs: []
        }
    },
    methods: {
        generateBreadcrumbs() {
            const pathSegments = this.$route.path.split('/').filter(Boolean);
            this.breadcrumbs = [
                { label: 'Home', url: '/' }
            ];
            
            let currentPath = '';
            pathSegments.forEach((segment, index) => {
                currentPath += '/' + segment;
                const isLast = index === pathSegments.length - 1;
                
                this.breadcrumbs.push({
                    label: this.formatLabel(segment),
                    url: isLast ? null : currentPath,
                    active: isLast
                });
            });
        },
        formatLabel(segment) {
            return segment
                .split('-')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
        }
    },
    watch: {
        '$route': 'generateBreadcrumbs'
    },
    mounted() {
        this.generateBreadcrumbs();
    }
}
```

```vue path=null start=null
<breadcrumb>
    <bc-item 
        v-for="(crumb, index) in breadcrumbs" 
        :key="index"
        :url="crumb.url"
        :active="crumb.active"
    >
        {{ crumb.label }}
    </bc-item>
</breadcrumb>
```

### With Icons

```vue path=null start=null
<breadcrumb>
    <bc-item url="/">
        <i class="fas fa-home mr-2"></i>Home
    </bc-item>
    <bc-item url="/dashboard">
        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
    </bc-item>
    <bc-item active>
        <i class="fas fa-file mr-2"></i>Current Document
    </bc-item>
</breadcrumb>
```

### Nested Modules

```vue path=null start=null
<breadcrumb>
    <bc-item url="/admin">Administration</bc-item>
    <bc-item url="/admin/users">User Management</bc-item>
    <bc-item url="/admin/users/departments">Departments</bc-item>
    <bc-item url="/admin/users/departments/123">Engineering</bc-item>
    <bc-item active>Team Members</bc-item>
</breadcrumb>
```

## Best Practices

1. **Always start with Home/Root**: Provides clear context
   ```vue path=null start=null
   <breadcrumb>
       <bc-item url="/">Home</bc-item>
       <!-- other items -->
   </breadcrumb>
   ```

2. **Mark current page as active**: Improves UX clarity
   ```vue path=null start=null
   <bc-item active>Current Page</bc-item>
   ```

3. **Don't make active item a link**: No need to link to current page
   ```vue path=null start=null
   <!-- ✓ Good -->
   <bc-item active>About Us</bc-item>
   
   <!-- ✗ Bad - don't add url to active item -->
   <bc-item url="/about" active>About Us</bc-item>
   ```

4. **Keep breadcrumbs concise**: Maximum 5-7 items typically
   ```vue path=null start=null
   <!-- ✓ Good - clear hierarchy -->
   <bc-item url="/">Home</bc-item>
   <bc-item url="/docs">Docs</bc-item>
   <bc-item url="/docs/guide">Guide</bc-item>
   <bc-item active>Getting Started</bc-item>
   
   <!-- ✗ Bad - too many levels -->
   <bc-item>Level 1</bc-item>
   <bc-item>Level 2</bc-item>
   <bc-item>Level 3</bc-item>
   <bc-item>Level 4</bc-item>
   <bc-item>Level 5</bc-item>
   <bc-item active>Level 6</bc-item>
   ```

5. **Use semantic labels**: User-friendly, descriptive text
   ```vue path=null start=null
   <!-- ✓ Good -->
   <bc-item url="/admin/users">User Management</bc-item>
   
   <!-- ✗ Bad - unclear -->
   <bc-item url="/admin/users">Admin/Users</bc-item>
   ```

6. **Place breadcrumbs above page content**: Standard UX position
   ```vue path=null start=null
   <div class="page-container">
       <breadcrumb><!-- breadcrumb items --></breadcrumb>
       <h1>Page Title</h1>
       <!-- page content -->
   </div>
   ```

## Styling

### Default Bootstrap 4 Styles

```css
.breadcrumb {
    background-color: #ffffff;
    padding: 0.75rem 1rem;
    margin-bottom: 1rem;
}

.breadcrumb-item {
    display: inline-block;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "/";
    padding: 0 0.5rem;
    color: #6c757d;
}

.breadcrumb-item.active {
    color: #6c757d;
}

.breadcrumb-item a {
    color: #007bff;
    text-decoration: none;
}

.breadcrumb-item a:hover {
    color: #0056b3;
    text-decoration: underline;
}
```

### Custom Styling

Override Bootstrap defaults with custom CSS:

```css path=null start=null
/* Custom separator */
.breadcrumb-item + .breadcrumb-item::before {
    content: "→";
    padding: 0 0.75rem;
    color: #007bff;
}

/* Dark theme */
.breadcrumb.dark {
    background-color: #343a40;
    color: #ffffff;
}

.breadcrumb.dark .breadcrumb-item a {
    color: #0dcaf0;
}

.breadcrumb.dark .breadcrumb-item.active {
    color: #adb5bd;
}
```

## Accessibility

### ARIA Attributes

Enhance breadcrumb accessibility:

```vue path=null start=null
<breadcrumb role="navigation" aria-label="Breadcrumb">
    <bc-item url="/">Home</bc-item>
    <bc-item url="/docs">Documentation</bc-item>
    <bc-item active aria-current="page">Getting Started</bc-item>
</breadcrumb>
```

## Troubleshooting

### Breadcrumbs not showing
- Verify `breadcrumb` component is used as container
- Check `bc-item` components are inside breadcrumb
- Ensure Bootstrap CSS is loaded

### Separators not appearing
- Verify Bootstrap 4 CSS is loaded
- Check `.breadcrumb-item` styling isn't overridden
- Ensure CSS pseudo-elements are enabled

### Active item styling wrong
- Verify `active` prop is set on the correct item (usually last)
- Check CSS specificity isn't overriding active state
- Don't add `url` to active items

### Links not working
- Verify `url` prop is provided for link items
- Check href attribute is rendering in HTML
- Ensure navigation system is working

### Responsive issues
- Check viewport meta tag is present
- Verify Bootstrap responsive classes work
- Test on mobile devices

## Browser Compatibility

- ✅ Chrome/Edge (all versions)
- ✅ Firefox (all versions)
- ✅ Safari (all versions)
- ✅ IE 11 (with polyfills)

## Dependencies

- Bootstrap 4+ (CSS classes)
- Vue 3

## See Also

- [Navbar Component](./navbar.md) - For main navigation
- Bootstrap 4 Breadcrumb Documentation
