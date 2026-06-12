<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Belanja Dinamis Cihuy</title>
    <link href="https://fonts.googleapis.com/css2 family=Poppins:wght@300;400;600;700&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style-struk.css">
</head>
<body>

<div class="container">
    <div class="input-card">
        <h3>Input Menu Belanja</h3>
        
        <div class="form-group">
            <label for="barang_nama">Nama Barang / Menu:</label>
            <input type="text" id="barang_nama" placeholder="Contoh: Es Kopi Susu">
        </div>
        <div class="form-group">
            <label for="barang_harga">Harga (Rp):</label>
            <input type="number" id="barang_harga" placeholder="Contoh: 25000">
        </div>
        <div class="form-group">
            <label for="barang_kategori">Kategori:</label>
            <select id="barang_kategori">
                <option value="Elektronik">Elektronik (Disc 10k, Pajak 2.5k)</option>
                <option value="Aaksesoris">Aaksesoris (Disc 5k, Pajak 1k)</option>
                <option value="Makanan">Makanan/Minuman (Disc 0, Pajak 1.5k)</option>
            </select>
        </div>
        <button type="button" id="btn-tambah" class="btn-tambah">＋ Tambah ke Struk</button>

        <div class="divider"></div>

        <h3>Seting Patungan</h3>
        <div class="form-group">
            <label for="input_orang">Jumlah Orang Patungan:</label>
            <input type="number" id="input_orang" min="1" value="2">
        </div>
        <div class="form-group">
            <label for="diskon_member">Diskon Tambahan (Rp):</label>
            <input type="number" id="diskon_member" min="0" value="0">
        </div>
        <button type="button" id="btn-reset" class="btn-reset">Reset Semua Data</button>
    </div>

    <div class="receipt-card">
        <div class="header">
            <h2>SENJA COFFEE & SHOP</h2>
            <p>Jl. Kenangan No. 37, Kota Bogor</p>
            <p>Tanggal: <span id="struk-tanggal">--:--</span></p>
        </div>

        <div class="divider"></div>

        <div class="item-list" id="struk-item-list">
            <p style="text-align: center; color: var(--text-muted); font-style: italic;">Belum ada barang dimasukkan.</p>
        </div>

        <div class="divider"></div>

        <div class="summary-table">
            <div class="summary-row">
                <span>Total Kotor:</span>
                <span id="out-kotor">Rp 0</span>
            </div>
            <div class="summary-row" style="color: #ef4444;">
                <span>Total Diskon:</span>
                <span id="out-diskon">-Rp 0</span>
            </div>
            <div class="summary-row">
                <span>Total Pajak:</span>
                <span id="out-pajak">+Rp 0</span>
            </div>
            
            <div class="divider"></div>
            
            <div class="summary-row total-row">
                <span>TOTAL AKHIR:</span>
                <span id="out-akhir" style="color: var(--accent);">Rp 0</span>
            </div>
        </div>

        <div class="patungan-box">
            <div class="patungan-title" id="patungan-title">Masing-masing Bayar (2 Orang)</div>
            <div class="patungan-amount" id="out-patungan">Rp 0</div>
        </div>

        <div class="footer-thanks">
            <p>*** Terima Kasih Telah Berbelanja ***</p>
            <p>Powered by Pakih POS Engine v4.0 (JS Dynamic)</p>
        </div>
    </div>
</div>

