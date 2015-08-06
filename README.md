CiviCRM image crop extension
============================

The extension integrates Jcrop with CiviCRM.
https://github.com/mlutfy/ca.bidon.imagecrop

It allows to crop and scale image fields in CiviCRM.
For now, it works only with the Contact "image_URL" field
in contact records view/edit and profile view/edit.

More information about Jcrop can be found here:
http://deepliquid.com/content/Jcrop_Download.html

Installation
------------

* Enable this extension in CiviCRM (Administer > System > Extensions)

Jcrop is bundled in this extension, to avoid having to download separately.

Technical details
-----------------

When cropping, the extension sends an ajax request to the server with the
contact ID and a set of coordinates for the cropping.

A new image is saved in a "imagecrop" subdirectory of customFileUploadDir
(ex: files/civicrm/custom/imagecrop). The directory is created if necessary.

The original image is always kept, to reduce the risks in case of mistake.
The image is swapped when viewing the contact record (in hook_civicrm_pageRun).

Since CiviCRM 4.4.5, since we cannot serve images directly from the 'upload'
directory, this extension implements a Page handler on /civicrm/imagecrop/imagefile
to serve the cropped image from the 'upload' subdirectory where cropped images
are stored.

Contributors
------------

* CiviCRM extension/integration written by Mathieu Lutfy (http://www.bidon.ca/about)
* Sponsored by Ixiam (http://www.ixiam.com/en)

Copyright
---------

Copyright (C) 2013-2015 Mathieu Lutfy (mathieu@bidon.ca)
License: AGPL 3
http://www.bidon.ca/en/about

Jcrop is free software under MIT License.
Copyright (c) 2008-2012 Tapmodo Interactive LLC
http://github.com/tapmodo/Jcrop

