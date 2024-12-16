<?php

namespace App\Http\Controllers;

use App\Models\VendorModel;
use App\Models\ProdukModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Validated;

class VendorController extends Controller
{
    public function VendorTampil()
    {
        $vendor_tok = VendorModel::all();
        return view('Vendor_user/vendor', ['vendors' => $vendor_tok]);
    }

    public function inputVendorTampil()
    {
        $bahan = ProdukModel::where('status', '=', 2)->get();
        return view('Vendor_user/vendorinput', ['bahan' => $bahan]);
    }

    public function inputVendor(Request $request)
    {
        // Buat vendor baru
        $vendor = VendorModel::create([
            'nama_vendor' =>  $request->nama_vendor,
            'telpon' => $request->telpon,
            'alamat' => $request->alamat,
            'status' => 1,
            'company' => 'No-Company',
        ]);

        // Simpan relasi produk (jika ada produk dipilih)
        if ($request->has('produk')) {
            $vendor->produk()->sync($request->produk);
        }

        return redirect(route('Vendor'))->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function editVendorTampil($id)
    {
        // Ambil vendor berdasarkan ID
        $vendors = VendorModel::find($id);

        // Ambil semua produk dengan status = 2
        $bahan = ProdukModel::where('status', '=', 2)->get();

        // Ambil ID produk yang sudah terhubung dengan vendor
        $selectedBahan = $vendors->produk()->pluck('produk_id')->toArray();

        // Kirim data ke view
        return view('Vendor_user/vendoredit', [
            'vendors' => $vendors,
            'bahan' => $bahan,
            'selectedBahan' => $selectedBahan
        ]);
    }

    public function editVendor(Request $request, $id)
    {
        // Cari vendor berdasarkan ID
        $vendor = VendorModel::find($id);

        // Validasi jika vendor tidak ditemukan
        if (!$vendor) {
            return redirect(route('Vendor'))->with('error', 'Vendor tidak ditemukan.');
        }

        // Update data vendor
        $vendor->update([
            'nama_vendor' => $request->nama_vendor,
            'alamat' => $request->alamat,
            'telpon' => $request->telpon,
            'status' => 1,
            'company' => $request->company
        ]);

        // Perbarui relasi produk di tabel pivot (jika ada produk dipilih)
        if ($request->has('bahan')) {
            $vendor->produk()->sync($request->bahan);
        } else {
            // Jika tidak ada produk yang dipilih, hapus semua relasi
            $vendor->produk()->sync([]);
        }

        // Redirect dengan pesan sukses
        return redirect(route('Vendor'))->with('success', 'Vendor berhasil diperbarui.');
    }

    public function deleteVendor($id)
    {
        // Cari vendor berdasarkan ID
        $vendor = VendorModel::find($id);

        // Validasi jika vendor tidak ditemukan
        if (!$vendor) {
            return redirect(route('Vendor'))->with('error', 'Vendor tidak ditemukan.');
        }

        // Hapus semua relasi produk di tabel pivot
        $vendor->produk()->detach();

        // Hapus vendor
        $vendor->delete();

        // Redirect dengan pesan sukses
        return redirect(route('Vendor'))->with('success', 'Vendor berhasil dihapus.');
    }
}
