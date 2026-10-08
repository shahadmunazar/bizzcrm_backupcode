@if($users->isEmpty())
    <tr><td colspan="{{ count($stages) + 4 }}" class="text-center">No users found</td></tr>
@else
    @foreach($users as $index => $bUser)
        @php $userId = $bUser->user_id; @endphp
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="text-center"><a href="#">{{ $bUser->user->name ?? $bUser->user->first_name ?? 'N/A' }}</a></td>
            @foreach($stages as $stage)
                @php
                    $style = '';
                    $linkStyle = '';
                    if (!empty($stage->color)) {
                        $style = 'background-color: ' . htmlspecialchars($stage->color) . '; color: #fff; border: 1px solid #fff;';
                        $linkStyle = 'color: #fff;';
                    }
                    $stat = $stats[$userId][$stage->id] ?? ['actual' => 0, 'duplicate' => 0, 'total' => 0];
                @endphp
                <td class="text-center" style="{{ $style }}">
                    <a href="#" style="{{ $linkStyle }}">
                        {{ $stat['actual'] }} / {{ $stat['duplicate'] }}
                    </a>
                </td>
            @endforeach
            <td class="text-center">{{ number_format($userMaturedAmounts[$userId] ?? 0, 2) }}</td>
            <td class="text-center">{{ $userTotals[$userId] ?? 0 }}</td>
        </tr>
    @endforeach
@endif
