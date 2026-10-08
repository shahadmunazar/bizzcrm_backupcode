@extends('layouts.master')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">My Personalization Settings</h2>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-white">AI Natural Language Configurator</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Tell the CRM how you want it to behave. The AI will translate your request into strict rules.</p>
                    <p><em>Examples: "I only want to see high priority applications." or "Remind me about activities after 2 days."</em></p>
                    
                    <form action="{{ route('preferences.parse_ai', ['box_slug' => $box_slug]) }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="natural_language" class="form-control" placeholder="Type your preference here..." required>
                            <button type="submit" class="btn btn-primary">Apply AI Setting</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Active JSON Preferences</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">These are your currently active settings powering your dashboards and views.</p>
                    <pre class="bg-light p-3 rounded"><code>{{ json_encode($preferences, JSON_PRETTY_PRINT) }}</code></pre>

                    <form action="{{ route('preferences.reset', ['box_slug' => $box_slug]) }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">Reset All Preferences</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
