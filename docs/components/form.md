# Form Component Bundle

## Overview

The Form component bundle provides a comprehensive form building system with automatic validation, field management, and support for multiple input types. It consists of four components that work together to create flexible, maintainable form structures with built-in error handling and AJAX submission support.

**Location**: `resources/components/form/`

**Components**:
- `VueForm` - Main form container managing submission and validation
- `VueField` - Generic field wrapper supporting multiple input types
- `Checkbox` - Checkbox input component
- `Radio` - Radio button input component

## Architecture

```
┌────────────────────────────────────────────────┐
│          VueForm                               │
│  (Form container, validation, submission)      │
│  Provides: labelSize, formOrientation, emitter │
│                                                │
│  ┌──────────────────────────────────────────┐  │
│  │ VueField (name, type: 'text')            │  │
│  │  ├─ input[type="text"]                   │  │
│  │  └─ error messages                       │  │
│  └──────────────────────────────────────────┘  │
│                                                │
│  ┌──────────────────────────────────────────┐  │
│  │ VueField (name, type: 'select')          │  │
│  │  ├─ <select> (or Select2)                │  │
│  │  └─ error messages                       │  │
│  └──────────────────────────────────────────┘  │
│                                                │
│  ┌──────────────────────────────────────────┐  │
│  │ VueField (name, type: 'checkbox')        │  │
│  │  ├─ Checkbox components                  │  │
│  │  └─ Checkbox components                  │  │
│  └──────────────────────────────────────────┘  │
│                                                │
└────────────────────────────────────────────────┘
```

## Component Registration

The Form bundle is registered via an index file that exports a `load` function:

```javascript path=/opt/public_html/theultragrey/oaters/resources/components/form/index.js start=1
import {defineAsyncComponent} from "vue";

function load(app){
    app.component('VueForm', defineAsyncComponent(() => import('./form.vue')));
    app.component('VueField', defineAsyncComponent(() => import('./field.vue')));
    app.component('Checkbox', defineAsyncComponent(() => import('./checkbox.vue')));
    app.component('Radio', defineAsyncComponent(() => import('./radio.vue')));
}

export default {load}
```

**Usage in Entry Point**:

```javascript path=null start=null
// resources/js/app.js
import {createApp} from "vue";
import Form from "../../components/form";

let app = createApp({
    // ... app config
});

// Load the Form bundle
Form.load(app);

app.mount('#app');
```

## Components

### VueForm

The main form container that manages submission, validation, field state, and event communication via an event emitter.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | String | - | **Required**. Unique ID for the form element |
| `action` | String | - | Form submission endpoint URL (defaults to current location) |
| `vertical` | Boolean | `false` | If true, renders fields in vertical layout mode |
| `ajax` | Boolean | `true` | If true, uses AJAX submission; if false, traditional form submission |
| `small` | Number | `6` | Bootstrap column width for labels on small screens |
| `medium` | Number | `3` | Bootstrap column width for labels on medium screens |
| `large` | Number | `2` | Bootstrap column width for labels on large screens |

#### Data

| Property | Type | Description |
|----------|------|-------------|
| `loading` | Boolean | Whether form is currently processing submission |
| `fields` | Object | Current form field values keyed by field name |
| `validation` | Object | Current validation errors keyed by field name |
| `hasFiles` | Boolean | Whether form contains file inputs (auto-detects) |
| `emitter` | Emitter | Event emitter for form-wide communication |

#### Methods

| Method | Parameters | Description |
|--------|-----------|-------------|
| `setField(field, value)` | `field`: String, `value`: Any | Update a field value in the form |
| `reset(defaults)` | `defaults`: Object | Reset form to initial state with optional default values |
| `submit()` | - | Submit the form via AJAX or traditional means |

#### Provided Values (for child components)

- `labelSize` - Bootstrap column sizes for responsive labels
- `formOrientation` - Layout orientation (vertical/horizontal)
- `emitter` - Event emitter for child communication
- `validation` - Current validation errors
- `setField` - Function to update field values

#### Encoding

The form automatically determines the encoding type:
- `application/x-www-form-urlencoded` - For regular fields
- `multipart/form-data` - When file inputs are present

#### Events (emitted)

| Event | Payload | Description |
|-------|---------|-------------|
| `init` | `{defaults: Object}` | Initialize form with default values |
| `init:sub` | `fieldName: String` | Initialize sub-fields (checkboxes/radios) |
| `destroy` | `fieldName: String` | Clean up field resources |

