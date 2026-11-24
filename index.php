<?php
require_once 'Menu.php';
require_once 'Transaksi.php';

$menuObj = new Menu();
$trxObj = new Transaksi();
$pesanError = "";

if (isset($_POST['proses_transaksi'])) {
    $idMenu = (int) $_POST['id_menu'];
    $jumlah = (int) $_POST['jumlah'];
    $bayar  = (int) $_POST['bayar'];

    $menuDipilih = $menuObj->getMenuById($idMenu);
    if ($menuDipilih) {
        $berhasil = $trxObj->prosesBayar($idMenu, $menuDipilih->getHarga(), $jumlah, $bayar);
        if ($berhasil) {
            header("Location: index.php");
            exit;
        } else {
            $pesanError = "Uang pembayaran kurang dari total belanja!";
        }
    }
}

if (isset($_GET['hapus'])) {
    $idHapus = (int) $_GET['hapus'];
    $trxObj->hapusTransaksi($idHapus);
    header("Location: index.php");
    exit;
}

$daftarMenu = $menuObj->getAllMenu();
$riwayatTrx = $trxObj->getRiwayatTransaksi();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kasir MoodieBites</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #faf7f5; margin: 30px; color: #333; }
        .container { max-width: 1000px; margin: auto; }
        .header { background: #5c3a21; color: #fff; padding: 15px 25px; border-radius: 8px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 22px; }
        .card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); margin-bottom: 25px; }
        .form-grid { display: grid; grid-template-columns: 2fr 1fr 1.5fr 1fr; gap: 12px; align-items: end; }
        label { display: block; font-size: 13px; margin-bottom: 5px; font-weight: 600; }
        select, input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        button { background: #d97724; color: #fff; border: none; padding: 9px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; width: 100%; }
        button:hover { background: #b86018; }
        .alert { background: #ffe0e0; color: #b30000; padding: 10px 15px; border-radius: 5px; margin-bottom: 15px; font-size: 14px; }
        .info-total { margin-top: 12px; font-size: 15px; font-weight: bold; color: #5c3a21; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 14px; }
        th, td { border-bottom: 1px solid #e6e0dc; padding: 10px; text-align: left; }
        th { background-color: #f3ece7; color: #5c3a21; }
        .btn-hapus { color: #c0392b; text-decoration: none; font-weight: 600; font-size: 13px; }
        .badge { background: #f3ece7; color: #5c3a21; padding: 3px 8px; border-radius: 4px; font-size: 12px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Kasir MoodieBites — Point of Sale</h1>
    </div>

    <div class="card">
        <h3 style="margin-top: 0;">Transaksi Baru</h3>
        
        <?php if ($pesanError != ""): ?>
            <div class="alert"><?= $pesanError ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-grid">
                <div>
                    <label>Pilih Menu MoodieBites</label>
                    <select name="id_menu" id="pilih_menu" onchange="hitungTotal()" required>
                        <?php foreach ($daftarMenu as $m): ?>
                            <option value="<?= $m->getIdMenu() ?>" data-harga="<?= $m->getHarga() ?>">
                                [<?= $m->getKategori() ?>] <?= $m->getNamaMenu() ?> - <?= $m->formatRupiah($m->getHarga()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Jumlah (Qty)</label>
                    <input type="number" name="jumlah" id="jumlah_beli" value="1" min="1" oninput="hitungTotal()" required>
                </div>
                <div>
                    <label>Uang Bayar (Rp)</label>
                    <input type="number" name="bayar" placeholder="Contoh: 50000" min="1000" required>
                </div>
                <div>
                    <button type="submit" name="proses_transaksi">Bayar</button>
                </div>
            </div>
            <div class="info-total" id="tampil_total">Total Tagihan: Rp 18.000</div>
        </form>
    </div>

    <div class="card">
        <h3 style="margin-top: 0;">Riwayat Penjualan MoodieBites</h3>
        <table>
            <thead>
                <tr>
                    <th>Kode Nota</th>
                    <th>Menu</th>
                    <th>Kategori</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Bayar</th>
                    <th>Kembalian</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($riwayatTrx)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: #888;">Belum ada transaksi hari ini.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($riwayatTrx as $t): ?>
                    <tr>
                        <td><strong><?= $t->getKodeTrx() ?></strong></td>
                        <td><?= $t->getNamaMenu() ?></td>
                        <td><span class="badge"><?= $t->getKategori() ?></span></td>
                        <td><?= $t->getJumlah() ?>x</td>
                        <td><?= $t->formatRupiah($t->getTotalHarga()) ?></td>
                        <td><?= $t->formatRupiah($t->getBayar()) ?></td>
                        <td><?= $t->formatRupiah($t->getKembalian()) ?></td>
                        <td>
                            <a href="index.php?hapus=<?= $t->getIdTransaksi() ?>" class="btn-hapus" onclick="return confirm('Hapus riwayat transaksi ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function hitungTotal() {
    const select = document.getElementById('pilih_menu');
    const harga = select.options[select.selectedIndex].getAttribute('data-harga');
    const qty = document.getElementById('jumlah_beli').value || 0;
    const total = harga * qty;
    document.getElementById('tampil_total').innerText = 'Total Tagihan: Rp ' + total.toLocaleString('id-ID');
}
window.onload = hitungTotal;
</script>

</body>
</html>