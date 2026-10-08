@if($users->isEmpty())
    <tr><td colspan="{{ count($stages) + 3 }}" class="text-center">No users found</td></tr>
@else
    @foreach($users as $index => $bUser)
        @php $userId = $bUser->user_id; @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td><a href="#">{{ $bUser->user->name ?? $bUser->user->first_name ?? 'N/A' }}</a></td>
            @foreach($stages as $stage)
                @php
                    $style = '';
                    $linkStyle = '';
                    if (!empty($stage->color)) {
                        $style = 'background-color: ' . htmlspecialchars($stage->color) . '; color: #fff;';
                        $linkStyle = 'color: #fff;';
                    }
                    $comp = $stats[$userId][$stage->id]['completed'] ?? 0;
                    $tot = $stats[$userId][$stage->id]['total'] ?? 0;
                @endphp
                <td style="{{ $style }}">
                    <a href="#" style="{{ $linkStyle }}">{{ $comp }}</a> / <a href="#" style="{{ $linkStyle }}">{{ $tot }}</a>
                </td>
            @endforeach
            <td>{{ $userTotals[$userId]['completed'] ?? 0 }} / {{ $userTotals[$userId]['total'] ?? 0 }}</td>
        </tr>
    @endforeach
@endif
