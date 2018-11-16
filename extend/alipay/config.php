<?php
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2016080600182076",

		//商户私钥
		'merchant_private_key' => "MIIEpAIBAAKCAQEAvsy3Q52yj3uGMb1vD3puVZIQN+Mz0I0rV9u8wcP+RzEsBzcMi25e57vOSMGZW4q9v+JILuIPo5Z9dY5B5TFoZ1YJaijqVopfc0F0r+CkkWlmKO+GO0km3baviyaxWoWzyOWi6EZywca03ge+JlBc2cIwzO2IgaXHmJe95c+NZi55MC48avMiqoqP4W4elXsOjZX9r9+EaO5mWrImCpzO1ostIWWDo3o5vXACflJhdeClrjDYymcsd01ytolvPjeTtu8Pj5MeQGP9pq3s477ui7bqrfknFObRdXLyFzqK0oT5nen4BUY/Xn/fe1C81BVhlYLrn9ChdBxubArbZrv0xQIDAQABAoIBAQCGq1W66uaqcWRRXUSzZxXTXgoGeF4NGGb8V0NOfHjQZPuPiCJZk6h25c7++W619yJg+mU6eiLRsxmQTL1j+NCZB1gTzxc/x+EBJ7UlomhlnkAWKqHS3JoLKbOTgtbxbqdr5/FpArhi306v3EUxI821gWQScKHF6G4N3SSSvUEvWer4X5WyvdQK8aHt32vGLFa4ccpsOPHoBLD5p5G03dTXZKgIAvfkkA2nz/SHSHPbi/pBuwoJigJ05v0qnPJeQs2Hj5gg+Fv6QGLz0Ece73cAoSSp3DLZZFriDpukqnK1o2cUlJRZwoeje++6LIRTb4LZJbkyQFUv3TBZPOII23HhAoGBAO/AIAwAwH7STgQ4sOrvNvEGu/vR9Uw9ylTfJjGMTA6FXrM9x681ggWabddT19YwVHGgroQ7qvDoah7hvWIPuUIOLAnl+pH3h0GG8FtH//0oTIMJleqEWQ3XYrXy8hNWAiilAExqE0QWQYXm1VtMKrajLZ5eX6IClpjmKg0ra8NJAoGBAMu7QJyY9EUJssUYyNY4wiBYTL69xQoOlKLM3sSuImLyW35AgnxBE1v3w//91tJ3AjOAPpjp/AmfZ8jzQ0QR0yfHeI9TM+sLhgtj/88kn1Y+rwdNEuQpfMchIo+GhZogBAKv33zUF6aoPQEAwlbHLhzYJx8nss3BZEXd6TMZ6qmdAoGAYfT04xKXk/mnaLAlEQiGQTqIKh6iA/GnLsAyIcJ5/ODfTYrrgJnzMwji4jzejiQXDIojY+HhOScs3MtNDrjv755MvgqaM3OYbtyMbi9f13bUtCGBgF1s6WvcaA0IfYABx8uK8K3Si1owkV48RF49gXvwkqlql45JK9W2zsSz9gkCgYEAs1dMAk4WwWtPJnex2/os0UJJlKMC/95j84f7FhHgDVAhQFtTQkQa4ZuSuxdd8vl2Q5XCBOP8zQ+40clqzlIOmfba5vgsPN1xCzflKRio9sfJVdkAJz9qKvS9MrENjOZgAWmGh6FAjKGOygI8ER3XNucEUUWl/cAfH+2/6g8HZnUCgYBPDG3y4NnYsUzLj4oB2MK80yItma+auhyev/I9TFXNf6yBtXhygklJXMFmZr8VsS6UpDFs98uuRW69lU65u7fJCrOO+tYNwSEzCDqIxz/GHI3SUnE63GUUxzMXfEan4ckcuY1Zwz/hytqKltIzrH3nl5uXVahIS1OglkYy7RtLkQ==",
		
		//异步通知地址(支付宝通过'post'方式向商家发起跳转)
		'notify_url' => "http://www.php68.com/home/shop/notify_url",
		
		//同步跳转(支付宝通过'get'方式向商家发起跳转)
		'return_url' => "http://www.php68.com/home/shop/return_url",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnhaYgWK0tlTD1dsd0+q/awwT/QTSRTTfU3XOp1M/MG8bG3rWs1dVk1vlxsrIegbFqEsJrpzIHOQ0ZPm5YgDpzqiR5w+zV+qc0wXrobdU7oOvkYePT8jYt+EzIaUEVjuKiELmgLoTJxg5+/wGIB8HYdg7CshURvQvyqfcNdVgTUztHdb5Fsy/EUddTZBfEJgr+rvyFXCQWaHQdYkb0u/wLno+YZeI40OjSvb7Pw195z7k5Z9s1gBi7eMSeTYjx+FGXOrNd5jBDOG2tWwvzYVsCiQfutZJ6sO3jeWs+l62JKWOWUYsUqyIHMJ2UDNAM4OIHmQx99DceiKQz8RY/2674QIDAQAB",
);

















