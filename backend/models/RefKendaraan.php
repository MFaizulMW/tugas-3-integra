<?php
namespace App\Models;

class RefKendaraan extends BaseModel {
    protected $table = 'ref_kendaraan';

    public function all(): array {
        $stmt = $this->db->query("SELECT * FROM ref_kendaraan ORDER BY kendaraan_id DESC");
        return $stmt->fetchAll();
    }

    public function find(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM ref_kendaraan WHERE kendaraan_id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data) {
        $stmt = $this->db->prepare("
            INSERT INTO ref_kendaraan (instansi_id, kendaraan_kode, kendaraan, kendaraan_tahun, kendaraan_jenis, kendaraan_nomor, kendaraan_mesin, kendaraan_warna, image)
            VALUES (:instansi_id, :kendaraan_kode, :kendaraan, :kendaraan_tahun, :kendaraan_jenis, :kendaraan_nomor, :kendaraan_mesin, :kendaraan_warna, :image)
        ");
        $stmt->execute([
            ':instansi_id' => $data['instansi_id'] ?? null,
            ':kendaraan_kode' => $data['kendaraan_kode'] ?? null,
            ':kendaraan' => $data['kendaraan'] ?? null,
            ':kendaraan_tahun' => $data['kendaraan_tahun'] ?? null,
            ':kendaraan_jenis' => $data['kendaraan_jenis'] ?? null,
            ':kendaraan_nomor' => $data['kendaraan_nomor'] ?? null,
            ':kendaraan_mesin' => $data['kendaraan_mesin'] ?? null,
            ':kendaraan_warna' => $data['kendaraan_warna'] ?? null,
            ':image' => $data['image'] ?? null,
        ]);
        $id = (int)$this->db->lastInsertId();
        return $this->find($id);
    }

    public function update(int $id, array $data) {
        // Build dynamic set
        $fields = [];
        $params = [':id' => $id];
        $allowed = ['instansi_id','kendaraan_kode','kendaraan','kendaraan_tahun','kendaraan_jenis','kendaraan_nomor','kendaraan_mesin','kendaraan_warna','image'];
        foreach ($allowed as $col) {
            if (array_key_exists($col, $data)) {
                $fields[] = "{$col} = :{$col}";
                $params[":{$col}"] = $data[$col];
            }
        }
        if (empty($fields)) return $this->find($id);
        $sql = "UPDATE ref_kendaraan SET ".implode(', ', $fields)." WHERE kendaraan_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $this->find($id);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM ref_kendaraan WHERE kendaraan_id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
