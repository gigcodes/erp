# API Documentation
This API uses `POST` request to communicate and HTTP [response codes](https://en.wikipedia.org/wiki/List_of_HTTP_status_codes) to indenticate status and errors. All responses come in standard JSON. All requests must include a `content-type` of `application/json` and the body must be valid JSON.

## Response Codes 
### Response Codes
```
200: Success
400: Bad request
401: Unauthorized
404: Cannot be found
405: Method not allowed
422: Unprocessable Entity 
50X: Server Error
```

## Ticket Create For Store

**Request:**

```json
POST https://erp.amourint.com/api/ticket/create
Accept: application/json
Content-Type: application/json
{
    "name": "Pravin",
    "last_name": "Solanki", 
    "email": "abc@example.com",
    "type_of_inquiry" : "Orders",
    "order_no": "ORDER-NO",
    "country": "India",
    "subject": "Some subject name",
    "message": "Some message need to ask",
    "source_of_ticket": "site url",
    "phone_no" : "919876543210"
}
```
**Successful Response:**
```json
Content-Type: application/json
{
    "status": "success",
    "data": {
        "id": "T20201009155741"
    },
    "message": "Ticket #T20201009155741 created successfully"
}
```
**Failed Response:**
```json
HTTP/1.1 500
Content-Type: application/json

{
    "status": "error",
    "message": "Unable to create ticket"
}
``` 


## Order status for a customer 

### Response Codes
```
200: Success
400: Bad request
401: Unauthorized
404: Cannot be found
405: Method not allowed
422: Unprocessable Entity 
50X: Server Error
```

**Request:**

```json
GET https://erp.amourint.com/api/customer/order-details?email=solanki7492@gmail.com&website=www.veralusso.com
Accept: application/json
Content-Type: application/json
'Authorization: Bearer (Requested_website_token)'
```
**Successful Response:**
```json
Content-Type: application/json

{
    "message":"Orders Fetched successfully",
    "status":200,
    "data":[
        {
            "id":6,
            "customer_id":2001,
            "order_id":"OFF-1000005",
            "order_type":"offline",
            "order_date":"2019-11-03",
            "price":null,
            "awb":null,
            "client_name":"Pravin Solanki",
            "city":null,
            "contact_detail":"919016398686",
            "clothing_size":null,
            "shoe_size":null,
            "advance_detail":null,
            "advance_date":null,
            "balance_amount":null,
            "sales_person":null,
            "office_phone_number":null,
            "order_status":"product shipped to client",
            "order_status_id":9,
            "date_of_delivery":null,
            "estimated_delivery_date":null,
            "note_if_any":null,
            "payment_mode":null,
            "received_by":null,
            "assign_status":null,
            "user_id":49,
            "refund_answer":null,
            "refund_answer_date":null,
            "auto_messaged":0,
            "auto_messaged_date":null,
            "auto_emailed":0,
            "auto_emailed_date":null,
            "remark":null,
            "is_priority":0,
            "coupon_id":null,
            "deleted_at":null,
            "created_at":"2019-11-03 23:10:23",
            "updated_at":"2020-10-10 11:05:24",
            "whatsapp_number":null,
            "currency":null,
            "invoice_id":2,
            "status_histories":[
                {
                    "id":4,
                    "status":"proceed without advance",
                    "magento_status":null,
                    "message_text_tpl":null,
                    "created_at":null,
                    "updated_at":null
                },
                {
                    "id":9,
                    "status":"product shipped to client",
                    "magento_status":"shipped",
                    "message_text_tpl":null,
                    "created_at":null,
                    "updated_at":"2020-01-03 13:39:46"
                }
            ],
            "waybill_histories":[
                {
                    "id":2,
                    "waybill_id":1,
                    "comment":"on 9",
                    "dat":"datatata",
                    "location":"location",
                    "created_at":"2020-10-09 00:00:00",
                    "updated_at":"2020-10-09 00:00:00"
                },
                {
                    "id":1,
                    "waybill_id":1,
                    "comment":"commnebt",
                    "dat":"datatata",
                    "location":"location",
                    "created_at":"2020-10-10 00:00:00",
                    "updated_at":"2020-10-10 00:00:00"
                }
            ],
            "action":null,
            "waybill":
            {
                "id":1,
                "order_id":6,
                "awb":"somebillno",
                "box_length":1,
                "box_width":1,
                "box_height":1,
                "actual_weight":12,
                "volume_weight":null,
                "cost_of_shipment":null,
                "duty_cost":null,
                "package_slip":"",
                "pickup_date":null,
                "created_at":null,
                "updated_at":null,
                "customer_id":0,
                "dimension":1
            }
        },
    ]
}
```
**Failed Response:**
```json
HTTP/1.1 500
Content-Type: application/json

{
    "status": "400",
    "message": "Email is absent in your request"
}
```