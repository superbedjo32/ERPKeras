<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SQListModel extends Model
{
    use HasFactory;
    protected $table = "t_sq_list";
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $fillable = [
        'sq_id',
        'produk_id',
        'qty',
        'satuan',
        'total'
    ];
    public $timestamps = false;
}
