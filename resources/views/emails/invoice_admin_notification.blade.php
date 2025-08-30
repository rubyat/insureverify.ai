<!doctype html>
<html>
  <body>
    <p>New invoice issued.</p>
    <p>
      Number: <strong>{{ $invoice->number }}</strong><br>
      User: {{ $invoice->user->email }} (ID {{ $invoice->user->id }})<br>
      Amount: <strong>${{ number_format(($invoice->total_cents ?? 0)/100, 2) }}</strong>
    </p>
    <p>Items:</p>
    <ul>
      @foreach(($invoice->items ?? []) as $item)
        <li>{{ $item->description }} — ${{ number_format(($item->amount_cents ?? 0)/100, 2) }}</li>
      @endforeach
    </ul>
  </body>
</html>