#### Example Usage

```vue path=null start=null
<vue-form id="contact-form" action="/api/contacts" large="3" ajax ref="form">
  <vue-field name="name" type="text" id="name">Full Name</vue-field>
  <vue-field name="email" type="email" id="email">Email Address</vue-field>
  <vue-field name="message" type="textarea" id="message">Message</vue-field>
  
  <button @click="$refs.form.submit()" class="btn btn-primary">Submit</button>
</vue-form>
```

### VueField

Flexible field wrapper component supporting multiple input types with automatic validation display and field management.

#### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | String | - | Unique ID for the input element |
| `name` | String | - | Form field name (used for submission and validation) |
| `type` | String | `'text'` | Input type: `text`, `number`, `password`, `email`, `tel`, `daterange`, `file`, `select`, `select2`, `checkbox`, `radio`, `autocomplete` |
| `autocomplete` | Boolean | `false` | Enable/disable HTML autocomplete attribute |
| `multiple` | Boolean | `false` | Allow multiple selections (for select/select2) |
| `ranged` | Boolean | - | Enable date range picker mode (for daterange type) |
| `url` | String | - | Server endpoint for dynamic data (select2/autocomplete) |
| `limit` | Number | - | Limit results for select2/autocomplete |

#### Slots

| Slot | Description |
|------|-------------|
| Default | Field label text |
| `options` | Option elements for select/checkbox/radio fields |

#### Computed Properties

| Property | Description |
|----------|-------------|
| `labelClass` | Bootstrap classes for responsive label layout |
| `inputClass` | Bootstrap classes for responsive input container |
| `elementClass` | CSS classes for the input element (includes validation state) |
| `inputType` | Internal type identifier for the field |
| `textType` | HTML input type attribute |

#### Methods

| Method | Parameters | Description |
|--------|-----------|-------------|
| `setValue(value)` | `value`: Any | Update field value and trigger change |
| `onChange()` | - | Handle field value changes |
| `construct(defaultValue)` | `defaultValue`: Any | Initialize field with default value |
| `destroy()` | - | Clean up field resources (select2, daterange) |
| `initDatePicker(defaultValue)` | `defaultValue`: String | Initialize date range picker |
| `initSelect(defaultValue)` | `defaultValue`: Object | Initialize Select2 enhanced select |

#### Supported Field Types

| Type | HTML Element | Features |
|------|--------------|----------|
| `text` | `<input type="text">` | Standard text input |
| `number` | `<input type="number">` | Numeric input |
| `password` | `<input type="password">` | Password input (hidden) |
| `email` | `<input type="email">` | Email validation |
| `tel` | `<input type="tel">` | Telephone input |
| `file` | `<input type="file">` | File upload (sets form encoding to multipart) |
| `daterange` | `<input type="text">` | Date or date range picker |
| `select` | `<select>` | Native HTML select dropdown |
| `select2` | `<select>` | Enhanced Select2 dropdown with search |
| `checkbox` | Checkbox components | Checkbox input group |
| `radio` | Radio components | Radio button group |
| `autocomplete` | Autocomplete component | Server-side autocomplete |

#### Example Usage

**Text Input**:
```vue path=null start=null
<vue-field name="username" type="text" id="username">Username</vue-field>
```

**Email Input**:
```vue path=null start=null
<vue-field name="email" type="email" id="email">Email Address</vue-field>
```

**Select Dropdown**:
```vue path=null start=null
<vue-field name="country" type="select" id="country">Country</vue-field>
  <template #options>
    <option value="us">United States</option>
    <option value="uk">United Kingdom</option>
    <option value="ca">Canada</option>
  </template>
</vue-field>
```

**Select2 (Enhanced Select)**:
```vue path=null start=null
<vue-field name="department" type="select2" id="department" url="/api/departments" :limit="5">
  Select Department
</vue-field>
```

**Date Range Picker**:
```vue path=null start=null
<vue-field name="date_range" type="daterange" id="date-range" ranged>
  Date Range
</vue-field>
```

**File Upload**:
```vue path=null start=null
<vue-field name="document" type="file" id="document">Upload Document</vue-field>
```

