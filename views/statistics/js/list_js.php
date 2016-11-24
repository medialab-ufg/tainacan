<script type="text/javascript">    
var TainacanChart = function() { };

TainacanChart.prototype.getMappedTitles = function() {
    return {
        add: 'Adicionados',
        edit: 'Editados',
        view: 'Visualizados',
        download: 'Baixados',
        delete: 'Deletados',
        login: 'Login',
        register: 'Registros',
        delete_user: 'Excluídos',
        administrator: 'Administrador',
        author: 'Autor',
        editor: 'Editor',
        subscriber: 'Assinante',
        contributor: 'Colaborador',
        access_oai_pmh: 'Acessos OAI-PMH',
        import_csv: 'Importação CSV',
        export_csv: 'Exportação CSV',
        import_tainacan: 'Importação Tainacan',
        export_tainacan: 'Exportação Tainacan'
    };
};

TainacanChart.prototype.displayFixedBase = function() {
    $("#charts-resume table tr.headers").html("<th class='curr-parent'> Status: </th>");
    $("#charts-resume table tr.content").html("<td class='curr-filter'> Usuários </td>");
};

TainacanChart.prototype.displayBaseAppend = function(title, value) {
    $("#charts-resume table tr.headers").append("<th>"+ title +"</th>");
    $("#charts-resume table tr.content").append("<td>"+ value +"</td>");
};

