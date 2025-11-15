<?php

namespace Modules\Common\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Common\App\Models\MeasurementUnit;

class MeasurementUnitsSeeder extends Seeder{
    public function run(): void{
        $units = [
            [
                'type' => 'length',
                'base_unit' => null,
                'factor' => 1,
                'en' => ['name' => 'Meter', 'symbol' => 'm'],
                'ar' => ['name' => 'متر', 'symbol' => 'م'],
            ],
            [
                'type' => 'length',
                'base_unit' => 'Meter',
                'factor' => 0.01,
                'en' => ['name' => 'Centimeter', 'symbol' => 'cm'],
                'ar' => ['name' => 'سنتيمتر', 'symbol' => 'سم'],
            ],
            [
                'type' => 'length',
                'base_unit' => 'Meter',
                'factor' => 0.001,
                'en' => ['name' => 'Millimeter', 'symbol' => 'mm'],
                'ar' => ['name' => 'ملليمتر', 'symbol' => 'ملم'],
            ],
            [
                'type' => 'length',
                'base_unit' => 'Meter',
                'factor' => 1000,
                'en' => ['name' => 'Kilometer', 'symbol' => 'km'],
                'ar' => ['name' => 'كيلومتر', 'symbol' => 'كم'],
            ], [
                'type' => 'length',
                'base_unit' => null,
                'factor' => 1,
                'en' => ['name' => 'Foot', 'symbol' => 'ft'],
                'ar' => ['name' => 'قدم', 'symbol' => 'قدم'],
            ],
            [
                'type' => 'length',
                'base_unit' => 'Foot',
                'factor' => 0.0833333,
                'en' => ['name' => 'Inch', 'symbol' => 'in'],
                'ar' => ['name' => 'بوصة', 'symbol' => 'بوصة'],
            ],
            [
                'type' => 'length',
                'base_unit' => 'Foot',
                'factor' => 5280,
                'en' => ['name' => 'Mile', 'symbol' => 'mi'],
                'ar' => ['name' => 'ميل', 'symbol' => 'ميل'],
            ], [
                'type' => 'weight',
                'base_unit' => null,
                'factor' => 1,
                'en' => ['name' => 'Kilogram', 'symbol' => 'kg'],
                'ar' => ['name' => 'كيلوغرام', 'symbol' => 'كج'],
            ],
            [
                'type' => 'weight',
                'base_unit' => 'Kilogram',
                'factor' => 0.001,
                'en' => ['name' => 'Gram', 'symbol' => 'g'],
                'ar' => ['name' => 'غرام', 'symbol' => 'غ'],
            ],
            [
                'type' => 'weight',
                'base_unit' => 'Kilogram',
                'factor' => 0.0000001,
                'en' => ['name' => 'Milligram', 'symbol' => 'mg'],
                'ar' => ['name' => 'ملليغرام', 'symbol' => 'ملغ'],
            ], [
                'type' => 'weight',
                'base_unit' => null,
                'factor' => 1,
                'en' => ['name' => 'Pound', 'symbol' => 'lb'],
                'ar' => ['name' => 'باوند', 'symbol' => 'باوند'],
            ],
            [
                'type' => 'weight',
                'base_unit' => 'Pound',
                'factor' => 0.0625,
                'en' => ['name' => 'Ounce', 'symbol' => 'oz'],
                'ar' => ['name' => 'أونصة', 'symbol' => 'أونصة'],
            ], [
                'type' => 'volume',
                'base_unit' => null,
                'factor' => 1,
                'en' => ['name' => 'Liter', 'symbol' => 'L'],
                'ar' => ['name' => 'لتر', 'symbol' => 'ل'],
            ],
            [
                'type' => 'volume',
                'base_unit' => 'Liter',
                'factor' => 0.001,
                'en' => ['name' => 'Milliliter', 'symbol' => 'mL'],
                'ar' => ['name' => 'ملليلتر', 'symbol' => 'مل'],
            ], [
                'type' => 'volume',
                'base_unit' => null,
                'factor' => 1,
                'en' => ['name' => 'US Gallon', 'symbol' => 'gal'],
                'ar' => ['name' => 'جالون أمريكي', 'symbol' => 'جالون'],
            ],
            [
                'type' => 'volume',
                'base_unit' => 'US Gallon',
                'factor' => 0.0078125,
                'en' => ['name' => 'US Fluid Ounce', 'symbol' => 'fl oz'],
                'ar' => ['name' => 'أونصة سائلة أمريكية', 'symbol' => 'أ ع'],
            ],
            [
                'type' => 'data',
                'base_unit' => null,
                'factor' => 1,
                'en' => ['name' => 'Gigabyte', 'symbol' => 'GB'],
                'ar' => ['name' => 'جيجابايت', 'symbol' => 'جيجا بايت'],
            ],
            [
                'type' => 'data',
                'base_unit' => 'Gigabyte',
                'factor' => 0.001,
                'en' => ['name' => 'Megabyte', 'symbol' => 'MB'],
                'ar' => ['name' => 'ميجابايت', 'symbol' => 'ميجا بايت'],
            ],
            [
                'type' => 'data',
                'base_unit' => 'Gigabyte',
                'factor' => 0.000001,
                'en' => ['name' => 'Kilobyte', 'symbol' => 'KB'],
                'ar' => ['name' => 'كيلوبايت', 'symbol' => 'كيلو بايت'],
            ],
            [
                'type' => 'data',
                'base_unit' => 'Gigabyte',
                'factor' => 1000,
                'en' => ['name' => 'Terabyte', 'symbol' => 'TB'],
                'ar' => ['name' => 'تيرابايت', 'symbol' => 'تيرا بايت'],
            ],
            [
                'type' => 'data',
                'base_unit' => 'Gigabyte',
                'factor' => 1000000,
                'en' => ['name' => 'Petabyte', 'symbol' => 'PB'],
                'ar' => ['name' => 'بيتابايت', 'symbol' => 'بيتا بايت'],
            ],
            [
                'type' => 'area',
                'base_unit' => null,
                'factor' => 1,
                'en' => ['name' => 'Square Meter', 'symbol' => 'm²'],
                'ar' => ['name' => 'متر مربع', 'symbol' => 'م²'],
            ],
            [
                'type' => 'area',
                'base_unit' => 'Square Meter',
                'factor' => 10000,
                'en' => ['name' => 'Hectare', 'symbol' => 'ha'],
                'ar' => ['name' => 'هكتار', 'symbol' => 'هكتار'],
            ],
            [
                'type' => 'area',
                'base_unit' => null,
                'factor' => 1,
                'en' => ['name' => 'Square Foot', 'symbol' => 'ft²'],
                'ar' => ['name' => 'قدم مربع', 'symbol' => 'قدم²'],
            ],
            [
                'type' => 'piece',
                'base_unit' => null,
                'factor' => 1,
                'en' => ['name' => 'Piece', 'symbol' => 'pc'],
                'ar' => ['name' => 'قطعة', 'symbol' => 'قطعة'],
            ]
        ];

        $baseUnits = [];
        foreach($units as $unit){
            if($unit['base_unit'] === null){
                $created = MeasurementUnit::factory()->asBaseUnit(
                    $unit['type'],
                    $unit['en']['name'],
                    $unit['en']['symbol'],
                    $unit['ar']['name'],
                    $unit['ar']['symbol']
                )->create();
                $baseUnits[$unit['en']['name']] = $created;
            }
        }

        foreach($units as $unit){
            if($unit['base_unit'] !== null && isset($baseUnits[$unit['base_unit']])){
                $baseUnit = $baseUnits[$unit['base_unit']];
                MeasurementUnit::factory()->asDerivedUnit(
                    $baseUnit->id,
                    $unit['type'],
                    $unit['factor'],
                    $unit['en']['name'],
                    $unit['en']['symbol'],
                    $unit['ar']['name'],
                    $unit['ar']['symbol']
                )->create();
            }
        }
    }
}
