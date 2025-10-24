# Counter Component

## Overview

A card-based counter component that displays a single numeric value with optional additional information. Commonly used for dashboards to show statistics, metrics, and KPIs.

**Location**: `resources/components/counter.vue`

**Type**: Simple Component

## Props

| Prop | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `title` | String | Yes | - | Main counter title/label |
| `value` | String | Yes | - | The numeric value to display (large, centered) |
| `extraTitle` | String | No | - | Secondary label for additional info |
| `extraValue` | String | No | - | Secondary value shown in footer |
| `class` | String | No | - | Additional CSS classes for card wrapper |
| `color` | String | No | - | Bootstrap color class for header (e.g., `blue-2`, `green-10`) |
| `whiteText` | Boolean | No | `false` | Force white text (deprecated, auto-detected from color) |

## Computed

| Property | Description |
|----------|-------------|
| `cardClass` | Builds CSS classes for card wrapper |
| `headerClass` | Builds CSS classes for header with auto-white text for dark colors |

## Usage

### Basic Counter

```vue path=null start=null
<counter 
    title="Total Users" 
    value="1,234"
></counter>
```

### Counter with Color

```vue path=null start=null
<counter 
    title="Total Sales" 
    value="$45,678"
    color="green-2"
></counter>
```

### Counter with Extra Information

```vue path=null start=null
<counter 
    title="Active Subscriptions" 
    value="542"
    extra-title="This Month"
    extra-value="89"
    color="cyan-2"
></counter>
```

### Counter in Grid Layout

```vue path=null start=null
<div class="row card-deck">
    <counter 
        class="col-md-3 p-0" 
        title="Tenants" 
        value="{{$counters['tenants']}}" 
        extra-title="This Month"
        extra-value="{{$counters['tenants_month']}}"
        color="green-2"
    ></counter>
    
    <counter 
        class="col-md-3 p-0" 
        title="Payments" 
        value="{{$counters['purchases']}}" 
        extra-title="This Month"
        extra-value="{{$counters['purchases_month']}}"
        color="blue-2"
    ></counter>
    
    <counter 
        class="col-md-3 p-0" 
        title="Subscriptions" 
        value="{{$counters['subscriptions']}}"
        color="cyan-2"
    ></counter>
</div>
```

### Real Example from OATERS

```blade path=/opt/public_html/theultragrey/oaters/Modules/Sapphire/resources/views/admin/dashboard.blade.php start=9
<counter class="col-md-3 p-0" title="{{trans('sapphire::admin.tenants.title')}}" value="{{$counters['tenants']}}" extra-title="{{trans('common::words.this_month')}}" extra-value="{{$counters['tenants_month']}}" color="green-2" white-text></counter>
<counter class="col-md-3 p-0" title="{{trans('sapphire::admin.payments.title')}}" value="{{$counters['purchases']}}" extra-title="{{trans('common::words.this_month')}}" extra-value="{{$counters['purchases_month']}}" color="blue-2" white-text></counter>
<counter class="col-md-3 p-0" title="{{trans('sapphire::admin.subscriptions.title')}}" value="{{$counters['subscriptions']}}" color="cyan-2" white-text></counter>
```

### Dynamic Values from Data

```vue path=null start=null
<div class="row">
    <div class="col-md-6">
        <counter 
            title="Revenue" 
            :value="formattedRevenue"
            color="green-3"
        ></counter>
    </div>
    <div class="col-md-6">
        <counter 
            title="Expenses" 
            :value="formattedExpenses"
            extra-title="Target"
            :extra-value="budgetTarget"
            color="red-3"
        ></counter>
    </div>
</div>
```

```javascript path=null start=null
export default {
    data() {
        return {
            revenue: 50000,
            expenses: 12000,
            budgetTarget: 15000
        }
    },
    computed: {
        formattedRevenue() {
            return '$' + this.revenue.toLocaleString();
        },
        formattedExpenses() {
            return '$' + this.expenses.toLocaleString();
        }
    }
}
```

## Component Registration

```javascript path=null start=null
import {createApp} from "vue";
import Counter from "@/components/counter.vue";

let app = createApp({});

app.component('Counter', Counter);
app.mount('#app');
```

## Features

- ✅ Large, centered numeric display
- ✅ Optional secondary value in footer
- ✅ Customizable header color
- ✅ Automatic white text for dark colors
- ✅ Responsive card wrapper
- ✅ Perfect for dashboards and statistics

## Color System

The component uses a numeric color naming convention:

| Color Range | Text Color | Use Case |
|-------------|------------|----------|
| `color-1` to `color-5` | White | Dark backgrounds |
| `color-6` to `color-10` | Black (default) | Light backgrounds |

### Examples

```vue path=null start=null
<!-- Dark header with white text -->
<counter title="Dark Counter" value="100" color="blue-2"></counter>

<!-- Light header with default text -->
<counter title="Light Counter" value="200" color="blue-8"></counter>
```

## Styling

### Component Styles

```scss path=null start=null
.card.counter {
    position: relative;
    
    .card-body {
        position: static;
        
        span {
            position: absolute;
            margin: auto;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            font-weight: bolder;
            font-size: 2em;
            height: 1em;
            line-height: 1;
        }
    }
}
```

### Custom Styling

The value is centered both horizontally and vertically using absolute positioning with margin auto technique:

```css path=null start=null
.card.counter .card-body span {
    font-weight: 700;
    font-size: 2em;
    text-align: center;
    /* Centered via absolute positioning */
}
```

## Best Practices

