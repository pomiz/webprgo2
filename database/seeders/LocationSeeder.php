<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['province' => 'DKI Jakarta', 'city' => 'Jakarta Pusat', 'latitude' => -6.1862, 'longitude' => 106.8340],
            ['province' => 'DKI Jakarta', 'city' => 'Jakarta Selatan', 'latitude' => -6.2615, 'longitude' => 106.8106],
            ['province' => 'DKI Jakarta', 'city' => 'Jakarta Barat', 'latitude' => -6.1684, 'longitude' => 106.7588],
            ['province' => 'DKI Jakarta', 'city' => 'Jakarta Timur', 'latitude' => -6.2250, 'longitude' => 106.9004],
            ['province' => 'DKI Jakarta', 'city' => 'Jakarta Utara', 'latitude' => -6.1219, 'longitude' => 106.7741],
            ['province' => 'Jawa Barat', 'city' => 'Bandung', 'latitude' => -6.9175, 'longitude' => 107.6191],
            ['province' => 'Jawa Barat', 'city' => 'Bekasi', 'latitude' => -6.2383, 'longitude' => 106.9756],
            ['province' => 'Jawa Barat', 'city' => 'Bogor', 'latitude' => -6.5971, 'longitude' => 106.8060],
            ['province' => 'Jawa Barat', 'city' => 'Depok', 'latitude' => -6.4025, 'longitude' => 106.7942],
            ['province' => 'Jawa Barat', 'city' => 'Cirebon', 'latitude' => -6.7320, 'longitude' => 108.5523],
            ['province' => 'Jawa Barat', 'city' => 'Tasikmalaya', 'latitude' => -7.3274, 'longitude' => 108.2207],
            ['province' => 'Jawa Barat', 'city' => 'Sukabumi', 'latitude' => -6.9277, 'longitude' => 106.9300],
            ['province' => 'Jawa Tengah', 'city' => 'Semarang', 'latitude' => -6.9666, 'longitude' => 110.4196],
            ['province' => 'Jawa Tengah', 'city' => 'Solo', 'latitude' => -7.5755, 'longitude' => 110.8243],
            ['province' => 'Jawa Tengah', 'city' => 'Pekalongan', 'latitude' => -6.8886, 'longitude' => 109.6753],
            ['province' => 'Jawa Tengah', 'city' => 'Tegal', 'latitude' => -6.8797, 'longitude' => 109.1426],
            ['province' => 'Jawa Tengah', 'city' => 'Magelang', 'latitude' => -7.4797, 'longitude' => 110.2177],
            ['province' => 'DI Yogyakarta', 'city' => 'Yogyakarta', 'latitude' => -7.7956, 'longitude' => 110.3695],
            ['province' => 'DI Yogyakarta', 'city' => 'Sleman', 'latitude' => -7.7166, 'longitude' => 110.3558],
            ['province' => 'Jawa Timur', 'city' => 'Surabaya', 'latitude' => -7.2575, 'longitude' => 112.7521],
            ['province' => 'Jawa Timur', 'city' => 'Malang', 'latitude' => -7.9666, 'longitude' => 112.6326],
            ['province' => 'Jawa Timur', 'city' => 'Sidoarjo', 'latitude' => -7.4478, 'longitude' => 112.7183],
            ['province' => 'Jawa Timur', 'city' => 'Kediri', 'latitude' => -7.8480, 'longitude' => 112.0178],
            ['province' => 'Jawa Timur', 'city' => 'Jember', 'latitude' => -8.1845, 'longitude' => 113.6681],
            ['province' => 'Banten', 'city' => 'Tangerang', 'latitude' => -6.1783, 'longitude' => 106.6319],
            ['province' => 'Banten', 'city' => 'Tangerang Selatan', 'latitude' => -6.2894, 'longitude' => 106.7108],
            ['province' => 'Banten', 'city' => 'Serang', 'latitude' => -6.1103, 'longitude' => 106.1640],
            ['province' => 'Banten', 'city' => 'Cilegon', 'latitude' => -6.0023, 'longitude' => 106.0507],
            ['province' => 'Sumatera Utara', 'city' => 'Medan', 'latitude' => 3.5952, 'longitude' => 98.6722],
            ['province' => 'Sumatera Utara', 'city' => 'Binjai', 'latitude' => 3.6001, 'longitude' => 98.4854],
            ['province' => 'Sumatera Barat', 'city' => 'Padang', 'latitude' => -0.9471, 'longitude' => 100.4172],
            ['province' => 'Sumatera Selatan', 'city' => 'Palembang', 'latitude' => -2.9761, 'longitude' => 104.7754],
            ['province' => 'Riau', 'city' => 'Pekanbaru', 'latitude' => 0.5071, 'longitude' => 101.4478],
            ['province' => 'Lampung', 'city' => 'Bandar Lampung', 'latitude' => -5.3971, 'longitude' => 105.2668],
            ['province' => 'Bali', 'city' => 'Denpasar', 'latitude' => -8.6500, 'longitude' => 115.2167],
            ['province' => 'Bali', 'city' => 'Badung', 'latitude' => -8.5819, 'longitude' => 115.1771],
            ['province' => 'Sulawesi Selatan', 'city' => 'Makassar', 'latitude' => -5.1477, 'longitude' => 119.4327],
            ['province' => 'Kalimantan Timur', 'city' => 'Balikpapan', 'latitude' => -1.2379, 'longitude' => 116.8529],
            ['province' => 'Kalimantan Timur', 'city' => 'Samarinda', 'latitude' => -0.4948, 'longitude' => 117.1436],
            ['province' => 'Kalimantan Selatan', 'city' => 'Banjarmasin', 'latitude' => -3.3194, 'longitude' => 114.5900],
            ['province' => 'NTB', 'city' => 'Mataram', 'latitude' => -8.5833, 'longitude' => 116.1167],
            ['province' => 'Sulawesi Utara', 'city' => 'Manado', 'latitude' => 1.4748, 'longitude' => 124.8421],
            ['province' => 'Papua', 'city' => 'Jayapura', 'latitude' => -2.5337, 'longitude' => 140.7181],
            ['province' => 'Maluku', 'city' => 'Ambon', 'latitude' => -3.6954, 'longitude' => 128.1814],
            ['province' => 'Aceh', 'city' => 'Banda Aceh', 'latitude' => 5.5483, 'longitude' => 95.3238],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
