@extends('layouts.master')
@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- RECENT PURCHASES -->
        <div class="panel panel-headline">
            <div class="panel-heading">
                <h3 class="panel-title">Laporan Transaksi Perbulan</h3>
                {{-- <div class="right">
                    <button type="button" data-toggle="modal" data-target="#modalInput"><i class="far fa-plus"></i>&nbsp; Input Transaksi</button>
                </div> --}}
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <p>Periode transaksi</p>
                        <div class="input-group">
                            <input class="form-control month-picker" id="periode-transaksi" type="text" value="{{date('F Y')}}" readonly>
                            <span class="input-group-btn"><button class="btn btn-primary" type="button" id="search-transaksi"><i class="fas fa-search"></i></button></span>
                        </div>
                    </div>
                </div>
                <br><br>
                <div id="data-transaksi">
                    <div class="loader">
                        <i class="fas fa-ban" style="font-size: 5rem; opacity: .5"></i>
                        <h5 style="margin-top: 2.5rem; opacity: .75">Belum ada data yang dipilih</h5>
                    </div>
                </div>
            </div>
        </div>
        <!-- END RECENT PURCHASES -->
    </div>
</div>
@endsection
