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
    "phone_no" : "919876543210",
    "sku":"7768484226295",
    "amount":"415.00",
    "notify_on":"phone"
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
Content-Type: application/json

{
    "status": "error",
    "message": "Unable to create ticket"
}
```

## Friend Referral API

//referrer => person refering other person
//referee=> person being reffered by referrer.
**Request:**

```json
POST https://erp.amourint.com/api/friend/referral/create
Accept: application/json
Content-Type: application/json
{
    "referrer_first_name": "Pravin", //required, length maxminum 30
    "referrer_last_name": "Solanki", //length maxminum 30
    "referrer_email": "abc@example.com",
    "referrer_phone" : "7777777777", //length maxminum 20
    "referee_first_name": "karamjit",//required,length maxminum 30
    "referee_last_name": "Singh", //required,length maxminum 30
    "referee_email": "Singh.karamjit1689@gmail.com", //required,email,length maxminum 20
    "referee_phone": "9999999999", //length maxminum 20
    "website": "WWW.SOLOLUXURY.COM",//required, must be a website in store websites
}
```

**Successful Response:**

```json
Content-Type: application/json
{
    "status": "success",
    "message": "refferal created successfully",
    "referrer_code": "o4kx9LzcrbYCMFj",
    "referrer_email": "abc@example.com",
    "referee_email": "Singh.karamjit1689@gmail.com"
}
```

**Failed Response:**

```json
HTTP/1.1 500
Content-Type: application/json

{
    "status" : "failed",
    "message" : "Unable to create coupon",
}
```

## Gift Card API

**Request:**

```json
POST https://erp.amourint.com/api/giftcards/add
Accept: application/json
Content-Type: application/json
{   "sender_name" : "sender", //required, length maxminum 30
    "sender_email" : "sender@example.com",
    "receiver_name" : "reciever", //required, length maxminum 30
    "receiver_email" : "reciever@example.com", //required, email
    "gift_card_coupon_code" : "A1A22A111FFF333", //required, unique, upto 50 chars
    "gift_card_description" : "dummy description", //required, length maxminum 1000
    "gift_card_amount" : "100", //required, integer
    "gift_card_message" : "test message", //length maxminum 200
    "expiry_date" : "2020-10-16", //required, date after yesterday
    "website"  : "WWW.SOLOLUXURY.COM", //required, must be a website in store websites
}
```

**Successful Response:**

```json
Content-Type: application/json
{
    "status": "success",
    "message": "gift card added successfully",
}
```

**Failed Response:**

```json
Content-Type: application/json

