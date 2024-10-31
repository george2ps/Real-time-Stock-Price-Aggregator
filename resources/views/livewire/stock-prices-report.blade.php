<div wire:poll.15s>
    <div class="container py-4">
        <h2>Real-Time Stock Report</h2>
        <table class="table table-auto w-full text-left">
            <thead>
            <tr>
                <th>Symbol</th>
                <th>Current Price</th>
                <th>Previous Price</th>
                <th>Percentage Change</th>
            </tr>
            </thead>
            <tbody>
            @foreach($stocks as $stock)
                <tr>
                    <td>{{ $stock->symbol }}</td>
                    <td>${{ number_format($stock->price, 2) }}</td>
                    <td>{{ $stock->previous_price ? '$' . number_format($stock->previous_price, 2) : 'N/A' }}</td>
                    <td class="{{ $stock->percentage_change < 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $stock->percentage_change ? number_format($stock->percentage_change, 2) . '%' : 'N/A' }}
                        @if($stock->percentage_change < 0)
                            <i class="fa-solid fa-arrow-down"></i>
                        @else
                            <i class="fa-solid fa-arrow-up"></i>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
