<ul class="nav navbar-bar navbar-right">
    <li class="dropdown open_item_actions">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
            <?php echo ViewHelper::render_icon("config-azul", "png"); ?>
        </a>
        <ul class="dropdown-menu pull-right dropdown-show" role="menu">
            <li> <a class="ac-view-item"> <?php _t('View Item',1); ?> </a> </li>
            <li> <a class="ac-open-file"> <?php _t('Open item file',1); ?> </a> </li>
            <li> <a class="ac-edit-item"> <?php _t('Edit item',1); ?> </a> </li>
            <li> <a class="ac-duplicateTC-item"> <?php _t('Duplicate in this collection',1); ?> </a> </li>
            <li> <a class="ac-duplicateOC-item"> <?php _t('Duplicate in other collection',1); ?> </a> </li>
            <li> <a class="ac-checkin"> <?php _t('Check-in',1); ?> </a> </li>            
            <li> <a class="ac-checkout"> <?php _t('Discard Check-out',1); ?> </a> </li>            
            <li> <a class="ac-create-version"> <?php _t('Create new version',1); ?> </a> </li>            
            <li> <a class="ac-item-versions"> <?php _t('Item versions',1); ?> </a> </li>            
            <li> <a class="ac-item-rdf"> <?php _t('Export RDF',1); ?> </a> </li>            
            <li> <a class="ac-item-graph"> <?php _t('See graph',1); ?> </a> </li>            
            <li> <a class="ac-comment-item"> <?php _t('Comment item',1); ?> </a> </li>            
            <li> <a class="ac-exclude-item"> <?php _t('Exclude item',1); ?> </a> </li>            
        </ul>
    </li>
</ul>