/* ========== DIANTRA BAKERY APP — CUSTOM JS ========== */

;(function() {
  'use strict';

  // === PAGE LOADER ===
  const loader = document.getElementById('pageLoader');
  if (loader) {
    window.addEventListener('load', function() {
      setTimeout(function() {
        loader.classList.add('hidden');
        setTimeout(function() { loader.remove(); }, 400);
      }, 150);
    });
  }

  // === DATATABLES ===
  if (typeof jQuery !== 'undefined' && typeof $.fn.DataTable !== 'undefined') {
    $('.table-datatable').DataTable({
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
        emptyTable: 'Tidak ada data'
      },
      pageLength: 25,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Semua']],
      order: [],
      columnDefs: [
        { orderable: false, targets: -1 }
      ]
    });
  }

  // === SWEETALERT2: FLASH MESSAGES ===
  if (typeof Swal !== 'undefined') {
    var flashDataEl = document.getElementById('flash-data');
    if (flashDataEl) {
      var flash = {
        success: flashDataEl.dataset.success,
        error: flashDataEl.dataset.error,
        warning: flashDataEl.dataset.warning,
        info: flashDataEl.dataset.info
      };

      if (flash.success) {
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: flash.success, timer: 3000, showConfirmButton: false, toast: true, position: 'top-end' });
      }
      if (flash.error) {
        Swal.fire({ icon: 'error', title: 'Gagal!', text: flash.error, confirmButtonText: 'OK' });
      }
      if (flash.warning) {
        Swal.fire({ icon: 'warning', title: 'Peringatan', text: flash.warning, confirmButtonText: 'OK' });
      }
      if (flash.info) {
        Swal.fire({ icon: 'info', title: 'Info', text: flash.info, timer: 3000, showConfirmButton: false, toast: true, position: 'top-end' });
      }
    }

    // === SWEETALERT2: DELETE CONFIRMATION ===
    document.querySelectorAll('.btn-delete-confirm').forEach(function(btn) {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        var form = this.closest('form');
        var message = this.dataset.confirm || 'Yakin hapus data ini?';
        Swal.fire({
          title: 'Konfirmasi',
          text: message,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#C44D4D',
          cancelButtonColor: '#9E9E9E',
          confirmButtonText: 'Ya, hapus!',
          cancelButtonText: 'Batal'
        }).then(function(result) {
          if (result.isConfirmed) { form.submit(); }
        });
      });
    });
  }

})();
