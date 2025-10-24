# Modal Component

## Overview

A flexible modal dialog component wrapping Bootstrap 4 modals with Vue 3 integration, supporting various sizes, configurations, and callbacks.

**Location**: `resources/components/modal.vue`

**Type**: Simple Component

## Props

| Prop | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `id` | String | Yes | - | Unique identifier for the modal element |
| `size` | String | No | - | Modal size: `sm`, `lg`, `xl`, `xxl` (empty = default) |
| `static` | Boolean | No | `false` | Prevent closing by clicking backdrop or Esc |
| `centered` | Boolean | No | `false` | Center modal vertically on screen |
| `color` | String | No | `'grey-10'` | Bootstrap color class for header |
| `titleTag` | String | No | `'h3'` | HTML tag for title (h1-h6) |
| `noPadding` | Boolean | No | `false` | Remove padding from modal body |
| `scrollable` | Boolean | No | `false` | Enable scrollable modal body |
| `onClose` | Function | No | - | Callback function when modal closes |
| `zIndex` | Number | No | - | Z-index stacking for nested modals |

## Methods

| Method | Parameters | Description |
|--------|-----------|-------------|
| `show(reset)` | `reset`: Function | Show modal and optionally call reset function |
| `hide()` | - | Hide/close the modal |

## Slots

| Slot | Description |
|------|-------------|
| `header` | Modal header content (title) |
| Default | Modal body content |
| `footer` | Modal footer content (buttons, actions) |

## Usage

### Basic Modal

```vue path=null start=null
<modal id="basic-modal" color="primary">
    <template #header>Dialog Title</template>
    
    <p>Modal content goes here...</p>
    
    <template #footer>
        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button class="btn btn-primary">Save</button>
    </template>
</modal>
```

### Large Modal

```vue path=null start=null
<modal id="large-modal" size="lg" color="blue-3">
    <template #header>Large Dialog</template>
    
    <p>Large modal content with more space...</p>
</modal>
```

### Scrollable Modal with Lots of Content

```vue path=null start=null
<modal id="scroll-modal" scrollable color="green-3">
    <template #header>Scrollable Content</template>
    
    <div>
        <p v-for="i in 100" :key="i">Item {{ i }}</p>
    </div>
</modal>
```

### Static Modal (Cannot be dismissed easily)

```vue path=null start=null
<modal id="static-modal" static color="warning" centered>
    <template #header>Important Action</template>
    
    <p>Please confirm this action.</p>
    
    <template #footer>
        <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button class="btn btn-danger" @click="confirmAction">Confirm</button>
    </template>
</modal>
```

### Programmatic Control

```vue path=null start=null
<modal 
    id="form-modal" 
    ref="formModal" 
    color="primary"
    @close="handleModalClose"
>
    <template #header>Edit Form</template>
    
    <form>
        <div class="form-group">
            <label>Name</label>
            <input v-model="formData.name" class="form-control">
        </div>
    </form>
    
    <template #footer>
        <button class="btn btn-secondary" @click="$refs.formModal.hide()">Cancel</button>
        <button class="btn btn-primary" @click="submitForm">Submit</button>
    </template>
</modal>
```

```javascript path=null start=null
export default {
    data() {
        return {
            formData: { name: '' }
        }
    },
    methods: {
        openModal() {
            this.$refs.formModal.show(() => {
                // Reset function called before showing
                this.formData = { name: '' };
            });
        },
        submitForm() {
            // Submit logic here
            this.$refs.formModal.hide();
        },
        handleModalClose() {
            // Called when modal is closed
            console.log('Modal closed');
        }
    }
}
```

### Real Examples from OATERS

```blade path=/opt/public_html/theultragrey/oaters/Modules/Ruby/resources/views/components/modals/profile.blade.php start=1
<modal id="profile-modal" ref="profileModal" color="primary" size="lg" :on-close="closeContact" scrollable>
    <template #header>Profile - @{{openContact.name}}</template>
    <v-loader v-if="!openContact.addresses"></v-loader>
    <!-- profile content -->
</modal>
```

```blade path=/opt/public_html/theultragrey/oaters/Modules/Sapphire/resources/views/components/admin/modals/subscription-form.blade.php start=1
<modal id="{{$id}}" ref="{{$ref}}" static size="lg" color="{{$color}}">
    <template #header>{{$title}}@if($edit?? false) - @{{ openSubscription.name }}@endif</template>
    <!-- form content -->
</modal>
```

## Component Registration

```javascript path=null start=null
import {createApp} from "vue";
import Modal from "@/components/modal.vue";

let app = createApp({});

app.component('Modal', Modal);
app.mount('#app');
```

