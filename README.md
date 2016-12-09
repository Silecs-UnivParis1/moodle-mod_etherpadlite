# About mod-etherpadlite

This is a module which integrates etherpad-lite as course activities in Moodle 2.5 and later.

It was initially developped, up to v2.7.0, by the *Humboldt-Universität zu Berlin*.

## Features

- Add / View / Delete Pads
- Users have the same name & writing color in all pads
- Moodle Import / Export support
- optional guest allowance
- It supports etherpad-lite servers, which can only be accessed through the API (access only through Moodle)
- It can check the HTTPS certificate of the ep-lite server for full security (man in the middle attacks)

- (v2.8.0) Integrate with the global search of Moodle 3.1.
  The title, description, and content of each pad instance are searchable according to the user's permissions.

## Requirements

- Moodle 2.5 or later
- etherpad-lite 1.3.0 or later, on the same 2nd-level-domain as your moodle server

### Installing etherpad-lite

You need an etherpad-lite server, running on at least the same 2nd-level-domain as your moodle server.
For instance `etherpad.example.com` and `moodle.example.com`.

When you want the server to only be accessible via Moodle,
I recommend to install `ep_remove_embed` over the ep-lite admin interface.
This removes the embed link.  
*To access the admin area, uncomment the user section in settings.json*

This is a quick guide for Debian stable (at least) Jessie and recent Ubuntu.
You'll find all information you need to install the server
on the project page <https://github.com/ether/etherpad-lite>.

1. `sudo apt install nodejs npm git`
2. Get the source code of etherpad-lite
	```
	cd ~/opt/
	git clone git clone git://github.com/ether/etherpad-lite.git
	cd etherpad-lite
	```
3. Modify `settings.json`, see below for recommended settings.
4. Run etherpad-lite with `./bin/run.sh`.

At the first run, it will download dependencies, so it may take a while.

A few other important settings:

- `"ip": "127.0.0.1",` unless you really want you etherpad server to be publicly available.
- `"requireSession" : true,` so that only Moodle users can access the pads.
- `"editOnly" : true,` so that only Moodle etherpadlite activities can create pads.

This is a basic guide for a local install,
more work is needed to put etherpad-lite into production mode.

## Installing this module into Moodle

0. Install Moodle
1. Copy this repository to the moodle subfolder: **mod/etherpadlite**
2. Open your admin/index.php page and follow the instructions.

### Configuration

The are 3 *mandatory* settings. Without them, the module will probably not work.

1. **(mandatory)** Server URL from your etherpadlite server.
   Make sure that your moodle server can access this URL, and *don't forget to include a trailing slash!*
2. **(mandatory)** ApiKey: this is stored in the file: `APIKEY.txt` on your etherpadlite server
3. Padname: this is optional and maybe just for debugging the databse
4. **(mandatory)** Cookie Domain: Enter the domain as described
5. Session elapse time: How long should one session be valid?
6. Https Redirect: This redirects moodle to https, so that the user feels secure <br>(later this should be used to delete sessions on the etherpadlite server)
7. Verify HTTPS cert: This lets curl check, if the https cert of the etherpadlite server is valid, to prevent man in the middle attacks
8. Guests allowed to write?: As described
