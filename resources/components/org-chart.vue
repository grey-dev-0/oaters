<template>
    <div ref="container" style="overflow:auto">
        <div ref="canvas"></div>
    </div>
</template>

<script setup>
import {ref, onMounted} from 'vue';
import {dia, shapes} from '@joint/core';
import {DirectedGraph} from '@joint/layout-directed-graph';

const canvas = ref(null);
const container = ref(null);
const graph = new dia.Graph({}, {cellNamespace: shapes});
let paper;

const props = defineProps({
    nodes: {
        type: Object,
        required: true
    },
    groups: {
        type: Object,
        default: () => ({})
    }
});

function layout(){
    let cells = adjacencyListToCells(props.nodes);
    const clusterElements = [];
    const clusterMap = {};
    if(props.groups && Object.keys(props.groups).length > 0){
        Object.entries(props.groups).forEach(([cluster, nodes]) => {
            if(nodes && nodes.length > 0){
                const clusterElement = makeClusterElement(cluster);
                clusterElements.push(clusterElement);
                clusterMap[cluster] = clusterElement;
            }
        });
        cells = [...clusterElements, ...cells];
        graph.resetCells(cells);
        Object.entries(props.groups).forEach(([cluster, nodes]) => {
            if(!clusterMap[cluster]) return;
            const clusterElement = clusterMap[cluster];
            const nodeElements = [];
            nodes.forEach(nodeName => {
                const element = graph.getCell(nodeName);
                if(element){
                    nodeElements.push(element);
                    clusterElement.embed(element);
                }
            });
            DirectedGraph.layout(graph, {nodeSep: 32, rankSep: 24});
            if(nodeElements.length > 0){
                const nodesBBox = graph.getCellsBBox(nodeElements);
                if(nodesBBox){
                    clusterElement.attr('label/refX', 0 - nodesBBox.width / 2);
                    clusterElement.attr('label/refY', 0 - nodesBBox.height / 2 - 25);
                }
            }
        });
    } else{
        graph.resetCells(cells);
        DirectedGraph.layout(graph);
    }

    centerGraph();
}

function centerGraph(){
    const graphBBox = graph.getBBox();
    const paperSize = paper.getComputedSize();

    if(graphBBox){
        const offsetX = (paperSize.width - graphBBox.width) / 2 - graphBBox.x;
        const offsetY = (paperSize.height - graphBBox.height) / 2 - graphBBox.y;
        graph.translate(offsetX, offsetY);
    }
    setTimeout(() => paper.fitToContent({minHeight: container.value.style.height, minWidth: '100%'}), 100);
}

function adjacencyListToCells(adjacencyList){
    const elements = [], links = [];
    Object.keys(adjacencyList).forEach((parentElementLabel) => {
        elements.push(makeNodeElement(parentElementLabel));
        const edges = adjacencyList[parentElementLabel] || [];
        edges.forEach((childElementLabel) => {
            if(!adjacencyList[childElementLabel]){
                throw new Error(`The element "${parentElementLabel}" is trying to create a link to "${childElementLabel}", which does not exist.`)
            }
            links.push(makeLink(parentElementLabel, childElementLabel));
        });
    });
    return elements.concat(links);
}

function makeLink(parentElementLabel, childElementLabel){
    return new shapes.standard.Link({
        source: {id: parentElementLabel, anchor: {name: 'bottom'}},
        target: {id: childElementLabel, anchor: {name: 'top'}},
        connector: {name: 'straight', args: {cornerType: 'line', cornerRadius: 0}},
        router: {name: 'rightAngle'},
        attrs: {
            line: {
                stroke: '#F68E96'
            }
        },
    });
}

function makeNodeElement(label){
    const maxLineLength = label.split('\n').reduce((max, l) => {
        return Math.max(l.length, max);
    }, 0);
    const letterSize = 12;
    const width = 2 * (letterSize * (0.3 * maxLineLength + 1));
    const height = 2 * ((label.split('\n').length + 1) * letterSize);

    return new shapes.standard.Rectangle({
        id: label,
        size: {width: width, height: height},
        attrs: {
            body: {
                stroke: '#226CE0',
                width: width,
                height: height,
                rx: 2,
                ry: 2
            },
            label: {
                text: label,
                fontSize: letterSize,
                fontFamily: 'monospace',
                fill: '#131E29',
            }
        }
    });
}

function makeClusterElement(label){
    return new shapes.standard.Rectangle({
        id: `group-${label}`,
        attrs: {
            body: {
                stroke: '#E6A23C',
                strokeWidth: 2,
                strokeDasharray: '5,3',
                fill: 'rgba(60, 162, 230, 0.05)',
                rx: 8,
                ry: 8
            },
            label: {
                text: label,
                fontSize: 14,
                fontWeight: 'bold',
                fontFamily: 'Arial, sans-serif',
                fill: '#001840',
                textAnchor: 'start',
                textVerticalAnchor: 'middle'
            }
        },
        markup: [
            {
                tagName: 'rect',
                selector: 'body'
            },
            {
                tagName: 'text',
                selector: 'label'
            }
        ]
    });
}

function calculateAvailableHeight(){
    if(!container.value) return;
    const containerTop = container.value.getBoundingClientRect().top;
    const availableHeight = window.innerHeight - containerTop - 20;
    container.value.style.height = `${Math.max(availableHeight, 300)}px`;
}

onMounted(() => {
    calculateAvailableHeight();
    paper = new dia.Paper({
        el: canvas.value,
        model: graph,
        width: '100%',
        height: container.value.style.height,
        background: {
            color: '#F8F9FA',
        },
        frozen: true,
        async: true,
        interactive: false,
        cellViewNamespace: shapes,
        validateEmbedding: function(childView, parentView){
            return parentView.model.id.toString().startsWith('group-') &&
                !childView.model.isLink();
        }
    });

    paper.unfreeze();
    layout();
});
</script>