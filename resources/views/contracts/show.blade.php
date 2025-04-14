@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            Vertragsinformationen (EntityId: 556515)
        </div>

        @if(isset($error))
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @elseif(isset($contract))
            <div x-data="{ editing: false }">
                <div x-show="!editing">
                    <div class="form-group">
                        <label>Beschreibung:</label>
                        <div class="description-text">{{ $contract['description'] ?? 'Keine Beschreibung verfügbar' }}</div>
                    </div>

                    <div class="form-group">
                        <label>Vertrags-ID:</label>
                        <div>{{ $contract['id'] ?? '-' }}</div>
                    </div>

                    <div class="form-group">
                        <label>Erstellt am:</label>
                        <div>{{ $contract['createdDate'] ?? '-' }}</div>
                    </div>

                    <button type="button" x-on:click="editing = true">Bearbeiten</button>
                </div>

                <div x-show="editing">
                    <form action="{{ route('contracts.update') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="description">Beschreibung:</label>
                            <textarea name="description" id="description" rows="4" required>{{ $contract['description'] ?? '' }}</textarea>
                        </div>

                        <button type="submit">Aktualisieren</button>
                        <button type="button" x-on:click="editing = false">Abbrechen</button>
                    </form>
                </div>
            </div>
        @else
            <div class="alert alert-danger">
                Keine Vertragsinformationen verfügbar.
            </div>
        @endif
    </div>

    <div class="card">
        <div class="card-header">
            API Rate-Limit
        </div>

        <form action="{{ route('rate-limit.update') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="rate_limit">Anfragen pro minute:</label>
                <input type="number" name="rate_limit" id="rate_limit" value="{{ $rateLimit ?? 30 }}" min="1" max="100" required>
            </div>

            <button type="submit">Speichern</button>
        </form>

        <div class="form-group" style="margin-top: 1rem;">
            <label>Aktuelles Limit:</label>
            <div>{{ $rateLimit ?? 30 }} Anfragen pro Minute</div>
        </div>
    </div>
@endsection

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
