<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorModel extends Model
{
    use HasFactory;
    protected $table = 't_vendor';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama_vendor',
        'telpon',
        'alamat',
        'status',
        'company'
    ];
    public $timestamps = false;

    public function produk()
    {
        return $this->belongsToMany(ProdukModel::class, 'vendor_produk', 'vendor_id', 'produk_id');
    }
}
