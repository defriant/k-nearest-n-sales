let tableProduk = $('#table-produk').DataTable({
    'ajax' : '/master-produk/get',
    'columns' : [
        {'data' : 'id'},
        {'data' : 'nama'},
        {'data' : 'harga'},
        {
            data:null,
            render:function(data, type, row) {
                return `<button id="editData" class="btn-table-action edit" data-toggle="modal" data-target="#modalEditData"><i class="fas fa-cog"></i></button> &nbsp;
                        <button id="deleteData" class="btn-table-action delete" data-toggle="modal" data-target="#modalDeleteData"><i class="fas fa-trash-alt"></i></button>`
            }
        }
    ]
})

$('#table-produk tbody').on('click', '[id*=editData]', function(){
    let data = tableProduk.row($(this).parents('tr')).data()
    let harga = data['harga']
    harga = harga.replace(/Rp. /g, '')
    harga = harga.replace(/,/g, '')
    
    $('#edit-id').val(data['id'])
    $('#edit-nama').val(data['nama'])
    $('#edit-harga').val(harga)
})

$('#table-produk tbody').on('click', '[id*=deleteData]', function(){
    let data = tableProduk.row($(this).parents('tr')).data()
    
    $('#delete-warning-message').html(`Hapus data produk ${data['nama']}`)
    $('#delete-id').val(data['id'])
})

$('#btn-input-data').on('click', function(){
    if ($('#input-nama').val().length == 0) {
        alert('Masukkan nama produk')
    }else if ($('#input-harga').val().length == 0) {
        alert('Masukkan harga prduk')
    }else{
        let params = {
            "nama": $('#input-nama').val(),
            "harga": $('#input-harga').val()
        }
    
        ajaxRequest.post({
            "url": "/master-produk/input",
            "data": params
        }).then(function(result){
            if (result.response == "success") {
                $('#modalInput').modal('hide')
                $('#input-nama').val('')
                $('#input-harga').val('')
                toastr.option = {
                    "timeout": "5000"
                }
                toastr["success"](result.message)
                $('#table-produk').DataTable().ajax.reload()
            }
        })
    }
})

$('#btn-edit-data').on('click', function(){
    if ($('#edit-nama').val().length == 0) {
        alert('Masukkan nama produk')
    }else if ($('#edit-harga').val().length == 0) {
        alert('Masukkan harga prduk')
    }else{
        let params = {
            "id": $('#edit-id').val(),
            "nama": $('#edit-nama').val(),
            "harga": $('#edit-harga').val()
        }
    
        ajaxRequest.post({
            "url": "/master-produk/update",
            "data": params
        }).then(function(result){
            if (result.response == "success") {
                $('#modalEditData').modal('hide')
                toastr.option = {
                    "timeout": "5000"
                }
                toastr["success"](result.message)
                $('#table-produk').DataTable().ajax.reload()
            }
        })
    }
})

$('#btn-delete-data').on('click', function(){
    let params = {
        "id": $('#delete-id').val()
    }

    ajaxRequest.post({
        "url": "/master-produk/delete",
        "data": params
    }).then(function(result){
        if (result.response == "success") {
            $('#modalDeleteData').modal('hide')
            toastr.option = {
                "timeout": "5000"
            }
            toastr["success"](result.message)
            $('#table-produk').DataTable().ajax.reload()
        }
    })
})