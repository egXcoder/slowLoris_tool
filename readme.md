## This is a php developed tool for slowLoris attack

        usage example 
        php slowLoris.php -h example.com -p 80 -s 200
        -h .. is the hostname
        -p .. is the port
        -s .. is the sockets numbers  

        this tool is for test purpose only

##     Description
Slowloris is a type of denial of service attack tool which allows a single machine to take down another machine's web server with minimal bandwidth and side effects on unrelated services and ports.

Slowloris tries to keep many connections to the target web server open and hold them open as long as possible. It accomplishes this by opening connections to the target web server and sending a partial request. Periodically, it will send subsequent HTTP headers, adding to, but never completing, the request. Affected servers will keep these connections open, filling their maximum concurrent connection pool, eventually denying additional connection attempts from clients.

##     Tool Steps
1. Tool start making lots of HTTP requests.
   
2. Tool send headers periodically (every ~15 seconds) to keep the connections open.
   
3. Tool close connections every Two minutes and start again not to get band by the server
   
4. If the server closes a connection, Tool create a new one keep doing the same thing.

## How to Mitigate and Prevent a Slowloris DDoS Attack
- Increase the maximum number of clients the Web server will allow

- Limit the number of connections a single IP address is allowed to attempt

- Place restrictions on the minimum transfer speed a connection is allowed

- Constrain the amount of time a client is permitted to stay connected.

In the case of Apache Web servers, several modules can be employed to prevent damage from a Slowloris DDoS attack. These modules include:
- Mod_limitipconn
- Mod_qos
- Mod_evasive
- Mod security
- Mod_noloris
- Mod_antiloris