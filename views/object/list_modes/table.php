<div class="col-md-12 table-view-container top-div"
   <?php if ($collection_list_mode != "table"): ?> style="display: none;" <?php endif ?> >
    <table id="table-view" class="table table-striped table-bordered">
      <thead>
      <tr>
          <th><?php _e('Date', 'tainacan'); ?></th>
          <th><?php _e('Item', 'tainacan'); ?></th>
          <th><?php _e('Description', 'tainacan'); ?></th>
          <th><?php _e('Actions', 'tainacan'); ?></th>
      </tr>
      </thead>
      <tbody id="table-view-elements"></tbody>
    </table>
</div>