## Modal Sizes

| Size | Class | Use Case |
|------|-------|----------|
| (empty) | `modal-dialog` | Default size (~500px) |
| `sm` | `modal-sm` | Small (~300px) |
| `lg` | `modal-lg` | Large (~800px) |
| `xl` | `modal-xl` | Extra large (~1140px) |
| `xxl` | `modal-xxl` | Custom extra large (85% width) |

### Examples

```vue path=null start=null
<!-- Small modal for quick confirmations -->
<modal id="confirm-modal" size="sm" color="warning">
    <template #header>Confirm Action</template>
    <p>Are you sure?</p>
</modal>

<!-- Large modal for forms -->
<modal id="form-modal" size="lg" color="primary">
    <template #header>Edit Details</template>
    <!-- extensive form -->
</modal>

<!-- Extra large modal for complex layouts -->
<modal id="complex-modal" size="xl" color="info">
    <template #header>Complex Dashboard</template>
    <!-- dashboard content -->
</modal>
```

## Features

- ✅ Multiple size options
- ✅ Static/dismissible modes
- ✅ Centered positioning option
- ✅ Scrollable body for long content
- ✅ Custom header colors with auto-white text
- ✅ Flexible header, body, and footer slots
- ✅ Callback on close
- ✅ Z-index stacking for nested modals
- ✅ Bootstrap 4 integration

## Styling

### Component Styles

```css path=null start=null
.modal .close {
    cursor: pointer;
}

.modal-xxl {
    max-width: 85%;
}
```

### Header Color

The header automatically applies white text for dark colors (1-5):

```vue path=null start=null
<!-- Dark header with white text -->
<modal id="dark-modal" color="blue-2">...</modal>

<!-- Light header with default text -->
<modal id="light-modal" color="blue-8">...</modal>
```

## Best Practices

1. **Always provide an ID**: Required for Bootstrap modal functionality
   ```vue path=null start=null
   <modal id="unique-modal-id">...</modal>
   ```

2. **Use refs for programmatic control**: Reference the component to call show/hide
   ```vue path=null start=null
   <modal ref="myModal" id="my-modal">...</modal>
   ```
   ```javascript
   this.$refs.myModal.show();
   this.$refs.myModal.hide();
   ```

3. **Use static for important dialogs**: Prevent accidental dismissal
   ```vue path=null start=null
   <modal static id="confirmation">
       <!-- Important confirmation modal -->
   </modal>
   ```

4. **Center important dialogs**: Use for alerts and confirmations
   ```vue path=null start=null
   <modal centered id="alert-modal">
       <!-- Alert or confirmation -->
   </modal>
   ```

5. **Make scrollable when needed**: For modals with lots of content
   ```vue path=null start=null
   <modal scrollable id="long-content">
       <!-- Long form or list -->
   </modal>
   ```

6. **Use appropriate colors**: Match modal purpose to color
   ```vue path=null start=null
   <!-- Success action -->
   <modal color="green-3">...</modal>
   
   <!-- Warning/confirmation -->
   <modal color="warning">...</modal>
   
   <!-- Error/danger -->
   <modal color="danger">...</modal>
   ```

## Common Patterns

### Confirmation Dialog

```vue path=null start=null
<modal 
    id="confirm-delete" 
    ref="deleteModal"
    size="sm"
    static
    centered
    color="danger"
>
    <template #header>Confirm Deletion</template>
    
    <p>Are you sure you want to delete this item? This action cannot be undone.</p>
    
    <template #footer>
        <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button class="btn btn-danger" @click="deleteItem">Delete</button>
    </template>
</modal>
```

```javascript path=null start=null
methods: {
    openDeleteDialog(itemId) {
        this.selectedItemId = itemId;
        this.$refs.deleteModal.show();
    },
    
    deleteItem() {
        // Delete API call
        this.$refs.deleteModal.hide();
    }
}
```

### Form Modal

```vue path=null start=null
<modal 
    ref="editModal"
    id="edit-form-modal" 
    size="lg"
    color="primary"
>
    <template #header>Edit {{ editingItem?.name }}</template>
    
    <form @submit.prevent="saveItem">
        <div class="form-group">
            <label>Name</label>
            <input v-model="editingItem.name" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>Description</label>
            <textarea v-model="editingItem.description" class="form-control" rows="4"></textarea>
        </div>
    </form>
    
    <template #footer>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" @click="saveItem">Save Changes</button>
    </template>
</modal>
```

