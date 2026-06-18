=== MiPS Payment Gateway for WooCommerce ===
Contributors: sebastienleblanc, MIPS
Tags: Payments, online-payments, MIPS, Woocommerce
Donate link: https://www.mips.mu/
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 5.6
Stable tag: 1.2.6
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Securely accept online payments from major debit and credit cards as well as other local payment methods with one easy installation.

== Description ==

MIPS is a digital payment ecosystem for Africa. Enable a multi-channel payment solution, through a single WooCommerce integration.
----------------------------------------------------------------------------------------------------------------------------------
The MIPS WooCommerce plugin allows your customers to make secure payments when checking out on your Woocommerce website.

Through one integration you can accept bank card payments from major international card brands or local payment methods like mobile wallets and bank apps.

**CENTRALISED BACKOFFICE TOOL**
-------------------------------
The MIPS backoffice is a powerful centralised dashboard, supported in English and French, that can be configured to track and manage all your online transactions, issue refunds and manage user rights.

**REQUIREMENTS**
----------------
1. A MIPS merchant account
2. A WooCommerce store

**COUNTRIES SUPPORTED**
-----------------------
* Mauritius
* Madagascar
* Seychelles
* Maldives
* Côte d'Ivoire

**BECOME A MIPS MERCHANT**
--------------------------
Complete the onboarding form found on MIPS website, by clicking [here](https://www.mips.mu/contact_page "mips contact form"). An onboarding specialist will walk you through the process of opening a merchant account and providing any assistance you may need for WooCommerce integration.

== Installation ==

See [documentation](https://www.mips.mu/mips_payment_gateway_for_woommerce_doc/mips_documentation.pdf "mips documentation")

**API GENERATION**
------------------

1. On the left menu select: WooCommerce > Settings > Advanced > Rest API > Add Key.
2. Add the details: 
   Description: Secure Card Payment
   User: Select the connected user
   Permission: Read/Write
3. Select 'Generate API Key'
4. Copy & Send the Consumer Key and Consumer Secret to the onboarding specialist and he will generate the authentication string that you will add in your plugin

**PLUGIN INSTALLATION**
-----------------------

1. Download the MIPS WooCommerce Plugin.
2. From the left hand menu on your WordPress website back-office select: Plugins > Add New > Upload Plugins > Choose File > Choose the zip file (mips-payment-gateway-for-woocommerce.zip) from your computer files.
3. Once again on the left menu select: Plugins > Installed plugins > Identify 'MIPS Payment System' and click 'Activate'.
4. On the left menu select: WooCommerce > Settings > Payments.
5. On the list of payment methods displayed enable 'MIPS Payment System' then select 'Manage'.
6. Enter the Authentication Code provided by the MIPS onboarding specialist and save the changes. The payment system is live and ready for TEST payments.

**TESTING**
-----------

1. Add products to the shopping cart of your online store.
2. Proceed with the checkout process.
3. At the point of payment, the MIPS Payment System will be displayed.
4. Enter the Test Card Details provided by the onboarding specialist.
5. Click submit on the 3Ds emulator.
6. Upon successful payments review 'Orders' on your Wordpress backoffice to ensure the status of the order has updated with the status 'paid/completed'.

**LIVE MODE**
-------------

* Once you are sure payments are working correctly, the onboarding specialist will switch your account to LIVE mode for real payments to commence.




== Screenshots ==
1. Authentication String
2. Checkout page
3. Payment iframe

== Changelog ==

= 1.2.6 =
* Fixed bug where WooCommerce payment methods disappeared after activating the MiPS plugin

= 1.2.5 =
* Added compatibility with WooCommerce Checkout Blocks
* Improved rendering of payment gateway title and icons within the Checkout Blocks

= 1.2.4 =
* set the text for payment method before payment is completed

= 1.2.3 =
* Fixed bug double slash on checkout page

= 1.2.1 =
* Removing duplicate images on payment iframe page
* Fixed bug with images being displayed on checkout page

= 1.1.1 =
* Fixed bug on checkout page where other active payment methods were blank

= 1.1.0 =
* Making payment methods images available on checkout page dynamic

= 1.0.0 =
* Initial release