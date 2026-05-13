<x-app-layout>
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                Edit Blocked Zone —
                @if($zone->type === 'polygon') Polygon
                @elseif($zone->type === 'postcode') Postcode
                @else Suburb
                @endif
            </h1>
            <a href="{{ route('admin.blocked-zones.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @include('admin.blocked-zones.partials._form', ['zone' => $zone])
    </div>
</x-app-layout>
