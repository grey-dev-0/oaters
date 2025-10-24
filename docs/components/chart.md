# Chart Component

## Overview

A Chart.js integration component with optional date range filtering, supporting multiple chart types and server-side data loading.

**Location**: `resources/components/chart.vue`

**Type**: Simple Component

## Props

| Prop | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `type` | String | No | `'line'` | Chart type: `line`, `bar`, `pie`, `doughnut`, `radar`, etc. |
| `id` | String | Yes | - | Unique identifier for the chart canvas |
| `class` | String | No | - | Additional CSS classes for card wrapper |
| `url` | String | Yes | - | AJAX endpoint for chart data |
| `title` | String | Yes | - | Chart title displayed in header |
| `color` | String | No | - | Bootstrap color class for header |
| `ranged` | Boolean | No | `false` | Enable date range picker |
| `defaultRange` | String | No | - | Default date range (e.g., "2024-01-01 to 2024-12-31") |
| `rangeTitle` | String | No | - | Placeholder for range picker |
| `centerHeader` | Boolean | No | `false` | Center-align the header title |
| `noPadding` | Boolean | No | `false` | Remove card body padding |

## Data

| Property | Type | Description |
|----------|------|-------------|
| `loading` | Boolean | Shows spinner while loading data |
| `datasets` | Array | Chart.js datasets from server |
| `labels` | Array | Chart.js labels from server |
| `chart` | Object | Chart.js instance |

## Computed

| Property | Description |
|----------|-------------|
| `cardClass` | Builds CSS classes for card wrapper |
| `headerClass` | Builds CSS classes for header |
| `bodyClass` | Builds CSS classes for card body |
| `whiteTitle` | Auto-determines if title should be white |

## Methods

| Method | Description |
|--------|-------------|
| `initRangePicker()` | Initialize daterangepicker for ranged charts |
| `load()` | Fetch chart data from server via AJAX |
| `draw()` | Render the chart with Chart.js |

## Server Response Format

### Request (if ranged)

```json
{
    "range": "2024-01-01 to 2024-12-31"
}
```

### Response

```json
{
    "datasets": [
        {
            "label": "Sales",
            "data": [10, 20, 30, 40],
            "backgroundColor": "rgba(54, 162, 235, 0.2)",
            "borderColor": "rgba(54, 162, 235, 1)",
            "borderWidth": 1
        }
    ],
    "labels": ["Jan", "Feb", "Mar", "Apr"]
}
```

## Usage

### Basic Line Chart

```vue path=null start=null
<chart 
    id="sales-chart" 
    title="Monthly Sales" 
    url="/api/charts/sales"
    color="blue-2"
></chart>
```

### Doughnut Chart with Custom Class

```vue path=null start=null
<chart 
    type="doughnut" 
    id="subscriptions-chart" 
    class="col-md-3 p-0" 
    color="purple-3" 
    style="max-height:300px" 
    center-header 
    title="Subscriptions" 
    url="/sa/charts/subscriptions-pie"
></chart>
```

### Chart with Date Range Filter

```vue path=null start=null
<chart 
    id="subscriptions-line" 
    color="cyan-10" 
    ranged 
    :default-range="defaultChartRange" 
    title="New Subscriptions" 
    url="/sa/charts/subscriptions-line"
></chart>
```

### Bar Chart

```vue path=null start=null
<chart 
    type="bar"
    id="revenue-chart" 
    title="Revenue by Product" 
    url="/api/charts/revenue"
    color="green-2"
></chart>
```

### Multiple Datasets

```vue path=null start=null
<chart 
    id="comparison-chart" 
    title="Sales vs Costs" 
    url="/api/charts/comparison"
    color="blue-3"
></chart>
```

### Real Examples from OATERS

```blade path=/opt/public_html/theultragrey/oaters/Modules/Sapphire/resources/views/admin/dashboard.blade.php start=12
<chart type="doughnut" id="subscriptions-chart" class="col-md-3 p-0" color="purple-3" style="max-height:300px" center-header title="{{trans('sapphire::admin.subscriptions.title')}}" url="{{url('sa/charts/subscriptions-pie')}}"></chart>
```

```blade path=/opt/public_html/theultragrey/oaters/Modules/Sapphire/resources/views/admin/dashboard.blade.php start=16
<chart id="subscriptions-line" color="cyan-10" ranged :default-range="defaultChartRange" title="{{trans('sapphire::admin.subscriptions.new')}}" url="{{url('sa/charts/subscriptions-line')}}"></chart>
```

## Component Registration

```javascript path=null start=null
import {createApp} from "vue";
import Chart from "@/components/chart.vue";

let app = createApp({
    data() {
        return {
            defaultChartRange: '2024-01-01 to 2024-12-31'
        }
    }
});

app.component('Chart', Chart);
app.mount('#app');
```

