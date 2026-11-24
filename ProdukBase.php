<?php

class ProdukBase {
    protected $namaMenu;
    protected $harga;

    public function __construct($namaMenu, $harga) {
        $this->namaMenu = $namaMenu;
        $this->harga = $harga;
    }

    public function formatRupiah($angka) {
        return "Rp " . number_format($angka, 0, ',', '.');
    }

    public function getNamaMenu() {
        return $this->namaMenu;
    }

    public function getHarga() {
        return $this->harga;
    }
}