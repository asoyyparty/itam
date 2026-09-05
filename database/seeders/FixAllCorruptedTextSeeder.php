<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixAllCorruptedTextSeeder extends Seeder
{
    public function run()
    {
        $brands = DB::table('brands')->get();
        foreach ($brands as $brand) {
            if ($brand->description) {
                $desc = $brand->description;
                $desc = str_replace(['ҐÇô', 'â€“', 'â€”', '–', '—'], ' - ', $desc);
                $desc = preg_replace('/[^\x20-\x7E]+/u', ' - ', $desc);
                $desc = preg_replace('/\s*-\s*/', ' - ', $desc);
                $desc = preg_replace('/\s+/', ' ', $desc);
                
                // Keep specific proper names clean
                $desc = str_replace('Hewlett - Packard', 'Hewlett-Packard', $desc);
                $desc = str_replace('TP - Link', 'TP-Link', $desc);
                $desc = str_replace('NVR / DVR', 'NVR/DVR', $desc);
                $desc = str_replace('audio / visual', 'audio/visual', $desc);
                
                $desc = trim($desc);

                DB::table('brands')->where('id', $brand->id)->update(['description' => $desc]);
            }
        }

        $categories = DB::table('categories')->get();
        foreach ($categories as $cat) {
            if ($cat->description) {
                $desc = $cat->description;
                $desc = str_replace(['ҐÇô', 'â€“', 'â€”', '–', '—'], ' - ', $desc);
                $desc = preg_replace('/[^\x20-\x7E]+/u', ' - ', $desc);
                $desc = preg_replace('/\s*-\s*/', ' - ', $desc);
                $desc = preg_replace('/\s+/', ' ', $desc);
                $desc = trim($desc);

                DB::table('categories')->where('id', $cat->id)->update(['description' => $desc]);
            }
        }
    }
}
