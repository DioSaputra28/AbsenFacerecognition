<?php

namespace Database\Seeders; // Pastikan namespace ini benar

use App\Model\Semester;
use Illuminate\Database\Seeder;
use App\User; // Sesuaikan dengan namespace model Anda
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Menyisipkan data pengguna
        User::insert([
            [
                'id'            =>  1,
                'nama'          => 'Administrator',
                'username'      => 'admin',
                'email'         => 'admin@gmail.com',
                'password'      => bcrypt('admin'),
                'email_verified_at' => Carbon::now(),
                'status'        => '1',
                'telepon'       => '081261865875',
                'jk'            => 'L',
                'tempat_lahir'  => 'Padang Japang',
                'tgl_lahir'     => '2002-04-02',
                'avatar'        => 'pp.jpg',
                'role'          => 'guru',
            ]
        ]);
    }
}
