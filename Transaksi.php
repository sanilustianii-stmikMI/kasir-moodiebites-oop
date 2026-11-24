<?php
require_once 'Database.php';
require_once 'ProdukBase.php';

class Transaksi extends ProdukBase {
    private $idTransaksi;
    private $kodeTrx;
    private $kategori;
    private $jumlah;
    private $totalHarga;
    private $bayar;
    private $kembalian;
    private $waktu;

    public function __construct($idTrx = null, $kodeTrx = "", $namaMenu = "", $kategori = "", $harga = 0, $jumlah = 0, $total = 0, $bayar = 0, $kembalian = 0, $waktu = "") {
        parent::__construct($namaMenu, $harga);
        $this->idTransaksi = $idTrx;
        $this->kodeTrx = $kodeTrx;
        $this->kategori = $kategori;
        $this->jumlah = $jumlah;
        $this->totalHarga = $total;
        $this->bayar = $bayar;
        $this->kembalian = $kembalian;
        $this->waktu = $waktu;
    }

    public static function generateKode() {
        return "MB-" . date("ymd") . rand(100, 999);
    }

    public function prosesBayar($idMenu, $hargaSatuan, $jumlah, $uangBayar) {
        $total = $hargaSatuan * $jumlah;
        if ($uangBayar < $total) {
            return false;
        }

        $kembalian = $uangBayar - $total;
        $kode = self::generateKode();

        $conn = Database::connect();
        $stmt = $conn->prepare("INSERT INTO transaksi (kode_trx, id_menu, jumlah, total_harga, bayar, kembalian) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siiiii", $kode, $idMenu, $jumlah, $total, $uangBayar, $kembalian);
        return $stmt->execute();
    }

    public function getRiwayatTransaksi() {
        $conn = Database::connect();
        $sql = "SELECT t.id_transaksi, t.kode_trx, m.nama_menu, k.nama_kategori, m.harga, 
                       t.jumlah, t.total_harga, t.bayar, t.kembalian, t.waktu_transaksi
                FROM transaksi t
                JOIN menu m ON t.id_menu = m.id_menu
                JOIN kategori k ON m.id_kategori = k.id_kategori
                ORDER BY t.id_transaksi DESC";
        
        $result = $conn->query($sql);
        $riwayat = [];

        while ($row = $result->fetch_assoc()) {
            $riwayat[] = new Transaksi(
                $row['id_transaksi'],
                $row['kode_trx'],
                $row['nama_menu'],
                $row['nama_kategori'],
                $row['harga'],
                $row['jumlah'],
                $row['total_harga'],
                $row['bayar'],
                $row['kembalian'],
                $row['waktu_transaksi']
            );
        }
        return $riwayat;
    }

    public function hapusTransaksi($id) {
        $conn = Database::connect();
        $stmt = $conn->prepare("DELETE FROM transaksi WHERE id_transaksi = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getIdTransaksi() { return $this->idTransaksi; }
    public function getKodeTrx() { return $this->kodeTrx; }
    public function getKategori() { return $this->kategori; }
    public function getJumlah() { return $this->jumlah; }
    public function getTotalHarga() { return $this->totalHarga; }
    public function getBayar() { return $this->bayar; }
    public function getKembalian() { return $this->kembalian; }
    public function getWaktu() { return $this->waktu; }
}