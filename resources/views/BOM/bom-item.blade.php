@extends('layouts.master')
@section('content')
<div class="container">
    <form action="{{ url('/bom/item_list/upload') }}" method="post" name="input-form" id="input-form">
        {{ csrf_field() }}
        <div class="form-group">
            <input type="hidden" class="form-control" name="id" id="kode_bom" value="{{$bom->kode_bom_list}}" readonly>
            <input type="hidden" class="form-control" name="id" id="kode_bom" value="{{$bom->id}}" readonly>
        </div>
        <div class="row mb-3">
            <label class="col-sm-2 col-form-label">Nama Produk</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="nama_produk" value="{{$bom->nama_produk}}" disabled>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-sm-2 col-form-label">Pilih Material</label>
            <div class="col-sm-10">
                <div class="dropdown">
                    <select class="form-select" name="kode_produk"> 
                        <option selected>-- Pilih Bahan --</option>
                        @if($materials->count())
                            @foreach($materials as $item)
                                <option value="{{$item->id}}" data-toggle="">{{$item->nama_produk}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-sm-2 col-form-label">Banyak Bahan</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="qty" id="quantity">
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-sm-2 col-form-label">Satuan</label>
            <div class="col-sm-10">
                <div class="dropdown">
                    <select class="form-select" name="satuan" id="satuan">
                        <option selected value=" ">-- Select Option --</option>
                        <option class="dropdown-item">M</option>
                        <option class="dropdown-item">Cm</option>
                        <option class="dropdown-item">Buah</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group mt-3">
            <button type="submit" name="simpan" class="btn btn-primary">Tambah Bahan</button>
            <a href="{{ route('tampilBom') }}" class="btn btn-danger">Batal</a>
            <a href="#" class="btn btn-success">Cetak</a>
        </div>
    </form>
</div>
<div class="container-fluid mt-4">
<div class="card">
    <div class="card-body">
        <br>
    <table class="table table-bordered" id="myTable">
        <thead>
            <tr>
                <th scope="col">No</th>
                <th scope="col">Kode</th>
                <th scope="col">Nama Bahan</th>
                <th scope="col">Banyak</th>
                <th scope="col">Satuan</th>
                <th scope="col">Harga Satuan</th>
                <th scope="col">Harga Total</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @if($list->count())
            @foreach($list as $item)
            <tr>
                <th scope="row">{{$loop->iteration}}</th>
                <td>{{$item->kode_bom}}</td>
                <td>{{$item->nama_produk}}</td>
                <td>{{$item->qty}}</td>
                <td>{{$item->satuan}}</td>
                <td>{{$item->harga}}</td>
                @php
                {{
                    $total = $item->harga * $item->qty;
                }}
                @endphp
                <td>{{$total}}</td>
                <td>
                    <a href="{{ url('/bom/delete_item_list/'.$item->kode_bom_list) }}" class="btn btn-danger delete-confirm bi bi-trash3-fill" role="button"></a>
                </td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <br>
    <div class="container-sm ">
        <div class="row"></div>
        <div class="row mt-auto p-2 bd-highlight">
            <label for="text_harga" class="font-weight-bold"> Total Harga : </label>
            <label for="total_harga" id="val">{{ $bom->total_harga }}</label>
        </div>
    </div>
</div>
</div>
</div>
@endsection