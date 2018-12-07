<?php

    Auth::routes();

    Route::middleware('auth')->group(function() {

        Route::get('/', function () {
            return redirect()->route('documents.create');
        });
        Route::get('dashboard', 'HomeController@index')->name('dashboard');

        //Company
        Route::get('companies/create', 'CompanyController@create')->name('companies.create');
        Route::get('companies/tables', 'CompanyController@tables');
        Route::get('companies/record', 'CompanyController@record');
        Route::post('companies', 'CompanyController@store');
        Route::post('companies/uploads', 'CompanyController@uploadFile');

        //Certificates
        Route::get('certificates/record', 'CertificateController@record');
        Route::post('certificates/uploads', 'CertificateController@uploadFile');
        Route::delete('certificates', 'CertificateController@destroy');

        //Users
        Route::get('users', 'UserController@index')->name('users.index');
        Route::get('users/columns', 'UserController@columns');
//        Route::get('users/create', 'UserController@create')->name('users.index');
        Route::get('users/tables', 'UserController@tables');
        Route::get('users/record/{user}', 'UserController@record');
        Route::post('users', 'UserController@store');
        Route::get('users/records', 'UserController@records');
        Route::delete('users/{user}', 'UserController@destroy');

        //Documents
        Route::get('documents', 'DocumentController@index')->name('documents.index');
        Route::get('documents/columns', 'DocumentController@columns');
        Route::get('documents/records', 'DocumentController@records');
//        Route::get('documents/create', 'DocumentController@create')->name('documents.create');
//        Route::get('documents/tables', 'DocumentController@tables');
//        Route::get('documents/record/{document}', 'DocumentController@record');
//        Route::post('documents', 'DocumentController@store');
        Route::post('documents/voided', 'DocumentController@voided');
        Route::get('documents/to_print/{document}', 'DocumentController@to_print');
        Route::get('documents/download/{type}/{document}', 'DocumentController@download')->name('documents.download');
        Route::get('documents/send_xml/{document}', 'DocumentController@send_xml');
        Route::post('documents/email', 'DocumentController@email');
        Route::get('documents/note/{document}', 'NoteController@create');
        Route::get('documents/item/tables', 'DocumentController@item_tables');
        Route::get('documents/table/{table}', 'DocumentController@table');

        //Summaries
        Route::get('summaries', 'SummaryController@index')->name('summaries.index');
        Route::get('summaries/records', 'SummaryController@records');
        Route::post('summaries/documents', 'SummaryController@documents');
        Route::post('summaries', 'SummaryController@store');
        Route::get('summaries/download/{type}/{summary}', 'SummaryController@download')->name('summaries.download');
        Route::get('summaries/ticket/{summary}', 'SummaryController@ticket');

        //Voided
        Route::get('voided/download/{type}/{voided}', 'VoidedController@download')->name('voided.download');
        Route::get('voided/ticket/{voided_id}/{group_id}', 'VoidedController@ticket');

        Route::get('reports', 'ReportController@index')->name('reports.index');
        Route::post('reports/search', 'ReportController@search')->name('search');
        Route::post('reports/pdf', 'ReportController@pdf')->name('report_pdf');
        Route::post('reports/excel', 'ReportController@excel')->name('report_excel');

        Route::post('options/delete_documents', 'OptionController@deleteDocuments');

        Route::get('services/ruc/{number}', 'Api\ServiceController@ruc');
        Route::get('services/dni/{number}', 'Api\ServiceController@dni');
    });
