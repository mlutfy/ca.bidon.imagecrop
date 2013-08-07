CiviCRM image crop extension
============================

The extension integrates Jcrop with CiviCRM.
https://github.com/mlutfy/ca.bidon.imagecrop

It allows to crop and scale image fields in CiviCRM.
For now, it works only with the Contact "image_URL" field.

This is a fairly new extension and has not been widely tested yet.

More information about Jcrop can be found here:
http://deepliquid.com/content/Jcrop_Download.html

Example
-------

Upload an image to a contact record, then click on the image to open
a popup that allows you to crop the image:
http://www.bidon.ca/files/civicrm-imagecrop-example.jpg

Installation
------------

* Enable this extension in CiviCRM (Administer > System > Extensions)
* Jcrop is bundled in this extension, to avoid having to download separately.

Technical details
-----------------

When cropping, the extension sends an ajax request to the server with the
contact ID and a set of coordinates for the cropping.

A new image is saved in a "imagecrop" subdirectory of customFileUploadDir
(ex: files/civicrm/custom/imagecrop). The directory is created if necessary.

The original image is always kept, to reduce the risks in case of mistake.
The image is swapped when viewing the contact record (in hook_civicrm_pageRun).
CiviCRM doesn't have a "load" hook for contacts (similar to node_load in Drupal),
so at some point, we may alter the image_URL value in the database, and assume
that we can rollback to the original by removing "imagecrop" from the URL.

(it may be cleaner to keep a separate SQL table with the "original" file name,
but that means adding a lot of code, so will only do it if necessary)

Contributors
------------

* CiviCRM extension/integration written by Mathieu Lutfy (http://www.bidon.ca/about)
* Sponsored in part by Ixiam (http://www.ixiam.com/en)

Copyright
---------

Copyright (C) 2013 Mathieu Lutfy (mathieu@bidon.ca)
License: AGPL 3
http://www.bidon.ca/en/about

Jcrop is free software under MIT License.
Copyright (c) 2008-2012 Tapmodo Interactive LLC
http://github.com/tapmodo/Jcrop

