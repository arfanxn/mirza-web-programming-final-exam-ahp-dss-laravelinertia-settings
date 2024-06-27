<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PairwiseComparison extends Model
{
    use HasFactory;
    use HasUlids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'primary_criterion_id',
        'secondary_criterion_id',
        'row',
        'column',
        'value',
    ];

    public function primaryCriterion()
    {
        return $this->belongsTo(Criterion::class, 'primary_criterion_id', 'id');
    }

    public function secondaryCriterion()
    {
        return $this->belongsTo(Criterion::class, 'secondary_criterion_id', 'id');
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
