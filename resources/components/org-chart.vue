<template>
    <div class="canvas" ref="canvas"></div>
</template>

<script setup>
import {ref, onMounted} from 'vue';
import {dia, shapes} from '@joint/core';
import {DirectedGraph} from '@joint/layout-directed-graph';

const canvas = ref(null);
const graph = new dia.Graph({}, {cellNamespace: shapes});

// Move paper creation inside onMounted so we can set the element
let paper, nodes;

function layout(){
    let cells = adjacencyListToCells(nodes);
    graph.resetCells(cells);
    DirectedGraph.layout(graph, {
        setLinkVertices: true,
        marginX: 5,
        marginY: 5
    });
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
}

function adjacencyListToCells(adjacencyList){
    const elements = [], links = [];
    Object.keys(adjacencyList).forEach((parentElementLabel) => {
        elements.push(makeElement(parentElementLabel));
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

function makeElement(label){
    const maxLineLength = label.split('\n').reduce((max, l) => {
        return Math.max(l.length, max);
    }, 0);

    const letterSize = 12;
    const width = 2 * (letterSize * (0.6 * maxLineLength + 1));
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

onMounted(() => {
    paper = new dia.Paper({
        el: canvas.value,
        model: graph,
        width: '100%',
        height: 500,
        background: {
            color: '#F8F9FA',
        },
        frozen: true,
        async: true,
        interactive: false,
        cellViewNamespace: shapes
    });

    paper.unfreeze();
});

defineExpose({
    draw(members){
        nodes = members;
        layout();
    }
});
</script>

<style>
.canvas{
    width: 100%;
    height: 500px;
}

.canvas :deep(.joint-paper){
    border: 1px solid #a0a0a0;
}
</style>