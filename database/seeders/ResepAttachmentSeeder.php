<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ResepAttachment;

class ResepAttachmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        $attachments = [

            // Resep ID 1 - Nasi Goreng Spesial
            [
                'resep_id' => 1,
                'mimetype' => 'image/jpeg',
                'path' => 'resep/nasi-goreng-1.jpg',
            ],
            [
                'resep_id' => 1,
                'mimetype' => 'image/jpeg',
                'path' => 'resep/nasi-goreng-2.jpg',
            ],

            // Resep ID 2 - Mie Goreng Jawa
            [
                'resep_id' => 2,
                'mimetype' => 'image/jpeg',
                'path' => 'resep/mie-goreng-1.jpg',
            ],

            // Resep ID 3 - Telur Dadar Crispy
            [
                'resep_id' => 3,
                'mimetype' => 'image/jpeg',
                'path' => 'resep/telur-dadar-1.jpg',
            ],

            // Resep ID 4 - Ayam Kecap Pedas
            [
                'resep_id' => 4,
                'mimetype' => 'image/jpeg',
                'path' => 'resep/ayam-kecap-1.jpg',
            ],
            [
                'resep_id' => 4,
                'mimetype' => 'video/mp4',
                'path' => 'resep/ayam-kecap-video.mp4',
            ],

            // Resep ID 5 - Capcay Sayur
            [
                'resep_id' => 5,
                'mimetype' => 'image/jpeg',
                'path' => 'resep/capcay-1.jpg',
            ],
        ];

        foreach ($attachments as $attachment) {
            ResepAttachment::create($attachment);
        }
    }
}
