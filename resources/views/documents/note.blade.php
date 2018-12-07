@extends('layouts.app')

@section('content')

    <documents-note :document="{{ json_encode($document) }}"></documents-note>

@endsection