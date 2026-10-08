@if($dates->isEmpty())
    <tr><td colspan="{{ count($stages) + 3 }}" class="text-center">No dates found</td></tr>
@else
    @foreach($dates as $index => $date)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td><a href="#">{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}</a></td>
            @foreach($stages as $stage)
                @php
                    $style = '';
                    $linkStyle = '';
                    if (!empty($stage->color)) {
                        $style = 'background-color: ' . htmlspecialchars($stage->color) . '; color: #fff;';
                        $linkStyle = 'color: #fff;';
                    }
                    $comp = $stats[$date][$stage->id]['completed'] ?? 0;
                    $tot = $stats[$date][$stage->id]['total'] ?? 0;
                @endphp
                <td style="{{ $style }}">
                    <a href="#" style="{{ $linkStyle }}">{{ $comp }}</a> / <a href="#" style="{{ $linkStyle }}">{{ $tot }}</a>
                </td>
            @endforeach
            <td>{{ $dateTotals[$date]['completed'] ?? 0 }} / {{ $dateTotals[$date]['total'] ?? 0 }}</td>
        </tr>
    @endforeach
@endif
