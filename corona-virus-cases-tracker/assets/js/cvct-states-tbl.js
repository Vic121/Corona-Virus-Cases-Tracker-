jQuery(document).ready( function ($) {
    var pagination = $("#cvct_states_table_id").data('pagination');
    jQuery('#cvct_states_table_id').DataTable({
        responsive: true,
        pageLength: pagination,
        "order": [ 1, 'desc' ]
        
    });
    
} );