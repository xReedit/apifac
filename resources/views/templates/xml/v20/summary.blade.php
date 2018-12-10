@php
    $summary = $document;
    $documents = $summary->documents;
@endphp
{!! '<?xml version="1.0" encoding="utf-8" standalone="no"?>' !!}
<SummaryDocuments
        xmlns="urn:sunat:names:specification:ubl:peru:schema:xsd:SummaryDocuments-1"
        xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
        xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
        xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
        xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"
        xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <ext:UBLExtensions>
        <ext:UBLExtension>
            <ext:ExtensionContent/>
        </ext:UBLExtension>
    </ext:UBLExtensions>
    <cbc:UBLVersionID>2.0</cbc:UBLVersionID>
    <cbc:CustomizationID>1.1</cbc:CustomizationID>
    <cbc:ID>{{ $summary->identifier }}</cbc:ID>
    <cbc:ReferenceDate>{{ $summary->date_of_reference->format('Y-m-d') }}</cbc:ReferenceDate>
    <cbc:IssueDate>{{ $summary->date_of_issue->format('Y-m-d') }}</cbc:IssueDate>
    <cac:Signature>
        <cbc:ID>{{ $company->number }}</cbc:ID>
        <cac:SignatoryParty>
            <cac:PartyIdentification>
                <cbc:ID>{{ $company->number }}</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><![CDATA[{{ $company->trade_name }}]]></cbc:Name>
            </cac:PartyName>
        </cac:SignatoryParty>
        <cac:DigitalSignatureAttachment>
            <cac:ExternalReference>
                <cbc:URI>#FACTURALO-PERU</cbc:URI>
            </cac:ExternalReference>
        </cac:DigitalSignatureAttachment>
    </cac:Signature>
    <cac:AccountingSupplierParty>
        <cbc:CustomerAssignedAccountID>{{ $company->number }}</cbc:CustomerAssignedAccountID>
        <cbc:AdditionalAccountID>6</cbc:AdditionalAccountID>
        <cac:Party>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA[{{$company->name}}]]></cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingSupplierParty>
    @foreach($documents as $doc)
        @php
            $document = $doc->document;
            $isNote = in_array($document->document_type_code, ['07', '08']);
        @endphp
        <sac:SummaryDocumentsLine>
            <cbc:LineID>{{ $loop->iteration }}</cbc:LineID>
            <cbc:DocumentTypeCode>{{ $document->document_type_code }}</cbc:DocumentTypeCode>
            <cbc:ID>{{ $document->series }}-{{ $document->number }}</cbc:ID>
            <cac:AccountingCustomerParty>
                <cbc:CustomerAssignedAccountID>{{ $document->customer->number }}</cbc:CustomerAssignedAccountID>
                <cbc:AdditionalAccountID>{{ $document->customer->identity_document_type_code }}</cbc:AdditionalAccountID>
            </cac:AccountingCustomerParty>
            @if($isNote)
            <cac:BillingReference>
                <cac:InvoiceDocumentReference>
                    <cbc:ID>{{ $document->note->affected_document_series }}-{{ $document->note->affected_document_number }}</cbc:ID>
                    <cbc:DocumentTypeCode>{{ $document->note->affected_document_type_code }}</cbc:DocumentTypeCode>
                </cac:InvoiceDocumentReference>
            </cac:BillingReference>
            @endif
            @if($isNote)
                @php($perception = $document->note->perception)
            @else
                @php($perception = $document->invoice->perception)
            @endif
            <sac:SUNATPerceptionSummaryDocumentReference>
                <sac:SUNATPerceptionSystemCode>{{ $perception->code }}</sac:SUNATPerceptionSystemCode>
                <sac:SUNATPerceptionPercent>{{ $perception->percentage }}</sac:SUNATPerceptionPercent>
                <cbc:TotalInvoiceAmount currencyID="PEN">{{ $perception->monto }}</cbc:TotalInvoiceAmount>
                <sac:SUNATTotalCashed currencyID="PEN">{{ $perception->total }}</sac:SUNATTotalCashed>
                <cbc:TaxableAmount currencyID="PEN">{{ $perception->base }}</cbc:TaxableAmount>
            </sac:SUNATPerceptionSummaryDocumentReference>
            <cac:Status>
                <cbc:ConditionCode>{{ $summary->process_type_id }}</cbc:ConditionCode>
            </cac:Status>
            <sac:TotalAmount currencyID="{{ $document->currency_type_code }}">{{ $document->total }}</sac:TotalAmount>
            @if($document->total_taxed > 0)
                <sac:BillingPayment>
                    <cbc:PaidAmount currencyID="{{ $document->currency_type_code }}">{{ $document->total_taxed }}</cbc:PaidAmount>
                    <cbc:InstructionID>01</cbc:InstructionID>
                </sac:BillingPayment>
            @endif
            @if($document->total_exonerated > 0)
                <sac:BillingPayment>
                    <cbc:PaidAmount currencyID="{{ $document->currency_type_code }}">{{ $document->total_exonerated }}</cbc:PaidAmount>
                    <cbc:InstructionID>02</cbc:InstructionID>
                </sac:BillingPayment>
            @endif
            @if($document->total_unaffected > 0)
                <sac:BillingPayment>
                    <cbc:PaidAmount currencyID="{{ $document->currency_type_code }}">{{ $document->total_unaffected }}</cbc:PaidAmount>
                    <cbc:InstructionID>03</cbc:InstructionID>
                </sac:BillingPayment>
            @endif
            @if($document->total_exportation > 0)
            <sac:BillingPayment>
                <cbc:PaidAmount currencyID="{{ $document->currency_type_code }}">{{ $document->total_exportation }}</cbc:PaidAmount>
                <cbc:InstructionID>04</cbc:InstructionID>
            </sac:BillingPayment>
            @endif
            @if(!$isNote)
                @if($document->invoice->total_free > 0)
                    <sac:BillingPayment>
                        <cbc:PaidAmount currencyID="{{ $document->currency_type_code }}">{{ $document->invoice->total_free }}</cbc:PaidAmount>
                        <cbc:InstructionID>05</cbc:InstructionID>
                    </sac:BillingPayment>
                @endif
            @endif
            @if($document->total_other_charges > 0)
                <cac:AllowanceCharge>
                    <cbc:ChargeIndicator>true</cbc:ChargeIndicator>
                    <cbc:Amount currencyID="{{ $document->currency_type_code }}">{{ $document->total_other_charges }}</cbc:Amount>
                </cac:AllowanceCharge>
            @endif
            <cac:TaxTotal>
                <cbc:TaxAmount currencyID="{{ $document->currency_type_code }}">{{ $document->total_igv }}</cbc:TaxAmount>
                <cac:TaxSubtotal>
                    <cbc:TaxAmount currencyID="{{ $document->currency_type_code }}">{{ $document->total_igv }}</cbc:TaxAmount>
                    <cac:TaxCategory>
                        <cac:TaxScheme>
                            <cbc:ID>1000</cbc:ID>
                            <cbc:Name>IGV</cbc:Name>
                            <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                        </cac:TaxScheme>
                    </cac:TaxCategory>
                </cac:TaxSubtotal>
            </cac:TaxTotal>
            @if($document->total_isc > 0)
                <cac:TaxTotal>
                    <cbc:TaxAmount currencyID="{{ $document->currency_type_code }}">{{ $document->total_isc }}</cbc:TaxAmount>
                    <cac:TaxSubtotal>
                        <cbc:TaxAmount currencyID="{{ $document->currency_type_code }}">{{$document->total_isc }}</cbc:TaxAmount>
                        <cac:TaxCategory>
                            <cac:TaxScheme>
                                <cbc:ID>2000</cbc:ID>
                                <cbc:Name>ISC</cbc:Name>
                                <cbc:TaxTypeCode>EXC</cbc:TaxTypeCode>
                            </cac:TaxScheme>
                        </cac:TaxCategory>
                    </cac:TaxSubtotal>
                </cac:TaxTotal>
            @endif
            @if($document->total_other_taxes > 0)
                <cac:TaxTotal>
                    <cbc:TaxAmount currencyID="{{ $document->currency_type_code }}">{{ $document->total_other_taxes }}</cbc:TaxAmount>
                    <cac:TaxSubtotal>
                        <cbc:TaxAmount currencyID="{{ $document->currency_type_code }}">{{ $document->total_other_taxes }}</cbc:TaxAmount>
                        <cac:TaxCategory>
                            <cac:TaxScheme>
                                <cbc:ID>9999</cbc:ID>
                                <cbc:Name>OTROS</cbc:Name>
                                <cbc:TaxTypeCode>>OTH</cbc:TaxTypeCode>
                            </cac:TaxScheme>
                        </cac:TaxCategory>
                    </cac:TaxSubtotal>
                </cac:TaxTotal>
            @endif
        </sac:SummaryDocumentsLine>
    @endforeach
</SummaryDocuments>