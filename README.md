SilverStripe Chargify Select Preregister Module
========================================

This module aims to make the task of using chargify and member profiles much smoother, 
based on existing technology.

Maintainer Contacts
-------------------
*  Benjamin R. White (<ben@mangostudio.eu>)

Requirements
------------
* SilverStripe 2.4
* The [MemberProfiles](https://github.com/ajshort/silverstripe-memberprofiles) module
* The [Chargify](https://github.com/ajshort/silverstripe-chargify) module

Installation Instructions
-------------------------

1. Place this directory in the root of your SilverStripe installation.
2. Visit yoursite.com/dev/build to rebuild the database.

Usage Overview
--------------
This extends the page type created by the chargify module so that non-logged in
members can see the products that are available. On clicking the product they are
redirected to the member profile page to enter their details, and then on completion 
of the registration form are redirected to the chargify signup page related to that
product.

1. Create a page of the 'PreSelectChargifyPage' type
2. Select a page of type 'MemberProfilePage' to redirect to on click of a non-registered user
3. Change the properties of the selected MemberProfilePage to allow redirect.

Known Issues
------------
[Issue Tracker](http://github.com/ben-mangostudio/silverstripe-chargifyselectpreregister/issues)