TainacanChart.prototype.getStatDesc = function(title, desc) {
    return title + "<p>"+ desc +"</p>";
};

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
        nextText: $(".stats-i18n .next-text").text(),
        prevText: $(".stats-i18n .prev-text").text(),
        showButtonPanel: false,
        showAnim: 'clip'
    });

    $('a.change-mode').on('click', function() {
        var selected_chart = $(this).attr('data-chart');
        var curr_img = $(this).html();

        $(".statChartType li").each(function(idx, elem){
           if( $(elem).attr('class') == selected_chart ) {
               $(elem).addClass('hide');
           } else {
               $(elem).removeClass('hide');
           }
        });

        $("#charts-container div").addClass('hide');
        $("div#" + selected_chart).removeClass('hide');

        $("#statChartType").html(curr_img);
         // Click again at current selected node to trigger chart drawing
        $('.dynatree-selected').click();
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

var tChart = new TainacanChart();
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
    onClick: function(node, event) {
        var parent = node.parent.data.title;
        var node_action = node.data.href;
        var chart_text = node.data.title;
        var chain = $('.temp-set').html(chart_text).text().replace(/\//gi, "");
        var split_title = chain.split(" ");
        $(".current-chart").text( split_title[0] + " / " + parent );
        if(node_action) {
            fetchData(parent, node_action);
        }
    },
    onPostInit: function(isReloading, isError) {
        $('.dynatree-radio').first().click();
    }
};

function statusChildren() {
    return [
        { title: tChart.getStatDesc("Status", "logins / registros / banidos / excluídos"), href: "status", addClass: 'hllog' },
        { title: "Itens <p> criaram / editaram / apagaram / <br/> visualizaram / baixaram</p>", href: "items" },
        { title: "Perfil <p> Pessoas que aderiram a um perfil </p>", href: "profile" },
        { title: "Categorias <p> criaram / editaram / apagaram / visualizaram </p>", href: "category" },
        { title: "Coleção <p> criaram / editaram / apagaram / visualizaram </p>", href: "collection" }
    ];
}

function itensChildren() {
    return [
        { title: "Usuário <p> view / comentado / votado </p>"},
        { title: "Status <p> ativos / rascunhos / lixeira / excluídos </p>"},
        { title: "Coleção <p> número de itens por coleção </p>"}
    ];
}

function collectionsChildren() {
    return [
        { title: "Status <p> criadas / editadas / excluídas / visualizadas / baixadas</p>", href: "collection"},
        { title: "Buscas Frequentes <p> ranking das buscas mais realizadas </p>"}
    ];
}

function commentsChildren() {
    return [{ title: "Status <p> adicionados / editados / excluídos / visualizados </p>", href: "comments" }];
}

function categoryChildren() {
    return [{ title: "Status <p> criados / editados / excluídos </p>", href: "category" }];
}

function tagsChildren() {
    return [{ title: "Status <p> adicionados / editados / excluídos </p>", href: 'tags' }];
}

function importsChildren() {
    return [{ title: "<p> Acessos OAI-PMH <br/> Importação / Exportação CSV <br/> Importação <br/>" +
    "Exportaçào formato Tainacan </p>", href: 'imports'}];
}

function getStatsTree() {
    return [
        { title: $('.stats-users').text(), noLink: true, expand: true, unselectable: true,
            hideCheckbox: true, children: statusChildren() },
        { title: $('.stats-items').text(), noLink: true, unselectable: true, hideCheckbox: true, children: itensChildren() },
        { title: $('.stats-collections').text(), noLink: true, hideCheckbox: true, children: collectionsChildren() },
        { title: $('.stats-comments').text(), noLink: true, hideCheckbox: true, children: commentsChildren() },
        { title: $('.stats-categories').text(), noLink: true, hideCheckbox: true, children: categoryChildren() },
        { title: $('.stats-tags').text(), noLink: true, hideCheckbox: true, children: tagsChildren()},
        { title: $('.stats-imports').text(), noLink: true, hideCheckbox: true, children: importsChildren() },
        { title: $('.stats-admin').text(), noLink: true, hideCheckbox: true}
    ];
}

function fetchData(parent, action) {
    var from = $("#from_period").val();
    var to = $("#to_period").val();

    $.ajax({
        url: $("#src").val() + '/controllers/log/log_controller.php', type: 'POST',
        data: { operation: 'user_events', parent: parent, event: action, from: from, to: to }
    }).done(function(r){
        var res_json = $.parseJSON(r);
        drawChart(action, res_json);
    });
}

function drawChart(title, data_obj) {
    if(data_obj.stat_object) {
        var basis = [ title, ' Qtd ', {role: 'style'} ];
        var chart_data = [basis];
        var dt = new google.visualization.DataTable();
        dt.addColumn('string', 'Topping');
        dt.addColumn('number', 'Slices');

        var chart = new TainacanChart();
        var color = data_obj.color || '#79a6ce';
        chart.displayFixedBase();

        for( event in data_obj.stat_object ) {
            obj_total = parseInt(data_obj.stat_object[event]);
            var curr_evt_title = chart.getMappedTitles()[event];
            chart_data.push([ curr_evt_title, obj_total, color ]);
            dt.addRow([ curr_evt_title, obj_total ]);
            chart.displayBaseAppend( curr_evt_title, obj_total);
        }

        var piechart_options = {title:'Qtd ' + title, width: 800, is3D: true };
        var piechart = new google.visualization.PieChart(document.getElementById('piechart_div'));
        piechart.draw(dt, piechart_options);

        var barchart_options = {title:'Barchart stats', width: 800, height:300, legend: 'none', color: '#01a0f'};
        var barchart = new google.visualization.BarChart(document.getElementById('barchart_div'));
        barchart.draw(dt, barchart_options);

        var data = google.visualization.arrayToDataTable( chart_data );
        var options = { colors: [color], legend: 'none' };
        var default_chart = new google.charts.Bar(document.getElementById('chart_div'));

        default_chart.draw(data, options);
    }
}

$('a.dl-pdf').click(function() {        
    getPDFStats();
});

function getPDFStats() {
    var curr_chart = $("#charts-container").clone();
    $("#pdf-chart .resume-content").html(curr_chart);

    var curr_res = $("#charts-resume").clone();
    $("#pdf-chart .resume-content").html(curr_res);

    var pdf = new jsPDF('p', 'pt', 'a4');
    var source = $('#pdf-chart')[0];

    specialElementHandlers = {
        // element with id of "bypass" - jQuery style selector
        '#bypassme': function (element, renderer) {
            // true = "handled elsewhere, bypass text extraction"
            return true
        }
    };
    margins = {
        top: 80,
        bottom: 60,
        left: 40,
        width: 800
    };
    // all coords and widths are in jsPDF instance's declared units
    // 'inches' in this case
    pdf.fromHTML(
      source, // HTML string or DOM elem ref.
      margins.left, // x coord
      margins.top, { // y coord
          'width': margins.width, // max width of content on PDF
          'elementHandlers': specialElementHandlers
      },
      function (dispose) {
          // dispose: object with X, Y of the last line add to the PDF
          //          this allow the insertion of new lines after html
          pdf.save('chart.pdf');
      }, margins);
}

$("#report_type_stat").dynatree(stats_dynatree_opts);
</script>