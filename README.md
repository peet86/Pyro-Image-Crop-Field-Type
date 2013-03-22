Pyro-Image-Crop-Field-Type
=====================================
Image cropping functionality for any PyroStreams based PyroCMS modules.  
Simple and easy to use image cropping tool for PyroCMS v2.2 Page, Blog and other 3rd party streams based modules.


Screenshot:
-----------
![Crop your images!](/screenshots/backend.png)


Features:
---------
v.1.0.1
- Multiple instance fix
- Delete button css fix
- Scale functionality

v.1.0.0
- Interactive image cropping based on Jcrop
- Compatible with all PyroCMS v2.1+ PyroStreams based core & third party modules 
- Easy-to-use tags: {{my_image.img}} // generates the cropped image (img html tag)
- Re-crop your photos anytime when editing your streams data (re-upload not necessary)
- Based on PyroCMS Core Files module and cache 
- Delete / re-upload functionality
- Languages: English (en), Hungarian (hu)


Install:
--------
1. Copy the modules/files folder to the shared addon modules path (/addons/shared_addons/modules).
2. Copy the field_types/imagecrop to the shared addon field_types path (/addons/shared_addons/modules/field_types)
3. Go to Control Panel / Add-ons / Modules and install the "Files - cropping" module 
4. Enjoy! 



Tags in templates
-----------------------

{{my_image.thumb.image}} - cropped image url
{{my_image.thumb.img}} - cropped html img tag

Original image:
{{my_image.img}} - the 'large' original version 

Scale: Resize your cropped image (90%,80%,70%, etc..)
{{my_image.thumb.scale.<percent 1-99>.image}} - cropped, scaled image url
{{my_image.thumb.scale.<percent 1-99>.img}} - cropped, html img tag
Example: 
Field crop width: 600, 
Field crop height: 400
{{my_image.thumb.scale.50.image}} - generate a cropped and then 50% scaled image: 300x200
Scale function is handly when you want to use the same cropped area in smaller sizes..


Other "core parameters" just like any other PyroCMS image file: 
{{my_image.filename}}
{{my_image.name}}
{{my_image.alt}}
{{my_image.description}}
{{my_image.ext}}
{{my_image.mimetype}}
{{my_image.width}}
{{my_image.height}}
{{my_image.id}}
{{my_image.filesize}}
{{my_image.download_count}}
{{my_image.date_added}}
{{my_image.folder_id}}
{{my_image.folder_name}}
{{my_image.folder_slug}

License: 
---------
Apache v2
Copyright: Peter Varga http://www.vargapeter.com
