<?php

namespace App\Http\Controllers;

use App\Jobs\QueueJob;
use Carbon\Carbon;

class QueueController extends Controller
{
    //
    public function queue()
    {
        $data = Carbon::now();
        $q = new QueueJob($data);
        $job = $q->onConnection('rabbitmq');
        dispatch($job);
        $q->delay(5);
        $job = $q->onConnection('rabbitmq')->onQueue('processing');
        dispatch($job);
    }
}
