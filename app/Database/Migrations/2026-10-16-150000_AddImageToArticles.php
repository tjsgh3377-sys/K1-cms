<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImageToArticles extends Migration
{
    public function up()
    {
        $this->forge->addColumn('articles', [
            'images' => [
                'type'       => 'JSON',
                'null'       => true,
                'comment'    => '게시글 이미지들 (JSON 배열)',
            ],
            'attachments' => [
                'type'       => 'JSON',
                'null'       => true,
                'comment'    => '파일 첨부들 (JSON 배열)',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('articles', ['images', 'attachments']);
    }
}
