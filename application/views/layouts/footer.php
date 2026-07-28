</div><!-- end #main-content -->

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

<script>
// ── CSRF Token (jika diaktifkan) ──
var BASE_URL = "<?= base_url() ?>";

// ── Global AJAX Setup ──
$.ajaxSetup({
    error: function(xhr) {
        var msg = 'Terjadi kesalahan sistem.';
        try {
            var r = JSON.parse(xhr.responseText);
            if (r.message) msg = r.message;
        } catch(e) {}
        showToast(msg, 'error');
    }
});

// ── Toast Notification ──
function showToast(message, type) {
    type = type || 'success';
    var icon = type === 'success' ? '<i class="bi bi-check-circle-fill"></i>' : '<i class="bi bi-x-circle-fill"></i>';
    var toast = $('<div class="toast-notif toast-' + type + '">' + icon + '<span>' + message + '</span></div>');
    $('#toast-container').append(toast);
    setTimeout(function() { toast.fadeOut(400, function() { $(this).remove(); }); }, 3500);
}

// ── Sidebar Toggle (mobile) ──
$('#sidebarToggle').on('click', function() {
    $('#sidebar').toggleClass('show');
});

// ── Format Rupiah ──
function formatRupiah(angka) {
    return 'Rp ' + parseFloat(angka).toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
}
</script>
</body>
</html>
