<?php

$_GET['url'] = 'https://'.$_SERVER['HTTP_HOST'].'/app/';
$_GET['title'] = '睿易运营';
$_GET['version'] = '1.0.3';
$_GET['target'] = 'com.ruiyi.business';

header('Content-Type: application/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
 <dict>
  <key>items</key>
  <array>
   <dict>
    <key>assets</key>
    <array>
     <dict>
      <key>kind</key>
      <string>software-package</string>
      <key>url</key>
      <string>https://shenghua.test/app/com_ruiyi_business/v103_0.ipa</string>
     </dict>
     <dict>
      <key>kind</key>
      <string>display-image</string>
      <key>needs-shine</key>
      <true/>
      <key>url</key>
      <string>https://shenghua.test/app/com_ruiyi_business/Icon.png</string>
     </dict>
     <dict>
      <key>kind</key>
      <string>full-size-image</string>
      <key>needs-shine</key>
      <true/>
      <key>url</key>
      <string>https://shenghua.test/app/com_ruiyi_business/Icon.png</string>
     </dict>
    </array>
    <key>metadata</key>
    <dict>
     <key>bundle-identifier</key>
     <string>com.ruiyi.business</string>
     <key>bundle-version</key>
     <string/>
     <key>kind</key>
     <string>software</string>
     <key>subtitle</key>
     <string>Fire vision Studio</string>
     <key>title</key>
     <string>睿易运营</string>
    </dict>
   </dict>
  </array>
 </dict>
</plist>';