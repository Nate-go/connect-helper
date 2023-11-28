<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invite</title>
</head>

<body style="display: flex; align-items: center; justify-content: center;">
    <div
        style="display: flex; align-items: center; justify-content: center;">
        <div
            style="max-width: 36rem; padding: 20px; text-align: center; color: #374151; background-color: #ffffff; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border-radius: 0.75rem;">
            <h1 style="font-size: 1.5rem; margin-bottom: 1rem;">Hello everyone</h1>
            <div style="overflow: hidden;">
                <img src="https://res.cloudinary.com/dsrtzowwc/image/upload/v1701067076/logo-web_rboojw.png" style="object-fit: cover; width: 100%; height: 100%;">
            </div>
            <div style="margin-top: 1.5rem;">
                <p>You are invited to join <strong>{{$type}}</strong> for <strong>{{$title}}</strong></p>
                <p>From: {{$from}} to {{$to}}</p>
                @if (!$online)
                    <p>Place: {{$place}}</p>
                @endif
            </div>
            @if ($online)
                <div style="margin-top: 1.5rem;">
                    <a href="{{$place}}"
                        style="display: inline-block; padding: 8px 16px; color: #ffffff; background-color: #3b82f6; border-radius: 4px; text-decoration: none;">
                        Click here to join</a>
                </div>
            @endif
        </div>
    </div>
</body>

</html>