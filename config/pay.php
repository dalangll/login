<?php

return [
    'alipay' => [
        // 支付宝分配的 APPID
        'app_id' => '2016090900469591',

        // 支付宝异步通知地址
        'notify_url' => 'https://openapi.alipaydev.com/gateway.do',

        // 支付成功后同步通知地址
        'return_url' => 'http://dalang.s1.natapp.cc/api/verfly',

        // 阿里公共密钥，验证签名时使用
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAugACuWM5q/mvql0KJTPdzW2eVjyej6LR2WgOf4z9KXrOWB+Lsi19sb1yYM3odAw9mqUO5KbobfSB2CKihXQPzpzdDoIBrrDqnk1XXH3zXVZLXoYQBF3X65w8yy7Mc3RU8Kt7NG+nugJi3q2EhJ2oSH0zGqdnYStLVOFCTeSYga0htHt2PHIj0KLxN7Wnv3a1DUjwoHzyiX8CgXccU8h/zUOfQf+Mmc32Zlm+rRRJVoUNxN7kqp5NceRRvl7nnJeZVijgy9LkZ+sTP1VSUyTbJ3pwa2mgerIKIiPH+zWBPYe1Lus246A2/XIHEZipkmo33XH8PvY9RggHQKr7eTmvxQIDAQAB',

        // 自己的私钥，签名时使用
        'private_key' => 'MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQC6AAK5Yzmr+a+qXQolM93NbZ5WPJ6PotHZaA5/jP0pes5YH4uyLX2xvXJgzeh0DD2apQ7kpuht9IHYIqKFdA/OnN0OggGusOqeTVdcffNdVktehhAEXdfrnDzLLsxzdFTwq3s0b6e6AmLerYSEnahIfTMap2dhK0tU4UJN5JiBrSG0e3Y8ciPQovE3tae/drUNSPCgfPKJfwKBdxxTyH/NQ59B/4yZzfZmWb6tFElWhQ3E3uSqnk1x5FG+Xuecl5lWKODL0uRn6xM/VVJTJNsnenBraaB6sgoiI8f7NYE9h7Uu6zbjoDb9cgcRmKmSajfdcfw+9j1GCAdAqvt5Oa/FAgMBAAECggEAEy1PGpgdaRrMPs9q7V0EoFqN+aw0WPDbnAO7gvEhZPZxceqGt68zB3FV6p8wOU7NxzT/bRm8WyHbhVJE2WeW/aF0tTs4Oy+ocPveZv3hap0sFinSWvDmAzfPmM9RaxHydAHqKQTeETKPyQ+w2w96O0cVTsk1wnH5SV4UPP21r6Dh4KdJXszpykO6+tb1vZXTOEbuKcdsrkQRVHTWgGKSJwxBHb25AdVwBqQ1PTNabzBB167r61LjPbW/GjwC0Pla3J7ar9mPrm4sY3ojfPqqxrI1oB/ysrIKzT8NMx3DIsz3Qgmsi9EunuEO9t1kek457DMQJhvEwIctO5ybRxyhIQKBgQDxOPlcFSC+YpvjAJ36zp2t2wcqGPRhsW9qrn+efOsSVTVHe8tzL5Jx2idRcZMo+HZFbzf7fJRNIAp3QK2BgWUugsQd9kdfYMtnaFocEui7HT5WqZku1Ufy0+E14E2W/wfipuZNUFZfoP87Ax0ydLz/chR8yjmvFvtBoIFJkCiILQKBgQDFZP9RDEtGKqU5FM6vFGbiEERVyjERcWXM69oX1w04XckBDUjyS31VCOkyQ+5A95+8U6cnOpY8dgh/wNLjgvZCcIUuLZ89CyUr8p5aY+sOmS58tE4LernciFiHL/jqo7qBrlzwVqRpX7gFm/mwynNvQ1uDuw6pOT59AUW8Oeus+QKBgAnqc6WrYKsy1oGgVt4mfCjXZhX+7JzPdGsIPUrAi9E3G3eSySw0+mofs5oPdyMqA4KbLcvytQ3ukT+RsxAtx4D/8xvgMdLj4bige4PH6zU14IgjdJ6OOeTrfMiALfBVD3frsG/hK4vGzHcai6q1TceaYuk2TuS5px5tgKMLf/8JAoGAMEdUfuEv4/h4Mq7ZIk8uHqp13Dm4qJiAmo8w51XMwPWHPP+f0MsP22Vzi7y7oB4wbmJTZq/YoO8Jgx2JNuYIDxXwLOwxnz+Dlu0fN+JqUka67Ps4f2xD2yFp7Z6ZFeJ3slIQHyRB0Bf+LTkJ5+iMNTFXR6/PlSoQE4jlInVUJBkCgYBVoWV/S3B6KQ+dNG3cyUGTab0o4OcKKrcLbfPAyupB5P8zwrZY8kIIDBLFCAZw9JKSs4N7JI14MGuBLDpqg2gnzh1QWERfZrwDjSokkoNGym3O9zkiso1qbzB39s/xbbgBtcIJohdKpxSzOaEYCdUaUKRinb7+J7QHrdVBpz2fDg==',
    ],

    'wechat' => [
        // 公众号APPID
        'app_id' => '',

        // 小程序APPID
        'miniapp_id' => '',

        // APP 引用的 appid
        'appid' => '',

        // 微信支付分配的微信商户号
        'mch_id' => '',

        // 微信支付异步通知地址
        'notify_url' => '',

        // 微信支付签名秘钥
        'key' => '',

        // 客户端证书路径，退款时需要用到。请填写绝对路径，linux 请确保权限问题。pem 格式。
        'cert_client' => '',

        // 客户端秘钥路径，退款时需要用到。请填写绝对路径，linux 请确保权限问题。pem 格式。
        'cert_key' => '',
    ],
];
