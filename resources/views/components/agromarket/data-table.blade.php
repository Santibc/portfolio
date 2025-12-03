@props(['headers'])

<div class="investments-table">
    <div class="table-header">
        @foreach($headers as $header)
            <div class="header-cell">{{ $header }}</div>
        @endforeach
    </div>

    {{ $slot }}
</div>
