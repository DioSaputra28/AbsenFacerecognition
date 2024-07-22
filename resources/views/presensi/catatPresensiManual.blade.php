@extends('layouts.app')
@section('title', 'Catat Presensi')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1>@yield('title')</h1>
                <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                    <ol class="breadcrumb pt-0">
                        <li class="breadcrumb-item">
                            <a href="#">Presensi</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <div class="separator mb-5"></div>
            </div>
        </div>
        <div>

            <ul class="list-group list-group-horizontal">
                <li class="card card-body mr-1 list-group-item">Kelas : {{ $kelas }}</li>
                <li class="card card-body mr-1 list-group-item">Mapel : {{ $mapel }}</li>
                <li class="card card-body mr-1 list-group-item">Semester : {{ $semester }}</li>
            </ul>
        </div>
        <div>


        </div>


        <div class="mt-4 separator"></div>
         <div>
            <form action="catat-aksi" method="POST" onsubmit="return confirm('Apakah data sudah benar?')">
                @csrf
                @php
                $no = 1;
                $inputsNo = 0;
                @endphp
            <table class="table table-light  data-table  " id="absensiSiswa">
                    <thead>
                        <tr>
                            <td></td>
                            <td>Nama</td>
                            <td>Keterangan</td>
                            <td>Note</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $dt)
                        <tr>
                            <td>{{ $no++ }}</td>
                            {{ $inputsNo++ }}
                            <td>{{ $dt->nama }}</td>
                            <input type="text" value="{{ $dt->id }}" name="inputs[{{ $inputsNo }}][user_id]" hidden>
                            <input type="text" value="{{ $dt->kelas_id }}" name="inputs[{{ $inputsNo }}][kelas_id]" hidden>
                            <input type="text" value="{{ $semester }}" name="inputs[{{ $inputsNo }}][semester_id]" hidden>
                            <td class="d-flex flex-row ">
                                <div class="px-2">
                                    <input type="radio" name="inputs[{{ $inputsNo }}][keterangan]" value="3">
                                    <label for="sakit">Sakit</label>
                                </div>
                                <div class="px-2">
                                    <input type="radio" name="inputs[{{ $inputsNo }}][keterangan]" value="2">
                                    <label for="izin">Izin</label>
                                </div>
                                <div class="px-2">
                                    <input type="radio" name="inputs[{{ $inputsNo }}][keterangan]" value="4">
                                    <label for="alpha">Alpha</label>
                                </div>
                            </td>
                            <td>
                                <input type="text"  name="inputs[{{ $inputsNo }}][note]">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <button type="submit" class="btn btn-primary">Catat</button>
            </form>


        </div>
    @endsection
    @section('css')
        <link href="{{ asset('vendor/bootstrap4-editable/css/bootstrap-editable.css') }}" rel="stylesheet">
    @endsection
    @section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js" integrity="sha384-+YQ4JLhjyBLPDQt//I+STsc9iw4uQqACwlvpslubQzn4u2UU2UFM80nGisd026JF" crossorigin="anonymous"></script>
    <script src="./plugin/sweetalert/sweetalert.min.js"></script>
    <script src="./plugin/webcamjs/webcam.min.js"></script>
        <script src="{{ asset('vendor/bootstrap4-editable/js/bootstrap-editable.js') }}"></script>
        <script src="{{ asset('js/vendor/glide.min.js') }}"></script>
        <script>
            $(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }


});


})



        </script>
    @endsection
