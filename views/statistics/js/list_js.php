<script type="text/javascript">
    google.charts.load('current', {'packages':['bar','corechart'], 'language':'pt_BR'});
    // google.charts.setOnLoadCallback(drawChart);
    $(function() {
        $(".period-config .input_date").datepicker({
            // dateFormat: 'dd/mm/yy',
            altFormat: 'dd/mm/yy',
            dateFormat: 'yy-mm-dd',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: '<?php i18n_str('Next ', true); ?>',
            prevText: '<?php i18n_str('Previous', true); ?>',
            showButtonPanel: false,
            showAnim: 'clip',
            onSelect: function(dateText, obj) {
                var input = $(obj).attr('id');
                cl("Got date: " + dateText + " from " + input);
            }
        });
    });

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
            var chart_text = node.data.title;
            var chain = $('.temp-set').html(chart_text).text().replace(/\//gi, "");
            var split_title = chain.split(" ");
            $(".current-chart").text( parent + " >> " + split_title[0] );

            getStatData(parent, node_action);
        }
    };

    function statusChildren() {
        return [
            { title: "Status <p> logins / registros / banidos / excluídos </p>", href: "status", addClass: 'hllog' },
            { title: "Itens <p> criaram / editaram / apagaram / <br/> visualizaram / baixaram</p>", href: "items" },
            { title: "Perfil <p> Pessoas que aderiram a um perfil </p>", href: "profile" },
            { title: "Categorias <p> criaram / editaram / apagaram / visualizaram </p>",href: "category" },
            { title: "Coleção <p> criaram / editaram / apagaram / visualizaram </p>", href: "collection" }
        ];
    }

    function itensChildren() {
        return [
            { title: "Usuário <p> view / comentado / votado </p>"},
            { title: "Status <p> criados / editados / excluídos / view / favoritos / baixados</p>"},
            { title: "Coleção <p> número de itens por coleção </p>"}
        ];
    }
    
    function collectionsChildren() {
        return [
            { title: "Status <p> criadas / editadas / excluídas / visualizadas / baixadas</p>"},
            { title: "Buscas Frequentes <p> ranking das buscas mais realizadas </p>"}
        ];
    }

    function commentsChildren() {
        return [{ title: "Status <p> adicionados / editados / excluídos / visualizados </p>" }];
    }

    function categoryChildren() {
        return [{ title: "Status <p> criados / editados / excluídos </p>" }];
    }

    function tagsChildren() {
        return [{ title: "Status <p> adicionados / editados / excluídos / visualizados </p>" }];
    }

    function importsChildren() {
        return [{ title: "<p> Acessos OAI-PHM <br/> Haversting OAI-PHM <br/> Backups <br/>" +
        "Restore <br/> Importação <br/> Exportação CSV <br/> Importação <br/> Exportaçào formato Tainacan </p>" }];
    }

    function getStatsTree() {
        return [
            { title: "<?php i18n_str('Users',true); ?>", noLink: true, expand: true, unselectable: true,
                hideCheckbox: true, children: statusChildren() },
            { title: "<?php i18n_str('Items',true); ?>", noLink: true, hideCheckbox: true, children: itensChildren() },
            { title: "<?php i18n_str('Collections',true); ?>", noLink: true, hideCheckbox: true, children: collectionsChildren() },
            { title: "<?php i18n_str('Comments',true); ?>", noLink: true, hideCheckbox: true, children: commentsChildren() },
            { title: "<?php i18n_str('Categories',true); ?>", noLink: true, hideCheckbox: true, children: categoryChildren() },
            { title: "<?php i18n_str('Tags',true); ?>", noLink: true, hideCheckbox: true, children: tagsChildren()},
            { title: "<?php i18n_str('Import / Export',true); ?>", noLink: true, hideCheckbox: true, children: importsChildren() },
            { title: "<?php i18n_str('Administration',true); ?>", noLink: true, hideCheckbox: true},
        ]
    }

    function getStatData(parent_name, node_action) {
        if(node_action) {
            switch (parent_name) {
                case "<?php i18n_str('Users', true); ?>":
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
        var from = $("#from_period").val();
        var to = $("#to_period").val();

        if(from && to) {
            cl("Fetch between " + from + " and " + to);
        } else { }

        $.ajax({
            url: base_path + '/controllers/log/log_controller.php',
            data: { operation: 'user_events', event: action }
        }).done(function(r){
            var res_json = $.parseJSON(r);
            cl(res_json);
            drawChart(action, res_json);
        })
    }
    mappd_titles = { add: 'Adicionados', edit: 'Editados', view: 'Visualizados', download: 'Baixados', delete: 'Deletados',
        login: 'Login', register: 'Registros', delete_user: 'Excluídos',
        administrator: 'Administrador', author: 'Autor', editor: 'Editor', subscriber: 'Assinante', contributor: 'Colaborador' };

    function drawChart(title, data_obj) {
        if(data_obj.stat_object) {
            var basis = [ title, 'qtd ', {role: 'style'} ];
            var chart_data = [basis];

            for( event in data_obj.stat_object ) {
                obj_total = parseInt(data_obj.stat_object[event]);
                cl(event);
                chart_data.push( [ mappd_titles[event], obj_total, 'red'] );
                displayBaseAppend(mappd_titles[event], obj_total);
            }
            var color = data_obj.color || '#79a6ce';

            var data = google.visualization.arrayToDataTable( chart_data );
            var options = { colors: [color] };
            var chart = new google.charts.Bar(document.getElementById('chart_div'));

            chart.draw(data, options);
        }
    }

    function displayBaseAppend(title, value) {
        $("#charts-resume table tr.headers").append("<th>"+ title +"</th>");
        $("#charts-resume table tr.content").append("<td>"+ value +"</td>");
    }

    $("#report_type_stat").dynatree(stats_dynatree_opts);
</script>