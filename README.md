Psst... If you would like to support my work or are just pure sound, you can [Buy me a coffee ☕](https://www.buymeacoffee.com/leemahoney3)!

# WHMCS Client Password Changer

### Version 1.0.6 is now available

Fixed bug where this did not work in a subdirectory (eg. /billing/).

---

## What is this?
Allows admins to change a users password manually without the need to send an email to the client and reset it that way.

## How it works?

When this addon is activated, administrators get the ability to change individual user passwords without the need to send a password reset email to the client. You can generate a password or enter one of your choosing.

There are a number of configuration optons available, please see the Configuration heading below.

## How do I install this?

1. Download the latest release from the releases section.

2. Copy the ```modules``` folder to your root WHMCS directory.

3. Navigate to **System Settings > Addon Modules** and click **Activate** beside  **Client Password Changer**.

4. Configure the addon as needed, it will then be available through **Addons >  Client Password Changer** 

## How to update

Simply copy the modules folder into the root of your WHMCS folder accepting any overwrites. I'd recommend keeping the module enabled to preserve your cofiguration settings.

## Configuration

There are a number of configuration options available when activating the addon. Please find them below.

|Name|Description|
|----|----|
|Show Buttons on Manage Users screens|Whether or not to show a "Change Password" button each user in the user management screens|
|Enable Logging|Log that the password has been manually changed on owned client accounts|
|Show Modals|Show modal for password change on user management pages, if disabled you will be redirected to the module|
|Generated Password Length|Length of the password generated by the password generator|
|Allow Special Characters|Allow special characters in the password generated by the password generator|
|Allow Numbers|Allow numbers in the password generated by the password generator|
|Allow Capital Letters|Allow capital letters in the password generated by the password generator|
|Pagination Record Limit|Number of records to show per page in the user list on the dashboard|
|Pagination Sort Order|The sort order of the user list on the dashboard|
|Pagination Sort Field|The field used to sort the user list on the dashboard|
|Show Update Message|Whether or not to show the update alert when a new version of this script is available|
|Access Control|WHMCS Access Control to manage which roles can access the module|

## Screenshots

Configuration Options
![WHMCS Client Password Changer](https://static.leemahoney.tech/img/whmcs/addons/whmcs-client-password-changer/im001.png)

Change Password Modal
![WHMCS Client Password Changer](https://static.leemahoney.tech/img/whmcs/addons/whmcs-client-password-changer/im006.png)

![WHMCS Client Password Changer](https://static.leemahoney.tech/img/whmcs/addons/whmcs-client-password-changer/im007.png)

![WHMCS Client Password Changer](https://static.leemahoney.tech/img/whmcs/addons/whmcs-client-password-changer/im008.png)

Dashboard
![WHMCS Client Password Changer](https://static.leemahoney.tech/img/whmcs/addons/whmcs-client-password-changer/im002.png)

Change Password Screen
![WHMCS Client Password Changer](https://static.leemahoney.tech/img/whmcs/addons/whmcs-client-password-changer/im003.png)

Change Password Buttons
![WHMCS Client Password Changer](https://static.leemahoney.tech/img/whmcs/addons/whmcs-client-password-changer/im004.png)

![WHMCS Client Password Changer](https://static.leemahoney.tech/img/whmcs/addons/whmcs-client-password-changer/im005.png)

Password Changes Logged
![WHMCS Client Password Changer](https://static.leemahoney.tech/img/whmcs/addons/whmcs-client-password-changer/im009.png)

## Have a feature request?

This module is still a work in progress, any ideas for it please let me know! I'm happy to implement anything that may benefit the module further. Email all feature requests to lee@leemahoney.dev

## Contributions

Feel free to fork the repo, make changes, then create a pull request!

