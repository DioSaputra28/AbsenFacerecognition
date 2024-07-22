@extends('layouts.app')
@section('title', 'Tambah Data Foto')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1>@yield('title')</h1>
                <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                    <ol class="breadcrumb pt-0">
                        <li class="breadcrumb-item">
                            <a href="#">Tambah Foto</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <div class="separator mb-5"></div>
            </div>
        </div>
        <form action="catat-aksi" method="POST" onsubmit="return confirm('Apakah data sudah benar?')">
            @csrf
            <button type="submit" class="btn btn-primary mb-2">Perbarui Dataset</button>
        <table class="table table-light  data-table  " id="absensiSiswa">
                <thead>
                    <tr>
                        <td>ID murid</td>
                        <td>Nama</td>
                        <td>Keterangan</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $dt)
                    <tr>
                        <td>{{ $dt->id }}</td>
                        <td>{{ $dt->nama }}</td>
                        <td><input class="form-control" type="file" id="formFile"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
@endsection
