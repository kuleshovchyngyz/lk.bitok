<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CollectionExport implements FromCollection, WithHeadings
{
    protected $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        return [
            "operation_id",
            "Дата и время",
            "сумма",
            "купил/продал",
            "user_id",
            "Номер кошелька",
            "Валюта",
            "ФИО",
            "ИНН",
            "Дата рождения",
        ];
    }
}
