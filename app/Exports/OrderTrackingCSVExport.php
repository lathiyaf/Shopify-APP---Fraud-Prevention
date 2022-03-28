<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderTrackingCSVExport implements FromCollection, WithHeadings
{

    public $data = '';
    public $risks = '';
    public $transactions = '';

    public function __construct($data, $risks, $transactions)
    {
        \Log::info('-----------------------generateCSV-------------------------');
        $this->data = $data;
        $this->risks = $risks;
        $this->transactions = $transactions;
    }

    public function collection()
    {
        $data = $this->data;
        $d = [];
        $bill_add = [];
        $ship_add = [];
        if( !empty( $data->billing_address ) ){
            $bill_add = $this->getAddress($data->billing_address);
        }
        if( !empty( $data->shipping_address ) ){
            $ship_add = $this->getAddress($data->shipping_address);
        }

        $d['App Id'] = $data->app_id;
        $d['Id'] = $data->id;
        $d['Billing Address'] = implode(',',$bill_add);
        $d['Cart Token'] = $data->cart_token;
        $d['customer'] = (@$data->customer) ? json_encode($data->customer) : '';
        $d['Customer Locale'] = $data->customer_locale;
        $d['Financial Status'] = $data->financial_status;
        $d['Line Items'] = (!empty($data->line_items)) ? json_encode($data->line_items) : '';
        $d['Order Number'] = $data->order_number;
        $d['Processed At'] = $data->processed_at;
        $d['Shipping Address'] = implode(',',$ship_add);
        $d['Source Name'] = $data->source_name;
        $d['Total Price'] = $data->total_price;
        $d['User Id'] = @($data->customer) ? $data->customer->id : '';
        $d['Client Details'] = (!empty($data->client_details)) ? json_encode($data->client_details) : '';
        $d['Discount Codes'] = (!empty($data->discount_codes)) ? json_encode($data->discount_codes) : '';
        $d['Gateway'] = $data->gateway;
        $d['Landing Site'] = $data->landing_site;
        $d['Processing Method'] = $data->processing_method;
        $d['Referring Sited'] = $data->referring_site;
        $d['Token'] = $data->token;
        $d['Transaction Properties'] = (!empty($this->transactions)) ? json_encode($this->transactions) : '';
        $d['Order Risk Properties'] = (!empty($this->risks)) ? json_encode($this->risks) : '';

        $arr = [];
        array_push($arr, $d);
        return collect($arr);
    }

    public function getAddress($add){
        $arr = [];
        ( $add->name ) ? array_push($arr, $add->name) : '';
        ( $add->address1 ) ? array_push($arr, $add->address1) : '';
        ( $add->address2 ) ? array_push($arr, $add->address2) : '';
        ( $add->city ) ? array_push($arr, $add->city) : '';
        ( $add->city ) ? array_push($arr, $add->city) : '';
        ( $add->province ) ? array_push($arr, $add->province) : '';
        ( $add->country ) ? array_push($arr, $add->country) : '';
        ( $add->company ) ? array_push($arr, $add->company) : '';
        ( $add->phone ) ? array_push($arr, $add->phone) : '';
        ( $add->latitude ) ? array_push($arr, $add->latitude) : '';
        ( $add->longitude ) ? array_push($arr, $add->longitude) : '';
        ( $add->country_code ) ? array_push($arr, $add->country_code) : '';
        ( $add->province_code ) ? array_push($arr, $add->province_code) : '';

        return $arr;
    }

    public function headings(): array
    {

        $data = [
            'App Id',
            'Id',
            'Billing Address',
            'Cart Token',
            'Customer',
            'Customer Locale',
            'Financial Status',
            'Line Items',
            'Order Number',
            'Processed At',
            'Shipping Address',
            'Source Name',
            'Total Price',
            'User Id',
            'Client Details',
            'Discount Codes',
            'Gateway',
            'Landing Site',
            'Processing Method',
            'Referring Sited',
            'Token',
            'Transaction Properties',
            'Order Risk Properties'
        ];
        return $data;
    }
}
