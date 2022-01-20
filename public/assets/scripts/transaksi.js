
let periode = ''

getTransaksi($('#periode-transaksi').val())

$('#search-transaksi').on('click', function(){
    if ($('#periode-transaksi').val().length == 0) {
        alert('Masukkan periode transaksi')
    }else{
        $('#data-transaksi').html(`<div class="loader">
                                        <div class="loader4"></div>
                                        <h5 style="margin-top: 2.5rem">Loading data</h5>
                                    </div>`)
        getTransaksi($('#periode-transaksi').val())
    }
})

function getTransaksi(tgl) {
    periode = tgl
    ajaxRequest.post({
        "url": "/transaksi/get",
        "data": {
            "tanggal": tgl
        }
    }).then(function(result){
        if (result.data.length == 0) {
            $('#data-transaksi').prepend(`<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalInput"><i class="far fa-plus"></i>&nbsp; Input Transaksi</button>
                                            <br><br>`)
            $('.loader').html(`<i class="fas fa-ban" style="font-size: 5rem; opacity: .5"></i>
                                <h5 style="margin-top: 2.5rem; opacity: .75">Belum ada transaksi untuk periode ini</h5>`)
        }else if (result.data.length > 0) {
            $('#data-transaksi').html(`<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalInput"><i class="far fa-plus"></i>&nbsp; Input Transaksi</button>
                                        <br><br>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Produk</th>
                                                    <th>Harga</th>
                                                    <th>Terjual</th>
                                                    <th>Total Harga</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-transaksi">
                                                
                                            </tbody>
                                        </table>
                                        <br><br>`)
            
            let tbodyTransaksi = ``
            let no = 1
            $.each(result.data, function(i, v){
                tbodyTransaksi = tbodyTransaksi + `<tr>
                                                        <td>${no}</td>
                                                        <td>${v.produk}</td>
                                                        <td>${v.harga}</td>
                                                        <td>${v.terjual}</td>
                                                        <td>${v.total_harga}</td>
                                                    </tr>`
                no = no + 1
            })

            $('#tbody-transaksi').html(tbodyTransaksi)
        }
    })
}

$('#input-produk').on('change', function(){
    $('#input-id').val($(this).val())
})

$('#btn-input-data').on('click', function(){
    if ($('#input-produk').val().length == 0) {
        alert('Pilih produk')
    }else if($('#input-terjual').val().length == 0){
        alert('Masukkan jumlah barang terjual')
    }else{
        let data = {
            "tanggal": periode,
            "id_produk": $('#input-id').val(),
            "terjual": $('#input-terjual').val()
        }

        ajaxRequest.post({
            "url": "/transaksi/input",
            "data": data
        }).then(function(result){
            if (result.response == "success") {
                getTransaksi(periode)
                toastr.option = {
                    "timeout": "5000"
                }
                toastr["success"](result.message)
                $('#input-produk').val('')
                $('#input-id').val('')
                $('#input-terjual').val('')
                $('#modalInput').modal('hide')
            }
        })
    }
})