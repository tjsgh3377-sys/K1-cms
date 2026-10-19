<?php

namespace App\Controllers\Api;

use App\Models\InquiryModel;
use CodeIgniter\REST\ResourceController;

class Inquiry extends ResourceController
{
    protected $modelName = 'App\Models\InquiryModel';
    protected $format = 'json';

    public function submit()
    {
        $rules = [
            'service' => 'required|in_list[card,leaflet,logo,sticker,other]',
            'name' => 'required|string|max_length[100]',
            'email' => 'required|valid_email',
            'phone' => 'required|string|max_length[20]',
            'company' => 'string|max_length[100]',
            'message' => 'required|string|max_length[5000]',
        ];

        if (!$this->validate($rules)) {
            return $this->respond([
                'status' => false,
                'message' => '입력 정보가 올바르지 않습니다.',
                'errors' => $this->validator->getErrors()
            ], 400);
        }

        $data = [
            'service' => $this->request->getPost('service'),
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'company' => $this->request->getPost('company'),
            'message' => $this->request->getPost('message'),
            'ip_address' => $this->request->getIPAddress(),
            'status' => 'new',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $model = new InquiryModel();

        if ($model->insert($data)) {
            return $this->respondCreated([
                'status' => true,
                'message' => '상담 신청이 완료되었습니다. 빠른 시간 내에 연락드리겠습니다.',
                'inquiry_id' => $model->getInsertID()
            ]);
        } else {
            return $this->respond([
                'status' => false,
                'message' => '저장 중 오류가 발생했습니다. 다시 시도해주세요.'
            ], 500);
        }
    }
}
