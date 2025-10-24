# Timeline Component Bundle

## Overview

The Timeline component bundle provides a visual timeline display component for showing chronological sequences of events. It consists of two components that work together to create an elegant, customizable timeline with color-coded entry indicators. The bundle includes built-in SCSS styling with a vertical timeline line and circular entry markers.

**Location**: `resources/components/timeline/`

**Components**:
- `Timeline` - Container component that wraps timeline entries
- `TimelineEntry` - Individual timeline entry with customizable color

## Architecture

```
┌────────────────────────────────────────┐
│          Timeline                      │
│     (Container for entries)            │
│                                        │
│  ┌──────────────────────────────────┐  │
│  │ :before                          │  │
│  │ (Vertical timeline line)         │  │
│  │ ████████████████████████████    │  │
│  │                                  │  │
│  │  ◉ TimelineEntry                │  │
│  │    :before (circle marker)       │  │
│  │    Entry content here...         │  │
│  │                                  │  │
│  │  ◉ TimelineEntry                │  │
│  │    Entry content here...         │  │
│  │                                  │  │
│  │  ◉ TimelineEntry                │  │
│  │    Entry content here...         │  │
│  │                                  │  │
│  └──────────────────────────────────┘  │
└────────────────────────────────────────┘
```

## Component Registration

The Timeline bundle is registered via an index file that exports a `load` function:

```javascript path=/opt/public_html/theultragrey/oaters/resources/components/timeline/index.js start=1
import {defineAsyncComponent} from "vue";

function load(app){
    app.component('Timeline', defineAsyncComponent(() => import('./timeline.vue')));
    app.component('TimelineEntry', defineAsyncComponent(() => import('./timeline-entry.vue')));
}

export default {load};
```

**Usage in Entry Point**:

```javascript path=null start=null
// resources/js/app.js
import {createApp} from "vue";
import Timeline from "../../components/timeline";

let app = createApp({
    // ... app config
});

// Load the Timeline bundle
Timeline.load(app);

app.mount('#app');
```

## Components

### Timeline

The main container component that renders as an unordered list (`<ul>`) with timeline styling. It provides the visual framework for timeline entries including the vertical line.

#### Props

None

#### Slots

| Slot | Description |
|------|-------------|
| Default | Timeline entry components (`<TimelineEntry>`) |

#### SCSS Styling

The Timeline component includes comprehensive SCSS styling:

```scss path=/opt/public_html/theultragrey/oaters/resources/components/timeline/timeline.vue start=13
ul.timeline{
    list-style-type: none;
    position: relative;

    &:before{
        content: ' ';
        background: #d4d9df;
        display: inline-block;
        position: absolute;
        left: 29px;
        width: 2px;
        height: 100%;
        z-index: 400;
    }

    & > li:first-child{
        margin-top: 0;
    }

    & > li{
        margin: 20px 0;
        padding-left: 20px;
    }

    & > li:last-child{
        margin-bottom: 0;
    }

    & > li:before{
        content: ' ';
        background: white;
        display: inline-block;
        position: absolute;
        border-radius: 50%;
        border: 3px solid;
        left: 20px;
        width: 20px;
        height: 20px;
        z-index: 400;
    }
}
```

#### Example Usage

```vue path=null start=null
<timeline>
  <timeline-entry>Event 1</timeline-entry>
  <timeline-entry>Event 2</timeline-entry>
  <timeline-entry>Event 3</timeline-entry>
</timeline>
```

### TimelineEntry

Individual timeline entry component that renders as a list item (`<li>`). Each entry displays a colored circle marker via its `:before` pseudo-element with a customizable color via CSS variable binding.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `color` | String | `'primary'` | CSS color variable name (without `--` prefix) to use for the entry marker |

#### Slots

| Slot | Description |
|------|-------------|
| Default | Entry content (text, HTML, or components) |

#### Color Variable Binding

The component uses Vue's `v-bind()` in CSS to dynamically set the marker color. The `color` prop is transformed into a CSS variable reference:

```javascript

computed: {
    colorVariable(){
        return `var(--${this.color})`;
    }
}
```

The border color is then applied via:

```css
ul.timeline > li:before{
   border-color: v-bind(colorVariable);
}
```

#### Available Colors

Colors are defined as CSS variables in your application's theme. Common Bootstrap/design system colors:

