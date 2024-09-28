<script src="https://code.jquery.com/jquery-3.5.1.min.js"
    integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="{{ asset('assets/js/loadingoverlay.min.js') }}"></script>
<script src="{{ asset('assets/datatables/datatables.bundle.js') }}"></script>

<script>
    $(document).ready(function() {
        set_datatable();
    });

    function set_datatable() {

        datatable = $('.datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                "url": '/',
                "type": "GET",
                'headers': {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    $(".loading").LoadingOverlay("show");
                    $(".loading").LoadingOverlay("hide", true);

                    var datatableFilters = [];

                    $.each($(".datatable").data("filters"), function(index, val) {
                        datatableFilters[val] = $('#' + val).val()
                    })

                    return $.extend({}, d, datatableFilters);
                }
            },
            language: {
                url: 'assets/datatables/tr.json'
            },
            columns: datatableColumns,
            order: [
                [0, "desc"]
            ],
            searching: true
        });
    }
    var datatableColumns = [];
    $('.datatable th').each(function() {
        var column = $(this).data('column');
        datatableColumns.push({
            data: column,
            name: column
        });
    });
</script>
