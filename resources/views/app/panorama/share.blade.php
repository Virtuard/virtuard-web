<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>360 - Virtual Tour</title>
    @include('partials.ipanorama.ipanorama-css')
    <style>
        #mypanorama {
            width: 100%;
            height: 100vh;
        }
    </style>
</head>

<body>
    @include('partials.ipanorama.ipanorama-init')

    @include('partials.ipanorama.ipanorama-js')
    @include('partials.ipanorama.ipanorama-preview-js')
</body>

</html>
