<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id','type','description','quantity','unit_price_cents','amount_cents','metadata'
    ];

    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
}
