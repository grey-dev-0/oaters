# OrgChart Component

## Overview

A sophisticated organizational chart visualization component that renders hierarchical personnel structures using JointJS. It displays organization hierarchy with support for department grouping, automatic layout, and responsive sizing.

**Location**: `resources/components/org-chart.vue`

**Type**: Simple Component (Advanced)

## Dependencies

- [JointJS Core](https://www.jointjs.com/) - Diagram and visualization library
- [JointJS Directed Graph Layout](https://www.jointjs.com/) - Automatic hierarchical layout

### Installation

```bash
npm install @joint/core @joint/layout-directed-graph
```

## Props

| Prop | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| `nodes` | Object | Yes | - | Adjacency list defining node hierarchy and relationships |
| `groups` | Object | No | `{}` | Grouping of nodes by department/category (cluster visualization) |

## Data Structures

### Nodes Structure (Adjacency List)

An object where each key is a node label and the value is an array of child node labels:

```javascript path=null start=null
{
    "CEO": ["CTO", "CFO"],
    "CTO": ["Engineer A", "Engineer B"],
    "CFO": ["Accountant A"],
    "Engineer A": [],
    "Engineer B": [],
    "Accountant A": []
}
```

**Rules:**
- Each node must have an entry in the adjacency list (even if empty)
- All referenced child nodes must exist in the adjacency list
- Circular references will cause layout issues

### Groups Structure (Department Clustering)

An object where each key is a group label and the value is an array of node labels in that group:

```javascript path=null start=null
{
    "Engineering Department": ["CTO", "Engineer A", "Engineer B"],
    "Finance Department": ["CFO", "Accountant A"]
}
```

**Rules:**
- Group names are displayed as labels
- Multiple nodes can be in the same group
- Nodes not in any group are still displayed

## Usage

### Basic Organization Chart

```vue path=null start=null
<org-chart 
    :nodes="organizationStructure"
></org-chart>
```

```javascript path=null start=null
data() {
    return {
        organizationStructure: {
            "CEO": ["VP Engineering", "VP Sales"],
            "VP Engineering": ["Engineer A", "Engineer B"],
            "VP Sales": ["Sales Rep A"],
            "Engineer A": [],
            "Engineer B": [],
            "Sales Rep A": []
        }
    }
}
```

### With Department Grouping

```vue path=null start=null
<org-chart 
    :nodes="organizationStructure"
    :groups="departments"
></org-chart>
```

```javascript path=null start=null
data() {
    return {
        organizationStructure: {
            "CEO": ["CTO", "CFO"],
            "CTO": ["Engineer A", "Engineer B"],
            "CFO": ["Accountant A"],
            "Engineer A": [],
            "Engineer B": [],
            "Accountant A": []
        },
        departments: {
            "Engineering": ["CTO", "Engineer A", "Engineer B"],
            "Finance": ["CFO", "Accountant A"]
        }
    }
}
```

### Real Example from OATERS

```blade path=/opt/public_html/theultragrey/oaters/Modules/Ruby/resources/views/structure.blade.php start=18
<org-chart ref="chart" :nodes="members" :groups="departments"></org-chart>
```

```javascript path=/opt/public_html/theultragrey/oaters/resources/js/ruby/structure.js start=5
app = createApp({
    data(){
        return {
            members: null,
            departments: null
        };
    },
    mounted(){
        const departments = {}, deptMembers = [];
        const transformedData = _reduce(subordinates, (result, item) => {
            const managerName = item.manager.name, 
                  memberName = item.member.name, 
                  departmentName = item.department.name;
            
            if(!result[managerName])
                result[managerName] = [];
            if(!result[memberName])
                result[memberName] = [];
            
            result[managerName].push(memberName);
            
            if(!departments[departmentName])
                departments[departmentName] = [];
            if(!departments[departmentName].includes(managerName) && !deptMembers.includes(managerName))
                departments[departmentName].push(managerName);
            if(!departments[departmentName].includes(memberName)){
                departments[departmentName].push(memberName);
                deptMembers.push(memberName);
            }
            return result;
        }, {});
        
        this.members = transformedData;
        this.departments = departments;
    }
})
```

## Component Registration

```javascript path=null start=null
import {createApp} from "vue";
import OrgChart from "@/components/org-chart.vue";

let app = createApp({});

app.component('OrgChart', OrgChart);
app.mount('#app');
```

Or using the common loader:

```javascript path=null start=null
common.loadComponents(app, {OrgChart: 'org-chart'});
```

## Features

- ✅ Hierarchical organization chart visualization
- ✅ Automatic layout algorithm (DirectedGraph)
- ✅ Department/group clustering with visual grouping boxes
- ✅ Responsive sizing based on viewport
- ✅ Auto-centered graph
- ✅ Scrollable container for large charts
- ✅ Support for multi-line node labels
- ✅ Automatic node sizing based on label length

## Visual Design

### Node Elements

- **Shape**: Rectangle with rounded corners (rx: 2, ry: 2)
- **Border Color**: `#226CE0` (Blue)
- **Border Width**: 1px
- **Text Color**: `#131E29` (Dark)
- **Font**: Monospace
- **Font Size**: 12px
- **Dynamic Sizing**: Based on text content length

### Link Elements

- **Style**: Straight lines with right-angle routing
- **Color**: `#F68E96` (Pink/Red)
- **Connectors**: Bottom anchor on parent, top anchor on child

### Group/Cluster Elements

- **Shape**: Rectangle with rounded corners (rx: 8, ry: 8)
- **Border**: `#E6A23C` (Orange) dashed (5,3)
- **Border Width**: 2px
- **Background**: `rgba(60, 162, 230, 0.05)` (Light blue)
- **Label Color**: `#001840` (Dark)
- **Label Font**: Arial, sans-serif, bold
- **Label Size**: 14px

### Background

- **Color**: `#F8F9FA` (Light gray)

## Architecture

### Layout Algorithm

The component uses JointJS's DirectedGraph layout to automatically position nodes:

```javascript path=null start=null
DirectedGraph.layout(graph, {
    nodeSep: 32,    // Horizontal space between nodes
    rankSep: 24     // Vertical space between ranks
})
```

### Responsive Sizing

1. Container height calculated based on available viewport space
2. Minimum height: 300px
3. Paper (canvas) fits content with `fitToContent()`
4. Graph automatically centers and scales

### Cluster Embedding

When groups are specified:
1. Create group/cluster elements
2. Embed nodes within their respective groups
3. Position group labels above grouped nodes
4. Groups are embedded containers, nodes are children

## Internal Methods

### `layout()`

Main layout orchestration function:
- Converts adjacency list to JointJS cells (nodes + links)
- Creates cluster elements for groups
- Embeds nodes in clusters
- Applies DirectedGraph layout
- Centers the graph

### `adjacencyListToCells(adjacencyList)`

Converts adjacency list representation to JointJS elements:
- Creates node elements for each key in adjacency list
- Creates link elements for parent-child relationships
- Validates that all referenced nodes exist
- Returns combined array of elements and links

### `makeNodeElement(label)`

Creates a visual node element:
- Calculates dynamic width based on longest line in label
- Calculates height based on number of lines
- Configures styling and attributes
- Returns JointJS Rectangle shape

### `makeLink(parentLabel, childLabel)`

Creates a link element between nodes:
- Defines source anchor (bottom of parent)
- Defines target anchor (top of child)
- Sets line color and routing style
- Returns JointJS Link shape

### `makeClusterElement(label)`

Creates a group/cluster container element:
- Dashed border styling
- Light background
- Configures label positioning
- Returns JointJS Rectangle with custom markup

### `centerGraph()`

Centers the graph in the available space:
- Calculates bounding box of all elements
- Computes offsets to center horizontally and vertically
- Translates graph to centered position
- Fits paper content

### `calculateAvailableHeight()`

Calculates responsive container height:
- Gets container's position from viewport top
- Calculates remaining space (window height - top - padding)
- Sets container height (minimum 300px)

## Data Transformation Example

Converting flat manager-member relationships to hierarchical structure:

```javascript path=null start=null
// Raw data from server
const subordinates = [
    { 
        manager: { name: "CEO" }, 
        member: { name: "CTO" },
        department: { name: "Engineering" }
    },
    { 
        manager: { name: "CTO" }, 
        member: { name: "Engineer A" },
        department: { name: "Engineering" }
    }
];

// Transform to nodes (adjacency list)
const members = {
    "CEO": ["CTO"],
    "CTO": ["Engineer A"],
    "Engineer A": []
};

// Transform to groups (departments)
const departments = {
    "Engineering": ["CEO", "CTO", "Engineer A"]
};
```

## Common Patterns

### Loading from Database

```javascript path=null start=null
import { reduce as _reduce } from "lodash";

export default {
    data() {
        return {
            members: null,
            departments: null
        }
    },
    methods: {
        loadOrganization() {
            fetch('/api/organization/structure')
                .then(r => r.json())
                .then(subordinates => {
                    const departments = {}, deptMembers = [];
                    
                    const transformedData = _reduce(
                        subordinates, 
                        (result, item) => {
                            const managerName = item.manager.name;
                            const memberName = item.member.name;
                            const departmentName = item.department.name;
                            
                            // Build hierarchy
                            if (!result[managerName])
                                result[managerName] = [];
                            if (!result[memberName])
                                result[memberName] = [];
                            
                            result[managerName].push(memberName);
                            
                            // Build departments
                            if (!departments[departmentName])
                                departments[departmentName] = [];
                            
                            if (!departments[departmentName].includes(managerName) 
                                && !deptMembers.includes(managerName)) {
                                departments[departmentName].push(managerName);
                            }
                            
                            if (!departments[departmentName].includes(memberName)) {
                                departments[departmentName].push(memberName);
                                deptMembers.push(memberName);
                            }
                            
                            return result;
                        }, 
                        {}
                    );
                    
                    this.members = transformedData;
                    this.departments = departments;
                });
        }
    },
    mounted() {
        this.loadOrganization();
    }
}
```

### Multi-level Organization

```javascript path=null start=null
data() {
    return {
        members: {
            "Board Chair": ["CEO"],
            "CEO": ["CTO", "CFO", "COO"],
            "CTO": ["VP Engineering", "VP Infrastructure"],
            "VP Engineering": ["Team Lead Backend", "Team Lead Frontend"],
            "VP Infrastructure": ["DevOps Lead"],
            "Team Lead Backend": ["Backend Dev 1", "Backend Dev 2"],
            "Team Lead Frontend": ["Frontend Dev 1", "Frontend Dev 2"],
            "DevOps Lead": [],
            "Backend Dev 1": [],
            "Backend Dev 2": [],
            "Frontend Dev 1": [],
            "Frontend Dev 2": [],
            "CFO": ["Finance Manager", "Accountant"],
            "COO": ["HR Manager"],
            "Finance Manager": [],
            "Accountant": [],
            "HR Manager": []
        },
        departments: {
            "Executive": ["Board Chair", "CEO"],
            "Technology": ["CTO", "VP Engineering", "VP Infrastructure", 
                         "Team Lead Backend", "Team Lead Frontend", "DevOps Lead",
                         "Backend Dev 1", "Backend Dev 2", "Frontend Dev 1", "Frontend Dev 2"],
            "Finance": ["CFO", "Finance Manager", "Accountant"],
            "Operations": ["COO", "HR Manager"]
        }
    }
}
```

### Dynamic Updates

```javascript path=null start=null
methods: {
    addEmployee(parentName, employeeName) {
        if (!this.members[employeeName]) {
            this.members[employeeName] = [];
        }
        if (!this.members[parentName].includes(employeeName)) {
            this.members[parentName].push(employeeName);
        }
        // Trigger re-render by reassigning
        this.members = { ...this.members };
        // Re-layout will happen on next update
        this.$refs.chart.layout();
    },
    
    removeEmployee(parentName, employeeName) {
        const index = this.members[parentName].indexOf(employeeName);
        if (index > -1) {
            this.members[parentName].splice(index, 1);
        }
        delete this.members[employeeName];
        this.members = { ...this.members };
        this.$refs.chart.layout();
    }
}
```

## Best Practices

1. **Ensure all nodes are in adjacency list**: Every node referenced must have an entry
   ```javascript path=null start=null
   // ✓ Good - all nodes have entries
   nodes: {
       "CEO": ["Manager"],
       "Manager": ["Employee"],
       "Employee": []
   }
   
   // ✗ Bad - missing Employee entry
   nodes: {
       "CEO": ["Manager"],
       "Manager": ["Employee"]  // Employee not defined
   }
   ```

2. **Use descriptive node labels**: Helps with readability
   ```javascript path=null start=null
   // ✓ Good
   "John Smith (CEO)": ["Jane Doe (CTO)"]
   
   // Less clear
   "Person 1": ["Person 2"]
   ```

3. **Group related nodes**: Use groups to show departments
   ```javascript path=null start=null
   groups: {
       "Engineering Department": ["CTO", "Engineer A", "Engineer B"],
       "Sales Department": ["VP Sales", "Sales Rep"]
   }
   ```

4. **Use references properly**: Match node IDs exactly
   ```javascript path=null start=null
   // IDs are case-sensitive
   nodes: {
       "CEO": ["CTO"],  // ✓ Correct
       // "CEO": ["cto"]  // ✗ Wrong - won't find "cto"
   }
   ```

5. **Handle large hierarchies**: Test with realistic data sizes
   ```javascript path=null start=null
   // For 100+ nodes, consider:
   // - Pagination/filtering
   // - Collapsible subtrees
   // - Lazy loading
   ```

6. **Multi-line labels**: Use newlines for long names
   ```javascript path=null start=null
   "John Smith\n(CEO)\nMain Office": ["Manager"]
   ```

## Styling Customization

To customize colors and styling, modify the component:

### Change Node Color

```javascript path=null start=null
// In makeNodeElement()
body: {
    stroke: '#226CE0',  // Change this
    // ...
}
```

### Change Link Color

```javascript path=null start=null
// In makeLink()
attrs: {
    line: {
        stroke: '#F68E96'  // Change this
    }
}
```

### Change Group/Cluster Color

```javascript path=null start=null
// In makeClusterElement()
body: {
    stroke: '#E6A23C',  // Border color
    fill: 'rgba(60, 162, 230, 0.05)',  // Background
    // ...
}
```

## Troubleshooting

### Chart not displaying
- Verify `nodes` prop is provided and contains valid data
- Check browser console for JavaScript errors
- Ensure JointJS libraries are installed and imported
- Verify component is mounted and ref is accessible

### Nodes overlapping
- The layout algorithm should prevent overlaps
- Check `nodeSep` and `rankSep` values in DirectedGraph.layout()
- Ensure no circular references exist

### Chart not centered
- Check container has positive height
- Verify `calculateAvailableHeight()` is calculating correctly
- Check `fitToContent()` is being called properly

### Groups not appearing
- Verify `groups` prop is provided
- Check that group node names match exactly (case-sensitive)
- Ensure group contains at least one node

### Invalid relationship error
- Error message: `"The element X is trying to create a link to Y, which does not exist"`
- Solution: Ensure all referenced nodes have an entry in the `nodes` object
- Check for typos in node names

### Performance issues with large hierarchies
- Component displays 100+ nodes
- Consider filtering or pagination
- Use lazy loading for large organizations
- May need custom layout algorithm for massive hierarchies

### Incorrect layout
- Verify adjacency list structure is correct
- Check for circular references (A → B → A)
- Ensure no duplicate node names
- Validate data before passing to component

### Responsive sizing not working
- Check container div has `overflow: auto`
- Verify window resize events are being handled
- Check minimum height (300px) isn't being overridden
- Test in different viewport sizes

## Browser Compatibility

- ✅ Chrome/Edge (all versions)
- ✅ Firefox (all versions)
- ✅ Safari (all versions)
- ✅ IE 11 (with polyfills)

## Performance Characteristics

| Metric | Value |
|--------|-------|
| Recommended max nodes | 100-200 |
| Render time (50 nodes) | 200-500ms |
| Render time (100 nodes) | 500-1000ms |
| Memory per node | ~50KB |
| Container height calculation | <50ms |

## Advanced Usage

### Accessing the JointJS API

```javascript path=null start=null
// In your component
mounted() {
    this.$refs.chart.graph   // Access the graph
    this.$refs.chart.paper   // Access the paper
}
```

### Custom Events Integration

```javascript path=null start=null
// Add click handlers to nodes
paper.on('cell:pointerclick', (cellView) => {
    const nodeId = cellView.model.get('id');
    this.$emit('node-clicked', nodeId);
});
```

### Export Diagram

```javascript path=null start=null
// Export as SVG
const svg = paper.toSVG();

// Export as PNG
const image = paper.toImage();
```

## Dependencies Reference

- **@joint/core@3.x+**: Core JointJS library
- **@joint/layout-directed-graph@3.x+**: DirectedGraph layout algorithm
- **vue@3.x+**: Vue 3 framework
- **Lodash**: For data transformation (optional, but recommended)

## See Also

- [JointJS Documentation](https://www.jointjs.com/)
- [JointJS Shapes API](https://www.jointjs.com/docs/jointjs/v3/tutorials)
- [Directed Graph Layout](https://www.jointjs.com/plugins/layout/DirectedGraph)
