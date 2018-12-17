<?php

namespace App\CoreFacturalo\Documents;

use App\CoreFacturalo\Helpers\Number\NumberLetter;
use App\Models\Document;
use Illuminate\Support\Str;

class DocumentBuilder
{
    public function saveDocument($data)
    {
        $data['number'] = Document::setNumber($data);
        $data['filename'] = Document::setFilename($data);
        $data['external_id'] = Str::uuid();
        $data['legends'] = $this->addLegends($data);

        $document = Document::create($data);

        foreach ($data['items'] as $row) {
            $document->details()->create($row);
        }

        return $document;
    }

    public function addLegends($data)
    {
        $legends = key_exists('legends', $data)?$data['legends']:[];
        $legends[] = [
            'code' => 1000,
            'value' => NumberLetter::convertToLetter($data['total'])
        ];

        return $legends;
    }
}