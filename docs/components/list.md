# List Component Bundle

## Overview

The List component bundle provides a Bootstrap-styled list display component with built-in support for collapsible items. It consists of two components working together to create accessible, hierarchical list structures with optional collapse/expand functionality powered by Bootstrap's collapse plugin.

**Location**: `resources/components/list/`

**Components**:
- `List` - Container component that manages list styling and collapse state
- `ListItem` - Individual list item with optional collapse functionality

## Architecture

```
┌──────────────────────────────────┐
│           List                   │
│  (Bootstrap .list-group)         │
│                                  │
│  ┌────────────────────────────┐  │
│  │ ListItem                   │  │
│  │ (Regular item)             │  │
│  └────────────────────────────┘  │
│                                  │
│  ┌────────────────────────────┐  │
│  │ ListItem                   │  │
│  │ (Collapsible toggle)       │  │
│  │ data-toggle="collapse"     │  │
│  └────────────────────────────┘  │
│                                  │
│  ┌────────────────────────────┐  │
│  │ ListItem (collapse)        │  │
│  │ Hidden content             │  │
│  │ (shown on toggle)          │  │
│  └────────────────────────────┘  │
│                                  │
└──────────────────────────────────┘
```

## Component Registration

The List bundle is registered via an index file that exports a `load` function:

```javascript path=/opt/public_html/theultragrey/oaters/resources/components/list/index.js start=1
import List from './list.vue';
import ListItem from './item.vue';

function load(app){
    app.component('List', List);
    app.component('ListItem', ListItem);
}

export default {load};
```

**Usage in Entry Point**:

```javascript path=null start=null
// resources/js/app.js
import {createApp} from "vue";
import List from "../../components/list";

let app = createApp({
    // ... app config
});

// Load the List bundle
List.load(app);

app.mount('#app');
```

## Components

### List

The main container component that renders as an unordered list (`<ul>`) with Bootstrap list-group styling. It manages styling variations based on parent collapse state.

#### Props

None

#### Slots

| Slot | Description |
|------|-------------|
| Default | List items (`<ListItem>` components) |

#### Computed Properties

| Property | Description |
|----------|-------------|
| `style` | Returns `v-list-group list-group` classes, plus `list-group-flush` if parent has collapse |

#### SCSS Styling

The List component includes built-in SCSS that handles collapse indicator icons:

```scss path=/opt/public_html/theultragrey/oaters/resources/components/list/list.vue start=22
.v-list-group{
    .list-group-item[data-toggle="collapse"]{
        &:has(+ .collapse, +.collapsing)::after{
            text-rendering: auto;
            -webkit-font-smoothing: antialiased;
            padding-top: 2px;
        }

        &:has(+ .collapse:not(.show))::after{
            display: block;
            float: right;
            font: var(--fa-font-solid);
            content: '\f102';
        }

        &:has(+ .collapse.show, + .collapsing)::after{
            display: block;
            float: right;
            font: var(--fa-font-solid);
            content: '\f103';
        }
    }
}
```

#### Example Usage

```vue path=null start=null
<list>
  <list-item>Item 1</list-item>
  <list-item>Item 2</list-item>
  <list-item>Item 3</list-item>
</list>
```

### ListItem

Individual list item component that renders as a list item (`<li>`) with Bootstrap styling. Supports optional collapse functionality via data attributes.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | String | - | ID attribute for the list item element |
| `collapseTarget` | String | - | ID (with `#` prefix) of the collapsible element to toggle |
| `collapse` | Boolean | `false` | If true, this item is a collapsible container |

#### Slots

| Slot | Description |
|------|-------------|
| Default | Item content |

#### Computed Properties

| Property | Description |
|----------|-------------|
| `style` | Returns `list-group-item` class, plus `cursor-pointer` if collapsible, plus `collapse p-0` if collapse |

#### Data Attributes

When `collapseTarget` is set, the component automatically renders with Bootstrap data attributes:
- `data-toggle="collapse"` - Enables collapse functionality
- `data-target` - ID of the target element to collapse
- `role="button"` - Semantic role for accessibility

#### Example Usage

**Simple Item**:
```vue path=null start=null
<list-item>Item text</list-item>
```

**Collapsible Toggle**:
```vue path=null start=null
<list-item collapse-target="#details">
  <strong>Click to expand</strong>
</list-item>
```