**Checkbox Group**:
```vue path=null start=null
<vue-field name="interests" type="checkbox" id="interests">Interests</vue-field>
  <template #options>
    <checkbox id="interest-1" name="interests" value="sports">Sports</checkbox>
    <checkbox id="interest-2" name="interests" value="music">Music</checkbox>
    <checkbox id="interest-3" name="interests" value="art">Art</checkbox>
  </template>
</vue-field>
```

**Autocomplete**:
```vue path=null start=null
<vue-field name="contact_id" type="autocomplete" id="contact" url="/api/contacts" :limit="10">
  Select Contact
</vue-field>
```

### Checkbox

Individual checkbox input component for use within VueField checkbox groups.

#### Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| `id` | String | Yes | Unique ID for the checkbox |
| `name` | String | Yes | Field name (must match parent field name) |
| `value` | String/Boolean | No | Value to submit when checked |

#### Slots

| Slot | Description |
|------|-------------|
| Default | Checkbox label text |

#### Example Usage

```vue path=null start=null
<checkbox id="terms" name="agreement" value="true">
  I agree to the terms and conditions
</checkbox>
```

### Radio

Individual radio button component for use within VueField radio groups.

#### Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| `id` | String | Yes | Unique ID for the radio button |
| `name` | String | Yes | Field name (all radios in a group share the same name) |
| `value` | String | Yes | Value to submit when selected |

#### Slots

| Slot | Description |
|------|-------------|
| Default | Radio button label text |

#### Events

| Event | Payload | Description |
|-------|---------|-------------|
| `change` | - | Emitted when radio button is selected |

#### Example Usage

```vue path=null start=null
<radio id="option-1" name="choice" value="option1">Option 1</radio>
<radio id="option-2" name="choice" value="option2">Option 2</radio>
<radio id="option-3" name="choice" value="option3">Option 3</radio>
```

## Complete Example

Here's a complete example showing the Form component with various field types:

### Blade View

```blade path=null start=null
<div class="card">
  <div class="card-header">
    <h5 class="card-title">Contact Form</h5>
  </div>
  <div class="card-body">
    <div id="app">
      <vue-form id="contact-form" action="/api/contacts" large="3" ajax ref="contactForm">
        <vue-field name="first_name" type="text" id="first-name">
          First Name
        </vue-field>
        
        <vue-field name="last_name" type="text" id="last-name">
          Last Name
        </vue-field>
        
        <vue-field name="email" type="email" id="email">
          Email Address
        </vue-field>
        
        <vue-field name="phone" type="tel" id="phone">
          Phone Number
        </vue-field>
        
        <vue-field name="country" type="select" id="country">
          Country
          <template #options>
            <option value="">-- Select a country --</option>
            <option value="us">United States</option>
            <option value="uk">United Kingdom</option>
            <option value="ca">Canada</option>
          </template>
        </vue-field>
        
        <vue-field name="department" type="select2" id="department" url="/api/departments" :limit="5">
          Department
        </vue-field>
        
        <vue-field name="interests" type="checkbox" id="interests">
          Interests
          <template #options>
            <checkbox id="interest-sports" name="interests" value="sports">Sports</checkbox>
            <checkbox id="interest-music" name="interests" value="music">Music</checkbox>
            <checkbox id="interest-art" name="interests" value="art">Art</checkbox>
          </template>
        </vue-field>
        
        <vue-field name="experience" type="radio" id="experience">
          Experience Level
          <template #options>
            <radio id="exp-beginner" name="experience" value="beginner">Beginner</radio>
            <radio id="exp-intermediate" name="experience" value="intermediate">Intermediate</radio>
            <radio id="exp-advanced" name="experience" value="advanced">Advanced</radio>
          </template>
        </vue-field>
      </vue-form>
      
      <div class="mt-3">
        <button @click="$refs.contactForm.submit()" class="btn btn-primary">
          Submit
        </button>
        <button @click="$refs.contactForm.reset()" class="btn btn-secondary ml-2">
          Reset
        </button>
      </div>
    </div>
  </div>
</div>
```

### JavaScript Entry Point

```javascript path=null start=null
import {createApp} from "vue";
import Form from "../../components/form";

let app = createApp({
    methods: {
        handleFormSuccess(response) {
            console.log('Form submitted successfully', response);
            alert('Your contact form has been submitted!');
        }
    }
});

Form.load(app);
app.mount('#app');
```

## Features

