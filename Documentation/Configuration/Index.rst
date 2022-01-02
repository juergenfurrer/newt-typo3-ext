.. include:: ../Includes.txt

.. _configuration:

=============
Configuration
=============

To configurate this extension, you have to add the static template of Newt

After adding the static, you will find the constants in the Constant editor:

======================================  ==========  ==================================================================  =======================================
Property:                               Data type:  Description:                                                        Default:
======================================  ==========  ==================================================================  =======================================
settings.apiPageId                      string      Page ID to use for url-build the API-Link
                                                    Point to the page you like to use as API
                                                    Because the Extension uses a typNum, it will work with any page
                                                    This is just to have a nice URL...
--------------------------------------  ----------  ------------------------------------------------------------------  ---------------------------------------
settings.apiTypeNum                     string      Page TypeNum for the API                                            1201
--------------------------------------  ----------  ------------------------------------------------------------------  ---------------------------------------
settings.apiBaseUrl                     string      Base-URL in case you use "/" in site-configuration
                                                    For the API to work, it is important to have a full-URL
                                                    When creating URL's with the site-configuration, this is sometimes
                                                    hard to get working, in case you use "/" as base-url, place the
                                                    sites Host here (e.g.: https://example.com)
--------------------------------------  ----------  ------------------------------------------------------------------  ---------------------------------------
settings.tokenExpiration                string      Token expiration in seconds (0 = infinit)
                                                    Insert here the lifetime of the token in seconds
--------------------------------------  ----------  ------------------------------------------------------------------  ---------------------------------------
persistence.storagePid                  string      Default storage PID
                                                    Defines the placement of the endpoint-configurations
======================================  ==========  ==================================================================  =======================================

[tsref:plugin.tx_newt]


======================================  ==========  ==================================================================  =======================================
Property:                               Data type:  Description:                                                        Default:
======================================  ==========  ==================================================================  =======================================
view.templateRootPath                   string      Defines the path where the admin-template are located               EXT:newt/Resources/Backend/Templates/
--------------------------------------  ----------  ------------------------------------------------------------------  ---------------------------------------
view.partialRootPath                    string      Defines the path where the admin-partials are located               EXT:newt/Resources/Backend/Partials/
--------------------------------------  ----------  ------------------------------------------------------------------  ---------------------------------------
view.layoutRootPath                     string      Defines the path where the admin-layout are located                 EXT:newt/Resources/Backend/Layouts/
======================================  ==========  ==================================================================  =======================================

[tsref:plugin.tx_newt_admin]

