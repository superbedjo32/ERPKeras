@extends('layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <div class="card">
        <div class="card-body">
            <br>
            <table class="table table-bordered datatable">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama Vendor</th>
                        <th scope="col">Tanggal Transaksi</th>
                        <th scope="col">Total Harga</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($rfqs->count())
                        @foreach ($rfqs as $item)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $item->nama_vendor }}</td>
                                <td>{{ $item->tgl_transaksi }}</td>
                                <td>{{ $item->total_harga }}</td>
                                <td>
                                    @if ($item->status == 1)
                                        <span class="badge text-bg-warning">Konfirmasi Pembayaran</span>
                                    @elseif($item->status == 2)
                                        <a href="{{ url('/po/data/list/' . $item->id) }}"
                                            class="btn btn-info bi bi-cart-check-fill" role="button"> Bayar</a>
                                    @elseif($item->status == 3)
                                        <!-- Ambil ID PO terkait dari relasi po -->
                                        @foreach ($item->po as $po)
                                            <a href="{{ url('/po/data/report/' . $po->id) }}"
                                                class="btn btn-light bi bi-printer-fill" role="button"> Report</a>
                                        @endforeach
                                    @endif
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

@endsection
