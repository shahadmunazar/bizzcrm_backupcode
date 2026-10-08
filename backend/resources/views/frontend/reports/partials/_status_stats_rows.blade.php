@if($stages->isEmpty())
    <tr><td colspan="{{ count($sources) + 4 }}" class="text-center">No status found</td></tr>
@else
    @foreach($stages as $index => $stage)
        @php
            $style = '';
            $linkStyle = '';
            if (!empty($stage->color)) {
                $style = 'background-color: ' . htmlspecialchars($stage->color) . '; color: #fff; border: 1px solid #fff;';
                $linkStyle = 'color: #fff;';
            }
        @endphp
        <tr style="{{ $style }}">
            <td class="text-center" style="{{ $style }}">{{ $index + 1 }}</td>
            <td class="text-center" style="{{ $style }}"><a href="#" style="{{ $linkStyle }}">{{ $stage->name }}</a></td>
            @foreach($sources as $source)
                @php
                    $stat = $stats[$stage->id][$source->id] ?? ['actual' => 0, 'duplicate' => 0, 'total' => 0];
                @endphp
                <td class="text-center" style="{{ $style }}">
                    <a href="#" style="{{ $linkStyle }}">
                        {{ $stat['actual'] }} / {{ $stat['duplicate'] }}
                    </a>
                </td>
            @endforeach
            <td class="text-center" style="{{ $style }}">{{ $stageAmounts[$stage->id] ?? 0 }}</td>
            <td class="text-center" style="{{ $style }}">{{ $stageTotals[$stage->id] ?? 0 }}</td>
        </tr>
    @endforeach
@endif
