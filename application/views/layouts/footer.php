</div><!-- end #main-content -->

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmxc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

<script>
// ── CSRF Token & Global AJAX Setup (Tugas 2) ──
var BASE_URL = "<?= base_url() ?>";
var csrfName = "<?= $this->security->get_csrf_token_name(); ?>";
var csrfCookie = "<?= config_item('csrf_cookie_name'); ?>";

// Helper untuk membaca nilai Cookie berdasarkan nama
function getCookie(name) {
    var value = "; " + document.cookie;
    var parts = value.split("; " + name + "=");
    if (parts.length === 2) return parts.pop().split(";").shift();
}

// ── Global AJAX Setup ──
$.ajaxSetup({
    beforeSend: function(xhr, settings) {
        // CSRF verification hanya dibutuhkan untuk request POST/PUT/DELETE dll. (Bukan GET/HEAD/OPTIONS)
        var method = settings.type ? settings.type.toUpperCase() : 'GET';
        if (method !== 'GET' && method !== 'HEAD' && method !== 'OPTIONS') {
            // Ambil token CSRF terbaru dari cookie, fallback ke PHP get_csrf_hash() jika cookie kosong
            var csrfHash = getCookie(csrfCookie) || "<?= $this->security->get_csrf_hash(); ?>";
            
            if (typeof settings.data === 'string') {
                // Kasus 1: Request berupa JSON String (jika contentType: application/json)
                if (settings.contentType && settings.contentType.indexOf('application/json') !== -1) {
                    try {
                        var json = JSON.parse(settings.data);
                        if ($.isPlainObject(json) && !json[csrfName]) {
                            json[csrfName] = csrfHash;
                            settings.data = JSON.stringify(json);
                        }
                    } catch (e) {
                        // Gagal parsing JSON, biarkan data aslinya
                    }
                } else {
                    // Kasus 2: Query String biasa (misalnya hasil dari $(form).serialize())
                    if (settings.data.indexOf(csrfName + '=') === -1) {
                        settings.data += (settings.data.length > 0 ? '&' : '') + encodeURIComponent(csrfName) + '=' + encodeURIComponent(csrfHash);
                    }
                }
            } else if ($.isPlainObject(settings.data)) {
                // Kasus 3: Objek Data biasa (Plain Object)
                settings.data[csrfName] = csrfHash;
            } else if (settings.data instanceof FormData) {
                // Kasus 4: Upload file / multipart form (FormData)
                if (!settings.data.has(csrfName)) {
                    settings.data.append(csrfName, csrfHash);
                }
            } else if (!settings.data) {
                // Kasus 5: Data kosong / undefined
                settings.data = {};
                settings.data[csrfName] = csrfHash;
            }
        }
    },
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
