var labelType, useGradients, nativeTextSupport, animate;

(function () {
    var ua = navigator.userAgent,
            iStuff = ua.match(/iPhone/i) || ua.match(/iPad/i),
            typeOfCanvas = typeof HTMLCanvasElement,
            nativeCanvasSupport = (typeOfCanvas == 'object' || typeOfCanvas == 'function'),
            textSupport = nativeCanvasSupport
            && (typeof document.createElement('canvas').getContext('2d').fillText == 'function');
    //I'm setting this based on the fact that ExCanvas provides text support for IE
    //and that as of today iPhone/iPad current text support is lame
    labelType = (!nativeCanvasSupport || (textSupport && !iStuff)) ? 'Native' : 'HTML';
    nativeTextSupport = labelType == 'Native';
    useGradients = nativeCanvasSupport;
    animate = !(iStuff || !nativeCanvasSupport);
})();

var Log = {
    elem: false,
    write: function (text) {
        if (!this.elem)
            this.elem = document.getElementById('log');
        this.elem.innerHTML = text;
        this.elem.style.left = (500 - this.elem.offsetWidth / 2) + 'px';
    }
};


function init() {
    //init data
    var json = "";
    $.ajax({
        type: "GET",
        url: $('#src').val()+'/controllers/collection/collection_controller.php',
        data: {
                collection_id: $("#collection_id").val(),
                operation: 'initGeneralJit'
            }
    }).done(function (result) {
        json = jQuery.parseJSON(result);
        var tamanhoTelaW = $(window).width();
        var hypertree = document.getElementById('hypertree');
        var w = (tamanhoTelaW / 2) - 100, h = (tamanhoTelaW / 2) - 100;
        //var w = hypertree.offsetWidth - 50, h = hypertree.offsetHeight - 50;

        //init Hypertree
        var ht = new $jit.Hypertree({
            
            //id of the visualization container
            injectInto: 'hypertree',
            //canvas width and height
            width: w,
            height: h,
            //Change node and edge styles such as
            //color, width and dimensions.
            Node: {
                dim: 9,
                color: "#f00"
            },
            Edge: {
                lineWidth: 2,
                color: "#088"
            },
            onBeforeCompute: function (node) {
                Log.write("centering");
            },
            //Attach event handlers and add text to the
            //labels. This method is only triggered on label
            //creation
            onCreateLabel: function (domElement, node) {
                domElement.innerHTML = node.name;
                $jit.util.addEvent(domElement, 'click', function () {
                    ht.onClick(node.id, {
                        onComplete: function () {
                            ht.controller.onComplete();
                        }
                    });
                });
            },
            //Change node styles when labels are placed
            //or moved.
            onPlaceLabel: function (domElement, node) {
                var style = domElement.style;
                style.display = '';
                style.cursor = 'pointer';
                if (node._depth <= 1) {
                    style.fontSize = "0.8em";
                    style.color = "#ddd";

                } else if (node._depth == 2) {
                    style.fontSize = "0.7em";
                    style.color = "#555";

                } else {
                    style.display = 'none';
                }

                var left = parseInt(style.left);
                var w = domElement.offsetWidth;
                style.left = (left - w / 2) + 'px';
            },
            onComplete: function () {
                Log.write("done");

                //Build the right column relations list.
                //This is done by collecting the information (stored in the data property) 
                //for all the nodes adjacent to the centered node.
                var node = ht.graph.getClosestNodeToOrigin("current");
                var html = "<h4>" + node.name + "</h4><b>Connections:</b>";
                html += "<ul>";
                node.eachAdjacency(function (adj) {
                    var child = adj.nodeTo;
                    if (child.data) {
                        var rel = (child.data.band == node.name) ? child.data.relation : node.data.relation;
                        html += "<li>" + child.name + " " + "<div class=\"relation\">(relation: " + rel + ")</div></li>";
                    }
                });
                html += "</ul>";
                $jit.id('inner-details').innerHTML = html;
            },
            Events: {
                enable: true,
                onClick: function (node, eventInfo, e) {
                    if (typeof node.id != "undefined")
                    {
                       list_all_objects(node.id, $("#collection_id").val(), $('#collection_single_ordenation').val());
                    }
                }
            }
        });

        //load JSON data.
        ht.loadJSON(json);
        //compute positions and plot.
        ht.refresh();
        //end
        ht.controller.onComplete();

    });
}
