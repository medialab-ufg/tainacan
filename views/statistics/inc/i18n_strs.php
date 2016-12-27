<div class="stats-i18n" style="display: none">
    <div class="period">
        <span class="next-text"> <?php _t('Next ', true); ?> </span>
        <span class="prev-text"> <?php _t('Previous', true); ?> </span>
    </div>
    <div class="report-types">
        <?php        
        foreach( $_log_helper->getReportTypes() as $type => $title ) { ?>
            <span class="stats-<?= $type ?>"><?= $title ?></span>
        <?php }
        ?>
    </div>

    <div class="general">
        <div class="search"> <?php _t('Search:', 1); ?> </div>
        <div class="repo-stats"> <?php _t('Repository Statistics', 1); ?> </div>
        <div class="collection-stats"> <?php _t('Collection Statistics', 1); ?> </div>
        <div class="consult-date"> <?php _t('Consulted ', 1); ?> </div>
        <div class="consult-period"> <?php _t('Consulted period: ', 1); ?> </div>
    </div>

    <div class="charts-subtitles"></div>
</div>