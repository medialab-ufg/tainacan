<script type="text/javascript">
    google.charts.load('current', {'packages':['bar', 'line', 'corechart']}); //bar
    google.charts.setOnLoadCallback(loadChart);
    
    var TainacanChart = function(){};
    TainacanChart.prototype.getMappedTitles = function() {
        return {
            add: '<?php _t("Added", 1); ?>',
            edit: '<?php _t("Edited", 1); ?>',
            view: '<?php _t("Viewed", 1); ?>',
            download: '<?php _t("Downloaded", 1); ?>',
            delete: '<?php _t("Deleted", 1); ?>',
            comment: '<?php _t("Commented", 1); ?>',
            vote: '<?php _t("Voted", 1); ?>',
            login: '<?php _t("Logged in", 1); ?>',
            register: '<?php _t("Registers", 1); ?>',
            delete_user: '<?php _t("Excluded", 1); ?>',
            administrator: '<?php _t("Administrator", 1); ?>',
            author: '<?php _t("Author", 1); ?>',
            editor: '<?php _t("Editor", 1); ?>',
            subscriber: '<?php _t("Subscriber", 1); ?>',
            contributor: '<?php _t("Colaborator", 1); ?>',
            access_oai_pmh: '<?php _t("OAI-PMH Accesses", 1); ?>',
            harvest_oai_pmh: '<?php _t("Harvesting OAI-PMH", 1); ?>',
            import_csv: '<?php _t("CSV Importation", 1); ?>',
            export_csv: '<?php _t("CSV Exportation", 1); ?>',
            import: '<?php _t("Importation", 1); ?>',
            export: '<?php _t("Exportation", 1); ?>',
            import_tainacan: '<?php _t("Tainacan Importation", 1); ?>',
            export_tainacan: '<?php _t("Tainacan Exportation", 1); ?>',
            publish: '<?php _t("Active", 1); ?>',
            draft: '<?php _t("Draft", 1); ?>',
            trash: '<?php _t("Trash", 1); ?>',
            //total_delete: '<?php _t("Deleted", 1); ?>',
            config: '<?php _t("Configurations", 1); ?>',
            welcome_mail: '<?php _t("Welcome Mail", 1); ?>',
            licenses: '<?php _t("Licenses", 1); ?>',
            keys: '<?php _t("Keys", 1); ?>',
            tools: '<?php _t("Tools", 1); ?>',
            metadata: '<?php _t("Metadata", 1); ?>',
            layout: '<?php _t("Layout", 1); ?>',
            social_media: '<?php _t("Social Media", 1); ?>'
        };
    };

    TainacanChart.prototype.displayFixedBase = function() {
        $("#charts-resume table tr.headers").html("<th class='curr-parent'> Status: </th>");
        //var parent_title = $(".current_parent_report").val();
        $("#charts-resume table tr.content").html("<td class='curr-filter'> Total: </td>");
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

    TainacanChart.prototype.appendSearchesBase = function(){
        $("#charts-resume table tr.headers").html("<td>Termo</td><td>Quantidade de buscas</td>");
        $("#charts-resume table tr.content").html("");
        $("#charts-resume table tr.quality-content").remove();
    }

    TainacanChart.prototype.appendQualityData = function(title, qtd) {
        $("#charts-resume table tbody").append("<tr class='quality-content'><td>"+ title +"</td><td>"+ qtd +"</td></tr>");
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
        // Specific functions to repository or collections stat page
        normalizeStatPage();

        var dateFormat = "dd/mm/yy";

        // Period config
        $("#from_period").datepicker({
            dateFormat: 'dd/mm/yy',
            showButtonPanel: false,
            showAnim: 'clip',
            changeMonth: true,
            changeYear: true,
            minDate: '01-01-2016',
            maxDate: '0',
            onSelect: function(dateText, obj) {
                var which = obj.id.toString().replace("_period", "");
                $("#pdf-chart .period ." + which).text(dateText);
            }
        }).on("change", function () {
            $("#to_period").datepicker("option", "minDate", getDate(this));
        });

        $("#to_period").datepicker({
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
            showButtonPanel: false,
            showAnim: 'clip',
            maxDate: '0',
        }).on("change", function () {
            $("#from_period").datepicker("option", "maxDate", getDate(this));
        });

        function getDate(element) {
            var date;

            try {
                date = $.datepicker.parseDate(dateFormat, element.value);
            } catch (error){
                date = null;
            }

            return date;
        }

        //Default period
        $("#from_period").val('01/01/'+today().substring(6,10));
        $("#to_period").val(today());

        //On set from and to
        function today() {
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth()+1; //January is 0!
            var yyyy = today.getFullYear();

            if(dd<10) {
                dd='0'+dd
            }

            if(mm<10) {
                mm='0'+mm
            }

            return today = dd+'/'+mm+'/'+yyyy;
        }

        $("#from_period").on('change', function () {
            var diffe = Math.ceil( ($("#to_period").datepicker('getDate') - $("#from_period").datepicker('getDate')) / (1000 * 60 * 60 * 24) ) + 1;
            //console.log(diffe);

            if(diffe < 7){
                $("#weeks").prop('disabled', true);
                $("#months").prop('disabled', true);
            }
            else if(diffe < 28){
                $("#months").prop('disabled', true);
            }
            else{
                $("#weeks").prop('disabled', false);
                $("#months").prop('disabled', false);
            }
            loadChart();
        });

        $("#to_period").on('change', function () {
            var diffe = Math.ceil( ($("#to_period").datepicker('getDate') - $("#from_period").datepicker('getDate')) / (1000 * 60 * 60 * 24) ) + 1;
            //console.log(diffe);

            if(diffe < 7){
                $("#weeks").prop('disabled', true);
                $("#months").prop('disabled', true);
            }
            else if(diffe < 28){
                $("#months").prop('disabled', true);
            }
            else{
                $("#weeks").prop('disabled', false);
                $("#months").prop('disabled', false);
            }
            loadChart();
        });

        //-

        // On change filter type week, day, month
        $("input[type=radio][name=optradio]").on('change', function () {
            loadChart();
        });

        //On change chart type
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
            loadChart();
        });
    });

    $("#config-repo").accordion({
        collapsible: true,
        active: 0,
        header: "label",
        animate: 200,
        heightStyle: "content",
        icons: false
    });

    $("#config-filt").accordion({
        collapsible: true,
        active: 0,
        header: "label",
        animate: 200,
        heightStyle: "content",
        icons: false
    });

    function normalizeStatPage() {
        var stats_title = [
            $('.stats-i18n .repo-stats').text(),
            $('.stats-i18n .collection-stats').text()
        ];
        // Repository Statistics
        if( $('body').hasClass('page-template-page-statistics') ) {
            $('.chart-header h3.topo').text(stats_title[0]);
        } else if( $('body').hasClass('single-socialdb_collection') ) { // Collection's Statistics
            $('.chart-header h3.topo').text(stats_title[1]);
        }
    }

    var tChart = new TainacanChart();

    // Report type configs
    var statsDynatreeData = {
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
            var parent = node.parent.data.title; //titulo da div parent, ex: users
            var node_action = node.data.id; //id do li dentro da div parent
            var chart_text = node.data.title;
            var chain = $('.temp-set').html(chart_text).text().replace(/\//gi, "");
            var split_title = chain.split(" ");
            
            if(node_action) {
                fetchData(parent, node_action);
            }
        },
        onPostInit: function(isReloading, isError) {
            if( $('body').hasClass('single-socialdb_collection') ) { // Collection's Statistics
                $('.repoOnly').hide();
                var fst_col_dyna = $('.dynatree-radio').get(1);
                $(fst_col_dyna).click();
            } else {
                $('.dynatree-radio').first().click();
            }
        },
        onQueryExpand: function() {
            if( $('body').hasClass('single-socialdb_collection') ) { // Collection's Statistics
                $('.repoOnly').hide('fast');
            } else if ( $('body').hasClass('page-template-page-statistics') ) {
                $('.collecOnly').hide('fast');
            }
        }
    };

    // Dynatrees report's children
    function statusChildren() {
        return [
            { title: "Status <p> logins / registros / banidos / excluídos </p>", id: "status", addClass: 'repoOnly'},
            { title: "Itens <p> criaram / editaram / apagaram / <br/> visualizaram / baixaram</p>", id: "items" },
            { title: "Perfil <p> Pessoas que aderiram a um perfil </p>", id: "profile", addClass: 'repoOnly'},
            { title: "Categorias <p> criaram / editaram / apagaram / visualizaram </p>", id: "category" },
            { title: "Coleção <p> criaram / editaram / apagaram / visualizaram </p>", id: "collection", addClass: 'repoOnly' }
        ];
    }

    function itensChildren() {
        return [
            { title: "Usuário <p> view / comentado / votado </p>", id: "user" },
            { title: "Status <p> ativos / rascunhos / lixeira / excluídos </p>", id: "general_status" },
            { title: "Coleção <p> número de itens por coleção </p>", id: "top_collections", addClass: 'repoOnly' }
        ];
    }

    function collectionsChildren() {
        return [
            { title: "Status <p> criadas / editadas / excluídas / visualizadas / baixadas</p>", id: "collection", addClass: 'repoOnly'},
            { title: "Buscas Frequentes <p> ranking das buscas mais realizadas </p>", id: "repo_searches", addClass: 'repoOnly'},
            { title: "Buscas <p> termos mais pesquisados </p>", id: "collection_searches", addClass: 'collecOnly'}
        ];
    }

    function commentsChildren() {
        return [{ title: "Status <p> adicionados / editados / excluídos / visualizados </p>", id: "comments" }];
    }

    function categoryChildren() {
        return [{ title: "Status <p> criados / editados / excluídos </p>", id: "category" }];
    }

    function tagsChildren() {
        return [{ title: "Status <p> adicionados / editados / excluídos </p>", id: 'tags' }];
    }

    function importsChildren() {
        return [
            { title: "<p> Acessos OAI-PMH <br/> Importação / Exportação CSV <br/> Importação formato Tainacan <br/> Exportação formato Tainacan </p>", id: 'imports', addClass: 'repoOnly'},
            { title: "<p> Acessos OAI-PMH <br/> Harvesting OAI-PMH <br/> Importação CSV <br/> Exportação CSV</p>", id: 'collection_imports', addClass: 'collecOnly'}
        ];
    }

    function adminChildren() {
        return [
            { title: "Páginas Administrativas <p> Configurações / metadados / chaves / licenças /<br /> e-mail boas vindas / ferramentas </p>", id: 'admin', addClass: 'repoOnly'},
            { title: "<p> Configurações / metadados / layout / redes sociais <br /> licenças / importação / exportação </p>", id: 'collection_admin', addClass: 'collecOnly' }
        ];
    }

    //Report type list
    function getStatsTree() {
        return [
            { title: $('.stats-users').text(), noLink: true, expand: true, unselectable: true, hideCheckbox: true, children: statusChildren() },
            { title: $('.stats-items').text(), noLink: true, unselectable: true, hideCheckbox: true, children: itensChildren() },
            { title: $('.stats-collections').text(), noLink: true, hideCheckbox: true, children: collectionsChildren() },
            { title: $('.stats-comments').text(), noLink: true, hideCheckbox: true, children: commentsChildren() },
            { title: $('.stats-categories').text(), noLink: true, hideCheckbox: true, children: categoryChildren() },
            { title: $('.stats-tags').text(), noLink: true, hideCheckbox: true, children: tagsChildren()},
            { title: $('.stats-imports').text(), noLink: true, hideCheckbox: true, children: importsChildren() },
            { title: $('.stats-admin').text(), noLink: true, hideCheckbox: true, children: adminChildren() }
        ];
    }

    function fetchData(parent, action) {
//        var from = "";
//        var to = "";
//        if( ($("#from_period").val()) && ($("#to_period").val()) ) {
        var f = $("#from_period").val().split('/');
        var t = $("#to_period").val().split('/');

        var dfrom = new Date(f[2], f[1] - 1, f[0]);
        var dto = new Date(t[2], t[1] - 1, t[0]);

        var from = dfrom.toISOString().split('T')[0];
        var to = dto.toISOString().split('T')[0];

        console.log(" "+ from +" "+ to);
//        }

        var stat_path = $('.stat_path').val() || $('#src').val(); //url do tema
        var c_id = $('.get_collection_stats').val() || null; //id da coleção ?!

        var filter = 'months';
        
        if($('#days').is(":checked")){
            filter = $('#days').val();
        }
        else if($('#weeks').is(":checked") && $('#weeks').is(":enabled")){
            filter = $('#weeks').val();
        }
        else if($('#months').is(":checked") && $('#months').is(":enabled")){
            filter = $('#months').val();
        }

        $.ajax({
            url: stat_path + '/controllers/log/log_controller.php', type: 'POST',
            data: { operation: 'user_events', parent: parent, event: action, from: from, to: to, collec_id: c_id, filter: filter }
        }).done(function(resp) {
        console.log("Done: " + resp);

            var res_json = JSON.parse(resp);
            console.log("Res: "+ res_json);

            var chart = $('.selected_chart_type').val(); //tipo de chart selecionado
            $(".current_parent_report").val(parent); //nome do parent atual 'ex: Users'

            if( (res_json == null) || res_json.length == 0) {
                toggleElements(["#charts-container div", "#charts-resume"], true);
                toggleElements(["#charts-container #no_chart_data"]);
            } else {
                toggleElements(["#"+chart+"chart_div", "#charts-resume"]);
                toggleElements(["#charts-container #no_chart_data"], true);
                setTimeout( function() {
                    drawChart(chart, action, res_json, filter)
                }, 300);
            }
        });
    }

    //Verify if element exist
    function existIn(array_n, rows){
        for(h in rows){
            //console.log("Aa: "+ h +" aa: "+ rows);
            if(array_n[1] == rows[Number(h)][0]){
                return [false, Number(h)];
            }
        }
        return true;
    }

    function drawChart(chart_type, title, data_obj, filter) {
        console.log(data_obj); // Show data object
        if(data_obj) {
            var tai_chart = new TainacanChart(); // New instance of TainacanChart
            var csvData = []; // The Array to create CSV file
            var chart_data = new google.visualization.DataTable(); // The DataTable to insert in Charts      
            if(data_obj.columns.events){
                var columnsData = data_obj.columns.events; // Array that contains name of events (login, add, etc.) 
            }
            else if(data_obj.columns){
                var columnsData = data_obj.columns;
            }
            // Preparation of DataTables to different filters

            if(filter == "days" || filter == "months"){
                chart_data.addColumn('string', 'date');
                for(evnt in columnsData){
                    chart_data.addColumn('number', tai_chart.getMappedTitles()[columnsData[evnt]] ? tai_chart.getMappedTitles()[columnsData[evnt]] : columnsData[evnt]);
                }
            }
            else if(filter == "weeks"){
                chart_data.addColumn('number', 'week_number');
                for(evnt in columnsData){
                    chart_data.addColumn('number', tai_chart.getMappedTitles()[columnsData[evnt]] ? tai_chart.getMappedTitles()[columnsData[evnt]] : columnsData[evnt]);
                }
            }

            //

            // If stat object has values
            if(data_obj.stat_object && data_obj.stat_object[0]) {

                // Show in statistics page footer with stats and totals
                tai_chart.displayFixedBase();
                
                // This if is not used
                    // Where the magic happens!

                    var rows = [[]]; // Array that contains rows of DataTable
                    var flag = ''; // Var that contains name of actual event
                    var cole_flag = 0;
                    
                    rows[0] = ['-'];
                    
                    //console.log('Rows created: '+ rows);
                    for( j in data_obj.stat_object ) {
                        var array_n = data_obj.stat_object[j]; // Array that contains the actual array from stat_object
                        var le2 = columnsData.length; // Var that contains total of events
                        //console.log("Array actual 1: "+ array_n);

                        // If has value in first array
                        if(rows[0][0]){
                            //console.log("rows a 1: "+ rows);
                            // If date is equal to date of an existing element and array exist
                            var temp  = existIn(array_n, rows);
                              //console.log("temp: "+ temp);
                            var el = rows[Number(temp[1])]; // Var that contains actual array from rows
                             // console.log("rows aa 1: "+ el);

                            if(temp[0] == false){ 
                                for(x in columnsData){
                                    //If event is equal to event in existing element
                                    if(columnsData[Number(x)] == array_n[0]){ 
                                        var indCol = Number(x)+1; // Var that contains the index of event
                                        break;
                                    }
                                }
                                //console.log("rows a 2: "+ rows);
                                el[indCol] = array_n[2]; // the actual receive total of expecifc event
                                
                                for(var g = 0; g <= le2; g++){
                                    if(!el[g]){
                                        el[g] = 0;
                                    }
                                }

                                rows[Number(temp[1])] = el; // The existing array is changed
                                //console.log("rows a 3: "+ rows);
                            }
                              // Array with same date doesn't exist
                            else {
                                //console.log("rows 1: "+ rows);
                                rows.push([]);
                                //console.log("rows 2: "+ rows);
                                for(z in columnsData){
                                //If event is equal to event in existing element
                                    if(columnsData[Number(z)] == array_n[0]){ 
                                        var indColu = Number(z)+1; // Var that contains the index of event
                                        break;
                                    }
                                }
                                //console.log("rows 3: "+ rows);
                                var le = rows.length-1; // Var that contains the total of rows
                                 
                                 
                                rows[le][0] = array_n[1]; // Add new date to array
                                //console.log("rows 4: "+ rows);
                                rows[le][indColu] = array_n[2]; // Add new value to array
                                //console.log("rows 5: "+ rows);
                                 
                                // Add zero in black array position
                                for(var g = 0; g <= le2; g++){
                                    if(!rows[le][g]){
                                        rows[le][g] = 0;
                                    }
                                }
                                //console.log("rows 6: "+ rows);
                                //remove the empty initial element
                                if(rows[0][0] == '-'){
                                    rows.splice(0,1); 
                                }
                                //console.log("rows 7: "+ rows);
                            }
                        }

                        csvData.push( array_n );

                        if(flag != array_n[0]){
                           flag = array_n[0];
                           
                           var curr_evt_title = tai_chart.getMappedTitles()[flag] ? tai_chart.getMappedTitles()[flag] : flag;
                           //console.log(flag);
                           if(title == 'top_collections'){
                               if (cole_flag == 0){
                                   tai_chart.appendQualityBase();
                                   cole_flag = 1;
                               } 
                               tai_chart.appendQualityData(curr_evt_title, array_n[3]);
                           }
                           else if(title == 'repo_searches' || title == 'collection_searches'){
                               if(cole_flag == 0){
                                   tai_chart.appendSearchesBase();
                                   cole_flag = 1;
                               }
                               tai_chart.appendQualityData(curr_evt_title, array_n[3]);
                           }
                           else{
                               // Display status and total
                               tai_chart.displayBaseAppend(curr_evt_title, array_n[3]);
                           }
                        }
                    } // end of for
                    //console.log("before: "+ rows);
                    rows.sort(); 
                    //console.log("after: "+ rows);
                    chart_data.addRows(rows);
            } 
            else if(!data_obj.stat_object[0]){
                chart_data.addRow();
                tai_chart.displayFixedBase();
            }

            // Generate CSV file for current chart
            tai_chart.createCsvFile(csvData);

            var div_chart = $("#charts-container").get(0);
            $(div_chart).removeClass('hide');
            // draw chart based at generated json data
            renderChart(title, chart_type, chart_data);
        }
    } // drawChart()

    function renderChart(current_title, type, stat_data) {
       // var color = chart_color || '#79a6ce';
       // console.log('#1 stat_data: '+ JSON.stringify(stat_data) +'\n'+ 'type: '+ type);
        // Google Charts objects
        if( type == 'pie' ) {
            var piechart = new google.visualization.PieChart(document.getElementById('piechart_div'));
            var pieOptions = {
                is3D: true,
                colors: ['#F09B35','#8DA9BF','#F2C38D','#E6AC03', '#D94308', '#013453'],
                legend: {position: 'top', alignment: 'center', textStyle: {fontSize: 11}},
                fontSize: 10,
                selectionMode: 'multiple',
                tooltip: {trigger: 'selection'},
                aggregationTarget: 'category'
            }; // '#0F4F8D','#2B85C1' tons de azul

            google.visualization.events.addListener(piechart, 'ready', function() {
                var chart_png = piechart.getImageURI();
                $('.dynamic-chart-img').removeClass('hide').attr('src', chart_png );
            });

            piechart.draw(stat_data, pieOptions);
        }
        else if ( type == 'bar' ) {
            var barchart = new google.visualization.BarChart(document.getElementById('barchart_div'));
            var barOptions = {
                bars: 'horizontal',
                legend: {position: 'top', alignment: 'center', textStyle: {fontSize: 11}},
                fontSize: 10,
                selectionMode: 'multiple',
                tooltip: {trigger: 'selection'},
                aggregationTarget: 'category'
            };

            google.visualization.events.addListener(barchart, 'ready', function() {
                var chart_png = barchart.getImageURI();
                $('.dynamic-chart-img').removeClass('hide').attr('src', chart_png );
            });

            barchart.draw(stat_data, barOptions);
        }
        else if( type == 'default' ) {
            var default_chart = new google.visualization.ColumnChart(document.getElementById('defaultchart_div'));
            var defaOptions = {
                bars: 'vertical',
                legend: {position: 'top', alignment: 'center', textStyle: {fontSize: 11}},
                fontSize: 10,
                selectionMode: 'multiple',
                tooltip: {trigger: 'selection'},
                aggregationTarget: 'category'
            };

            google.visualization.events.addListener(default_chart, 'ready', function(){
                var chart_png = default_chart.getImageURI();
                $('.dynamic-chart-img').removeClass('hide').attr('src', chart_png );
            });

            default_chart.draw(stat_data, defaOptions);
        }
        else if( type == 'curveline'){
            //var tessst = $.parseJSON(stat_data);
            //console.log('#2 stat_data: '+ tessst +'\n'+ 'type: '+ type);

            var linechart = new google.visualization.LineChart(document.getElementById('curvelinechart_div'));
            var curveOptions = {
                curveType: 'function',
                legend: {position: 'top', alignment: 'center', textStyle: {fontSize: 11}},
                fontSize: 10,
                selectionMode: 'multiple',
                tooltip: {trigger: 'selection'},
                aggregationTarget: 'category'
            };

            //console.log('#2 data: '+ tessst +'\n'+ 'type: '+ type +'\n'+ linechart +'\n'+ curveOptions);

            google.visualization.events.addListener(linechart, 'ready', function(){
                var chart_png = linechart.getImageURI();
                $('.dynamic-chart-img').removeClass('hide').attr('src', chart_png);
            });

            linechart.draw(stat_data, curveOptions);
        }
        $('.chartChanger').removeClass('hide');
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
        var formatted_date = getTodayFormatted();

        var pdf = new jsPDF('p', 'pt');

        var logo = $('img.tainacan-logo-cor').get(0);
        var projectLogo = new Image();
        projectLogo.src = $(logo).attr("src");
        var logo_settings = { width: (projectLogo.naturalWidth * 0.48), height: (projectLogo.naturalHeight * 0.48) };
        pdf.addImage(projectLogo, 'PNG', line_dims.startX + 15, line_dims.startY - 45, logo_settings.width, logo_settings.height);

        // wether stat has chart or not
        var no_chart_stat = $('tr.headers td').length;
        if( no_chart_stat == 2 ) {
            var chart_table_YDist = 120;
        } else if ( no_chart_stat == 0 ) {
            var chart_table_YDist = 300;
            var chartImg = new Image();
            chartImg.src = $('.chart-img img.dynamic-chart-img').attr('src');
            var chart_settings = { width: (chartImg.naturalWidth * 0.6), height: (chartImg.naturalHeight * 0.6) };
            var pdfWidth = pdf.internal.pageSize.width;
            var horizontal_chart_center = (pdfWidth / 2)  - (chartImg.naturalWidth * 0.6 / 2);
            pdf.addImage(chartImg, 'PNG', horizontal_chart_center, chart_margins.top, chart_settings.width, chart_settings.height);
        }

        pdf.rect(line_dims.startX, line_dims.startY, line_dims.length, line_dims.thickness, 'F');

        var consultDate = $(".stats-i18n .consult-date").text() + formatted_date;
        var same_x_dist = 350;

        var current_pdf_chart = $(".chart-header h3.topo").text();
        var repository_chart_title = $(".stats-i18n .repo-stats").text();
        var colleciton_chart_title = $(".stats-i18n .collection-stats").text();
        if( current_pdf_chart == repository_chart_title ) {
            var current_pdf_title_xDist = (same_x_dist+20);
        } else if(current_pdf_chart == colleciton_chart_title) {
            var current_pdf_title_xDist = (same_x_dist+45);
        }

        pdf.setFontSize(14);
        pdf.setFontType('bold');
        pdf.text(current_pdf_chart, current_pdf_title_xDist,(line_dims.startY - 17) ); // Estatísticas ...

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

        // headerStyles: { textColor: [12,105,139], lineColor: [0,0,0], fillColor: 255 }
        var autoTable_opts = { theme: 'striped', startY: chart_table_YDist, headerStyles: { fillColor: [44, 62, 80] } };
        pdf.autoTable( resume_data.columns, resume_data.data, autoTable_opts);

        var footer_set = {startX: 160, startY: pdf.internal.pageSize.height - 68};

        pdf.rect(line_dims.startX, footer_set.startY, line_dims.length, line_dims.thickness, 'F');
        pdf.fromHTML( $('#user_details').get(0), line_dims.startX, footer_set.startY );

        var right_footer_text = '<?php _t("Page ", 1); ?>' + 1 + '<?php _t(" of ", 1); ?>' + pdf.internal.getNumberOfPages();
        pdf.text(right_footer_text, footer_set.startX + 350, pdf.internal.pageSize.height - 20);

        var timeStamp = d.getFullYear() + d.getDay() + d.getMilliseconds();
        var chart_name = 'tainacan_' + curr_type + '_report_' + timeStamp + '.pdf';
        pdf.save( chart_name );
    }

    function formatChartDate(dateToFormat) {
        if(dateToFormat instanceof Date) {
            return dateToFormat.getDate() + '/' + (dateToFormat.getMonth() + 1) + '/' + dateToFormat.getFullYear();
        }
    }

    // Load dynatree
    $("#report-type-stat").dynatree(statsDynatreeData);

    // On Load page or change filters
    function loadChart(){
        $('.dynatree-selected').click();
    }
</script>
