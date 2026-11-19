document.addEventListener('DOMContentLoaded', function () {
  // Buka/tutup sidebar di layar kecil
  var sidebar = document.getElementById('sidebar');
  var btn = document.getElementById('menuBtn');
  if (btn && sidebar) {
    btn.addEventListener('click', function (e) { e.stopPropagation(); sidebar.classList.toggle('show'); });
    document.addEventListener('click', function (e) {
      if (!sidebar.contains(e.target)) sidebar.classList.remove('show');
    });
  }

  // Modal konfirmasi hapus (satu modal dipakai semua tombol hapus)
  var modalHapus = document.getElementById('modalHapus');
  if (modalHapus) {
    modalHapus.addEventListener('show.bs.modal', function (e) {
      var tombol = e.relatedTarget;
      modalHapus.querySelector('form').action = tombol.getAttribute('data-action');
      modalHapus.querySelector('.nama-hapus').textContent = tombol.getAttribute('data-nama');
    });
  }

  // Modal penolakan / catatan (dipakai admin untuk menolak SOS)
  var modalTolak = document.getElementById('modalTolak');
  if (modalTolak) {
    modalTolak.addEventListener('show.bs.modal', function (e) {
      modalTolak.querySelector('form').action = e.relatedTarget.getAttribute('data-action');
    });
  }

  // Pencarian cepat pada tabel: <input data-cari="#idTabel">
  document.querySelectorAll('[data-cari]').forEach(function (input) {
    input.addEventListener('input', function () {
      var kata = input.value.toLowerCase();
      document.querySelectorAll(input.getAttribute('data-cari') + ' tbody tr[data-row]').forEach(function (tr) {
        tr.style.display = tr.textContent.toLowerCase().indexOf(kata) > -1 ? '' : 'none';
      });
    });
  });

  // Alert hilang otomatis
  setTimeout(function () {
    document.querySelectorAll('.alert-auto').forEach(function (a) {
      a.style.transition = 'opacity .5s'; a.style.opacity = 0;
      setTimeout(function () { a.remove(); }, 500);
    });
  }, 5000);

  // Registrasi: tampilkan kolom alamat hanya untuk fasilitas
  var roleRadios = document.querySelectorAll('input[name="role"]');
  var boxAlamat = document.getElementById('boxAlamat');
  function ubahRole() {
    if (!boxAlamat) return;
    var pilih = document.querySelector('input[name="role"]:checked');
    boxAlamat.style.display = pilih && pilih.value === 'fasilitas' ? '' : 'none';
  }
  roleRadios.forEach(function (r) { r.addEventListener('change', ubahRole); });
  ubahRole();

  // Notifikasi admin: kolom golongan hanya saat target = golongan
  var target = document.getElementById('target');
  var boxGol = document.getElementById('boxGolongan');
  function ubahTarget() { if (target && boxGol) boxGol.style.display = target.value === 'golongan' ? '' : 'none'; }
  if (target) { target.addEventListener('change', ubahTarget); ubahTarget(); }

  // Tampil/sembunyi kata sandi
  document.querySelectorAll('[data-toggle-pass]').forEach(function (b) {
    b.addEventListener('click', function () {
      var inp = document.querySelector(b.getAttribute('data-toggle-pass'));
      if (!inp) return;
      var tampil = inp.type === 'password';
      inp.type = tampil ? 'text' : 'password';
      b.querySelector('i').className = tampil ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
  });
});
