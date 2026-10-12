<script>
    // Konfirmasi SweetAlert untuk form bertanda .js-confirm (data-confirm="pesan")
    document.querySelectorAll('form.js-confirm').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (form.dataset.confirmed) return;
            e.preventDefault();
            Swal.fire({
                text: form.dataset.confirm || 'Lanjutkan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, lanjutkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#1d4ed8'
            }).then(function (r) {
                if (r.isConfirmed) { form.dataset.confirmed = 1; form.submit(); }
            });
        });
    });
</script>
