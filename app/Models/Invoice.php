<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    protected $fillable = [
        'subscription_id','user_id','number','status','currency','subtotal_cents','discount_cents','tax_cents','total_cents','period_start','period_end','provider','provider_invoice_id','metadata','issued_at','due_at','paid_at'
    ];

    protected $casts = [
        'issued_at'=>'datetime','due_at'=>'datetime','paid_at'=>'datetime','period_start'=>'datetime','period_end'=>'datetime','metadata'=>'array'
    ];

    public function subscription(): BelongsTo { return $this->belongsTo(Subscription::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function items(): HasMany { return $this->hasMany(InvoiceItem::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }

    public static function generateInvoiceNumber(): string
    {
        // Simple sequential per-year number: INV-YYYY-XXXXXX
        return DB::transaction(function () {
            $year = now()->format('Y');
            $prefix = "INV-{$year}-";
            $last = static::where('number','like', $prefix.'%')
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value('number');
            $seq = 1;
            if ($last) {
                $seq = (int) substr($last, -6) + 1;
            }
            return $prefix . str_pad((string)$seq, 6, '0', STR_PAD_LEFT);
        });
    }
}
