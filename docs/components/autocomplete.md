# Autocomplete Component

## Overview

An AJAX-powered autocomplete input component with debounced search, supporting both single and multiple selection modes.

**Location**: `resources/components/autocomplete.vue`

**Type**: Simple Component

## Props

| Prop | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `id` | String | Yes | - | Unique identifier for the input element |
| `name` | String | No | - | Form input name attribute |
| `placeholder` | String | No | - | Placeholder text for the input |
| `multiple` | Boolean | No | `false` | Enable multiple selection mode (uses Select2) |
| `required` | Boolean | No | `false` | Make the field required |
| `url` | String | Yes | - | AJAX endpoint for search results |
| `queryKey` | String | No | `'query'` | Parameter name for search query in request |
| `resultsKey` | String | No | `'suggestions'` | Property name for results in response |
| `limit` | Number | No | `5` | Maximum number of suggestions to show |
| `selectedId` | String | No | - | Pre-selected item ID |
| `selectedTitle` | String | No | - | Pre-selected item display text |

## Data

| Property | Type | Description |
|----------|------|-------------|
| `show` | Boolean | Controls dropdown visibility |
| `suggestions` | Array/Object | Search results from server |
| `query` | String | Current search query text |
| `selection` | String | Selected item ID |

## Events

| Event | Payload | Description |
|-------|---------|-------------|
| `change` | `(name, selectedKey)` | Emitted when selection changes |

## Methods

| Method | Parameters | Description |
|--------|-----------|-------------|
| `search(event)` | `event`: KeyboardEvent | Debounced AJAX search (200ms) |
| `select(key, value)` | `key`: String, `value`: String | Select an item from suggestions |
| `clear()` | - | Clear current selection |
| `close(delayed)` | `delayed`: Boolean | Close suggestions dropdown |

## Injection

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `validation` | Object | `[]` | Form validation errors injected from parent |

## Server Response Format

### Request

```json
{
    "query": "john",
    "limit": 5
}
```

### Response (Array)

```json
{
    "suggestions": ["John Doe", "John Smith", "Johnny Walker"]
}
```

### Response (Object)

```json
{
    "suggestions": {
        "1": "John Doe",
        "2": "John Smith",
        "3": "Johnny Walker"
    }
}
```

## Usage

### Single Selection

```vue path=null start=null
<autocomplete
    id="employee-search"
    name="employee_id"
    placeholder="Search employees..."
    url="/api/employees/search"
    @change="handleEmployeeSelect"
></autocomplete>
```

### Multiple Selection (with Select2)

```vue path=null start=null
<autocomplete
    id="tags-input"
    name="tags[]"
    placeholder="Select tags..."
    url="/api/tags/search"
    multiple
    required
>
    <template #options>
        <!-- Pre-populated options -->
        <option value="1">Tag 1</option>
        <option value="2">Tag 2</option>
    </template>
</autocomplete>
```

### With Pre-selected Value

```vue path=null start=null
<autocomplete
    id="department-search"
    name="department_id"
    url="/api/departments/search"
    selected-id="5"
    selected-title="Engineering Department"
></autocomplete>
```

### Custom Query/Results Keys

```vue path=null start=null
<autocomplete
    id="product-search"
    url="/api/products/search"
    query-key="search_term"
    results-key="products"
    :limit="10"
></autocomplete>
```

### Handling Selection Changes

```javascript path=null start=null
export default {
    methods: {
        handleEmployeeSelect(fieldName, employeeId) {
            console.log(`Selected employee ID: ${employeeId}`);
            // Fetch additional employee details
            this.loadEmployeeDetails(employeeId);
        }
    }
}
```

## Component Registration

```javascript path=null start=null
import {createApp} from "vue";
import Autocomplete from "@/components/autocomplete.vue";

let app = createApp({
    methods: {
        handleSelection(name, value) {
            console.log(`${name}: ${value}`);
        }
    }
});

app.component('Autocomplete', Autocomplete);
app.mount('#app');
```

## Dependencies

### Required
- jQuery for AJAX and DOM manipulation
- Lodash (`debounce` function)

