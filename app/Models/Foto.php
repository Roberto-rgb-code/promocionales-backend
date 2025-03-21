<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Foto extends Model {
  use HasFactory;

  protected $fillable = ['promocional_id', 'foto_path'];

  public function promocional() {
    return $this->belongsTo(Promocional::class);
  }
}