```javascript path=null start=null
data() {
    return {
        editingItem: null
    }
},
methods: {
    openEditModal(item) {
        this.editingItem = { ...item }; // Clone item
        this.$refs.editModal.show(() => {
            // Reset on open
            this.editingItem = { ...item };
        });
    },
    
    saveItem() {
        // Save API call
        this.$refs.editModal.hide();
    }
}
```

### Loading Modal

```vue path=null start=null
<modal 
    id="loading-modal"
    static
    centered
    :no-padding="true"
>
    <div class="text-center p-5">
        <i class="fas fa-spinner fa-spin fa-3x mb-3"></i>
        <p>Processing your request...</p>
    </div>
</modal>
```

### Nested Modals

```vue path=null start=null
<!-- Parent modal -->
<modal id="parent-modal" :z-index="0">
    <template #header>Parent Modal</template>
    
    <button @click="$refs.childModal.show()">Open Child Modal</button>
</modal>

<!-- Child modal -->
<modal 
    ref="childModal"
    id="child-modal" 
    :z-index="1"
>
    <template #header>Child Modal</template>
    
    <p>Child modal content...</p>
</modal>
```

### Alert Modal

```vue path=null start=null
<modal 
    id="alert-modal"
    size="sm"
    centered
    :no-padding="true"
    color="info"
>
    <template #header>Alert</template>
    
    <div class="p-3">
        <p>{{ alertMessage }}</p>
    </div>
    
    <template #footer>
        <button class="btn btn-primary" data-dismiss="modal">OK</button>
    </template>
</modal>
```

```javascript path=null start=null
methods: {
    showAlert(message) {
        this.alertMessage = message;
        bootstrap.Modal.getOrCreateInstance(
            document.getElementById('alert-modal')
        ).show();
    }
}
```

### Multi-step Modal

```vue path=null start=null
<modal ref="wizardModal" id="wizard-modal" size="lg" color="primary">
    <template #header>
        {{ ['Step 1', 'Step 2', 'Step 3'][currentStep] }}
    </template>
    
    <!-- Step 1 -->
    <div v-if="currentStep === 0">
        <p>Step 1 content...</p>
    </div>
    
    <!-- Step 2 -->
    <div v-if="currentStep === 1">
        <p>Step 2 content...</p>
    </div>
    
    <!-- Step 3 -->
    <div v-if="currentStep === 2">
        <p>Step 3 content...</p>
    </div>
    
    <template #footer>
        <button 
            v-if="currentStep > 0"
            class="btn btn-secondary" 
            @click="previousStep"
        >
            Previous
        </button>
        
        <button 
            v-if="currentStep < 2"
            class="btn btn-primary" 
            @click="nextStep"
        >
            Next
        </button>
        
        <button 
            v-if="currentStep === 2"
            class="btn btn-success" 
            @click="finishWizard"
        >
            Finish
        </button>
    </template>
</modal>
```

```javascript path=null start=null
data() {
    return {
        currentStep: 0
    }
},
methods: {
    nextStep() {
        if (this.currentStep < 2) this.currentStep++;
    },
    previousStep() {
        if (this.currentStep > 0) this.currentStep--;
    },
    finishWizard() {
        // Complete wizard action
        this.$refs.wizardModal.hide();
    }
}
```

## Troubleshooting

### Modal not showing
- Verify ID is unique on the page
- Ensure `show()` method is called
- Check that Bootstrap JavaScript is loaded
- Verify modal element is in DOM

### Modal not closing
- Check if modal is `static` - these can't be dismissed by Esc/backdrop
- Ensure `hide()` method is called or button has `data-dismiss="modal"`
- Verify Bootstrap JavaScript events are firing

### Close callback not firing
- Verify `onClose` prop is passed as a function
- Check that modal is fully closed (use `hidden.bs.modal` event)
- Ensure callback is defined in component methods

### Z-index issues with nested modals
- Use `zIndex` prop to stack modals correctly
- Parent modal: `:z-index="0"`
- Child modal: `:z-index="1"`
- Increment for deeper nesting

### Modal appears behind backdrop
- Check z-index stacking in CSS
- Verify `zIndex` prop is set correctly
- Check parent element positioning

### Scrollable content not working
- Set `scrollable` prop to `true`
- Ensure content exceeds modal height
- Check parent container isn't constraining height

### Header color not applying
- Verify color class exists (e.g., `bg-blue-3`)
- Check Bootstrap 4 CSS is loaded
- Ensure no custom CSS is overriding colors

### Modal body padding issues
- Use `noPadding` prop to remove padding
- Use custom CSS to adjust padding
- Check Bootstrap modal-body styles
