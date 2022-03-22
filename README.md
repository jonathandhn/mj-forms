# mj-forms
Manage your Mailjet subscribers with elementor forms. 

### How to :

Add your API Key and API Secret on wp-config.php

```define( 'MAILJET_API', '' );```

```define( 'MAILJET_SECRET', '' );```

Select a elementor form, add an output action "Mailjet", type the list ID you want your subscribers to be part of, Mailjet do not enforce any field or custom merge tag by default. In order for the plugin to work, please set them according to your account value on the elementor output action "Mailjet" settings.  

Name your forms fields as : name firstname phone custom in order to assign them to the plugin. 