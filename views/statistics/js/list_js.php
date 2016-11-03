<script type="text/javascript">
    google.charts.load('current', {'packages':['bar','corechart'], 'language':'pt_BR'});

    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var login_qry = '<?php print_r( Log::getUserEvents('login')); ?>';
        var register_qry = '<?php print_r( Log::getUserEvents('register')); ?>';
        var delete_qry = '<?php print_r( Log::getUserEvents('delete_user')); ?>';
        var parsd_login = $.parseJSON(login_qry);
        cl(parsd_login);

        var parsd_reg = $.parseJSON(register_qry);
        cl(parsd_reg);

        var parsd_del = $.parseJSON(delete_qry);
        cl(parsd_del);

        var logins, registers, deletes;
        $(parsd_login).each(function (idx, val) {
            if(idx == 0) {
                logins = val.total_login;
            }
        });
        $(parsd_reg).each(function (idx, val) {
            if(idx == 0) {
                registers = val.total_login;
            }
        });
        $(parsd_del).each(function (idx, val) {
            if(idx == 0) {
                deletes = val.total_login;
            }
        });

        var total_logins = ['Login', logins, 'color: #0c698b' ]; // CSS-style declaration
        var total_registers = ['Registros', registers, 'color: #b87333' ]; //RGB value
        var total_del = ['Excluídos', deletes, 'silver' ]; // English color name

        var data = google.visualization.arrayToDataTable([
            ['Status de usuários', 'qtd ', { role: 'style' }],
            total_del,
            total_logins,
            total_registers,
            ['Banidos', 3, 'silver'],            // English color name
        ]);
        var options = { colors: ['#0c698b'] }; // // width: 900

        // var chart = new google.visualization.BarChart (document.getElementById('chart_div'));
        var chart = new google.charts.Bar(document.getElementById('chart_div'));
        chart.draw(data, options);
    }

    $("#statistics-config").accordion({
        collapsible: true,
        active: 1,
        header: "label",
        animate: 200,
        heightStyle: "content",
        icons: true
    });

    $("#report_type_stat").dynatree({
        onActivate: function(node) {
            // A DynaTreeNode object is passed to the activation handler
            // Note: we also get this event, if persistence is on, and the page is reloaded.
            alert("You activated " + node.data.title);
        },
        checkbox: true,
        children: [ // Pass an array of nodes.
            {title: "<h4>Item 1</h4><p>oi</p>"},
            {title: "Folder 2", isFolder: true,
                children: [
                    {title: "Sub-item 2.1"},
                    {title: "Sub-item 2.2"}
                ]
            },
            {title: "Item 3"}
        ],
        classNames: { checkbox: 'dynatree-radio'}
    });
    /*
    $("#report_type_stat").dynatree({
        // selectionVisible: true, // Make sure, selected nodes are visible (expanded).
        // checkbox: true,
        persist: true,
        children: [ // Pass an array of nodes.
            {title: "Item 1"},
            {title: "Folder 2", isFolder: true,
                children: [
                    {title: "Sub-item 2.1"},
                    {title: "Sub-item 2.2"}
                ]
            },
            {title: "Item 3"}
        ],

        initAjax: {
            url: src + '/controllers/collection/collection_controller.php',
            data: {
                collection_id: $("#collection_id").val(),
                operation: 'initDynatree'
            },
            addActiveKey: true
        },

        onClick: function(node, event) {
            cl(node);
            cl(event);
        },
        onActivate: function (dtnode) {
            alert("You activated " + dtnode.data.title);
        }
    });
    */
</script>