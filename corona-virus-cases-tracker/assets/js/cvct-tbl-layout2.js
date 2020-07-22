jQuery(document).ready( function ($) {
    var pagination = $("#cvct_table_id").data('pagination');
    jQuery('#cvct_table_id').DataTable({
        // scrollX: 300,
        // paging: true,
       responsive: true,
        pageLength: pagination,
        "order": [ 1, 'desc' ],
    });
    
} );