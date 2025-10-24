# Avatar Component

## Overview

A circular avatar component that displays user images with automatic fallback to initials when no image is available.

**Location**: `resources/components/avatar.vue`

**Type**: Simple Component

## Props

| Prop | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `image` | String | No | - | URL to user's profile image |
| `name` | String | Yes | - | User's full name (for initials) |
| `color` | String | No | `'grey-2'` | Background color for initials display |

## Data

| Property | Type | Description |
|----------|------|-------------|
| `fontSize` | String | Computed font size for initials (responsive) |
| `showImage` | Boolean | Whether to show image or initials |

## Computed

| Property | Description |
|----------|-------------|
| `initials` | Extracts first and last name initials (e.g., "John Doe" → "J D") |

## Methods

| Method | Description |
|--------|-------------|
| `setInitsFontSize()` | Calculates responsive font size based on container width |

## Usage

### With Image

```vue path=null start=null
<avatar 
    name="John Doe" 
    image="/uploads/avatars/john-doe.jpg"
></avatar>
```

### With Custom Color

```vue path=null start=null
<avatar 
    name="Jane Smith" 
    color="blue-3"
></avatar>
```

### Initials Only

```vue path=null start=null
<avatar name="Alice Johnson"></avatar>
```

### Dynamic from Data

```vue path=null start=null
<avatar 
    v-if="openContact.id" 
    :name="openContact.name" 
    :image="openContact.image"
></avatar>
```

### Real Example from OATERS

```blade path=/opt/public_html/theultragrey/oaters/Modules/Ruby/resources/views/components/modals/profile.blade.php start=6
<avatar v-if="openContact.id" :name="openContact.name" :image="openContact.image"></avatar>
```

## Component Registration

```javascript path=null start=null
import {createApp} from "vue";
import Avatar from "@/components/avatar.vue";

let app = createApp({
    data() {
        return {
            user: {
                name: "John Doe",
                image: "/uploads/avatars/john.jpg"
            }
        }
    }
});

app.component('Avatar', Avatar);
app.mount('#app');
```

## Initials Generation

The component automatically generates initials from the full name:

| Input Name | Generated Initials |
|------------|-------------------|
| "John Doe" | "J D" |
| "Alice" | "A" |
| "Bob Smith Johnson" | "B J" |
| "Mary-Jane Watson" | "M W" |

### Logic

1. Splits name by spaces
2. Takes first character of first word (uppercase)
3. Takes first character of last word (uppercase)
4. Single names show only one initial

## Features

- ✅ Automatic initials generation from full name
- ✅ Responsive font sizing
- ✅ Graceful image error handling
- ✅ Circular design (50% border-radius)
- ✅ Aspect ratio preservation (1:1)
- ✅ Works in modals (recalculates size on modal show)
- ✅ White text on colored background

## Styling

### Component Styles

```scss path=null start=null
.avatar {
    border-radius: 50%;

    img, strong {
        top: 0;
        left: 0;
    }

    strong {
        font-size: v-bind(fontSize);
        line-height: 2;
    }

    &::after {
        content: '';
        display: block;
        padding-bottom: 100%;
    }
}
```

### Dynamic Font Size

The component uses Vue's `v-bind` for dynamic font size styling based on container width:

```scss path=null start=null
strong {
    font-size: v-bind(fontSize); // Calculated as width / 2
    line-height: 2;
}
```

### Size Control

Control avatar size via parent container:

```vue path=null start=null
<div style="width: 100px; height: 100px;">
    <avatar name="John Doe"></avatar>
</div>
```

Or with CSS classes:

```vue path=null start=null
<div class="avatar-container">
    <avatar name="John Doe"></avatar>
</div>
```

```css path=null start=null
.avatar-container {
    width: 80px;
    height: 80px;
}
```

## Available Colors

Common color options (adjust based on your theme):
- `grey-1` through `grey-10` - Gray shades
- `blue-1` through `blue-10` - Blue shades
- `green-1` through `green-10` - Green shades
- `red-1` through `red-10` - Red shades
- `purple-1` through `purple-10` - Purple shades

## Best Practices