1. **Use consistent formatting**: Format numbers consistently across counters
   ```vue path=null start=null
   <counter 
       title="Sales" 
       :value="'$' + sales.toLocaleString()"
   ></counter>
   ```

2. **Add extra context**: Use `extraTitle` and `extraValue` for meaningful comparisons
   ```vue path=null start=null
   <counter 
       title="New Users" 
       :value="totalUsers"
       extra-title="This Month"
       :extra-value="monthlyUsers"
   ></counter>
   ```

3. **Use appropriate colors**: Match colors to metric types
   ```vue path=null start=null
   <!-- Positive metrics in green -->
   <counter title="Revenue" value="$10K" color="green-2"></counter>
   
   <!-- Negative metrics in red -->
   <counter title="Issues" value="5" color="red-2"></counter>
   
   <!-- Neutral metrics in blue -->
   <counter title="Pending" value="23" color="blue-2"></counter>
   ```

4. **Grid layout for dashboards**: Use Bootstrap grid for responsive layout
   ```vue path=null start=null
   <div class="row card-deck">
       <counter class="col-md-3 p-0" ...></counter>
       <counter class="col-md-3 p-0" ...></counter>
       <counter class="col-md-3 p-0" ...></counter>
   </div>
   ```

5. **Keep titles short**: Use concise, readable titles
   ```vue path=null start=null
   <!-- Good -->
   <counter title="Active Users" value="1,234"></counter>
   
   <!-- Too long -->
   <counter title="Total Number of Active Users in System" value="1,234"></counter>
   ```

## Common Patterns

### Dashboard Statistics

```vue path=null start=null
<div class="row card-deck mb-3">
    <counter 
        class="col-md-3 p-0"
        title="Total Revenue"
        value="$125,400"
        extra-title="This Month"
        extra-value="$45,320"
        color="green-2"
    ></counter>
    
    <counter 
        class="col-md-3 p-0"
        title="Total Orders"
        value="3,245"
        extra-title="This Month"
        extra-value="1,120"
        color="blue-2"
    ></counter>
    
    <counter 
        class="col-md-3 p-0"
        title="Active Users"
        value="892"
        extra-title="Online Now"
        extra-value="45"
        color="cyan-2"
    ></counter>
    
    <counter 
        class="col-md-3 p-0"
        title="Conversion Rate"
        value="3.2%"
        color="purple-2"
    ></counter>
</div>
```

### KPI Cards

```vue path=null start=null
<div class="row">
    <div class="col-lg-3 col-md-6">
        <counter 
            title="Customer Satisfaction"
            value="4.8"
            extra-title="Out of 5"
            color="amber-2"
        ></counter>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <counter 
            title="System Uptime"
            value="99.9%"
            color="green-2"
        ></counter>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <counter 
            title="Avg Response Time"
            value="45ms"
            extra-title="Target"
            extra-value="< 50ms"
            color="blue-2"
        ></counter>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <counter 
            title="Open Tickets"
            value="12"
            color="red-3"
        ></counter>
    </div>
</div>
```

### Real-time Updates

```javascript path=null start=null
export default {
    data() {
        return {
            stats: {
                users: 1234,
                revenue: 50000,
                orders: 256
            }
        }
    },
    methods: {
        updateStats() {
            // Fetch stats from server
            setInterval(() => {
                fetch('/api/dashboard/stats')
                    .then(r => r.json())
                    .then(data => {
                        this.stats = data;
                    });
            }, 5000); // Update every 5 seconds
        }
    },
    mounted() {
        this.updateStats();
    }
}
```

```vue path=null start=null
<counter 
    title="Active Sessions" 
    :value="stats.activeSessions"
></counter>
```

### Comparison Counters

```vue path=null start=null
<div class="row">
    <div class="col-md-6">
        <counter 
            title="This Period"
            :value="currentSales"
            extra-title="Last Period"
            :extra-value="previousSales"
            :color="currentSales > previousSales ? 'green-3' : 'red-3'"
        ></counter>
    </div>
    
    <div class="col-md-6">
        <counter 
            title="Growth Rate"
            :value="growthPercentage + '%'"
            :color="growthPercentage > 0 ? 'green-3' : 'red-3'"
        ></counter>
    </div>
</div>
```

## Number Formatting Examples

```javascript path=null start=null
// Format as currency
formatCurrency(value) {
    return '$' + value.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Format as percentage
formatPercentage(value) {
    return (value * 100).toFixed(1) + '%';
}

// Format as compact number
formatCompact(value) {
    if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
    if (value >= 1000) return (value / 1000).toFixed(1) + 'K';
    return value.toString();
}

// Format with thousand separator
formatThousands(value) {
    return value.toLocaleString();
}
```

## Troubleshooting

### Title or value not showing
- Verify `title` and `value` props are provided
- Check that props are not empty strings
- Ensure no CSS is hiding the elements

### Color not applying
- Confirm color class exists in your CSS (e.g., `bg-green-2`)
- Check if custom CSS is overriding header styles
- Verify Bootstrap 4 is loaded

### Extra footer not displaying
- Verify both `extraTitle` and `extraValue` props are provided
- Check that neither prop is undefined or empty
- Ensure footer slot is not being overridden

### Value size too small/large
- Check the `font-size: 2em` style in component
- Verify no parent CSS is constraining the size
- Test in different container sizes

### Layout issues in grid
- Use Bootstrap grid classes (col-md-3, etc.)
- Add `p-0` class to remove extra padding
- Ensure parent row has `card-deck` class for equal heights

### Text color contrast issues
- Verify color choice for readability
- For dark backgrounds, white text is automatically applied for colors 1-5
- Test with color-blind viewers if color is important
