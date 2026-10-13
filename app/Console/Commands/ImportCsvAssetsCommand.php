<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\Category;
use App\Models\Location;
use App\Models\Brand;
use App\Models\Employee;

class ImportCsvAssetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'itam:import-csv-assets {--dry-run : Run simulation without committing changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Safely import and map the 83 custom assets dataset into ITAM with assignments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        $this->info($isDryRun ? "Starting CSV Assets Import Simulation (DRY-RUN)..." : "Starting Safe CSV Assets Import & Assignment...");

        $csvData = <<<'CSV'
Asset Number,Asset Name,Purchase Date,PO Number,Asset Cost,Lokasi,Divisi,Kategori,NIK PIC,PIC
04.02.3791,MINI PC,2026-02-10,2600314,3300000,KANTOR PRODUKSI LT. 2,Herbicide Production,COMPUTER,20250601104,Rizki Ramadhan
04.02.3792,MINI PC,2026-02-10,2600314,3300000,KANTOR PRODUKSI LT. 2,PDCA Facilitator,COMPUTER,20250101003,I Gusti Ayu Diah Zarasvaty
04.02.3793,MINI PC,2026-02-10,2600314,3300000,KANTOR PRODUKSI LT. 2,Herbicide Production,COMPUTER,20220701123,Moh Azmi Aulia
04.02.3794,MINI PC,2026-02-10,2600314,3300000,KANTOR PRODUKSI LT. 2,Plastic Production,COMPUTER,20240201012,Riki Susanto
04.02.3795,MINI PC,2026-02-10,2600314,3300000,KANTOR PRODUKSI LT. 2,PPIC,COMPUTER,20210701140,Aji Malkan
04.02.3806,"LED MONITOR 19"" INCH FULL HD HDMI VGA INPUT SPC GARANSI RESMI",2026-03-02,2600473,600000,GEDUNG I3 - OFFICE GDG RMT,Raw Material Warehouse,MONITOR,20250501071,Ginta Febrianti
04.02.3807,"LED MONITOR 19"" INCH FULL HD HDMI VGA INPUT SPC GARANSI RESMI",2026-03-02,2600473,600000,KANTOR PRODUKSI LT. 1,HRIS,MONITOR,20111101003,Maesaroh
04.02.3808,"LED MONITOR 19"" INCH FULL HD HDMI VGA INPUT SPC GARANSI RESMI",2026-03-02,2600473,600000,LAB LT. 1,Agrochemical R&D,MONITOR,20250601098,Lina Widya Puspitaningrum
04.02.3809,"LED MONITOR 19"" INCH FULL HD HDMI VGA INPUT SPC GARANSI RESMI",2026-03-02,2600473,600000,KANTOR PRODUKSI LT. 2,Engineering Project,MONITOR,20240401030,Jeffrey Krismana Putra
04.02.3810,"LED MONITOR 19"" INCH FULL HD HDMI VGA INPUT SPC GARANSI RESMI",2026-03-02,2600473,600000,KANTOR PRODUKSI LT. 2,PPIC,MONITOR,20250301053,Listiyani Nurwindya Sari
04.02.3817,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,LAB LT. 1,Agrochemical R&D,COMPUTER,20240501034,Nursandy Yoga Fadjrianto
04.02.3818,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,LAB LT. 1,Plastic Quality Control,COMPUTER,20201001112,Ria Indriyani
04.02.3819,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,LAB LT. 1,Agrochemical R&D,COMPUTER,20250801154,Dini Annisa
04.02.3820,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,LAB LT. 1,Pesticide Quality Control,COMPUTER,20221201326,Kardilah Rostiana
04.02.3821,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,GEDUNG I3 - OFFICE GDG RMT,Raw Material Warehouse,COMPUTER,20120801002,Nedi
04.02.3822,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,LAB LT. 1,Pesticide Quality Control,COMPUTER,20211001219,Anis Awaliyah
04.02.3823,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,LAB LT. 1,Pesticide Quality Control,COMPUTER,20240201011,Moh Ghaffar Deprian
04.02.3824,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,LAB LT. 1,Agrochemical R&D,COMPUTER,20250601098,Lina Widya Puspitaningrum
04.02.3825,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,KANTOR PRODUKSI LT. 1,Finish Good Warehouse,COMPUTER,20240701095,Asep Herli Kurnia
04.02.3826,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,GEDUNG F4 - OFFICE PROD FL,Herbicide Production,COMPUTER,20070901001,Dedi Wahyudi/Foreman
04.02.3827,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,KANTOR PRODUKSI LT. 1,"HC, GA, HSE",COMPUTER,20240601075,Andi Zufar Ahyu Mangung
04.02.3828,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,KANTOR PRODUKSI LT. 1,"HC, GA, HSE",COMPUTER,20130701002,Ahmad Fuad
04.02.3829,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,KANTOR PRODUKSI LT. 2,QA & Product Development,COMPUTER,20251201175,Santun Janji Kabul Santoso
04.02.3830,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,GEDUNG B1 - OFFICE GUDANG RMT C12,Raw Material Warehouse,COMPUTER,20250701142,Asep Tsauriman Tazaka
04.02.3831,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,GEDUNG B3 - MTC LT. 2,Plastic Maintenance,COMPUTER,20250401060,Randa Afabek
04.02.3832,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,GEDUNG I3 - OFFICE GDG RMT,Raw Material Warehouse,COMPUTER,20250501071,Ginta Febrianti
04.02.3833,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,GEDUNG F5 - OFFICE,Finish Good Warehouse,COMPUTER,20221101287,Sahrudin
04.02.3834,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,GEDUNG D3 - OFFICE GUDANG RMT BTL,Raw Material Warehouse,COMPUTER,20240401031,Novi Dwi Lestari
04.02.3835,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,KANTOR PRODUKSI LT. 2,PPIC,COMPUTER,20250301053,Listiyani Nurwindya Sari
04.02.3836,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,KANTOR PRODUKSI LT. 1,"HC, GA, HSE",COMPUTER,20260201051,Syaiful Anam
04.02.3837,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,GEDUNG H2 - OFFICE,Plastic Production,COMPUTER,20220601112,Indra Sasmita/Foreman
04.02.3838,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,KANTOR PRODUKSI LT. 1,HRIS,COMPUTER,20111101003,Maesaroh
04.02.3839,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,GEDUNG C2 - OFFICE MLS,Plastic Production,COMPUTER,20111001006,Nurhali
04.02.3840,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,KANTOR PRODUKSI LT. 2,Plastic Production,COMPUTER,20241201172,Dzarien Alparindri
04.02.3841,MINI PC MAX N B1 N95,2026-03-05,2600535,3525000,GEDUNG B1 - OFFICE GUDANG RMT C12,Raw Material Warehouse,COMPUTER,20100301007,Soleh
04.02.3842,PRINTER EPSON DOMATRIX LX310,2026-03-10,2600566,3000000,GEDUNG C2 - OFFICE MLS,Plastic Production,PRINTER,20111001006,Nurhali
04.02.3843,PRINTER EPSON DOMATRIX LX310,2026-03-10,2600566,3000000,GEDUNG F5 - OFFICE,Finish Good Warehouse,PRINTER,20220201046,Aris Mangsur
04.02.3844,PRINTER EPSON DOMATRIX LX310,2026-03-10,2600566,3000000,GEDUNG I3 - OFFICE GDG RMT,Raw Material Warehouse,PRINTER,20250501071,Ginta Febrianti
04.02.3845,PRINTER EPSON DOMATRIX LX310,2026-03-10,2600566,3000000,GEDUNG B1 - PROD CF,Herbicide Production,PRINTER,20050201001,Atmajaya
04.02.3846,PRINTER EPSON DOMATRIX LX310,2026-03-10,2600566,3000000,GEDUNG D3 - OFFICE GUDANG RMT BTL,Raw Material Warehouse,PRINTER,20240401031,Novi Dwi Lestari
04.02.3847,WIRELESS HDMI TRANSMITTER RECEIVER 2 IN 1 HDMI USB TYPE C LAPTOP SMARTPHONE TABLET 100 METER PX WMS-7000PRO,2026-03-02,2600472,1850000,KANTOR PRODUKSI LT. 2,IT,PERIPHERALS & ACCESSORIES,00000000000,IT
04.02.3865,PEMBELIAN SMART TV 32 INCH COOCAA 32S3U,2026-04-23,2600904,1700000,POS SECURITY,IT,TV,00000000000,IT
04.02.3866,EZVIZ C6N 2MP + Braket + Memori / Camera CCTV Indoor Smart Wifi IP 1080P - C6N+BRAKET+128GB,2026-04-23,2600907,780000,GUDANG RMT CEMPLANG,"HC, GA, HSE",CCTV,20190401058,Erik Sutrisno
04.02.3867,EZVIZ C6N 2MP + Braket + Memori / Camera CCTV Indoor Smart Wifi IP 1080P - C6N+BRAKET+128GB,2026-04-23,2600907,780000,GUDANG RMT CEMPLANG,"HC, GA, HSE",CCTV,20190401058,Erik Sutrisno
04.02.3868,EZVIZ C6N 2MP + Braket + Memori / Camera CCTV Indoor Smart Wifi IP 1080P - C6N+BRAKET+128GB,2026-04-23,2600907,780000,GUDANG RMT CEMPLANG,"HC, GA, HSE",CCTV,20190401058,Erik Sutrisno
04.02.3886,MIXER YAMAHA MG10XUF,2026-05-05,2600948,4500000,KANTOR PRODUKSI LT. 2,IT,AUDIO DEVICE,00000000000,IT
04.02.3890,EZVIZ C6N 2MP + Braket + Memori / Camera CCTV Indoor Smart Wifi IP 1080P - C6N+BRAKET+128GB,2026-05-26,2601114,785000,Gudang RMT Kawasan,"HC, GA, HSE",CCTV,20190401058,Erik Sutrisno
04.02.3891,EZVIZ C6N 2MP + Braket + Memori / Camera CCTV Indoor Smart Wifi IP 1080P - C6N+BRAKET+128GB,2026-05-26,2601114,785000,Gudang RMT Kawasan,"HC, GA, HSE",CCTV,20190401058,Erik Sutrisno
04.02.3892,EZVIZ C6N 2MP + Braket + Memori / Camera CCTV Indoor Smart Wifi IP 1080P - C6N+BRAKET+128GB,2026-05-26,2601114,785000,Gudang RMT Kawasan,"HC, GA, HSE",CCTV,20190401058,Erik Sutrisno
04.02.3893,TP-Link Router WiFi TL-MR100 4G LTE,2026-05-26,2601114,570000,Gudang RMT Kawasan,"HC, GA, HSE",ROUTER,20190401058,Erik Sutrisno
04.02.3919,KAMERA CANON EOS R50 KIT 18-45MM,2026-06-04,2601150,10650000,KANTOR PRODUKSI LT. 2,"HC, GA, HSE",CAMERA,20190401058,Erik Sutrisno
04.02.3920,LENOVO THINKSYSTEM ST45 V3 SERVER,2026-06-04,2601150,15600000,KANTOR PRODUKSI LT. 2,IT,SERVER,00000000000,IT
04.02.3921,SCANNER EPSON DS 410,2026-06-04,2601150,5100000,KANTOR PRODUKSI LT. 1,Finish Good Warehouse,PRINTER,20150101009,Sagari Nilam Sar
04.02.3922,FINGERPRINT SOLUTION X100-C,2026-06-04,2601150,1750000,GEDUNG D1,IT,FINGERPRINT,00000000000,IT
04.02.3923,FINGERPRINT SOLUTION X100-C,2026-06-04,2601150,1750000,KANTOR MINI LAB,IT,FINGERPRINT,00000000000,IT
04.02.3946,"WD MY PASSPORT 4TB - HD HDD HARDISK EKSTERNAL EXTERNAL 2.5"" USB 3.0 - HITAM",2026-07-10,2601404,2468469,KANTOR PRODUKSI LT. 2,IT,EXTERNAL DRIVE,00000000000,IT
04.02.3947,"SAMSUNG 65"" CRYSTAL UHD 4K UE100F 4K SMART TV | UA65UE100FKXXD",2026-07-09,2601388,8200000,KANTOR PRODUKSI LT. 2,IT,TV,00000000000,IT
04.02.3948,"NORTH BAYOU 55""-80"" IN NB AVA1800-70-1P STANDING TV NB AVA 1800 LCD",2026-07-09,2601388,1800000,KANTOR PRODUKSI LT. 2,IT,PERIPHERALS & ACCESSORIES,00000000000,IT
04.02.3949,PX WTR-5500 Wireless HDMI Audio Video Transmitter Receiver Display TV,2026-07-09,2601388,1800000,KANTOR PRODUKSI LT. 2,IT,PERIPHERALS & ACCESSORIES,00000000000,IT
04.02.3994,PRINTER EPSON L3251 HITAM ALL IN ONE PRINTER WIRELESS,2026-07-20,2601493,2700000,KANTOR PRODUKSI LT. 2,PDCA Facilitator,PRINTER,20250101003,I Gusti Ayu Diah Zarasvaty
04.02.3995,PRINTER CANON PIXMA G5070 WIFI LAN,2026-07-20,2601493,2100000,KANTOR LAB LT 2,Agrochemical R&D,PRINTER,20260101006,MEISSA LIFANGRHA RONALD
04.02.3996,PRINTER CANON PIXMA G5070 WIFI LAN,2026-07-20,2601493,2100000,KANTOR PRODUKSI LT. 1,Finish Good Warehouse,PRINTER,20160401002,Siska Dian Ratnasari
04.02.3983,FINGERPRINT SOLUTION X100C,2026-07-27,2601525,3500000,PBK9 - GEDUNG F5,Herbicide Production,FINGERPRINT,00000000000,IT
04.02.3984,Windows GGWA - Windows 11 Pro - Legalization get Genuine,2026-07-27,2601525,3500000,KANTOR PRODUKSI LT. 2,IT,OS LICENSE,20250101003,I Gusti Ayu Diah Zarasvaty
04.02.3985,Windows GGWA - Windows 11 Pro - Legalization get Genuine,2026-07-27,2601525,3500000,KANTOR PRODUKSI LT. 2,IT,OS LICENSE,20080401002,Siti Latifah
04.02.3986,Windows GGWA - Windows 11 Pro - Legalization get Genuine,2026-07-27,2601525,3500000,KANTOR PRODUKSI LT. 2,IT,OS LICENSE,20150901007,Devanda Herawan Wibisono
04.02.3987,Windows GGWA - Windows 11 Pro - Legalization get Genuine,2026-07-27,2601525,3500000,KANTOR PRODUKSI LT. 1,IT,OS LICENSE,20130101011,Anis Novitasari
04.02.3988,Windows GGWA - Windows 11 Pro - Legalization get Genuine,2026-07-27,2601525,3500000,KANTOR PRODUKSI LT. 1,IT,OS LICENSE,20150101009,Sagari Nilam Sar
04.02.3989,Windows GGWA - Windows 11 Pro - Legalization get Genuine,2026-07-27,2601525,3500000,KANTOR PRODUKSI LT. 2,IT,OS LICENSE,20170301171,Lukmanul Hakim
04.02.3990,Windows GGWA - Windows 11 Pro - Legalization get Genuine,2026-07-27,2601525,3500000,KANTOR PRODUKSI LT. 1,IT,OS LICENSE,20161001014,Abdul Cholik
04.02.3991,Windows GGWA - Windows 11 Pro - Legalization get Genuine,2026-07-27,2601525,3500000,KANTOR PRODUKSI LT. 2,IT,OS LICENSE,20111001003,Anis Siswanto
04.02.3992,Windows GGWA - Windows 11 Pro - Legalization get Genuine,2026-07-27,2601525,3500000,LAB LT. 2,IT,OS LICENSE,20201201183,Novia Suryani
04.02.3993,Windows GGWA - Windows 11 Pro - Legalization get Genuine,2026-07-27,2601525,3500000,LAB LT. 2,IT,OS LICENSE,20241101166,Rifqi Aulia Hardiansyah
04.02.3998,FINGERPRINT SOLUTION X100C,2026-08-13,2601664,1900000,PBK6 - GEDUNG H2,Plastic Production,FINGERPRINT,00000000000,IT
04.02.3999,EZVIZ C6N 2MP + Braket + Memori / Camera CCTV Indoor Smart Wifi IP 1080P - C6N+BRAKET+128GB,2026-08-13,2601666,800000,GUDANG RMT KAWASAN,Raw Material Warehouse,CCTV,20190401058,Erik Sutrisno
04.02.4000,EZVIZ C6N 2MP + Braket + Memori / Camera CCTV Indoor Smart Wifi IP 1080P - C6N+BRAKET+128GB,2026-08-13,2601666,800000,GUDANG RMT KAWASAN,Raw Material Warehouse,CCTV,20190401058,Erik Sutrisno
04.02.4001,EZVIZ C6N 2MP + Braket + Memori / Camera CCTV Indoor Smart Wifi IP 1080P - C6N+BRAKET+128GB,2026-08-13,2601666,800000,GUDANG RMT KAWASAN,Raw Material Warehouse,CCTV,20190401058,Erik Sutrisno
04.02.4002,TP-Link Router WiFi TL-MR100 4G LTE,2026-08-13,2601666,640000,GUDANG RMT KAWASAN,Raw Material Warehouse,ROUTER,20190401058,Erik Sutrisno
04.02.4020,"WD 4TB PURPLE 3.5"" WD40PURZ HDD",2026-08-26,2601762,3824324.33,KANTOR PRODUKSI LT. 2,IT,STORAGE,00000000000,IT
04.02.4021,"WD 4TB PURPLE 3.5"" WD40PURZ HDD",2026-08-26,2601762,3824324.33,KANTOR PRODUKSI LT. 2,IT,STORAGE,00000000000,IT
04.02.4022,"WD 4TB PURPLE 3.5"" WD40PURZ HDD",2026-08-26,2601762,3824324.33,KANTOR PRODUKSI LT. 2,IT,STORAGE,00000000000,IT
04.02.4023,HARDDISK SEAGATE IRONWOLF NAS INTERNAL 2TB,2026-09-22,2601967,3127460,KANTOR PRODUKSI LT. 2,IT,STORAGE,00000000000,IT
04.02.4024,HARDDISK SEAGATE IRONWOLF NAS INTERNAL 2TB,2026-09-22,2601967,3127460,KANTOR PRODUKSI LT. 2,IT,STORAGE,00000000000,IT
CSV;

        $lines = explode("\n", trim($csvData));
        $header = str_getcsv(array_shift($lines));
        $rows = [];
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            $rows[] = array_combine($header, str_getcsv($line));
        }

        // Brands
        $microsoftBrand = Brand::firstOrCreate(['name' => 'Microsoft']);
        $northBayouBrand = Brand::firstOrCreate(['name' => 'North Bayou']);

        $dbBrands = Brand::all()->keyBy(function($b) { return strtolower($b->name); });
        $dbLocations = Location::all()->keyBy(function($l) { return strtoupper(trim($l->name)); });
        $dbCategories = Category::all()->keyBy(function($c) { return strtolower(trim($c->name)); });
        $dbEmployees = Employee::all()->keyBy(function($e) { return trim($e->employee_id); });

        $locationMap = [
            'GEDUNG D1' => 'GEDUNG D1 - PROD BOTOL',
            'KANTOR MINI LAB' => 'GEDUNG D5 - MINI LAB',
            'KANTOR LAB LT 2' => 'LAB LT. 2',
            'PBK9 - GEDUNG F5' => 'GEDUNG F5 - OFFICE',
            'PBK6 - GEDUNG H2' => 'GEDUNG H2 - OFFICE',
            'GUDANG RMT KAWASAN' => 'GUDANG RMT KAWASAN',
        ];

        $categoryMap = [
            'COMPUTER' => 'Computer',
            'MONITOR' => 'Monitor',
            'PRINTER' => 'Printer',
            'PERIPHERALS & ACCESSORIES' => 'Accessories',
            'TV' => 'Other IT Asset',
            'CCTV' => 'CCTV',
            'AUDIO DEVICE' => 'Other IT Asset',
            'ROUTER' => 'Network',
            'CAMERA' => 'Other IT Asset',
            'SERVER' => 'Computer',
            'FINGERPRINT' => 'Office Equipment',
            'EXTERNAL DRIVE' => 'Storage',
            'OS LICENSE' => 'Software',
            'STORAGE' => 'Storage',
        ];

        $detectBrandId = function($name) use ($dbBrands) {
            $u = strtoupper($name);
            if (str_contains($u, 'EPSON')) return $dbBrands->get('epson')->id ?? null;
            if (str_contains($u, 'CANON')) return $dbBrands->get('canon')->id ?? null;
            if (str_contains($u, 'EZVIZ')) return $dbBrands->get('ezviz')->id ?? null;
            if (str_contains($u, 'COOCA')) return $dbBrands->get('cooca')->id ?? null;
            if (str_contains($u, 'SPC')) return $dbBrands->get('spc')->id ?? null;
            if (str_contains($u, 'YAMAHA')) return $dbBrands->get('yamaha')->id ?? null;
            if (str_contains($u, 'TP-LINK')) return $dbBrands->get('tp-link')->id ?? null;
            if (str_contains($u, 'LENOVO')) return $dbBrands->get('lenovo')->id ?? null;
            if (str_contains($u, 'SOLUTION')) return $dbBrands->get('solution')->id ?? null;
            if (str_contains($u, 'WD ') || str_contains($u, 'WD40PURZ')) return $dbBrands->get('western digital')->id ?? null;
            if (str_contains($u, 'SEAGATE')) return $dbBrands->get('seagate')->id ?? null;
            if (str_contains($u, 'SAMSUNG')) return $dbBrands->get('samsung')->id ?? null;
            if (str_contains($u, 'NORTH BAYOU')) return $dbBrands->get('north bayou')->id ?? null;
            if (str_contains($u, 'PX WMS') || str_contains($u, 'PX WTR')) return $dbBrands->get('px')->id ?? null;
            if (str_contains($u, 'WINDOWS')) return $dbBrands->get('microsoft')->id ?? null;
            return null;
        };

        $updatedCount = 0;
        $insertedCount = 0;
        $assignedCount = 0;
        // Temporarily mute event listeners to prevent external Telegram HTTP delays during bulk import
        Asset::flushEventListeners();
        AssetAssignment::flushEventListeners();

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $r) {
                $tag = trim($r['Asset Number']);
                $name = trim($r['Asset Name']);
                $rawDate = trim($r['Purchase Date']);
                $poNumber = trim($r['PO Number']);
                $cost = (float)str_replace(',', '.', trim($r['Asset Cost']));
                $rawLoc = trim($r['Lokasi']);
                $rawCat = trim($r['Kategori']);
                $rawNik = trim($r['NIK PIC']);
                $rawPic = trim($r['PIC']);

                $targetLocName = $locationMap[$rawLoc] ?? $rawLoc;
                $locObj = $dbLocations->get(strtoupper($targetLocName));
                if (!$locObj) {
                    throw new \Exception("Row {$index} ({$tag}): Location not found for '{$rawLoc}' (target: '{$targetLocName}')");
                }

                $targetCatName = $categoryMap[$rawCat] ?? $rawCat;
                $catObj = $dbCategories->get(strtolower($targetCatName));
                if (!$catObj) {
                    throw new \Exception("Row {$index} ({$tag}): Category not found for '{$rawCat}' (target: '{$targetCatName}')");
                }

                $brandId = $detectBrandId($name);

                $isItPool = ($rawNik === '00000000000' || strtoupper($rawPic) === 'IT');
                $employee = null;
                if (!$isItPool) {
                    $employee = $dbEmployees->get($rawNik);
                    if (!$employee) {
                        throw new \Exception("Row {$index} ({$tag}): Employee NIK '{$rawNik}' ({$rawPic}) not found!");
                    }
                }

                $formattedCost = "Rp " . number_format($cost, 0, ',', '.');
                $poNote = "PO: {$poNumber} | Biaya: {$formattedCost}";

                $asset = Asset::where('asset_tag', $tag)->first();

                if ($asset) {
                    $existingSpec = is_array($asset->spec_data) ? $asset->spec_data : [];
                    $existingSpec['cost'] = $cost;
                    $existingSpec['po_number'] = $poNumber;

                    $notes = $asset->notes;
                    if (empty($notes)) {
                        $notes = $poNote;
                    } elseif (!str_contains($notes, "PO: {$poNumber}")) {
                        $notes = $notes . " | " . $poNote;
                    }

                    $status = $isItPool ? 'Available' : 'Assigned';

                    $updateData = [
                        'name' => $name,
                        'category_id' => $catObj->id,
                        'location_id' => $locObj->id,
                        'delivery_order_number' => $poNumber,
                        'date_received' => $rawDate,
                        'status' => $status,
                        'spec_data' => $existingSpec,
                        'notes' => $notes,
                    ];
                    if ($brandId) {
                        $updateData['brand_id'] = $brandId;
                    }

                    if (!$isDryRun) {
                        $asset->update($updateData);
                    }
                    $updatedCount++;
                } else {
                    $status = $isItPool ? 'Available' : 'Assigned';
                    $insertData = [
                        'asset_tag' => $tag,
                        'name' => $name,
                        'uom' => 'UNIT',
                        'category_id' => $catObj->id,
                        'brand_id' => $brandId,
                        'location_id' => $locObj->id,
                        'delivery_order_number' => $poNumber,
                        'date_received' => $rawDate,
                        'status' => $status,
                        'spec_data' => [
                            'cost' => $cost,
                            'po_number' => $poNumber,
                        ],
                        'notes' => $poNote,
                    ];

                    if (!$isDryRun) {
                        $asset = Asset::create($insertData);
                    } else {
                        $asset = new Asset($insertData);
                        $asset->id = 999900 + $index;
                    }
                    $insertedCount++;
                }

                if ($isItPool) {
                    $itPoolCount++;
                } else {
                    $existingAssign = AssetAssignment::where('asset_id', $asset->id)
                        ->where('status', 'Assigned')
                        ->first();

                    if ($existingAssign) {
                        if ($existingAssign->employee_id != $employee->id && !$isDryRun) {
                            $existingAssign->update([
                                'employee_id' => $employee->id,
                                'assigned_date' => $rawDate,
                                'notes' => 'Diperbarui via migrasi/import aset 2026'
                            ]);
                        }
                    } else {
                        if (!$isDryRun) {
                            AssetAssignment::create([
                                'asset_id' => $asset->id,
                                'employee_id' => $employee->id,
                                'assigned_date' => $rawDate,
                                'status' => 'Assigned',
                                'notes' => 'Penyerahan aset tercatat dari migrasi/import data 2026',
                            ]);
                        }
                        $assignedCount++;
                    }
                }
            }

            if ($isDryRun) {
                DB::rollBack();
                $this->warn("Dry-run simulation complete! No changes committed.");
            } else {
                DB::commit();
                Cache::forget('dashboard_metrics_cache');
                Cache::forget('available_assets_select');
                Cache::forget('active_employees_select');
                Cache::forget('asset_status_counts');
                Cache::forget('assignment_status_counts');
                $this->info("Successfully imported and committed to database!");
            }

            $this->table(
                ['Metric', 'Count'],
                [
                    ['Total Rows Processed', count($rows)],
                    ['Updated Existing Assets', $updatedCount],
                    ['Inserted New Assets', $insertedCount],
                    ['Created/Updated Assignments', $assignedCount],
                    ['IT Pool (Available) Assets', $itPoolCount],
                ]
            );

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Import Failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
