<div class="btn-group" role="group">
    <a href="{{ route('admin.cleaning-orders.show', $order) }}"
       class="btn btn-sm btn-info"
       title="View Details">
        <i class="bi bi-eye"></i>
    </a>
    <button type="button"
            class="btn btn-sm btn-danger delete-order"
            data-url="{{ route('admin.cleaning-orders.destroy', $order) }}"
            title="Delete">
        <i class="bi bi-trash"></i>
    </button>
</div>
