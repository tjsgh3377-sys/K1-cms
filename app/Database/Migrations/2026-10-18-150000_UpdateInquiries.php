<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateInquiries extends Migration
{
    public function up()
    {
        // purpose를 service로 변경
        if ($this->db->fieldExists('purpose', 'inquiries')) {
            $this->forge->dropColumn('inquiries', 'purpose');
        }

        // 필드 추가
        if (!$this->db->fieldExists('service', 'inquiries')) {
            $this->forge->addColumn('inquiries', [
                'service' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'comment'    => '서비스 종류 (card, leaflet, logo, sticker, other)',
                ],
            ]);
        }

        if (!$this->db->fieldExists('status', 'inquiries')) {
            $this->forge->addColumn('inquiries', [
                'status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'new',
                    'comment'    => '상태 (new, contacted, completed, rejected)',
                ],
            ]);
        }

        if (!$this->db->fieldExists('ip_address', 'inquiries')) {
            $this->forge->addColumn('inquiries', [
                'ip_address' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                ],
            ]);
        }

        // 필드 크기 확장
        if ($this->db->fieldExists('name', 'inquiries')) {
            $this->forge->modifyColumn('inquiries', [
                'name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                ],
            ]);
        }
    }

    public function down()
    {
        // 롤백: service를 purpose로 되돌리기
        if ($this->db->fieldExists('service', 'inquiries')) {
            $this->forge->dropColumn('inquiries', 'service');
        }

        if ($this->db->fieldExists('status', 'inquiries')) {
            $this->forge->dropColumn('inquiries', 'status');
        }

        if ($this->db->fieldExists('ip_address', 'inquiries')) {
            $this->forge->dropColumn('inquiries', 'ip_address');
        }

        // purpose 필드 재생성
        if (!$this->db->fieldExists('purpose', 'inquiries')) {
            $this->forge->addColumn('inquiries', [
                'purpose' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                ],
            ]);
        }
    }
}