| Color     | CSS Variable  | Usage                       |
|-----------|---------------|-----------------------------|
| Primary   | `--primary`   | Main/featured events        |
| Success   | `--success`   | Completed/successful events |
| Warning   | `--warning`   | In-progress/warning events  |
| Danger    | `--danger`    | Failed/error events         |
| Info      | `--info`      | Information/note events     |
| Secondary | `--secondary` | Neutral events              |

#### Example Usage

**Default Primary Color**:
```vue path=null start=null
<timeline-entry>
  Project started
</timeline-entry>
```

**Custom Color**:
```vue path=null start=null
<timeline-entry color="success">
  Milestone completed
</timeline-entry>
```

**Warning Color**:
```vue path=null start=null
<timeline-entry color="warning">
  Review in progress
</timeline-entry>
```

## Complete Example

Here's a complete example showing the Timeline component in action:

### Blade View

```blade
<div class="card">
  <div class="card-header">
    <h5 class="card-title">Project Timeline</h5>
  </div>
  <div class="card-body">
    <div id="app">
      <timeline>
        <timeline-entry color="primary">
          <strong>Project Kickoff</strong>
          <p class="text-muted small mb-0">January 15, 2024</p>
          <small>Requirements gathering and planning began</small>
        </timeline-entry>

        <timeline-entry color="info">
          <strong>Design Phase Complete</strong>
          <p class="text-muted small mb-0">February 1, 2024</p>
          <small>UI/UX design approved by stakeholders</small>
        </timeline-entry>

        <timeline-entry color="info">
          <strong>Development Started</strong>
          <p class="text-muted small mb-0">February 15, 2024</p>
          <small>Backend and frontend development began</small>
        </timeline-entry>

        <timeline-entry color="warning">
          <strong>Testing Phase</strong>
          <p class="text-muted small mb-0">April 1, 2024</p>
          <small>Quality assurance and bug fixes in progress</small>
        </timeline-entry>

        <timeline-entry color="success">
          <strong>Production Ready</strong>
          <p class="text-muted small mb-0">April 30, 2024</p>
          <small>Project deployed to production environment</small>
        </timeline-entry>
      </timeline>
    </div>
  </div>
</div>
```

### JavaScript Entry Point

```javascript
import {createApp} from "vue";
import Timeline from "../../components/timeline";

let app = createApp({
    data() {
        return {
            events: [
                { date: '2024-01-15', title: 'Kickoff', status: 'completed', color: 'primary' },
                { date: '2024-02-01', title: 'Design Phase', status: 'completed', color: 'info' },
                { date: '2024-02-15', title: 'Development', status: 'completed', color: 'info' },
                { date: '2024-04-01', title: 'Testing', status: 'in-progress', color: 'warning' },
                { date: '2024-04-30', title: 'Launch', status: 'pending', color: 'secondary' }
            ]
        };
    }
});

Timeline.load(app);
app.mount('#app');
```

## Features

- ✅ Clean, vertical timeline layout
- ✅ Customizable color-coded entry markers
- ✅ CSS variable-based coloring (supports design system colors)
- ✅ Responsive design
- ✅ Built-in SCSS styling
- ✅ Flexible content support (text, HTML, components)
- ✅ Zero external dependencies

## Styling

### Default Styling

The Timeline component includes comprehensive default styling:

