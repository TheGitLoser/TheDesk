<table>
    <tr style="border-bottom-width: 20px;"><td style="width:80px">User</td><td>{{$user}}</td></tr>
    <tr><td style="width:80px">Count</td><td>
        @php
            print_r($count);
        @endphp
    </td></tr>
    <tr><td style="width:80px">Output</td><td>
        @php
            print_r($output);
        @endphp
    </td></tr>
    <tr><td style="width:80px">Session</td><td>
        @php
            print_r($session);
        @endphp
    </td></tr>
</table>