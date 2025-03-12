<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PetBreed;

class PetBreedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $date = date('Y-m-d H:i:s');
        PetBreed::insert([
            ['name_uk' => 'Німецька вівчарка', 'name_en' => 'German Shepherd', 'name_ru' => 'Немецкая овчарка', 'pet_type_id' => 1, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Лабрадор ретрівер', 'name_en' => 'Labrador Retriever', 'name_ru' => 'Лабрадор ретривер', 'pet_type_id' => 1, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Золотистий ретрівер', 'name_en' => 'Golden Retriever', 'name_ru' => 'Золотистый ретривер', 'pet_type_id' => 1, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Французький бульдог', 'name_en' => 'French Bulldog', 'name_ru' => 'Французский бульдог', 'pet_type_id' => 1, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Йоркширський терʼєр', 'name_en' => 'Yorkshire Terrier', 'name_ru' => 'Йоркширский терьер', 'pet_type_id' => 1, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Доберман', 'name_en' => 'Doberman', 'name_ru' => 'Доберман', 'pet_type_id' => 1, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Такса', 'name_en' => 'Dachshund', 'name_ru' => 'Такса', 'pet_type_id' => 1, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Сибірський хаскі', 'name_en' => 'Siberian Husky', 'name_ru' => 'Сибирский хаски', 'pet_type_id' => 1, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Чихуахуа', 'name_en' => 'Chihuahua', 'name_ru' => 'Чихуахуа', 'pet_type_id' => 1, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Мопс', 'name_en' => 'Pug', 'name_ru' => 'Мопс', 'pet_type_id' => 1, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Китайська хохлата собака', 'name_en' => 'Chinese Crested Dog', 'name_ru' => 'Китайская хохлатая собака', 'pet_type_id' => 1, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Бігль', 'name_en' => 'Beagle', 'name_ru' => 'Бигль', 'pet_type_id' => 1, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Боксер', 'name_en' => 'Boxer', 'name_ru' => 'Боксер', 'pet_type_id' => 1, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Англійський кокер-спаніель', 'name_en' => 'English Cocker Spaniel', 'name_ru' => 'Английский кокер-спаниель', 'pet_type_id' => 1, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Пудель', 'name_en' => 'Poodle', 'name_ru' => 'Пудель', 'pet_type_id' => 1, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Ротвейлер', 'name_en' => 'Rottweiler', 'name_ru' => 'Ротвейлер', 'pet_type_id' => 1, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Американський кокер-спаніель', 'name_en' => 'American Cocker Spaniel', 'name_ru' => 'Американский кокер-спаниель', 'pet_type_id' => 1, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Мальтійський бішон', 'name_en' => 'Maltese Bichon', 'name_ru' => 'Мальтийский бишон', 'pet_type_id' => 1, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Бішон фрізе', 'name_en' => 'Bichon Frise', 'name_ru' => 'Бишон фризе', 'pet_type_id' => 1, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Померанський шпіц', 'name_en' => 'Pomeranian Spitz', 'name_ru' => 'Померанский шпиц', 'pet_type_id' => 1, 'created_at' => $date, 'updated_at' => $date],
            
            ['name_uk' => 'Британська короткошерста', 'name_en' => 'British Shorthair', 'name_ru' => 'Британская короткошерстная', 'pet_type_id' => 2, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Мейн-кун', 'name_en' => 'Maine Coon', 'name_ru' => 'Мейн-кун', 'pet_type_id' => 2, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Бенгальська кішка', 'name_en' => 'Bengal Cat', 'name_ru' => 'Бенгальская кошка', 'pet_type_id' => 2, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Сіамська кішка', 'name_en' => 'Siamese Cat', 'name_ru' => 'Сиамская кошка', 'pet_type_id' => 2, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Сфінкс', 'name_en' => 'Sphynx', 'name_ru' => 'Сфинкс', 'pet_type_id' => 2, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Шотландська висловуха', 'name_en' => 'Scottish Fold', 'name_ru' => 'Шотландская вислоухая', 'pet_type_id' => 2, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Персидська кішка', 'name_en' => 'Persian Cat', 'name_ru' => 'Персидская кошка', 'pet_type_id' => 2, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Російська блакитна', 'name_en' => 'Russian Blue', 'name_ru' => 'Русская голубая', 'pet_type_id' => 2, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Регдол', 'name_en' => 'Ragdoll', 'name_ru' => 'Регдолл', 'pet_type_id' => 2, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Абіссінська кішка', 'name_en' => 'Abyssinian Cat', 'name_ru' => 'Абиссинская кошка', 'pet_type_id' => 2, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Орієнтальна кішка', 'name_en' => 'Oriental Cat', 'name_ru' => 'Ориентальная кошка', 'pet_type_id' => 2, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Бурманська кішка', 'name_en' => 'Burmese Cat', 'name_ru' => 'Бурманская кошка', 'pet_type_id' => 2, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Британська довгошерста', 'name_en' => 'British Longhair', 'name_ru' => 'Британская длинношерстная', 'pet_type_id' => 2, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Корат', 'name_en' => 'Korat', 'name_ru' => 'Корат', 'pet_type_id' => 2, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Турецька ангора', 'name_en' => 'Turkish Angora', 'name_ru' => 'Турецкая ангора', 'pet_type_id' => 2, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Турецький ван', 'name_en' => 'Turkish Van', 'name_ru' => 'Турецкий ван', 'pet_type_id' => 2, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Сомалійська кішка', 'name_en' => 'Somali Cat', 'name_ru' => 'Сомалийская кошка', 'pet_type_id' => 2, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Корат', 'name_en' => 'Korat', 'name_ru' => 'Корат', 'pet_type_id' => 2, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Турецька ангора', 'name_en' => 'Turkish Angora', 'name_ru' => 'Турецкая ангора', 'pet_type_id' => 2, 'created_at' => $date, 'updated_at' => $date],
            ['name_uk' => 'Турецький ван', 'name_en' => 'Turkish Van', 'name_ru' => 'Турецкий ван', 'pet_type_id' => 2, 'created_at' => $date, 'updated_at' => $date],
        ]);
    }
}