- ✅ Multiple input types (text, email, select, checkbox, radio, file, daterange, autocomplete)
- ✅ Built-in AJAX form submission
- ✅ Automatic validation error display
- ✅ Responsive label sizing (small, medium, large)
- ✅ Select2 integration for enhanced selects
- ✅ Date range picker support
- ✅ File upload with automatic multipart encoding
- ✅ Server-side autocomplete
- ✅ Event-based field communication via emitter
- ✅ Field reset functionality with defaults
- ✅ Bootstrap form styling

## Styling

### Default Styling

The Form component uses Bootstrap form classes:

- `.form-group` - Field container
- `.form-control` - Input element
- `.form-check` - Checkbox/radio container
- `.form-check-input` - Checkbox/radio input
- `.form-check-label` - Checkbox/radio label
- `.text-danger` - Error message color

### Bootstrap Responsive Column Grid

The form automatically uses Bootstrap's 12-column grid:

```vue path=null start=null
<!-- Label/Input split varies by screen size -->
<!-- Small: 6 cols label, 6 cols input -->
<!-- Medium: 3 cols label, 9 cols input -->
<!-- Large: 2 cols label, 10 cols input -->
<vue-form id="form" small="6" medium="3" large="2">
```

### Custom Styling

Customize through Bootstrap utility classes or custom CSS:

```vue path=null start=null
<vue-field name="email" type="email" id="email" class="mb-4">
  Email Address
</vue-field>
```

## Best Practices

1. **Always Set Unique IDs**: Required for form element targeting
   ```vue path=null start=null
   ✅ GOOD
   <vue-form id="my-form">
   ```

2. **Use Semantic Field Names**: Match backend expected names
   ```vue path=null start=null
   ✅ GOOD
   <vue-field name="first_name" id="first-name">First Name</vue-field>
   
   ❌ BAD
   <vue-field name="fname" id="first-name">First Name</vue-field>
   ```

3. **Provide Clear Labels**: Use field labels in the default slot
   ```vue path=null start=null
   ✅ GOOD
   <vue-field name="email" id="email">Email Address</vue-field>
   ```

4. **Handle Validation Errors**: Check response status in submission handler
   ```javascript path=null start=null
   this.$refs.form.submit().done(() => {
       // Success - validation passed
   }).fail((xhr) => {
       if (xhr.status === 422) {
           // Validation errors - form displays them
       }
   });
   ```

5. **Set Appropriate Field Types**: Use correct input type for better UX
   ```vue path=null start=null
   ✅ GOOD
   <vue-field name="email" type="email" id="email">Email</vue-field>
   <vue-field name="phone" type="tel" id="phone">Phone</vue-field>
   <vue-field name="quantity" type="number" id="quantity">Quantity</vue-field>
   ```

## Common Patterns

### Contact Form with Validation

```vue path=null start=null
<vue-form id="contact-form" action="/api/contact" ajax ref="form">
  <vue-field name="name" type="text" id="name">Full Name</vue-field>
  <vue-field name="email" type="email" id="email">Email Address</vue-field>
  <vue-field name="subject" type="text" id="subject">Subject</vue-field>
  <vue-field name="message" type="textarea" id="message">Message</vue-field>
</vue-form>

<button @click="submitForm" class="btn btn-primary">Send Message</button>

<script>
export default {
  methods: {
    submitForm() {
      this.$refs.form.submit()
        .done(() => alert('Message sent!'))
        .fail(xhr => {
          if (xhr.status === 422) {
            console.log('Validation errors:', this.$refs.form.validation);
          }
        });
    }
  }
};
</script>
```

### Form with Dynamic Select Options

```vue path=null start=null
<vue-form id="product-form" action="/api/products" ajax ref="form">
  <vue-field name="category" type="select" id="category">
    Category
    <template #options>
      <option v-for="cat in categories" :key="cat.id" :value="cat.id">
        {{ cat.name }}
      </option>
    </template>
  </vue-field>
</vue-form>

<script>
export default {
  data() {
    return {
      categories: [
        { id: 1, name: 'Electronics' },
        { id: 2, name: 'Clothing' },
        { id: 3, name: 'Books' }
      ]
    };
  }
};
</script>
```

### User Preference Form with Checkboxes

