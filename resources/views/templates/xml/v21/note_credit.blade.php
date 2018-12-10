@php
    $establishment = $document->establishment;
    $customer = $document->customer;
    $details = $document->details;
    $legends = $document->legends;
    $note = $document->note;
@endphp
{!! '<?xml version="1.0" encoding="utf-8" standalone="no"?>' !!}
<CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2"
            xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
            xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
            xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
            xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">
    <ext:UBLExtensions>
        <ext:UBLExtension>
            <ext:ExtensionContent/>
        </ext:UBLExtension>
    </ext:UBLExtensions>
    <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
    <cbc:CustomizationID>2.0</cbc:CustomizationID>
    <cbc:ID>{{$document->series.'-'.$document->number}}</cbc:ID>
    <cbc:IssueDate>{{ $document->date_of_issue->format('Y-m-d') }}</cbc:IssueDate>
    <cbc:IssueTime>{{ $document->time_of_issue }}</cbc:IssueTime>
    @foreach($legends as $legend)
        <cbc:Note languageLocaleID="{{ $legend->code }}"><![CDATA[{{ $legend->description }}]]></cbc:Note>
    @endforeach
    <cbc:DocumentCurrencyCode>{{ $document->currency_type_code }}</cbc:DocumentCurrencyCode>
    <cac:DiscrepancyResponse>
        <cbc:ReferenceID>{{ $note->affected_document_series.'-'.$note->affected_document_number }}</cbc:ReferenceID>
        <cbc:ResponseCode>{{ $note->note_type_code }}</cbc:ResponseCode>
        <cbc:Description>{{ $note->description }}</cbc:Description>
    </cac:DiscrepancyResponse>
    @if($document->purchase_order)
        <cac:OrderReference>
            <cbc:ID>{{ $document->purchase_order }}</cbc:ID>
        </cac:OrderReference>
    @endif
    <cac:BillingReference>
        <cac:InvoiceDocumentReference>
            <cbc:ID>{{ $note->affected_document_series.'-'.$note->affected_document_number }}</cbc:ID>
            <cbc:DocumentTypeCode>{{ $note->affected_document_type_code }}</cbc:DocumentTypeCode>
        </cac:InvoiceDocumentReference>
    </cac:BillingReference>
    @if($document->guides)
        @foreach($document->guides as $guide)
            <cac:DespatchDocumentReference>
                <cbc:ID>{{ $guide->number }}</cbc:ID>
                <cbc:DocumentTypeCode>{{ $guide->document_type_code }}</cbc:DocumentTypeCode>
            </cac:DespatchDocumentReference>
        @endforeach
    @endif
    @if($document->related_documents)
        @foreach($document->related_documents as $related)
            <cac:AdditionalDocumentReference>
                <cbc:ID>{{ $related->number }}</cbc:ID>
                <cbc:DocumentTypeCode>{{ $related->document_type_code }}</cbc:DocumentTypeCode>
            </cac:AdditionalDocumentReference>
        @endforeach
    @endif
    <cac:Signature>
        <cbc:ID>{{ $company->number }}</cbc:ID>
        <cbc:Note>FACTURALO</cbc:Note>
        <cac:SignatoryParty>
            <cac:PartyIdentification>
                <cbc:ID>{{ $company->number }}</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><![CDATA[{{ $company->name }}]]></cbc:Name>
            </cac:PartyName>
        </cac:SignatoryParty>
        <cac:DigitalSignatureAttachment>
            <cac:ExternalReference>
                <cbc:URI>#FACTURALO-PERU</cbc:URI>
            </cac:ExternalReference>
        </cac:DigitalSignatureAttachment>
    </cac:Signature>
    <cac:AccountingSupplierParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="6">{{ $company->number }}</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><![CDATA[{{ $company->trade_name }}]]></cbc:Name>
            </cac:PartyName>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA[{{ $company->name }}]]></cbc:RegistrationName>
                <cac:RegistrationAddress>
                    <cbc:ID>{{ $establishment->location_code }}</cbc:ID>
                    <cbc:AddressTypeCode>{{ $establishment->code }}</cbc:AddressTypeCode>
                    @if($establishment->urbanization)
                        <cbc:CitySubdivisionName>{{ $establishment->urbanization }}</cbc:CitySubdivisionName>
                    @endif
                    <cbc:CityName>{{ $establishment->province }}</cbc:CityName>
                    <cbc:CountrySubentity>{{ $establishment->department  }}</cbc:CountrySubentity>
                    <cbc:District>{{ $establishment->district }}</cbc:District>
                    <cac:AddressLine>
                        <cbc:Line><![CDATA[{{ $establishment->address }}]]></cbc:Line>
                    </cac:AddressLine>
                    <cac:Country>
                        <cbc:IdentificationCode>{{ $establishment->country_code }}</cbc:IdentificationCode>
                    </cac:Country>
                </cac:RegistrationAddress>
            </cac:PartyLegalEntity>
            @if($establishment->email || $establishment->telephone)
                <cac:Contact>
                    @if($establishment->telephone)
                        <cbc:Telephone>{{ $establishment->telephone }}</cbc:Telephone>
                    @endif
                    @if($establishment->email)
                        <cbc:ElectronicMail>{{ $establishment->email }}</cbc:ElectronicMail>
                    @endif
                </cac:Contact>
            @endif
        </cac:Party>
    </cac:AccountingSupplierParty>
    <cac:AccountingCustomerParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="{{ $customer->identity_document_type_code }}">{{ $customer->number }}</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA[{{ $customer->name }}]]></cbc:RegistrationName>
                @if($customer->address)
                    <cac:RegistrationAddress>
                        @if($customer->location_code)
                            <cbc:ID>{{ $customer->location_code }}</cbc:ID>
                        @endif
                        <cac:AddressLine>
                            <cbc:Line><![CDATA[{{ $customer->address }}]]></cbc:Line>
                        </cac:AddressLine>
                        <cac:Country>
                            <cbc:IdentificationCode>{{ $customer->country_code }}</cbc:IdentificationCode>
                        </cac:Country>
                    </cac:RegistrationAddress>
                @endif
            </cac:PartyLegalEntity>
            @if($customer->email || $customer->telephone)
                <cac:Contact>
                    @if($customer->telephone)
                        <cbc:Telephone>{{ $customer->telephone }}</cbc:Telephone>
                    @endif
                    @if($customer->email)
                        <cbc:ElectronicMail>{{ $customer->email }}</cbc:ElectronicMail>
                    @endif
                </cac:Contact>
            @endif
        </cac:Party>
    </cac:AccountingCustomerParty>
    <cac:TaxTotal>
        <cbc:TaxAmount currencyID="{{ $document->currency_type_code }}">{{ $document->total_taxes }}</cbc:TaxAmount>
        @if($document->total_isc > 0)
            <cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID="{{ $document->currency_type_code }}">{{ $document->total_base_isc }}</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID="{{ $document->currency_type_code }}">{{ $document->total_isc }}</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cac:TaxScheme>
                        <cbc:ID>2000</cbc:ID>
                        <cbc:Name>ISC</cbc:Name>
                        <cbc:TaxTypeCode>EXC</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        @endif
        @if($document->total_taxed > 0)
            <cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID="{{ $document->currency_type_code }}">{{ $document->total_taxed }}</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID="{{ $document->currency_type_code }}">{{ $document->total_igv }}</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cac:TaxScheme>
                        <cbc:ID>1000</cbc:ID>
                        <cbc:Name>IGV</cbc:Name>
                        <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        @endif
        @if($document->total_unaffected > 0)
            <cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID="{{ $document->currency_type_code }}">{{ $document->total_unaffected }}</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID="{{ $document->currency_type_code }}">0</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cac:TaxScheme>
                        <cbc:ID>9998</cbc:ID>
                        <cbc:Name>INA</cbc:Name>
                        <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        @endif
        @if($document->total_exonerated > 0)
            <cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID="{{ $document->currency_type_code }}">{{ $document->total_exonerated }}</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID="{{ $document->currency_type_code }}">0</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cac:TaxScheme>
                        <cbc:ID>9997</cbc:ID>
                        <cbc:Name>EXO</cbc:Name>
                        <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        @endif
        @if($invoice->total_free > 0)
            <cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID="{{ $document->currency_type_code }}">{{ $invoice->total_free }}</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID="{{ $document->currency_type_code }}">0</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cac:TaxScheme>
                        <cbc:ID>9996</cbc:ID>
                        <cbc:Name>GRA</cbc:Name>
                        <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        @endif
        @if($document->total_exportation > 0)
            <cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID="{{ $document->currency_type_code }}">{{ $document->total_exportation }}</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID="{{ $document->currency_type_code }}">0</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cac:TaxScheme>
                        <cbc:ID>9995</cbc:ID>
                        <cbc:Name>EXP</cbc:Name>
                        <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        @endif
        @if($document->total_other_taxes > 0)
            <cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID="{{ $document->currency_type_code }}">{{ $document->total_base_other_taxes }}</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID="{{ $document->currency_type_code }}">{{ $document->total_other_taxes }}</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cac:TaxScheme>
                        <cbc:ID>9999</cbc:ID>
                        <cbc:Name>OTROS</cbc:Name>
                        <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        @endif
    </cac:TaxTotal>
    <cac:LegalMonetaryTotal>
        @if($document->total_other_charges > 0)
            <cbc:ChargeTotalAmount currencyID="{{ $document->currency_type_code }}">{{ $document->total_other_charges }}</cbc:ChargeTotalAmount>
        @endif
        <cbc:PayableAmount currencyID="{{ $document->currency_type_code }}">{{ $document->total }}</cbc:PayableAmount>
    </cac:LegalMonetaryTotal>
    @foreach($details as $row)
    <cac:CreditNoteLine>
        <cbc:ID>{{ $loop->iteration }}</cbc:ID>
        <cbc:CreditedQuantity unitCode="{{ $row->unit_type_code }}">{{ $row->quantity }}</cbc:CreditedQuantity>
        <cbc:LineExtensionAmount currencyID="{{ $document->currency_type_code }}">{{ $row->total_value }}</cbc:LineExtensionAmount>
        <cac:PricingReference>
            <cac:AlternativeConditionPrice>
                <cbc:PriceAmount currencyID="{{ $document->currency_type_code }}">{{ $row->unit_price }}</cbc:PriceAmount>
                <cbc:PriceTypeCode>{{ $row->price_type_code }}</cbc:PriceTypeCode>
            </cac:AlternativeConditionPrice>
        </cac:PricingReference>
        <cac:TaxTotal>
            <cbc:TaxAmount currencyID="{{ $document->currency_type_code }}">{{ $row->total_taxes }}</cbc:TaxAmount>
            @if ($row->total_isc > 0)
                <cac:TaxSubtotal>
                    <cbc:TaxableAmount currencyID="{{ $document->currency_type_code }}">{{ $row->total_base_isc }}</cbc:TaxableAmount>
                    <cbc:TaxAmount currencyID="{{ $document->currency_type_code }}">{{ $row->total_isc}}</cbc:TaxAmount>
                    <cac:TaxCategory>
                        <cbc:Percent>{{ $row->percentage_isc }}</cbc:Percent>
                        <cbc:TierRange>{{ $row->system_isc_type_code }}</cbc:TierRange>
                        <cac:TaxScheme>
                            <cbc:ID>2000</cbc:ID>
                            <cbc:Name>ISC</cbc:Name>
                            <cbc:TaxTypeCode>EXC</cbc:TaxTypeCode>
                        </cac:TaxScheme>
                    </cac:TaxCategory>
                </cac:TaxSubtotal>
            @endif
            <cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID="{{ $document->currency_type_code }}">{{ $row->total_base_igv }}</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID="{{ $document->currency_type_code }}">{{ $row->total_igv }}</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cbc:Percent>{{ $row->percentage_igv }}</cbc:Percent>
                    <cbc:TaxExemptionReasonCode>{{ $row->affectation_igv_type_code }}</cbc:TaxExemptionReasonCode>
                    @php($affect = \App\Core\Helpers\TributeFunction::getByAffectation($row->affectation_igv_type_code))
                    <cac:TaxScheme>
                        <cbc:ID>{{ $affect['id'] }}</cbc:ID>
                        <cbc:Name>{{ $affect['name'] }}</cbc:Name>
                        <cbc:TaxTypeCode>{{ $affect['code'] }}</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
            @if ($row->total_other_taxes > 0)
                <cac:TaxSubtotal>
                    <cbc:TaxableAmount currencyID="{{ $document->currency_type_code }}">{{ $row->total_base_other_taxes }}</cbc:TaxableAmount>
                    <cbc:TaxAmount currencyID="{{ $document->currency_type_code }}">{{ $row->total_other_taxes }}</cbc:TaxAmount>
                    <cac:TaxCategory>
                        <cbc:Percent>{{ $row->percentage_other_taxes }}</cbc:Percent>
                        <cac:TaxScheme>
                            <cbc:ID>9999</cbc:ID>
                            <cbc:Name>OTROS</cbc:Name>
                            <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                        </cac:TaxScheme>
                    </cac:TaxCategory>
                </cac:TaxSubtotal>
            @endif
        </cac:TaxTotal>
        <cac:Item>
            <cbc:Description><![CDATA[{{ $row->item_description }}]]></cbc:Description>
            @if($row->internal_id)
                <cac:SellersItemIdentification>
                    <cbc:ID>{{ $row->internal_id }}</cbc:ID>
                </cac:SellersItemIdentification>
            @endif
            @if($row->item_code)
                <cac:CommodityClassification>
                    <cbc:ItemClassificationCode>{{ $row->item_code }}</cbc:ItemClassificationCode>
                </cac:CommodityClassification>
            @endif
            @if($row->item_code_gs1)
                <cac:StandardItemIdentification>
                    <cbc:ID>{{ $row->item_code_gs1 }}</cbc:ID>
                </cac:StandardItemIdentification>
            @endif
            @if($row->attributes)
                @foreach($row->attributes as $attribute)
                    <cac:AdditionalItemProperty>
                        <cbc:Name><![CDATA[{{ $attribute->name }}]]></cbc:Name>
                        <cbc:NameCode>{{ $attribute->code }}</cbc:NameCode>
                        @if($attribute->value)
                            <cbc:Value>{{ $attribute->value }}</cbc:Value>
                        @endif
                        @if($attribute->start_date || $attribute->end_date || $attribute->duration)
                            <cac:UsabilityPeriod>
                                @if($attribute->start_date)
                                    <cbc:StartDate>{{ $attribute->start_date }}</cbc:StartDate>
                                @endif
                                @if($attribute->end_date)
                                    <cbc:EndDate>{{ $attribute->end_date }}</cbc:EndDate>
                                @endif
                                @if($attribute->duration)
                                    <cbc:DurationMeasure unitCode="DAY">{{ $attribute->duration }}</cbc:DurationMeasure>
                                @endif
                            </cac:UsabilityPeriod>
                        @endif
                    </cac:AdditionalItemProperty>
                @endforeach
            @endif
        </cac:Item>
        <cac:Price>
            <cbc:PriceAmount currencyID="{{ $document->currency_type_code }}">{{ $row->unit_value }}</cbc:PriceAmount>
        </cac:Price>
    </cac:CreditNoteLine>
    @endforeach
</CreditNote>