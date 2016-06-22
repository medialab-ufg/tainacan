<?php
/*
 * Template Name: Index
 * Description: teste
 */
get_header();
$options = get_option('socialdb_theme_options');
$get = json_encode($_GET);
?>

<?php while (have_posts()) : the_post(); ?>
    <input type="hidden" id="src" name="src" value="<?php echo get_template_directory_uri() ?>">
    <input type="hidden" id="verb" name="verb" value='<?php
    if ($_GET['verb']) {
        echo $get;
    }
    ?>'>
    <div class="panel-heading" style="max-width: 100%;">
        <div class="row">
            <div class="col-md-12">
                <dl>
                    <dt>Example Tables
                    <dd><a href="doc/oai_records_mysql.sql">OAI Records (mysql)</a></dd>
                    <dd><a href="doc/oai_records_pgsql.sql">OAI Records (pgsql)</a></dd>
                    </dt>
                    <dt>Query and check your Data-Provider</dt>
                    <dd><a href="<?php echo get_template_directory_uri() ?>/controllers/export/oaipmh_controller.php">Identify</a></dd>
                    <dd><a href="#">ListMetadataFormats</a></dd>
                    <dd><a href="#">ListSets</a></dd>
                    <dd><a href="#">ListIdentifiers</a></dd>
                    <dd><a href="<?php echo get_template_directory_uri() ?>/controllers/export/oaipmh_controller.php?verb=listRecords">ListRecords</a></dd>
                    <dd><a href="#">GetRecord</a></dd>
                    </dt>
                </dl>
            </div>
        </div>
    </div> 
    <script>
        $(function () {
            var query_string = ''
            var get_requisition = jQuery.parseJSON($('#verb').val());
            if (get_requisition && get_requisition.verb) {
                query_string = '?verb=' + get_requisition.verb;
                $.each(get_requisition, function (idx, general) {
                    if (idx !== 'verb') {
                        query_string = query_string + '&' + idx + '=' + general;
                    }
                });
                window.location = $('#src').val() + '/controllers/export/oaipmh_controller.php' + query_string;
            }
        });

    </script>
    <?php
endwhile; // end of the loop.
get_footer();
?>

