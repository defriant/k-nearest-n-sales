
let periode = ''
let produkOption = $('#input-produk').html()

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
            // $('#data-transaksi').prepend(`<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalInput"><i class="far fa-plus"></i>&nbsp; Input Transaksi</button>
            //                                 <br><br>`)
            $('#data-transaksi').html(`<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalInput"><i class="far fa-plus"></i>&nbsp; Input Transaksi</button>
                                        <br><br>
                                        <div class="loader">
                                            <i class="fas fa-ban" style="font-size: 5rem; opacity: .5"></i>
                                            <h5 style="margin-top: 2.5rem; opacity: .75">Belum ada transaksi untuk periode ini</h5>
                                        </div>`)
        }else if (result.data.length > 0) {
            $('#data-transaksi').html(`<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalInput"><i class="far fa-plus"></i>&nbsp; Input Transaksi</button>
                                        <br><br>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>ID Transaksi</th>
                                                    <th>Jam</th>
                                                    <th>Terjual</th>
                                                    <th>Total</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody-transaksi">
                                                
                                            </tbody>
                                        </table>
                                        <br><br>`)
            
            let tbodyTransaksi = ``
            let no = 1
            $.each(result.data, function(i, v){
                let hrs = new Date(v.created_at).getHours()
                if (hrs < 10) {
                    hrs = "0" + hrs
                }

                let mnt = new Date(v.created_at).getMinutes()
                if (mnt < 10) {
                    mnt = "0" + mnt
                }

                tbodyTransaksi = tbodyTransaksi + `<tr>
                                                        <td>${no}</td>
                                                        <td>${v.id}</td>
                                                        <td>${hrs}:${mnt}</td>
                                                        <td>${v.terjual}</td>
                                                        <td>${v.total}</td>
                                                        <td style="width: 15%; text-align: center;">
                                                            <button id="editData" class="btn-table-action detail detail-transaksi" data-toggle="modal" data-target="#modalDetail" data-id="${v.id}">Detail</button>
                                                        </td>
                                                    </tr>`
                no = no + 1
            })

            $('#tbody-transaksi').html(tbodyTransaksi)
            detailTransaksiFn()
        }
    })
}

function detailTransaksiFn() {
    $('.detail-transaksi').unbind('click')
    $('.detail-transaksi').on('click', function(){
        let params = {
            "id": $(this).data('id')
        }

        $('#modal-body-detail-transaksi').html(`<div class="loader">
                                                    <div class="loader4"></div>
                                                    <h5 style="margin-top: 2.5rem">Loading data</h5>
                                                </div>`)

    ajaxRequest.post({
        "url": "/transaksi/detail",
        "data": params
    }).then(function(result){
        let tproduk = ``
        $.each(result.produk, function(i, v){
            tproduk = tproduk + `<tr>
                                    <td>${v.produk}</td>
                                    <td>${v.harga}</td>
                                    <td>${v.jumlah}</td>
                                    <td>${v.total}</td>
                                </tr>`
        })

        $('#modal-body-detail-transaksi').html(`<p>Invoice <span style="float: right">${result.invoice}</span></p>
                                                <p>Tanggal <span style="float: right">${result.tanggal}</span></p>
                                                <p>Waktu <span style="float: right">${result.waktu}</span></p>
                                                <br>
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Produk</th>
                                                            <th>Harga</th>
                                                            <th>Jumlah</th>
                                                            <th>Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="detail-transaksi-body">
                                                        ${tproduk}
                                                    </tbody>
                                                    <thead>
                                                        <tr>
                                                            <th>Total Belanja</th>
                                                            <th></th>
                                                            <th></th>
                                                            <th>${result.total}</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                                <br>`)
        })
    })
}

$('.transaksi-tambah-produk').on('click', function(){
    $('#transaksi-produk').append(`<div class="row" style="margin-bottom: 2rem">
                                        <div class="col-md-7">
                                            <p>Produk</p>
                                            <select class="form-control input-produk">
                                                <option value=""></option>
                                                ${produkOption}
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <p>Jumlah</p>
                                            <input type="number" class="form-control input-jumlah">
                                        </div>
                                        <div class="col-md-2">
                                            <p>&nbsp;</p>
                                            <button class="btn-table-action delete transaksi-delete-produk"><i class="fas fa-trash-alt"></i></button>
                                        </div>
                                    </div>`)
    selectProduk()
    btnDeleteProduk()
})

function btnDeleteProduk() {
    $('.transaksi-delete-produk').unbind('click')
    $('.transaksi-delete-produk').on('click', function(){
        $(this).parent().parent().remove()
        selectProduk()
        btnDeleteProduk()
    })
}

function selectProduk() {
    $('.input-produk').unbind('change')
    $('.input-produk').on('change', function(){
        $(this).parent().parent().removeClass('invalid')
    })

    $('.input-jumlah').unbind('input')
    $('.input-jumlah').on('input', function(){
        $(this).parent().parent().removeClass('invalid')
    })
}

$('#btn-input-data').on('click', function(){
    let produk = []
    let valid = true

    $.each($('.input-produk'), function(i, v){
        let thisProduk = $(this)
        if (thisProduk.val().length == 0  || $('.input-jumlah').eq(i).val().length == 0) {
            valid = false
            thisProduk.parent().parent().addClass('invalid')
        }else{
            $.each(produk, function(pIndex, pVal){
                if (pVal.produk == thisProduk.val()) {
                    valid = false
                    thisProduk.parent().parent().addClass('invalid')
                    alert('Duplikat input produk')
                }
            })
            produk.push({
                "produk": $(this).val(),
                "jumlah": $('.input-jumlah').eq(i).val()
            })
        }
    })

    if (valid) {
        let params = {
            "tanggal": $('#periode-transaksi').val(),
            "produk": produk
        }

        ajaxRequest.post({
            "url": "/transaksi/input",
            "data": params
        }).then(res => {
                $('#modalInput').modal('hide')
                getTransaksi($('#periode-transaksi').val())
                toastr.option = {
                    "timeout": "5000"
                }
                toastr["success"](res.message)
                $('#transaksi-produk').html(`<div class="row" style="margin-bottom: 2rem">
                                            <div class="col-md-7">
                                                <p>Produk</p>
                                                <select class="form-control input-produk">
                                                    <option value=""></option>
                                                    ${produkOption}
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <p>Jumlah</p>
                                                <input type="number" class="form-control input-jumlah">
                                            </div>
                                            <div class="col-md-2">
                                                <p>&nbsp;</p>
                                            </div>
                                        </div>`)
        })
    }

    // if ($('#input-produk').val().length == 0) {
    //     alert('Pilih produk')
    // }else if($('#input-terjual').val().length == 0){
    //     alert('Masukkan jumlah barang terjual')
    // }else{
    //     let data = {
    //         "tanggal": periode,
    //         "id_produk": $('#input-id').val(),
    //         "terjual": $('#input-terjual').val()
    //     }

    //     ajaxRequest.post({
    //         "url": "/transaksi/input",
    //         "data": data
    //     }).then(function(result){
    //         if (result.response == "success") {
    //             getTransaksi(periode)
    //             toastr.option = {
    //                 "timeout": "5000"
    //             }
    //             toastr["success"](result.message)
    //             $('#input-produk').val('')
    //             $('#input-id').val('')
    //             $('#input-terjual').val('')
    //             $('#modalInput').modal('hide')
    //         }
    //     })
    // }
})