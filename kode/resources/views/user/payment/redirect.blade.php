<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{site_settings('site_name')}} - {{translate($title)}}</title>
</head>

<body>
<form action="{{$data->url}}" method="{{$data->method}}" id="auto_submit">
    @foreach($data->val as $k=> $v)
        <input type="hidden" name="{{$k}}" value="{{$v}}"/>
    @endforeach
</form>
<script>
    "use strict";
    document.getElementById("auto_submit").submit();
</script>
</body>

</html>
