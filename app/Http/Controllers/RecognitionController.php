<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use App\Model\Absen;
use App\Model\Kelas;
use App\Model\Mapel;
use App\Model\Semester;
use App\Model\AbsenPending;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Routing\Controller;

class RecognitionController extends Controller
{
    public function training()
    {
        $pythonScriptPath = base_path('public/Python/inputFoto.py'); 
        $pythonExecutable = 'python3';
        $command = escapeshellcmd("$pythonExecutable $pythonScriptPath");
        $output = shell_exec($command);
        return redirect()->back()->with('message', 'Berhasil update model');
    }
    public function uploadPhoto(Request $request)
    {
        if(isset($_POST['photoStore'])) {
            $encoded_data = $_POST['photoStore'];
            $binary_data = base64_decode($encoded_data);
        
            $photoname = uniqid().'.jpeg';
            $pathTrue = public_path('Python/image/');
            $result = file_put_contents( $pathTrue.$photoname, $binary_data);
        
            if($result) {
                echo 'success';
            } else {
                echo die('Could not save image! check file permission.');
            }
        }
    }
}
