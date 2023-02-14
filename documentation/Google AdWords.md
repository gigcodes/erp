# Google AdWords Account, Campaign, Ads Group and Ads

The account module is the representation of the Google Ads account, which provide to manage campaign, ads group and using Google Ads API. These module are handling with the following tables: `googleadsaccounts`, `googlecampaigns`, `googleadsgroups` and `googleads`.

*API Reference:* https://developers.google.com/google-ads/api/docs/first-call/overview
*Used PHP Package:* https://github.com/googleads/google-ads-php

## Account
The account module store the configuration information to access Google Ads API for manage campaign, ads group and ads.
 - **Create:** It can be created by adding data in the create form. User have to enter account name, store website, config file, and choose status. 
 - **Edit:** An edit option is available in this module to edit the all data that we entered in the creation time of the account.
 - **Delete** option will permanently delete an account.
 - **Create Campaign** option will allow to manage campaign regarding thing.

*Sample Config File:* https://github.com/googleads/google-ads-php/blob/main/examples/Authentication/google_ads_php.ini

## Campaign
The campaign module has store budget details, billing detail and other details of campaign to manage ads group and ads using Google Ads API.
 - **Create:** It can be created by adding data in the create form. User have to enter campaign name, channel type, channel sub type, shopping setting, bidding details, URL details, budget amount, duration and status. 
 - **Edit:** An edit option is available in this module to edit the camapign name, bidding details, budget amount, duration and status. 
 - **Delete** option will permanently delete campaign from database as well as Google Ads account.
 - **Ad Groups** option will allow to manage campaign's ads group thing.

*API Reference:* https://developers.google.com/google-ads/api/docs/campaigns/overview

## Ads Group
The ads group module has store group name, budget amount and status of ads group to manage ads using Google Ads API.
 - **Create:** It can be created by adding data in the create form. User have to enter ads group name, budget amount and status. 
 - **Edit:** An edit option is available in this module to edit the all data that we entered in the creation time of the ads group.
 - **Delete** option will permanently delete ads group from database as well as Google Ads account.
 - **Ads** option will allow to manage ads group's ads thing.

*API Reference:* https://developers.google.com/google-ads/api/docs/campaigns/create-ad-groups

## Ads
The ads module has store ads details and create ads on Google Ads using its API.
 - **Create:** It can be created by adding data in the create form. User have to enter headline 1, headline 2, headline 3, description 1, description 2, final URL, path 1, path 2 and status.
 - **Delete** option will permanently delete ads from database as well as Google Ads account.

*API Reference:* https://developers.google.com/google-ads/api/docs/ads/overview