- **Vertical Line**: 2px gray line (#d4d9df) running through the center
- **Entry Markers**: 20px circular markers with 3px borders
- **Spacing**: 20px margin between entries
- **Marker Position**: 20px from the left edge
- **Z-index**: 400 for proper layering

### Custom Styling

Customize the timeline through:

1. **CSS Variables** (for marker colors):
   ```scss path=null start=null
   :root {
     --primary: #0d6efd;
     --success: #198754;
     --warning: #ffc107;
     --danger: #dc3545;
     --info: #0dcaf0;
     --secondary: #6c757d;
   }
   ```

2. **Custom Classes**:
   ```scss path=null start=null
   .timeline {
     ul {
       &:before {
         background-color: #007bff; // Change line color
       }
       
       > li {
         &:before {
           border-width: 4px; // Change marker size
         }
       }
     }
   }
   ```

3. **Inline Styles**:
   ```vue path=null start=null
   <timeline-entry color="primary">
     <div style="padding: 1rem; background-color: #f8f9fa; border-radius: 4px;">
       Custom styled entry
     </div>
   </timeline-entry>
   ```

### Common Styling Patterns

**Compact Timeline**:
```scss path=null start=null
ul.timeline {
  > li {
    margin: 10px 0;
    padding-left: 15px;
  }
  
  > li:before {
    left: 8px;
    width: 14px;
    height: 14px;
  }
  
  &:before {
    left: 14px;
  }
}
```

**Large Timeline**:
```scss path=null start=null
ul.timeline {
  > li {
    margin: 30px 0;
    padding-left: 50px;
  }
  
  > li:before {
    left: 15px;
    width: 30px;
    height: 30px;
  }
  
  &:before {
    left: 29px;
  }
}
```

## Best Practices

1. **Use Semantic Color Mapping**: Match entry colors to status/type
   ```vue path=null start=null
   ✅ GOOD
   <timeline-entry color="success">Completed</timeline-entry>
   <timeline-entry color="warning">In Progress</timeline-entry>
   <timeline-entry color="danger">Failed</timeline-entry>
   ```

2. **Include Timestamps**: Provide context for timeline entries
   ```vue path=null start=null
   <timeline-entry>
     <strong>Event Title</strong>
     <p class="text-muted small">2024-01-15 10:30 AM</p>
     Description here
   </timeline-entry>
   ```

3. **Keep Content Concise**: Don't overload entries with content
   ```vue path=null start=null
   ✅ GOOD - Brief and clear
   <timeline-entry>
     <strong>Phase Complete</strong>
     <small>All tests passed</small>
   </timeline-entry>
   
   ❌ BAD - Too much content
   <timeline-entry>
     Very long description that takes up too much space...
   </timeline-entry>
   ```

4. **Define CSS Variables**: Ensure theme colors are available
   ```css path=null start=null
   :root {
     --primary: #0d6efd;
     --success: #198754;
     --warning: #ffc107;
     --danger: #dc3545;
   }
   ```

5. **Order Chronologically**: Display events in time order (oldest to newest or newest to oldest)
   ```vue path=null start=null
   ✅ GOOD - Chronological order
   <timeline>
     <timeline-entry>2024-01-01: Started</timeline-entry>
     <timeline-entry>2024-02-01: In Progress</timeline-entry>
     <timeline-entry>2024-03-01: Complete</timeline-entry>
   </timeline>
   ```

## Common Patterns

### Timeline with Status Colors

```vue path=null start=null
<script>
export default {
  data() {
    return {
      events: [
        { title: 'Created', date: '2024-01-01', status: 'complete', color: 'success' },
        { title: 'In Review', date: '2024-01-15', status: 'in-progress', color: 'warning' },
        { title: 'Approved', date: '2024-02-01', status: 'pending', color: 'secondary' }
      ]
    };
  }
};
</script>

<template>
  <timeline>
    <timeline-entry v-for="event in events" :key="event.date" :color="event.color">
      <strong>{{ event.title }}</strong>
      <p class="text-muted small mb-1">{{ event.date }}</p>
      <span :class="['badge', 'badge-' + event.status]">{{ event.status }}</span>
    </timeline-entry>
  </timeline>
</template>
```

### Timeline with User Actions

```vue path=null start=null
<timeline>
  <timeline-entry color="primary">
    <div class="d-flex align-items-center">
      <img src="/avatar.jpg" alt="User" class="rounded-circle mr-2" style="width: 32px; height: 32px;">
      <div>
        <strong>John Doe</strong>
        <p class="text-muted small mb-0">Created the project</p>
      </div>
    </div>
  </timeline-entry>

  <timeline-entry color="info">
    <div class="d-flex align-items-center">
      <img src="/avatar2.jpg" alt="User" class="rounded-circle mr-2" style="width: 32px; height: 32px;">
      <div>
        <strong>Jane Smith</strong>
        <p class="text-muted small mb-0">Added design files</p>
      </div>
    </div>
  </timeline-entry>
</timeline>
```

### Timeline with Icons

```vue path=null start=null
<timeline>
  <timeline-entry color="success">
    <div class="d-flex">
      <i class="fas fa-check-circle text-success mr-3" style="font-size: 1.5rem;"></i>
      <div>
        <strong>Setup Complete</strong>
        <p class="text-muted small">Environment configured</p>
      </div>
    </div>
  </timeline-entry>

  <timeline-entry color="info">
    <div class="d-flex">
      <i class="fas fa-cog text-info mr-3" style="font-size: 1.5rem;"></i>
      <div>
        <strong>Configuration</strong>
        <p class="text-muted small">Settings applied</p>
      </div>
    </div>
  </timeline-entry>

  <timeline-entry color="warning">
    <div class="d-flex">
      <i class="fas fa-exclamation-triangle text-warning mr-3" style="font-size: 1.5rem;"></i>
      <div>
        <strong>Pending Review</strong>
        <p class="text-muted small">Awaiting approval</p>
      </div>
    </div>
  </timeline-entry>
</timeline>
```

### Timeline with Expandable Details

```vue path=null start=null
<script>
export default {
  data() {
    return {
      expandedEntry: null
    };
  }
};
</script>

<template>
  <timeline>
    <timeline-entry 
      v-for="(entry, index) in entries" 
      :key="index" 
      :color="entry.color"
      @click="expandedEntry = expandedEntry === index ? null : index"
      style="cursor: pointer;"
    >
      <strong>{{ entry.title }}</strong>
      <small class="text-muted">{{ entry.date }}</small>
      
      <div v-if="expandedEntry === index" class="mt-2 p-3 bg-light rounded">
        <p class="mb-0">{{ entry.details }}</p>
      </div>
    </timeline-entry>
  </timeline>
</template>
```

### Horizontal Timeline (Advanced)

```vue path=null start=null
<div style="display: flex; justify-content: space-between; position: relative;">
  <!-- Horizontal line behind items -->
  <div style="position: absolute; top: 20px; left: 0; right: 0; height: 2px; background: #d4d9df; z-index: 0;"></div>
  
  <!-- Timeline entries displayed horizontally -->
  <div v-for="event in events" :key="event.id" style="flex: 1; position: relative; z-index: 1;">
    <div style="width: 40px; height: 40px; background: white; border: 3px solid var(--primary); border-radius: 50%; margin: 0 auto;"></div>
    <p class="text-center mt-2 small">{{ event.title }}</p>
  </div>
</div>
```

## Troubleshooting

### Timeline Not Displaying

- Verify `Timeline` and `TimelineEntry` are properly registered via the bundle's `load()` function
- Check that component names match: `timeline` and `timeline-entry` (case-insensitive)
- Ensure entries are properly nested within the Timeline component

### Marker Colors Not Showing

- Verify CSS variables are defined in your stylesheet (e.g., `--primary`, `--success`)
- Check that the `color` prop value matches a defined CSS variable
- Inspect browser DevTools to see if `v-bind()` is working correctly
- Ensure CSS variable values are valid colors

### Vertical Line Not Visible

- Check that the timeline container has sufficient height
- Verify background color is not the same as the line color (#d4d9df)
- Inspect the `::before` pseudo-element in DevTools
- Ensure z-index is not being overridden

### Spacing Issues

- Adjust `padding-left` and `margin` values in custom CSS
- Ensure marker position (`left` property) aligns with line position (`left` in `::before`)
- Use custom SCSS to modify spacing for compact or large timelines

## Browser Compatibility

| Browser | Support |
|---------|---------|
| Chrome | ✅ Full support |
| Firefox | ✅ Full support |
| Safari | ✅ Full support |
| Edge | ✅ Full support |
| IE 11 | ⚠️ Requires Vue 3 polyfills and CSS variable polyfills |

## Dependencies

- Vue 3
- CSS Variables support (for color customization)
- SCSS (included, styles compiled to CSS)

## CSS Variables Reference

For the Timeline component to work properly with color customization, your application should define these CSS variables:

```css
:root {
  --primary: #0d6efd;
  --success: #198754;
  --danger: #dc3545;
  --warning: #ffc107;
  --info: #0dcaf0;
  --secondary: #6c757d;
  --light: #f8f9fa;
  --dark: #212529;
}
```

## See Also

- [Card](/components/card) - For grouping timeline content
- [Tab](/components/tab) - For multiple timeline views
- [Loader](/components/loader) - For loading states while fetching timeline data
- [Chart](/components/chart) - For visualizing timeline analytics