<script>
    // Penampung data belanjaan
    let dataBelanjaan = [];

    // Mengambil elemen HTML
    const inputNama = document.getElementById('barang_nama');
    const inputHarga = document.getElementById('barang_harga');
    const inputKategori = document.getElementById('barang_kategori');
    const btnTambah = document.getElementById('btn-tambah');
    const btnReset = document.getElementById('btn-reset');
    
    const inputOrang = document.getElementById('input_orang');
    const inputDiskonTambahan = document.getElementById('diskon_member');

    // Mengeset tanggal otomatis di struk sesuai waktu sekarang
    const skrg = new Date();
    document.getElementById('struk-tanggal').innerText = `${String(skrg.getDate()).padStart(2, '0')}-${String(skrg.getMonth()+1).padStart(2, '0')}-${skrg.getFullYear()} ${String(skrg.getHours()).padStart(2, '0')}:${String(skrg.getMinutes()).padStart(2, '0')} WIB`;

    // Fungsi format rupiah otomatis
    function formatRupiah(angka) {
        return 'Rp ' + angka.toLocaleString('id-ID');
    }

    // FUNGSI UTAMA: Menghitung Total dan Menggambar Ulang Struk
    function updateStruk() {
        const itemListContainer = document.getElementById('struk-item-list');
        itemListContainer.innerHTML = ''; // Kosongkan struk lama

        if (dataBelanjaan.length === 0) {
            itemListContainer.innerHTML = '<p style="text-align: center; color: var(--text-muted); font-style: italic;">Belum ada barang dimasukkan.</p>';
            document.getElementById('out-kotor').innerText = 'Rp 0';
            document.getElementById('out-diskon').innerText = '-Rp 0';
            document.getElementById('out-pajak').innerText = '+Rp 0';
            document.getElementById('out-akhir').innerText = 'Rp 0';
            document.getElementById('out-patungan').innerText = 'Rp 0';
            return;
        }

        let totalKotor = 0;
        let totalDiskon = 0;
        let totalPajak = 0;

        // Loop array data belanjaan pake logika hitungan lu kemarin
        dataBelanjaan.forEach(barang => {
            let diskonItem = 0;
            let pajakItem = 0;

            if (barang.kategori === "Elektronik") {
                diskonItem = 10000;
                pajakItem = 2500;
            } else if (barang.kategori === "Aaksesoris") {
                diskonItem = 5000;
                pajakItem = 1000;
            } else if (barang.kategori === "Makanan") {
                diskonItem = 0;
                pajakItem = 1500;
            }

            totalKotor += barang.harga;
            totalDiskon += diskonItem;
            totalPajak += pajakItem;

            // Bikin element baris barang di HTML struk
            const itemRow = document.createElement('div');
            itemRow.className = 'item-row';
            itemRow.innerHTML = `
                <div class="item-name">${barang.nama}</div>
                <div class="item-price">${formatRupiah(barang.harga)}</div>
            `;
            itemListContainer.appendChild(itemRow);

            if (diskonItem > 0) {
                const promoRow = document.createElement('div');
                promoRow.className = 'promo-text';
                promoRow.innerText = `  * Disc: -${formatRupiah(diskonItem)}`;
                itemListContainer.appendChild(promoRow);
            }
        });

        // Ambil data seting patungan tambahan
        const jmlOrang = parseInt(inputOrang.value) || 1;
        const discTambahan = parseInt(inputDiskonTambahan.value) || 0;
        
        let totalDiskonAkhir = totalDiskon + discTambahan;
        let totalAkhir = (totalKotor - totalDiskonAkhir) + totalPajak;
        if(totalAkhir < 0) totalAkhir = 0; // proteksi biar gak minus
        
        let patungan = totalAkhir / jmlOrang;

        // Update angka-angka ringkasan di struk sebelah kanan
        document.getElementById('out-kotor').innerText = formatRupiah(totalKotor);
        document.getElementById('out-diskon').innerText = '-' + formatRupiah(totalDiskonAkhir);
        document.getElementById('out-pajak').innerText = '+' + formatRupiah(totalPajak);
        document.getElementById('out-akhir').innerText = formatRupiah(totalAkhir);
        
        document.getElementById('patungan-title').innerText = `Masing-masing Bayar (${jmlOrang} Orang)`;
        document.getElementById('out-patungan').innerText = formatRupiah(patungan);
    }

    // Event Klik tombol "Tambah ke Struk"
    btnTambah.addEventListener('click', function() {
        const nama = inputNama.value.trim();
        const harga = parseInt(inputHarga.value);
        const kategori = inputKategori.value;

        // Validasi input ga boleh kosong
        if (nama === '' || isNaN(harga) || harga <= 0) {
            alert('Woi, isi dulu nama menu ama harganya yang bener! 😂');
            return;
        }

        // Masukkan data barang baru ke dalam list array
        dataBelanjaan.push({ nama, harga, kategori });

        // Bersihkan kembali inputan nama & harga biar siap ngetik menu selanjutnya
        inputNama.value = '';
        inputHarga.value = '';
        inputNama.focus();

        // Jalankan fungsi cetak struk otomatis
        updateStruk();
    });

    // Jalankan kalkulasi otomatis kalau user ganti inputan jumlah orang / diskon member secara real-time
    inputOrang.addEventListener('input', updateStruk);
    inputDiskonTambahan.addEventListener('input', updateStruk);

    // Reset data
    btnReset.addEventListener('click', function() {
        dataBelanjaan = [];
        inputDiskonTambahan.value = 0;
        inputOrang.value = 2;
        updateStruk();
    });
</script>

</body>
</html>