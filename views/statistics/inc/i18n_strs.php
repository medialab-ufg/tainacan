<div class="stats-i18n" style="display: none">
    <div class="period">
        <span class="next-text"> <?php i18n_str('Next ', true); ?> </span>
        <span class="prev-text"> <?php i18n_str('Previous', true); ?> </span>
    </div>
    <div class="report-types">
        <?php        
        foreach( $_log_helper->getReportTypes() as $type => $title ) { ?>
            <span class="stats-<?= $type ?>"> <?= $title ?> </span>
        <?php }
        ?>
    </div>
</div>