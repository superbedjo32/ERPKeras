<?php

namespace App\Http\Controllers;

use App\Models\RfqModel;
use App\Models\RfqListModel;
use App\Models\ProdukModel;
use App\Models\VendorModel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class RfqController extends Controller
{
    public function rfqTampil()
    {
        $rfq = RfqModel::join('t_vendor', 't_rfq.vendor_id', '=', 't_vendor.id')
            ->get(['t_rfq.*', 't_vendor.nama_vendor', 't_vendor.alamat']);
        return view('RFQ/rfq', ['rfqs' => $rfq]);
    }

    public function inputRfqTampil()
    {
        $vendor = VendorModel::all(); // Muat semua vendor
        return view('RFQ/rfqinput', ['vendor' => $vendor]);
    }

    // Tambahkan fungsi untuk memuat bahan berdasarkan vendor
    public function getBahanByVendor($vendorId)
    {
        $bahan = ProdukModel::where('status', 2)
            ->whereHas('vendor', function ($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })->get();

        return response()->json($bahan);
    }

    public function getRfqByVendor($vendorId)
    {
        $rfqList = RfqListModel::where('vendor_id', $vendorId)
            ->with(['produk', 'vendor']) // Pastikan relasi sudah diatur
            ->get();

        return response()->json($rfqList);
    }

    public function inputRfqList(Request $request)
    {
        try {
            // Ambil last ID dari RfqModel
            $lastRfq = RfqModel::orderBy('id', 'desc')->first();

            if (!$lastRfq) {
                $lastRfq = RfqModel::create([
                    'vendor_id' => $request->vendor,
                    'tgl_transaksi' => now(),
                    'total_harga' => '', // Set default value
                    'pembayaran' => '', // Set default value
                    'status' => 1 // Set default status
                ]);
            }

            // Buat entry baru di RfqListModel
            $rfq = RfqListModel::create([
                'vendor_id' => $request->vendor,
                'produk_id' => $request->bahan,
                'qty' => $request->qty,
                'harga' => $request->harga,
                'rfq_id' => $lastRfq->id
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil ditambahkan',
                'data' => $rfq
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function InputRfq(Request $request)
    {
        RfqModel::create([
            'vendor_id' => $request->vendor_id,
            'tgl_transaksi' => Carbon::now()->toDateTimeString(),
            'total_harga' => $request->total_harga,
            'pembayaran' => $request->pembayaran,
            'status' => 1,
        ]);
        return redirect('/rfq/data');
    }

    public function rfqList($id_rfq)
    {
        $rfq = RfqModel::where('id', $id_rfq)->first(['vendor_id']);

        // Periksa apakah data RFQ ditemukan
        if (!$rfq) {
            return redirect()->back()->with('error', 'Data RFQ tidak ditemukan.');
        }

        // Ambil nama_vendor dari VendorModel berdasarkan vendor_id
        $vendor = VendorModel::where('id', $rfq->vendor_id)->first();

        // Periksa apakah data Vendor ditemukan
        if (!$vendor) {
            return redirect()->back()->with('error', 'Data Vendor tidak ditemukan.');
        }
        $vendor_id = $rfq->vendor_id;

        // Ambil semua data rfq_list berdasarkan vendor_id
        $bahan = ProdukModel::where('status', 2)
            ->whereHas('vendor', function ($query) use ($vendor_id) {
                $query->where('vendor_id', $vendor_id);
            })->get();

        $rfqList = RfqListModel::with('produk') // Include relasi produk
            ->where('vendor_id', $vendor_id)
            ->get();

        $findRfq = RfqModel::where('id', $id_rfq)->first();

        return view('RFQ/rfqlist', ['vendor' => $vendor, 'bahan' => $bahan, 'rfq_list' => $rfqList, 'findRfq' => $findRfq]);
    }

    public function updateRfq(Request $request, $id_rfq)
    {

        // Cari RFQ berdasarkan ID
        $rfq = RfqModel::find($id_rfq);

        // Jika RFQ tidak ditemukan
        if (!$rfq) {
            return redirect()->back()->with('error', 'RFQ tidak ditemukan.');
        }
        // Update data RFQ
        $rfq->update([
            'rfq_id' => $id_rfq,
            'vendor_id' => $request->vendor_id,
            'tgl_transaksi' => Carbon::now()->toDateTimeString(), // Set waktu transaksi ke waktu saat ini
            'total_harga' => $request->total_harga, // Asumsikan input berupa angka
            'pembayaran' => $request->pembayaran,
            'status' => 1, // Set status menjadi 1
        ]);

        // Redirect dengan pesan sukses
        return redirect('/rfq/data');
    }

    public function ListProses(Request $request)
    {
        RfqListModel::create([
            'id_rfq' => $request->id_rfq,
            'kode_produk' => $request->kode_produk,
            'qty' => $request->qty,
            'satuan' => $request->satuan
        ]);
        $produk = ProdukModel::find($request->kode_produk);
        $harga = $produk->harga;
        $rfq = RfqModel::find($request->id_rfq);
        $harga_lama = $rfq->total_harga;
        $harga_baru = $harga_lama + ($harga * $request->qty);
        $rfq->total_harga = $harga_baru;
        $rfq->save();
        return redirect('/rfq/data/list/' . $request->id_rfq);
    }

    public function generateInvoice($id)
    {
        // Ambil data RFQ berdasarkan ID
        $rfq = RfqModel::findOrFail($id);

        // Ambil data vendor berdasarkan vendor_id di RFQ
        $vendor = VendorModel::findOrFail($rfq->vendor_id);

        // Ambil list produk berdasarkan RFQ
        $rfqList = RfqListModel::with('produk')->where('vendor_id', $vendor->id)->get();

        // Data yang akan dikirim ke view
        $data = [
            'rfq' => $rfq,
            'vendor' => $vendor,
            'rfqList' => $rfqList,
        ];

        // Load view PDF dan kirim data
        $pdf = Pdf::loadView('RFQ.rfqInvoice', $data);

        // Return PDF untuk di-download atau ditampilkan
        return $pdf->stream('invoice.pdf');
    }

    public function rfqConfirmPembayaran(Request $request, $id_rfq)
    {
        $rfq = RfqModel::find($id_rfq);
        $rfq->status = $rfq->status + 1;
        $rfq->save();
        return redirect('/rfq/data');
    }

    public function rfqDelete($id_rfq)
    {
        $rfq = RfqModel::find($id_rfq);
        $rfq->delete();
        return redirect(Route('RfqTampil'));
    }

    public function rfqListDelete($id_rfqList)
    {
        try {
            // Cari data berdasarkan ID
            $rfq = RfqListModel::findOrFail($id_rfqList);

            // Hapus data
            $rfq->delete();

            // Kirim respon berhasil
            return response()->json(['message' => 'Data berhasil dihapus'], 200);
        } catch (\Exception $e) {
            // Tangani error
            return response()->json(['message' => 'Gagal menghapus data', 'error' => $e->getMessage()], 500);
        }
    }
}
