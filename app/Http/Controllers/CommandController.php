<?php


namespace App\Http\Controllers;

use Artisan;
use Exception;

class CommandController extends Controller
{
    public function runCrawling()
    {
        try {
            Artisan::call('crawl-vendors');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Command "crawl-vendors" run successfully');
    }

    public function runSync()
    {
        try {
            Artisan::call('sync-products');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Command "sync-products" run successfully');
    }
}