{
    "status" : "failed",
    "message" : "Unable to add gift card at the moment. Please try later !",
}
```

## Gift Card Amount Check API

**Request:**

```json
GET https://erp.amourint.com/api/giftcards/check-giftcard-coupon-amount
Accept: application/json
Content-Type: application/json
{   "coupon_code" : "A1A22A111FFF333", //required, length maxminum 30, existing in gift_cards
}
```

**Successful Response:**
```json
Content-Type: application/json
{
    "status": "success",
    "message": "gift card amount fetched successfully",
    "data": {
        "gift_card_amount": 120,
        "gift_card_coupon_code":"A1A22A111FFF333",
        "updated_at": "2020-10-16 11:13:41"
    }
}
```

**Failed Response:**

```
    "status" : "failed",
    "message" : "coupon does not exists in record !",
}
```

## Order status for a customer 

**Request:**

```json
GET https://erp.amourint.com/api/customer/order-details?email=solanki7492@gmail.com&website=www.veralusso.com&order_no=000000001
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
                    "status":"advance recieved",
                    "created_at":"2020-10-10 11:05:24"
                },
                {
                    "status":"proceed without advance",
                    "created_at":"2020-10-10 11:00:24"
                }
            ],
            "action":null
        },
    ]
}
```
**Failed Response:**

```json
Content-Type: application/json
{
    "status": "400",
    "message": "Email is absent in your request"
}
```
## Buyback | Return | Exchange | Refund Check Product API

**Request:**

```json
GET https://erp.amourint.com/api/orders/products
Accept: application/json
Content-Type: application/json
{
    "customer_email" : "firasath90@gmail.com",
    "website" : "www.brands-labels.com",
    "order_id" : "000000012"
}
```

**Successful Response:**
```json
{
    "status": "success",
    "orders": {
        "1": [
            {
                "product_name": "Dr. Osborne Harber",
                "product_price": "0",
                "sku": "6493033100078",
                "product_id": 296561,
                "order_id": "1"
            },
            {
                "product_name": "Dr. Osborne Harber",
                "product_price": "0",
                "sku": "6493033100079",
                "product_id": 296560,
                "order_id": "1"
            }
        ],
        "2": [
            {
                "product_name": "Dr. Osborne Harber",
                "product_price": "0",
                "sku": "6493033100080",
                "product_id": 296569,
                "order_id": "2"
            }
        ]
    }
}
```

**Failed Response:**
```json
Content-Type: application/json
{
    "status": "failed",
    "message": "Customer not found with this email !"
}
```
## Create Buyback | Return | Exchange | Refund request API

**Request:**

```json
POST https://erp.amourint.com/api/return-exchange-buyback/create
Accept: application/json
Content-Type: application/json
{
    "customer_email" : "firasath90@gmail.com",
    "website" : "www.brands-labels.com",
    "order_id" : "000000012",
    "product_sku" : "Test01",
    "type":"exchange"
}
```
For type expected value will be "return","exchange","buyback","refund"

**Successful Response:**
```json
Content-Type: application/json
{
    "status": "success",
    "message": "Exchange request created successfully"
}
```

**Failed Response:**
```json
Content-Type: application/json
{
    "status": "failed",
    "message": "Unable to create Exchange request!"
}
```

## Price comparison API

**Request:**

```json
POST https://erp.amourint.com/api/price_comparision/details
Accept: application/json
Content-Type: application/json
{
    "sku" : "565655VT0406512FW2019",
    "country" : "IN"
}
```
**Successful Response:**
```json
Content-Type: application/json
{
    "status": "sucess",
    "results": [
        {
            "name": "N/A",
            "currency": "USD",
            "price": "631.99",
            "country_code": "in"
        }
    ]
}
```

**Failed Response:**
```json
Content-Type: application/json
{
    "status": "failed",
    "message": "No Category Found"
}
```


## Affilates Api

**Request:**

```json
POST https://erp.amourint.com/api/affiliate/add
Accept: application/json
Content-Type: application/json
{
    "website" : "www.brands-labels.com", //existing website
    "first_name":"xyz", //optional
    "last_name":"ABC", //optional
    "phone":"9999999999", //optional, string
    "emailaddress":"example@domain.com", //optional
    "website_name":"sololuxury", //optional, string
    "url":"url", //optional, string
    "unique_visitors_per_month":"unique_visitors_per_month", //optional, string
    "page_views_per_month":"page_views_per_month", //optional, string
    "street_address":"address", //optional, string
    "city":"Texas", //optional, string
    "postcode":"111111", //optional, string
    "country":"United States Of America", //optional, string

}
```

**Successful Response:**
```json
HTTP/1.1 200
Content-Type: application/json
{
    "status": "success",
    "message": "affiliate added successfully !"
}
```

**Failed Response:**
```json
HTTP/1.1 500
Content-Type: application/json
{
    "status": "failed",
    "message": "unable to add affiliate !"
}
```


## Magneto Customer Reference Store API

**Request:**

```json
POST https://erp.amourint.com/api/magento/customer-reference
Accept: application/json
Content-Type: application/json
{
    "name" : "Wahiduzzaman", //required
    "email" : "wahidlaskar05@gmail.com", //required
    "phone": "918638973610", //optional
    "website": "WWW.SOLOLUXURY.COM",//required, must be a website in store websites
    "dob": "2020-10-23", //optional
    "wedding_anniversery": "2020-10-23"//optional
}
```
**Successful Response:**
```json
Content-Type: application/json
{
     "status": "200",
    "message": "Saved SucessFully"
}
```

**Failed Response:**
```json
Content-Type: application/json
{
    "status": "403",
    "message": "Email is required"
}
```

```
## Tickets API

**Request:**

