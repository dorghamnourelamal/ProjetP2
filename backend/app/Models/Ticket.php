<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'reservation_id',
        'code',
        'type',
        'prix',
        'statut',
    ];

    protected $appends = [
        'qr_code_url',
        'verification_url',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function getQrCodeUrlAttribute(): string
    {
        return url("/api/tickets/qrcode/{$this->code}");
    }

    public function getVerificationUrlAttribute(): string
    {
        $frontendUrl = rtrim(env('FRONTEND_URL', 'http://localhost:4200'), '/');

        return "{$frontendUrl}/tickets/verify/{$this->code}";
    }
}
