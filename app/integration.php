<?php

class Integration{
    private $name;
    private $email;
    private $phone;
    private $price;
    private $errors = [
        400 => 'Bad request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not found',
        500 => 'Internal server error',
        502 => 'Bad gateway',
        503 => 'Service unavailable',
    ];
    private $subdomain = 'savelovaeu';
    private $access_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImE0M2E2N2RjNWZjYzA5NmJlNGU1NmU5ZjQ3YTFjNjczY2NiYTAyMTFlZDExNmQwYzdhZDBmZjY1ZjdhOWM4M2Y4NGQ1YjJkZDkwNWRkZGFjIn0.eyJhdWQiOiIwNzc0NmJkMC02ZTY4LTRiNzEtYjE3ZC1lM2UzM2M5MGVmNTQiLCJqdGkiOiJhNDNhNjdkYzVmY2MwOTZiZTRlNTZlOWY0N2ExYzY3M2NjYmEwMjExZWQxMTZkMGM3YWQwZmY2NWY3YTljODNmODRkNWIyZGQ5MDVkZGRhYyIsImlhdCI6MTcwODI3MjQ2MiwibmJmIjoxNzA4MjcyNDYyLCJleHAiOjE3MTAzNzQ0MDAsInN1YiI6IjEwNjkwNDEwIiwiZ3JhbnRfdHlwZSI6IiIsImFjY291bnRfaWQiOjMxNTc3NjI2LCJiYXNlX2RvbWFpbiI6ImFtb2NybS5ydSIsInZlcnNpb24iOjIsInNjb3BlcyI6WyJjcm0iLCJmaWxlcyIsImZpbGVzX2RlbGV0ZSIsIm5vdGlmaWNhdGlvbnMiLCJwdXNoX25vdGlmaWNhdGlvbnMiXSwiaGFzaF91dWlkIjoiYmVkNDcyMGUtZTQxMC00MWRhLTkyYmYtZTE3ZGRjYTcxOGEyIn0.mq2tRlG6E6A0G8YlGbYAdHQuOG8TEoUOzSQPCooPTZbaqNwddJBWh-IBZG51vKrGCa_68i2NSIU4eoScSMpR4Qn07Xf0uswKReGGvg-Tu3CREm1Fn-Lr7mKAGRu5NX8RwjslVB1ivuQpjsKK0lATNFVcVgb1ka2HH4ppSOkILQvUspvCXSDskgnkYB7DhlP24TKc5eUJH_xMLcN207zduy6B2xLDkK2ubuELtOWXkmItoKBlwPrlwZYqA9DjaXnyYAuaNBZe2JVobZ6Xyl91boLDPzYaj5cjs0uh6DRSXR3nFY3l63C46xM67JFIagJq1mfs3DJgCGvegw2jIcEZsg';
    private $contact_id;
    private $is_time = false;

    public function __construct($name, $email, $phone, $price, $startTime, $stopTime) {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->price = $price;
        $sess_time = abs(strtotime($startTime) - strtotime($stopTime));
        if ($sess_time >= 30) {
            $this->is_time = true;
        }
    }

    public function amoCRM_add_contact() {
        $link = 'https://' . $this->subdomain . '.amocrm.ru/api/v4/contacts';
        $headers = [
            'Authorization: Bearer ' . $this->access_token
        ];
        $data = [
            [
                "name" => $this->name,
                "custom_fields_values" => [
                    [
                        "field_code" => 'PHONE',
                        "values" => [
                            [
                                "value" => $this->phone
                            ]
                        ]
                    ],
                    [
                        "field_code" => 'EMAIL',
                        "values" => [
                            [
                                "value" => $this->email
                            ]
                        ]
                    ],
                    [
                        "field_id" => 379319,
                        "values" => [
                            [
                                "value" => $this->is_time
                            ]
                        ]
                    ],
                ],
            ]
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_COOKIEFILE, 'amo/cookie.txt');
        curl_setopt($curl, CURLOPT_COOKIEJAR, 'amo/cookie.txt');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        $out = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $code = (int) $code;
        if ($code < 200 || $code > 204) die( "Error $code. " . (isset($this->errors[$code]) ? $this->errors[$code] : 'Undefined error') );


        $Response = json_decode($out, true);
        $Response = $Response['_embedded']['contacts'];
        foreach ($Response as $v)
            if (is_array($v))
                $this->contact_id = $v['id'];

    }

    public function amoCRM_add_deal() {
        $this->amoCRM_add_contact();
        $link = 'https://' . $this->subdomain . '.amocrm.ru/api/v4/leads';
        $headers = [
            'Authorization: Bearer ' . $this->access_token
        ];
        $data = [
            [
                "price" => (int) $this->price,
                "_embedded" => [
                    "contacts" => [
                        [
                            "id" => $this->contact_id
                        ]
                    ],
                ]
            ]
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_COOKIEFILE, 'amo/cookie.txt');
        curl_setopt($curl, CURLOPT_COOKIEJAR, 'amo/cookie.txt');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        $out = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $code = (int) $code;
        if ($code < 200 || $code > 204) die( "Error $code. " . (isset($this->errors[$code]) ? $this->errors[$code] : 'Undefined error') );

    }
}
?>
