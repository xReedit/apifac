<?php
namespace App\Http\Controllers;

use App\Models\Document;

class NoteController extends Controller
{
    public function create($document_id)
    {
        $document = Document::with(['details', 'invoice'])->find($document_id);

        return view('documents.note', compact('document'));
    }
}