```vue path=null start=null
<vue-form id="preferences-form" action="/api/preferences" ajax ref="form">
  <vue-field name="notifications" type="checkbox" id="notifications">
    Notification Preferences
    <template #options>
      <checkbox id="email-notify" name="notifications" value="email">
        Email Notifications
      </checkbox>
      <checkbox id="sms-notify" name="notifications" value="sms">
        SMS Notifications
      </checkbox>
      <checkbox id="push-notify" name="notifications" value="push">
        Push Notifications
      </checkbox>
    </template>
  </vue-field>
</vue-form>
```

### Multi-Step Form in Modal

```blade path=/opt/public_html/theultragrey/oaters/Modules/Ruby/resources/views/components/modals/contact-form.blade.php start=1
<modal id="contact-modal" ref="contactModal" size="lg">
    <template #header>Contact Form</template>
    <vue-form id="contact-form" ref="contactForm" large="3" ajax action="/api/contacts">
        <tabs active-tab="#basic">
            <template #navigation>
                <tab target="#basic">Basic Info</tab>
                <tab target="#contact">Contact Details</tab>
            </template>
            
            <pane id="basic">
                <vue-field name="name" type="text" id="name">Name</vue-field>
            </pane>
            
            <pane id="contact">
                <vue-field name="email" type="email" id="email">Email</vue-field>
                <vue-field name="phone" type="tel" id="phone">Phone</vue-field>
            </pane>
        </tabs>
    </vue-form>
    <template #footer>
        <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" @click="submitForm">Save</button>
    </template>
</modal>
```

## Validation

### Server-Side Validation

The form supports Laravel-style validation error responses (422 status code):

```json
{
    "errors": {
        "email": ["The email field is required"],
        "phone": ["The phone must be a valid phone number"],
        "address.city": ["The city is required"]
    }
}
```

### Error Display

Errors are automatically displayed below fields with the `.text-danger` class. Access validation state:

```javascript path=null start=null
console.log(this.$refs.form.validation);
// { email: ['The email field is required'], phone: [...] }
```

## Troubleshooting

### Form Not Submitting

- Verify `VueForm` is registered via the bundle's `load()` function
- Check that the `action` prop is set or form will submit to current URL
- Ensure `ajax` prop is correctly set (default is `true`)
- Verify `id` prop is unique and set

### Validation Errors Not Showing

- Check server is returning 422 status with proper error format
- Verify field `name` props match error keys in response
- Check browser console for JavaScript errors
- Ensure validation object is being populated correctly

### Select2 or DatePicker Not Initializing

- Verify required libraries are loaded (Select2, daterangepicker)
- Check that field `type` is correct (`select2`, `daterange`)
- Ensure DOM element has been rendered before initialization
- Clear any previous initialization with `destroy()` on field change

### Field Values Not Persisting

- Ensure each field has a unique `id` and `name`
- Verify field `onChange` is being called
- Check that form is not being reset unexpectedly
- Confirm `setField` method is being called correctly

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
- Bootstrap 4 or 5 (for form styling)
- jQuery (for AJAX and DOM manipulation)
- Select2 (optional, for enhanced select fields)
- daterangepicker (optional, for date range fields)
- mitt (event emitter for component communication)
- Autocomplete component (for autocomplete fields)

## Real-World Usage

This component is used extensively in the OATERS application for creating forms in modals:

```blade path=/opt/public_html/theultragrey/oaters/Modules/Ruby/resources/views/components/modals/contact-form.blade.php start=5
<vue-form id="{{$id}}-form" ref="{{$ref}}Form" large="3" ajax action="{{url('r/contacts/'.($edit? 'update' : 'create'))}}">
    @if($edit)
        <input type="hidden" name="id" :value="openContact.id">
    @endif
    <tabs active-tab="#user">
        <template #navigation>
            <tab target="#user">{{trans('ruby::contacts.user')}}</tab>
            <tab target="#contact">{{trans('ruby::contacts.contact')}}</tab>
        </template>
        <pane id="user">
            <vue-field name="username" type="text" id="username">Username</vue-field>
            <vue-field name="password" type="password" id="password">Password</vue-field>
        </pane>
        <!-- ... more tabs and fields ... -->
    </tabs>
</vue-form>
```

## See Also

- [Modal](/components/modal) - For forms in modal dialogs
- [Tab](/components/tab) - For multi-step forms
- [Card](/components/card) - For grouping form sections
- [Loader](/components/loader) - For displaying loading state during submission
