@extends('layouts.master')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container-fluid">
        <div class="row">
            <h1 class="h3 mb-4 text-gray-800 col-md-12">Masukan RFQ</h1>
            <form id="input-form" onsubmit="submitForm(event)">
                {{ csrf_field() }}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Nama Vendor</label>
                    <div class="col-sm-10">
                        <div class="dropdown">
                            <select class="form-select" name="vendor" id="vendor-select">
                                <option value="{{ $vendor->id }}">{{ $vendor->nama_vendor }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Input fields (hidden by default) -->
                <div id="input-fields">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Pilih Bahan</label>
                        <div class="col-sm-10">
                            <div class="dropdown">
                                <select class="form-select" name="bahan" id="bahan-select">
                                    <option selected disabled>-- Pilih Bahan --</option>
                                    @foreach ($bahan as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama_produk }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Banyak Bahan</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="qty" id="qty">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Harga</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="harga" id="harga">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" id="submit-button">Tambah Bahan</button>
                </div>
            </form>

            <!-- Table (hidden by default) -->
            <div id="rfq-table">
                <div class="container-fluid mt-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="h3 mt-3 text-gray-800 col-md-12">RFQ List</h5>
                            <table class="table table-bordered " id="rfq-table">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Bahan</th>
                                        <th scope="col">Banyak Bahan</th>
                                        <th scope="col">Harga</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="rfq-table-body">
                                    @php
                                        $totalHarga = 0;
                                    @endphp

                                    @foreach ($rfq_list as $item)
                                        <tr>
                                            <th scope="row">{{ $loop->iteration }}</th>
                                            <td>{{ $item->produk->nama_produk }}</td>
                                            <td>{{ $item->qty }}</td>
                                            <td>{{ $item->harga }}</td>
                                            <td>
                                                <button class="btn btn-danger btn-sm"
                                                    onclick="deleteItem({{ $item->id }})">Hapus</button>
                                            </td>
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
            <form action="{{ route('rfq.update.proses', $findRfq->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="container-fluid mt-4">
                    <div class="card">
                        <div class="card-body mt-3">
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
                                <label class="col-sm-2 col-form-label">Pembayaran</label>
                                <div class="col-sm-10">
                                    <select class="form-select" name="pembayaran" id="pembayaran-select">
                                        <option selected disabled>-- Pilih Pembayaran --</option>
                                        <option value="1"
                                            {{ isset($findRfq) && $findRfq->pembayaran == 1 ? 'selected' : '' }}>Cash
                                        </option>
                                        <option value="2"
                                            {{ isset($findRfq) && $findRfq->pembayaran == 2 ? 'selected' : '' }}>Transfer
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-primary" id="submit-button">Tambah RFQ</button>
                        <a href="{{ route('tampilBom') }}" class="btn btn-danger">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        function submitForm(event) {
            event.preventDefault(); // Mencegah reload halaman

            const vendorId = document.getElementById('vendor-select').value;
            const bahanId = document.getElementById('bahan-select').value;
            const qty = document.getElementById('qty').value;
            const harga = document.getElementById('harga').value;

            // Validasi input
            if (!vendorId || !bahanId || !qty || !harga) {
                alert('Semua field harus diisi!');
                return;
            }

            const data = {
                vendor: vendorId,
                bahan: bahanId,
                qty: qty,
                harga: harga
            };

            // Tambahkan indikator loading (opsional)
            document.getElementById('submit-button').disabled = true;

            fetch('/rfq-list/data/input/proses', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Gagal menambahkan data.');
                    }
                    return response.json();
                })
                .then(result => {
                    console.log('Data berhasil ditambahkan:', result);
                    alert('Data berhasil ditambahkan.');

                    // Reset input fields (hanya input qty dan harga)
                    document.getElementById('qty').value = '';
                    document.getElementById('harga').value = '';

                    // Load ulang tabel RFQ
                    window.location.reload();
                })
                .catch(error => {
                    console.error('Error saat menambahkan data:', error);
                    alert('Terjadi kesalahan saat menambahkan data.');
                })
                .finally(() => {
                    // Hilangkan indikator loading
                    document.getElementById('submit-button').disabled = false;
                });
        }

        function deleteItem(id) {
            console.log('Memanggil deleteItem dengan ID:', id); // Debug log
            if (confirm('Apakah Anda yakin ingin menghapus item ini?')) {
                fetch(`/rfq-list/delete/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Gagal menghapus data.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log(data);
                        alert('Data berhasil dihapus.');

                        window.location.reload();
                    })
                    .catch(error => {
                        console.error('Error menghapus data:', error);
                        alert('Terjadi kesalahan saat menghapus data.');
                    });
            }
        }
    </script>
@endsection
