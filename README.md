# Message Vault for NextCloud
⚠️ We're currently in the very early stages of beta testing and things may not be working correctly yet. ⚠️

The Message Vault takes XML backups from the Android app "SMS Backup & Restore" and
imports them into the Nextcloud database for easily viewing.
Support is baked in for importing of:

- Multimedia Messaging Service (MMS) 
- Rich Communication Services messages (RCS/Google Chat)
- Short Message Service (SMS) 
- Files and attachments are supported and will restore to `./.messagevault`
- Full support for Nextcloud 22+

This is a completely new app and is not at all based on code, logic, or database from ocsms or the phone sync app that accompanies it.

# Features
Current features include,
- Importing XML backup files
- Deleting threads
- Viewing threads
- Workflow for automating new imports

# Future
Features that should be added in the future include, and are in no particular order and also don't indicate that they'll ever be started or completed.
- Mass delete and deletion of entire message database
- Individual message deletion
- Merging of messages
- Search, preferably by date (year/month), attachments, contacts, full message, etc
- Better infinite scroll support (threads with thousands of messages can't all be seen)
- Export to HTML?
- Import of other formats and mediums (ie, Signal)
- Potential front end "live" sync from mobile devices?
- Folders for better organization

# Maintainers
- [Justin Osborne](https://github.com/onfire4g05)
