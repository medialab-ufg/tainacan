<script type="text/javascript">
    google.charts.load('current', {'packages':['bar','corechart'], 'language':'pt_BR'});

    // google.charts.setOnLoadCallback(drawChart);

    function drawChart(data_obj) {
        var login_qry = '<?php print_r( Log::getUserEvents('user_status', 'login') ); ?>';
        var register_qry = '<?php print_r( Log::getUserEvents('user_status', 'register')); ?>';
        var delete_qry = '<?php print_r( Log::getUserEvents('user_status', 'delete_user')); ?>';
        var parsd_login = $.parseJSON(login_qry);
        // cl(parsd_login);

        var parsd_reg = $.parseJSON(register_qry);
        // cl(parsd_reg);

        var parsd_del = $.parseJSON(delete_qry);
        // cl(parsd_del);

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

        var total_logins = ['Login', logins, 'color: #0c698b' ];
        var total_registers = ['Registros', registers, 'color: #b87333' ];
        var total_del = ['Excluídos', deletes, 'silver' ];

        var chart_data = [
            ['Status de usuários', 'qtd ', { role: 'style' }],
            total_del,
            total_logins,
            total_registers
        ];
        var data = google.visualization.arrayToDataTable( chart_data );
        var options = { colors: ['#0c698b'] };

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

    var stats_dynatree_opts = {
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
        classNames: { checkbox: 'dynatree-radio'},
        children: getStatsTree(),
        onActivate: function(node) {
            cl("Nó ativado: " + node.data.key);
        },
        onClick: function(node, event) {
            var parent = node.parent.data.title;
            var node_action = node.data.href;
            getStatData(parent, node_action);
        }
    };

    function getStatsTree() {
        return [
            { title: "Usuários",
                noLink: true,
                expand: true,
                unselectable: true,
                hideCheckbox: true,
                children: [
                    {title: "<div> Status </div><p> logins / registros / banidos / excluídos </p>", href: "status"},
                    {title: "<div> Itens </div><p> criaram / editaram / apagaram / visualizaram /<br/>  baixaram</p>", href: "items"},
                    {title: "<div> Perfil </div><p> Pessoas que aderiram a um perfil </p>", href: "profile"},
                    {title: "<div> Categorias </div><p> criaram / editaram / apagaram / visualizaram <br/> / baixaram </p>"},
                    {title: "<div> Coleção </div><p> criaram / editaram / apagaram / visualizaram </p>", href: "collection "}
                ]
            },
            { title: "Itens",
                noLink: true,
                hideCheckbox: true,
                children: [
                    {title: "<div> Usuário </div><p> view / comentado / votado </p>"},
                    {title: "<div> Status </div><p> criados / editados / excluídos / view / favoritos / baixados</p>"},
                    {title: "<div> Coleção </div><p> número de itens por coleção </p>"}
                ]
            },
            { title: "Coleções", noLink: true, hideCheckbox: true},
            { title: "Comentários", noLink: true, hideCheckbox: true},
            { title: "Categorias", noLink: true, hideCheckbox: true},
            { title: "Tags", noLink: true, hideCheckbox: true},
            { title: "Importar / Exportar", noLink: true, hideCheckbox: true},
            { title: "Administração", noLink: true, hideCheckbox: true},
        ]
    }

    function getStatData(parent_name, node_action) {
        if(node_action) {
            switch (parent_name) {
                case "Usuários":
                    cl("Query: " + "user_" + node_action);
                    fetchData(node_action);
                    break;
                case "Itens":
                    cl("getting itens data!");
                    break;
                default:
                    cl("Not defined yet!");
            } // switch
        } // if
    }
    
    function fetchData(action) {
        var base_path = $("#src").val();
        $.ajax({
            url: base_path + '/controllers/log/log_controller.php',
            data: {
                operation: 'user_events',
                event: action
            }
        }).done(function(r){
            cl("resposta normal");
            cl(r);
            var res_json = $.parseJSON(r);
            cl(res_json);
            drawChart();
        })
    }

    $("#report_type_stat").dynatree(stats_dynatree_opts);

    $(function() {
        $(".period-config .input_date").datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: 'Próximo',
            prevText: 'Anterior',
            showButtonPanel: false,
            showAnim: 'clip'
        });
    });

</script>