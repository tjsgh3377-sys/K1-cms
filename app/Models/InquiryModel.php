<?php

namespace App\Models;

use CodeIgniter\Model;

class InquiryModel extends Model
{
    protected $table          = 'inquiries';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $allowedFields  = ['service', 'name', 'email', 'phone', 'company', 'message', 'status', 'ip_address', 'is_read'];
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;

    protected $validationRules = [
        'service' => 'required|in_list[card,leaflet,logo,sticker,other]',
        'name'    => 'required|max_length[100]',
        'email'   => 'required|valid_email|max_length[100]',
        'phone'   => 'required|max_length[20]',
        'message' => 'required',
    ];
    protected $validationMessages = [
        'service' => ['required' => '서비스를 선택해 주세요.'],
        'name'    => ['required' => '이름을 입력해 주세요.'],
        'email'   => [
            'required'    => '이메일을 입력해 주세요.',
            'valid_email' => '이메일 주소를 다시 확인해 주세요.',
        ],
        'phone'   => ['required' => '연락처를 입력해 주세요.'],
        'message' => ['required' => '문의 내용을 입력해 주세요.'],
    ];

    public function getList(int $per_page, int $page = 1)
    {
        return $this->orderBy('id', 'DESC')->paginate($per_page, 'default', $page);
    }

    /**
     * 아직 읽지 않은 문의 수 — 대시보드와 좌측 메뉴 배지에 쓴다.
     */
    public function countUnread(): int
    {
        return $this->where('is_read', 0)->countAllResults();
    }
}
