<?php
require __DIR__ . '/db.php';
$type = $_GET['type'] ?? 'barang';
[$from, $to] = dates();

if ($type === 'penjualan') {
    $sql = "SELECT p.tanggal AS Tanggal, p.jam AS Jam, p.kode AS Faktur,
                   COALESCE(NULLIF(p.nama_pelanggan,''), 'Tanpa Nama') AS Pelanggan,
                   i.kode_barang AS Kode_Barang, i.nama_barang AS Nama_Barang,
                   i.satuan AS Satuan, i.qty AS Qty, i.harga AS Harga,
                   i.diskon AS Diskon, i.subtotal AS Subtotal, p.operator AS Operator
            FROM penjualan p JOIN itempenjualan i ON i.kode=p.kode
            WHERE p.tanggal BETWEEN ? AND ? ORDER BY p.tanggal, p.jam, p.kode, i.nourut";
    $stmt = db()->prepare($sql); $stmt->execute([$from, $to]);
    $name = "penjualan_{$from}_sd_{$to}.xls";
} else {
    $search = trim($_GET['search'] ?? '');
    $kategori = trim($_GET['kategori'] ?? '');
    $supplier = trim($_GET['supplier'] ?? '');
    $stok = $_GET['stok'] ?? 'semua';
    $where = []; $params = [];
    if ($search !== '') { $where[] = '(kode LIKE ? OR nama LIKE ? OR kode_barcode LIKE ?)'; $like = "%{$search}%"; array_push($params, $like, $like, $like); }
    if ($kategori !== '') { $where[] = 'kategori = ?'; $params[] = $kategori; }
    if ($supplier !== '') { $where[] = 'supplier = ?'; $params[] = $supplier; }
    if ($stok === 'tersedia') $where[] = '(toko + gudang) > 0';
    if ($stok === 'habis') $where[] = '(toko + gudang) <= 0';
    $sql = "SELECT kode AS Kode, nama AS Nama_Barang, kategori AS Kategori,
        golongan AS Golongan, satuan AS Satuan, toko AS Stok_Toko, gudang AS Stok_Gudang,
        hpp AS HPP, harga_toko AS Harga_Toko, harga_partai AS Harga_Partai,
        harga_cabang AS Harga_Cabang, supplier AS Supplier FROM barang";
    if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
    $sql .= ' ORDER BY nama';
    $stmt = db()->prepare($sql); $stmt->execute($params);
    $name = 'list_barang_filter_' . date('Y-m-d') . '.xls';
}
$rows = $stmt->fetchAll();
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $name . '"');
echo "<html><head><meta charset='UTF-8'></head><body><table border='1'>";
if ($rows) {
    echo '<tr>'; foreach (array_keys($rows[0]) as $h) echo '<th>' . htmlspecialchars(str_replace('_',' ',$h)) . '</th>'; echo '</tr>';
    foreach ($rows as $row) { echo '<tr>'; foreach ($row as $v) echo '<td>' . htmlspecialchars((string)$v) . '</td>'; echo '</tr>'; }
}
echo '</table></body></html>';
