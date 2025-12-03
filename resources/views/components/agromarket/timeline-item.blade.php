@props([
    'date',
    'month',
    'title',
    'description',
    'amount' => null,
    'time',
    'icon' => 'fas fa-circle',
    'type' => 'dividend', // dividend, investment, trading, etc.
    'status' => 'completed' // completed, pending, processing
])

@php
    $amountClass = '';
    if ($amount) {
        $amountClass = strpos($amount, '-') === 0 ? 'negative' : 'positive';
    }
@endphp

<div class="timeline-item">
    <div class="timeline-date">
        <span class="date">{{ $date }}</span>
        <span class="month">{{ $month }}</span>
    </div>
    <div class="timeline-content">
        <div class="activity-card {{ $type }}">
            <div class="activity-icon">
                <i class="{{ $icon }}"></i>
            </div>
            <div class="activity-details">
                <h4>{{ $title }}</h4>
                <p>{{ $description }}</p>
                <div class="activity-meta">
                    @if($amount)
                        <span class="amount {{ $amountClass }}">{{ $amount }}</span>
                    @endif
                    <span class="time">{{ $time }}</span>
                </div>
            </div>
            <div class="activity-status {{ $status }}">
                @if($status === 'completed')
                    <i class="fas fa-check-circle"></i>
                @elseif($status === 'pending')
                    <i class="fas fa-clock"></i>
                @else
                    <i class="fas fa-spinner"></i>
                @endif
            </div>
        </div>
    </div>
</div>
