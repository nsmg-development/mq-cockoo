# 설치

### required
- php7.4
- mysql
- redis

### download
```
git clone git@github.com:nsmg-development/mq-cockoo.git
cd mq-cockoo
```

### dependency install
```
composer install
```

### configuration
- .env 
  - APP_ENV : local : production
  - APP_URL : real
  - DB...

### migrate
- OAuth와 내역 저장용
```
php artisan migrate
```

### valet
```
valet park
valet link
```


# 클라이언트 준비

### FCM 준비 -> 뻐꾸기 시스템에 전달
- API Auth Json 파일
  - 🏠 프로젝트 개요 -> ⚙️ 프로젝트 설정 -> 클라우드메시징 탭 -> ⋮ google cloud console에서 api 관리
  - 구글 api 및 서비스 -> OAuth 2.0 클라이언트 ID -> 웹클라이언트 다운로드 -> json 파일
    - 없으면 생성
- project id
  - 🏠 프로젝트 개요 -> ⚙️ 프로젝트 설정 -> 일반 탭 -> 내 프로젝트 섹션 -> 프로젝트 id
  
### 클라이언트 생성
```
php artisan passport:client --client
>> name?
PROJECT-ID ClientCredentials Grant Client 
```

### FCM Auth JSON 위치
- base-path / authjson / PROJECT-ID.json


# Login - get Access Token
- https://app-url.com/api/v1/token
- GET
- header
  - ```
    "Accept" : "application/json"
    ```
- body
  - ```
    {
        "grant_type" : "client_credentials",
        "client_id" : 4,
        "client_secret" : "X5UP2Offg9yaAjSFkf8pwIyrungpQtCJG0EIorc5",
        "scope" : ""
    }
    ```
  - 위 client 생성 command로 생성된 id, secret 삽입
  - grant type과 scope는 고정

# Request - push 발송
- https://app-url.com/api/v1/push/sendDefault
- POST
  - header
  - ```
    "Accept" : "application/json"
    "Authorization" : "Bearer 로그인으로 획득한 토큰"
    ```
- body
  - ```
    {
      "tokens": [,
          "cYGiHUnoTgq0iNQuzIY9mB:APA91bFJGCJ8bhuA_SHI_hdqqYzhpszahsZD7x6AVca3QeNWVT14Yvf_LHh7csZP58cv9TTS5NxCcvO9X4Ap0hppk7aJgpXAsY8wGypu17NJflsLo4nHKsiVlf93Afry-ESQxXp11111",
          "wawevrwfwefUHKnkjnOIJOm4324KJNLKszcYGiHUnoTgq0iNQuzIY9mB:APfdsfeahsZD7x6AVca3QeNWVT14Yvf_LHh7csZP58cv9TTS5NxCcvO9X4Ap0hppiVlf93Afry-1k7aJgpXAsY8wGypu17NJflsLo4nHKs",
          // ... 
          "cYGiHUnoTgq0iNQuzIY9mB:APA91bFXAsY8wGypu17NJflsLo4nHKsiVlf93Afry-ESQxXp1111JGCJ8bhuA_SHI_hdqqYzhpszahsZD7x6AVca3QeNWVT14Yvf_LHh7csZP58cv9TTS5NxCcvO9X4Ap0hppJKNlk7h",
          "Y8wGypu17NJflsLo4nHKsiVlf93Afry-ESQxXp11111cYGiHUnoTgq0iNQuzIY9mB:APA91bFJGCJ8bhuA_SHI_hdqqYzhpszahsZD7x6AVca3QeNWVT14Yvf_LHh7csZP58cv9TTS5NxCcvO9X4Ap0hppk7aJgpXAs"
      ],
      "title": "여기에 푸시 제목을",
      "body": "여기에 푸시 내용을 너무 길지 않게 작성"
    }
    ```
  - 받는 사람 FCM 토큰 배열, 제목, 내용 JSON 전송


