@props(['headers'])

<div class="data-table-container">
    <div class="data-table">
        <div class="table-header">
            @foreach($headers as $header)
                <div class="header-cell">{{ $header }}</div>
            @endforeach
        </div>
        {{ $slot }}
    </div>
</div>
