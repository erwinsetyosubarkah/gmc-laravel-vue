// contact.js

window.initNovenaContact = function() {
    (function ($) {
        'use strict';

        // Cari elemen terbaru di DOM Vue saat ini
        var form = $('.contact__form'),
            message = $('.contact__msg');

        // Jika form tidak ditemukan di halaman ini, hentikan fungsi agar tidak boros memori
        if (!form.length) return;

        // Success function
        function done_func(response) {
            message.fadeIn().removeClass('alert-danger').addClass('alert-success');
            message.text(response);
            setTimeout(function () {
                message.fadeOut();
            }, 2000);
            form.find('input:not([type="submit"]), textarea').val('');
        }

        // Fail function
        function fail_func(data) {
            // Perbaikan bug bawaan template: harusnya alert-danger saat gagal
            message.fadeIn().removeClass('alert-success').addClass('alert-danger');
            message.text(data.responseText || 'Terjadi kesalahan, silakan coba lagi.');
            setTimeout(function () {
                message.fadeOut();
            }, 2000);
        }

        // Lepas event handler lama (.off) lalu pasang yang baru (.on) agar tidak double submit
        form.off('submit').on('submit', function (e) {
            e.preventDefault();
            var form_data = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: form_data
            })
            .done(done_func)
            .fail(fail_func);
        });

    })(jQuery);
};

// Tetap jalankan otomatis saat load pertama kali untuk mengantisipasi elemen statis non-Vue
window.addEventListener('DOMContentLoaded', window.initNovenaContact);
