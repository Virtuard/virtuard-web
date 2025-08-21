<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>360 - Virtual Tour</title>
    <style>
        iframe {
            width: 100%;
            height: 100vh;
        }
    </style>
</head>

<body>
    <iframe src="{{ $embedUrl }}" width="100%" height="100vh" frameborder="0" allowfullscreen loading="lazy"></iframe>
</body>

</html>
