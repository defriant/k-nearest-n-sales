@extends('layouts.master')
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-headline">
            <div class="panel-heading">
                <h3 class="panel-title">Prediksi Penjualan</h3>
            </div>
            <div class="panel-body">
                <p>Periode</p>
                <select id="prediksiPeriode" class="form-control" style="width: 70%">
                    @foreach ($prediksiPeriode as $p)
                        <option value="{{ $p }}">{{ $p }}</option>
                    @endforeach
                </select>
                <br>
                <p>Barang Terjual</p>
                <input type="number" id="terjual" class="form-control" style="width: 70%">
                <br>
                <p>Pendapatan</p>
                <div class="input-group" style="width: 70%">
                    <span class="input-group-addon">Rp.</span>
                    <input class="form-control" id="pendapatan" type="number">
                </div>
                <br>
                <p>K - Nearest Neighbour</p>
                <select id="knn" class="form-control" style="width: 70%">
                    <option value="3">3 - Nearest Neighbour</option>
                    <option value="5">5 - Nearest Neighbour</option>
                    <option value="7">7 - Nearest Neighbour</option>
                    <option value="9">9 - Nearest Neighbour</option>
                </select>
                <br>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="text-right"><button id="btn-prediksi-data" class="btn btn-primary">Mulai
                            Prediksi</button></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="hasil-prediksi">
    
</div>
@endsection
