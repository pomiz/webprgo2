<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    const STATUS_PENDING_PAYMENT = 'pending_payment';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_PENDING_PAYMENT => 'Menunggu Pembayaran',
        self::STATUS_CONFIRMED => 'Pembayaran Dikonfirmasi',
        self::STATUS_PROCESSING => 'Sedang Dikemas',
        self::STATUS_SHIPPED => 'Dikirim',
        self::STATUS_COMPLETED => 'Selesai',
        self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    const STATUS_COLORS = [
        self::STATUS_PENDING_PAYMENT => 'yellow',
        self::STATUS_CONFIRMED => 'blue',
        self::STATUS_PROCESSING => 'indigo',
        self::STATUS_SHIPPED => 'purple',
        self::STATUS_COMPLETED => 'green',
        self::STATUS_CANCELLED => 'red',
    ];

    protected $fillable = [
        'user_id',
        'total_price',
        'subtotal',
        'shipping_cost',
        'shipping_address',
        'status',
        'virtual_account',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'gray';
    }

    public function getNextStatusAttribute(): ?string
    {
        $flow = [
            self::STATUS_PENDING_PAYMENT => self::STATUS_CONFIRMED,
            self::STATUS_CONFIRMED => self::STATUS_PROCESSING,
            self::STATUS_PROCESSING => self::STATUS_SHIPPED,
            self::STATUS_SHIPPED => self::STATUS_COMPLETED,
        ];

        return $flow[$this->status] ?? null;
    }

    public function canAdvance(): bool
    {
        return $this->next_status !== null && $this->status !== self::STATUS_CANCELLED;
    }

    public function canCancel(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING_PAYMENT,
            self::STATUS_CONFIRMED,
            self::STATUS_PROCESSING,
        ]);
    }

    public function advance(): bool
    {
        if (!$this->canAdvance()) return false;
        $this->status = $this->next_status;
        return $this->save();
    }

    public function cancel(): bool
    {
        if (!$this->canCancel()) return false;
        $this->status = self::STATUS_CANCELLED;
        return $this->save();
    }
}
