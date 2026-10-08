@if($sources->isEmpty())
    <tr><td colspan="{{ count($stages) + 4 }}" class="text-center">No sources found</td></tr>
@else
    @foreach($sources as $index => $source)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td><a href="#">{{ $source->name }}</a></td>
            @foreach($stages as $stage)
                @php
                    $style = '';
                    $linkStyle = '';
                    if (!empty($stage->color)) {
                        $style = 'background-color: ' . htmlspecialchars($stage->color) . '; color: #fff; border: 1px solid #fff;';
                        $linkStyle = 'color: #fff;';
                    }
                    $stat = $stats[$source->id][$stage->id] ?? ['actual' => 0, 'duplicate' => 0, 'total' => 0];
                @endphp
                <td style="{{ $style }}">
                    <a href="#" style="{{ $linkStyle }}">
                        {{ $stat['actual'] }} / {{ $stat['duplicate'] }}
                    </a>
                </td>
            @endforeach
            <td>{{ $sourceAmounts[$source->id] ?? 0 }}</td>
            <td>{{ $sourceTotals[$source->id] ?? 0 }}</td>
        </tr>
    @endforeach
@endif