**Collapsible Content**:
```vue path=null start=null
<list-item id="details" collapse>
  Hidden content here
</list-item>
```

## Complete Example

Here's a complete example showing the List component with collapsible sections:

### Blade View

```blade path=null start=null
<div class="card">
  <div class="card-header">
    <h5 class="card-title">Documents</h5>
  </div>
  <div class="card-body">
    <div id="app">
      <list>
        <list-item collapse-target="#personal-docs">
          <strong><i class="fas fa-folder-open mr-2"></i>Personal Documents</strong>
        </list-item>
        <list-item id="personal-docs" collapse>
          <list>
            <list-item v-for="doc in personalDocs" :key="doc.id">
              <div class="d-flex justify-content-between align-items-center w-100">
                <div>
                  <i class="fas fa-file-pdf mr-2 text-danger"></i>
                  {{ doc.title }}
                </div>
                <small class="text-muted">{{ doc.uploadedDate }}</small>
              </div>
            </list-item>
          </list>
        </list-item>

        <list-item collapse-target="#work-docs">
          <strong><i class="fas fa-briefcase mr-2"></i>Work Documents</strong>
        </list-item>
        <list-item id="work-docs" collapse>
          <list>
            <list-item v-for="doc in workDocs" :key="doc.id">
              <div class="d-flex justify-content-between align-items-center w-100">
                <div>
                  <i class="fas fa-file-word mr-2 text-primary"></i>
                  {{ doc.title }}
                </div>
                <small class="text-muted">{{ doc.uploadedDate }}</small>
              </div>
            </list-item>
          </list>
        </list-item>

        <list-item collapse-target="#archive-docs">
          <strong><i class="fas fa-archive mr-2"></i>Archived Documents</strong>
        </list-item>
        <list-item id="archive-docs" collapse>
          <list>
            <list-item v-for="doc in archivedDocs" :key="doc.id">
              <div class="d-flex justify-content-between align-items-center w-100">
                <div>
                  <i class="fas fa-file mr-2 text-secondary"></i>
                  <span class="text-muted">{{ doc.title }}</span>
                </div>
                <small class="text-muted">{{ doc.uploadedDate }}</small>
              </div>
            </list-item>
          </list>
        </list-item>
      </list>
    </div>
  </div>
</div>
```

### JavaScript Entry Point

```javascript path=null start=null
import {createApp} from "vue";
import List from "../../components/list";

let app = createApp({
    data() {
        return {
            personalDocs: [
                { id: 1, title: 'Passport.pdf', uploadedDate: '2024-01-15' },
                { id: 2, title: 'Birth Certificate.pdf', uploadedDate: '2024-01-10' }
            ],
            workDocs: [
                { id: 3, title: 'Resume.docx', uploadedDate: '2024-02-01' },
                { id: 4, title: 'Cover Letter.docx', uploadedDate: '2024-02-01' }
            ],
            archivedDocs: [
                { id: 5, title: 'Old Resume.pdf', uploadedDate: '2023-12-01' }
            ]
        };
    }
});

List.load(app);
app.mount('#app');
```

## Features

- ✅ Bootstrap-styled list groups
- ✅ Built-in collapse/expand functionality
- ✅ Automatic chevron icons (up/down)
- ✅ Nested list support (lists within lists)
- ✅ Semantic HTML structure
- ✅ Responsive design
- ✅ Font Awesome integration for icons

## Styling

### Default Styling

The List component uses Bootstrap's list-group classes:

- `.list-group` - Main list container
- `.list-group-item` - Individual list items
- `.list-group-flush` - Applied when in a collapsible context (removes borders)
- `.v-list-group` - Custom class for additional styling

### Custom Styling

Customize the list through:

1. **Bootstrap Classes** (on ListItem):
   ```vue path=null start=null
   <list-item class="list-group-item-primary">
     Primary item
   </list-item>
   ```

2. **Bootstrap Utilities** (for spacing and colors):
   ```vue path=null start=null
   <list-item class="py-3 bg-light">
     Styled item with padding and background
   </list-item>
   ```

3. **Custom CSS**:
   ```scss path=null start=null
   .v-list-group {
     .list-group-item {
       padding: 1rem;
       border-left: 4px solid #007bff;
       
       &:hover {
         background-color: #f8f9fa;
       }
     }
   }
   ```

### Available Bootstrap List Classes

