<!doctype html>
<html>
  <body>
    <p>Hi {{ $invoice->user->first_name ?? $invoice->user->name }},</p>
    <p>Your invoice <strong>{{ $invoice->number }}</strong> has been issued.</p>
    <p>
      Amount: <strong>${{ number_format(($invoice->total_cents ?? 0)/100, 2) }}</strong><br>
      Period: {{ optional($invoice->period_start)->toDateString() }} to {{ optional($invoice->period_end)->toDateString() }}
    </p>
    <p>Items:</p>
    <ul>
      @foreach(($invoice->items ?? []) as $item)
        <li>{{ $item->description }} — ${{ number_format(($item->amount_cents ?? 0)/100, 2) }}</li>
      @endforeach
    </ul>
    <p>Thank you for your business.</p>
  </body>
</html>
