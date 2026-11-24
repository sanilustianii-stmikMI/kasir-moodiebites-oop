<?php
require_once 'Database.php';
require_once 'ProdukBase.php';

class Menu extends ProdukBase {
    private $idMenu;
    private $kategori;

    public function __construct($idMenu = null, $namaMenu = "", $harga = 0, $kategori = "") {
        parent::__construct($namaMenu, $harga);
        $this->idMenu = $idMenu;
        $this->kategori = $kategori;
    }

    public function getAllMenu() {
        $conn = Database::connect();
        $sql = "SELECT m.id_menu, m.nama_menu, m.harga, k.nama_kategori 
                FROM menu m 
                JOIN kategori k ON m.id_kategori = k.id_kategori
                ORDER BY k.id_kategori ASC, m.nama_menu ASC";
        
        $result = $conn->query($sql);
        $listMenu = [];

        while ($row = $result->fetch_assoc()) {
            $listMenu[] = new Menu(
                $row['id_menu'],
                $row['nama_menu'],
                $row['harga'],
                $row['nama_kategori']
            );
        }
        return $listMenu;
    }

    public function getMenuById($id) {
        $conn = Database::connect();
        $stmt = $conn->prepare("SELECT m.id_menu, m.nama_menu, m.harga, k.nama_kategori 
                                FROM menu m JOIN kategori k ON m.id_kategori = k.id_kategori 
                                WHERE m.id_menu = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if ($res) {
            return new Menu($res['id_menu'], $res['nama_menu'], $res['harga'], $res['nama_kategori']);
        }
        return null;
    }

    public function getIdMenu() { return $this->idMenu; }
    public function getKategori() { return $this->kategori; }
}