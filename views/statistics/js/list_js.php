<script type="text/javascript">     
    google.charts.load('current', {'packages':['bar','corechart'], 'language':'pt_BR'});
    // google.charts.setOnLoadCallback(drawChart);
    
    var TainacanChart = function() { };
    TainacanChart.prototype.getMappedTitles = function() {
        return {
            add: '<?php _t("Added",1); ?>',
            edit: '<?php _t("Edited",1); ?>',
            view: '<?php _t("Viewed",1); ?>',
            download: '<?php _t("Downloaded",1); ?>',
            delete: '<?php _t("Deleted",1); ?>',
            comment: '<?php _t("Commented",1); ?>',
            vote: '<?php _t("Voted",1); ?>',
            login: '<?php _t("Login",1); ?>',
            register: '<?php _t("Registers",1); ?>',
            delete_user: '<?php _t("Excluded",1); ?>',
            administrator: '<?php _t("Administrator",1); ?>',
            author: '<?php _t("Author",1); ?>',
            editor: '<?php _t("Editor",1); ?>',
            subscriber: '<?php _t("Subscriber",1); ?>',
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

    TainacanChart.prototype.createCsvFile = function(csvData) {
        var csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "Evento, Qtd\n";
        csvData.forEach(function(infoArray, index) {
            var dataString = infoArray.join(",");
            csvContent += index < csvData.length ? dataString + "\n" : dataString;
        });
        var encodedURI = encodeURI(csvContent);

        $('a.dl-csv').attr('href', encodedURI);
        $('a.dl-csv').attr('download', 'exported-chart.csv');
    };
    
    $(function() {
        $(".period-config .input_date").datepicker({
            //altFormat: 'dd/mm/yy',
            dateFormat: 'yy-mm-dd',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: $(".stats-i18n .next-text").text(),
            prevText: $(".stats-i18n .prev-text").text(),
            showButtonPanel: false,
            showAnim: 'clip',
            onSelect: function(dateText, obj) {
                cl("You chose " + dateText);
                // cl(obj);
                var which = obj.id.toString().replace("_period", "");
                cl(which);
                $("#pdf-chart .period ." + which).text(dateText);
            }
        });

        $('a.change-mode').on('click', function() {
            var selected_chart = $(this).attr('data-chart');
            var curr_img = $(this).html();
            var chart_type = selected_chart.replace('chart_div', '');
            
            $('.selected_chart_type').val(chart_type);
            // cl("Atualizado para: > " + $('.selected_chart_type').val() );

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
            { title: "Usuário <p> view / comentado / votado </p>", href: "user" },
            { title: "Status <p> ativos / rascunhos / lixeira / excluídos </p>", href: "status" },
            { title: "Coleção <p> número de itens por coleção </p>", href: "collection" }
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
        return [{ title: "<p> Acessos OAI-PMH <br/> Importação / Exportação CSV <br/> Importação formato Tainacan <br/>" +
        "Exportação formato Tainacan </p>", href: 'imports'}];
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
            // { title: $('.stats-admin').text(), noLink: true, hideCheckbox: true}
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
            var chart = $('.selected_chart_type').val();
            drawChart(chart, action, res_json);
        });
    }

    function drawChart(chart_type, title, data_obj) {
        if(data_obj.stat_object) {
            // all variables used along the function
            var basis = [ title, ' Qtd ', {role: 'style'} ];
            var chart_data = [basis];
            var commom_data = new google.visualization.DataTable();
            commom_data.addColumn('string', title);
            commom_data.addColumn('number', 'Qtd');

            var chart = new TainacanChart();
            var color = data_obj.color || '#79a6ce';
            var csvData = [];

            chart.displayFixedBase();
            for( event in data_obj.stat_object ) {
                var obj_total = parseInt(data_obj.stat_object[event]);
                var curr_evt_title = chart.getMappedTitles()[event];
                var curr_tupple = [ curr_evt_title, obj_total ];
                chart_data.push([ curr_tupple[0], curr_tupple[1], color ]);
                commom_data.addRow(curr_tupple);
                csvData.push( curr_tupple );
                chart.displayBaseAppend(curr_tupple[0], curr_tupple[1]);
            }

            // Generate CSV file for current chart
            chart.createCsvFile(csvData);

            // Google Charts objects
            if( chart_type == 'pie' ) {
                var piechart = new google.visualization.PieChart(document.getElementById('piechart_div'));
                var piechart_options = { title:'Qtd ' + title, is3D: true,
                    colors: ['#F09B35','#8DA9BF','#F2C38D','#E6AC03', '#D94308', '#013453'] }; // '#0F4F8D','#2B85C1' tons de azul

                google.visualization.events.addListener(piechart, 'ready', function() {
                    var chart_png = piechart.getImageURI();
                    $('.dynamic-chart-img').removeClass('hide').attr('src', chart_png );
                });

                piechart.draw(commom_data, piechart_options);
            } else if ( chart_type == 'bar' ) {
                var barchart = new google.visualization.BarChart(document.getElementById('barchart_div'));
                var barchart_options = {title:'Barchart stats', width: 800, height:300, legend: 'none', colors: ['#013453', 'orange']};

                google.visualization.events.addListener(barchart, 'ready', function() {
                    var chart_png = barchart.getImageURI();
                    $('.dynamic-chart-img').removeClass('hide').attr('src', chart_png );
                });

                barchart.draw(commom_data, barchart_options);
            } else if( chart_type == 'default' ) {
                var default_chart = new google.charts.Bar(document.getElementById('defaultchart_div'));
                var data = google.visualization.arrayToDataTable( chart_data );
                
                var default_options = { colors: [color], legend: 'none' };
                default_chart.draw(data, default_options);
            }
        }
    }

    $('a.dl-pdf').click(function() {
        drawStatPDF();
    });

    function drawStatPDF() {
        var curr_type = $('.selected_chart_type').val();
        var d = new Date();
        var line_dims = { startX: 20, startY: 40, length: 550, thickness: 0.5 };
        var week_day = " (" + (getWeekDay()[d.getDay()]).toString().toLowerCase() + ")";
        var formated_date = d.getDate() + '/' + (d.getMonth() + 1) + '/' + d.getFullYear() + week_day;

        var margins = { top: 80, bottom: 60, left: 25, width: 180 };
        var image = { width: 180, height: 40 },
             logo = { width: 180, height: 40 }; // 30 & 8 or 40 & 10.92

        var pdf = new jsPDF('p', 'pt');
        
        var chart_png = $('.chart-img img.dynamic-chart-img').attr('src');

        var chartImg = new Image();
        chartImg.src = chart_png;
        chartImg.onload = function () {
            var chart_settings = { 
                width: (chartImg.naturalWidth * 0.7), 
                height: (chartImg.naturalHeight * 0.7)
            };
            pdf.addImage(chartImg, 'PNG', margins.left, margins.top, chart_settings.width, chart_settings.height);
        };

        var logo = $('img.tainacan-logo-cor').attr('src');
        var projectLogo = new Image();
        projectLogo.src = logo;
        projectLogo.onload = function () {
            var logo_settings = { 
                width: (projectLogo.naturalWidth * 0.1), 
                height: (projectLogo.naturalHeight * 0.1)
            };
            pdf.addImage(projectLogo, 'PNG', line_dims.startX, (line_dims.startX - 11), logo_settings.width, logo_settings.height); 
        };
        
        pdf.rect(line_dims.startX, line_dims.startY, line_dims.length, line_dims.thickness, 'F');

        var consultDate = "Consultado em: " + formated_date;
        pdf.setFontSize(14);
        pdf.setFontType('bold');
        pdf.text('Estatísticas do Repositório', 390, (line_dims.startX) );

        pdf.setFontSize(9);
        pdf.setTextColor(100);
        pdf.setFontType('normal');
        pdf.text(consultDate, 410, line_dims.startX + 12);

        pdf.setTextColor(100);
         // content, xPos, yPos
        pdf.fromHTML('<strong>Pesquisa: </strong> Coleções / Criadas', line_dims.startX, (line_dims.startY - 3) );
        pdf.fromHTML('<strong>Período Consultado: </strong> de 15 a 21/09/2016', 360, (line_dims.startY - 3) );
        pdf.rect(line_dims.startX, line_dims.startY + 20, line_dims.length, line_dims.thickness, 'F');

        var resume_data = pdf.autoTableHtmlToJson( $('#charts-resume table').get(0) );
        cl(resume_data);
        
        var p = 300;
        var autoTable_opts = {
            theme: 'plain', margin: { top: p }, startY: p,
        };

        pdf.autoTable( resume_data.columns, resume_data.data, autoTable_opts);

        pdf.fromHTML( $('#user_details').get(0), line_dims.startX, pdf.autoTableEndPosY() ); // 150

        var timeStamp = d.getMilliseconds();
        var chart_name = curr_type + '_chart_' + timeStamp + '.pdf';
        pdf.save( chart_name );
    }

    function getWeekDay() {
        return ['Domingo','Segunda-feira','Terça-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sábado'];
    }

    $("#report_type_stat").dynatree(stats_dynatree_opts);
</script>