### Optional
- [Select2](https://select2.org/) - Required for `multiple` mode
- [Select2 Bootstrap 4 Theme](https://github.com/ttskch/select2-bootstrap4-theme) - For Bootstrap styling

### Import Example

```javascript path=null start=null
import select2 from 'select2';
import 'select2/dist/css/select2.min.css';
import 'select2-theme-bootstrap4/dist/select2-bootstrap.min.css';

select2(); // Initialize Select2
```

## Features

- ✅ Debounced search (200ms) for performance
- ✅ Keyboard navigation (Esc to close)
- ✅ Clear button (× icon) when value selected
- ✅ Form validation integration
- ✅ Single or multiple selection modes
- ✅ Bootstrap styling
- ✅ Server-side search with configurable parameters

## Form Integration

### With Vue Field Component

The component integrates with OATERS' form system when used within a `VueField` component:

```vue path=null start=null
<vue-field>
    <autocomplete
        id="employee-select"
        name="employee_id"
        url="/api/employees/search"
    ></autocomplete>
</vue-field>
```

### Validation Support

The component displays validation errors via injected validation state:

```vue path=null start=null
<!-- Border turns red when validation error exists -->
<autocomplete
    id="employee"
    name="employee_id"
    url="/api/employees/search"
    required
></autocomplete>
```

## Styling

The component includes custom styling for the autocomplete group:

```scss path=null start=null
.autocomplete-group {
    position: relative;

    .autocomplete-clear {
        display: block;
        position: absolute;
        top: 6px;
        right: 12px;
        cursor: pointer;
        font-size: 1.5em;
        line-height: 1;
    }

    .autocomplete-dropdown {
        li {
            cursor: pointer;
        }
    }
}
```

## Best Practices

1. **Debouncing is built-in**: No need to add your own debouncing (200ms delay)

2. **Use Select2 for multiple**: Set `multiple` prop for multi-select functionality
   ```vue path=null start=null
   <autocomplete multiple :url="searchUrl"></autocomplete>
   ```

3. **Return consistent format**: Server should always return object/array in same key
   ```php
   return response()->json(['suggestions' => $results]);
   ```

4. **Limit results**: Use the `limit` prop to control performance
   ```vue path=null start=null
   <autocomplete :limit="10" :url="searchUrl"></autocomplete>
   ```

5. **Handle empty states**: Server should return empty array/object when no results

## Server-Side Implementation Example

### Laravel Controller

```php path=null start=null
public function search(Request $request)
{
    $query = $request->input('query');
    $limit = $request->input('limit', 5);
    
    $results = Employee::where('name', 'LIKE', "%{$query}%")
        ->limit($limit)
        ->pluck('name', 'id')
        ->toArray();
    
    return response()->json([
        'suggestions' => $results
    ]);
}
```

### With Custom Keys

```php path=null start=null
public function search(Request $request)
{
    $searchTerm = $request->input('search_term');
    
    $products = Product::where('title', 'LIKE', "%{$searchTerm}%")
        ->get()
        ->pluck('title', 'id');
    
    return response()->json([
        'products' => $products
    ]);
}
```

## Common Patterns

### Cascading Dropdowns

```vue path=null start=null
<autocomplete
    id="country"
    name="country_id"
    url="/api/countries/search"
    @change="handleCountryChange"
></autocomplete>

<autocomplete
    id="city"
    name="city_id"
    :url="citySearchUrl"
    :key="selectedCountry"
></autocomplete>
```

```javascript path=null start=null
data() {
    return {
        selectedCountry: null,
        citySearchUrl: '/api/cities/search'
    }
},
methods: {
    handleCountryChange(name, countryId) {
        this.selectedCountry = countryId;
        this.citySearchUrl = `/api/cities/search?country_id=${countryId}`;
    }
}
```

### Dynamic Search Parameters

```vue path=null start=null
<autocomplete
    id="employee"
    url="/api/employees/search"
    :ajax-data="getSearchParams"
></autocomplete>
```

```javascript path=null start=null
methods: {
    getSearchParams(data) {
        data.department_id = this.departmentId;
        data.status = 'active';
        return data;
    }
}
```

## Troubleshooting

### Autocomplete not searching
- Verify `url` endpoint is accessible and returns correct format
- Check browser console for AJAX errors
- Ensure `queryKey` and `resultsKey` match server implementation
- Verify jQuery is loaded

### Select2 not working in multiple mode
- Ensure Select2 library is imported and initialized
- Check that Select2 CSS is loaded
- Verify the component is mounted before Select2 initialization

### Clear button not appearing
- Check that a value is selected (`selection` is not empty)
- Verify the clear button styles are not overridden
- Ensure the component is in single selection mode (not `multiple`)

### Dropdown not closing
- Check that `close()` method is being called
- Verify no JavaScript errors are preventing execution
- Ensure Esc key handler is working

### Pre-selected value not showing
- Verify `selectedId` and `selectedTitle` props are passed correctly
- Check that component is used within `VueField` parent
- Ensure values are available when component mounts
