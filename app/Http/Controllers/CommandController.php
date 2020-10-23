<?php

namespace App\Http\Controllers;

use Exception;
use Symfony\Component\Process\Process;
use Log;

class CommandController extends Controller
{
    public function runCrawling()
    {
        try {
            $this->runProcess('crawl-vendors');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Command "crawl-vendors" run successfully');
    }

    public function runSync()
    {
        try {
            $this->runProcess('sync-products');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }

//        return back()->with('success', 'Command "sync-products" run successfully');
    }

    private function runProcess($command)
    {
        Log::info('started runProcess in controller'. $command);

        $phpPath = config('filesystems.php_path');
        $cwdPath = config('filesystems.cwd_path');
        $process = new Process($phpPath . ' artisan ' . $command . ' > /dev/null 2>&1 &', $cwdPath);
        $process->start();
        $process->wait(function ($type, $buffer) {
            if (Process::ERR === $type) {
                echo 'ERR > '.$buffer;
            } else {
                echo 'OUT > '.$buffer;
            }
        });
    }
}
