$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
})

if (location.pathname == '/dashboard') {
    chartPendapatan()
}else if(location.pathname == '/kelola-data-penjualan'){
    dataPenjualan()
}

function chartPendapatan() {
    let mychart
    $.ajax({
        type:'get',
        url:'/data-pendapatan',
        success:function(response){
            console.log(response)
            let ctx = document.getElementById("data-penjualan-chart").getContext('2d')
            mychart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                    datasets: [
                        {
                            label: 'Pendapatan',
                            data: response.pendapatan,
                            borderColor: mainColor,
                            backgroundColor: mainColor
                        }
                    ]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    })

    
    $('.change-periode').on('click', function(){
        $('.change-periode').removeClass('active')
        $(this).addClass('active')
        let periode = $(this).data('periode')
        updateChartPendapatan(mychart, periode)
    })
}

function updateChartPendapatan(mychart, periode) {
    $.ajax({
        type:'get',
        url:'/data-pendapatan?tahun='+periode,
        success:function(response){
            $('#terjual').html(response.terjual)
            $('#totalPendapatan').html(response.totalPendapatan)
            mychart.data = {
                labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                datasets: [
                    {
                        label: 'Pendapatan',
                        data: response.pendapatan,
                        borderColor: mainColor,
                        backgroundColor: mainColor
                    }
                ]
            }
            mychart.update()
        }
    })
}

$('.periodePicker').datepicker({
    changeMonth: true,
    changeYear: true,
    showButtonPanel: true,
    dateFormat: 'MM yy',
    onClose: function (dateText, inst) {
        $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
    }
})

function dataPenjualan() {
    let table = $('#table-penjualan').DataTable({
        'ajax' : '/data-penjualan',
        'columns' : [
            {'data' : 'no'},
            {'data' : 'periode'},
            {'data' : 'stok_awal'},
            {'data' : 'stok_akhir'},
            {'data' : 'terjual'},
            {'data' : 'pendapatan'},
            {
                data:null,
                render:function(data, type, row) {
                    return `<button id="editData" class="btn-table-action edit" data-toggle="modal" data-target="#modalEditData"><i class="fas fa-cog"></i></button>`
                }
            }
        ]
    })

    $('#table-penjualan tbody').on('click', '[id*=editData]', function(){
        let data = table.row($(this).parents('tr')).data()
        
        $('#editId').val(data['no'])
        $('#editMonthYear').val(data['periode'])
        $('#editStokAwal').val(data['stok_awal'])
        $('#editStokAkhir').val(data['stok_akhir'])
        $('#editTerjual').val(data['terjual'])
        $('#editPendapatan').val(data['pendapatan'])
    })
}

$('#btn-input-data').on('click', function () {
    if ($('#stokAwal').val().length == 0) {
        alert('Masukkan Stok Awal')
    } else if ($('#stokAkhir').val().length == 0) {
        alert('Masukan Stok Akhir')
    } else if ($('#terjual').val().length == 0) {
        alert('Masukan Barang Terjual')
    } else if ($('#pendapatan').val().length == 0) {
        alert('Masukan Pendapatan')
    } else {
        $('#btn-input-data').attr('disabled', 'disabled')
        $.ajax({
            type: 'post',
            url: '/input-data-penjualan',
            data: {
                monthYear: $('#monthYear').val(),
                stokAwal: $('#stokAwal').val(),
                stokAkhir: $('#stokAkhir').val(),
                terjual: $('#terjual').val(),
                pendapatan: $('#pendapatan').val()
            },
            success: function (response) {
                if (response.response == 'success') {
                    $('#monthYear').removeClass('periodePicker')
                    $('#monthYear').val(response.lastPeriod)
                    $('#stokAwal').val('')
                    $('#stokAkhir').val('')
                    $('#terjual').val('')
                    $('#pendapatan').val('')
                    toastr.option = {
                        "timeout": "5000"
                    }
                    toastr["success"]("Data penjualan bulan " + response.monthYear + " berhasil diinput")
                    $('#btn-input-data').removeAttr('disabled')
                    $('#modalInput').modal('toggle')
                    $('#table-penjualan').DataTable().ajax.reload()
                } else if (response.response == 'failed') {
                    toastr.option = {
                        "timeout": "5000"
                    }
                    toastr["error"]("Data penjualan bulan " + response.monthYear + " sudah terdata, <br> Gagal menginput data")
                    $('#btn-input-data').removeAttr('disabled')
                }
            }
        })
    }
})

$('#btn-edit-data').on('click', function(){
    if ($('#editStokAwal').val().length == 0) {
        alert('Masukkan Stok Awal')
    } else if ($('#editStokAkhir').val().length == 0) {
        alert('Masukan Stok Akhir')
    } else if ($('#editTerjual').val().length == 0) {
        alert('Masukan Barang Terjual')
    } else if ($('#editPendapatan').val().length == 0) {
        alert('Masukan Pendapatan')
    } else {
        $('#btn-edit-data').attr('disabled', 'disabled')
        $.ajax({
            type: 'post',
            url: '/edit-data-penjualan',
            data: {
                id: $('#editId').val(),
                stokAwal: $('#editStokAwal').val(),
                stokAkhir: $('#editStokAkhir').val(),
                terjual: $('#editTerjual').val(),
                pendapatan: $('#editPendapatan').val()
            },
            success: function (response) {
                if (response == "success") {
                    toastr.option = {
                        "timeout": "5000"
                    }
                    toastr["success"]("Data penjualan bulan " + $('#editMonthYear').val() + " berhasil di edit")
                    $('#btn-edit-data').removeAttr('disabled')
                    $('#modalEditData').modal('toggle')
                    $('#table-penjualan').DataTable().ajax.reload()
                }else{
                    toastr.option = {
                        "timeout": "5000"
                    }
                    toastr["error"]("Gagal merubah data penjualan periode " + $('#editMonthYear').val())
                }
            }
        })
    }
})

$('#btn-prediksi-data').on('click', function () {
    if ($('#terjual').val().length == 0) {
        alert('Lengkapi form prediksi !')
    }else if($('#pendapatan').val().length == 0){
        alert('Lengkapi form prediksi !')
    }else{
        let data = {
            "periode": $('#prediksiPeriode').val(),
            "terjual": $('#terjual').val(),
            "pendapatan": $('#pendapatan').val(),
            "k": $('#knn').val(),
        }

        $('#hasil-prediksi').empty()
        $('#hasil-prediksi').append(`<div class="panel panel-headline" id="panel-prediksi-loading">
                                        <div class="loader">
                                            <div class="loader4"></div>
                                            <h5 style="margin-top: 2.5rem">Membuat data prediksi</h5>
                                        </div>
                                    </div>`)
        $('#hasil-prediksi').append(`<div class="panel panel-headline" id="panel-head-prediksi" style="display: none;"></div>`)
        setTimeout(() => {
            knn(data)
        }, 1000);
    }
})

function knn(data) {
    $.ajax({
        type: 'POST',
        url: '/knn',
        data: data,
        success:function(response){
            console.log(response);
            $('#panel-head-prediksi').empty()
            $('#panel-head-prediksi').append(`<div class="panel-heading">
                                                <h3 class="panel-title">Prediksi Klasifikasi Penjualan</h3>
                                            </div>
                                            <div class="panel-body" id="panel-body-prediksi-terjual">
                                                <p>Data Penjualan :</p>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Periode</th>
                                                                    <th>Produk Terjual</th>
                                                                    <th>Pendapatan</th>
                                                                    <th>Euclidean</th>
                                                                    <th>Klasifikasi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="data-penjualan">
                                                                
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <hr>
                                                <p>${response.k} - Nearest Neighbour :</p>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th>Periode</th>
                                                                    <th>Produk Terjual</th>
                                                                    <th>Pendapatan</th>
                                                                    <th>Euclidean</th>
                                                                    <th>Klasifikasi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="data-knearest">
                                                                
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <hr>
                                                <p>Hasil Prediksi :</p>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <table class="table table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>Periode</th>
                                                                    <th>Produk Terjual</th>
                                                                    <th>Pendapatan</th>
                                                                    <th></th>
                                                                    <th>Prediksi Klasifikasi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="data-hasil">
                                                                
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>`)

            $.each(response.dataPenjualan, function(i, v){
                let klasifikasi
                if (v.klasifikasi == "naik") {
                    klasifikasi = `<td style="color: green; width: 20%;"><i class="fas fa-chevron-up"></i> &nbsp; Naik</td>`
                }else if(v.klasifikasi == "turun"){
                    klasifikasi = `<td style="color: red; width: 20%;"><i class="fas fa-chevron-down"></i> &nbsp; Turun</td>`
                }

                $('#data-penjualan').append(`<tr>
                                                <td style="width: 20%">${v.periode}</td>
                                                <td style="width: 20%">${v.terjual}</td>
                                                <td style="width: 20%">${v.pendapatan}</td>
                                                <td style="width: 20%">${v.euclidean}</td>
                                                ${klasifikasi}
                                            </tr>`)
            })

            $.each(response.knearest, function(i, v){
                let klasifikasi
                if (v.klasifikasi == "naik") {
                    klasifikasi = `<td style="color: green; width: 20%;"><i class="fas fa-chevron-up"></i> &nbsp; Naik</td>`
                }else if(v.klasifikasi == "turun"){
                    klasifikasi = `<td style="color: red; width: 20%;"><i class="fas fa-chevron-down"></i> &nbsp; Turun</td>`
                }

                $('#data-knearest').append(`<tr>
                                                <td style="width: 20%">${v.periode}</td>
                                                <td style="width: 20%">${v.terjual}</td>
                                                <td style="width: 20%">${v.pendapatan}</td>
                                                <td style="width: 20%">${v.euclidean}</td>
                                                ${klasifikasi}
                                            </tr>`)
            })

            let klasifikasi
            if (response.prediksi.result == "naik") {
                klasifikasi = `<td style="color: green; width: 20%;"><i class="fas fa-chevron-up"></i> &nbsp; Naik</td>`
            }else if(response.prediksi.result == "turun"){
                klasifikasi = `<td style="color: red; width: 20%;"><i class="fas fa-chevron-down"></i> &nbsp; Turun</td>`
            }

            $('#data-hasil').append(`<tr>
                                        <td style="width: 20%;">${response.prediksi.periode}</td>
                                        <td style="width: 20%;">${response.prediksi.terjual}</td>
                                        <td style="width: 20%;">${response.prediksi.pendapatan}</td>
                                        <td style="width: 20%;"></td>
                                        ${klasifikasi}
                                    </tr>`)

            $('#btn-prediksi-data').removeAttr('disabled')
            $('#panel-prediksi-loading').remove()
            $('#panel-head-prediksi').show()
        }
    })
}

