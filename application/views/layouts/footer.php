</div><!-- end #main-content -->

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

<script>
// ── CSRF Token & Global AJAX Setup ──
var BASE_URL = "<?= base_url() ?>";

// Gunakan ajaxPrefilter karena jauh lebih handal mencegat data POST yang kosong
$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
    var method = options.type ? options.type.toUpperCase() : 'GET';
    
    // Hanya sisipkan token jika metode bukan GET (seperti POST, PUT, DELETE)
    if (method !== 'GET' && method !== 'HEAD' && method !== 'OPTIONS') {
        var csrfName = $('meta[name="csrf-token-name"]').attr('content');
        var csrfHash = $('meta[name="csrf-token-hash"]').attr('content');
        
        // Hindari error jika tag meta belum termuat
        if (!csrfName || !csrfHash) return;

        if (originalOptions.data instanceof FormData) {
            // Kasus Upload File
            if (!originalOptions.data.has(csrfName)) {
                originalOptions.data.append(csrfName, csrfHash);
            }
        } else if (options.contentType && options.contentType.indexOf('application/json') !== -1) {
            // Kasus Format JSON
            try {
                var json = JSON.parse(options.data || '{}');
                json[csrfName] = csrfHash;
                options.data = JSON.stringify(json);
            } catch(e) {}
        } else {
            // Kasus Data Kosong (Seperti Tombol Hapus) atau Form Biasa
            var tokenQuery = encodeURIComponent(csrfName) + '=' + encodeURIComponent(csrfHash);
            if (!options.data) {
                options.data = tokenQuery; // Paksa isi dengan token jika sebelumnya kosong
            } else if (typeof options.data === 'string' && options.data.indexOf(csrfName + '=') === -1) {
                options.data += '&' + tokenQuery;
            }
        }
    }
});

// Penanganan Error tetap menggunakan ajaxSetup
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
    $('#sidebar').addClass('show');
    $('#sidebar-overlay').addClass('show');
});

// Menutup sidebar saat tombol X atau area luar diklik
$('#sidebarClose, #sidebar-overlay').on('click', function() {
    $('#sidebar').removeClass('show');
    $('#sidebar-overlay').removeClass('show');
});

// ── Format Rupiah ──
function formatRupiah(angka) {
    return 'Rp ' + parseFloat(angka).toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
}
</script>
</body>
</html>
