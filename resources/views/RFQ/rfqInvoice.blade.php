<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
        }
        .info-table, .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td, .product-table td, .product-table th {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .product-table th {
            background-color: #f2f2f2;
            text-align: left;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h2>Invoice</h2>
        <p>Tanggal Transaksi: {{ $rfq->tgl_transaksi }}</p>
    </div>

    <!-- Vendor Information -->
    <table class="info-table">
        <tr>
            <td><strong>Nama Vendor:</strong> {{ $vendor->nama_vendor }}</td>
            <td><strong>Telepon:</strong> {{ $vendor->telpon }}</td>
        </tr>
        <tr>
            <td><strong>Alamat:</strong> {{ $vendor->alamat }}</td>
            <td><strong>Company:</strong> {{ $vendor->company }}</td>
        </tr>
    </table>

    <!-- Product List -->
    <h4>Detail Produk</h4>
    <table class="product-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Deskripsi</th>
                <th>Qty</th>
                <th>Harga Satuan</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; $grandTotal = 0; @endphp
            @foreach($rfqList as $item)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $item->produk->nama_produk }}</td>
                    <td>{{ $item->produk->deskripsi }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>Rp. {{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td>Rp. {{ number_format($item->qty * $item->harga, 0, ',', '.') }}</td>
                </tr>
                @php $grandTotal += $item->qty * $item->harga; @endphp
            @endforeach
        </tbody>
    </table>

    <!-- Total Harga -->
    <h4>Total Harga: Rp. {{ number_format($grandTotal, 0, ',', '.') }}</h4>

    <!-- Footer -->
    <div class="footer">
        <p>Terima kasih telah melakukan transaksi dengan kami!</p>
    </div>
</body>
</html>
