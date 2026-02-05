// dashbord click active start
document.addEventListener("DOMContentLoaded", function () {
    const navLinks = document.querySelectorAll("#sidebar .nav-link");
    const currentPath = window.location.pathname;

    navLinks.forEach(link => {
        // Remove old active
        link.classList.remove("active");

        // URL match (refresh ke baad)
        if (link.getAttribute("href") === currentPath) {
            link.classList.add("active");
        }

        // Click par active + save
        link.addEventListener("click", function () {
            localStorage.setItem("activeSidebarLink", this.getAttribute("href"));

            navLinks.forEach(l => l.classList.remove("active"));
            this.classList.add("active");
        });
    });

    // LocalStorage se restore
    const savedLink = localStorage.getItem("activeSidebarLink");
    if (savedLink) {
        navLinks.forEach(link => {
            if (link.getAttribute("href") === savedLink) {
                link.classList.add("active");
            }
        });
    }
});
// dashbord click active end



document.addEventListener('click', function (e) {

    const badge = e.target.closest('.user-status-toggle');
    const vBadge = e.target.closest('.verification-status-toggle');

    if (badge) {
        const userId = badge.dataset.id;
        fetch(`/dashboard/users/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'active') {
                badge.className = 'badge px-3 py-2 user-status-toggle bg-success-subtle text-success';
                badge.innerHTML = '<i class="fas fa-unlock me-1"></i> Active';
                badge.title = 'Click to freeze';
                toastr.success(data.message);
            } else if (data.status === 'frozen') {
                badge.className = 'badge px-3 py-2 user-status-toggle bg-danger-subtle text-danger';
                badge.innerHTML = '<i class="fas fa-lock me-1"></i> Frozen';
                badge.title = 'Click to unfreeze';
                toastr.warning(data.message);
            } else {
                toastr.error(data.message || 'Action not allowed');
            }
        })
        .catch(() => toastr.error('Something went wrong'));
    }

    if (vBadge) {
        const userId = vBadge.dataset.id;
        const currentStatus = vBadge.innerText.trim().toLowerCase();
        const newStatus = currentStatus === 'approved' ? 'pending' : 'verified';

        fetch(`/dashboard/approvals/${userId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (newStatus === 'verified') {
                    vBadge.className = 'badge px-3 py-2 verification-status-toggle bg-success-subtle text-success';
                    vBadge.innerHTML = '<i class="fas fa-check-circle me-1"></i> Approved';
                    vBadge.title = 'Click to mark as pending';
                } else {
                    vBadge.className = 'badge px-3 py-2 verification-status-toggle bg-warning-subtle text-warning';
                    vBadge.innerHTML = '<i class="fas fa-clock me-1"></i> Pending';
                    vBadge.title = 'Click to verify';
                }
                toastr.success(data.success);
            } else {
                toastr.error('Failed to update status');
            }
        })
        .catch(() => toastr.error('Something went wrong'));
    }
});

/**
 * Initialize intl-tel-input for all elements with class .intl-phone
 */
function initIntlPhone() {
    const phoneInputs = document.querySelectorAll(".intl-phone");
    phoneInputs.forEach(input => {
        // Prevent double initialization
        if (input.dataset.intlInitialized) return;
        
        const fullPhoneInput = document.querySelector(input.dataset.fullField || "#full_phone");
        
        const iti = window.intlTelInput(input, {
            initialCountry: "auto",
            geoIpLookup: function(success, failure) {
                fetch("https://ipapi.co/json")
                    .then(res => res.json())
                    .then(data => success(data.country_code))
                    .catch(() => success("pk"));
            },
            separateDialCode: true,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
        });

        const updateFullPhone = () => {
            if (fullPhoneInput) {
                fullPhoneInput.value = iti.getNumber();
            }
        };

        input.addEventListener('change', updateFullPhone);
        input.addEventListener('keyup', updateFullPhone);

        // Numeric only and digit limitation
        input.addEventListener('keypress', function(e) {
            if (e.which < 48 || e.which > 57) e.preventDefault();
            
            const placeholder = this.getAttribute('placeholder');
            if (placeholder) {
                const maxDigits = placeholder.replace(/[^0-9]/g, '').length;
                if (this.value.replace(/[^0-9]/g, '').length >= maxDigits) {
                    e.preventDefault();
                }
            }
        });

        // Store instance for later use if needed
        input.iti = iti;
        input.dataset.intlInitialized = "true";
    });
}

document.addEventListener('DOMContentLoaded', initIntlPhone);