| Class | Effect |
|-------|--------|
| `list-group-item-primary` | Primary background color |
| `list-group-item-success` | Success background color |
| `list-group-item-danger` | Danger background color |
| `list-group-item-warning` | Warning background color |
| `list-group-item-info` | Info background color |
| `list-group-item-light` | Light background color |
| `list-group-item-dark` | Dark background color |

## Best Practices

1. **Use Semantic Structure**: Maintain proper hierarchy with nested lists
   ```vue path=null start=null
   ✅ GOOD
   <list>
     <list-item collapse-target="#sub">Main</list-item>
     <list-item id="sub" collapse>
       <list>
         <list-item>Sub item</list-item>
       </list>
     </list-item>
   </list>
   ```

2. **Unique IDs for Collapsible Items**: Ensure each collapsible item has a unique ID
   ```vue path=null start=null
   ✅ GOOD
   <list-item collapse-target="#section-1">Section 1</list-item>
   <list-item id="section-1" collapse></list-item>
   
   <list-item collapse-target="#section-2">Section 2</list-item>
   <list-item id="section-2" collapse></list-item>
   ```

3. **Match collapse-target Format**: Use `#` prefix in target IDs
   ```vue path=null start=null
   ✅ GOOD
   <list-item collapse-target="#details"></list-item>
   <list-item id="details" collapse></list-item>
   
   ❌ BAD
   <list-item collapse-target="details"></list-item>
   ```

4. **Include Visual Indicators**: Use icons to clarify item types
   ```vue path=null start=null
   <list-item collapse-target="#documents">
     <i class="fas fa-folder"></i> Documents
   </list-item>
   ```

5. **Keep Items Accessible**: Ensure adequate spacing and click targets
   ```vue path=null start=null
   <list-item class="py-2">
     <span class="font-weight-bold">Item Title</span>
     <p class="mb-0 text-muted">Descriptive text</p>
   </list-item>
   ```

## Common Patterns

### Simple List

```vue path=null start=null
<list>
  <list-item v-for="item in items" :key="item.id">
    {{ item.name }}
  </list-item>
</list>
```

### List with Icons and Actions

```vue path=null start=null
<list>
  <list-item v-for="item in items" :key="item.id">
    <div class="d-flex justify-content-between align-items-center w-100">
      <div>
        <i :class="item.icon" class="mr-2"></i>
        <span>{{ item.name }}</span>
      </div>
      <button class="btn btn-sm btn-outline-primary" @click="editItem(item)">
        Edit
      </button>
    </div>
  </list-item>
</list>
```

### Multi-level Nested List

```vue path=null start=null
<list>
  <template v-for="category in categories" :key="category.id">
    <list-item :collapse-target="'#cat-' + category.id">
      <strong>{{ category.name }}</strong>
      <span class="badge badge-secondary ml-2">{{ category.items.length }}</span>
    </list-item>
    <list-item :id="'cat-' + category.id" collapse>
      <list>
        <template v-for="subcategory in category.items" :key="subcategory.id">
          <list-item :collapse-target="'#subcat-' + subcategory.id">
            {{ subcategory.name }}
          </list-item>
          <list-item :id="'subcat-' + subcategory.id" collapse>
            <list>
              <list-item v-for="item in subcategory.children" :key="item.id">
                <i class="fas fa-check text-success mr-2"></i>{{ item.name }}
              </list-item>
            </list>
          </list-item>
        </template>
      </list>
    </list-item>
  </template>
</list>
```

### Collapsible Documents List

```vue path=null start=null
<script>
export default {
  data() {
    return {
      documents: [
        { id: 1, title: 'Document 1.pdf', size: '2.4 MB', type: 'pdf' },
        { id: 2, title: 'Document 2.docx', size: '1.1 MB', type: 'docx' }
      ]
    };
  }
};
</script>

<template>
  <list>
    <list-item collapse-target="#all-documents">
      <strong>Documents</strong>
      <span class="badge badge-info ml-2">{{ documents.length }}</span>
    </list-item>
    <list-item id="all-documents" collapse>
      <list>
        <list-item v-for="doc in documents" :key="doc.id" class="small">
          <div class="d-flex justify-content-between align-items-center w-100">
            <div>
              <i class="fas fa-file mr-2"></i>
              {{ doc.title }}
            </div>
            <div class="text-right">
              <small class="text-muted d-block">{{ doc.size }}</small>
              <button class="btn btn-sm btn-outline-primary mt-1" @click="download(doc)">
                Download
              </button>
            </div>
          </div>
        </list-item>
      </list>
    </list-item>
  </list>
</template>
```

