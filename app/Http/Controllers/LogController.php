<?php

namespace App\Http\Controllers;

use Code16\Sharp\Http\Composers\MenuViewComposer;
use Code16\Sharp\Http\SharpProtectedController;
use Exception;
use Symfony\Component\Process\Process;
use Log;

/**
 * Class LogController
 * @package App\Http\Controllers
 * @property MenuViewComposer $menuViewComposer
 */
class LogController extends SharpProtectedController
{
    public function __construct(MenuViewComposer $menuViewComposer)
    {
        $this->menuViewComposer = $menuViewComposer;
    }

    public function index()
    {
        $logsJsons = explode(PHP_EOL, file_get_contents(config('logging.channels.single.path')));
        $logsJsons = array_reverse($logsJsons);

        $logItems = [];
        foreach ($logsJsons as $logsJson) {
            if ($logsJson) {
                $logItems[] = json_decode($logsJson);
            }
        }
        $view = view('logs', ['logItems' => $logItems]);
        $this->menuViewComposer->compose($view);

        return $view;
    }

}
