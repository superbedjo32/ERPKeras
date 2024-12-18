<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfqListModel extends Model
{
    use HasFactory;
    protected $table = "rfq_list";
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $fillable = [
        'rfq_id',
        'vendor_id',
        'produk_id',
        'qty',
        'harga'
    ];
    public $timestamps = false;

    public function vendor()
    {
        return $this->belongsTo(VendorModel::class, 'vendor_id');
    }

    // Relasi ke model Produk
    public function produk()
    {
        return $this->belongsTo(ProdukModel::class, 'produk_id');
    }
}
