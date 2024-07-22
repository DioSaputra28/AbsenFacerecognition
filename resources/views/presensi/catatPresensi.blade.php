@extends('layouts.app')
@section('title', 'Catat Presensi')
@section('content')
        <div>
            <ul class="list-group list-group-horizontal">
                @isset($kelas)
                    <li class="card card-body mr-1 list-group-item">Kelas : {{ $kelas }}</li>
                @endisset
                @isset($mapel)
                    <li class="card card-body mr-1 list-group-item">Mapel : {{ $mapel }}</li>
                @endisset
                @isset($semester)
                    <li class="card card-body mr-1 list-group-item">Semester : {{ $semester }}</li>
                @endisset
            </ul>
        </div>
        <div>
            <button class="btn btn-primary text-white mt-3" id="accesscamera" data-toggle="modal" data-target="#photoModal">
                Ambil Gambar
            </button>
            <form action="catat-absensi" method="POST">
                @csrf
                <input type="text" name="kelas" value="{{ $kelas }}" hidden>
                <input type="text" name="semester" value="{{ $semester }}" hidden>
                <input type="text" value="{{ $mapel }}" name="mapel" hidden>
                <input type="hidden" name="kenali_wajah" value="1">
                <button type="submit" class="btn btn-primary text-white mt-2">Kenali Wajah</button>
            </form>
            <!-- Modal -->
            <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Ambil Gambar</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div>
                                <div id="my_camera" class="d-block mx-auto rounded overflow-hidden"></div>
                            </div>
                            <div id="results" class="d-none"></div>
                            <form method="post" id="photoForm">
                                <input type="hidden" id="photoStore" name="photoStore" value="">
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary mx-auto text-white" id="takephoto">Ambil Gambar</button>
                            <button type="button" class="btn btn-primary mx-auto text-white d-none" id="retakephoto">Ambil ulang</button>
                            <button type="submit" class="btn btn-primary mx-auto text-white d-none" id="uploadphoto" form="photoForm" data-url="{{ route('unggah-image') }}">Unggah</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tampilkan output jika ada -->
        @isset($namaUser)
            <div class="alert alert-success mt-3">
                Berhasil absen: {{ $namaUser }}
            </div>
        @endisset
        
        @if(isset($output) && !empty($output))
            <!-- Form yang dijalankan ketika output memiliki nilai -->
            <form id="autoSubmitForm" action="{{ route('absensi.aksiCatatDeteksi') }}" method="POST">
                @csrf
                <input type="text" value="{{ $output }}" name="user_id" hidden>  
                <input type="text" value="{{ $kelas_id }}" name="kelas_id" hidden>
                <input type="text" value="{{ $semester }}" name="semester_id" hidden>
                <input type="text" name="keterangan" value="1" hidden>
                <input type="text" value="{{ $mapel }}" name="mapel" hidden>
            </form>
        
            <script>
                document.getElementById('autoSubmitForm').submit();
            </script>
        @endif        
        <div class="mt-4 separator"></div>
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
            Webcam.set({
                width: 320,
                height: 240,
                image_format: 'jpeg',
                jpeg_quality: 90
            });

            $('#accesscamera').on('click', function() {
                Webcam.reset();
                Webcam.on('error', function() {
                    $('#photoModal').modal('hide');
                    swal({
                        title: 'Warning',
                        text: 'Please give permission to access your webcam',
                        icon: 'warning'
                    }).then(() => {
                            // Ensure modal is hidden and backdrop is removed
                            $('#photoModal').modal('hide'); // Close modal again to be sure
                            $('body').removeClass('modal-open'); // Remove modal-open class from body
                            $('.modal-backdrop').remove(); // Remove the backdrop div
                        });
                });
                Webcam.attach('#my_camera');
            });

            $('#takephoto').on('click', take_snapshot);

            $('#retakephoto').on('click', function() {
                $('#my_camera').addClass('d-block');
                $('#my_camera').removeClass('d-none');

                $('#results').addClass('d-none');

                $('#takephoto').addClass('d-block');
                $('#takephoto').removeClass('d-none');

                $('#retakephoto').addClass('d-none');
                $('#retakephoto').removeClass('d-block');

                $('#uploadphoto').addClass('d-none');
                $('#uploadphoto').removeClass('d-block');
            });

            $('#photoForm').on('submit', function(e) {
            e.preventDefault();

            var url = $('#uploadphoto').data('url');

            $.ajax({
                url: url,
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                processData: false,
                success: function(data) {
                    if (data == 'success') {
                        Webcam.reset();

                        $('#my_camera').addClass('d-block');
                        $('#my_camera').removeClass('d-none');

                        $('#results').addClass('d-none');

                        $('#takephoto').addClass('d-block');
                        $('#takephoto').removeClass('d-none');

                        $('#retakephoto').addClass('d-none');
                        $('#retakephoto').removeClass('d-block');

                        $('#uploadphoto').addClass('d-none');
                        $('#uploadphoto').removeClass('d-block');

                        $('#photoModal').modal('hide');

                        swal({
                            title: 'Success',
                            text: 'Photo uploaded successfully',
                            icon: 'success',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            timer: 2000
                        }).then(() => {
                            // Ensure modal is hidden and backdrop is removed
                            $('#photoModal').modal('hide'); // Close modal again to be sure
                            $('body').removeClass('modal-open'); // Remove modal-open class from body
                            $('.modal-backdrop').remove(); // Remove the backdrop div
                        });
                    } else {
                        swal({
                            title: 'Error',
                            text: 'Something went wrong',
                            icon: 'error'
                        });
                    }
                }
            });
        });


        })

        function take_snapshot()
        {
            //take snapshot and get image data
            Webcam.snap(function(data_uri) {
                //display result image
                $('#results').html('<img src="' + data_uri + '" class="d-block mx-auto rounded"/>');

                var raw_image_data = data_uri.replace(/^data\:image\/\w+\;base64\,/, '');
                $('#photoStore').val(raw_image_data);
            });

            $('#my_camera').removeClass('d-block');
            $('#my_camera').addClass('d-none');

            $('#results').removeClass('d-none');

            $('#takephoto').removeClass('d-block');
            $('#takephoto').addClass('d-none');

            $('#retakephoto').removeClass('d-none');
            $('#retakephoto').addClass('d-block');

            $('#uploadphoto').removeClass('d-none');
            $('#uploadphoto').addClass('d-block');
        }

        </script>
    @endsection
