@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-center flex-column mx-auto" style="height: 100vh; width: 85vw;">
        <div class="buttons d-flex align-items-center mb-3">
            <a href="javascript:;" class="btn btn-primary refresh_datatable ms-auto">Tablo yenile</a>
            <a href="javascript:;" class="btn btn-success refresh_data ms-2">Trendyol data yenile</a>
        </div>
        <div class="d-flex align-items-center w-100">
            <table id="datatable" class="datatable table table-striped loading h-100" style="width: 85vw">
                <thead class="thead-light">
                    <tr class="fw-bold fs-6 text-muted">
                        <th data-column="product_code">#</th>
                        <th data-column="name">Başlık</th>
                        <th data-column="price">Fiyat</th>
                        <th data-column="stock_unit_type">Stok Türü</th>
                        <th data-column="quantity">Stok Sayısı</th>
                        <th style="width: 130px" data-column="is_varianted">Variant Durumu</th>
                        <th data-column="process">İşlem</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).delegate(".update_item", "click", function() {
            let url = $(this).data('url');

            Swal.fire({
                title: "Güncelleme yapın",
                html: `<input id="quantity" class="swal2-input" placeholder="Stok Sayısı"><input id="price" class="swal2-input" placeholder="Fiyat">`,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: "Onayla",
                showLoaderOnConfirm: true,
                preConfirm: async () => {
                    const quantity = document.getElementById('quantity').value;
                    const price = document.getElementById('price').value;
                    try {
                        const response = await $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: url,
                            method: 'PUT',
                            data: {
                                quantity: quantity,
                                price: price
                            }
                        });
                        return response; // İstek başarılı olursa sonucu döndür
                    } catch (error) {
                        Swal.showValidationMessage(`Hata: ${error.responseJSON.message}`);
                    }
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        text: result.value.message,
                        icon: 'success',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('.refresh_datatable').click();
                        }
                    });
                }
            });
        });

        $('.refresh_datatable').click(function() {
            $('#datatable').DataTable().ajax.reload()
        });
        $('.refresh_data').click(function() {
            $.ajax({
                url: '/trendyol/products',
                type: 'GET',
                success: function(response) {
                    Swal.fire({
                        text: "Trendyoldan veriler çekiliyor.",
                        icon: "success"
                    });
                },
            });
        });
    </script>
@endsection
