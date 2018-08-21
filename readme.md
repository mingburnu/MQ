> mv /var/lib/dpkg/info /var/lib/dpkg/info.bak<br>
> mkdir /var/lib/dpkg/info<br>
> apt-get update<br>
>apt upgrade<br>
>apt-get install erlang<br>
>echo 'deb http://www.rabbitmq.com/debian/ testing main' | tee /etc/apt/sources.list.d/rabbitmq.list<br>
>wget -O- https://www.rabbitmq.com/rabbitmq-release-signing-key.asc | apt-key add -<br>
>apt-get update<br>
>apt-get install rabbitmq-server<br>
> vim /etc/rabbitmq/rabbitmq.config<br>

    [
           {rabbit, [{loopback_users, []}]}
    ].

>systemctl restart rabbitmq-server<br>
>systemctl is-active rabbitmq-server<br>
>rabbitmq-plugins enable rabbitmq_management<br>
>composer require vladimir-yuldashev/laravel-queue-rabbitmq<br>

[edit config/app.php](https://github.com/mingburnu/MQ/blob/master/config/app.php)<br>

    'providers' => [
        .............
        VladimirYuldashev\LaravelQueueRabbitMQ\LaravelQueueRabbitMQServiceProvider::class,       
    ]

[edit config/queue.php](https://github.com/mingburnu/MQ/blob/master/config/queue.php)<br>

    'connections' => [
                'rabbitmq' => [
                    'driver'                => 'rabbitmq',
        
                    'host'                  => env('RABBITMQ_HOST', '127.0.0.1'),
                    'port'                  => env('RABBITMQ_PORT', 5672),
        
                    'vhost'                 => env('RABBITMQ_VHOST', '/'),
                    'login'                 => env('RABBITMQ_LOGIN', 'guest'),
                    'password'              => env('RABBITMQ_PASSWORD', 'guest'),
        
                    'queue'                 => env('RABBITMQ_QUEUE'), // name of the default queue,
        
                    'exchange_declare'      => env('RABBITMQ_EXCHANGE_DECLARE', true), // create the exchange if not exists
                    'queue_declare_bind'    => env('RABBITMQ_QUEUE_DECLARE_BIND', true), // create the queue if not exists and bind to the exchange
        
                    'queue_params'          => [
                        'passive'           => env('RABBITMQ_QUEUE_PASSIVE', false),
                        'durable'           => env('RABBITMQ_QUEUE_DURABLE', true),
                        'exclusive'         => env('RABBITMQ_QUEUE_EXCLUSIVE', false),
                        'auto_delete'       => env('RABBITMQ_QUEUE_AUTODELETE', false),
                    ],
        
                    'exchange_params' => [
                        'name'        => env('RABBITMQ_EXCHANGE_NAME', null),
                        'type'        => env('RABBITMQ_EXCHANGE_TYPE', 'direct'), // more info at http://www.rabbitmq.com/tutorials/amqp-concepts.html
                        'passive'     => env('RABBITMQ_EXCHANGE_PASSIVE', false),
                        'durable'     => env('RABBITMQ_EXCHANGE_DURABLE', true), // the exchange will survive server restarts
                        'auto_delete' => env('RABBITMQ_EXCHANGE_AUTODELETE', false),
                    ],
        
                ],
        
        .................    
    ]

>php artisan make:job QueueJob<br>
>php artisan make:controller QueueController<br>

[edit app/Jobs/QueueJob.php](https://github.com/mingburnu/MQ/blob/master/app/Jobs/QueueJob.php)<br>
[edit app/Http/Controllers/QueueController.php](https://github.com/mingburnu/MQ/blob/master/app/Http/Controllers/QueueController.php)<br>
[edit routes/web.php](https://github.com/mingburnu/MQ/blob/master/routes/web.php)<br>

    Route::get('/queue',['as'=>'queue.job','uses'=>'QueueController@queue']);

>php artisan queue:work rabbitmq --queue=processing,queue_name<br>

[http://RABBITMQ_HOST:15672]()<br>

### REFERECE
[Queues](https://laravel.com/docs/5.4/queues)<br>
[How to Install Erlang on Debian 9 & Debian 8](https://tecadmin.net/install-erlang-debian/)<br>
[Debian/Ubuntu 安裝 RabbitMQ](https://andyyou.github.io/2017/09/07/rabbitmq-ubuntu/)<br>
[laravel5 rabbitmq使用测试](https://www.phpsong.com/3163.html)<br>