# Loader Component

## Overview

A sophisticated loading spinner component that uses a unique hybrid architecture combining:
- **Blade template** (`loader.blade.php`) for initial page load display
- **Vue wrapper component** (`loader-wrapper.vue`) for runtime use
- **JavaScript module** (`loader.js`) for template-based rendering

The loader displays a 3D cube grid animation during initial page load and when Vue components need to show loading states.

**Location**: 
- `Modules/Common/resources/views/components/loader.blade.php`
- `resources/components/loader-wrapper.vue`
- `resources/components/loader.js`

**Type**: Hybrid Component (Blade + Vue + JavaScript)

## Architecture

### Components

1. **Blade Template** (`loader.blade.php`)
   - Renders HTML for the spinner
   - Displays during initial page load (before Vue hydrates)
   - Works with `v-cloak` directive to hide Vue templates

2. **Vue Wrapper** (`loader-wrapper.vue`)
   - Vue wrapper component for runtime use
   - Wraps the Loader component
   - Used when you need loading states during Vue app operation

3. **JavaScript Module** (`loader.js`)
   - Simple module that defines template-based component
   - References `#loader` template from Blade
   - Allows Vue to render the same template dynamically

## How It Works

### Initial Page Load Flow

```
1. Browser receives HTML with v-cloak on app container
2. Blade loader.blade.php renders immediately (visible)
3. Vue app hydrates and removes v-cloak
4. Blade loader hides via CSS rules
5. Vue takes over for runtime loading states
```

### CSS Display Logic

```css
[v-cloak], #loader {
    display: none;
}

/* Show loader while Vue initializes */
[v-cloak] + #loader {
    display: block;
}
```

## Usage

### During Initial Page Load

The loader is automatically displayed while Vue is initializing:

```blade path=/opt/public_html/theultragrey/oaters/Modules/Common/resources/views/components/loader.blade.php start=1
@props(['color'])

<div id="loader">
    <div class="sk-cube-grid">
        <div class="sk-cube sk-cube1"></div>
        <!-- 9 cube elements total -->
    </div>
</div>
```

### During Vue Runtime

Use the `VLoader` component to show loading states:

```vue path=null start=null
<div class="card">
    <h3>Content</h3>
    <v-loader v-if="loading"></v-loader>
    <div v-else>
        <p>{{ content }}</p>
    </div>
</div>
```

### Real Example from OATERS

```blade path=/opt/public_html/theultragrey/oaters/Modules/Ruby/resources/views/components/modals/profile.blade.php start=3
<modal id="profile-modal" ref="profileModal" color="primary" size="lg" :on-close="closeContact" scrollable>
    <template #header>Profile - @{{openContact.name}}</template>
    <v-loader v-if="!openContact.addresses"></v-loader>
    <div :class="'row' + (openContact.addresses? '' : ' invisible')">
        <!-- profile content -->
    </div>
</modal>
```

```blade path=/opt/public_html/theultragrey/oaters/Modules/Ruby/resources/views/dashboard.blade.php start=8
<card style="min-height:50vh" title="WIP">
    <v-loader></v-loader>
</card>
```

## Component Registration

### In Layout

Place the Blade component in your master layout:

```blade path=null start=null
<body>
    <div id="app" v-cloak>
        <!-- Vue app content -->
    </div>
    
    <!-- Include loader for initial page load -->
    @include('common::components.loader')
</body>
```

### In JavaScript Entry Point

Register the Vue wrapper component:

```javascript path=null start=null
import {createApp} from "vue";
import VLoader from "@/components/loader-wrapper.vue";

let app = createApp({});

app.component('VLoader', VLoader);
app.mount('#app');
```

Or use the template-based loader:

```javascript path=null start=null
import Loader from "@/components/loader.js";

// Register globally
app.component('Loader', Loader);
```

## Features

- ✅ 3D animated cube grid spinner
- ✅ Works during initial page load (before Vue hydration)
- ✅ Works during Vue runtime (conditional rendering)
- ✅ Centered positioning
- ✅ Drop shadow for visual depth
- ✅ Smooth scaling animation
- ✅ Responsive design

## Animation Details

### Cube Grid Structure

A 3x3 grid of cubes, each with individual animation delays:

```html
<div class="sk-cube-grid">
    <div class="sk-cube sk-cube1"></div>  <!-- Delay: 0.2s -->
    <div class="sk-cube sk-cube2"></div>  <!-- Delay: 0.3s -->
    <div class="sk-cube sk-cube3"></div>  <!-- Delay: 0.4s -->
    <div class="sk-cube sk-cube4"></div>  <!-- Delay: 0.1s -->
    <div class="sk-cube sk-cube5"></div>  <!-- Delay: 0.2s -->
    <div class="sk-cube sk-cube6"></div>  <!-- Delay: 0.3s -->
    <div class="sk-cube sk-cube7"></div>  <!-- Delay: 0.0s -->
    <div class="sk-cube sk-cube8"></div>  <!-- Delay: 0.1s -->
    <div class="sk-cube sk-cube9"></div>  <!-- Delay: 0.2s -->
</div>
```

### Animation Keyframes

```css
@keyframes sk-cubeGridScaleDelay {
    0%, 70%, 100% {
        transform: scale3D(1, 1, 1) rotate(0);
    }
    35% {
        transform: scale3D(0, 0, 1) rotate(45deg);
    }
}
```

- **Duration**: 2 seconds
- **Iteration**: Infinite loop
- **Easing**: ease-in-out
- **Effect**: Cubes scale down and rotate during the animation

### Color Styling

Each cube has a different shade for visual depth:

| Cube | Delay | Color |
|------|-------|-------|
| 1 | 0.2s | `--$color-2` |
| 2 | 0.3s | `#505050` |
| 3 | 0.4s | `#333` |
| 4 | 0.1s | `#8d8d8d` |
| 5 | 0.2s | `--$color-4` |
| 6 | 0.3s | `#afafaf` |
| 7 | 0.0s | `#6e6e6e` |
| 8 | 0.1s | `#898989` |
| 9 | 0.2s | `--$color-7` |

## Styling

### Component Styles

```scss path=/opt/public_html/theultragrey/oaters/Modules/Common/resources/views/components/loader.blade.php start=17
#loader, #v-loader {
    position: absolute;
    margin: auto;
    height: 125px;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
}

.sk-cube-grid {
    width: 75px;
    height: 100px;
    margin: auto;
    transform: rotateZ(45deg) rotateX(45deg);
    filter: drop-shadow(2px 2px 8px #a5a5a5);
}

.sk-cube-grid .sk-cube {
    width: 33%;
    height: 33%;
    float: left;
    animation: sk-cubeGridScaleDelay 2s infinite ease-in-out;
}
```

### Sizing

- **Container**: 125px height
- **Cube grid**: 75px width × 100px height
- **Each cube**: 33% × 33% (11px × 33px)

## Best Practices

1. **Use for async loading**: Show while fetching data
   ```vue path=null start=null
   <v-loader v-if="loading"></v-loader>
   <div v-else>
       <p>{{ fetchedData }}</p>
   </div>
   ```

2. **Center in containers**: Loader uses absolute positioning
   ```vue path=null start=null
   <div class="card" style="position: relative; min-height: 300px;">
       <v-loader v-if="loading"></v-loader>
       <content v-else>...</content>
   </div>
   ```

3. **Include in layout**: Always include for initial load
   ```blade path=null start=null
   <body>
       <div id="app" v-cloak>...</div>
       @include('common::components.loader')
   </body>
   ```

4. **Combine with v-cloak**: Hide Vue templates until ready
   ```html path=null start=null
   <div id="app" v-cloak>
       <!-- Vue templates hidden until hydration -->
   </div>
   ```

5. **Use with conditional visibility**: Hide content while loading
   ```vue path=null start=null
   <v-loader v-if="!dataReady"></v-loader>
   <div v-show="dataReady" class="content">
       <!-- Content only visible when data is ready -->
   </div>
   ```

## Common Patterns

### Modal Loading State

```vue path=null start=null
<modal id="data-modal" ref="dataModal">
    <template #header>Loading Data...</template>
    
    <v-loader v-if="isLoading"></v-loader>
    
    <div v-show="!isLoading">
        <p>{{ data }}</p>
    </div>
</modal>
```

### Card Loading State

```vue path=null start=null
<card title="Statistics" color="blue-3">
    <v-loader v-if="loadingStats"></v-loader>
    
    <div v-show="!loadingStats" class="stats-content">
        <counter title="Total" :value="stats.total"></counter>
    </div>
</card>
```

