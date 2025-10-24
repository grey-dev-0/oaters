# Alert Component

## Overview

A dismissible notification component with fade animations, useful for displaying success messages, errors, or warnings.

**Location**: `resources/components/alert.vue`

**Type**: Simple Component

## Props

| Prop | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `id` | String | Yes | - | Unique identifier for the alert element |
| `color` | String | Yes | - | Bootstrap color variant (e.g., `success`, `danger`, `warning`, `info`, `primary`) |
| `content` | String | No | - | HTML content to display (used when no slot provided) |

## Data

| Property | Type | Description |
|----------|------|-------------|
| `shown` | Boolean | Controls visibility state of the alert |

## Methods

| Method | Description |
|--------|-------------|
| `show()` | Display the alert with fade-in animation |
| `hide()` | Hide the alert with fade-out animation |

## Slots

| Slot | Description |
|------|-------------|
| Default | Alert content (overrides `content` prop) |

## Usage

### Basic Usage (with content prop)

```vue path=null start=null
<alert 
    id="toast" 
    ref="toast" 
    :color="toast.color" 
    :content="toast.content"
></alert>
```

### With Slot Content

```vue path=null start=null
<alert id="success-alert" color="success">
    <strong>Success!</strong> Your changes have been saved.
</alert>
```

### Programmatic Control

```javascript path=null start=null
// In your component methods
methods: {
    showSuccessMessage() {
        this.toast.color = 'success';
        this.toast.content = 'Operation completed successfully!';
        this.$refs.toast.show();
    },
    showErrorMessage(message) {
        this.toast.color = 'danger';
        this.toast.content = `<strong>Error:</strong> ${message}`;
        this.$refs.toast.show();
    }
}
```

### Real Example from OATERS

```blade path=/opt/public_html/theultragrey/oaters/Modules/Ruby/resources/views/contacts.blade.php start=17
<alert id="toast" ref="toast" :color="toast.color" :content="toast.content"></alert>
```

## Component Registration

```javascript path=null start=null
import {createApp} from "vue";
import Alert from "@/components/alert.vue";

let app = createApp({
    data() {
        return {
            toast: {
                color: 'primary',
                content: null
            }
        }
    }
});

app.component('Alert', Alert);
app.mount('#app');
```

## Behavior

- Auto-shows when using the default slot
- Dismissible via close button (×)
- Fade-in animation on show (25ms delay)
- Fade-out animation on hide (250ms duration)
- Positioned with `mb-2` margin-bottom class

## Styling

The component includes custom positioning for the close button:

```scss path=null start=null
.alert {
    .close {
        position: absolute;
        top: 0;
        right: 0;
        z-index: 2;
        padding: .75rem 1.25rem;
        color: inherit;
    }
}
```

## Available Colors

Bootstrap alert colors supported:
- `primary` - Blue
- `secondary` - Gray
- `success` - Green
- `danger` - Red
- `warning` - Yellow/Orange
- `info` - Light Blue
- `light` - Light Gray
- `dark` - Dark Gray

## Best Practices

1. **Use refs for programmatic control**: Always add a `ref` to trigger show/hide
   ```vue path=null start=null
   <alert id="toast" ref="toast" :color="color" :content="message"></alert>
   ```

2. **Store alert state in data**: Manage color and content reactively
   ```javascript path=null start=null
   data() {
       return {
           toast: { color: 'primary', content: null }
       }
   }
   ```

3. **Unique IDs**: Ensure each alert has a unique ID if multiple alerts exist on the page

4. **HTML Content**: The `content` prop accepts HTML via `v-html`, so sanitize user input

## Common Patterns

### Toast Notifications

```javascript path=null start=null
export default {
    data() {
        return {
            toast: { color: 'primary', content: null }
        }
    },
    methods: {
        notify(message, type = 'success') {
            this.toast.color = type;
            this.toast.content = message;
            this.$refs.toast.show();
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                this.$refs.toast.hide();
            }, 5000);
        }
    }
}
```

### Form Validation Feedback

```javascript path=null start=null
methods: {
    async submitForm() {
        try {
            await this.saveData();
            this.notify('Form submitted successfully!', 'success');
        } catch (error) {
            this.notify(`Error: ${error.message}`, 'danger');
        }
    }
}
```

## Troubleshooting

### Alert not showing
- Ensure you call `$refs.alertName.show()` method
- Check that `id` prop is unique on the page
- Verify Bootstrap CSS is loaded for fade animations

### Close button not working
- Ensure Bootstrap JavaScript is loaded
- Check that no CSS is overriding the button position
- Verify jQuery is available (used by Bootstrap)

### Content not updating
- Make sure to use reactive data properties
- Use `this.$refs.alert.show()` after updating content
- Check if `v-html` directive is properly rendering HTML
