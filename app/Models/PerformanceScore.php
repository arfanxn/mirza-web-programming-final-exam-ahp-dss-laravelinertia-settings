<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceScore extends Model
{
    use HasFactory;
    use HasUlids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'alternative_id',
        'criterion_id',
        'value',
    ];

    public function criterion()
    {
        return $this->belongsTo(Criterion::class);
    }


    public function alternative()
    {
        return $this->belongsTo(Alternative::class);
    }

    /**
     * value
     */
    protected function value(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => (float) $value,
        );
    }
}
