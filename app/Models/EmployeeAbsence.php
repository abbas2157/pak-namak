<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAbsence extends Model
{
    /** Fraction of a day an absence can cover, and how it reads on screen. */
    public const PORTIONS = [
        '1.00' => 'Full Day',
        '0.50' => 'Half Day',
        '0.25' => 'Quarter Day',
    ];

    protected $fillable = ['employee_id', 'date', 'portion', 'paid', 'note'];

    protected $casts = [
        'date' => 'date',
        'portion' => 'decimal:2',
        'paid' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /** "Full Day" / "Half Day" / "Quarter Day" — falls back to the raw fraction. */
    public function portionLabel(): string
    {
        return self::PORTIONS[number_format((float) $this->portion, 2, '.', '')]
            ?? rtrim(rtrim((string) $this->portion, '0'), '.').' Day';
    }

    /** Urdu counterpart of portionLabel(), for the screens that show both. */
    public function portionLabelUrdu(): string
    {
        return [
            '1.00' => 'پورا دن',
            '0.50' => 'آدھا دن',
            '0.25' => 'چوتھائی دن',
        ][number_format((float) $this->portion, 2, '.', '')] ?? '';
    }

    /**
     * Days shown as a short number: 1, 0.5, 0.25 — used wherever a day count
     * is printed so "2.50 days" doesn't read as "2.50".
     */
    public static function formatDays(float $days): string
    {
        return rtrim(rtrim(number_format($days, 2, '.', ''), '0'), '.') ?: '0';
    }
}
