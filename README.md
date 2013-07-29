CiviCRM image crop extension
============================

This is a test extension integrating Jcrop with CiviCRM.
It is not ready for production use and does not do much.

The intention is to allow crop & scale on some image fields in CiviCRM.
For now, it's applied only to the Contact "image_URL" field.

Example
-------

Upload an image to a contact record, then click on the image to open
a popup that allows you to crop the image:
http://www.bidon.ca/files/civicrm-imagecrop-example.jpg

Installation
------------

* Enable this extension in CiviCRM (Administer > System > Extensions)
* Download Jcrop locally

Jcrop can be found here: http://deepliquid.com/content/Jcrop_Download.html

Install: you must install jcrop manually, so that you have:
  /path/to/extensions/ca.bidon.imagecrop/jcrop/js/jquery.Jcrop.min.js
  /path/to/extensions/ca.bidon.imagecrop/jcrop/css/jquery.Jcrop.min.css

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