1. **Always provide name**: Required for initials fallback
   ```vue path=null start=null
   <avatar name="John Doe" :image="userImage"></avatar>
   ```

2. **Use appropriate colors**: Match your app's color scheme
   ```vue path=null start=null
   <avatar name="Admin User" color="blue-3"></avatar>
   ```

3. **Test image URLs**: Component handles errors gracefully but validate URLs when possible
   ```javascript path=null start=null
   computed: {
       validImageUrl() {
           return this.user.image || null;
       }
   }
   ```

4. **Conditional rendering**: Only render when data is available
   ```vue path=null start=null
   <avatar v-if="user" :name="user.name" :image="user.image"></avatar>
   ```

5. **Consistent sizing**: Use wrapper divs to control size across the app
   ```vue path=null start=null
   <div class="user-avatar-sm">
       <avatar :name="user.name"></avatar>
   </div>
   ```

## Common Patterns

### User List

```vue path=null start=null
<div class="user-list">
    <div v-for="user in users" :key="user.id" class="user-item">
        <div class="user-avatar-wrapper">
            <avatar 
                :name="user.name" 
                :image="user.avatar"
                :color="user.color"
            ></avatar>
        </div>
        <span class="user-name">{{ user.name }}</span>
    </div>
</div>
```

### Profile Header

```vue path=null start=null
<div class="profile-header">
    <div class="avatar-large">
        <avatar 
            :name="profile.name" 
            :image="profile.image"
            color="blue-3"
        ></avatar>
    </div>
    <h2>{{ profile.name }}</h2>
    <p>{{ profile.role }}</p>
</div>
```

```css path=null start=null
.avatar-large {
    width: 150px;
    height: 150px;
    margin: 0 auto;
}
```

### Comment Thread

```vue path=null start=null
<div class="comment">
    <div class="comment-avatar">
        <avatar 
            :name="comment.author" 
            :image="comment.authorImage"
            color="grey-3"
        ></avatar>
    </div>
    <div class="comment-content">
        <strong>{{ comment.author }}</strong>
        <p>{{ comment.text }}</p>
    </div>
</div>
```

```css path=null start=null
.comment-avatar {
    width: 40px;
    height: 40px;
    float: left;
    margin-right: 10px;
}
```

### Hover States

```vue path=null start=null
<div class="avatar-clickable" @click="viewProfile(user.id)">
    <avatar :name="user.name" :image="user.image"></avatar>
</div>
```

```css path=null start=null
.avatar-clickable {
    cursor: pointer;
    transition: transform 0.2s;
}

.avatar-clickable:hover {
    transform: scale(1.1);
}
```

## Modal Integration

The component automatically recalculates font size when used in Bootstrap modals:

```javascript path=/opt/public_html/theultragrey/oaters/resources/components/avatar.vue start=50
$('body').on('shown.bs.modal', '.modal', () => {
    this.setInitsFontSize();
});
```

This ensures proper sizing even when the avatar is initially hidden.

## Image Error Handling

When an image fails to load:

1. The `onerror` attribute sets `showImage = false`
2. Component automatically switches to initials display
3. No additional code needed from developers

```vue path=/opt/public_html/theultragrey/oaters/resources/components/avatar.vue start=3
<img v-if="showImage" class="w-100 h-100 d-block position-absolute" :src="image" alt="image" onerror="showImage = false">
<strong v-else ref="initsElement" class="initials text-white w-100 h-100 text-center d-block position-absolute">{{initials}}</strong>
```

## Troubleshooting

### Avatar not displaying correctly
- Check image URL is accessible (use browser DevTools)
- Ensure container has defined dimensions
- Verify color class exists in your CSS framework

### Initials not showing
- Verify `name` prop is provided and not empty
- Check that `showImage` is false when image fails
- Ensure white text color is not blending with background

### Font size too small/large
- Check that container has proper dimensions
- Verify `setInitsFontSize()` is being called
- Test in different viewports

### Aspect ratio issues
- Ensure parent container doesn't have conflicting styles
- Check that `padding-bottom: 100%` trick is working
- Verify no CSS is overriding the component styles

### Modal sizing issues
- Confirm Bootstrap modal events are firing
- Check that jQuery is loaded
- Verify modal shown event listener is attached
