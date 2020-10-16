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
    "referrer_email": "abc@example.com", //required, must be a customer registered with this email
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
    "sender_email" : "sender@example.com", //required, must be a customer registered with this email
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
HTTP/1.1 500
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

```json
HTTP/1.1 500
Content-Type: application/json

{
    "status" : "failed",
    "message" : "coupon does not exists in record !",
}
```

