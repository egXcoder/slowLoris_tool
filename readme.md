## This is a php developed tool for slowLoris attack


        usage example 
        php slowLoris.php -h example.com -p 80


        this tool is for test purpose only




 **1. We start making lots of HTTP requests.**
   
**2. We send headers periodically (every ~15 seconds) to keep the connections open.**
   
**3. we close connections every two minutes and start again not to get band by the server** 
   
**4. If the server closes a connection, we create a new one keep doing the same thing.**