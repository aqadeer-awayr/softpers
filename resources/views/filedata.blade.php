<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <title>Laravel File Upload</title>
    <style>
        .container {
            max-width: 500px;
        }

        dl,
        ol,
        ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }
    </style>
</head>

<body>


    <div class="container">

        <table class="table">
            <thead>
                <tr>
                    @foreach($attributes as $attribute)
                    <th>{{$attribute}}</th>
                    @endforeach

                </tr>
            </thead>
            <tbody>
                @foreach($data as $data)
                <tr>
                    @foreach($attributes as $attribute)
                    <td>{{$data->$attribute}}</td>
                    @endforeach

                </tr>
                @endforeach


            </tbody>
        </table>
    </div>
</body>

</html>