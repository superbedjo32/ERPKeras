<?php

namespace App\Http\Controllers;

use App\Models\PO;
use App\Models\RfqModel;
use App\Models\ProdukModel;
use App\Models\VendorModel;
use App\Models\RfqListModel;
use App\Models\POModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class POController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rfq = RfqModel::with('po') // Load relasi PO
            ->join('t_vendor', 't_rfq.vendor_id', '=', 't_vendor.id')
            ->get(['t_rfq.*', 't_vendor.nama_vendor', 't_vendor.alamat']);

        return view('PO.po', ['rfqs' => $rfq]);
    }


    public function poList($id_rfq)
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

        return view('PO.polist', ['vendor' => $vendor, 'bahan' => $bahan, 'rfq_list' => $rfqList, 'findRfq' => $findRfq]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'rfq_id'   => 'required|integer',
            'pembayaran'  => 'nullable|string', // Untuk pembayaran = 1 (teks)
            'image'       => 'nullable|file|mimes:jpg,jpeg,png|max:2048', // Untuk pembayaran = 2 (upload file)
        ]);

        try {
            // Mulai transaksi database
            DB::beginTransaction();

            // Ambil data RFQ berdasarkan rfq_id
            $rfq = RfqModel::findOrFail($request->rfq_id);

            // Convert total_harga dari format Rp. 300.000 ke integer 300000
            $totalHarga = (int) str_replace(['Rp.', '.', ' '], '', $rfq->total_harga);

            // Validasi pembayaran
            if ($request->has('pembayaran')) {
                $inputPembayaran = (int) str_replace(['Rp.', '.', ' '], '', $request->pembayaran);

                if ($inputPembayaran !== $totalHarga) {
                    return back()->with('error', 'Jumlah pembayaran tidak sesuai dengan total harga RFQ.');
                }
            }

            // Generate kode_po otomatis
            $kodePO = 'PO-' . time(); // Contoh format PO

            // Simpan data pembayaran dan gambar
            $data = [
                'kode_po'        => $kodePO,
                'tgl_pembayaran' => now(), // Tanggal pembayaran saat ini
                'rfq_id'         => $request->rfq_id,
            ];

            if ($request->has('pembayaran')) {
                // Jika pembayaran input teks
                $data['pembayaran'] = $request->pembayaran;
            }

            if ($request->hasFile('image')) {
                // Jika upload file gambar
                $path = $request->file('image')->store('gambar', 'public'); // Simpan di folder public/gambar
                $data['gambar'] = $path;
            }

            // Insert ke database PO
            POModel::create($data);

            // Ubah status di tabel rfqModel menjadi 3
            $rfq->status = 3;
            $rfq->save();

            DB::commit();

            return redirect('/po/data');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan data PO: ' . $e->getMessage());
        }
    }
    public function report($id)
    {
        // Ambil data PO berdasarkan ID
        $po = DB::table('t_po')
            ->join('t_rfq', 't_po.rfq_id', '=', 't_rfq.id')
            ->join('t_vendor', 't_rfq.vendor_id', '=', 't_vendor.id')
            ->leftJoin('rfq_list', 't_rfq.id', '=', 'rfq_list.rfq_id')
            ->leftJoin('t_produk', 'rfq_list.produk_id', '=', 't_produk.id')
            ->where('t_po.id', $id)
            ->select(
                't_po.*',
                't_rfq.*',
                't_vendor.nama_vendor',
                't_vendor.telpon as vendor_telpon',
                't_vendor.alamat as vendor_alamat',
                't_produk.nama_produk',
                't_produk.harga as produk_harga',
                'rfq_list.qty as produk_qty',
                'rfq_list.harga as rfq_harga'
            )
            ->first(); // Tambahkan ini

        // Cek jika data PO ditemukan
        if (!$po) {
            return redirect()->back()->with('error', 'PO tidak ditemukan.');
        }

        // Ambil data RFQ List berdasarkan vendor_id
        $rfqList = DB::table('rfq_list')
            ->join('t_produk', 'rfq_list.produk_id', '=', 't_produk.id')
            ->where('rfq_list.rfq_id', $po->rfq_id) // Ambil berdasarkan rfq_id
            ->select(
                'rfq_list.*',
                't_produk.nama_produk',
                't_produk.harga'
            )
            ->get();

        // Hitung total harga produk
        $totalHarga = $rfqList->sum(function ($item) {
            return $item->qty * $item->harga;
        });

        // Data untuk report PDF
        $data = [
            'po' => $po,
            'rfqList' => $rfqList,
            'vendor' => (object) [
                'nama_vendor' => $po->nama_vendor,
                'alamat' => $po->vendor_alamat,
                'telpon' => $po->vendor_telpon
            ],
            'totalHarga' => $totalHarga,
            'rfq' => $po // Menyertakan data RFQ untuk informasi transaksi
        ];

        // Generate PDF
        $pdf = Pdf::loadView('PO.poreport_pdf', $data);

        // Return PDF sebagai response download
        return $pdf->download('report_po_' . $po->kode_po . '.pdf');
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PO  $pO
     * @return \Illuminate\Http\Response
     */
    public function show(PO $pO)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PO  $pO
     * @return \Illuminate\Http\Response
     */
    public function edit(PO $pO)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PO  $pO
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PO $pO)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PO  $pO
     * @return \Illuminate\Http\Response
     */
    public function destroy(PO $pO)
    {
        //
    }
}
