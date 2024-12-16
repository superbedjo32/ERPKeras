<?php

namespace App\Http\Controllers;

use App\Models\BomModel;
use App\Models\ProdukModel;
use App\Models\BomListModel;
use Illuminate\Http\Request;

class BomController extends Controller
{
    public function tampilBom()
    {
        $bom = BomModel::with(['bahans']) // Eager load relasi bahan
            ->join('t_produk', 't_bom.kode_produk', '=', 't_produk.id')
            ->get(['t_bom.*', 't_produk.nama_produk as produk_nama']);
        $produk = ProdukModel::where('status', 1)->get();
        return view('BOM/bom-list', ['boms' => $bom], ['produk' => $produk]);
    }

    public function uploadBOM(Request $request)
    {
        $tanggal = date("Y/m/d");
        $jumlah = BomModel::count();
        $kode_bom = 'BM' . str_pad($jumlah + 1, 3, '0', STR_PAD_LEFT);

        // Buat BOM
        $bom = BomModel::create([
            'kode_bom' => $kode_bom,
            'kode_produk' => $request->kode_produk,
            'tanggal' => $tanggal,
            'total_harga' => 0, // Total harga dihitung dari bahan
        ]);

        // Tambahkan bahan ke BOM
        $bahanIds = $request->bahan; // Array ID bahan
        $totalHarga = 0;

        foreach ($bahanIds as $bahanId) {
            $bahan = ProdukModel::find($bahanId);
            $totalHarga += $bahan->harga;

            // Relasi ke tabel pivot
            $bom->bahans()->attach($bahanId);
        }

        // Update total harga
        $bom->update(['total_harga' => $totalHarga]);

        return redirect('/bom');
    }

    public function materialInput()
    {
        $produk = ProdukModel::where('status', 1)->get();
        $bahan = ProdukModel::where('status', 2)->get();
        return view('BOM/bom', ['produk' => $produk, 'bahan' => $bahan]);
    }

    public function materialInputItems($kode_bom)
    {
        session(['kode_bom' => $kode_bom]);
        // Ambil detail BOM
        $bom = BomModel::with('bahans') // Relasi bahan dari tabel pivot
            ->join('t_produk', 't_bom.kode_produk', '=', 't_produk.id')
            ->where('t_bom.id', $kode_bom)
            ->first(['t_bom.*', 't_produk.nama_produk as produk_nama', 't_produk.harga as produk_harga']);

        // Ambil daftar bahan yang sudah terhubung dengan BOM dari tabel pivot
        $bomList = BomListModel::join('t_produk', 't_bom_list.kode_produk', '=', 't_produk.id')
            ->where('t_bom_list.kode_bom', $kode_bom)
            ->get([
                't_bom_list.id',
                't_bom_list.kode_produk', // Kode produk
                't_bom_list.qty', // Kuantitas bahan
                't_bom_list.satuan', // Satuan bahan
                't_bom_list.harga_total', // Harga total bahan
                't_produk.id_reference', // ID Referensi produk
                't_produk.nama_produk', // Nama produk
                't_produk.harga', // Harga satuan produk
            ]);

        // Ambil bahan yang tersedia untuk dipilih
        $produk = ProdukModel::whereHas('boms', function ($query) use ($kode_bom) {
            $query->where('bom_id', $kode_bom); // Filter bahan_id sesuai $kode_bom
        })
            ->where('status', 2) // Hanya ambil yang status = 2
            ->get(['id', 'nama_produk']);

        return view('BOM/bom-item', [
            'bom' => $bom,
            'materials' => $produk, // Bahan yang bisa dipilih
            'list' => $bomList,
        ]);
    }

    public function uploadList(Request $request)
    {
        $product = ProdukModel::find($request->kode_produk);
        $harga_satuan = $product->harga;

        $total_harga = $harga_satuan * $request->qty;

        BomListModel::create([
            'kode_bom_list' => $request->kode_bom_list,
            'kode_bom' => $request->kode_bom,
            'kode_produk' => $request->kode_produk,
            'qty' => $request->qty,
            'satuan' => $request->satuan,
            'harga_total' => $total_harga
        ]);

        return redirect('/bom/item_list/' . $request->kode_bom);
    }

    public function deletbom($id)
    {
        $bom = BomModel::find($id);
        $bom->delete();
        return redirect('/bom');
    }

    public function deleteList($kode_bom_list)
    {
        // Ambil kode_bom dari sesi
        $kode_bom = session('kode_bom');

        // Cari data yang akan dihapus berdasarkan ID
        $bom_list = BomListModel::where('id', $kode_bom_list)->first();

        if (!$bom_list) {
            return redirect()->back()->with('error', 'Item not found!');
        }

        // Hapus data dari tabel
        $bom_list->delete();

        // Redirect kembali ke halaman item_list dengan kode_bom
        return redirect('/bom/item_list/' . $kode_bom)->with('success', 'Item has been deleted successfully.');
    }
}
