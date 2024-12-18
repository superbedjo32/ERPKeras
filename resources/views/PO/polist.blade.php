@extends('layouts.master')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container-fluid">
        <div class="row">
            <h1 class="h3 mb-4 text-gray-800 col-md-12">Detail Payment Order</h1>
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @elseif(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Nama Vendor</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="nama_vendor" id="nama_vendor"
                        value="{{ $vendor->nama_vendor }}" readonly>
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Telpon</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="telpon" id="telpon" value="{{ $vendor->telpon }}"
                        readonly>
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Alamat</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="alamat" id="alamat" value="{{ $vendor->alamat }}"
                        readonly>
                </div>
            </div>

            <!-- Table (hidden by default) -->
            <div id="rfq-table">
                <div class="container-fluid mt-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="h3 mt-3 text-gray-800 col-md-12">Bahan List</h5>
                            <table class="table table-bordered " id="rfq-table">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Kode Bahan</th>
                                        <th scope="col">Bahan</th>
                                        <th scope="col">Banyak Bahan</th>
                                        <th scope="col">Harga</th>
                                        <th scope="col">Sub Harga</th>
                                    </tr>
                                </thead>
                                <tbody id="rfq-table-body">
                                    @php
                                        $totalHarga = 0;
                                    @endphp

                                    @foreach ($rfq_list as $item)
                                        <tr>
                                            <th scope="row">{{ $loop->iteration }}</th>
                                            <td>{{ $item->produk->id_reference }}</td>
                                            <td>{{ $item->produk->nama_produk }}</td>
                                            <td>{{ $item->qty }}</td>
                                            <td>{{ $item->harga }}</td>
                                            <td>{{ $item->harga * $item->qty }}</td>
                                        </tr>

                                        @php
                                            $totalHarga += $item->harga * $item->qty;
                                        @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid mt-4">
                <div class="card">
                    <div class="card-body mt-3">
                        <h5 class="h3 mb-3 text-gray-800 col-md-12">Payment</h5>
                        <!-- Vendor ID -->
                        <input type="hidden" id="vendor-id" name="vendor_id" readonly class="form-control"
                            value="{{ $findRfq->vendor_id }}">

                        <!-- Total Harga -->
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Total Harga</label>
                            <div class="col-sm-10">
                                <input type="text" id="val" name="total_harga" readonly class="form-control"
                                    value="Rp. {{ number_format($totalHarga, 0, ',', '.') }}">
                            </div>
                        </div>

                        <!-- Pembayaran -->
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Metode Pembayaran</label>
                            <div class="col-sm-10">
                                <input type="text" id="val" name="metode_pembayaran" readonly class="form-control"
                                    value="{{ $findRfq->pembayaran == 1 ? 'Cash' : ($findRfq->pembayaran == 2 ? 'Transfer' : '-') }}"
                                    readonly>

                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('po.input.proses') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                        <div class="card-body mt-3">
                            <!-- RFQ ID -->
                            <input type="hidden" id="vendor-id" name="rfq_id" readonly class="form-control"
                                value="{{ $findRfq->id }}">

                            <!-- Pembayaran -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Pembayaran</label>
                                <div class="col-sm-10">
                                    @if ($findRfq->pembayaran == 1)
                                        <input type="text" id="val" name="pembayaran"class="form-control"
                                            placeholder="Masukkan Pembayaran">
                                    @elseif ($findRfq->pembayaran == 2)
                                        <div class="input-group mb-3">
                                            <input type="file" class="form-control" id="inputGroupFile02">
                                            <label class="input-group-text" name="image"
                                                for="inputGroupFile02">Upload</label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-primary" id="submit-button">Bayar</button>
                        <a href="{{ route('tampilBom') }}" class="btn btn-danger">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
