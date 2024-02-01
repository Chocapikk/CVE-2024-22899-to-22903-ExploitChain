# CVE-2024-22899-to-22903-ExploitChain 🛠️🔓

This repository houses a full exploit chain for Authenticated Remote Code Execution (RCE) on VinChin version 7.2 and earlier, addressing vulnerabilities CVE-2024-22899 through CVE-2024-22903.

## Usage 🚀

To use the exploit script, execute:

```bash
$ python exploit.py --help
```

### Options 📋

- `-h`, `--help` - Show this help message and exit.
- `-u URL`, `--url URL` - URL of the login page.
- `-user USERNAME`, `--username USERNAME` - Username for login (optional if trying CVE-2024-22902 or CVE-2024-22901).
- `-p PASSWORD`, `--password PASSWORD` - Password for login (optional if trying CVE-2024-22902 or CVE-2024-22901).
- `-rip RSHELL_IP`, `--rshell_ip RSHELL_IP` - Reverse shell IP address.
- `-rport RSHELL_PORT`, `--rshell_port RSHELL_PORT` - Reverse shell port.
- `--payload_type {setNetworkCardInfo,syncNtpTime,deleteUpdateAPK,getVerifydiyResult}` - Type of payload to send.
- `--payload {nc,bash,python,perl,php}` - Type of payload to use (choices: 'nc', 'bash', 'python', 'perl', 'php'), default='nc'.

### Credential-Free Usage 🚫🔑

The script can attempt to exploit the system without provided credentials by leveraging:
- **CVE-2024-22902**: Attempts to connect via SSH as the `root` user.
- **CVE-2024-22901**: Tries default MySQL database credentials to rewrite the admin hash and authenticate.

### Browser Compatibility 🌐

The exploit requires Chrome 114 for compatibility with the WebDriver used in the exploit process. It is crucial to have Chrome installed on the host system. The exploit has been tested with Chrome version 114.

## Requirements 📦

Install the necessary dependencies from `requirements.txt`:

```bash
$ pip install -r requirements.txt
```

## Disclaimer ⚠️

This exploit is for educational and security research purposes only. Unauthorized testing on systems without explicit permission is illegal. The exploit could potentially damage the target instance; use it with caution. The author is not responsible for misuse or any damage that might occur.

## Further Reading 📖

For an in-depth writeup on the exploit and vulnerabilities, visit the [LeakIX blog post](https://blog.leakix.net/2024/01/vinchin-backup-rce-chain/) or consult the `./docs/index.md` file in this repository.
