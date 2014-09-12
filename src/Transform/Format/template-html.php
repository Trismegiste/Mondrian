<!DOCTYPE html>
<meta charset="utf-8">
<style>

    .link {
        fill: none;
        stroke: #666;
        stroke-width: 1.5px;
    }

    .nostroke {
        fill: currentColor;
        stroke: none;
    }

    text {
        font: 10px sans-serif;
        pointer-events: none;
        text-shadow: 0 1px 0 #fff, 1px 0 0 #fff, 0 -1px 0 #fff, -1px 0 0 #fff;
    }

</style>
<body>   
    <svg width="100%" height="600">
        <defs>
            <g id="class-symbol"><circle r="8" class="nostroke"/></g>
            <g id="interface-symbol"><polygon points="-8,-8  8,-8  0,8" class="nostroke"/></g>
            <g id="method-symbol"><polygon points="-8,8  8,8  0,-8" class="nostroke"/></g>
            <g id="param-symbol"><polygon points="-8,0  0,8  8,0  0,-8" class="nostroke"/></g>
            <g id="impl-symbol"><polygon points="-8,8  8,8  8,-8  -8,-8" class="nostroke"/></g>
            <g id="trait-symbol"><polygon points="0,-9 -9,-2 -6,10 6,10 9,-2" class="nostroke"/></g>
        </defs>
    </svg>
    <script>
        
        <?php echo file_get_contents(__DIR__ . '/d3.min.js'); ?>

        var graph = <?php echo parent::export() ?>;

        var force = d3.layout.force()
                .nodes(d3.values(graph.nodes))
                .links(graph.links)
                .size([1000, 600])
                .linkDistance(60)
                .charge(-300)
                .on("tick", tick)
                .start();

        var svg = d3.select("svg");

        // Per-type markers, as they don't inherit styles.
        svg.append("defs")
                .append("marker")
                .attr("id", "edge")
                .attr("viewBox", "0 -5 10 10")
                .attr("refX", 15)
                .attr("refY", -1.5)
                .attr("markerWidth", 6)
                .attr("markerHeight", 6)
                .attr("orient", "auto")
                .append("path")
                .attr("d", "M0,-5L10,0L0,5");

        var path = svg.append("g").selectAll("path")
                .data(force.links())
                .enter().append("path")
                .attr("class", "link")
                .attr("marker-end", "url(#edge)");

        var shape = svg.append("g").selectAll("use")
                .data(force.nodes())
                .enter().append("use")
                .attr("xlink:href", function(d) { return "#" + d.type + "-symbol" })
                .attr('style', function(d) {
                    var color = d.color;
                    if (/[\.\d]+,[\.\d]+,[\.\d]+/.test(d.color)) {
                        color = d.color.split(",");  
                        color = 'hsl(' + (360 * color[0]) + ', 100%, 50%)';
                    }
                    return 'color:' + color
                })
                .call(force.drag);

        var text = svg.append("g").selectAll("text")
                .data(force.nodes())
                .enter().append("text")
                .attr("x", 8)
                .attr("y", ".31em")
                .text(function(d) { return d.name; });

        // Use elliptical arc path segments to doubly-encode directionality.
        function tick() {
            path.attr("d", linkArc);
            shape.attr("transform", transform);
            text.attr("transform", transform);
        }

        function linkArc(d) {
            var dx = d.target.x - d.source.x,
                    dy = d.target.y - d.source.y,
                    dr = Math.sqrt(dx * dx + dy * dy);
            return "M" + d.source.x + "," + d.source.y + "A" + dr + "," + dr + " 0 0,1 " + d.target.x + "," + d.target.y;
        }

        function transform(d) {
            return "translate(" + d.x + "," + d.y + ")";
        }

    </script>
</body>
</html>
