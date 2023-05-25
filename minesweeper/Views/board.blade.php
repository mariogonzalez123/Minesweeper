@extends('master')
@section('content')
<table>
    <caption>MINESWEEPER</caption>
    @for ($i = 0; $i< BOARD; $i++) 
    <tr>
        @for ($j = 0; $j < BOARD; $j++) 
        <td data-x="{{$i}}" data-y="{{$j}}" data-imgpath="{{PATH_PIC}}" id="{{ $i . $j }}"></td>
        @endfor
    </tr>
    @endfor
</table>

@endsection