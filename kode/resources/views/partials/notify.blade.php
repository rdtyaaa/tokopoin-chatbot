

@if ($errors->any())
    @foreach($errors->all() as $message)
        <script>
            "use strict";
            @php
              $message = translate($message);
            @endphp
            toaster("{{$message}}",'danger')
        </script>
    @endforeach
@endif

@if (Session::has('success') )
    <script>
        "use strict";
        toaster("{{Session::get('success')}}",'success')
    </script>
@endif

@if (Session::has('error'))
    <script>
        "use strict";
        toaster("{{Session::get('error')}}",'danger')
    </script>
    @php
      session()->forget('error');
    @endphp
@endif