```json
POST https://erp.amourint.com/api/ticket/send
Accept: application/json
Content-Type: application/json
{
    "website" : "live_chat",
    "email" : "bardam.yus@gmail.com", //optional if ticket_id is set 
    "ticket_id":"PWTCR", //optional if email is set
    "per_page":"10" //optional, default is 15
}
```
**Successful Response:**
```json
{
    "status": "success",
    "tickets": {
        "current_page": 1,
        "data": [
            {
                "id": 3,
                "customer_id": 3008,
                "name": "Bardambek Yusupov",
                "last_name": null,
                "email": "bardam.yus@gmail.com",
                "ticket_id": "PWTCR",
                "subject": "Task test",
                "message": "Message: Hi",
                "assigned_to": null,
                "source_of_ticket": "live_chat",
                "status_id": 1,
                "date": "2020-08-25 01:26:31",
                "created_at": "2020-09-11 11:48:23",
                "updated_at": "2020-09-11 12:08:33",
                "type_of_inquiry": null,
                "country": null,
                "phone_no": null,
                "order_no": null,
                "status": "open"
            },
            {
                "id": 4,
                "customer_id": 3008,
                "name": "Bardambek Yusupov",
                "last_name": null,
                "email": "bardam.yus@gmail.com",
                "ticket_id": "J7XPB",
                "subject": "About new Products",
                "message": "Message: Hi",
                "assigned_to": null,
                "source_of_ticket": "live_chat",
                "status_id": 1,
                "date": "2020-08-25 01:25:30",
                "created_at": "2020-09-11 11:48:23",
                "updated_at": "2020-09-11 11:48:23",
                "type_of_inquiry": null,
                "country": null,
                "phone_no": null,
                "order_no": null,
                "status": "open"
            }
        ],
        "first_page_url": "http://127.0.0.1:8000/api/ticket/send?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "http://127.0.0.1:8000/api/ticket/send?page=1",
        "next_page_url": null,
        "path": "http://127.0.0.1:8000/api/ticket/send",
        "per_page": 15,
        "prev_page_url": null,
        "to": 2,
        "total": 2
    }
}
```

**Failed Response:**
```json
Content-Type: application/json
{
    "status": "failed",
    "message": "Tickets not found for customer !"
}
```
## Push Notifications API

**Request:**

```json
POST https://erp.amourint.com/api/notification/create
Accept: application/json
Content-Type: application/json
{
    "website" : "WWW.SOLOLUXURY.COM", //required , exists in store websites
    "token" : "sdsad2e232dsdsd", //required 
}
```
**Successful Response:**
```json
HTTP/1.1 200
{
    "status": "success",
    "message": "Notification created successfully !"
}
```

**Failed Response:**
```json
HTTP/1.1 500
Content-Type: application/json
{
    "status": "failed",
    "message": "Unable to create notifications !"
}

```

## Influencer Api

**Request:**

```json
POST https://erp.amourint.com/api/influencer/add
Accept: application/json
Content-Type: application/json
{
    "website" : "www.veralusso.com", //existing website
    "first_name":"Solo", //optional
    "last_name":"Luxury", //optional
    "phone":"9999999999", //optional, string
    "emailaddress":"solo@luxury.com", //optional
    "facebook":"example@domain.com", //optional
    "facebook_followers":"12M", //optional
    "instagram":"example@domain.com", //optional
    "instagram_followers":"10M", //optional
    "twitter":"example@domain.com", //optional
    "twitter_followers":"12M", //optional
    "youtube":"example@domain.com", //optional
    "youtube_followers":"25K", //optional
    "linkedin":"example@domain.com", //optional
    "linkedin_followers":"10K", //optional
    "pinterest":"example@domain.com", //optional
    "pinterest_followers":"5K", //optional
    "worked_on":"example@domain.com", //optional
    "website_name":"sololuxury", //optional, string
    "url":"url", //optional, string
    "country":"United States Of America", //optional, string
    "type" :"influencer"
}
```

**Successful Response:**
```json
HTTP/1.1 200
Content-Type: application/json
{
    "status": "success",
    "message": "influencer added successfully !"
}
```

**Failed Response:**
```json
HTTP/1.1 500
Content-Type: application/json
{
    "status": "failed",
    "message": "unable to add influencer !"
}
```

## Store data into the laravel logs

**Request:**

```json
POST http://erp.theluxuryunlimited.com/api/laravel-logs/save
Accept: application/json
Content-Type: application/json
{
    "message": "error-message",
    "website": "Farfetch",  
    "url": "https:\/\/www.farfetch.com\/mt\/shopping\/kids\/young-versace-crystal-logo-t-shirt-item-15339323.aspx?q=YC000346YA00019A1008"
}
```

**Successful Response:**

```json
Content-Type: application/json
{
    "status": "success",
    "message": "Log data Saved"
}
```

**Failed Response:**

```json
Content-Type: application/json

{
    "status": "failed",
    "message": "{message}"
}
```
