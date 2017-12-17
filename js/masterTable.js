/**
 * On document ready.
 */
$(document).ready(function () {
    /*
     * =================================
     * Initialize master table instance.
     * =================================
     */
    var masterTable = $('.master-table').DataTable({
        // Sorting.
        order: [],

        // Scrolling.
        "scrollY": true,
        "scrollX": true,

        // Column definitions.
        columnDefs: [
            {
                targets: 'dt-nosort',
                orderable: false
            },
            {
                targets: 'dt-id-column',
                visible: false,
                searchable: false
            }
        ]
    });
});