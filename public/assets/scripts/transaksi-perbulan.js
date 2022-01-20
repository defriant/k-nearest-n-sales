
$('#search-transaksi').on('click', function(){
    if ($('#periode-transaksi').val().length == 0) {
        alert('Pilih periode transaksi')
    }else{
        ajaxRequest.post({
            "url": "/transaksi-perbulan/get",
            "data": {
                "periode": $('#periode-transaksi').val()
            }
        }).then(function(result){
            if (result.response == "success") {
                $('#data-transaksi').html(`<br>
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Periode</th>
                                                        <th>Produk</th>
                                                        <th>Terjual</th>
                                                        <th>Pendapatan</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbody-transaksi">
                                                    
                                                </tbody>
                                                <thead>
                                                    <tr>
                                                        <th style="padding-top: 1.5rem; padding-bottom: 1.5rem">Total</th>
                                                        <th></th>
                                                        <th id="total-terjual" style="padding-top: 1.5rem; padding-bottom: 1.5rem"></th>
                                                        <th id="total-pendapatan" style="padding-top: 1.5rem; padding-bottom: 1.5rem"></th>
                                                    </tr>
                                                </thead>
                                            </table>
                                            <br><br>`)

                let tbodyTransaksi = ``
                $.each(result.data, function(i, v){
                    tbodyTransaksi = tbodyTransaksi + `<tr>
                                                            <td>${v.periode}</td>
                                                            <td>${v.produk}</td>
                                                            <td>${v.terjual}</td>
                                                            <td>${v.pendapatan}</td>
                                                        </tr>`
                })

                $('#tbody-transaksi').html(tbodyTransaksi)
                $('#total-terjual').html(result.total_terjual)
                $('#total-pendapatan').html(result.total_pendapatan)
            }
        })
    }
})