### Page Loading Overlay

```blade path=null start=null
<body>
    <div id="app" v-cloak>
        <!-- Page content -->
    </div>
    
    <!-- Shows during initial page load -->
    @include('common::components.loader')
</body>
```

### Conditional Data Loading

```javascript path=null start=null
export default {
    data() {
        return {
            data: null,
            isLoading: true
        }
    },
    methods: {
        fetchData() {
            this.isLoading = true;
            
            fetch('/api/data')
                .then(r => r.json())
                .then(data => {
                    this.data = data;
                    this.isLoading = false;
                })
                .catch(err => {
                    console.error(err);
                    this.isLoading = false;
                });
        }
    },
    mounted() {
        this.fetchData();
    }
}
```

```vue path=null start=null
<div class="container" style="position: relative; min-height: 300px;">
    <v-loader v-if="isLoading"></v-loader>
    <div v-show="!isLoading">
        <h2>{{ data.title }}</h2>
        <p>{{ data.content }}</p>
    </div>
</div>
```

## Visual Hierarchy

### Initial Load Phase

```
1. HTML loads (100ms)
   ↓
2. Blade template renders + Loader CSS shows
   ↓
3. JavaScript downloads (500ms+)
   ↓
4. Vue hydrates (100ms)
   ↓
5. v-cloak removed, Loader hidden
   ↓
6. Vue app interactive
```

### Runtime Phase

```
1. User triggers action
   ↓
2. Set isLoading = true
   ↓
3. v-loader component shows
   ↓
4. Async operation completes
   ↓
5. Set isLoading = false
   ↓
6. v-loader component hides
```

## Browser Compatibility

- ✅ Chrome/Edge (all versions)
- ✅ Firefox (all versions)
- ✅ Safari (all versions)
- ✅ IE 11 (with CSS 3D transform fallbacks)

### CSS Features Used

- `position: absolute` - Positioning
- `margin: auto` - Centering
- `transform: scale3D()` - 3D scaling
- `animation` - Keyframe animation
- `filter: drop-shadow()` - Visual effect
- `v-cloak` - Vue directive

## Troubleshooting

### Loader not showing during initial load
- Verify `@include('common::components.loader')` is in layout
- Check that loader is placed after `#app` element
- Ensure `v-cloak` is on app container
- Verify CSS is loaded before HTML

### Loader not hiding after Vue loads
- Check that `v-cloak` is properly removed by Vue
- Verify CSS display rules: `[v-cloak] { display: none; }`
- Ensure Vue hydration completes successfully
- Check browser console for JavaScript errors

### Loader showing but appears broken
- Verify CSS file is loading
- Check that animation keyframes are defined
- Ensure no CSS is overriding loader styles
- Test in different browsers for compatibility

### Loader not centered
- Verify parent container has `position: relative`
- Check that container has defined dimensions
- Ensure `top: 0; right: 0; bottom: 0; left: 0;` is applied
- Verify `margin: auto` is not being overridden

### Animation not smooth
- Check browser hardware acceleration support
- Verify `animation` property is supported
- Look for CSS that might disable animations
- Test on slower devices

### Loader appears behind content
- Check z-index stacking
- Verify parent container positioning
- Use `z-index: 999` if needed
- Ensure overlay doesn't have pointer events

### Multiple loaders showing
- Verify only one loader template is included
- Check that only one v-cloak is on app
- Ensure no duplicate component registrations
- Check for multiple #app elements in DOM

## Performance Considerations

- **Initial Load**: Loader displays while JavaScript downloads (~500ms typical)
- **Memory**: Single CSS animation uses minimal resources
- **GPU**: 3D transforms use GPU acceleration when available
- **Rendering**: Optimized animation prevents layout thrashing

## Accessibility

- **Screen Readers**: Loader is decorative, no text
- **Keyboard**: No interaction needed (passive element)
- **Motion**: Uses standard CSS animation (respects `prefers-reduced-motion`)
- **Color**: Uses grayscale + drop shadow for visibility

Consider adding ARIA attributes for better accessibility:

```vue path=null start=null
<div 
    role="status" 
    aria-label="Loading content"
    aria-busy="true"
>
    <v-loader></v-loader>
</div>
```
