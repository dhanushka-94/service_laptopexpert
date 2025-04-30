<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceJob extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_id',
        'customer_id',
        'technician_id',
        'device_type',
        'brand',
        'model',
        'serial_number',
        'reported_issues',
        'accessories',
        'status',
        'diagnosis',
        'repair_notes',
        'parts_used',
        'estimated_cost',
        'final_cost',
        'completion_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'final_cost' => 'decimal:2',
        'completion_date' => 'date',
    ];

    /**
     * Get the customer that owns the job.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the technician assigned to the job.
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    /**
     * Get the notes for the job.
     */
    public function notes(): HasMany
    {
        return $this->hasMany(JobNote::class);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate job ID when creating a new service job
        static::creating(function ($job) {
            $job->job_id = 'JOB-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        });
    }
}
