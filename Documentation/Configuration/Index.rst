.. include:: ../Includes.txt

.. _configuration:

=============
Configuration
=============

1. Date and time format
=======================

   Date and time formats use PHP :php:`date()` syntax, for example
   ``F j, Y``, ``j. F Y``, ``g:i a`` or ``H:i``.

   **Site settings (TYPO3 v13+)**

   When the *News Comments* site set is enabled, enter a custom format
   under **Date & Time Settings**. This is the path used by TypoScript
   objects such as ``lib.tx_nsnewscomments.comments``. No upgrade wizard
   is required for this setup.

   **Constant Editor (TYPO3 v12, or without a site set)**

   The *Date Format* and *Time Format* constants are free-text fields.
   The default date format is ``F j, Y`` (a comma after the day).

   **Content element plugin**

   The plugin FlexForm has text fields for date and time. Enable *Use
   global date and time format* to take the values from site settings
   or TypoScript instead.

2. Configure Extension Constants
================================

   Configure extension constants as per your requirements.

   .. rst-class:: bignums

   #. Switch to the root page of your site.

   #. Switch to the **Template module** and select *Constant Editor*.

   #. Select Category = PLUGIN.TX_NSNEWSCOMMENTS (12)

   #. Configure it.

   .. figure:: Images/1-TYPO3-ns_news_comments_configuration.png
        :alt: ns_news_comments Configuration 1
        :width: 1200px


3. Configure Plugin
===================

   Configure plugins as per your requirements.

        .. figure:: Images/2-TYPO3-ns_news_comments_configuration.png
            :alt: ns_news_comments Configuration 2
            :width: 1000px

        .. figure:: Images/3-TYPO3-ns_news_comments_configuration.png
            :alt: ns_news_comments Configuration 3
            :width: 1300px

4. Enable Captcha
=================

  If you want to use captcha in your site than you should enable
  .htaccess file which is placed in Private folder of this extension.

  .. figure:: Images/4-TYPO3-ns_news_comments_configuration.png
            :alt: ns_news_comments Configuration 3
            :width: 1100px

  .. figure:: Images/5-TYPO3-ns_news_comments_configuration.png
            :alt: ns_news_comments Configuration 3
            :width: 1100px

That's it, Now you can enjoy comments of your news readers :)