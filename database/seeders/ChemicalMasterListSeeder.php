<?php

namespace Database\Seeders;

use App\Models\Chemical;
use App\Models\ChemicalCategory;
use App\Models\ChemicalLocation;
use App\Models\ChemicalMonthlyBalance;
use Illuminate\Database\Seeder;

class ChemicalMasterListSeeder extends Seeder
{
    public function run(): void
    {
        $category = ChemicalCategory::first();
        $location = ChemicalLocation::first();

        $chemicals = [

            ['no' => 1,   'name' => '1,10 - phenanthroline chloride monohydrate',                         'unit' => 'g'],
            ['no' => 2,   'name' => '1,10 - phenanthroline monohydrate',                                  'unit' => 'g'],
            ['no' => 3,   'name' => '1,8-Dihydroxy-2-(4-Sulfophenylazo)-naphthalene-3,6-disulfonic acid trisodium salt', 'unit' => 'g'],
            ['no' => 4,   'name' => '1,5-Diphenylcarbazide',                                              'unit' => 'g'],
            ['no' => 5,   'name' => '1-Amino-2-hydroxide-4-naphtalene sulfonic acid',                     'unit' => 'g'],
            ['no' => 6,   'name' => '1-Butanol',                                                          'unit' => 'ml'],
            ['no' => 7,   'name' => '1-Naphtholbenzine',                                                  'unit' => 'g'],
            ['no' => 8,   'name' => '4-amino-2,3-dimethyl-1phenyl-3-pyrazolin-5-one',                     'unit' => 'g'],
            ['no' => 9,   'name' => 'Acetic acid (glacial) 100%',                                         'unit' => 'ml'],
            ['no' => 10,  'name' => 'Acetone p.a',                                                        'unit' => 'ml'],
            ['no' => 11,  'name' => 'Acetone teknis',                                                     'unit' => 'ml'],
            ['no' => 12,  'name' => 'Alizarin -3- methylamine-N,N diacetic acid dihydrate',               'unit' => 'g'],
            ['no' => 13,  'name' => 'Alizarin Red indicator',                                             'unit' => 'g'],
            ['no' => 14,  'name' => 'Alkylbenzyl dimethylammonium chloride',                              'unit' => 'g'],
            ['no' => 15,  'name' => 'Aluminium foil',                                                     'unit' => 'pcs'],
            ['no' => 16,  'name' => 'Aluminium hydroxide',                                                'unit' => 'g'],
            ['no' => 17,  'name' => 'Aluminium kalium sulfat dodecahydrat',                               'unit' => 'g'],
            ['no' => 18,  'name' => 'Amidosulfuris acid',                                                 'unit' => 'g'],
            ['no' => 19,  'name' => 'Ammonia solution 25 %',                                              'unit' => 'ml'],
            ['no' => 20,  'name' => 'Ammonium acetate',                                                   'unit' => 'g'],
            ['no' => 21,  'name' => 'Ammonium fluoride',                                                  'unit' => 'g'],

            ['no' => 22,  'name' => 'Ammonium heptamolybdate tetrahydrate',                               'unit' => 'g'],
            ['no' => 23,  'name' => 'Ammonium iron (III) sulfate dodecahydrate',                          'unit' => 'g'],
            ['no' => 24,  'name' => 'Amonium chloride',                                                   'unit' => 'g'],
            ['no' => 25,  'name' => 'Amonium monovanadate',                                               'unit' => 'g'],
            ['no' => 26,  'name' => 'Aquabides',                                                          'unit' => 'ml'],
            ['no' => 27,  'name' => 'Asam salisilat teknis',                                              'unit' => 'g'],
            ['no' => 28,  'name' => 'Asam sulfamat',                                                      'unit' => 'g'],
            ['no' => 29,  'name' => 'Barbituric acid',                                                    'unit' => 'g'],
            ['no' => 30,  'name' => 'Barium acetat',                                                      'unit' => 'g'],
            ['no' => 31,  'name' => 'Barium chloride dihydrate',                                          'unit' => 'g'],
            ['no' => 32,  'name' => 'Biuret wasserfrei',                                                  'unit' => 'g'],
            ['no' => 33,  'name' => 'Boric acid',                                                         'unit' => 'g'],
            ['no' => 34,  'name' => 'Brilliant Gree Bile Broth',                                          'unit' => 'g'],
            ['no' => 35,  'name' => 'Bromocresol green indicator',                                        'unit' => 'g'],
            ['no' => 36,  'name' => 'Brucine',                                                            'unit' => 'g'],
            ['no' => 37,  'name' => 'Cadmium sulfat hydrate',                                             'unit' => 'g'],
            ['no' => 38,  'name' => 'Calcium carbonate',                                                  'unit' => 'g'],
            ['no' => 39,  'name' => 'Calcium hypochlorite',                                               'unit' => 'g'],
            ['no' => 40,  'name' => 'Cerium (III) nitratehex-hydrate',                                    'unit' => 'g'],
            ['no' => 41,  'name' => 'Chloramin T trihydrate',                                             'unit' => 'g'],
            ['no' => 42,  'name' => 'Chloroform',                                                         'unit' => 'ml'],

            ['no' => 43,  'name' => 'Chromatropic Acid disodium salt',                                    'unit' => 'g'],
            ['no' => 44,  'name' => 'Chromium (VI) oxide',                                                'unit' => 'g'],
            ['no' => 45,  'name' => 'Citric acid monohydrate',                                            'unit' => 'g'],
            ['no' => 46,  'name' => 'Cobalt (II) chloride hexahydrate',                                   'unit' => 'g'],
            ['no' => 47,  'name' => 'Compact dry bc',                                                     'unit' => 'pcs'],
            ['no' => 48,  'name' => 'Compact dry ec',                                                     'unit' => 'pcs'],
            ['no' => 49,  'name' => 'Compact dry etb',                                                    'unit' => 'pcs'],
            ['no' => 50,  'name' => 'Compact dry etc',                                                    'unit' => 'pcs'],
            ['no' => 51,  'name' => 'Compact dry pa',                                                     'unit' => 'pcs'],
            ['no' => 52,  'name' => 'Compact dry SA',                                                     'unit' => 'pcs'],
            ['no' => 53,  'name' => 'Compact dry sl',                                                     'unit' => 'pcs'],
            ['no' => 54,  'name' => 'Compact dry tc',                                                     'unit' => 'pcs'],
            ['no' => 55,  'name' => 'Compact dry tc (2)',                                                 'unit' => 'pcs'],
            ['no' => 56,  'name' => 'Compact dry vp',                                                     'unit' => 'pcs'],
            ['no' => 57,  'name' => 'Compact dry ym',                                                     'unit' => 'pcs'],
            ['no' => 58,  'name' => 'Copper (II) sulfate pentahydrate',                                   'unit' => 'g'],
            ['no' => 59,  'name' => 'Curcumine',                                                          'unit' => 'g'],
            ['no' => 60,  'name' => 'Cyclohexane',                                                        'unit' => 'ml'],
            ['no' => 61,  'name' => 'D (+) Glucose anhydrous for biochemistry',                           'unit' => 'g'],
            ['no' => 62,  'name' => 'Devarda alloy',                                                      'unit' => 'g'],
            ['no' => 63,  'name' => 'Di-ammonium oxalate monohydrate',                                    'unit' => 'g'],

            ['no' => 64,  'name' => 'Di-amonium hydrogen phosphate',                                      'unit' => 'g'],
            ['no' => 65,  'name' => 'Dikalium hydrogen phosphat',                                         'unit' => 'g'],
            ['no' => 66,  'name' => 'Dimedone',                                                           'unit' => 'g'],
            ['no' => 67,  'name' => 'Disodium hydrogen phosphate dodecahydrate',                          'unit' => 'g'],
            ['no' => 68,  'name' => 'Disodium hydrogen phosphate heptahydrate',                           'unit' => 'g'],
            ['no' => 69,  'name' => 'Di-sodium oxalate',                                                  'unit' => 'g'],
            ['no' => 70,  'name' => 'Dodecyl sulfate sodium salt',                                        'unit' => 'g'],
            ['no' => 71,  'name' => 'EC Broth',                                                           'unit' => 'g'],
            ['no' => 72,  'name' => 'Eisen (II) sulfate-heptahydrat',                                     'unit' => 'g'],
            ['no' => 73,  'name' => 'EMB Agar',                                                           'unit' => 'g'],
            ['no' => 74,  'name' => 'EO STERILE PETRI DISH 90MM CITOTEST',                                'unit' => 'pcs'],
            ['no' => 75,  'name' => 'Eosin Y (Yellowish) CI 45380',                                       'unit' => 'g'],
            ['no' => 76,  'name' => 'Erichrome cyanine R CI 4380',                                        'unit' => 'g'],
            ['no' => 77,  'name' => 'Ethanol',                                                            'unit' => 'ml'],
            ['no' => 78,  'name' => 'Ethanol absolut',                                                    'unit' => 'ml'],
            ['no' => 79,  'name' => 'Fecal swab 2 ml copan',                                              'unit' => 'pcs'],
            ['no' => 80,  'name' => 'Ferroin indicator solution',                                          'unit' => 'ml'],
            ['no' => 81,  'name' => 'Formaldehyde solution 37%',                                          'unit' => 'ml'],
            ['no' => 82,  'name' => 'Glass fiber filter 1 mikron 47 mm 10 pk PALL',                       'unit' => 'pcs'],
            ['no' => 83,  'name' => 'Glass fiber filter 0,45 mikron 47 mm 10 pk PALL',                    'unit' => 'pcs'],
            ['no' => 84,  'name' => 'Gelatine',                                                           'unit' => 'g'],

            ['no' => 85,  'name' => 'Hexamethylene-tetramine',                                            'unit' => 'g'],
            ['no' => 86,  'name' => 'Hydrazinium sulfate',                                                'unit' => 'g'],
            ['no' => 87,  'name' => 'Hydrochloric acid 37%',                                              'unit' => 'ml'],
            ['no' => 88,  'name' => 'Hydrochloric acid fuming 37%',                                       'unit' => 'ml'],
            ['no' => 89,  'name' => 'Hydrogen peroxide 30%',                                              'unit' => 'ml'],
            ['no' => 90,  'name' => 'Hydroxlamine hydrochloride',                                         'unit' => 'g'],
            ['no' => 91,  'name' => 'Hydroxylammonium chloride',                                          'unit' => 'g'],
            ['no' => 92,  'name' => 'Hydroxylammonium sulfate',                                           'unit' => 'g'],
            ['no' => 93,  'name' => 'Indigocarmin',                                                       'unit' => 'g'],
            ['no' => 94,  'name' => 'Iodine',                                                             'unit' => 'g'],
            ['no' => 95,  'name' => 'Iron (III) Chloride',                                                'unit' => 'g'],
            ['no' => 96,  'name' => 'Iron (II) Sulfate Heptahydrate',                                     'unit' => 'g'],
            ['no' => 97,  'name' => 'Iron test',                                                          'unit' => 'pcs'],
            ['no' => 98,  'name' => 'Isoamyl alkohol',                                                    'unit' => 'ml'],
            ['no' => 99,  'name' => 'Isobutyl methyl ketone',                                             'unit' => 'ml'],
            ['no' => 100, 'name' => 'Kalium bromat',                                                      'unit' => 'g'],
            ['no' => 101, 'name' => 'Kalium chlorat',                                                     'unit' => 'g'],
            ['no' => 102, 'name' => 'Kalium iodidat',                                                     'unit' => 'g'],
            ['no' => 103, 'name' => 'Kaliumantimon (III)-oxidtartrat-hemihydrat',                         'unit' => 'g'],
            ['no' => 104, 'name' => 'Kertas saring 41 Diameter 125 mm Whatman',                           'unit' => 'pcs'],
            ['no' => 105, 'name' => 'Kertas saring 42 Diameter 125 mm Whatman',                           'unit' => 'pcs'],

            ['no' => 106, 'name' => 'Kertas saring 43 Diameter 125 mm Whatman',                           'unit' => 'pcs'],
            ['no' => 107, 'name' => 'Kertas saring 0,45 mikron Diameter 47 mm Whatman',                   'unit' => 'pcs'],
            ['no' => 108, 'name' => 'Kertas saring 0,45 mikron Diameter 47 mm PALL',                      'unit' => 'pcs'],
            ['no' => 109, 'name' => 'Kwikstik a. brasiliensis',                                           'unit' => 'pcs'],
            ['no' => 110, 'name' => 'Kwikstik c. albicans',                                               'unit' => 'pcs'],
            ['no' => 111, 'name' => 'Kwikstik e. coli',                                                   'unit' => 'pcs'],
            ['no' => 112, 'name' => 'Kwikstik k. aerogenes',                                              'unit' => 'pcs'],
            ['no' => 113, 'name' => 'Kwikstik s. aureus',                                                 'unit' => 'pcs'],
            ['no' => 114, 'name' => 'Kwikstik s. typhimurium',                                            'unit' => 'pcs'],
            ['no' => 115, 'name' => 'L (+) Asorbic Acid',                                                 'unit' => 'g'],
            ['no' => 116, 'name' => 'Lactose Broth agar',                                                 'unit' => 'g'],
            ['no' => 117, 'name' => 'Lanthanum (III) chloride heptahydrate 98%',                          'unit' => 'g'],
            ['no' => 118, 'name' => 'Lanthanum nitrate hexahydrate',                                      'unit' => 'g'],
            ['no' => 119, 'name' => 'Lauryl Tryptose Broth',                                              'unit' => 'g'],
            ['no' => 120, 'name' => 'Lead (II) acetate trihydrate',                                       'unit' => 'g'],
            ['no' => 121, 'name' => 'Lead (II) oxide',                                                    'unit' => 'g'],
            ['no' => 122, 'name' => 'L-Histidine monohydrate chloride monohydrate',                       'unit' => 'g'],
            ['no' => 123, 'name' => 'M Endo agar',                                                        'unit' => 'g'],
            ['no' => 124, 'name' => 'M Fecal coliform agar',                                              'unit' => 'g'],
            ['no' => 125, 'name' => 'MacConkey Agar',                                                     'unit' => 'g'],
            ['no' => 126, 'name' => 'Magnesium (II) sulfate monohydrate',                                 'unit' => 'g'],

            ['no' => 127, 'name' => 'Magnesium chloride',                                                 'unit' => 'g'],
            ['no' => 128, 'name' => 'Magnesium chloride hexahydrate',                                     'unit' => 'g'],
            ['no' => 129, 'name' => 'Magnesium oxide',                                                    'unit' => 'g'],
            ['no' => 130, 'name' => 'Manganese (II) Sulfate Monohydrate',                                 'unit' => 'g'],
            ['no' => 131, 'name' => 'Manganese test',                                                     'unit' => 'pcs'],
            ['no' => 132, 'name' => 'Masker orlee hijab',                                                 'unit' => 'pcs'],
            ['no' => 133, 'name' => 'Masker orlee non hijab',                                             'unit' => 'pcs'],
            ['no' => 134, 'name' => 'Mercury (II) chloride',                                              'unit' => 'g'],
            ['no' => 135, 'name' => 'Mercury (II) iodide',                                                'unit' => 'g'],
            ['no' => 136, 'name' => 'Mercury (II) oxide',                                                 'unit' => 'g'],
            ['no' => 137, 'name' => 'Mercury (II) sulfate',                                               'unit' => 'g'],
            ['no' => 138, 'name' => 'Mercury (II) thiocyanate',                                           'unit' => 'g'],
            ['no' => 139, 'name' => 'Methanol',                                                           'unit' => 'ml'],
            ['no' => 140, 'name' => 'Methyl Purple Indicator Solution 1 g/L',                             'unit' => 'ml'],
            ['no' => 141, 'name' => 'Methyl red (CI 13020)',                                              'unit' => 'g'],
            ['no' => 142, 'name' => 'Methyl red sodium salt (CI 13020)',                                  'unit' => 'g'],
            ['no' => 143, 'name' => 'Methylenblue',                                                       'unit' => 'g'],
            ['no' => 144, 'name' => 'Micropipette 1 ml',                                                  'unit' => 'pcs'],
            ['no' => 145, 'name' => 'Micropipette 5 ml',                                                  'unit' => 'pcs'],
            ['no' => 146, 'name' => 'Micropipette plus vol 100 mikron',                                   'unit' => 'pcs'],
            ['no' => 147, 'name' => 'Micropipette plus vol 0,5 ml',                                       'unit' => 'pcs'],
            ['no' => 148, 'name' => 'MUG EC Broth',                                                       'unit' => 'g'],

            ['no' => 149, 'name' => 'N-(1-Naphthyl) ethylenediamine dihydro-chloride',                    'unit' => 'g'],
            ['no' => 150, 'name' => 'N,N-diethyl-1,4 phenyle diammonium sulfat',                          'unit' => 'g'],
            ['no' => 151, 'name' => 'N,N-dimethyl-1,4 phenylene diamonium dichloride',                    'unit' => 'g'],
            ['no' => 152, 'name' => 'N,N-Dimethyl-1,4 phenyl-enediamine oxalate',                        'unit' => 'g'],
            ['no' => 153, 'name' => 'n-Amylalkohol',                                                      'unit' => 'ml'],
            ['no' => 154, 'name' => 'Naphtholbenzein',                                                    'unit' => 'g'],
            ['no' => 155, 'name' => 'Natrium fluoride',                                                   'unit' => 'g'],
            ['no' => 156, 'name' => 'Natrium molibdat dihidrat',                                          'unit' => 'g'],
            ['no' => 157, 'name' => 'n-heptane',                                                          'unit' => 'ml'],
            ['no' => 158, 'name' => 'n-Hexadecane',                                                       'unit' => 'ml'],
            ['no' => 159, 'name' => 'Nickel (II) nitrate hexahydrate',                                    'unit' => 'g'],
            ['no' => 160, 'name' => 'Nitric acid 65%',                                                    'unit' => 'ml'],
            ['no' => 161, 'name' => 'Nutrient Agar',                                                      'unit' => 'g'],
            ['no' => 162, 'name' => 'Nutrient Broth',                                                     'unit' => 'g'],
            ['no' => 163, 'name' => 'Ortho-Phosphoric acid 85%',                                          'unit' => 'ml'],
            ['no' => 164, 'name' => 'Oxalic acid dihydrate',                                              'unit' => 'g'],
            ['no' => 165, 'name' => 'Pararosanline (chloride) CI 42500',                                  'unit' => 'g'],
            ['no' => 166, 'name' => 'Perochloric acid 70-72%',                                            'unit' => 'ml'],
            ['no' => 167, 'name' => 'Petri Dish Labware Charuzu 60 x 15 mm',                              'unit' => 'pcs'],
            ['no' => 168, 'name' => 'Pewarnaan gram (crystal violet)',                                    'unit' => 'ml'],
            ['no' => 169, 'name' => 'Pewarnaan gram (lugol)',                                             'unit' => 'ml'],

            ['no' => 170, 'name' => 'Pewarnaan gram (safranin)',                                          'unit' => 'ml'],
            ['no' => 171, 'name' => 'Phenol',                                                             'unit' => 'g'],
            ['no' => 172, 'name' => 'Phenol Red (Phenol sulfonphthalein)',                                'unit' => 'g'],
            ['no' => 173, 'name' => 'Phenol red indicator',                                               'unit' => 'g'],
            ['no' => 174, 'name' => 'Phenolphathalein indicator',                                         'unit' => 'g'],
            ['no' => 175, 'name' => 'Pipet pasteur plastik 3 ml',                                        'unit' => 'pcs'],
            ['no' => 176, 'name' => 'Pipet pasteur plastik 5 ml',                                        'unit' => 'pcs'],
            ['no' => 177, 'name' => 'Pipette Tips 1-10ml',                                               'unit' => 'pcs'],
            ['no' => 178, 'name' => 'Plate count agar',                                                   'unit' => 'g'],
            ['no' => 179, 'name' => 'Plate count agar (2)',                                               'unit' => 'g'],
            ['no' => 180, 'name' => 'Potassium chloride',                                                 'unit' => 'g'],
            ['no' => 181, 'name' => 'Potassium chromate',                                                 'unit' => 'g'],
            ['no' => 182, 'name' => 'Potassium cyanide',                                                  'unit' => 'g'],
            ['no' => 183, 'name' => 'Potassium dichromate',                                               'unit' => 'g'],
            ['no' => 184, 'name' => 'Potassium dihydrogen phosphate',                                     'unit' => 'g'],
            ['no' => 185, 'name' => 'Potassium hexacynoferrate (II) trihydrate',                          'unit' => 'g'],
            ['no' => 186, 'name' => 'Potassium hexxchloroplatirale (IV)',                                  'unit' => 'g'],
            ['no' => 187, 'name' => 'Potassium hydrogen phthalate',                                       'unit' => 'g'],
            ['no' => 188, 'name' => 'Potassium hydroxide',                                                'unit' => 'g'],
            ['no' => 189, 'name' => 'Potassium Iodide',                                                   'unit' => 'g'],
            ['no' => 190, 'name' => 'Potassium nitrate',                                                  'unit' => 'g'],

            ['no' => 191, 'name' => 'Potassium permanganat',                                              'unit' => 'g'],
            ['no' => 192, 'name' => 'Potassium peroxodisulfate',                                          'unit' => 'g'],
            ['no' => 193, 'name' => 'Potassium sodium tartrate tetrahydrate',                             'unit' => 'g'],
            ['no' => 194, 'name' => 'Potassium Sulfate',                                                  'unit' => 'g'],
            ['no' => 195, 'name' => 'Potato Dextrose Agar',                                              'unit' => 'g'],
            ['no' => 196, 'name' => 'Potato Dextrose Agar (2)',                                          'unit' => 'g'],
            ['no' => 197, 'name' => 'Pyrroliodine-1-dithiocarboxylic acid ammonium salt',                 'unit' => 'g'],
            ['no' => 198, 'name' => 'Sabun cuci sunlight',                                               'unit' => 'pcs'],
            ['no' => 199, 'name' => 'Salicylic Acid',                                                    'unit' => 'g'],
            ['no' => 200, 'name' => 'Sarung tangan ukuran S',                                            'unit' => 'pcs'],
            ['no' => 201, 'name' => 'Sarung tangan ukuran M',                                            'unit' => 'pcs'],
            ['no' => 202, 'name' => 'Sarung tangan ukuran L',                                            'unit' => 'pcs'],
            ['no' => 203, 'name' => 'Selenium reagent mixture',                                          'unit' => 'g'],
            ['no' => 204, 'name' => 'Silver nitrate',                                                    'unit' => 'g'],
            ['no' => 205, 'name' => 'Silver sulfate',                                                    'unit' => 'g'],
            ['no' => 206, 'name' => 'Sodium acetat trihydrate',                                          'unit' => 'g'],
            ['no' => 207, 'name' => 'Sodium azide',                                                      'unit' => 'g'],
            ['no' => 208, 'name' => 'Sodium borohyride',                                                 'unit' => 'g'],
            ['no' => 209, 'name' => 'Sodium Carbonate',                                                  'unit' => 'g'],
            ['no' => 210, 'name' => 'Sodium chloride',                                                   'unit' => 'g'],
            ['no' => 211, 'name' => 'Sodium dihydrogen phospate dihydrate',                              'unit' => 'g'],

            ['no' => 212, 'name' => 'Sodium fluoride',                                                   'unit' => 'g'],
            ['no' => 213, 'name' => 'Sodium hydrogen Carbonate',                                         'unit' => 'g'],
            ['no' => 214, 'name' => 'Sodium hydrogen sulfite',                                           'unit' => 'g'],
            ['no' => 215, 'name' => 'Sodium hydroxide',                                                  'unit' => 'g'],
            ['no' => 216, 'name' => 'Sodium nitrite',                                                    'unit' => 'g'],
            ['no' => 217, 'name' => 'Sodium nitroprusside dihydrate',                                    'unit' => 'g'],
            ['no' => 218, 'name' => 'Sodium sulfate',                                                    'unit' => 'g'],
            ['no' => 219, 'name' => 'Sodium sulfite',                                                    'unit' => 'g'],
            ['no' => 220, 'name' => 'Sodium tetraborate',                                                'unit' => 'g'],
            ['no' => 221, 'name' => 'Sodium tetraphenyl borate',                                         'unit' => 'g'],
            ['no' => 222, 'name' => 'Sodium thiosulfate',                                                'unit' => 'g'],
            ['no' => 223, 'name' => 'Spirtus',                                                           'unit' => 'ml'],
            ['no' => 224, 'name' => 'Starch',                                                            'unit' => 'g'],
            ['no' => 225, 'name' => 'Stearic acid',                                                      'unit' => 'g'],
            ['no' => 226, 'name' => 'Sterile Petri Dish Labware Charuzu 90 x 15 mm',                      'unit' => 'pcs'],
            ['no' => 227, 'name' => 'Strontium nitrate',                                                 'unit' => 'g'],
            ['no' => 228, 'name' => 'Sulfanilamide',                                                     'unit' => 'g'],
            ['no' => 229, 'name' => 'Sulfanilic acid',                                                   'unit' => 'g'],
            ['no' => 230, 'name' => 'Sulphuric acid',                                                    'unit' => 'ml'],
            ['no' => 231, 'name' => 'Susu bear brand',                                                   'unit' => 'pcs'],
            ['no' => 232, 'name' => 'Thiocetamide',                                                      'unit' => 'g'],

            ['no' => 233, 'name' => 'Tin (II) chloride',                                                 'unit' => 'g'],
            ['no' => 234, 'name' => 'Tisu paseo',                                                        'unit' => 'pcs'],
            ['no' => 235, 'name' => 'Titriplex',                                                         'unit' => 'g'],
            ['no' => 236, 'name' => 'Titriplex I',                                                       'unit' => 'g'],
            ['no' => 237, 'name' => 'Titriplex II',                                                      'unit' => 'g'],
            ['no' => 238, 'name' => 'Titriplex III',                                                     'unit' => 'g'],
            ['no' => 239, 'name' => 'Tri-natriumphosphat-dodecahhydrate',                                 'unit' => 'g'],
            ['no' => 240, 'name' => 'Trisodium citrate dihydrate',                                       'unit' => 'g'],
            ['no' => 241, 'name' => 'Tri-Sodium Phosphate dodecahydrat',                                 'unit' => 'g'],
            ['no' => 242, 'name' => 'Tube 16 x 100 srk medium',                                          'unit' => 'pcs'],
            ['no' => 243, 'name' => 'Wolframatophosphosaure hydrate',                                     'unit' => 'g'],
            ['no' => 244, 'name' => 'Xylene',                                                            'unit' => 'ml'],
            ['no' => 245, 'name' => 'Zirkon (IV) Oxidchlorid-Octahydrat',                                'unit' => 'g'],
            ['no' => 246, 'name' => 'Zinc acetate dihydrate',                                            'unit' => 'g'],
            ['no' => 247, 'name' => 'Zinc sulfate',                                                      'unit' => 'g'],
            ['no' => 248, 'name' => 'Zinc sulfate heptahydrate',                                         'unit' => 'g'],
        ];

        $currentMonth = now()->format('Y-m');
        $added   = 0;
        $updated = 0;
        $existed = 0;

        foreach ($chemicals as $item) {
            $baseName = trim(preg_replace('/\s*\(\d+\)$/', '', $item['name']));

            $chemical = Chemical::where('chemical_name', $item['name'])
                ->orWhere('chemical_name', $baseName)
                ->first();

            if (!$chemical) {

                $code = 'CHM-REF-' . str_pad((string)$item['no'], 3, '0', STR_PAD_LEFT);
                if (Chemical::where('chemical_code', $code)->exists()) {
                    $code = 'CHM-REF-' . str_pad((string)$item['no'], 3, '0', STR_PAD_LEFT) . '-' . strtoupper(bin2hex(random_bytes(2)));
                }

                $chemical = Chemical::create([
                    'chemical_code'  => $code,
                    'chemical_name'  => $item['name'],
                    'category_id'    => $category?->id ?? 1,
                    'location_id'    => $location?->id ?? 1,
                    'unit'           => $item['unit'],
                    'current_stock'  => 0,
                    'minimum_stock'  => 0,
                    'status'         => 'SAFE',
                    'physical_state' => in_array($item['unit'], ['ml', 'L']) ? 'liquid' : 'solid',
                ]);
                $added++;
            } else {

                if (empty($chemical->unit) && !empty($item['unit'])) {
                    $chemical->unit = $item['unit'];
                    $chemical->save();
                    $updated++;
                } else {
                    $existed++;
                }
            }

            foreach (array_unique(['2026-04', $currentMonth]) as $month) {
                ChemicalMonthlyBalance::firstOrCreate(
                    [
                        'chemical_id'  => $chemical->id,
                        'period_month' => $month,
                    ],
                    [
                        'saldo_awal' => (float)($chemical->current_stock ?? 0),
                        'penerimaan' => 0,
                    ]
                );
            }
        }

        $this->command->info("Chemical Master List seeded: {$added} added, {$updated} updated unit, {$existed} already existed (total: " . count($chemicals) . ").");
    }
}

