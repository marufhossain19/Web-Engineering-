# How to Apply the Messaging System

## Quick Start Guide

### 1. Apply Database Migration

Open phpMyAdmin and run this SQL file:
```
migrations/001_add_messages_table.sql
```

OR use MySQL command line:
```bash
mysql -u root -p weby_db < migrations/001_add_messages_table.sql
```

### 2. Test the System

That's it! The messaging system is now active. Test it by:
1. Clicking the chat bubble icon on any note/question card
2. Sending a message
3. Viewing messages on your profile (click the notification bell)

---

## If You Want to Remove It Later

Run the rollback SQL file:
```
migrations/001_rollback_messages_table.sql
```

Then delete these new files:
- `api/send_message.php`
- `api/get_messages.php`
- `api/get_unread_count.php`
- `api/mark_messages_read.php`
- `js/messages.js`

And restore the modified files using Git:
```bash
git checkout notes.php questions.php profile.php css/animations.css
```

See `migrations/README.md` for detailed rollback instructions.
