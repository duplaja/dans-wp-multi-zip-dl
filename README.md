# dans-wp-multi-zip-dl

Welcome! This is a file downloader plugin that offers the ability for the user to select which files (from a list) they want to download as a combined zip file.

### Features Include:

* Displays a list of all files within a specified folder.
* Lets the user select (checkboxes) which files they want to download.
* All options are configured via shortcode
* Attempts to create the specified folder (backend) if it does not exist
* Disabled download button when no files selected

### Shortcodes:
* Default Display [dans-multizip] (defaults to 1st folder)
* Optional Attributes Ex:[dans-multizip dir=1 divid=dllist]
* dir= (number of the folder you want, defaults to 1 if not entered)
* divid= (id of the div your file display list is stored in, for custom theming. Defaults to random string to allow multiple per page)

### Setup

* Copy folder into your plugins folder
* Folders to share should be under your-upload-folder/zip/subfolder-here (script will attempt to automatically create this, but depending on permissions it may not work)
* If no files show once you've uploaded some via FTP, check your folder permissions

### Liscence

All files released under MIT Liscence
