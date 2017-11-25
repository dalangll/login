<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>
尊敬的 {{ $user->username}} 用户，
<br>
<a href="{{ url("api/gel").'?uid='.$user->id.'&uuid='.$uuid}}">请点击此处激活账户</a>

</body>
</html>

