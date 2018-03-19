<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{csrf_token()}}">
        <title>Laravel 5.5 ReactJS CRUD Example</title>
        <link href="{{asset('assets/dist/app.min.css')}}" rel="stylesheet" type="text/css">
        <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
        </script>
    </head>
    <body>
        <div id="example"></div>
        <script src="{{asset('js/app.js')}}" ></script>
    </body>
</html>