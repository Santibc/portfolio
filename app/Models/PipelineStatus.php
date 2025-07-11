<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PipelineStatus extends Model
{
    use HasFactory;

    protected $table = 'pipeline_statuses';

    protected $fillable = [
        'name',
    ];
        public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}
