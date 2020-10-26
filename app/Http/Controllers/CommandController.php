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
            $this->runProcess('crawl-vendors', '');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Command "crawl-vendors" run successfully');
    }

    public function runSync(string $productId = '', string $siteClientId = '')
    {
        try {
            $params = '';
            if ($siteClientId) {
                $params = ' ' . $productId . ' ' . $siteClientId;
            } elseif ($productId) {
                $params = ' ' . $productId;
            }
            $this->runProcess('sync-products', $params);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Command "sync-products" run successfully');
    }

    private function runProcess($command, string $params)
    {
        Log::info('started runProcess in controller'. $command);

        $phpPath = config('filesystems.php_path');
        $cwdPath = config('filesystems.cwd_path');
        $process = new Process(
            $phpPath . ' artisan ' . $command . $params . ' > /dev/null 2>&1 &',
            $cwdPath,
            null,
            null,
            7200 // 2 hours
        );
        $process->run();
    }
}
