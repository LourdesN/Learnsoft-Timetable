<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Stream extends Model
{
    use HasFactory;

    protected $fillable = ['stream'];

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
}