@extends('layouts.master')
@section('content')
    <div class="container-fluid mt-4">
        <div class="container-fluid mb-3">
            <a href="/bom-input" class="btn btn-primary">Create BOM</a>
        </div>
        <div class="card">
            <div class="card-body">
                <br>
                <table class="table table-bordered datatable">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Kode Bom</th>
                            <th scope="col">Nama Produk</th>
                            <th scope="col">Harga Produksi</th>
                            <th scope="col">Tanggal Buat</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($boms->count())
                            @foreach ($boms as $index => $bom)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $bom->kode_bom }}</td>
                                    <td>{{ $bom->produk_nama }}</td>
                                    <td>{{ $bom->tanggal }}</td>
                                    <td>Rp {{ number_format($bom->total_harga, 0, ',', '.') }}</td>
                                    {{-- <td>
                                        @foreach ($bom->bahans as $bahan)
                                            <span class="badge bg-primary">{{ $bahan->nama_produk }}</span>
                                        @endforeach
                                    </td> --}}
                                    <td>
                                        <a href="{{ url('/bom/item_list/' . $bom->id) }}"
                                            class="btn btn-primary">Detail</a>
                                        {{-- <a href="{{ url('/bom/item_list/' . $item->id) }}"
                                            class="btn btn-warning bi bi-pencil-square" role="button"> </a> --}}
                                        <a href="{{ url('/bom/delete/' . $bom->id) }}"
                                            class="btn btn-danger delete-confirm bi bi-trash3-fill" role="button"> </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7"> No record found </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
