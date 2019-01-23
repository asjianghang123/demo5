<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\Mongs\Users;

class CryptAllPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CryptAllPassword';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CryptAllPassword';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {   
        $rows = Users::get(['id','pwd'])->toArray();
        foreach ($rows as $row) {
            $id = $row['id'];
            $pwd = $row['pwd'];
            if (strlen($pwd) < 20) {
                Users::where('id', $id)->update(
                [
                    'pwd' => Crypt::encrypt($pwd)
                ]);
            }
        }
    }

}