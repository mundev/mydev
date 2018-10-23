EasyBlog.require()
<?php if ($this->config->get('integration_google_adsense_script')) { ?>
.script('//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js')
<?php } ?>
.done(function(){
    (adsbygoogle = window.adsbygoogle || []).push({});
});