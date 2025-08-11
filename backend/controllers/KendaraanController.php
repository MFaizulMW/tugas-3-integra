<?php
namespace App\Controllers;

use App\Models\RefKendaraan;

class KendaraanController {
    protected $model;

    public function __construct() {
        $this->model = new RefKendaraan();
    }

    public function index() {
        $items = $this->model->all();
        echo json_encode(['code'=>200,'message'=>'OK','data'=>$items]);
    }

    public function show($id) {
        $item = $this->model->find((int)$id);
        if (!$item) {
            http_response_code(404);
            echo json_encode(['code'=>404,'message'=>'Not Found','details'=>'Record not found']);
            return;
        }
        echo json_encode(['code'=>200,'message'=>'OK','data'=>$item]);
    }

    public function create() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            http_response_code(400);
            echo json_encode(['code'=>400,'message'=>'Bad Request','details'=>'Invalid JSON']);
            return;
        }
        // Minimal validation
        if (empty($input['kendaraan']) || empty($input['kendaraan_nomor'])) {
            http_response_code(422);
            echo json_encode(['code'=>422,'message'=>'Validation Error','details'=>'kendaraan and kendaraan_nomor are required']);
            return;
        }
        $created = $this->model->create($input);
        echo json_encode(['code'=>201,'message'=>'Created','data'=>$created]);
    }

    public function update($id) {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            http_response_code(400);
            echo json_encode(['code'=>400,'message'=>'Bad Request','details'=>'Invalid JSON']);
            return;
        }
        $exists = $this->model->find((int)$id);
        if (!$exists) {
            http_response_code(404);
            echo json_encode(['code'=>404,'message'=>'Not Found','details'=>'Record not found']);
            return;
        }
        $updated = $this->model->update((int)$id, $input);
        echo json_encode(['code'=>200,'message'=>'Updated','data'=>$updated]);
    }

    public function delete($id) {
        $exists = $this->model->find((int)$id);
        if (!$exists) {
            http_response_code(404);
            echo json_encode(['code'=>404,'message'=>'Not Found','details'=>'Record not found']);
            return;
        }
        $this->model->delete((int)$id);
        echo json_encode(['code'=>200,'message'=>'Deleted']);
    }
}