## Chart Types

Supports all Chart.js chart types:

| Type | Description | Best For |
|------|-------------|----------|
| `line` | Line chart with points | Time series trends |
| `bar` | Vertical bar chart | Category comparisons |
| `radar` | Radar/spider chart | Multi-dimensional data |
| `pie` | Pie chart | Parts of a whole (no animation) |
| `doughnut` | Doughnut chart | Parts of a whole (with center) |
| `polarArea` | Polar area chart | Categorical data |
| `bubble` | Bubble chart | Three-dimensional data |
| `scatter` | Scatter plot | Correlations |

## Dependencies

### Required
- [Chart.js](https://www.chartjs.org/) - JavaScript charting library
- jQuery for AJAX requests

### Optional
- [daterangepicker](https://www.daterangepicker.com/) - Required for `ranged` charts
- Moment.js - Required by daterangepicker

### Import Example

```javascript path=null start=null
import Chart from 'chart.js/auto';
import 'daterangepicker';
import 'daterangepicker/daterangepicker.css';
```

## Features

- ✅ Loading spinner during data fetch
- ✅ Automatic chart refresh when range changes
- ✅ Responsive legend positioning
- ✅ Error handling with alerts
- ✅ Card wrapper with customizable styling
- ✅ Auto-destroy and recreate on data update
- ✅ Multiple chart types support

## Styling

### Component Styles

```scss path=null start=null
.card-header {
    .form-control.float-right {
        width: initial !important;
    }
}

.card-body {
    position: relative;
    min-height: 164px;
    
    .chart-loader {
        position: absolute;
        margin: auto;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        width: 2em;
        height: 2em;
        i {
            font-size: 2em;
        }
    }
}
```

### Custom Styling

```css path=null start=null
.chart-container {
    position: relative;
    height: 400px;
}

canvas {
    max-width: 100%;
}
```

## Legend Positioning

The component automatically positions the legend:
- **Pie/Doughnut**: Bottom
- **Other types**: Top

```javascript path=/opt/public_html/theultragrey/oaters/resources/components/chart.vue start=137
chartOptions = {
    legend: {
        position: (this.type == 'pie' || this.type == 'doughnut')? 'bottom' : 'top'
    }
}
```

## Best Practices

1. **Enable ranged for time-series**: Use date filtering for temporal data
   ```vue path=null start=null
   <chart 
       ranged 
       :default-range="defaultRange"
       url="/api/charts/sales-trends"
   ></chart>
   ```

2. **Match chart type to data**: 
   - Pie/doughnut for parts-of-whole
   - Line/bar for trends
   - Radar for multi-dimensional
   ```vue path=null start=null
   <chart type="pie" url="/api/breakdown" title="Market Share"></chart>
   ```

3. **Return proper Chart.js format**: Server must return valid Chart.js datasets structure
   ```json
   {
       "datasets": [{
           "label": "Sales",
           "data": [10, 20, 30],
           "backgroundColor": "rgba(75, 192, 192, 0.2)"
       }],
       "labels": ["Q1", "Q2", "Q3"]
   }
   ```

4. **Handle errors gracefully**: Implement proper error responses from server
   ```php
   return response()->json([
       'message' => 'Unable to load chart data'
   ], 500);
   ```

5. **Use descriptive titles**: Make chart purpose clear
   ```vue path=null start=null
   <chart title="Revenue Trends (Last 12 Months)"></chart>
   ```

## Server-Side Implementation Example

### Laravel Controller

```php path=null start=null
public function salesChart(Request $request)
{
    $range = $request->input('range');
    
    // Parse date range if provided
    if ($range && strpos($range, ' to ')) {
        [$startDate, $endDate] = explode(' to ', $range);
        $startDate = \Carbon\Carbon::createFromFormat('Y-m-d', trim($startDate));
        $endDate = \Carbon\Carbon::createFromFormat('Y-m-d', trim($endDate));
    } else {
        $startDate = \Carbon\Carbon::now()->subDays(30);
        $endDate = \Carbon\Carbon::now();
    }
    
    $sales = Sale::whereBetween('created_at', [$startDate, $endDate])
        ->groupBy(\DB::raw('DATE(created_at)'))
        ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
        ->get();
    
    return response()->json([
        'datasets' => [
            [
                'label' => 'Sales',
                'data' => $sales->pluck('total'),
                'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                'borderColor' => 'rgba(75, 192, 192, 1)',
                'borderWidth' => 2,
                'fill' => true
            ]
        ],
        'labels' => $sales->pluck('date')
    ]);
}
```

### Pie Chart Example

```php path=null start=null
public function subscriptionsChart()
{
    $subscriptions = Subscription::selectRaw('status, COUNT(*) as count')
        ->groupBy('status')
        ->get();
    
    $colors = [
        'active' => 'rgba(75, 192, 192, 0.8)',
        'inactive' => 'rgba(255, 159, 64, 0.8)',
        'expired' => 'rgba(153, 102, 255, 0.8)'
    ];
    
    return response()->json([
        'datasets' => [
            [
                'label' => 'Subscriptions by Status',
                'data' => $subscriptions->pluck('count'),
                'backgroundColor' => $subscriptions->map(fn($s) => $colors[$s->status] ?? 'rgba(200, 200, 200, 0.8)'),
                'borderColor' => '#fff',
                'borderWidth' => 2
            ]
        ],
        'labels' => $subscriptions->pluck('status')
    ]);
}
```

## Common Patterns

### Dashboard with Multiple Charts

```vue path=null start=null
<div class="row card-deck">
    <chart 
        type="doughnut"
        id="subscriptions"
        class="col-md-3"
        color="purple-3"
        title="Subscriptions"
        url="/api/charts/subscriptions"
    ></chart>
    
    <chart 
        id="revenue"
        class="col-md-3"
        color="green-3"
        title="Revenue"
        url="/api/charts/revenue"
    ></chart>
    
    <chart 
        type="bar"
        id="users"
        class="col-md-3"
        color="blue-3"
        title="New Users"
        url="/api/charts/users"
    ></chart>
</div>
```

### Comparison Charts

```vue path=null start=null
<div class="row">
    <div class="col-md-6">
        <chart 
            ranged
            :default-range="lastMonth"
            id="sales-line"
            title="Sales Trends"
            url="/api/charts/sales"
        ></chart>
    </div>
    <div class="col-md-6">
        <chart 
            type="bar"
            ranged
            :default-range="lastMonth"
            id="costs-bar"
            title="Cost Analysis"
            url="/api/charts/costs"
        ></chart>
    </div>
</div>
```

### Single Page Charts with Auto-Refresh

```vue path=null start=null
<script setup>
import { ref, onMounted } from 'vue';

const defaultRange = ref(getDefaultRange());
const refreshRate = ref(60000); // 1 minute

function getDefaultRange() {
    const now = new Date();
    const month = now.getMonth() + 1;
    return `${now.getFullYear()}-${month}-01 to ${now.getFullYear()}-${month}-${getDaysInMonth(month)}`;
}

onMounted(() => {
    setInterval(() => {
        // Manually refresh charts
        $refs.chartId.load();
    }, refreshRate.value);
});
</script>

<template>
    <chart 
        ref="chartId"
        ranged
        :default-range="defaultRange"
        url="/api/charts/real-time"
        title="Real-Time Analytics"
    ></chart>
</template>
```

## Data Processing Example

### Transform Raw Data

```javascript path=null start=null
methods: {
    formatChartData(rawData) {
        return {
            datasets: rawData.map(dataset => ({
                label: dataset.name,
                data: dataset.values,
                backgroundColor: this.getRandomColor(),
                borderColor: this.getRandomColor(),
                fill: false
            })),
            labels: rawData.labels
        };
    },
    
    getRandomColor() {
        const letters = '0123456789ABCDEF';
        let color = 'rgba(';
        for (let i = 0; i < 3; i++) {
            color += Math.floor(Math.random() * 256) + ',';
        }
        color += '0.8)';
        return color;
    }
}
```

## Troubleshooting

### Chart not rendering
- Ensure Chart.js is imported and available
- Check server response matches expected format
- Verify canvas element has a parent with dimensions
- Check browser console for Chart.js errors

### Date range not working
- Ensure daterangepicker is imported
- Verify moment.js is loaded (required by daterangepicker)
- Check that `ranged` prop is set to `true`
- Verify date format matches server implementation

### Chart not refreshing on range change
- Confirm `load()` method is called when range changes
- Check browser console for AJAX errors
- Verify server is returning new data

### Loading spinner not showing
- Ensure `loading` data property is set to `true`
- Check that spinner CSS is not hidden
- Verify Font Awesome is loaded

### Legend not displaying
- Ensure `legend` is configured in Chart.js options
- Check that chart type is supported
- Verify CSS is not hiding legend

### Chart getting cut off
- Add `position: relative` to parent container
- Ensure parent has defined dimensions
- Check responsive options in Chart.js config

### Colors not applying
- Verify `backgroundColor` and `borderColor` are valid CSS colors
- Use rgba format for transparency: `rgba(75, 192, 192, 0.2)`
- Check that colors match Chart.js requirements

### Multiple charts overlapping
- Ensure each chart has unique `id`
- Use grid layout to separate charts
- Check canvas z-index if overlapping occurs
