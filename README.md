# Magenerds_GermanLaw

This extension provides basic configurations for your Magento store in order to be ready for the german market.   
It includes many system configuration settings, tax configuration as well as cms pages for legal information.   
It also shows legal notices for shipping costs after every price.

## Extension installation

The easiest way to install the Magenerds module is via composer

```
# add to composer require
composer require magenerds/germanlaw

# run magento setup to activate the module
bin/magento set:up
```

## Extension activation

At any time you can enable and disable this extension in the system configuration.   
You can do this by opening the backend menu ```Stores > Configuration```.   
There you will find the entry ```Magenerds > GermanLaw```.   
After clicking on it you see a dropdown box where you can enable/disable the module. Save the configuration and delete
the cache.

![GermanLaw-Activation](_images/gl_activ.png?raw=true "GermanLaw Activation")

## Extension configuration

You can configure the shipping notice either to be displayed after every price directly or in the footer whereas there
ist an asterisk (*) behind every price referencing to the footer.   
Go to ```Stores > Configuration and Magenerds > GermanLaw``` and then to Price display. There you can choose if you use
the asterisk or not:

![GermanLaw-Configuration](_images/gl_config.png?raw=true "GermanLaw Configuration")

You can also define the tax and shipping text being dispayed. Therefore you can use %s in order to define the tax amount
in the text.   
For defining the link to the shipping page you can use [ and ] before and after the word you want to use as a link.
**Example**: Inkl. %s MwSt., zzgl. [Versand] will lead to Inkl. 19% MwSt., zzgl. Versand whereas Versand is the link to
the shipping page.
The last configuration setting is the selected cms page which is the shipping page you want to link to.

## Installed CMS Pages

This extension installs 5 cms pages during installation:

* AGB
* Widerrufsbelehrung
* Impressum
* Versandkosten
* Datenschutzbestimmung

The cms pages have dummy content so you should fill them if you use them. The german law says you have to use them :-)

## Tax configuration

This extension installs the two german tax rates:

* 7%
* 19%

You can select them if you edit a product in the backend under Product details:

![GermanLaw-Tax](_images/gl_tax.png?raw=true "GermanLaw Tax-Config")

## System configuration

This extensions configures the system so that the store is ready for the german market. In detail the settings are as
follows:

* Default country: DE
* Locale code: de_DE
* State required: CA,EE,FI,FR,LV,LT,RO,ES,US
* Timezone: Europe/Berlin
* Weight unit: kgs
* Week’s first day: Monday
* Currency: EUR
* Sendfriend: Disabled as not allowed in Germany
* Newsletter subscription confirmation: Enabled
* Create customer account confirmation: Enabled
* Prefix options: Herr;Frau;Firma
* Show middlename: Disabled
* Show price including tax: Enabled
* Show discount including tax: Enabled
* Default country: DE
* Show agreements: Enabled
* Origin country: DE
* Origin postcode: empty
