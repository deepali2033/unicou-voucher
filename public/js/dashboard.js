document.addEventListener('click', function (e) {

    const badge = e.target.closest('.user-status-toggle');
    if (!badge) return;

    // if (!confirm('Change user account status?')) return;

    const userId = badge.dataset.id;

    fetch(`/admin/users/${userId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {

        if (data.status === 'active') {
            badge.className =
                'badge px-3 py-2 user-status-toggle bg-success-subtle text-success';
            badge.innerHTML =
                '<i class="fas fa-unlock me-1"></i> Active';
            badge.title = 'Click to freeze';

            toastr.success(data.message);

        } else if (data.status === 'frozen') {
            badge.className =
                'badge px-3 py-2 user-status-toggle bg-danger-subtle text-danger';
            badge.innerHTML =
                '<i class="fas fa-lock me-1"></i> Frozen';
            badge.title = 'Click to unfreeze';

            toastr.warning(data.message);

        } else {
            toastr.error(data.message || 'Action not allowed');
        }
    })
    .catch(() => {
        toastr.error('Something went wrong');
    });
});
