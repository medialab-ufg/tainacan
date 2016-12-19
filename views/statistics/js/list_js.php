<script type="text/javascript">
    google.charts.load('current', {'packages':['bar','corechart']});
    // google.charts.setOnLoadCallback(drawChart);
    
    var TainacanChart = function(){};
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
            contributor: '<?php _t("Colaborator",1); ?>',
            access_oai_pmh: '<?php _t("OAI-PMH Accesses",1); ?>',
            import_csv: '<?php _t("CSV Importation",1); ?>',
            export_csv: '<?php _t("CSV Exportation",1); ?>',
            import_tainacan: '<?php _t("Tainacan Importation",1); ?>',
            export_tainacan: '<?php _t("Tainacan Exportation",1); ?>',
            total_active: '<?php _t("Active",1); ?>',
            total_draft: '<?php _t("Draft",1); ?>',
            total_trash: '<?php _t("Trash",1); ?>',
            total_delete: '<?php _t("Deleted",1); ?>'
        };
    };

    TainacanChart.prototype.displayFixedBase = function() {
        $("#charts-resume table tr.headers").html("<th class='curr-parent'> Status: </th>");
        var parent_title = $(".current_parent_report").val();
        $("#charts-resume table tr.content").html("<td class='curr-filter'>" + parent_title + "</td>");
    };

    TainacanChart.prototype.displayBaseAppend = function(title, value) {
        $("#charts-resume table tr.headers").append("<th>"+ title +"</th>");
        $("#charts-resume table tr.quality-content").remove();
        $("#charts-resume table tr.content").append("<td>"+ value +"</td>");
    };

    TainacanChart.prototype.appendQualityBase = function() {
        $("#charts-resume table tr.headers").html("<td>Coleção</td><td>Nº de itens</td>");
        $("#charts-resume table tr.content").html("");
        $("#charts-resume table tr.quality-content").remove();
    };

    TainacanChart.prototype.appendQualityData = function(title, qtd) {
        $("#charts-resume table tbody").append("<tr class='quality-content'><td>"+ title +"</td><td>"+qtd+"</td></tr>");
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
                var which = obj.id.toString().replace("_period", "");
                $("#pdf-chart .period ." + which).text(dateText);
            }
        });
        $('a.change-mode').on('click', function() {
            var selected_chart = $(this).attr('data-chart');
            var curr_img = $(this).html();
            var chart_type = selected_chart.replace('chart_div', '');

            $('.selected_chart_type').val(chart_type);
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
            // $(".current-chart").html( parent + "<span class='glyphicon glyphicon-triangle-right'></span>" + split_title[0] );
            $(".current-chart").html( parent + "<span> / </span>" + split_title[0] );
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
            // { title: tChart.getStatDesc("Status", "logins / registros / banidos / excluídos"), href: "status", addClass: 'hllog' },
            { title: "Status <p> logins / registros / banidos / excluídos </p>", href: "status", addClass: 'hllog' },
            { title: "Itens <p> criaram / editaram / apagaram / <br/> visualizaram / baixaram</p>", href: "items" },
            { title: "Perfil <p> Pessoas que aderiram a um perfil </p>", href: "profile" },
            { title: "Categorias <p> criaram / editaram / apagaram / visualizaram </p>", href: "category" },
            { title: "Coleção <p> criaram / editaram / apagaram / visualizaram </p>", href: "collection" }
        ];
    }

    function itensChildren() {
        return [
            { title: "Usuário <p> view / comentado / votado </p>", href: "user" },
            { title: "Status <p> ativos / rascunhos / lixeira / excluídos </p>", href: "general_status" },
            { title: "Coleção <p> número de itens por coleção </p>", href: "top_collections" }
        ];
    }

    function collectionsChildren() {
        return [
            { title: "Status <p> criadas / editadas / excluídas / visualizadas / baixadas</p>", href: "collection"},
            { title: "Buscas Frequentes <p> ranking das buscas mais realizadas </p>", href: "searches"}
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
        var stat_path = $('.stat_path').val();

        $.ajax({
            url: stat_path + '/controllers/log/log_controller.php', type: 'POST',
            data: { operation: 'user_events', parent: parent, event: action, from: from, to: to }
        }).done(function(r){
            var res_json = $.parseJSON(r);
            var chart = $('.selected_chart_type').val();
            $(".current_parent_report").val(parent);
            
            if( (res_json == null) || res_json.length == 0) {
                toggleElements(["#charts-container div", "#charts-resume"], true);
                toggleElements(["#charts-container #no_chart_data"]);
            } else {
                toggleElements(["#"+chart+"chart_div", "#charts-resume"]);
                toggleElements(["#charts-container #no_chart_data"], true);
                drawChart(chart, action, res_json);
            }
        });
    }

    function drawChart(chart_type, title, data_obj) {
        // if has stats data
        if(data_obj) {
            var tai_chart = new TainacanChart();
            var basis = [ title, ' #Itens ', {role: 'style'} ]; // 'Qtd'
            var color = data_obj.color || '#79a6ce';
            var csvData = [];

            if(chart_type == "default") {
                var chart_data = [basis];
            } else {
                var chart_data = new google.visualization.DataTable();
                chart_data.addColumn('string', title);
                chart_data.addColumn('number', 'Qtd');
            }

            if(data_obj.stat_object) {
                // Dynamic header's chart data
                tai_chart.displayFixedBase();
                if(data_obj.item_status) {
                    for( index_i in data_obj.stat_object ){
                        for( t in data_obj.stat_object[index_i] ){
                            var obj_total = parseInt(data_obj.stat_object[index_i][t]);
                            var curr_evt_title = tai_chart.getMappedTitles()[t];
                            var curr_tupple = [ curr_evt_title, obj_total ];
                            if( chart_data instanceof google.visualization.DataTable ) {
                                chart_data.addRow(curr_tupple);
                            } else {
                                chart_data.push([curr_evt_title,obj_total, '#EF4C28']);
                            }
                            csvData.push( curr_tupple );
                            tai_chart.displayBaseAppend(curr_tupple[0], curr_tupple[1]);
                        }
                    }
                } else {
                    for( event in data_obj.stat_object ) {
                        var obj_total = parseInt(data_obj.stat_object[event]);
                        var curr_evt_title = tai_chart.getMappedTitles()[event];
                        var curr_tupple = [ curr_evt_title, obj_total ];

                        if( (typeof chart_data === 'object') && (chart_data instanceof google.visualization.DataTable) ) {
                            chart_data.addRow(curr_tupple);
                        } else {
                            chart_data.push([ curr_tupple[0], curr_tupple[1], color ]);
                        }

                        csvData.push( curr_tupple );
                        tai_chart.displayBaseAppend(curr_tupple[0], curr_tupple[1]);
                    } // for
                }

            } else if(data_obj.quality_stat) {
                tai_chart.appendQualityBase();
                for( colecao in data_obj.quality_stat ) {
                    for( c in data_obj.quality_stat[colecao]) {
                        var obj_total = parseInt(data_obj.quality_stat[colecao][c]);
                        var curr_tupple = [c,obj_total];
                        if( chart_data instanceof google.visualization.DataTable ) {
                            chart_data.addRow(curr_tupple);
                        } else {
                            chart_data.push([c,obj_total, '#2D882D']);
                        }
                        csvData.push( curr_tupple );
                        tai_chart.appendQualityData(curr_tupple[0], curr_tupple[1]);
                    } // for
                }
            }

            // Generate CSV file for current chart
            tai_chart.createCsvFile(csvData);

            // draw chart based at generated json data
            renderChart(title, chart_type, chart_data, color);
        }
    } // drawChart()

    function renderChart(current_title, type, stat_data, chart_color) {
        var color = chart_color || '#79a6ce';
        // Google Charts objects
        if( type == 'pie' ) {
            var piechart = new google.visualization.PieChart(document.getElementById('piechart_div'));
            var piechart_options = { title:'Qtd ' + current_title, is3D: true,
                colors: ['#F09B35','#8DA9BF','#F2C38D','#E6AC03', '#D94308', '#013453'] }; // '#0F4F8D','#2B85C1' tons de azul

            google.visualization.events.addListener(piechart, 'ready', function() {
                var chart_png = piechart.getImageURI();
                $('.dynamic-chart-img').removeClass('hide').attr('src', chart_png );
            });

            piechart.draw(stat_data, piechart_options);
        } else if ( type == 'bar' ) {
            var barchart = new google.visualization.BarChart(document.getElementById('barchart_div'));
            var barchart_options = {title:'Barchart stats', width: 800, height:300, legend: 'none', colors: ['#013453', 'orange']};

            google.visualization.events.addListener(barchart, 'ready', function() {
                var chart_png = barchart.getImageURI();
                $('.dynamic-chart-img').removeClass('hide').attr('src', chart_png );
            });

            barchart.draw(stat_data, barchart_options);
        } else if( type == 'default' ) {
            var default_chart = new google.visualization.ColumnChart(document.getElementById('defaultchart_div'));
            var data = new google.visualization.arrayToDataTable( stat_data );
            var default_options = { colors: [color], legend: 'none' };

            google.visualization.events.addListener(default_chart, 'ready', function(){
                var chart_png = default_chart.getImageURI();
                $('.dynamic-chart-img').removeClass('hide').attr('src', chart_png );
            });

            default_chart.draw(data, default_options);
        }
    }

    $('a.dl-pdf').click(function() {
        drawStatPDF();
    });

    $('a.dl-xls').click(function() {
        var tables = $("#charts-resume table").tableExport({
            formats: ["xls"], // 'csv'
            fileName: 'tainacan_report'
        });
        var xls_btn = $("#charts-resume .bottom button").get(0);
        $(xls_btn).click().addClass('hide');
    });

    function drawStatPDF() {
        var curr_type = $('.selected_chart_type').val();
        var d = new Date();
        var line_dims = { startX: 28, startY: 75, length: 540, thickness: 1 };
        var chart_margins = { top: 135, bottom: 60, left: 25, width: 180 };

        var from = $(".period-config #from_period").val();
        var to = $(".period-config #to_period").val();

        if(from) {
            var text_from = formatChartDate( new Date(from) );
        } else {
            var text_from = "01/01/" + new Date().getFullYear();
        }
        if (to) {
            var text_to = " a " + formatChartDate( new Date(to) );
        } else {
            var text_to = " a " + formatChartDate( new Date() );
        }

        var period_consult = $(".stats-i18n .consult-period").text();
        var week_day = " (" + (getWeekDay()[d.getDay()]).toString().toLowerCase() + ")";
        var formatted_date = d.getDate() + '/' + (d.getMonth() + 1) + '/' + d.getFullYear() + week_day;

        var pdf = new jsPDF('p', 'pt');

        var logo = $('img.tainacan-logo-cor').get(0);
        var projectLogo = new Image();
        projectLogo.src = $(logo).attr("src");
        var logo_settings = { width: (projectLogo.naturalWidth * 0.48), height: (projectLogo.naturalHeight * 0.48) };
        pdf.addImage(projectLogo, 'PNG', line_dims.startX + 15, line_dims.startY - 45, logo_settings.width, logo_settings.height);

        var chartImg = new Image();
        chartImg.src = $('.chart-img img.dynamic-chart-img').attr('src');
        var chart_settings = { width: (chartImg.naturalWidth * 0.6), height: (chartImg.naturalHeight * 0.6) };
        var pdfWidth = pdf.internal.pageSize.width;
        var horizontal_chart_center = (pdfWidth / 2)  - (chartImg.naturalWidth * 0.6 / 2);

        pdf.addImage(chartImg, 'PNG', horizontal_chart_center, chart_margins.top, chart_settings.width, chart_settings.height);

        pdf.rect(line_dims.startX, line_dims.startY, line_dims.length, line_dims.thickness, 'F');

        var consultDate = $(".stats-i18n .consult-date").text() + formatted_date;
        var same_x_dist = 350;
        pdf.setFontSize(14);
        pdf.setFontType('bold');
        pdf.text($(".stats-i18n .repo-stats").text(), (same_x_dist+20), (line_dims.startY - 17) ); // Estatísticas ...

        pdf.setFontSize(8);
        pdf.setTextColor(100);
        pdf.setFontType('normal');
        pdf.text(consultDate, same_x_dist + 50, line_dims.startY - 5); // Consultado em

        pdf.setTextColor(0);
        pdf.setFontSize(9.5);
        pdf.setFontType('bold');
        // content, xPos, yPos
        var dist_from_top = line_dims.startY + 20;
        pdf.text( $(".stats-i18n .search").text(), (line_dims.startX + 15), dist_from_top );
        pdf.setFontType('normal');
        var current_chart = $('.current-chart').text();
        pdf.text(current_chart, (line_dims.startX + 64), dist_from_top );

        pdf.setFontType('bold');
        pdf.text(period_consult, same_x_dist, dist_from_top ); // Período consultado

        pdf.setFontType('normal');
        pdf.text(text_from  + text_to, same_x_dist + 95, dist_from_top );
        pdf.rect(line_dims.startX, line_dims.startY + 30, line_dims.length, line_dims.thickness, 'F');

        var resume_data = pdf.autoTableHtmlToJson( $('#charts-resume table').get(0) );
        
        var p = 300;
        /* headerStyles: { textColor: [12,105,139], lineColor: [0,0,0], fillColor: 255 } */
        var autoTable_opts = { theme: 'striped', startY: p, headerStyles: { fillColor: [44, 62, 80] } };
        pdf.autoTable( resume_data.columns, resume_data.data, autoTable_opts);

        var footer_set = { startX: (pdf.autoTableEndPosY() + 160), startY: (pdf.autoTableEndPosY() + 430) };

        pdf.rect(line_dims.startX, footer_set.startY, line_dims.length, line_dims.thickness, 'F');
        pdf.fromHTML( $('#user_details').get(0), line_dims.startX, footer_set.startY );

        var right_footer_text = '<?php _t("Page ",1); ?>' + 1 + '<?php _t(" of ",1); ?>' + pdf.internal.getNumberOfPages();
        pdf.text(right_footer_text, footer_set.startX + 5, pdf.internal.pageSize.height - 20);
        // cl(pdf.internal);

        var timeStamp = d.getFullYear() + d.getDay() + d.getMilliseconds();
        var chart_name = 'tainacan_' + curr_type + '_report_' + timeStamp + '.pdf';
        pdf.save( chart_name );
    }

    function getWeekDay() {
        return ['Domingo','Segunda-feira','Terça-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sábado'];
    }

    function formatChartDate(dateToFormat) {
        if( dateToFormat instanceof Date) {
            return dateToFormat.getDate() + '/' + (dateToFormat.getMonth() + 1) + '/' + dateToFormat.getFullYear();
        }
    }

    $("#report_type_stat").dynatree(stats_dynatree_opts);
</script>
