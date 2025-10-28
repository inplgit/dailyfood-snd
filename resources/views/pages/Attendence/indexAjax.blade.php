@php 
$i = 1;
@endphp
@foreach ($Attendence as $item)
    <tr>
        <td>{{$i}}</td>
        <td>{{date('d-m-y',strtotime(trim(str_replace('/', '-',$item->date))))}}</td>
        <td>{{date('H:i',strtotime(trim(str_replace('/', '-',$item->date))))}}</td>
        <td>@if($item->attendence_status == 1) P @else A @endif</td>
       
    </tr>
    @php 
    $i++;
    @endphp
@endforeach