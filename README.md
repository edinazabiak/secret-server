# Titkos szerver feladat

A feladat egy titkos szerver megvalósítása, ahol titkokat lehet tárolni, létrehozni, illetve lekérni limitált időn belül. A részletes feladat leírás az alábbi [github repóban](https://github.com/ngabesz-wse/secret-server-task) érhető el. 

## Program használata: 
A program használatához érdemes valamilyen API platformot, mint például HTTPie, Insomnia vagy Postman. 

Az API élőben az alábbi címen elérhető: [https://www.zabiakedina.hu/secret-server/](https://www.zabiakedina.hu/secret-server/)

### Minden titok lekérése: 
Input: 
```
http get localhost/secret-server/
```
Output:
```
HTTP/1.1 200 OK
Connection: Keep-Alive
Content-Length: 1625
Content-Type: application/json; charset=UTF-8
Date: Fri, 23 Sep 2022 02:43:21 GMT
Keep-Alive: timeout=5, max=100
Server: Apache/2.4.53 (Win64) OpenSSL/1.1.1n PHP/8.1.6
X-Powered-By: PHP/8.1.6

[
    {
        "createdAt": "2022-09-23 02:34:06",
        "expiresAt": "2022-09-23 02:39:06",
        "hashCode": "92f5f1f4905812ada9889e0efdf12cc7",
        "id": 5,
        "remainingViews": 10,
        "secretText": "Titok 1"
    },
    {
        "createdAt": "2022-09-23 02:34:36",
        "expiresAt": "2022-09-23 02:39:36",
        "hashCode": "60b0b57ef0e05e1b3978afa627497abd",
        "id": 6,
        "remainingViews": 10,
        "secretText": "Titok 1"
    },
(...)
```

### Egy titok létrehozása: 
Input: 
```
http post localhost/secret-server/ secret="Lorem ipsum..." expireAfter:=10 expireAfterViews:=3
```
Output:
```
HTTP/1.1 201 Created
Connection: Keep-Alive
Content-Length: 78
Content-Type: application/json; charset=UTF-8
Date: Fri, 23 Sep 2022 02:45:01 GMT
Keep-Alive: timeout=5, max=100
Server: Apache/2.4.53 (Win64) OpenSSL/1.1.1n PHP/8.1.6
X-Powered-By: PHP/8.1.6

{
    "description": "Successful operation",
    "id": "8b047ab90ce2098fab645ea6d1b0f320"
}
```

### Egy titok lekérése: 
Input: 
```
http get localhost/secret-server/8b047ab90ce2098fab645ea6d1b0f320
```
Output:
```
HTTP/1.1 200 OK
Connection: Keep-Alive
Content-Length: 172
Content-Type: application/json; charset=UTF-8
Date: Fri, 23 Sep 2022 02:46:58 GMT
Keep-Alive: timeout=5, max=100
Server: Apache/2.4.53 (Win64) OpenSSL/1.1.1n PHP/8.1.6
X-Powered-By: PHP/8.1.6

{
    "createdAt": "2022-09-23 04:45:01",
    "expiresAt": "2022-09-23 04:55:01",
    "hashCode": "8b047ab90ce2098fab645ea6d1b0f320",
    "id": 16,
    "remainingViews": 2,
    "secretText": "Lorem ipsum..."
}
```