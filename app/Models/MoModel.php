<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoModel extends Model
{
    use HasFactory;
    protected $table = 'mo';
    protected $primaryKey = 'id';
    protected $fillable = ['kode_mo','tgl','kode_bom','qty', 'status'];
    public $timestamps = false;
}
