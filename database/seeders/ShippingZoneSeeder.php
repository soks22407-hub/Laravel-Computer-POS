<?php
namespace Database\Seeders;

use App\Models\ShippingZone;
use Illuminate\Database\Seeder;

class ShippingZoneSeeder extends Seeder
{
    public function run()
    {
        \App\Models\ShippingZone::truncate();
        $provinces = [
            'Phnom Penh', 'Siem Reap', 'Battambang', 'Sihanoukville',
            'Kampot', 'Kep', 'Kandal', 'Kampong Cham', 'Kampong Chhnang',
            'Kampong Speu', 'Kampong Thom', 'Kracheh', 'Mondulkiri',
            'Oddar Meanchey', 'Pailin', 'Preah Vihear', 'Prey Veng',
            'Pursat', 'Ratanakiri', 'Stung Treng', 'Svay Rieng',
            'Takeo', 'Tboung Khmum', 'Koh Kong','Banteay Meanchey'
        ];

        foreach ($provinces as $province) {
            ShippingZone::create([
                'province'            => $province,
                'fee'                 => $province == 'Phnom Penh' ? 2.00 : 3.00,
                'free_shipping_above' => 100.00, // free shipping above $100
                'is_active'           => true,
            ]);
        }
    }
}