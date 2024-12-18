@extends('layouts.master')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container-fluid">
        <div class="row">
            <h1 class="h3 mb-4 text-gray-800 col-md-12">Masukan RFQ</h1>
            <form id="input-form" onsubmit="submitForm(event)">
                {{ csrf_field() }}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Pilih Vendor</label>
                    <div class="col-sm-10">
                        <div class="dropdown">
                            <select class="form-select" name="vendor" id="vendor-select">
                                <option selected>-- Pilih Vendor --</option>
                                @foreach ($vendor as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_vendor }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Input fields (hidden by default) -->
                {{-- <div id="input-fields" style="display: none;">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Pilih Bahan</label>
                        <div class="col-sm-10">
                            <div class="dropdown">
                                <select class="form-select" name="bahan" id="bahan-select">
                                    <option selected disabled>-- Pilih Bahan --</option>
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
                </div> --}}
            </form>

            <!-- Table (hidden by default) -->
            {{-- <div id="rfq-table" style="display: none;">
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
                                    <!-- Data akan diisi oleh JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> --}}

            <div id="vendor-form-container"></div>

        </div>
    </div>
    <script>
        // function submitForm(event) {
        //     event.preventDefault(); // Mencegah reload halaman

        //     const vendorId = document.getElementById('vendor-select').value;
        //     const bahanId = document.getElementById('bahan-select').value;
        //     const qty = document.getElementById('qty').value;
        //     const harga = document.getElementById('harga').value;

        //     // Validasi input
        //     if (!vendorId || !bahanId || !qty || !harga) {
        //         alert('Semua field harus diisi!');
        //         return;
        //     }

        //     const data = {
        //         vendor: vendorId,
        //         bahan: bahanId,
        //         qty: qty,
        //         harga: harga
        //     };

        //     // Tambahkan indikator loading (opsional)
        //     document.getElementById('submit-button').disabled = true;

        //     fetch('/rfq-list/data/input/proses', {
        //             method: 'POST',
        //             headers: {
        //                 'Content-Type': 'application/json',
        //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        //             },
        //             body: JSON.stringify(data)
        //         })
        //         .then(response => {
        //             if (!response.ok) {
        //                 throw new Error('Gagal menambahkan data.');
        //             }
        //             return response.json();
        //         })
        //         .then(result => {
        //             console.log('Data berhasil ditambahkan:', result);
        //             alert('Data berhasil ditambahkan.');

        //             // Reset input fields (hanya input qty dan harga)
        //             document.getElementById('qty').value = '';
        //             document.getElementById('harga').value = '';

        //             // Load ulang tabel RFQ
        //             loadRfqTable(vendorId);
        //         })
        //         .catch(error => {
        //             console.error('Error saat menambahkan data:', error);
        //             alert('Terjadi kesalahan saat menambahkan data.');
        //         })
        //         .finally(() => {
        //             // Hilangkan indikator loading
        //             document.getElementById('submit-button').disabled = false;
        //         });
        // }

        // function loadRfqTable(vendorId) {
        //     const tableBody = document.getElementById('rfq-table-body');

        //     // Tambahkan indikator loading
        //     tableBody.innerHTML = '<tr><td colspan="5">Loading...</td></tr>';

        //     fetch(`/get-rfq-by-vendor/${vendorId}`)
        //         .then(response => {
        //             if (!response.ok) {
        //                 throw new Error('Gagal memuat data RFQ.');
        //             }
        //             return response.json();
        //         })
        //         .then(data => {
        //             console.log('Data RFQ:', data);
        //             tableBody.innerHTML = ''; // Kosongkan tabel
        //             let totalHarga = 0;
        //             let vendorId = '';

        //             if (data.length > 0) {
        //                 let index = 1;
        //                 data.forEach(item => {
        //                     const row = `
        //                 <tr>
        //                     <td>${index++}</td>
        //                     <td>${item.produk.nama_produk}</td>
        //                     <td>${item.qty}</td>
        //                     <td>${item.harga}</td>
        //                     <td>
        //                         <button class="btn btn-danger btn-sm" onclick="deleteItem(${item.id})">Hapus</button>
        //                     </td>
        //                 </tr>
        //             `;
        //                     tableBody.innerHTML += row;
        //                     totalHarga += item.harga * item.qty;
        //                     vendorId = item.vendor_id;
        //                 });
        //                 const totalHargaInput = document.getElementById('val');
        //                 totalHargaInput.value =
        //                     `Rp. ${totalHarga.toLocaleString()}`;
        //                 const vendorIdInput = document.getElementById('vendor-id');
        //                 vendorIdInput.value = vendorId;
        //             } else {
        //                 tableBody.innerHTML = '<tr><td colspan="5">Tidak ada data RFQ untuk vendor ini.</td></tr>';
        //             }
        //         })
        //         .catch(error => {
        //             console.error('Error memuat data RFQ:', error);
        //             tableBody.innerHTML = '<tr><td colspan="5">Terjadi kesalahan saat memuat data RFQ.</td></tr>';
        //         });
        // }

        // function deleteItem(id) {
        //     console.log('Memanggil deleteItem dengan ID:', id); // Debug log
        //     if (confirm('Apakah Anda yakin ingin menghapus item ini?')) {
        //         fetch(`/rfq-list/delete/${id}`, {
        //                 method: 'DELETE',
        //                 headers: {
        //                     'Content-Type': 'application/json',
        //                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
        //                         .getAttribute('content')
        //                 }
        //             })
        //             .then(response => {
        //                 if (!response.ok) {
        //                     throw new Error('Gagal menghapus data.');
        //                 }
        //                 return response.json();
        //             })
        //             .then(data => {
        //                 console.log(data);
        //                 alert('Data berhasil dihapus.');

        //                 // Reload tabel setelah penghapusan
        //                 const vendorId = document.getElementById('vendor-select').value;
        //                 loadRfqTable(vendorId); // Pastikan fungsi ini ada
        //             })
        //             .catch(error => {
        //                 console.error('Error menghapus data:', error);
        //                 alert('Terjadi kesalahan saat menghapus data.');
        //             });
        //     }
        // }
        document.getElementById('vendor-select').addEventListener('change', function() {
            const vendorId = this.value;
            console.log(vendorId);

            // const bahanSelect = document.getElementById('bahan-select');
            // const inputFields = document.getElementById('input-fields');
            const vendorFormContainer = document.getElementById(
                'vendor-form-container'); // This is where we will append the second form

            // Reset input fields
            // bahanSelect.innerHTML = '<option selected>-- Memuat Bahan --</option>';

            // Show/hide input fields based on vendor selection
            if (vendorId) {
                // inputFields.style.display = 'block';

                // Fetch bahan
                // fetch(`/get-bahan-by-vendor/${vendorId}`)
                //     .then(response => response.json())
                //     .then(data => {
                //         bahanSelect.innerHTML = '<option selected>-- Pilih Bahan --</option>';
                //         data.forEach(item => {
                //             const option = document.createElement('option');
                //             option.value = item.id;
                //             option.textContent = item.nama_produk;
                //             bahanSelect.appendChild(option);
                //         });
                //     })
                //     .catch(error => {
                //         console.error('Error memuat bahan:', error);
                //         bahanSelect.innerHTML = '<option selected>-- Gagal Memuat Bahan --</option>';
                //     });

                // Create and append the second form dynamically
                const formHTML = `
                    <form action="{{ route('rfq.input.proses') }}" method="POST">
                        @csrf
                        <div class="container-fluid mt-2">
                            <div class="card">
                                <div class="card-body mt-3">
                                    <!-- Vendor ID -->
                                    <input type="text" id="vendor-id" name="vendor_id" readonly class="form-control" value="${vendorId}">

                                    <!-- Total Harga -->
                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label">Total Harga</label>
                                        <div class="col-sm-10">
                                            <input type="text" id="val" name="total_harga" readonly class="form-control" value="Rp 0">
                                        </div>
                                    </div>

                                    <!-- Pembayaran -->
                                    <div class="row mb-3">
                                        <label class="col-sm-2 col-form-label">Pembayaran</label>
                                        <div class="col-sm-10">
                                            <select class="form-select" name="pembayaran" id="pembayaran-select">
                                                <option selected disabled>-- Pilih Pembayaran --</option>
                                                <option value="1" {{ old('pembayaran') == '1' ? 'selected' : '' }}>Cash</option>
                                                <option value="2" {{ old('pembayaran') == '2' ? 'selected' : '' }}>Transfer</option>
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
                `;
                vendorFormContainer.innerHTML = formHTML; // Append the form

                // Fetch RFQ data and display it
                // fetch(`/get-rfq-by-vendor/${vendorId}`)
                //     .then(response => response.json())
                //     .then(data => {
                //         const tableBody = document.getElementById('rfq-table-body');
                //         tableBody.innerHTML = ''; // Kosongkan tabel sebelumnya
                //         let totalHarga = 0; // Inisialisasi total harga
                //         let vendorId = '';
                //         console.log(data);

                //         const table = document.getElementById('rfq-table');
                //         table.style.display = 'block'; // Pastikan tabel ditampilkan

                //         if (data.length > 0) {
                //             let index = 1;
                //             data.forEach(item => {
                //                 const row = `
                //                 <tr>
                //                     <td>${index++}</td>
                //                     <td>${item.produk.nama_produk}</td>
                //                     <td>${item.qty}</td>
                //                     <td>${item.harga}</td>
                //                     <td>
                //                         <button class="btn btn-danger btn-sm" onclick="deleteItem(${item.id})">Hapus</button>
                //                     </td>
                //                 </tr>
                //             `;
                //                 tableBody.innerHTML += row; // Tambahkan baris ke tabel

                //                 // Hitung total harga
                //                 totalHarga += item.harga * item.qty;
                //                 vendorId = item.vendor_id;
                //             });
                //         } else {
                //             // Jika tidak ada data, tampilkan baris "Data tidak ditemukan"
                //             const row = `
                //             <tr>
                //                 <td colspan="5" class="text-center">Tidak ada data RFQ untuk vendor ini.</td>
                //             </tr>
                //         `;
                //             tableBody.innerHTML = row;
                //         }

                //         // Update field Total Harga
                //         const totalHargaInput = document.getElementById('val');
                //         totalHargaInput.value = `Rp. ${totalHarga.toLocaleString()}`; // Format dengan koma

                //         // Update Vendor ID pada form
                //         const vendorIdInput = document.getElementById('vendor-id');
                //         vendorIdInput.value = vendorId;
                //     })
                //     .catch(error => {
                //         console.error('Error memuat RFQ:', error);
                //         alert('Terjadi kesalahan saat memuat data RFQ.');
                //     });

            } else {
                inputFields.style.display = 'none';
                vendorFormContainer.innerHTML = ''; // Clear the second form if no vendor is selected
            }
        });
    </script>
@endsection
