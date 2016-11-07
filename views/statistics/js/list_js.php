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
            cl("You activated " + node.data.key);
        },
        minExpandLevel: 1,
        selectionVisible: true,
        checkbox:  true,
        clickFolderMode: 1,
        activeVisible: true,
        nodeIcon: false,
        selectMode: 1,
        fx: { height: "toggle", duration: 300 },
        autoCollapse: true,
        autoFocus: true,
        children: [
            {title: "Usuários",
                noLink: true,
                expand: true,
                unselectable: true,
                hideCheckbox: true,
                children: [
                    {title: "<div> Status </div><p> logins / registros / banidos / excluídos </p>", href: "status"},
                    {title: "<div> Itens </div><p> criaram / editaram / apagaram / visualizaram /<br/>  baixaram</p>", href: "items"},
                    {title: "<div> Perfil </div><p> Pessoas que aderiram a um perfil </p>", href: "profile"},
                    {title: "<div> Categorias </div><p> criaram / editaram / apagaram / visualizaram <br/> / baixaram </p>"},
                    {title: "<div> Coleção </div><p> criaram / editaram / apagaram / visualizaram </p>"}
                ]
            },
            {title: "Itens",
                noLink: true,
                hideCheckbox: true,
                children: [
                    {title: "<div> Usuário </div><p> view / comentado / votado </p>"},
                    {title: "<div> Status </div><p> criados / editados / excluídos / view / favoritos / baixados</p>"},
                    {title: "<div> Coleção </div><p> número de itens por coleção </p>"}
                ]
            },
            {title: "Coleções", noLink: true, hideCheckbox: true},
            {title: "Comentários", noLink: true, hideCheckbox: true},
            {title: "Categorias", noLink: true, hideCheckbox: true},
            {title: "Tags", noLink: true, hideCheckbox: true},
            {title: "Importar / Exportar", noLink: true, hideCheckbox: true},
            {title: "Administração", noLink: true, hideCheckbox: true},
        ],
        onClick: function(node, event) {
            /*
            if(node.childList.length > 0) {
                cl(node.data.title);
                // cl(node);
                $('.chart-header .current-chart').text('Status do usuário');
            } else {
            }
            */
            var key = node.data.key;
            var parent = node.parent.data.title;
            var node_action = node.data.href;
            // cl("Pai: " + parent);
            if(parent == "Usuários") {
                if(node_action) {
                    cl("TO DO:" + node_action);
                } else {
                    cl("Set your href please");
                }
            }
            // cl(node.span);
            // cl(node.tree);
            // cl(node.data);
            cl("A chave é:" + key);

        },
        classNames: { checkbox: 'dynatree-radio'},

    });

</script>