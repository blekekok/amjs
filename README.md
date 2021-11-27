# Metaforums

A forum site bassed on PHP and jQuery with basic tools and features sufficient enough for small communities and or groups.

Structure
---------
```bash
├── installation (delete after install)
├── public_res (resource folder to contain user profile images)
└── src
    ├── php (all php files used to run the server)
        └── config.php (configuration file, modify this for the website to work)
    ├── res (icons for the site)
    ├── script (all .js files)
    └── style (all .css files)
```

Installation
-------------

Tools that are used
 Tools   | Version   
 ------- | --------- 
Ubuntu | 20.04
Apache | 2.4.41
PHP    | 7.4
MySQL | 8.0.27

### MySQL Installation
Run the `metaforums-template.sql` included in the installation folder.

Run `SET GLOBAL time_zone = '-6:00';` and change the `-6:00` to adjust your MySQL time based on your system clock.

When an account is registered, you can change their roles in MFUsers role column. The available roles are `user, mod, siteadmin`.

You can add more groups and categories on the `MFGroups` and `MFCategories` tables. The `groupid` column in `MFCategories` has to match the id on `MFGroups`

### Web configuration
You have to change certain configuration inside `src/php/config.php` in order for the website to work properly.

You have to change the `EMAIL_ADDRESS` according to your own email address.

If you used the default settings or have not configured your web server, change the `SITE_ADDRESS` according to the domain the website is using.

### Mail configuration
The site uses the basic mail system provided by PHP themselves. You have to configure your own SMTP server on the server itself.

Libraries
--------------
* [Quill.js](https://quilljs.com/) (Used for WYSIWYG Editor)