### Filterable List with Collapse Groups

```vue path=null start=null
<script>
export default {
  data() {
    return {
      searchQuery: '',
      items: [
        { id: 1, group: 'Active', name: 'Item 1', status: 'active' },
        { id: 2, group: 'Active', name: 'Item 2', status: 'active' },
        { id: 3, group: 'Inactive', name: 'Item 3', status: 'inactive' }
      ]
    };
  },
  computed: {
    groupedItems() {
      return this.items.reduce((acc, item) => {
        if (!acc[item.group]) acc[item.group] = [];
        acc[item.group].push(item);
        return acc;
      }, {});
    }
  }
};
</script>

<template>
  <div>
    <input 
      v-model="searchQuery" 
      type="text" 
      class="form-control mb-3" 
      placeholder="Search items..."
    >
    <list>
      <template v-for="(group, groupName) in groupedItems" :key="groupName">
        <list-item :collapse-target="'#group-' + groupName">
          <strong>{{ groupName }}</strong>
          <span class="badge ml-2">{{ group.length }}</span>
        </list-item>
        <list-item :id="'group-' + groupName" collapse>
          <list>
            <list-item v-for="item in group" :key="item.id">
              <span :class="['badge', item.status === 'active' ? 'badge-success' : 'badge-secondary']">
                {{ item.status }}
              </span>
              <span class="ml-2">{{ item.name }}</span>
            </list-item>
          </list>
        </list-item>
      </template>
    </list>
  </div>
</template>
```

## Troubleshooting

### Collapse Not Working

- Verify `List` and `ListItem` are properly registered via the bundle's `load()` function
- Ensure `collapse-target` has `#` prefix: `collapse-target="#target"` not `collapse-target="target"`
- Verify the target ListItem has matching `id` (without `#`): `id="target"`
- Check that Bootstrap's collapse JavaScript is loaded
- Ensure the collapse item has the `collapse` prop set to `true`

### Icons Not Showing

- Verify Font Awesome CSS is included (`var(--fa-font-solid)` requires Font Awesome 6.0+)
- Check browser console for CSS variable errors
- Inspect the `::after` pseudo-element in DevTools
- Ensure the SCSS is properly compiled to CSS

### Styling Not Applied

- Verify Bootstrap CSS is included in the page
- Check for conflicting CSS rules overriding list-group styles
- Ensure custom CSS is scoped or has proper specificity
- Use browser DevTools to inspect computed styles

### Nested Lists Not Expanding Properly

- Ensure each nested list has unique IDs for collapse targets
- Verify parent collapse item is properly closed before opening child items
- Check that spacing and padding don't interfere with content display
- Consider using `list-group-flush` class for nested lists

## Browser Compatibility

| Browser | Support |
|---------|---------|
| Chrome | ✅ Full support |
| Firefox | ✅ Full support |
| Safari | ✅ Full support |
| Edge | ✅ Full support |
| IE 11 | ⚠️ Requires Vue 3 and Bootstrap 4 polyfills |

## Dependencies

- Vue 3
- Bootstrap 4 or 5 (for list-group styling)
- Font Awesome 6.0+ (for collapse icons)
- Bootstrap JavaScript (for collapse plugin)

## Real-World Usage

This component is used in the OATERS application for displaying hierarchical documents:

```blade path=/opt/public_html/theultragrey/oaters/Modules/Ruby/resources/views/components/modals/profile.blade.php start=73
<list v-if="openContact.applicant && openContact.applicant.documents && openContact.applicant.documents.length">
    <list-item collapse-target="#documents"><strong>{{trans('common::words.documents')}}</strong></list-item>
    <list-item id="documents" collapse>
        <list>
            <list-item v-for="document in openContact.applicant.documents">
                @{{document.title}}
                <a :href="document.download_url" target="_blank" class="btn btn-sm btn-outline-primary float-right" title="{{trans('common::words.download')}}"><i class="fa fas fa-download mr-2"></i>{{trans('common::words.download')}}</a>
            </list-item>
        </list>
    </list-item>
</list>
```

## See Also

- [Card](/components/card) - For grouping list content
- [Tab](/components/tab) - For tabbed list views
- [Modal](/components/modal) - For modal dialogs containing lists
- [Loader](/components/loader) - For loading states while fetching list data
