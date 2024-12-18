<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembayaran PO</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .header,
        .footer {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .table th {
            background-color: #f2f2f2;
        }

        .total {
            margin-top: 20px;
            text-align: right;
            font-size: 16px;
        }

        .section-title {
            margin-top: 30px;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <!-- Identitas Vendor -->
    <div class="header">
        <h1>Laporan Pembayaran PO</h1>
        <p><strong>Vendor: </strong>{{ $vendor->nama_vendor }}</p>
        <p><strong>Alamat: </strong>{{ $vendor->alamat }}</p>
        <p><strong>Telpon: </strong>{{ $vendor->telpon }}</p>
    </div>

    <!-- Transaksi Order (RFQ) -->
    <div class="section-title">Transaksi Order (RFQ)</div>
    <table class="table">
        <tr>
            <th>No. RFQ</th>
            <th>Tanggal Transaksi</th>
            <th>Total Harga</th>
            <th>Status</th>
        </tr>
        <tr>
            <td>{{ $rfq->id }}</td>
            <td>{{ \Carbon\Carbon::parse($rfq->tgl_transaksi)->format('d-m-Y') }}</td>
            <td>{{ $rfq->total_harga }}</td>
            <td>{{ $rfq->status }}</td>
        </tr>
    </table>

    <!-- Daftar Produk yang Dibeli (RFQ List) -->
    <div class="section-title">Daftar Produk yang Dibeli</div>
    <table class="table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rfqList as $rfqListItem)
                <tr>
                    <td>{{ $rfqListItem->nama_produk }}</td>
                    <td>{{ 'Rp. ' . number_format($rfqListItem->harga, 0, ',', '.') }}</td>
                    <td>{{ $rfqListItem->qty }}</td>
                    <td>{{ 'Rp. ' . number_format($rfqListItem->qty * $rfqListItem->harga, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Data PO (Pembayaran) -->
    <div class="section-title">Data PO</div>
    <table class="table">
        <tr>
            <th>Kode PO</th>
            <th>Tanggal Pembayaran</th>
            <th>Status Pembayaran</th>
        </tr>
        <tr>
            <td>{{ $po->kode_po }}</td>
            <td>{{ \Carbon\Carbon::parse($po->tgl_pembayaran)->format('d-m-Y') }}</td>
            <td>{{ $po->pembayaran == 1 ? 'Lunas' : 'Belum Lunas' }}</td>
        </tr>
    </table>

    <!-- Total Harga -->
    <div class="total">
        <strong>Total Harga: Rp. {{ number_format($totalHarga, 0, ',', '.') }}</strong>
    </div>

    <div class="footer">
        <p>Terima kasih atas kerja samanya.</p>
    </div>

</body>

</html>
