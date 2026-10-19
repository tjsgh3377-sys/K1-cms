<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCategoryToGalleries extends Migration
{
    public function up()
    {
        // category 필드 추가
        if (!$this->db->fieldExists('category', 'galleries')) {
            $this->forge->addColumn('galleries', [
                'category' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'comment'    => '카테고리 (card, leaflet, logo, sticker)',
                    'after'      => 'title',
                ],
            ]);
        }

        // description 필드 추가
        if (!$this->db->fieldExists('description', 'galleries')) {
            $this->forge->addColumn('galleries', [
                'description' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'comment'    => '포트폴리오 설명',
                    'after'      => 'category',
                ],
            ]);
        }
    }

    public function down()
    {
        // 롤백: 필드 제거
        if ($this->db->fieldExists('category', 'galleries')) {
            $this->forge->dropColumn('galleries', 'category');
        }

        if ($this->db->fieldExists('description', 'galleries')) {
            $this->forge->dropColumn('galleries', 'description');
        }
    }
}
